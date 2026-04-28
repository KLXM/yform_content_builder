<?php

/**
 * Feature-Raster Element - Konfiguration
 * Repeater-Element: Icon/Bild + Titel + Text – ohne Modal, direkt bedienbar.
 */

$config = yform_content_builder_config::class;

return [
    'label' => 'Feature-Raster',
    'icon' => 'fa fa-th-large',
    'description' => 'Icons, Bilder oder Symbole mit Titel und Text – ideal für Vorteile, Leistungen und Features.',
    'version' => '1.13.0',
    'category' => 'content',

    'field_groups' => [
        'content_tab' => [
            'label' => 'Features',
            'icon' => 'fa-list',
            'fields' => ['items'],
        ],
        'design_tab' => [
            'label' => 'Design',
            'icon' => 'fa-sliders',
            'fields' => ['columns', 'columns_tablet', 'columns_mobile', 'gap', 'icon_style', 'card_style', 'text_align'],
        ],
        'section_tab' => [
            'label' => 'Sektion',
            'icon' => 'fa-columns',
            'fields' => $config::getSectionFieldNames(),
        ],
    ],

    'fields' => array_merge(
        [
            'items' => [
                'type' => 'repeater',
                'label' => 'Features',
                'add_label' => 'Feature hinzufügen',
                'view' => 'list',
                'fields' => [
                    'icon' => [
                        'type' => 'be_media',
                        'label' => 'Icon / Bild',
                        'notice' => 'SVG, PNG oder JPG – empfohlen: quadratisches Format oder SVG-Icon',
                        'col' => 3,
                    ],
                    'icon_uikit' => [
                        'type' => 'text',
                        'label' => 'UIkit Icon-Name',
                        'notice' => 'z. B. "star", "check", "mail" – wird verwendet, wenn kein Bild gewählt ist',
                        'col' => 3,
                    ],
                    'heading' => [
                        'type' => 'text',
                        'label' => 'Titel',
                        'required' => true,
                        'col' => 6,
                    ],
                    'text' => [
                        'type' => 'textarea',
                        'label' => 'Beschreibung',
                    ],
                    'link_url' => [
                        'type' => 'text',
                        'label' => 'Link (URL, optional)',
                        'col' => 8,
                    ],
                    'link_text' => [
                        'type' => 'text',
                        'label' => 'Link-Text',
                        'default' => 'Mehr',
                        'col' => 4,
                    ],
                ],
            ],

            'columns' => [
                'type' => 'choice',
                'label' => 'Spalten (Desktop)',
                'choices' => [
                    '2' => '2 Spalten',
                    '3' => '3 Spalten',
                    '4' => '4 Spalten',
                ],
                'default' => '3',
            ],
            'columns_tablet' => [
                'type' => 'choice',
                'label' => 'Spalten (Tablet)',
                'choices' => [
                    '1' => '1 Spalte',
                    '2' => '2 Spalten',
                    '3' => '3 Spalten',
                ],
                'default' => '2',
            ],
            'columns_mobile' => [
                'type' => 'choice',
                'label' => 'Spalten (Mobil)',
                'choices' => [
                    '1' => '1 Spalte',
                    '2' => '2 Spalten',
                ],
                'default' => '1',
            ],
            'gap' => [
                'type' => 'choice',
                'label' => 'Abstände',
                'choices' => [
                    'small' => 'Klein',
                    'medium' => 'Mittel',
                    'large' => 'Groß',
                    'collapse' => 'Kein Abstand',
                ],
                'default' => 'medium',
            ],
            'icon_style' => [
                'type' => 'choice',
                'label' => 'Icon-Darstellung',
                'choices' => [
                    'plain' => 'Einfach (ohne Hintergrund)',
                    'circle' => 'Kreis',
                    'box' => 'Quadrat',
                ],
                'default' => 'plain',
            ],
            'card_style' => [
                'type' => 'choice',
                'label' => 'Box-Stil',
                'choices' => [
                    '' => 'Kein Rahmen',
                    'default' => 'Rahmen (uk-card-default)',
                    'muted' => 'Grau (uk-card-muted)',
                    'primary' => 'Primary (uk-card-primary)',
                ],
                'default' => '',
            ],
            'text_align' => [
                'type' => 'choice',
                'label' => 'Text-Ausrichtung',
                'choices' => [
                    'left' => 'Links',
                    'center' => 'Zentriert',
                ],
                'default' => 'left',
            ],
        ],
        $config::getSectionFields()
    ),
];
