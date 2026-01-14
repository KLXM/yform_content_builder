<?php
/**
 * Slideshow Element - Konfiguration mit zentraler Config
 */

// Zentrale Konfigurationsklasse
$config = yform_content_builder_config::class;
$hasThemeBuilder = $config::hasThemeBuilder();

// Dynamische Optionen aus uikit_theme_builder
$overlayOptions = [
    'glass' => 'Glasmorphism',
    'dark' => 'Dunkel',
    'light' => 'Hell',
    'none' => 'Kein Hintergrund',
];

// Settings Modal Felder aufbauen
$settingsModalFields = ['ratio', 'animation', 'autoplay', 'interval', 'show_navigation', 'show_dots', 'is_viewport', 'container', 'margin', 'custom_id', 'custom_classes'];
$settingsModalFields = array_merge($settingsModalFields, $config::getSectionFieldNames());

return [
    'label' => 'Slideshow',
    'icon' => 'fa fa-images',
    'description' => 'Moderne Slideshow mit Bildern/Videos, Text-Overlays und Link-Optionen',
    'settings_modal' => [
        'label' => 'Slideshow Einstellungen',
        'icon' => 'fa-cog',
        'fields' => $settingsModalFields
    ],
    'fields' => array_merge(
        // Element-spezifische Felder
        [
        // Globale Slideshow-Einstellungen
        'ratio' => [
            'type' => 'choice',
            'label' => 'Seitenverhältnis',
            'choices' => [
                '16:9' => '16:9 (Standard)',
                '4:3' => '4:3',
                '1:1' => '1:1 (Quadratisch)',
                '21:9' => '21:9 (Ultrawide)',
                'viewport' => 'Viewport-Höhe (Vollbild)'
            ],
            'default' => '16:9'
        ],
        'animation' => [
            'type' => 'choice',
            'label' => 'Übergangs-Animation',
            'choices' => [
                'fade' => 'Fade',
                'slide' => 'Slide',
                'scale' => 'Scale',
                'pull' => 'Pull',
                'push' => 'Push'
            ],
            'default' => 'fade'
        ],
        'autoplay' => [
            'type' => 'checkbox',
            'label' => 'Autoplay aktivieren'
        ],
        'interval' => [
            'type' => 'choice',
            'label' => 'Autoplay-Intervall',
            'choices' => [
                '3000' => '3 Sekunden',
                '4000' => '4 Sekunden',
                '5000' => '5 Sekunden',
                '6000' => '6 Sekunden',
                '8000' => '8 Sekunden',
                '10000' => '10 Sekunden'
            ],
            'default' => '6000'
        ],
        'show_navigation' => [
            'type' => 'checkbox',
            'label' => 'Navigation-Pfeile anzeigen',
            'default' => true
        ],
        'show_dots' => [
            'type' => 'checkbox',
            'label' => 'Punkt-Navigation anzeigen'
        ],
        'is_viewport' => [
            'type' => 'checkbox',
            'label' => 'Vollbild-Höhe (Viewport)'
        ],
        'container' => [
            'type' => 'choice',
            'label' => 'Container-Breite',
            'choices' => [
                '' => 'Kein Container',
                'uk-container' => 'Standard Container',
                'uk-container uk-container-small' => 'Kleiner Container',
                'uk-container uk-container-large' => 'Großer Container',
                'uk-container uk-container-xlarge' => 'Extra großer Container'
            ],
            'default' => ''
        ],
        'margin' => [
            'type' => 'choice',
            'label' => 'Außenabstände',
            'choices' => [
                '' => 'Keine Abstände',
                'uk-margin-medium' => 'Medium Abstand',
                'uk-margin-large' => 'Großer Abstand',
                'uk-margin-xlarge' => 'Extra großer Abstand'
            ],
            'default' => ''
        ],
        'custom_id' => [
            'type' => 'text',
            'label' => 'CSS ID',
            'placeholder' => 'z.B. hero-slideshow'
        ],
        'custom_classes' => [
            'type' => 'text',
            'label' => 'CSS Klassen',
            'placeholder' => 'Zusätzliche CSS-Klassen'
        ],
        // Slides Repeater
        'slides' => [
            'type' => 'repeater',
            'label' => 'Slides',
            'item_modal' => [
                'label' => 'Text-Design & Links',
                'icon' => 'fa-paint-brush',
                'fields' => ['text_position', 'text_background', 'text_align', 'title_size', 'text_size', 'title_handwriting', 'title_slanted', 'link_type', 'link', 'link_text', 'link_target', 'button_style', 'button_size']
            ],
            'fields' => [
                'media' => [
                    'type' => 'be_media',
                    'label' => 'Bild oder Video (MP4)',
                    'required' => true,
                    'notice' => 'Unterstützt Bilder und MP4-Videos'
                ],
                'title' => [
                    'type' => 'text',
                    'label' => 'Titel',
                    'placeholder' => 'Slide-Titel'
                ],
                'text' => [
                    'type' => 'textarea',
                    'label' => 'Text',
                    'placeholder' => 'Beschreibungstext für den Slide',
                    'rows' => 3
                ],
                'text_position' => [
                    'type' => 'choice',
                    'label' => 'Text-Position',
                    'choices' => [
                        'uk-position-bottom-center uk-text-center' => 'Unten zentriert',
                        'uk-position-bottom-left uk-text-left' => 'Unten links',
                        'uk-position-bottom-right uk-text-right' => 'Unten rechts',
                        'uk-position-top-center uk-text-center' => 'Oben zentriert',
                        'uk-position-top-left uk-text-left' => 'Oben links',
                        'uk-position-top-right uk-text-right' => 'Oben rechts',
                        'uk-position-center-center uk-text-center' => 'Mitte zentriert',
                        'uk-position-center-left uk-text-left' => 'Mitte links',
                        'uk-position-center-right uk-text-right' => 'Mitte rechts'
                    ],
                    'default' => 'uk-position-bottom-center uk-text-center'
                ],
                'text_background' => [
                    'type' => 'choice',
                    'label' => 'Text-Hintergrund',
                    'choices' => $overlayOptions,
                    'default' => 'glass'
                ],
                'text_align' => [
                    'type' => 'choice',
                    'label' => 'Text-Ausrichtung',
                    'choices' => [
                        'uk-text-left' => 'Linksbündig',
                        'uk-text-center' => 'Zentriert',
                        'uk-text-right' => 'Rechtsbündig'
                    ],
                    'default' => 'uk-text-center'
                ],
                'title_size' => [
                    'type' => 'choice',
                    'label' => 'Titel-Größe',
                    'choices' => [
                        'uk-heading-small' => 'Klein',
                        'uk-heading-medium' => 'Medium',
                        'uk-heading-large' => 'Groß',
                        'uk-heading-xlarge' => 'Extra Groß',
                        'uk-heading-2xlarge' => 'XXL'
                    ],
                    'default' => 'uk-heading-large'
                ],
                'text_size' => [
                    'type' => 'choice',
                    'label' => 'Text-Größe',
                    'choices' => [
                        'uk-text-small' => 'Klein',
                        'uk-text-default' => 'Standard',
                        'uk-text-lead' => 'Lead Text',
                        'uk-text-large' => 'Groß'
                    ],
                    'default' => 'uk-text-lead'
                ],
                'title_handwriting' => [
                    'type' => 'checkbox',
                    'label' => 'Handschrift-Font für Titel'
                ],
                'title_slanted' => [
                    'type' => 'checkbox',
                    'label' => 'Titel schräg stellen'
                ],
                'link_type' => [
                    'type' => 'choice',
                    'label' => 'Link-Art',
                    'choices' => [
                        'button' => 'Button',
                        'slide' => 'Ganzer Slide verlinkt'
                    ],
                    'default' => 'button'
                ],
                'link' => [
                    'type' => 'be_link',
                    'label' => 'Link-Ziel'
                ],
                'link_text' => [
                    'type' => 'text',
                    'label' => 'Button-Text',
                    'placeholder' => 'z.B. Mehr erfahren',
                    'default' => 'Mehr erfahren'
                ],
                'link_target' => [
                    'type' => 'choice',
                    'label' => 'Link-Ziel',
                    'choices' => [
                        '_self' => 'Gleiches Fenster',
                        '_blank' => 'Neues Fenster'
                    ],
                    'default' => '_self'
                ],
                'button_style' => [
                    'type' => 'choice',
                    'label' => 'Button-Stil',
                    'choices' => [
                        'uk-button-primary' => 'Primary (Brand-Grün)',
                        'uk-button-secondary' => 'Secondary (Grau)',
                        'uk-button-default' => 'Default (Standard)',
                        'uk-button-glass' => 'Glasmorphism',
                        'uk-button-outline-primary' => 'Outline Primary',
                        'uk-button-outline-secondary' => 'Outline Secondary'
                    ],
                    'default' => 'uk-button-primary'
                ],
                'button_size' => [
                    'type' => 'choice',
                    'label' => 'Button-Größe',
                    'choices' => [
                        'uk-button-small' => 'Klein',
                        '' => 'Standard',
                        'uk-button-large' => 'Groß'
                    ],
                    'default' => ''
                ]
            ]
        ]
        ],
        
        // Section-Felder aus zentraler Config
        $config::getSectionFields()
    ),
];