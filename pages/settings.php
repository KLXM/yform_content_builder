<?php

/**
 * YForm Content Builder - Einstellungen
 */

$addon = rex_addon::get('yform_content_builder');

use FriendsOfREDAXO\YFormContentBuilder\Widgets\ContentBuilderWidgetRegistry;

// Formular verarbeiten
if (rex_post('save', 'bool')) {
    $addon->setConfig('theme', rex_post('theme', 'string', ''));
    $addon->setConfig('compact_mode', rex_post('compact_mode', 'bool', false));
    $addon->setConfig('enabled_widgets', rex_post('enabled_widgets', 'array', []));
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
$enabledWidgets = $addon->getConfig('enabled_widgets', []);

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

// Kompaktmodus-Toggle
$n = [];
$n['label'] = '<label for="compact_mode">' . rex_i18n::msg('yform_content_builder_compact_mode') . '</label>';
$n['field'] = '<div class="checkbox"><label><input type="hidden" name="compact_mode" value="0"><input type="checkbox" id="compact_mode" name="compact_mode" value="1"' . ($compactMode ? ' checked' : '') . '> ' . rex_i18n::msg('yform_content_builder_compact_mode_label') . '</label></div>';
$n['note'] = rex_i18n::msg('yform_content_builder_compact_mode_notice');
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

// Widgets-Sektion
$widgetContent = '';
$widgetContent .= '<fieldset>';
$widgetContent .= '<legend>' . rex_i18n::msg('yform_content_builder_widgets') . '</legend>';

$widgetFormElements = [];

// Widget-Liste
ContentBuilderWidgetRegistry::init();
$allWidgets = ContentBuilderWidgetRegistry::getAll();

if (!empty($allWidgets)) {
    $widgetListHtml = '<div class="table-responsive"><table class="table table-striped">';
    $widgetListHtml .= '<thead><tr>';
    $widgetListHtml .= '<th style="width: 30px;"></th>';
    $widgetListHtml .= '<th>' . rex_i18n::msg('yform_content_builder_widget_name') . '</th>';
    $widgetListHtml .= '<th>' . rex_i18n::msg('yform_content_builder_widget_description') . '</th>';
    $widgetListHtml .= '<th>' . rex_i18n::msg('yform_content_builder_widget_hook') . '</th>';
    $widgetListHtml .= '</tr></thead>';
    $widgetListHtml .= '<tbody>';
    
    foreach ($allWidgets as $widget) {
        $type = $widget::getType();
        $checked = in_array($type, $enabledWidgets, true) ? ' checked' : '';
        
        $widgetListHtml .= '<tr>';
        $widgetListHtml .= '<td><input type="checkbox" name="enabled_widgets[]" value="' . rex_escape($type) . '"' . $checked . '></td>';
        $widgetListHtml .= '<td><strong>' . rex_escape($widget::getLabel()) . '</strong><br><small class="text-muted">' . rex_escape($type) . '</small></td>';
        $widgetListHtml .= '<td>' . rex_escape($widget::getDescription()) . '</td>';
        $widgetListHtml .= '<td><code>' . rex_escape($widget->getHookName()) . '</code></td>';
        $widgetListHtml .= '</tr>';
    }
    
    $widgetListHtml .= '</tbody></table></div>';
    
    $n = [];
    $n['label'] = '';
    $n['field'] = $widgetListHtml;
    $n['note'] = rex_i18n::msg('yform_content_builder_widgets_notice');
    $formElements[] = $n;
} else {
    $n = [];
    $n['label'] = '';
    $n['field'] = '<div class="alert alert-info">' . rex_i18n::msg('yform_content_builder_no_widgets') . '</div>';
    $formElements[] = $n;
}

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/form.php');

$content .= '</fieldset>';

// Submit-Button (für alle Einstellungen)
$formElements = [];
$n = [];
$n['field'] = '<button class="btn btn-save rex-form-aligned" type="submit" name="save" value="1">' . rex_i18n::msg('yform_content_builder_save') . '</button>';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/submit.php');

// Ein Formular mit allen Einstellungen ausgeben
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
