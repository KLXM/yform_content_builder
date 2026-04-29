<?php
/**
 * Table Element - Barrierefreier Tabelleneditor mit CSV Import
 */

$config = \KLXM\YFormContentBuilder\Config::class;

return [
    'label' => 'Tabelle',
    'icon' => 'fa fa-table',
    'description' => 'Responsive, barrierefreie Tabelle mit CSV Import',
    'version' => '1.13.0',
    'category' => 'data',
    'field_groups' => [
        'content' => [
            'label' => 'Inhalt',
            'icon' => 'fa-table',
            'fields' => ['table_data', 'table_caption']
        ],
        'styling' => [
            'label' => 'Styling',
            'icon' => 'fa-paint-brush',
            'fields' => ['table_style', 'table_size', 'table_hover', 'table_striped', 'table_divider', 'table_responsive']
        ],
        'design' => [
            'label' => 'Design',
            'icon' => 'fa-paint-brush',
            'fields' => array_merge(['table_align'], $config::getSectionFieldNames())
        ]
    ],
    'fields' => array_merge(
        [
            'table_data' => [
                'type' => 'textarea',
                'label' => 'Tabelleninhalt (JSON)',
                'notice' => 'JSON Format oder CSV importieren. Struktur: {"head": [["Spalte 1", "Spalte 2"]], "body": [["Zelle 1", "Zelle 2"]]}',
                'attributes' => ['rows' => 12]
            ],
            'table_caption' => [
                'type' => 'text',
                'label' => 'Tabellenüberschrift',
                'notice' => 'Optional: Beschreibung der Tabelle für Accessibility'
            ],
            'table_style' => [
                'type' => 'choice',
                'label' => 'Stil',
                'choices' => [
                    'default' => 'Standard',
                    'uk-table-divider' => 'Mit Trennlinien',
                    'uk-table-striped' => 'Zebra-Streifen',
                    'uk-table-striped uk-table-divider' => 'Zebra + Linien'
                ],
                'default' => 'default'
            ],
            'table_size' => [
                'type' => 'choice',
                'label' => 'Größe',
                'choices' => [
                    'default' => 'Standard',
                    'uk-table-small' => 'Klein',
                    'uk-table-large' => 'Groß'
                ],
                'default' => 'default'
            ],
            'table_hover' => [
                'type' => 'checkbox',
                'label' => 'Hover-Effekt (Zeilen hervorheben)'
            ],
            'table_striped' => [
                'type' => 'checkbox',
                'label' => 'Zebra-Streifen'
            ],
            'table_divider' => [
                'type' => 'checkbox',
                'label' => 'Trennlinien zwischen Zeilen'
            ],
            'table_responsive' => [
                'type' => 'choice',
                'label' => 'Responsiv',
                'choices' => [
                    '' => 'Scroll horizontal',
                    'uk-table-responsive' => 'Spalten stacking auf Mobile'
                ],
                'default' => ''
            ],
            'table_align' => [
                'type' => 'choice',
                'label' => 'Vertikale Ausrichtung',
                'choices' => [
                    '' => 'Standard',
                    'uk-table-middle' => 'Mittig'
                ],
                'default' => ''
            ]
        ],
        $config::getSectionFields()
    ),
];
