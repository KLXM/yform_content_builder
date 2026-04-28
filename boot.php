<?php

/**
 * YForm Content Builder
 * Slice-based Content Builder for YForm
 */

// YForm-Feldklasse laden (im globalen Namespace für YForm-Erkennung)
require_once rex_path::addon('yform_content_builder', 'lib/rex_yform_value_content_builder.php');

// Helper-Klasse laden
require_once rex_path::addon('yform_content_builder', 'lib/yform_content_builder_helper.php');

// Config-Klasse laden
require_once rex_path::addon('yform_content_builder', 'lib/yform_content_builder_config.php');

// Help-Modal-Helper laden
require_once rex_path::addon('yform_content_builder', 'lib/yform_content_builder_help_modal_helper.php');

// SVG-Helper laden
require_once rex_path::addon('yform_content_builder', 'lib/yform_content_builder_svg.php');

// Media Manager Helper laden
require_once rex_path::addon('yform_content_builder', 'lib/yform_content_media_manager_helper.php');

// Medien-ALT-Resolver laden (elementübergreifende ALT-Logik)
require_once rex_path::addon('yform_content_builder', 'lib/YFormContentBuilderMediaAltResolver.php');

// Modul-Helper-Klasse laden (für Verwendung in normalen REDAXO Modulen)
require_once rex_path::addon('yform_content_builder', 'lib/yform_content_builder_module.php');

// YForm-Listen-Profile + Renderer (für yform_list Element)
require_once rex_path::addon('yform_content_builder', 'lib/YformListProfiles.php');
require_once rex_path::addon('yform_content_builder', 'lib/YformListRenderer.php');
require_once rex_path::addon('yform_content_builder', 'lib/rex_api_yform_list_columns.php');

// Forcal-Termine-Renderer (für forcal_list Element) – nur wenn forcal-Addon vorhanden
if (rex_addon::get('forcal')->isAvailable()) {
    require_once rex_path::addon('yform_content_builder', 'lib/ForcalListRenderer.php');
    class_alias(\KLXM\YFormContentBuilder\ForcalListRenderer::class, 'ForcalListRenderer');
}

// Field-Klassen laden (Plugin-System für Feldtypen)
foreach (glob(rex_path::addon('yform_content_builder', 'lib/fields/*.php')) as $fieldFile) {
    require_once $fieldFile;
}

// Class aliases for backward compatibility (namespaced classes)
class_alias(\KLXM\YFormContentBuilder\yform_content_builder_helper::class, 'yform_content_builder_helper');
class_alias(\KLXM\YFormContentBuilder\YFormContentBuilderMediaAltResolver::class, 'YFormContentBuilderMediaAltResolver');
class_alias(\KLXM\YFormContentBuilder\YformListProfiles::class, 'YformListProfiles');
class_alias(\KLXM\YFormContentBuilder\YformListRenderer::class, 'YformListRenderer');
class_alias(\KLXM\YFormContentBuilder\yform_content_builder_config::class, 'yform_content_builder_config');
class_alias(\KLXM\YFormContentBuilder\yform_content_builder_module::class, 'yform_content_builder_module');
class_alias(\KLXM\YFormContentBuilder\yform_content_builder_help_modal_helper::class, 'yform_content_builder_help_modal_helper');
class_alias(\KLXM\YFormContentBuilder\YFormContentBuilderSvg::class, 'YFormContentBuilderSvg');
class_alias(\KLXM\YFormContentBuilder\YFormContentMediaManagerHelper::class, 'YFormContentMediaManagerHelper');

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

