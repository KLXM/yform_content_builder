<?php

/**
 * Moving Tiles Element
 * Alternating parallax tiles with text and media (image/video)
 */

return [
    'label' => 'Moving Tiles',
    'icon' => 'fa fa-th-large',
    'description' => 'Parallax Tiles mit alternierenden Layouts, Fade-Effekt und Video-Autoplay',
    
    'settings_modal' => [
        'label' => 'Einstellungen',
        'icon' => 'fa-cog',
        'fields' => ['tile_style', 'section_bg', 'section_padding', 'first_position', 'parallax_enabled', 'parallax_offset', 'fade_enabled']
    ],
    
    'fields' => [
        // TILE STYLE (Textbereich Farbe)
        'tile_style' => [
            'type' => 'choice',
            'label' => 'Textbereich Farbe',
            'choices' => [
                'uk-tile-default' => 'Default (Weiß)',
                'uk-tile-muted' => 'Muted (Grau)',
                'uk-tile-primary' => 'Primary',
                'uk-tile-secondary' => 'Secondary'
            ],
            'default' => 'uk-tile-default'
        ],
        
        // SECTION SETTINGS
        'section_bg' => [
            'type' => 'choice',
            'label' => 'Hintergrund',
            'choices' => [
                '' => 'Keine',
                'uk-background-default' => 'Default (Weiß)',
                'uk-background-muted' => 'Muted (Grau)',
                'uk-background-primary' => 'Primary',
                'uk-background-secondary' => 'Secondary'
            ],
            'default' => ''
        ],
        'section_padding' => [
            'type' => 'choice',
            'label' => 'Padding',
            'choices' => [
                'uk-section-small' => 'Klein',
                '' => 'Standard',
                'uk-section-large' => 'Groß',
                'uk-section-xlarge' => 'Extra Groß'
            ],
            'default' => ''
        ],
        
        // POSITION SETTING
        'first_position' => [
            'type' => 'choice',
            'label' => 'Erstes Element: Medium Position',
            'choices' => [
                'left' => 'Links',
                'right' => 'Rechts'
            ],
            'default' => 'left'
        ],
        
        // PARALLAX
        'parallax_enabled' => [
            'type' => 'checkbox',
            'label' => 'Parallax aktivieren',
            'default' => 1
        ],
        'parallax_offset' => [
            'type' => 'text',
            'label' => 'Parallax Offset (px)',
            'default' => '30',
            'notice' => 'z.B. 30 für ±30px Versatz'
        ],
        
        // FADE ANIMATION
        'fade_enabled' => [
            'type' => 'checkbox',
            'label' => 'Fade-In Animation aktivieren',
            'default' => 0,
            'notice' => 'Tiles erscheinen mit Fade-Effekt beim Scrollen'
        ],
        
        // REPEATER ITEMS
        'items' => [
            'type' => 'repeater',
            'label' => 'Features',
            'add_label' => 'Feature hinzufügen',
            
            // Medien-Modal für erweiterte Bildoptionen
            'media_modal' => [
                'label' => 'Medieneinstellungen',
                'icon' => 'fa-sliders',
                'trigger_after' => 'image',
                'fields' => ['item_tile_style', 'image_alt', 'image_decorative', 'image_lightbox']
            ],
            
            'fields' => [
                // Media - wie bei cards mit 'image' als Feldname
                'image' => [
                    'type' => 'be_media',
                    'label' => 'Bild oder Video',
                    'preview' => true,
                    'notice' => 'Bilder und Videos werden unterstützt'
                ],
                
                // Text-Inhalt
                'text' => [
                    'type' => 'cke5',
                    'label' => 'Text 1'
                ],
                
                // Medien-Optionen (im Modal)
                'image_alt' => [
                    'type' => 'text',
                    'label' => 'Alt-Text',
                    'notice' => 'Überschreibt den Alt-Text aus dem Medienpool'
                ],
                'image_decorative' => [
                    'type' => 'checkbox',
                    'label' => 'Dekoratives Bild (kein Alt-Text nötig)'
                ],
                'image_lightbox' => [
                    'type' => 'checkbox',
                    'label' => 'In Lightbox öffnen'
                ],
                
                // Tile-Farbe pro Item (überschreibt globale Einstellung)
                'item_tile_style' => [
                    'type' => 'choice',
                    'label' => 'Textbereich Farbe (optional)',
                    'choices' => [
                        '' => 'Global (aus Einstellungen)',
                        'uk-tile-default' => 'Default (Weiß)',
                        'uk-tile-muted' => 'Muted (Grau)',
                        'uk-tile-primary' => 'Primary',
                        'uk-tile-secondary' => 'Secondary'
                    ],
                    'default' => ''
                ]
            ]
        ]
    ]
];
