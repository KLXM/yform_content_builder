<?php

/**
 * YForm Content Builder
 * Slice-based Content Builder for YForm
 */

// API-Klassen registrieren (namespaced, via rex_api_function::register)
rex_api_function::register('content_builder', \KLXM\YFormContentBuilder\Api\ContentBuilderApi::class);
rex_api_function::register('yform_list_columns', \KLXM\YFormContentBuilder\Api\ListColumnsApi::class);

// Theme Builder Integration - Theme für Backend setzen
if (rex::isBackend() && rex_addon::get('uikit_theme_builder')->isAvailable()) {
    $configuredTheme = rex_addon::get('yform_content_builder')->getConfig('theme');
    if ($configuredTheme && class_exists('UikitThemeBuilder\DomainContext')) {
        // Cache zurücksetzen und Theme setzen
        \UikitThemeBuilder\DomainContext::resetContext();
        \UikitThemeBuilder\DomainContext::setTheme($configuredTheme);
    }
}

// Extension Points registrieren
rex_extension::register('PACKAGES_INCLUDED', function() {
    // Templates registrieren
    rex_yform::addTemplatePath(rex_path::addon('yform_content_builder', 'ytemplates'));
});

// Assets für Backend einbinden
if (rex::isBackend()) {
    rex_view::addCssFile(rex_addon::get('yform_content_builder')->getAssetsUrl('content-builder.css'));
    rex_view::addCssFile(rex_addon::get('yform_content_builder')->getAssetsUrl('content-builder-dark.css'));
    rex_view::addCssFile(rex_addon::get('yform_content_builder')->getAssetsUrl('divider.css'));
    rex_view::addCssFile(rex_addon::get('yform_content_builder')->getAssetsUrl('cards.css'));
    rex_view::addJsFile(rex_addon::get('yform_content_builder')->getAssetsUrl('content-builder.js'));
    rex_view::addJsFile(rex_addon::get('yform_content_builder')->getAssetsUrl('media-browser.js'));
    rex_view::addJsFile(rex_addon::get('yform_content_builder')->getAssetsUrl('field-widgets.js'));

    // YForm Manager Assets laden (für YFormPickerField)
    if (rex_addon::get('yform')->isAvailable()) {
        rex_view::addJsFile(rex_addon::get('yform')->getAssetsUrl('widget.js'));
        rex_view::addJsFile(rex_addon::get('yform')->getAssetsUrl('manager.js'));
    }

    // YForm-Listen-Profile: AJAX-Spaltenlader nur auf der Settings-Subseite laden.
    if ('yform_content_builder/settings' === rex_be_controller::getCurrentPage()) {
        $ycbAddon = rex_addon::get('yform_content_builder');
        rex_view::addJsFile($ycbAddon->getAssetsUrl('yform_list_profiles.js'));
        rex_view::setJsProperty('YFL_API_URL', rex_url::backendController([
            'rex-api-call' => 'yform_list_columns',
        ]));
    }
}

// Assets für Frontend einbinden (CSS für Elemente)
if (!rex::isBackend()) {
    rex_view::addCssFile(rex_addon::get('yform_content_builder')->getAssetsUrl('divider.css'));
    rex_view::addCssFile(rex_addon::get('yform_content_builder')->getAssetsUrl('cards.css'));
}

