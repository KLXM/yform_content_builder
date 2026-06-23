<?php

/**
 * YForm Content Builder - Einstellungen
 */

$addon = rex_addon::get('yform_content_builder');

$themeProviderChoices = \KLXM\YFormContentBuilder\Config\ThemeProviderBridge::getThemeChoices();
$hasThemeProvider = \KLXM\YFormContentBuilder\Config\ThemeProviderBridge::isProviderAvailable() || $themeProviderChoices !== [];

$availableYformTables = [];
if (rex_addon::get('yform')->isAvailable() && class_exists(rex_yform_manager_table::class)) {
    try {
        foreach (rex_yform_manager_table::getAll() as $table) {
            $tableName = (string) $table->getTableName();
            if ($tableName === '') {
                continue;
            }

            $availableYformTables[$tableName] = (string) $table->getName();
        }
    } catch (Throwable) {
        // ignore
    }
}

ksort($availableYformTables, SORT_NATURAL | SORT_FLAG_CASE);

// Formular verarbeiten
if (rex_post('save', 'bool')) {
    $addon->setConfig('theme', rex_post('theme', 'string', ''));

    $postedTableThemes = rex_post('table_themes', 'array', []);
    $tableThemes = [];
    if (is_array($postedTableThemes)) {
        foreach ($postedTableThemes as $tableName => $themeName) {
            $tableName = trim((string) $tableName);
            if ($tableName === '' || !array_key_exists($tableName, $availableYformTables)) {
                continue;
            }

            $themeName = trim((string) $themeName);
            if ($themeName !== '') {
                $tableThemes[$tableName] = $themeName;
            }
        }
    }
    $addon->setConfig('table_themes', $tableThemes);

    $addon->setConfig('compact_mode', rex_post('compact_mode', 'bool', false));
    $addon->setConfig('enable_online_toggle', rex_post('enable_online_toggle', 'bool', false));
    $addon->setConfig('enable_copy_paste', rex_post('enable_copy_paste', 'bool', false));
    $addon->setConfig('enable_element_search', rex_post('enable_element_search', 'bool', false));
    $addon->setConfig('enable_demo_elements', rex_post('enable_demo_elements', 'bool', true));

    $replaceKeepCoreElements = rex_post('replace_keep_core_elements', 'array', []);
    $replaceKeepCoreElements = array_values(array_unique(array_filter(array_map(
        static fn ($value): string => trim((string) $value),
        is_array($replaceKeepCoreElements) ? $replaceKeepCoreElements : []
    ), static fn (string $value): bool => $value !== '')));
    $addon->setConfig('replace_keep_core_elements', $replaceKeepCoreElements);

    echo rex_view::success(rex_i18n::msg('yform_content_builder_settings_saved'));
    
    // Theme-Provider Kontext aktualisieren
    if ($hasThemeProvider) {
        \KLXM\YFormContentBuilder\Config\ThemeProviderBridge::resetThemeContext();
        \KLXM\YFormContentBuilder\Config\ThemeProviderBridge::setTheme((string) $addon->getConfig('theme', ''));
    }
}

// Verfügbare Themes laden
$themes = ['' => '-- Kein Theme (Domain-Context verwenden) --'];
if ($themeProviderChoices !== []) {
    $themes = array_merge($themes, $themeProviderChoices);
}

$currentTheme = $addon->getConfig('theme', '');
$tableThemes = $addon->getConfig('table_themes', []);
if (!is_array($tableThemes)) {
    $tableThemes = [];
}

$compactMode = $addon->getConfig('compact_mode', false);
$enableOnlineToggle = $addon->getConfig('enable_online_toggle', false);
$enableCopyPaste = $addon->getConfig('enable_copy_paste', false);
$enableElementSearch = $addon->getConfig('enable_element_search', false);
$enableDemoElements = $addon->getConfig('enable_demo_elements', true);
$replaceKeepCoreElements = $addon->getConfig('replace_keep_core_elements', []);
if (!is_array($replaceKeepCoreElements)) {
    $replaceKeepCoreElements = [];
}

$replaceKeepCoreElements = array_values(array_unique(array_filter(array_map(
    static fn ($value): string => trim((string) $value),
    $replaceKeepCoreElements
), static fn (string $value): bool => $value !== '')));

$coreElementOptions = [];
$missingReplaceKeepCoreElements = [];
$coreElementsPath = rex_path::addon('yform_content_builder', 'elements/');
if (is_dir($coreElementsPath)) {
    $dirs = scandir($coreElementsPath);
    if (is_array($dirs)) {
        foreach ($dirs as $dir) {
            if ($dir === '.' || $dir === '..') {
                continue;
            }

            $elementPath = $coreElementsPath . $dir;
            $configPath = $elementPath . '/config.php';
            if (!is_dir($elementPath) || !is_file($configPath)) {
                continue;
            }

            \KLXM\YFormContentBuilder\Helper::loadElementI18n($elementPath);
            $config = include $configPath;
            $label = is_array($config) ? trim((string) ($config['label'] ?? '')) : '';
            if ($label === '') {
                $label = $dir;
            }

            $coreElementOptions[$dir] = $label;
        }
    }
}

