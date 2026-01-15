<?php

/**
 * YForm Content Builder - Modul-Generator
 * Erstelle automatisch REDAXO-Module für deine Content Builder Elemente
 */

$addon = rex_addon::get('yform_content_builder');

// Helper: Generiert Modul-Code für ein Element
function generateModuleCode($elementKey, $framework) {
    $config = [];
    $configPath = rex_path::addon('yform_content_builder', 'elements/' . $elementKey . '/config.php');
    
    if (file_exists($configPath)) {
        $config = include $configPath;
    }
    
    $label = isset($config['label']) ? rex_escape($config['label']) : ucfirst($elementKey);
    
    $code = <<<PHP
<?php
/**
 * Modul: {$label}
 * Element: {$elementKey}
 */

echo yform_content_builder_module::create('{$elementKey}', 'REX_VALUE[1]', '{$framework}')->renderInput();
?>
PHP;
    
    return $code;
}

// Module erstellen
if (rex_post('create_modules', 'bool')) {
    $selectedElements = rex_post('elements', 'array', []);
    $framework = rex_post('framework', 'string', 'uikit');
    
    if (!empty($selectedElements)) {
        $createdModules = [];
        
        foreach ($selectedElements as $elementKey) {
            $elementKey = rex_escape($elementKey);
            $configPath = rex_path::addon('yform_content_builder', 'elements/' . $elementKey . '/config.php');
            
            if (!file_exists($configPath)) {
                continue;
            }
            
            $config = include $configPath;
            $label = isset($config['label']) ? $config['label'] : ucfirst($elementKey);
            $moduleKey = 'yfcb_' . $elementKey;
            
            // Modul-Code generieren
            $inputCode = generateModuleCode($elementKey, $framework);
            
            $outputCode = <<<PHP
<?php
echo yform_content_builder_module::create('{$elementKey}', 'REX_VALUE[1]', '{$framework}')->renderOutput();
?>
PHP;
            
            // In DB eintragen
            try {
                // Prüfe ob Modul existiert
                $existingSql = rex_sql::factory();
                $existingSql->setQuery('SELECT id FROM ' . rex::getTable('module') . ' WHERE `key` = ?', [$moduleKey]);
                
                if ($existingSql->getRows() > 0) {
                    // Update mit direktem SQL wegen 'key' Keyword
                    $updateSql = rex_sql::factory();
                    $updateSql->setQuery(
                        'UPDATE ' . rex::getTable('module') . ' SET `name` = :name, `input` = :input, `output` = :output WHERE `key` = :key',
                        [
                            ':name' => $label,
                            ':input' => $inputCode,
                            ':output' => $outputCode,
                            ':key' => $moduleKey
                        ]
                    );
                    $createdModules[] = $label . ' (aktualisiert)';
                } else {
                    // Insert mit direktem SQL wegen 'key' Keyword
                    $insertSql = rex_sql::factory();
                    $insertSql->setQuery(
                        'INSERT INTO ' . rex::getTable('module') . ' (`key`, `name`, `input`, `output`) VALUES (:key, :name, :input, :output)',
                        [
                            ':key' => $moduleKey,
                            ':name' => $label,
                            ':input' => $inputCode,
                            ':output' => $outputCode
                        ]
                    );
                    $createdModules[] = $label . ' (neu erstellt)';
                }
            } catch (Exception $e) {
                echo rex_view::error('Fehler beim Erstellen von Modul "' . $label . '": ' . $e->getMessage());
                continue;
            }
        }
        
        if (!empty($createdModules)) {
            $message = '<ul>';
            foreach ($createdModules as $module) {
                $message .= '<li>' . rex_escape($module) . '</li>';
            }
            $message .= '</ul>';
            echo rex_view::success('Module erstellt/aktualisiert: ' . $message);
        }
    }
}


// Alle verfügbaren Elemente laden
$elementsDir = rex_path::addon('yform_content_builder', 'elements');
$elements = [];

