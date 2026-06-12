<?php
/**
 * Überschrift Element - Konfiguration mit zentraler Config
 */

// ============================================================================
// EXTRA FELDER - von außen erweiterbar
// ============================================================================
$extra = [];

// Lade HeadlineExtra wenn vorhanden (aus beliebigen Addons)
if (class_exists('HeadlineExtra') && method_exists('HeadlineExtra', 'GetConfig')) {
    $extra = HeadlineExtra::GetConfig();
}

// Zentrale Konfigurationsklasse
$config = \KLXM\YFormContentBuilder\Config::class;
$hasThemeBuilder = $config::hasThemeBuilder();

// Dynamische Farboptionen
$colorOptions = [
    '' => 'Standard',
    'primary' => 'Primary',
    'secondary' => 'Secondary',
    'success' => 'Success',
    'warning' => 'Warning',
    'danger' => 'Danger',
    'muted' => 'Grau',
];

// ThemeBuilder Farben nur als Strings hinzufügen
if ($hasThemeBuilder && class_exists('UikitThemeBuilder\DomainContext')) {
    $themeColors = \UikitThemeBuilder\DomainContext::getTextColorOptions();
    if (is_array($themeColors)) {
        foreach ($themeColors as $key => $value) {
            // Nur hinzufügen wenn Wert ein String ist, nicht array
            if (is_string($value)) {
                $colorOptions[$key] = $value;
            }
        }
    }
}

return [
    'label' => 'Überschrift',
    'icon' => 'fa fa-header',
    'description' => 'Überschrift mit Styling-Optionen',
    'version' => '1.13.0',
    'category' => 'content',
    
    // Tab-Gruppierung
    'field_groups' => [
        'content_tab' => [
            'label' => 'Inhalt',
            'icon' => 'fa-text-width',
            'fields' => array_merge(
                ['text', 'tag', 'size', 'modifier'],
                array_keys($extra) // Extra-Felder hinzufügen
            )
        ],
        'style_tab' => [
            'label' => 'Styling',
            'icon' => 'fa-paint-brush',
            'fields' => ['alignment', 'color', 'underline', 'spacing_top', 'spacing_bottom']
        ],
        'link_tab' => [
            'label' => 'Link',
            'icon' => 'fa-link',
            'fields' => ['link_type', 'link_url', 'link_internal']
        ],
        'section_tab' => [
            'label' => 'Sektion',
            'icon' => 'fa-columns',
            'fields' => $config::getOptionalSectionFieldNames()
        ]
    ],
    
    'fields' => array_merge(
        // Element-spezifische Felder
        [
        'text' => [
            'type' => 'text',
            'label' => 'Überschrift',
            'required' => true
        ],
        'tag' => [
            'type' => 'choice',
            'label' => 'HTML-Tag',
            'choices' => [
                'h1' => 'H1 (Hauptüberschrift)',
                'h2' => 'H2',
                'h3' => 'H3',
                'h4' => 'H4',
                'h5' => 'H5',
                'h6' => 'H6'
            ],
            'default' => 'h2'
        ],
        'size' => [
            'type' => 'choice',
            'label' => 'Größe',
            'choices' => [
                '' => 'Standard (entsprechend Tag)',
                'small' => 'Klein (uk-heading-small)',
                'medium' => 'Mittel (uk-heading-medium)',
                'large' => 'Groß (uk-heading-large)',
                'xlarge' => 'X-Groß (uk-heading-xlarge)',
                '2xlarge' => '2X-Groß (uk-heading-2xlarge)',
                '3xlarge' => '3X-Groß (uk-heading-3xlarge)'
            ],
            'default' => ''
        ],
        'modifier' => [
            'type' => 'choice',
            'label' => 'UIkit Modifier',
            'choices' => [
                '' => 'Keine',
                'divider' => 'Mit Trennlinie (uk-heading-divider)',
                'bullet' => 'Mit Kugel (uk-heading-bullet)',
                'line' => 'Mit Mittel-Linie (uk-heading-line)'
            ],
            'default' => '',
            'notice' => 'UIkit-spezifische Formatierungen'
        ],
        'alignment' => [
            'type' => 'choice',
            'label' => 'Ausrichtung',
            'choices' => [
                'left' => 'Links',
                'center' => 'Zentriert',
                'right' => 'Rechts'
            ],
            'default' => 'left'
        ],
        'color' => [
            'type' => 'choice',
            'label' => 'Farbe',
            'choices' => $colorOptions,
            'default' => ''
        ],
        'spacing_top' => [
            'type' => 'choice',
            'label' => 'Abstand oben',
            'choices' => [
                '' => 'Standard',
                'none' => 'Kein',
                'small' => 'Klein',
                'medium' => 'Mittel',
                'large' => 'Groß'
            ],
            'default' => ''
        ],
        'spacing_bottom' => [
            'type' => 'choice',
            'label' => 'Abstand unten',
            'choices' => [
                '' => 'Standard',
                'none' => 'Kein',
                'small' => 'Klein',
                'medium' => 'Mittel',
                'large' => 'Groß'
            ],
            'default' => ''
        ],
        'underline' => [
            'type' => 'checkbox',
            'label' => 'Unterstreichung anzeigen'
        ],
        'link_type' => [
            'type' => 'choice',
            'label' => 'Als Link',
            'choices' => [
                '' => 'Kein Link',
                'external' => 'Externe URL',
                'internal' => 'Interne Seite'
            ],
            'default' => ''
        ],
        'link_url' => [
            'type' => 'text',
            'label' => 'Externe URL'
        ],
        'link_internal' => [
            'type' => 'be_link',
            'label' => 'Interne Seite'
        ]
        ],
        
        // Section-Felder aus zentraler Config
        $config::getOptionalSectionFields(),
        
        // Extra-Felder wenn vorhanden
        $extra
    ),
];