asort($coreElementOptions, SORT_NATURAL | SORT_FLAG_CASE);
$missingReplaceKeepCoreElements = array_values(array_diff($replaceKeepCoreElements, array_keys($coreElementOptions)));
foreach ($missingReplaceKeepCoreElements as $missingElementKey) {
    $coreElementOptions[$missingElementKey] = $missingElementKey . ' (nicht gefunden)';
}

// Formular bauen
$content = '';
$content .= '<fieldset>';
$content .= '<legend>' . rex_i18n::msg('yform_content_builder_general_settings') . '</legend>';

$formElements = [];

// Aktiver Modus anzeigen (merge oder replace)
$currentMode = \KLXM\YFormContentBuilder\Config\ElementModeResolver::getElementMode();
$modeLabel = $currentMode === 'merge' ? rex_i18n::msg('yform_content_builder_mode_merge', 'Merge (Demo + Custom)') : rex_i18n::msg('yform_content_builder_mode_replace', 'Replace (nur Custom)');
$n = [];
$n['label'] = '<label>' . rex_i18n::msg('yform_content_builder_mode') . '</label>';
$n['field'] = '<p class="form-control-static"><span class="label label-' . ($currentMode === 'merge' ? 'info' : 'warning') . '">' . rex_escape($modeLabel) . '</span></p>';
$n['note'] = 'Wird per Extension Point YFORM_CONTENT_BUILDER_ELEMENT_MODE definiert. Aktuell: <code>' . rex_escape($currentMode) . '</code>';
$formElements[] = $n;

// Theme-Auswahl (nur wenn Theme-Provider verfügbar)
if ($hasThemeProvider) {
    $n = [];
    $n['label'] = '<label for="theme">' . rex_i18n::msg('yform_content_builder_theme') . '</label>';
    $n['field'] = '<select class="form-control" id="theme" name="theme">';
    foreach ($themes as $value => $label) {
        $selected = ($value === $currentTheme) ? ' selected' : '';
        $n['field'] .= '<option value="' . rex_escape($value) . '"' . $selected . '>' . rex_escape($label) . '</option>';
    }
    $n['field'] .= '</select>';
    $n['note'] = rex_i18n::msg('yform_content_builder_theme_notice');
    $formElements[] = $n;

    if ($availableYformTables !== []) {
        $tableThemeField = '<div class="table-responsive">';
        $tableThemeField .= '<table class="table table-striped table-hover" style="margin-bottom:0;">';
        $tableThemeField .= '<thead><tr>';
        $tableThemeField .= '<th>' . rex_i18n::msg('yform_content_builder_theme_table_column') . '</th>';
        $tableThemeField .= '<th>' . rex_i18n::msg('yform_content_builder_theme_theme_column') . '</th>';
        $tableThemeField .= '</tr></thead><tbody>';

        foreach ($availableYformTables as $tableName => $tableLabel) {
            $currentTableTheme = trim((string) ($tableThemes[$tableName] ?? ''));
            $tableThemeField .= '<tr>';
            $tableThemeField .= '<td><strong>' . rex_escape($tableLabel) . '</strong><br><code>' . rex_escape($tableName) . '</code></td>';
            $tableThemeField .= '<td><select class="form-control" name="table_themes[' . rex_escape($tableName) . ']">';
            foreach ($themes as $value => $label) {
                $selected = ($value === $currentTableTheme) ? ' selected' : '';
                $tableThemeField .= '<option value="' . rex_escape($value) . '"' . $selected . '>' . rex_escape($label) . '</option>';
            }
            $tableThemeField .= '</select></td>';
            $tableThemeField .= '</tr>';
        }

        $tableThemeField .= '</tbody></table></div>';

        $n = [];
        $n['label'] = '<label>' . rex_i18n::msg('yform_content_builder_theme_per_table') . '</label>';
        $n['field'] = $tableThemeField;
        $n['note'] = rex_i18n::msg('yform_content_builder_theme_per_table_notice');
        $formElements[] = $n;
    }
}

// Kompaktmodus-Toggle
$n = [];
$n['label'] = '<label for="compact_mode">' . rex_i18n::msg('yform_content_builder_compact_mode') . '</label>';
$n['field'] = '<div class="checkbox"><label><input type="hidden" name="compact_mode" value="0"><input type="checkbox" id="compact_mode" name="compact_mode" value="1"' . ($compactMode ? ' checked' : '') . '> ' . rex_i18n::msg('yform_content_builder_compact_mode_label') . '</label></div>';
$n['note'] = rex_i18n::msg('yform_content_builder_compact_mode_notice');
$formElements[] = $n;

