<?php

return [
    'label' => 'Accordion / Tabs',
    'icon' => 'fa fa-list',
    'description' => 'Aufklappbare Inhaltsbereiche oder Tabs',
    'repeater' => true,
    'fields' => [
        'display_type' => [
            'type' => 'choice',
            'label' => 'Darstellung',
            'choices' => [
                'accordion' => 'Accordion (aufklappbar)',
                'tabs' => 'Tabs (nebeneinander)'
            ],
            'default' => 'accordion'
        ],
        'style' => [
            'type' => 'choice',
            'label' => 'Stil',
            'choices' => [
                'default' => 'Standard',
                'primary' => 'Primary',
                'success' => 'Success',
                'info' => 'Info'
            ],
            'default' => 'default'
        ],
        'items' => [
            'type' => 'repeater',
            'label' => 'Elemente',
            'fields' => [
                'title' => ['type' => 'text', 'label' => 'Titel'],
                'icon' => ['type' => 'text', 'label' => 'Icon (optional)', 'notice' => 'z.B. fa-home'],
                'content' => ['type' => 'cke5', 'label' => 'Inhalt'],
            ],
        ],
    ],
];
