<?php
/**
 * Columns Layout Element Configuration
 */
return [
    'label' => 'Spalten-Layout',
    'icon' => 'fa-columns',
    'description' => 'Erzeugt ein mehrspaltiges Layout für geschachtelte Elemente',
    'version' => '1.0.0',
    'category' => 'layout',
    
    'fields' => [
        'col_layout' => [
            'type' => 'choice',
            'label' => 'Layout-Verteilung',
            'choices' => [
                '50_50' => '2 Spalten (50% / 50%)',
                '33_33_33' => '3 Spalten (33% / 33% / 33%)',
                '25_75' => '2 Spalten (25% / 75%)',
                '75_25' => '2 Spalten (75% / 25%)',
            ],
            'default' => '50_50'
        ]
    ]
];
