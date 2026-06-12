<?php

/**
 * Hero Banner Element - Konfiguration
 * Großes Vollbild-Bild mit Überschrift, Text und einem oder zwei Buttons.
 */

$config = \KLXM\YFormContentBuilder\Config::class;

return [
    'label' => 'Hero Banner',
    'icon' => 'fa fa-picture-o',
    'description' => 'Großes Bild oder Video mit Überschrift, Text und Buttons – ideal als Seitenanfang.',
    'version' => '1.13.0',
    'category' => 'media',

    'field_groups' => [
        'content_tab' => [
            'label' => 'Inhalt',
            'icon' => 'fa-text-height',
            'fields' => ['badge', 'heading', 'tag', 'subheading', 'text',
                'btn1_text', 'btn1_link_type', 'btn1_url', 'btn1_internal', 'btn1_style',
                'btn2_text', 'btn2_url'],
        ],
        'media_tab' => [
            'label' => 'Bild / Video',
            'icon' => 'fa-image',
            'fields' => ['image', 'image_alt', 'video'],
        ],
        'design_tab' => [
            'label' => 'Design',
            'icon' => 'fa-sliders',
            'fields' => ['height', 'content_align', 'content_valign', 'overlay', 'text_color',
                'parallax_bg', 'parallax_bg_velocity', 'parallax_content'],
        ],
        'section_tab' => [
            'label' => 'Sektion',
            'icon' => 'fa-columns',
            'fields' => $config::getOptionalSectionFieldNames(),
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
                'required' => true,
            ],
            'tag' => [
                'type' => 'choice',
                'label' => 'HTML-Tag',
                'choices' => [
                    'h1' => 'H1 (Haupttitel)',
                    'h2' => 'H2',
                    'h3' => 'H3',
                ],
                'default' => 'h1',
            ],
            'subheading' => [
                'type' => 'text',
                'label' => 'Unterzeile',
            ],
            'text' => [
                'type' => 'textarea',
                'label' => 'Text / Einleitung',
                'notice' => 'Kurzer Einleitungstext – bleibt am besten knapp',
            ],
            'btn1_text' => [
                'type' => 'text',
                'label' => 'Haupt-Button Beschriftung',
                'col' => 4,
            ],
            'btn1_link_type' => [
                'type' => 'choice',
                'label' => 'Haupt-Button Link',
                'choices' => [
                    '' => 'Kein Link',
                    'external' => 'Externe URL',
                    'internal' => 'Interne Seite',
                ],
                'default' => '',
                'col' => 4,
            ],
            'btn1_style' => [
                'type' => 'choice',
                'label' => 'Haupt-Button Stil',
                'choices' => [
                    'uk-button-default' => 'Standard',
                    'uk-button-primary' => 'Primary',
                    'uk-button-secondary' => 'Secondary',
                    'uk-button-danger' => 'Danger',
                    'uk-button-text' => 'Text-Link',
                ],
                'default' => 'uk-button-primary',
                'col' => 4,
            ],
            'btn1_url' => [
                'type' => 'text',
                'label' => 'Haupt-Button: Externe URL',
                'col' => 6,
            ],
            'btn1_internal' => [
                'type' => 'be_link',
                'label' => 'Haupt-Button: Interne Seite',
                'col' => 6,
            ],
            'btn2_text' => [
                'type' => 'text',
                'label' => '2. Button Beschriftung (optional)',
                'col' => 6,
            ],
            'btn2_url' => [
                'type' => 'text',
                'label' => '2. Button URL',
                'col' => 6,
            ],
            'image' => [
                'type' => 'be_media',
                'label' => 'Hintergrundbild',
                'notice' => 'Wird als Hintergrundbild verwendet – möglichst groß (min. 1600px Breite)',
            ],
            'image_alt' => [
                'type' => 'text',
                'label' => 'Bild-Alternativtext',
                'notice' => 'Kurzbeschreibung für Barrierefreiheit',
            ],
            'video' => [
                'type' => 'be_media',
                'label' => 'Hintergrund-Video (MP4)',
                'notice' => 'Falls angegeben, wird das Video statt des Bildes angezeigt (stumm, Schleife)',
            ],
            'height' => [
                'type' => 'choice',
                'label' => 'Höhe',
                'choices' => [
                    'small' => 'Klein (~300px)',
                    'medium' => 'Mittel (~450px)',
                    'large' => 'Groß (~600px)',
                    'viewport' => 'Volle Bildschirmhöhe',
                    'viewport-2-3' => '2/3 Bildschirmhöhe',
                ],
                'default' => 'large',
            ],
            'content_align' => [
                'type' => 'choice',
                'label' => 'Inhalt horizontal',
                'choices' => [
                    'left' => 'Links',
                    'center' => 'Zentriert',
                    'right' => 'Rechts',
                ],
                'default' => 'left',
            ],
            'content_valign' => [
                'type' => 'choice',
                'label' => 'Inhalt vertikal',
                'choices' => [
                    'top' => 'Oben',
                    'middle' => 'Mitte',
                    'bottom' => 'Unten',
                ],
                'default' => 'middle',
            ],
            'overlay' => [
                'type' => 'choice',
                'label' => 'Overlay (Abdunklung)',
                'choices' => [
                    '' => 'Kein Overlay',
                    'dark' => 'Dunkel (40%)',
                    'dark-heavy' => 'Dunkel (65%)',
                    'light' => 'Hell (40%)',
                ],
                'default' => 'dark',
            ],
            'text_color' => [
                'type' => 'choice',
                'label' => 'Textfarbe',
                'choices' => [
                    'light' => 'Hell (uk-light)',
                    'dark' => 'Dunkel',
                    '' => 'Standard',
                ],
                'default' => 'light',
            ],

            // ===== PARALLAX =====
            'parallax_bg' => [
                'type' => 'checkbox',
                'label' => 'Parallax-Hintergrund',
                'notice' => 'Hintergrundbild scrollt langsamer als die Seite (uk-parallax bgy) – funktioniert nicht mit Video',
            ],
            'parallax_bg_velocity' => [
                'type' => 'choice',
                'label' => 'Parallax-Stärke (Hintergrund)',
                'choices' => [
                    '150' => 'Dezent',
                    '300' => 'Mittel',
                    '500' => 'Intensiv',
                ],
                'default' => '300',
                'notice' => 'Blendet ein, wenn Parallax-Hintergrund aktiv ist',
            ],
            'parallax_content' => [
                'type' => 'checkbox',
                'label' => 'Parallax-Text',
                'notice' => 'Text und Buttons schweben beim Scrollen leicht nach oben (uk-parallax y)',
            ],
        ],
        $config::getOptionalSectionFields()
    ),
];
