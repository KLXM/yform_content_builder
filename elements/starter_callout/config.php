<?php
/**
 * Starter Callout - einfacher CTA Block
 */

$config = yform_content_builder_config::class;

return [
    'label' => 'Callout',
    'icon' => 'fa fa-bullhorn',
    'description' => 'Kompakter Hinweis- oder CTA-Block mit Button.',
    'version' => '1.13.0',
    'category' => 'standards',
    'field_groups' => [
        'content_tab' => [
            'label' => 'Inhalt',
            'icon' => 'fa-bullhorn',
            'fields' => ['eyebrow', 'headline', 'text', 'button_text', 'button_url'],
        ],
        'section_tab' => [
            'label' => 'Sektion',
            'icon' => 'fa-columns',
            'fields' => $config::getSectionFieldNames(),
        ],
    ],
    'fields' => array_merge([
        'eyebrow' => [
            'type' => 'text',
            'label' => 'Label (optional)',
        ],
        'headline' => [
            'type' => 'text',
            'label' => 'Ueberschrift',
        ],
        'text' => [
            'type' => 'textarea',
            'label' => 'Text',
        ],
        'button_text' => [
            'type' => 'text',
            'label' => 'Button Text',
            'default' => 'Mehr erfahren',
        ],
        'button_url' => [
            'type' => 'text',
            'label' => 'Button URL',
        ],
    ], $config::getSectionFields()),
];
