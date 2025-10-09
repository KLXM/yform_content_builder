<?php

return [
    'label' => 'Galerie',
    'description' => 'Medien-Galerie mit Grid/Masonry Layout',
    'icon' => 'fa-th',
    'category' => 'media',
    
    'fields' => [
        'headline' => [
            'type' => 'text',
            'label' => 'Überschrift',
            'notice' => 'Optional: Überschrift für die Galerie'
        ],
        'layout' => [
            'type' => 'choice',
            'label' => 'Layout',
            'choices' => [
                'grid' => 'Grid (gleichmäßig)',
                'masonry' => 'Masonry (Pinterest-Style)'
            ],
            'default' => 'grid'
        ],
        'columns' => [
            'type' => 'choice',
            'label' => 'Spalten',
            'choices' => [
                '2' => '2 Spalten',
                '3' => '3 Spalten',
                '4' => '4 Spalten',
                '5' => '5 Spalten'
            ],
            'default' => '3'
        ],
        'aspect_ratio' => [
            'type' => 'choice',
            'label' => 'Seitenverhältnis',
            'choices' => [
                'auto' => 'Original',
                '16:9' => '16:9 (Widescreen)',
                '4:3' => '4:3 (Standard)',
                '1:1' => '1:1 (Quadrat)',
                '3:2' => '3:2 (Klassisch)'
            ],
            'default' => 'auto'
        ],
        'gap' => [
            'type' => 'choice',
            'label' => 'Abstand',
            'choices' => [
                'small' => 'Klein (10px)',
                'medium' => 'Mittel (20px)',
                'large' => 'Groß (30px)'
            ],
            'default' => 'medium'
        ],
        'view_mode' => [
            'type' => 'choice',
            'label' => 'Eingabe-Ansicht',
            'choices' => [
                'list' => 'Liste (untereinander)',
                'grid' => 'Kacheln (Grid)'
            ],
            'default' => 'grid'
        ],
        'items' => [
            'type' => 'repeater',
            'label' => 'Galerie-Items',
            'add_label' => 'Medium hinzufügen',
            'view' => 'grid', // Neue Option für gekachelte Ansicht
            'grid_columns' => 3, // Anzahl Spalten im Grid
            'fields' => [
                'media' => [
                    'type' => 'be_media_enhanced',
                    'label' => 'Bild/Video',
                    'allowed_types' => ['image', 'video']
                ],
                'caption' => [
                    'type' => 'text',
                    'label' => 'Bildunterschrift',
                    'notice' => 'Optional: Beschreibung für das Medium'
                ],
                'alt_text' => [
                    'type' => 'text',
                    'label' => 'Alt-Text',
                    'notice' => 'Für Barrierefreiheit und SEO'
                ]
            ],
            'item_modal' => [
                'label' => 'Erweiterte Optionen',
                'icon' => 'fa-cog',
                'fields' => ['caption', 'alt_text']
            ]
        ]
    ],
    
    'settings_modal' => [
        'label' => 'Layout-Einstellungen',
        'icon' => 'fa-cogs',
        'fields' => ['layout', 'columns', 'aspect_ratio', 'gap', 'view_mode']
    ]
];