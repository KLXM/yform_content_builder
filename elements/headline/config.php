<?php

return [
    'label' => 'Überschrift',
    'icon' => 'fa fa-header',
    'description' => 'Überschrift mit Styling-Optionen',
    'fields' => [
        'text' => [
            'type' => 'text',
            'label' => 'Überschrift',
            'required' => true
        ],
        'tag' => [
            'type' => 'choice',
            'label' => 'HTML-Tag',
            'choices' => [
                'h1' => 'H1 (Hauptüberschrift)',
                'h2' => 'H2',
                'h3' => 'H3',
                'h4' => 'H4',
                'h5' => 'H5',
                'h6' => 'H6'
            ],
            'default' => 'h2'
        ],
        'size' => [
            'type' => 'choice',
            'label' => 'Größe',
            'choices' => [
                '' => 'Standard (entsprechend Tag)',
                'large' => 'Groß',
                'small' => 'Klein'
            ],
            'default' => ''
        ],
        'alignment' => [
            'type' => 'choice',
            'label' => 'Ausrichtung',
            'choices' => [
                'left' => 'Links',
                'center' => 'Zentriert',
                'right' => 'Rechts'
            ],
            'default' => 'left'
        ],
        'color' => [
            'type' => 'choice',
            'label' => 'Farbe',
            'choices' => [
                '' => 'Standard',
                'primary' => 'Primary',
                'success' => 'Success',
                'info' => 'Info',
                'warning' => 'Warning',
                'danger' => 'Danger',
                'muted' => 'Grau'
            ],
            'default' => ''
        ],
        'spacing_top' => [
            'type' => 'choice',
            'label' => 'Abstand oben',
            'choices' => [
                '' => 'Standard',
                'none' => 'Kein',
                'small' => 'Klein',
                'medium' => 'Mittel',
                'large' => 'Groß'
            ],
            'default' => ''
        ],
        'spacing_bottom' => [
            'type' => 'choice',
            'label' => 'Abstand unten',
            'choices' => [
                '' => 'Standard',
                'none' => 'Kein',
                'small' => 'Klein',
                'medium' => 'Mittel',
                'large' => 'Groß'
            ],
            'default' => ''
        ],
        'underline' => [
            'type' => 'checkbox',
            'label' => 'Unterstreichung anzeigen'
        ],
        'link_type' => [
            'type' => 'choice',
            'label' => 'Als Link',
            'choices' => [
                '' => 'Kein Link',
                'external' => 'Externe URL',
                'internal' => 'Interne Seite'
            ],
            'default' => ''
        ],
        'link_url' => [
            'type' => 'text',
            'label' => 'Externe URL'
        ],
        'link_internal' => [
            'type' => 'be_link',
            'label' => 'Interne Seite'
        ]
    ],
];
