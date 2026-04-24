<?php

/**
 * Bild & Text Element - Konfiguration
 * Einfaches Layout: Bild und Text nebeneinander, wahlweise links oder rechts.
 */

$config = yform_content_builder_config::class;

return [
    'label' => 'Bild & Text',
    'icon' => 'fa fa-columns',
    'description' => 'Bild und Text nebeneinander – Links oder Rechts, einfach und flexibel.',

    'field_groups' => [
        'content_tab' => [
            'label' => 'Inhalt',
            'icon' => 'fa-text-height',
            'fields' => ['badge', 'heading', 'tag', 'subheading', 'text'],
        ],
        'media_tab' => [
            'label' => 'Bild',
            'icon' => 'fa-image',
            'fields' => ['image', 'image_alt', 'image_ratio'],
        ],
        'design_tab' => [
            'label' => 'Design',
            'icon' => 'fa-sliders',
            'fields' => ['media_position', 'image_width', 'vertical_align', 'image_rounded', 'image_shadow', 'image_style'],
        ],
        'link_tab' => [
            'label' => 'Link',
            'icon' => 'fa-link',
            'fields' => ['link_type', 'link_url', 'link_internal', 'link_text', 'link_style'],
        ],
        'section_tab' => [
            'label' => 'Sektion',
            'icon' => 'fa-columns',
            'fields' => $config::getSectionFieldNames(),
        ],
    ],

    'fields' => array_merge(
        [
            'badge' => [
                'type' => 'text',
                'label' => 'Badge / Label',
                'notice' => 'Kleines Etikett über der Überschrift (optional)',
            ],
            'heading' => [
                'type' => 'text',
                'label' => 'Überschrift',
            ],
            'tag' => [
                'type' => 'choice',
                'label' => 'HTML-Tag',
                'choices' => [
                    'h1' => 'H1 (Haupttitel)',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'p' => 'Absatz',
                ],
                'default' => 'h2',
            ],
            'subheading' => [
                'type' => 'text',
                'label' => 'Unterzeile',
                'notice' => 'Wird als Einleitung unter der Überschrift angezeigt',
            ],
            'text' => [
                'type' => 'tinymce',
                'profile' => 'default',
                'label' => 'Text',
            ],
            'image' => [
                'type' => 'be_media',
                'label' => 'Bild',
            ],
            'image_alt' => [
                'type' => 'text',
                'label' => 'Bild-Alternativtext',
                'notice' => 'Kurzbeschreibung des Bildes für Barrierefreiheit und SEO',
            ],
            'image_ratio' => [
                'type' => 'choice',
                'label' => 'Bildformat',
                'choices' => [
                    '' => 'Original (keine Beschneidung)',
                    '16_9' => 'Breitbild (16:9)',
                    '4_3' => 'Standard (4:3)',
                    '1_1' => 'Quadratisch (1:1)',
                    '3_4' => 'Hochformat (3:4)',
                ],
                'default' => '',
            ],
            'media_position' => [
                'type' => 'choice',
                'label' => 'Bild-Position',
                'choices' => [
                    'left' => 'Bild links, Text rechts',
                    'right' => 'Bild rechts, Text links',
                ],
                'default' => 'left',
            ],
            'image_width' => [
                'type' => 'choice',
                'label' => 'Bild-Breite',
                'choices' => [
                    '1-3' => '1/3 – schmal',
                    '2-5' => '2/5 – kompakt',
                    '1-2' => '1/2 – halb',
                    '3-5' => '3/5 – breit',
                    '2-3' => '2/3 – sehr breit',
                ],
                'default' => '1-2',
            ],
            'vertical_align' => [
                'type' => 'choice',
                'label' => 'Vertikale Ausrichtung',
                'choices' => [
                    'top' => 'Oben ausrichten',
                    'middle' => 'Mitte (zentriert)',
                    'bottom' => 'Unten ausrichten',
                ],
                'default' => 'middle',
            ],
            'image_rounded' => [
                'type' => 'checkbox',
                'label' => 'Bild abrunden (uk-border-rounded)',
            ],
            'image_shadow' => [
                'type' => 'choice',
                'label' => 'Bild-Schatten',
                'choices' => [
                    '' => 'Kein Schatten',
                    'small' => 'Klein',
                    'medium' => 'Mittel',
                    'large' => 'Groß',
                    'xlarge' => 'Extra Groß',
                ],
                'default' => '',
            ],
            'image_style' => [
                'type' => 'choice',
                'label' => 'Bild-Effekt',
                'choices' => [
                    ''         => 'Standard (kein Effekt)',
                    'stacked'  => 'Bildstapel (gestapelte Dekoration)',
                    'overlap'  => 'Overlap (Bild überlappt Textbereich)',
                ],
                'default' => '',
                'notice' => '"Bildstapel" erzeugt einen dekorativen Tiefeneffekt hinter dem Bild. "Overlap" lässt das Bild in den Textbereich hineinragen.',
            ],
            'link_type' => [
                'type' => 'choice',
                'label' => 'Button-Link',
                'choices' => [
                    '' => 'Kein Button',
                    'external' => 'Externe URL',
                    'internal' => 'Interne Seite',
                ],
                'default' => '',
            ],
            'link_url' => [
                'type' => 'text',
                'label' => 'Externe URL',
            ],
            'link_internal' => [
                'type' => 'be_link',
                'label' => 'Interne Seite',
            ],
            'link_text' => [
                'type' => 'text',
                'label' => 'Button-Text',
                'default' => 'Mehr erfahren',
            ],
            'link_style' => [
                'type' => 'choice',
                'label' => 'Button-Stil',
                'choices' => [
                    'uk-button-default' => 'Standard',
                    'uk-button-primary' => 'Primary',
                    'uk-button-secondary' => 'Secondary',
                    'uk-button-text' => 'Text-Link mit Pfeil',
                ],
                'default' => 'uk-button-default',
            ],
        ],
        $config::getSectionFields()
    ),
];
