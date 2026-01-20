<?php

namespace FriendsOfREDAXO\YFormContentBuilder\Widgets;

/**
 * Abstrakte Basisklasse für Content Builder Widgets
 * 
 * Stellt Hilfsmethoden für Widgets bereit.
 */
abstract class ContentBuilderWidgetAbstract implements ContentBuilderWidgetInterface
{
    /**
     * Prüft ob das Widget aktiviert ist
     * 
     * @return bool
     */
    public function isEnabled(): bool
    {
        $enabledWidgets = \rex_addon::get('yform_content_builder')->getConfig('enabled_widgets', []);
        return in_array(static::getType(), $enabledWidgets, true);
    }
    
    /**
     * Rendert ein Template
     * 
     * @param string $templatePath Pfad zum Template
     * @param array $data Daten für das Template
     * @return string
     */
    protected function renderTemplate(string $templatePath, array $data): string
    {
        if (!file_exists($templatePath)) {
            return '';
        }
        
        ob_start();
        extract($data);
        include $templatePath;
        return ob_get_clean();
    }
    
    /**
     * Escapet HTML
     * 
     * @param string $value
     * @return string
     */
    protected function escape(string $value): string
    {
        return \rex_escape($value);
    }
}
