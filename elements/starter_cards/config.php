<?php
/**
 * Starter Cards - einfache Karten
 */

$config = \KLXM\YFormContentBuilder\Config::class;

return [
    'label' => 'Cards',
    'icon' => 'fa fa-th-large',
    'description' => 'Vereinfachte Karten mit Bild, Titel, Text und Link.',
    'version' => '1.13.0',
    'category' => 'standards',
    'field_groups' => [
        'content_tab' => [
            'label' => 'Inhalt',
            'icon' => 'fa-th-large',
            'fields' => ['headline', 'items'],
        ],
        'layout_tab' => [
            'label' => 'Layout',
            'icon' => 'fa-columns',
            'fields' => $config::getGridFieldNames(),
        ],
        'section_tab' => [
            'label' => 'Sektion',
            'icon' => 'fa-columns',
            'fields' => $config::getOptionalSectionFieldNames(),
        ],
    ],
    'fields' => array_merge([
        'headline' => [
            'type' => 'text',
            'label' => 'Ueberschrift',
        ],
        'items' => [
            'type' => 'repeater',
            'label' => 'Karten',
            'min' => 1,
            'add_label' => 'Karte hinzufuegen',
            'fields' => [
                'image' => [
                    'type' => 'be_media',
                    'label' => 'Bild',
                ],
                'title' => [
                    'type' => 'text',
                    'label' => 'Titel',
                ],
                'text' => [
                    'type' => 'tinymce',
                    'label' => 'Text',
                    'profile' => 'default',
                ],
                'link_url' => [
                    'type' => 'text',
                    'label' => 'Link URL',
                ],
                'link_text' => [
                    'type' => 'text',
                    'label' => 'Link Text',
                    'default' => 'Mehr erfahren',
                ],
            ],
        ],
    ], $config::getStandardFields()),
];
