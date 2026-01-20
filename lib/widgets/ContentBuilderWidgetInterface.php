<?php

namespace FriendsOfREDAXO\YFormContentBuilder\Widgets;

/**
 * Interface für Content Builder Widgets
 * 
 * Widgets können zusätzliche Felder zu bestehenden Elementen hinzufügen.
 * Externe AddOns können eigene Widgets registrieren.
 */
interface ContentBuilderWidgetInterface
{
    /**
     * Eindeutiger Typ-Name des Widgets
     * 
     * @return string z.B. 'social_media', 'date_field', 'contact_picker'
     */
    public static function getType(): string;
    
    /**
     * Label des Widgets (für Settings-Seite)
     * 
     * @return string z.B. 'Social Media Links', 'Datum-Feld'
     */
    public static function getLabel(): string;
    
    /**
     * Beschreibung des Widgets (für Settings-Seite)
     * 
     * @return string
     */
    public static function getDescription(): string;
    
    /**
     * Felder, die das Widget hinzufügt
     * 
     * @return array Array von Feld-Konfigurationen im Content Builder Format
     */
    public function getFields(): array;
    
    /**
     * Hook-Name, an dem das Widget einhaken soll
     * 
     * @return string z.B. 'before_content', 'after_settings', 'in_repeater'
     */
    public function getHookName(): string;
    
    /**
     * Render-Funktion für Frontend-Ausgabe
     * 
     * @param array $widgetData Die Daten des Widgets
     * @param string $framework Framework-Name (bootstrap, uikit, plain)
     * @return string HTML-Output
     */
    public function render(array $widgetData, string $framework = 'bootstrap'): string;
}
