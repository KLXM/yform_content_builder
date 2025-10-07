<?php

return [
    'label' => 'Cards Grid',
    'icon' => 'fa fa-th',
    'description' => 'Grid mit Cards (UIkit-Style)',
    'settings_modal' => [
        'label' => 'Grid & Design Einstellungen',
        'icon' => 'fa-cog',
        'fields' => ['columns', 'columns_tablet', 'columns_mobile', 'gap', 'match_height', 'card_style', 'card_size']
    ],
    'fields' => [
        'columns' => [
            'type' => 'choice',
            'label' => 'Spalten (Desktop)',
            'choices' => [
                '2' => '2 Spalten',
                '3' => '3 Spalten',
                '4' => '4 Spalten',
                '5' => '5 Spalten',
                '6' => '6 Spalten'
            ],
            'default' => '3'
        ],
        'columns_tablet' => [
            'type' => 'choice',
            'label' => 'Spalten (Tablet)',
            'choices' => [
                '1' => '1 Spalte',
                '2' => '2 Spalten',
                '3' => '3 Spalten',
                '4' => '4 Spalten'
            ],
            'default' => '2'
        ],
        'columns_mobile' => [
            'type' => 'choice',
            'label' => 'Spalten (Mobile)',
            'choices' => [
                '1' => '1 Spalte',
                '2' => '2 Spalten'
            ],
            'default' => '1'
        ],
        'gap' => [
            'type' => 'choice',
            'label' => 'Abstand zwischen Cards',
            'choices' => [
                'small' => 'Klein (15px)',
                'medium' => 'Mittel (30px)',
                'large' => 'Groß (40px)'
            ],
            'default' => 'medium'
        ],
        'match_height' => [
            'type' => 'checkbox',
            'label' => 'Gleiche Höhe für alle Cards'
        ],
        'card_style' => [
            'type' => 'choice',
            'label' => 'Card Style',
            'choices' => [
                'default' => 'Default (mit Rahmen)',
                'primary' => 'Primary',
                'secondary' => 'Secondary',
                'hover' => 'Hover Effect'
            ],
            'default' => 'default'
        ],
        'card_size' => [
            'type' => 'choice',
            'label' => 'Card Padding',
            'choices' => [
                'small' => 'Klein',
                'default' => 'Standard',
                'large' => 'Groß'
            ],
            'default' => 'default'
        ],
        'items' => [
            'type' => 'repeater',
            'label' => 'Cards',
            'item_modal' => [
                'label' => 'Erweiterte Optionen',
                'icon' => 'fa-ellipsis-h',
                'fields' => ['subtitle', 'badge', 'badge_color', 'link_type', 'link_url', 'link_internal', 'link_text']
            ],
            'fields' => [
                'image' => [
                    'type' => 'be_media',
                    'label' => 'Bild',
                    'notice' => 'Optional'
                ],
                'image_position' => [
                    'type' => 'choice',
                    'label' => 'Bild Position',
                    'choices' => [
                        'top' => 'Oben',
                        'bottom' => 'Unten'
                    ],
                    'default' => 'top'
                ],
                'title' => [
                    'type' => 'text',
                    'label' => 'Titel',
                    'required' => true
                ],
                'text' => [
                    'type' => 'cke5',
                    'label' => 'Text'
                ],
                'subtitle' => [
                    'type' => 'text',
                    'label' => 'Untertitel',
                    'notice' => 'Optional'
                ],
                'badge' => [
                    'type' => 'text',
                    'label' => 'Badge',
                    'notice' => 'Optional, z.B. "NEU"'
                ],
                'badge_color' => [
                    'type' => 'choice',
                    'label' => 'Badge Farbe',
                    'choices' => [
                        'primary' => 'Primary',
                        'success' => 'Success',
                        'info' => 'Info',
                        'warning' => 'Warning',
                        'danger' => 'Danger'
                    ],
                    'default' => 'primary'
                ],
                'link_type' => [
                    'type' => 'choice',
                    'label' => 'Link',
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
                ],
                'link_text' => [
                    'type' => 'text',
                    'label' => 'Link Text',
                    'default' => 'Mehr erfahren'
                ]
            ]
        ]
    ],
];
