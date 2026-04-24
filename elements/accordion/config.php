<?php
/**
 * Accordion / Tabs Element - Erweiterte Konfiguration
 */

// Zentrale Konfigurationsklasse
$config = yform_content_builder_config::class;
$hasThemeBuilder = $config::hasThemeBuilder();

return [
    'label' => 'Accordion / Tabs',
    'icon' => 'fa fa-list',
    'description' => 'Aufklappbare Inhaltsbereiche oder Tabs mit erweiterten Optionen',
    'repeater' => true,
    'settings_modal' => [
        'label' => 'Layout-Einstellungen',
        'icon' => 'fa-cog',
        'fields' => $config::getSettingsModalFields(['style'])
    ],
    'fields' => array_merge(
        // Element-spezifische Felder
        [
        // Display Settings
        'display_type' => [
            'type' => 'choice',
            'label' => 'Darstellung',
            'choices' => [
                'accordion' => 'Accordion (aufklappbar)',
                'tabs' => 'Tabs (nebeneinander)',
                'tabs-left' => 'Tabs (links vertikal)',
            ],
            'default' => 'accordion',
        ],
        
        // Accordion Options
        'accordion_collapsible' => [
            'type' => 'checkbox',
            'label' => 'Alle schließbar',
            'notice' => 'Erlaubt das Schließen aller Elemente gleichzeitig',
        ],
        'accordion_multiple' => [
            'type' => 'checkbox',
            'label' => 'Mehrere öffnen',
            'notice' => 'Mehrere Elemente können gleichzeitig geöffnet sein',
        ],
        'accordion_animation' => [
            'type' => 'choice',
            'label' => 'Animation',
            'choices' => [
                'true' => 'Standard (slide)',
                'false' => 'Keine',
            ],
            'default' => 'true',
        ],
        'first_open' => [
            'type' => 'checkbox',
            'label' => 'Erstes Element geöffnet',
            'default' => true,
        ],
        
        // Tab Options
        'tab_style' => [
            'type' => 'choice',
            'label' => 'Tab-Stil',
            'choices' => [
                'default' => 'Standard',
                'pill' => 'Pills (abgerundet)',
                'divider' => 'Mit Trennlinien',
            ],
            'default' => 'default',
        ],
        'tab_alignment' => [
            'type' => 'choice',
            'label' => 'Tab-Ausrichtung',
            'choices' => [
                'left' => 'Links',
                'center' => 'Zentriert',
                'right' => 'Rechts',
                'expand' => 'Volle Breite',
            ],
            'default' => 'left',
        ],
        
        // Style
        'style' => [
            'type' => 'choice',
            'label' => 'Stil',
            'choices' => [
                'default' => 'Standard',
                'primary' => 'Primary',
                'secondary' => 'Secondary',
                'muted' => 'Muted',
            ],
            'default' => 'default',
        ],
        
        // Items
        'items' => [
            'type' => 'repeater',
            'label' => 'Elemente',
            'fields' => [
                'title' => [
                    'type' => 'text',
                    'label' => 'Titel',
                ],
                'icon' => [
                    'type' => 'text',
                    'label' => 'Icon (optional)',
                    'notice' => 'UIkit-Icon: home, user, star, etc. | Font Awesome: fa-home',
                ],
                'content' => [
                    'type' => 'tinymce',
                    'profile' => 'default',
                    'label' => 'Inhalt',
                ],
                'image' => [
                    'type' => 'media',
                    'label' => 'Bild (optional)',
                    'notice' => 'Optionales Bild im Content-Bereich',
                ],
                'disabled' => [
                    'type' => 'checkbox',
                    'label' => 'Deaktiviert',
                    'notice' => 'Element wird ausgegraut dargestellt',
                ],
            ],
        ],
        ],
        
        // Section-Felder aus zentraler Config
        $config::getSectionFields()
    ),
];
