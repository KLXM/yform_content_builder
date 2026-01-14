<?php

/**
 * YForm Content Builder - Einstellungen
 */

$addon = rex_addon::get('yform_content_builder');

// Formular verarbeiten
if (rex_post('save', 'bool')) {
    $addon->setConfig('theme', rex_post('theme', 'string', ''));
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

// Formular bauen
$content = '';
$content .= '<fieldset>';
$content .= '<legend>' . rex_i18n::msg('yform_content_builder_theme_settings') . '</legend>';

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