if (is_dir($elementsDir)) {
    $dirs = scandir($elementsDir);
    foreach ($dirs as $dir) {
        if ($dir[0] === '.') continue;
        $configPath = $elementsDir . '/' . $dir . '/config.php';
        if (file_exists($configPath)) {
            $config = include $configPath;
            if (is_array($config) && isset($config['label'])) {
                $elements[$dir] = $config['label'];
            }
        }
    }
}

// Sortiere Elemente
asort($elements);

// UI
$fragment = new rex_fragment();
$fragment->setVar('class', 'edit', false);
$fragment->setVar('title', 'Module erstellen/aktualisieren', false);

$content = '';
$content .= '<form action="' . rex_url::currentBackendPage() . '" method="post">';

// Framework-Auswahl
$content .= '<div class="form-group">';
$content .= '<label for="framework"><strong>Framework</strong></label>';
$content .= '<select class="form-control" id="framework" name="framework">';
$content .= '<option value="uikit">UIkit</option>';
$content .= '<option value="bootstrap">Bootstrap</option>';
$content .= '</select>';
$content .= '<small class="help-block">Wähle das Framework, das du in deinen Modulen verwenden möchtest.</small>';
$content .= '</div>';

// Elemente auswählen
$content .= '<div class="form-group">';
$content .= '<label><strong>Elemente auswählen</strong></label>';
$content .= '<p class="help-block">Wähle die Elemente, für die du automatisch REDAXO-Module erstellen möchtest.</p>';
$content .= '<div style="border: 1px solid #ddd; padding: 15px; border-radius: 4px; max-height: 400px; overflow-y: auto; background: #f9f9f9;">';

if (empty($elements)) {
    $content .= '<p style="color: #999;">Keine Elemente gefunden.</p>';
} else {
    $content .= '<div class="form-group">';
    $content .= '<button type="button" class="btn btn-xs btn-default" onclick="document.querySelectorAll(\'.element-checkbox\').forEach(c => c.checked = true);">Alle auswählen</button>';
    $content .= ' ';
    $content .= '<button type="button" class="btn btn-xs btn-default" onclick="document.querySelectorAll(\'.element-checkbox\').forEach(c => c.checked = false);">Alle abwählen</button>';
    $content .= '</div>';
    
    foreach ($elements as $elementKey => $elementLabel) {
        $moduleKey = 'yfcb_' . $elementKey;
        $content .= '<div class="checkbox">';
        $content .= '<label>';
        $content .= '<input type="checkbox" class="element-checkbox" name="elements[]" value="' . rex_escape($elementKey) . '">';
        $content .= ' <strong>' . rex_escape($elementLabel) . '</strong> ';
        $content .= '<small style="color: #999;">(Key: ' . rex_escape($moduleKey) . ')</small>';
        $content .= '</label>';
        $content .= '</div>';
    }
}

$content .= '</div>';
$content .= '</div>';

// Buttons
$content .= '<div class="form-group">';
$content .= '<button type="submit" name="create_modules" value="1" class="btn btn-primary">';
$content .= '<i class="fa fa-plus"></i> Module erstellen';
$content .= '</button>';
$content .= '</div>';

$content .= '</form>';

$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');

// Info-Box
$infoContent = '<p><i class="fa fa-info-circle"></i> ';
$infoContent .= '<strong>So funktioniert es:</strong><br>';
$infoContent .= '1. Wähle die gewünschten Elemente aus<br>';
$infoContent .= '2. Wähle dein Framework (UIkit oder Bootstrap)<br>';
$infoContent .= '3. Klicke auf "Module erstellen"<br>';
$infoContent .= '4. Die Module werden automatisch in der REDAXO-Datenbank angelegt und sind sofort einsatzbereit<br>';
$infoContent .= '<br>';
$infoContent .= '<strong>Module Key Format:</strong> yfcb_[element-name] (z.B. yfcb_cards)<br>';
$infoContent .= 'Du kannst die Module in deinen REDAXO-Seiten verwenden, indem du sie in dein Seitenlayout einbindest.';
$infoContent .= '</p>';

$fragment = new rex_fragment();
$fragment->setVar('class', 'info', false);
$fragment->setVar('title', 'Info', false);
$fragment->setVar('body', $infoContent, false);
echo $fragment->parse('core/page/section.php');
