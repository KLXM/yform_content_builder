<?php

namespace KLXM\YFormContentBuilder\Config;

use rex;
use rex_addon;
use rex_extension;

/**
 * Zentrale Aufloesung von Element-Modus und registrierten Element-Pfaden.
 */
final class ElementModeResolver
{
    private const CONFIG_KEY_REPLACE_KEEP_CORE_ELEMENTS = 'replace_keep_core_elements';

    /**
     * @return array<int, string>
     */
    public static function getCustomPaths(): array
    {
        $customPaths = rex_extension::registerPoint(new \rex_extension_point(
            'YFORM_CONTENT_BUILDER_ELEMENT_PATHS',
            []
        ));

        $normalized = [];
        if (is_array($customPaths)) {
            foreach ($customPaths as $customPath) {
                $path = trim((string) $customPath);
                if ($path !== '') {
                    $normalized[] = rtrim($path, '/\\') . '/';
                }
            }
        }

        if ($normalized === [] && rex_addon::exists('project') && rex_addon::get('project')->isAvailable()) {
            $projectPath = rex_addon::get('project')->getPath('elements/');
            if (is_dir($projectPath)) {
                $normalized[] = $projectPath;
            }
        }

        return array_values(array_unique($normalized));
    }

    public static function getElementMode(): string
    {
        $elementMode = (string) rex_extension::registerPoint(new \rex_extension_point(
            'YFORM_CONTENT_BUILDER_ELEMENT_MODE',
            'replace'
        ));

        return in_array($elementMode, ['replace', 'merge'], true) ? $elementMode : 'replace';
    }

    /**
     * Liefert Core-Elemente, die trotz replace-Modus verfuegbar bleiben sollen.
     *
     * @return array<int, string>
     */
    public static function getReplaceKeepCoreElements(): array
    {
        $raw = rex_addon::get('yform_content_builder')->getConfig(self::CONFIG_KEY_REPLACE_KEEP_CORE_ELEMENTS, []);

        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $raw = $decoded;
            } else {
                $raw = array_map('trim', explode(',', $raw));
            }
        }

        if (!is_array($raw)) {
            return [];
        }

        $normalized = [];
        foreach ($raw as $elementKey) {
            $elementKey = trim((string) $elementKey);
            if ($elementKey !== '') {
                $normalized[] = $elementKey;
            }
        }

        return array_values(array_unique($normalized));
    }
}
