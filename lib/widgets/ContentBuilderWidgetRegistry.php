<?php

namespace FriendsOfREDAXO\YFormContentBuilder\Widgets;

use rex;
use rex_addon;
use rex_extension;
use rex_extension_point;

/**
 * Widget Registry für Content Builder
 * 
 * Verwaltet alle registrierten Widgets und stellt sie bereit.
 */
class ContentBuilderWidgetRegistry
{
    /** @var array<string, ContentBuilderWidgetInterface> */
    private static array $widgets = [];
    
    /** @var bool */
    private static bool $initialized = false;
    
    /**
     * Initialisiert die Registry
     */
    public static function init(): void
    {
        if (self::$initialized) {
            return;
        }
        
        // Standard-Widgets registrieren
        self::registerDefaults();
        
        // Extension Point für externe Addons
        $widgets = rex_extension::registerPoint(new rex_extension_point(
            'YFORM_CONTENT_BUILDER_WIDGETS',
            self::$widgets
        ));
        
        if (is_array($widgets)) {
            self::$widgets = $widgets;
        }
        
        self::$initialized = true;
    }
    
    /**
     * Registriert ein Widget
     * 
     * @param ContentBuilderWidgetInterface $widget
     */
    public static function register(ContentBuilderWidgetInterface $widget): void
    {
        self::$widgets[$widget::getType()] = $widget;
    }
    
    /**
     * Registriert Standard-Widgets
     */
    private static function registerDefaults(): void
    {
        // Demo-Widgets
        self::register(new DateWidget());
        self::register(new SocialMediaWidget());
    }
    
    /**
     * Gibt ein Widget zurück
     * 
     * @param string $type
     * @return ContentBuilderWidgetInterface|null
     */
    public static function get(string $type): ?ContentBuilderWidgetInterface
    {
        self::init();
        return self::$widgets[$type] ?? null;
    }
    
    /**
     * Gibt alle Widgets zurück
     * 
     * @return array<string, ContentBuilderWidgetInterface>
     */
    public static function getAll(): array
    {
        self::init();
        return self::$widgets;
    }
    
    /**
     * Gibt nur aktivierte Widgets zurück
     * 
     * @return array<string, ContentBuilderWidgetInterface>
     */
    public static function getEnabled(): array
    {
        self::init();
        
        $enabledWidgets = rex_addon::get('yform_content_builder')->getConfig('enabled_widgets', []);
        
        return array_filter(self::$widgets, function($widget) use ($enabledWidgets) {
            return in_array($widget::getType(), $enabledWidgets, true);
        });
    }
    
    /**
     * Gibt Widgets für einen bestimmten Hook zurück
     * 
     * @param string $hookName
     * @return array<string, ContentBuilderWidgetInterface>
     */
    public static function getForHook(string $hookName): array
    {
        $enabledWidgets = self::getEnabled();
        
        return array_filter($enabledWidgets, function($widget) use ($hookName) {
            return $widget->getHookName() === $hookName;
        });
    }
    
    /**
     * Gibt Widget-Felder für einen Hook zurück
     * 
     * @param string $hookName
     * @param string $prefix Optional: Prefix für Feldnamen
     * @return array
     */
    public static function getFieldsForHook(string $hookName, string $prefix = 'widget_'): array
    {
        $widgets = self::getForHook($hookName);
        $fields = [];
        
        foreach ($widgets as $widget) {
            $widgetFields = $widget->getFields();
            
            // Felder mit Widget-Typ prefixen
            foreach ($widgetFields as $fieldName => $fieldConfig) {
                $prefixedName = $prefix . $widget::getType() . '_' . $fieldName;
                $fields[$prefixedName] = $fieldConfig;
            }
        }
        
        return $fields;
    }
}
