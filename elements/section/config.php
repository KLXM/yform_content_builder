<?php
/**
 * Section / Container Element (Auto-Close Wrapper)
 * 
 * Definiert einen visuellen Container für nachfolgende Elemente.
 * Das nächste Section-Element oder das Ende schließt automatisch.
 */

// Prüfen ob uikit_theme_builder verfügbar ist
$hasUikitThemeBuilder = rex_addon::get('uikit_theme_builder')->isAvailable();

// Theme-Auswahl Optionen (nur wenn Theme Builder verfügbar)
$themeChoices = [];
if ($hasUikitThemeBuilder && class_exists('UikitThemeBuilder\DomainContext')) {
    $themeChoices = ['' => '-- Automatisch (Domain) --'];
    $availableThemes = \UikitThemeBuilder\DomainContext::getAvailableThemes();
    $themeChoices = array_merge($themeChoices, $availableThemes);
}

// Dynamische Optionen aus uikit_theme_builder
$backgroundOptions = [
    'none' => 'Keine',
    'light' => 'Hell (Grau)',
    'dark' => 'Dunkel',
    'primary' => 'Primary',
    'secondary' => 'Secondary',
    'muted' => 'Gedämpft',
    'white' => 'Weiß',
];

if ($hasUikitThemeBuilder && class_exists('UikitThemeBuilder\DomainContext')) {
    $backgroundOptions = array_merge($backgroundOptions, \UikitThemeBuilder\DomainContext::getBackgroundOptions());
}

return [
    'label' => 'Section / Container',
    'icon' => 'fa-object-group',
    'description' => 'Visueller Abschnitt mit Hintergrund (Auto-Close)',
    'auto_close' => true, // Markierung: Dieses Element ist ein Auto-Close Container
    
    'fields' => [
        'label' => [
            'type' => 'text',
            'label' => 'Bezeichnung',
            'notice' => 'Interne Bezeichnung (nicht sichtbar im Frontend)'
        ],
        
        'background_color' => [
            'type' => 'choice',
            'label' => 'Hintergrundfarbe',
            'choices' => $backgroundOptions,
            'default' => 'light'
        ],
        
        'background_image' => [
            'type' => 'be_media',
            'label' => 'Hintergrundbild',
            'notice' => 'Optional: Hintergrundbild statt Farbe'
        ],
        
        'padding_top' => [
            'type' => 'choice',
            'label' => 'Abstand oben',
            'choices' => [
                'none' => 'Kein',
                'small' => 'Klein',
                'medium' => 'Mittel',
                'large' => 'Groß',
                'xlarge' => 'Extra groß'
            ],
            'default' => 'medium'
        ],
        
        'padding_bottom' => [
            'type' => 'choice',
            'label' => 'Abstand unten',
            'choices' => [
                'none' => 'Kein',
                'small' => 'Klein',
                'medium' => 'Mittel',
                'large' => 'Groß',
                'xlarge' => 'Extra groß'
            ],
            'default' => 'medium'
        ],
        
        'container' => [
            'type' => 'choice',
            'label' => 'Container',
            'choices' => [
                'container' => 'Container (max-width)',
                'container-fluid' => 'Container Fluid (100%)',
                'none' => 'Kein Container'
            ],
            'default' => 'container',
            'notice' => 'Breite des Inhalts'
        ],
        
        'text_align' => [
            'type' => 'choice',
            'label' => 'Textausrichtung',
            'choices' => [
                '' => 'Standard',
                'left' => 'Links',
                'center' => 'Zentriert',
                'right' => 'Rechts'
            ],
            'default' => ''
        ],
        
        'custom_class' => [
            'type' => 'text',
            'label' => 'Custom CSS-Klasse',
            'notice' => 'Eigene CSS-Klasse für individuelle Styles'
        ],
        
        'custom_id' => [
            'type' => 'text',
            'label' => 'Custom ID',
            'notice' => 'Eigene ID für Anker-Links (z.B. #kontakt)'
        ],
    ]
];
