<?php
/**
 * Trennlinie Element - Konfiguration mit zentraler Config
 */

// Zentrale Konfigurationsklasse
$config = yform_content_builder_config::class;
$hasThemeBuilder = $config::hasThemeBuilder();

// Dynamische Farboptionen
$colorOptions = [
    'default' => 'Standard (Grau)',
    'primary' => 'Primary',
    'secondary' => 'Secondary',
    'success' => 'Success',
    'warning' => 'Warning',
    'danger' => 'Danger',
];

if ($hasThemeBuilder && class_exists('UikitThemeBuilder\DomainContext')) {
    $colorOptions = array_merge($colorOptions, \UikitThemeBuilder\DomainContext::getTextColorOptions());
}

return [
    'label' => 'Trennlinie',
    'icon' => 'fa fa-minus',
    'description' => 'Visuelle Trennelement mit verschiedenen Styles',
    'settings_modal' => [
        'label' => 'Section-Einstellungen',
        'icon' => 'fa-cog',
        'fields' => $config::getSectionFieldNames()
    ],
    'fields' => array_merge(
        // Element-spezifische Felder
        [
        'style' => [
            'type' => 'choice',
            'label' => 'Style',
            'choices' => [
                'simple' => 'Einfache Linie',
                'double' => 'Doppelte Linie',
                'dotted' => 'Gepunktet',
                'dashed' => 'Gestrichelt',
                'thick' => 'Dicke Linie',
                'gradient' => 'Farbverlauf',
                'icon' => 'Linie mit Icon',
                'text' => 'Linie mit Text',
                'scroll' => 'Scroll-Animation (Chevron)'
            ],
            'default' => 'simple'
        ],
        'icon' => [
            'type' => 'text',
            'label' => 'Icon Klasse',
            'notice' => 'Nur bei Style "icon", z.B. fa fa-star',
            'default' => 'fa fa-star'
        ],
        'text' => [
            'type' => 'text',
            'label' => 'Text',
            'notice' => 'Nur bei Style "text"'
        ],
        'color' => [
            'type' => 'choice',
            'label' => 'Farbe',
            'choices' => $colorOptions,
            'default' => 'default'
        ],
        'width' => [
            'type' => 'choice',
            'label' => 'Breite',
            'choices' => [
                'full' => '100%',
                'wide' => '80%',
                'medium' => '60%',
                'narrow' => '40%'
            ],
            'default' => 'full'
        ],
        'spacing_top' => [
            'type' => 'choice',
            'label' => 'Abstand oben',
            'choices' => [
                'small' => 'Klein (20px)',
                'medium' => 'Mittel (40px)',
                'large' => 'Groß (60px)'
            ],
            'default' => 'medium'
        ],
        'spacing_bottom' => [
            'type' => 'choice',
            'label' => 'Abstand unten',
            'choices' => [
                'small' => 'Klein (20px)',
                'medium' => 'Mittel (40px)',
                'large' => 'Groß (60px)'
            ],
            'default' => 'medium'
        ]
        ],
        
        // Section-Felder aus zentraler Config
        $config::getSectionFields()
    ),
];
