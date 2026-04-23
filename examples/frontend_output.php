<?php
/**
 * Beispiel: Frontend-Ausgabe von Content Builder Slices
 * 
 * Verwendung im Template oder Modul:
 * <?php include rex_addon::get('yform_content_builder')->getPath('examples/frontend_output.php'); ?>
 */

// Dataset holen (z.B. aus YOrm)
// $dataset = \MyNamespace\MyModel::get($id);
// $content = $dataset->getValue('content_builder');

// Oder direkt aus YForm Table Manager
$content = $this->getValue('content_builder'); // Im YForm-Context

// JSON dekodieren
$slices = json_decode($content, true);

if (!is_array($slices) || empty($slices)) {
    return;
}

// Framework-Einstellung (aus Config oder hardcoded)
$framework = 'bootstrap'; // oder 'uikit', 'plain'

foreach ($slices as $slice) {
    // Offline geschaltete Abschnitte überspringen
    if (is_array($slice) && array_key_exists('online', $slice) && $slice['online'] === false) {
        continue;
    }
    $sliceType = $slice['type'];
    $elementData = $slice['data'] ?? [];
    
    // Template-Pfad
    $addon = rex_addon::get('yform_content_builder');
    $elementPath = $addon->getPath('elements/' . $sliceType);
    $templateFile = $elementPath . '/templates/' . $framework . '.php';
    
    // Fallback auf plain.php
    if (!file_exists($templateFile)) {
        $templateFile = $elementPath . '/templates/plain.php';
    }
    
    // Template ausgeben
    if (file_exists($templateFile)) {
        include $templateFile;
    } else {
        echo '<!-- Element template not found: ' . rex_escape($sliceType) . ' -->';
    }
}
