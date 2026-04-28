<?php

/**
 * Testimonial Element - Konfiguration
 * Repeater ohne Modal: Zitat, Autor, Bild – direkt und einfach einzugeben.
 */

$config = yform_content_builder_config::class;

return [
    'label' => 'Testimonials',
    'icon' => 'fa fa-quote-left',
    'description' => 'Zitate und Kundenstimmen – mit oder ohne Autorenfoto, einzeln oder als Raster.',
    'version' => '1.13.0',
    'category' => 'content',

    'field_groups' => [
        'content_tab' => [
            'label' => 'Testimonials',
            'icon' => 'fa-comment',
            'fields' => ['items'],
        ],
        'design_tab' => [
            'label' => 'Design',
            'icon' => 'fa-sliders',
            'fields' => ['style', 'columns', 'columns_tablet'],
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
                'label' => 'Testimonials',
                'add_label' => 'Testimonial hinzufügen',
                'view' => 'list',
                'fields' => [
                    'author_image' => [
                        'type' => 'be_media',
                        'label' => 'Foto (optional)',
                        'col' => 3,
                    ],
                    'author_name' => [
                        'type' => 'text',
                        'label' => 'Name',
                        'required' => true,
                        'col' => 5,
                    ],
                    'author_role' => [
                        'type' => 'text',
                        'label' => 'Funktion / Firma',
                        'col' => 4,
                    ],
                    'quote' => [
                        'type' => 'textarea',
                        'label' => 'Zitat',
                        'required' => true,
                        'notice' => 'Ohne Anführungszeichen – diese werden automatisch hinzugefügt',
                    ],
                    'rating' => [
                        'type' => 'choice',
                        'label' => 'Bewertung (optional)',
                        'choices' => [
                            '' => 'Keine Bewertung',
                            '5' => '★★★★★',
                            '4' => '★★★★☆',
                            '3' => '★★★☆☆',
                        ],
                        'default' => '',
                    ],
                ],
            ],

            'style' => [
                'type' => 'choice',
                'label' => 'Stil',
                'choices' => [
                    'card' => 'Karte (mit Rahmen)',
                    'minimal' => 'Minimal (nur Text)',
                    'accent' => 'Akzent (farbige Linie)',
                ],
                'default' => 'card',
            ],
            'columns' => [
                'type' => 'choice',
                'label' => 'Spalten (Desktop)',
                'choices' => [
                    '1' => '1 Spalte (zentriert)',
                    '2' => '2 Spalten',
                    '3' => '3 Spalten',
                ],
                'default' => '2',
            ],
            'columns_tablet' => [
                'type' => 'choice',
                'label' => 'Spalten (Tablet)',
                'choices' => [
                    '1' => '1 Spalte',
                    '2' => '2 Spalten',
                ],
                'default' => '1',
            ],
        ],
        $config::getSectionFields()
    ),
];
