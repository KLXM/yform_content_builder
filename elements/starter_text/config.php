<?php
/**
 * Starter Text - sehr einfaches Textelement
 */

$config = \KLXM\YFormContentBuilder\Config::class;

return [
    'label' => 'Text',
    'icon' => 'fa fa-align-left',
    'description' => 'Einfacher Textblock mit TinyMCE (Profil: default).',
    'version' => '1.13.0',
    'category' => 'standards',
    'field_groups' => [
        'content_tab' => [
            'label' => 'Inhalt',
            'icon' => 'fa-file-text-o',
            'fields' => ['headline', 'headline_tag', 'text'],
        ],
        'section_tab' => [
            'label' => 'Sektion',
            'icon' => 'fa-columns',
            'fields' => $config::getSectionFieldNames(),
        ],
    ],
    'fields' => array_merge([
        'headline' => [
            'type' => 'text',
            'label' => 'Ueberschrift',
        ],
        'headline_tag' => [
            'type' => 'choice',
            'label' => 'Ueberschrift Tag',
            'choices' => [
                'h2' => 'H2',
                'h3' => 'H3',
                'h4' => 'H4',
                'p' => 'Absatz',
            ],
            'default' => 'h2',
        ],
        'text' => [
            'type' => 'tinymce',
            'profile' => 'default',
            'label' => 'Text',
        ],
    ], $config::getSectionFields()),
];
