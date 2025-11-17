<?php

/**
 * YForm Content Builder
 * Slice-based Content Builder for YForm
 */

// YForm-Feldklasse laden (im globalen Namespace für YForm-Erkennung)
require_once rex_path::addon('yform_content_builder', 'lib/rex_yform_value_content_builder.php');

// Helper-Klasse laden
require_once rex_path::addon('yform_content_builder', 'lib/yform_content_builder_helper.php');

// Modul-Helper-Klasse laden (für Verwendung in normalen REDAXO Modulen)
require_once rex_path::addon('yform_content_builder', 'lib/yform_content_builder_module.php');

// AJAX-Handler laden
require_once rex_path::addon('yform_content_builder', 'lib/ajax_handler.php');

// Extension Points registrieren
rex_extension::register('PACKAGES_INCLUDED', function() {
    // Templates registrieren
    rex_yform::addTemplatePath(rex_path::addon('yform_content_builder', 'ytemplates'));
});

// AJAX-Anfragen im Backend verarbeiten
if (rex::isBackend()) {
    rex_extension::register('PAGE_CHECKED', function() {
        yform_content_builder_ajax_handler::handle();
    });
}

// Assets für Backend einbinden
if (rex::isBackend()) {
    rex_view::addCssFile(rex_addon::get('yform_content_builder')->getAssetsUrl('content-builder.css'));
    rex_view::addCssFile(rex_addon::get('yform_content_builder')->getAssetsUrl('divider.css'));
    rex_view::addCssFile(rex_addon::get('yform_content_builder')->getAssetsUrl('cards.css'));
    rex_view::addJsFile(rex_addon::get('yform_content_builder')->getAssetsUrl('content-builder.js'));
    rex_view::addJsFile(rex_addon::get('yform_content_builder')->getAssetsUrl('media-browser.js'));
}

// Assets für Frontend einbinden (CSS für Elemente)
if (!rex::isBackend()) {
    rex_view::addCssFile(rex_addon::get('yform_content_builder')->getAssetsUrl('divider.css'));
    rex_view::addCssFile(rex_addon::get('yform_content_builder')->getAssetsUrl('cards.css'));
}

