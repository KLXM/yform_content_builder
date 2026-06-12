<?php
/**
 * Starter Text - sehr einfaches Textelement
 */

use KLXM\YFormContentBuilder\Config;

$elementConfig = Config::class;

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
            'fields' => ['text'],
        ],
        'section_settings_tab' => [
            'label' => 'Sektion',
            'icon' => 'fa-columns',
            'fields' => array_merge(
                ['enable_section', 'enable_container'],
                $elementConfig::getSectionFieldNames()
            ),
        ],
    ],
    'fields' => array_merge([
        'enable_section' => [
            'type' => 'checkbox',
            'label' => 'Sektion aktivieren',
            'default' => false,
            'notice' => 'Nur aktivieren, wenn dieses Element eine eigene Section-Umhüllung benötigt.',
        ],
        'enable_container' => [
            'type' => 'checkbox',
            'label' => 'Container aktivieren',
            'default' => false,
            'notice' => 'Nur aktivieren, wenn ein eigener Container gesetzt werden soll.',
        ],
        'text' => [
            'type' => 'tinymce',
            'profile' => 'default',
            'label' => 'Text',
        ],
    ], $elementConfig::getSectionFields()),
];
