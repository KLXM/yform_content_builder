<?php
/**
 * Starter Galerie - Grid oder Masonry
 */

$config = \KLXM\YFormContentBuilder\Config::class;

return [
    'label' => 'Galerie',
    'icon' => 'fa fa-th',
    'description' => 'Einfache Bildergalerie mit Grid oder Masonry.',
    'version' => '1.13.0',
    'category' => 'standards',
    'field_groups' => [
        'content_tab' => [
            'label' => 'Inhalt',
            'icon' => 'fa-image',
            'fields' => ['headline', 'layout', 'items'],
        ],
        'layout_tab' => [
            'label' => 'Layout',
            'icon' => 'fa-th-large',
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
        'layout' => [
            'type' => 'choice',
            'label' => 'Darstellung',
            'choices' => [
                'grid' => 'Grid',
                'masonry' => 'Masonry',
            ],
            'default' => 'grid',
        ],
        'items' => [
            'type' => 'repeater',
            'label' => 'Bilder',
            'min' => 1,
            'add_label' => 'Bild hinzufuegen',
            'fields' => [
                'image' => [
                    'type' => 'be_media',
                    'label' => 'Bild',
                ],
                'caption' => [
                    'type' => 'text',
                    'label' => 'Bildunterschrift',
                ],
            ],
        ],
    ], $config::getStandardFields()),
];
