<?php

/**
 * YForm Content Builder Helper
 * Einfache Frontend-Ausgabe von Slices
 */
class yform_content_builder_helper
{
    /**
     * Rendert Content Builder Slices im Frontend
     *
     * @param string $jsonContent JSON-String mit Slices
     * @param string $framework Framework für Templates (bootstrap|uikit|plain)
     * @return string HTML-Ausgabe
     */
    public static function render(string $jsonContent, string $framework = 'bootstrap'): string
    {
        $slices = json_decode($jsonContent, true);
        
        if (!is_array($slices) || empty($slices)) {
            return '';
        }
        
        $output = '';
        
        foreach ($slices as $slice) {
            $output .= self::renderSlice($slice, $framework);
        }
        
        return $output;
    }

    /**
     * Rendert ein einzelnes Slice
     *
     * @param array $slice Slice-Daten
     * @param string $framework Framework
     * @return string HTML-Ausgabe
     */
    protected static function renderSlice(array $slice, string $framework): string
    {
        $sliceType = $slice['type'] ?? '';
        $elementData = $slice['data'] ?? [];
        
        if (empty($sliceType)) {
            return '';
        }
        
        $addon = rex_addon::get('yform_content_builder');
        $elementPath = $addon->getPath('elements/' . $sliceType);
        $templateFile = $elementPath . '/templates/' . $framework . '.php';
        
        // Fallback auf plain.php
        if (!file_exists($templateFile)) {
            $templateFile = $elementPath . '/templates/plain.php';
        }
        
        if (!file_exists($templateFile)) {
            return '<!-- Element template not found: ' . rex_escape($sliceType) . ' -->';
        }
        
        ob_start();
        include $templateFile;
        return ob_get_clean();
    }

    /**
     * Wrapper für einfache Verwendung im Template
     *
     * @param mixed $dataset YOrm Dataset oder JSON-String
     * @param string $fieldName Feldname (optional, wenn Dataset übergeben wird)
     * @param string $framework Framework
     * @return string HTML-Ausgabe
     */
    public static function output($dataset, string $fieldName = 'content_builder', string $framework = 'bootstrap'): string
    {
        if (is_string($dataset)) {
            // Direkter JSON-String
            return self::render($dataset, $framework);
        }
        
        if (is_object($dataset) && method_exists($dataset, 'getValue')) {
            // YOrm Dataset
            $content = $dataset->getValue($fieldName);
            return self::render($content, $framework);
        }
        
        return '';
    }

    /**
     * Extrahiert alle Bilder aus dem Content für z.B. OG-Tags
     *
     * @param string $jsonContent JSON-String mit Slices
     * @return array Array mit Bild-Dateinamen
     */
    public static function extractImages(string $jsonContent): array
    {
        $slices = json_decode($jsonContent, true);
        $images = [];
        
        if (!is_array($slices)) {
            return $images;
        }
        
        foreach ($slices as $slice) {
            $data = $slice['data'] ?? [];
            
            // Durchsuche alle Felder nach Bildern
            foreach ($data as $key => $value) {
                if (is_string($value) && (
                    str_contains($key, 'image') || 
                    str_contains($key, 'media') || 
                    str_contains($key, 'bild')
                )) {
                    if (!empty($value)) {
                        $images[] = $value;
                    }
                }
                
                // Repeater-Felder durchsuchen
                if (is_array($value)) {
                    foreach ($value as $item) {
                        if (is_array($item)) {
                            foreach ($item as $subKey => $subValue) {
                                if (is_string($subValue) && (
                                    str_contains($subKey, 'image') || 
                                    str_contains($subKey, 'media') || 
                                    str_contains($subKey, 'bild')
                                )) {
                                    if (!empty($subValue)) {
                                        $images[] = $subValue;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        
        return array_unique($images);
    }

    /**
     * Extrahiert ersten Text für z.B. Meta-Description
     *
     * @param string $jsonContent JSON-String mit Slices
     * @param int $maxLength Maximale Länge
     * @return string Text ohne HTML
     */
    public static function extractFirstText(string $jsonContent, int $maxLength = 160): string
    {
        $slices = json_decode($jsonContent, true);
        
        if (!is_array($slices)) {
            return '';
        }
        
        foreach ($slices as $slice) {
            $data = $slice['data'] ?? [];
            
            foreach ($data as $key => $value) {
                if (is_string($value) && (
                    str_contains($key, 'text') || 
                    str_contains($key, 'content') || 
                    str_contains($key, 'beschreibung')
                )) {
                    $text = strip_tags($value);
                    $text = trim($text);
                    
                    if (!empty($text)) {
                        if (mb_strlen($text) > $maxLength) {
                            $text = mb_substr($text, 0, $maxLength - 3) . '...';
                        }
                        return $text;
                    }
                }
            }
        }
        
        return '';
    }
}
