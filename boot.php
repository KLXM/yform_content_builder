<?php

/**
 * YForm Content Builder
 * Slice-based Content Builder for YForm
 */

// === PHASE 1: Config-Klassen registrieren ===
require_once rex_path::addon('yform_content_builder', 'lib/config/FrameworkConfig.php');
require_once rex_path::addon('yform_content_builder', 'lib/config/EditorConfig.php');
require_once rex_path::addon('yform_content_builder', 'lib/config/ElementRegistry.php');
require_once rex_path::addon('yform_content_builder', 'lib/config/ElementModeResolver.php');
require_once rex_path::addon('yform_content_builder', 'lib/config/ThemeProviderBridge.php');

// API-Klassen registrieren (namespaced, via rex_api_function::register)
rex_api_function::register('content_builder', \KLXM\YFormContentBuilder\Api\ContentBuilderApi::class);
rex_api_function::register('yform_list_columns', \KLXM\YFormContentBuilder\Api\ListColumnsApi::class);

if (rex_addon::get('yform')->isAvailable()) {
    rex_extension::register('MEDIA_IS_IN_USE', [\KLXM\YFormContentBuilder\MediaInUse::class, 'isMediaInUse']);
}

// Theme-Provider Integration: konfiguriertes Backend-Theme anwenden
if (rex::isBackend()) {
    $configuredTheme = (string) rex_addon::get('yform_content_builder')->getConfig('theme', '');
    if ($configuredTheme !== '') {
        \KLXM\YFormContentBuilder\Config\ThemeProviderBridge::resetThemeContext();
        \KLXM\YFormContentBuilder\Config\ThemeProviderBridge::setTheme($configuredTheme);
    }
}

// Extension Points registrieren
rex_extension::register('PACKAGES_INCLUDED', function() {
    // Templates registrieren
    rex_yform::addTemplatePath(rex_path::addon('yform_content_builder', 'ytemplates'));
});

// Assets für Backend einbinden
if (rex::isBackend()) {
    $addon = rex_addon::get('yform_content_builder');
    $assetUrl = static function (string $assetPath) use ($addon): string {
        $url = $addon->getAssetsUrl($assetPath);
        $file = $addon->getAssetsPath($assetPath);
        if (is_file($file)) {
            $mtime = filemtime($file);
            if (false !== $mtime) {
                $url .= '?v=' . $mtime;
            }
        }

        return $url;
    };

    rex_view::addCssFile($assetUrl('content-builder.css'));
    rex_view::addCssFile($assetUrl('content-builder-dark.css'));
    rex_view::addCssFile($assetUrl('divider.css'));
    rex_view::addCssFile($assetUrl('cards.css'));
    rex_view::addJsFile($assetUrl('content-builder.js'));
    rex_view::addJsFile($assetUrl('media-browser.js'));
    rex_view::addJsFile($assetUrl('field-widgets.js'));

    // YForm Manager Assets laden (für YFormPickerField)
    if (rex_addon::get('yform')->isAvailable()) {
        rex_view::addJsFile(rex_addon::get('yform')->getAssetsUrl('widget.js'));
        rex_view::addJsFile(rex_addon::get('yform')->getAssetsUrl('manager.js'));
    }

    // YForm-Listen-Profile: AJAX-Spaltenlader nur auf der Settings-Subseite laden.
    if ('yform_content_builder/settings' === rex_be_controller::getCurrentPage()) {
        rex_view::addJsFile($assetUrl('yform_list_profiles.js'));
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

