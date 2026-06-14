<?php

/**
 * YForm Content Builder - Einstellungen
 */

$addon = rex_addon::get('yform_content_builder');

// Formular verarbeiten
if (rex_post('save', 'bool')) {
    $addon->setConfig('theme', rex_post('theme', 'string', ''));
    $addon->setConfig('compact_mode', rex_post('compact_mode', 'bool', false));
    $addon->setConfig('enable_online_toggle', rex_post('enable_online_toggle', 'bool', false));
    $addon->setConfig('enable_copy_paste', rex_post('enable_copy_paste', 'bool', false));
    $addon->setConfig('enable_demo_elements', rex_post('enable_demo_elements', 'bool', true));

    $replaceKeepCoreElements = rex_post('replace_keep_core_elements', 'array', []);
    $replaceKeepCoreElements = array_values(array_unique(array_filter(array_map(
        static fn ($value): string => trim((string) $value),
        is_array($replaceKeepCoreElements) ? $replaceKeepCoreElements : []
    ), static fn (string $value): bool => $value !== '')));
    $addon->setConfig('replace_keep_core_elements', $replaceKeepCoreElements);

    echo rex_view::success(rex_i18n::msg('yform_content_builder_settings_saved'));
    
    // Theme Builder Cache zurücksetzen
    if (rex_addon::get('uikit_theme_builder')->isAvailable() && class_exists('UikitThemeBuilder\DomainContext')) {
        \UikitThemeBuilder\DomainContext::resetContext();
        \UikitThemeBuilder\DomainContext::setTheme($addon->getConfig('theme'));
    }
}

// Verfügbare Themes laden
$themes = ['' => '-- Kein Theme (Domain-Context verwenden) --'];
if (rex_addon::get('uikit_theme_builder')->isAvailable() && class_exists('UikitThemeBuilder\DomainContext')) {
    $availableThemes = \UikitThemeBuilder\DomainContext::getAvailableThemes();
    $themes = array_merge($themes, $availableThemes);
}

$currentTheme = $addon->getConfig('theme', '');
$compactMode = $addon->getConfig('compact_mode', false);
$enableOnlineToggle = $addon->getConfig('enable_online_toggle', false);
$enableCopyPaste = $addon->getConfig('enable_copy_paste', false);
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
$replaceKeepCoreElements = array_values(array_intersect($replaceKeepCoreElements, array_keys($coreElementOptions)));

// Formular bauen
$content = '';
$content .= '<fieldset>';
$content .= '<legend>' . rex_i18n::msg('yform_content_builder_general_settings') . '</legend>';

$formElements = [];

// Theme-Auswahl
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

// Info-Box
if (rex_addon::get('uikit_theme_builder')->isAvailable()) {
    $infoContent = '<p><i class="fa fa-info-circle"></i> ' . rex_i18n::msg('yform_content_builder_theme_info') . '</p>';
    
    $fragment = new rex_fragment();
    $fragment->setVar('class', 'info', false);
    $fragment->setVar('title', 'Info', false);
    $fragment->setVar('body', $infoContent, false);
    echo $fragment->parse('core/page/section.php');
} else {
    echo rex_view::warning(rex_i18n::msg('yform_content_builder_theme_builder_missing'));
}

// =============================================================================
// YForm-Listen-Profile (für yform_list Element)
// =============================================================================
require __DIR__ . '/settings_yform_list_profiles.php';