// Online/Offline-Toggle
$n = [];
$n['label'] = '<label for="enable_online_toggle">' . rex_i18n::msg('yform_content_builder_enable_online_toggle') . '</label>';
$n['field'] = '<div class="checkbox"><label><input type="hidden" name="enable_online_toggle" value="0"><input type="checkbox" id="enable_online_toggle" name="enable_online_toggle" value="1"' . ($enableOnlineToggle ? ' checked' : '') . '> ' . rex_i18n::msg('yform_content_builder_enable_online_toggle_label') . '</label></div>';
$n['note'] = rex_i18n::msg('yform_content_builder_enable_online_toggle_notice');
$formElements[] = $n;

// Copy & Paste Toggle
$n = [];
$n['label'] = '<label for="enable_copy_paste">' . rex_i18n::msg('yform_content_builder_enable_copy_paste') . '</label>';
$n['field'] = '<div class="checkbox"><label><input type="hidden" name="enable_copy_paste" value="0"><input type="checkbox" id="enable_copy_paste" name="enable_copy_paste" value="1"' . ($enableCopyPaste ? ' checked' : '') . '> ' . rex_i18n::msg('yform_content_builder_enable_copy_paste_label') . '</label></div>';
$n['note'] = rex_i18n::msg('yform_content_builder_enable_copy_paste_notice');
$formElements[] = $n;

// Element Search Toggle
$n = [];
$n['label'] = '<label for="enable_element_search">' . rex_i18n::msg('yform_content_builder_enable_element_search') . '</label>';
$n['field'] = '<div class="checkbox"><label><input type="hidden" name="enable_element_search" value="0"><input type="checkbox" id="enable_element_search" name="enable_element_search" value="1"' . ($enableElementSearch ? ' checked' : '') . '> ' . rex_i18n::msg('yform_content_builder_enable_element_search_label') . '</label></div>';
$n['note'] = rex_i18n::msg('yform_content_builder_enable_element_search_notice');
$formElements[] = $n;

// Demo-Elemente Toggle
$n = [];
$n['label'] = '<label for="enable_demo_elements">' . rex_i18n::msg('yform_content_builder_enable_demo_elements') . '</label>';
$n['field'] = '<div class="checkbox"><label><input type="hidden" name="enable_demo_elements" value="0"><input type="checkbox" id="enable_demo_elements" name="enable_demo_elements" value="1"' . ($enableDemoElements ? ' checked' : '') . '> ' . rex_i18n::msg('yform_content_builder_enable_demo_elements_label') . '</label></div>';
$n['note'] = rex_i18n::msg('yform_content_builder_enable_demo_elements_notice');
$formElements[] = $n;

// Replace-Modus: Core-Elemente trotzdem verfügbar
$n = [];
$n['label'] = '<label for="replace_keep_core_elements">' . rex_i18n::msg('yform_content_builder_replace_keep_core_elements') . '</label>';
$n['field'] = '<input type="hidden" name="replace_keep_core_elements[]" value="">';
$n['field'] .= '<select class="form-control" id="replace_keep_core_elements" name="replace_keep_core_elements[]" multiple size="8">';
foreach ($coreElementOptions as $elementKey => $elementLabel) {
    $selected = in_array($elementKey, $replaceKeepCoreElements, true) ? ' selected' : '';
    $n['field'] .= '<option value="' . rex_escape($elementKey) . '"' . $selected . '>'
        . rex_escape($elementLabel . ' (' . $elementKey . ')')
        . '</option>';
}
$n['field'] .= '</select>';
$n['note'] = rex_i18n::msg('yform_content_builder_replace_keep_core_elements_notice');
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/form.php');

$content .= '</fieldset>';

// Submit-Button
$formElements = [];
$n = [];
$n['field'] = '<button class="btn btn-save rex-form-aligned" type="submit" name="save" value="1">' . rex_i18n::msg('yform_content_builder_save') . '</button>';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/submit.php');

// Formular ausgeben
$fragment = new rex_fragment();
$fragment->setVar('class', 'edit', false);
$fragment->setVar('title', rex_i18n::msg('yform_content_builder_settings'), false);
$fragment->setVar('body', '<form action="' . rex_url::currentBackendPage() . '" method="post">' . $content . '</form>', false);
echo $fragment->parse('core/page/section.php');

// Info-Box für Theme-Provider
if ($hasThemeProvider) {
    $infoContent = '<p><i class="fa fa-info-circle"></i> ' . rex_i18n::msg('yform_content_builder_theme_info') . '</p>';
    
    $fragment = new rex_fragment();
    $fragment->setVar('class', 'info', false);
    $fragment->setVar('title', rex_i18n::msg('yform_content_builder_theme_settings'), false);
    $fragment->setVar('body', $infoContent, false);
    echo $fragment->parse('core/page/section.php');
}

// =============================================================================
// YForm-Listen-Profile (für yform_list Element)
// =============================================================================
require __DIR__ . '/settings_yform_list_profiles.php';
