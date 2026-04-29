<?php
/**
 * Starter Ueberschrift - bewusst minimal
 */

$config = \KLXM\YFormContentBuilder\Config::class;

return [
    'label' => 'Ueberschrift',
    'icon' => 'fa fa-header',
    'description' => 'Sehr einfache Ueberschrift ohne erweiterte Styling-Optionen.',
    'version' => '1.14.0',
    'category' => 'standards',
    'field_groups' => [
        'content_tab' => [
            'label' => 'Inhalt',
            'icon' => 'fa-header',
            'fields' => ['text', 'subline', 'tag'],
        ],
        'layout_tab' => [
            'label' => 'Layout',
            'icon' => 'fa-columns',
            'fields' => ['container_width', 'section_padding'],
        ],
    ],
    'fields' => [
        'text' => [
            'type' => 'text',
            'label' => 'Ueberschrift',
            'required' => true,
        ],
        'subline' => [
            'type' => 'text',
            'label' => 'Subline (optional)',
        ],
        'tag' => [
            'type' => 'choice',
            'label' => 'HTML Tag',
            'choices' => [
                'h1' => 'H1',
                'h2' => 'H2',
                'h3' => 'H3',
                'h4' => 'H4',
            ],
            'default' => 'h2',
        ],
        'container_width' => [
            'type' => 'choice',
            'label' => 'Container-Breite',
            'choices' => $config::getContainerOptions(),
            'default' => 'uk-container',
        ],
        'section_padding' => [
            'type' => 'choice',
            'label' => 'Section-Breite',
            'choices' => [
                '' => 'Standard',
                'uk-section-xsmall' => 'Sehr kompakt',
                'uk-section-small' => 'Kompakt',
                'uk-section' => 'Normal',
                'uk-section-large' => 'Groß',
                'uk-section-xlarge' => 'Sehr groß',
            ],
            'default' => '',
            'notice' => 'Steuert die vertikale Section-Groesse (ohne Farben/Hintergrundbild).',
        ],
    ],
];
