<?php

namespace FriendsOfREDAXO\YFormContentBuilder\Widgets;

/**
 * Datum Widget
 * 
 * Fügt ein Datumsfeld zu Elementen hinzu.
 * Demo-Widget für das Widget-System.
 */
class DateWidget extends ContentBuilderWidgetAbstract
{
    /**
     * @inheritDoc
     */
    public static function getType(): string
    {
        return 'date';
    }
    
    /**
     * @inheritDoc
     */
    public static function getLabel(): string
    {
        return 'Datum-Feld';
    }
    
    /**
     * @inheritDoc
     */
    public static function getDescription(): string
    {
        return 'Fügt ein Datumsfeld mit optionalem Label hinzu. Nützlich für Events, Artikel-Datum, etc.';
    }
    
    /**
     * @inheritDoc
     */
    public function getFields(): array
    {
        return [
            'date_value' => [
                'type' => 'text',
                'label' => 'Datum',
                'notice' => 'Format: DD.MM.YYYY oder YYYY-MM-DD'
            ],
            'date_label' => [
                'type' => 'text',
                'label' => 'Datum-Label',
                'default' => 'Datum'
            ],
            'date_show' => [
                'type' => 'checkbox',
                'label' => 'Datum anzeigen'
            ]
        ];
    }
    
    /**
     * @inheritDoc
     */
    public function getHookName(): string
    {
        return 'after_content';
    }
    
    /**
     * @inheritDoc
     */
    public function render(array $widgetData, string $framework = 'bootstrap'): string
    {
        $dateValue = $widgetData['date_value'] ?? '';
        $dateLabel = $widgetData['date_label'] ?? 'Datum';
        $dateShow = $widgetData['date_show'] ?? false;
        
        if (!$dateShow || empty($dateValue)) {
            return '';
        }
        
        // Datum formatieren
        $formattedDate = $this->formatDate($dateValue);
        
        $output = '';
        
        switch ($framework) {
            case 'uikit':
                $output .= '<div class="uk-margin">';
                $output .= '<span class="uk-text-meta">' . $this->escape($dateLabel) . ': </span>';
                $output .= '<time datetime="' . $this->escape($dateValue) . '">' . $this->escape($formattedDate) . '</time>';
                $output .= '</div>';
                break;
                
            case 'bootstrap':
            default:
                $output .= '<div class="date-widget">';
                $output .= '<span class="text-muted">' . $this->escape($dateLabel) . ': </span>';
                $output .= '<time datetime="' . $this->escape($dateValue) . '">' . $this->escape($formattedDate) . '</time>';
                $output .= '</div>';
                break;
        }
        
        return $output;
    }
    
    /**
     * Formatiert das Datum
     * 
     * @param string $date
     * @return string
     */
    private function formatDate(string $date): string
    {
        // Versuche verschiedene Formate
        $timestamp = strtotime($date);
        
        if ($timestamp === false) {
            return $date;
        }
        
        // Deutsches Format
        return date('d.m.Y', $timestamp);
    }
}
