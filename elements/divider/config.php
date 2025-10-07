<?php

return [
    'label' => 'Trennlinie',
    'icon' => 'fa fa-minus',
    'description' => 'Visuelle Trennelement mit verschiedenen Styles',
    'fields' => [
        'style' => [
            'type' => 'choice',
            'label' => 'Style',
            'choices' => [
                'simple' => 'Einfache Linie',
                'double' => 'Doppelte Linie',
                'dotted' => 'Gepunktet',
                'dashed' => 'Gestrichelt',
                'thick' => 'Dicke Linie',
                'gradient' => 'Farbverlauf',
                'icon' => 'Linie mit Icon',
                'text' => 'Linie mit Text',
                'scroll' => 'Scroll-Animation (Chevron)'
            ],
            'default' => 'simple'
        ],
        'icon' => [
            'type' => 'text',
            'label' => 'Icon Klasse',
            'notice' => 'Nur bei Style "icon", z.B. fa fa-star',
            'default' => 'fa fa-star'
        ],
        'text' => [
            'type' => 'text',
            'label' => 'Text',
            'notice' => 'Nur bei Style "text"'
        ],
        'color' => [
            'type' => 'choice',
            'label' => 'Farbe',
            'choices' => [
                'default' => 'Standard (Grau)',
                'primary' => 'Primary',
                'success' => 'Success',
                'info' => 'Info',
                'warning' => 'Warning',
                'danger' => 'Danger'
            ],
            'default' => 'default'
        ],
        'width' => [
            'type' => 'choice',
            'label' => 'Breite',
            'choices' => [
                'full' => '100%',
                'wide' => '80%',
                'medium' => '60%',
                'narrow' => '40%'
            ],
            'default' => 'full'
        ],
        'spacing_top' => [
            'type' => 'choice',
            'label' => 'Abstand oben',
            'choices' => [
                'small' => 'Klein (20px)',
                'medium' => 'Mittel (40px)',
                'large' => 'Groß (60px)'
            ],
            'default' => 'medium'
        ],
        'spacing_bottom' => [
            'type' => 'choice',
            'label' => 'Abstand unten',
            'choices' => [
                'small' => 'Klein (20px)',
                'medium' => 'Mittel (40px)',
                'large' => 'Groß (60px)'
            ],
            'default' => 'medium'
        ]
    ],
];
