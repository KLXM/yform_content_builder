<?php

/**
 * Timeline Element - Konfiguration
 * Vertikale Zeitlinie / Ablauf-Darstellung mit Repeater.
 */

$config = yform_content_builder_config::class;

return [
    'label' => 'Timeline',
    'icon' => 'fa fa-stream',
    'description' => 'Vertikale Zeitlinie für Meilensteine, Prozesse oder Ereignisse.',
    'version' => '1.13.0',
    'category' => 'content',
    'field_groups' => [
        'content_tab' => [
            'label' => 'Inhalt',
            'icon' => 'fa-stream',
            'fields' => ['heading', 'tag', 'intro', 'items'],
        ],
        'design_tab' => [
            'label' => 'Design',
            'icon' => 'fa-sliders',
            'fields' => ['style', 'icon_default', 'icon_color', 'line_color'],
        ],
        'section_tab' => [
            'label' => 'Sektion',
            'icon' => 'fa-columns',
            'fields' => $config::getSectionFieldNames(),
        ],
    ],

    'fields' => array_merge(
        [
            'heading' => [
                'type' => 'text',
                'label' => 'Überschrift (optional)',
            ],
            'tag' => [
                'type' => 'choice',
                'label' => 'HTML-Tag',
                'choices' => [
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'p'  => 'Absatz',
                ],
                'default' => 'h2',
            ],
            'intro' => [
                'type' => 'textarea',
                'label' => 'Einleitung (optional)',
            ],
            'items' => [
                'type' => 'repeater',
                'label' => 'Timeline-Einträge',
                'add_label' => 'Eintrag hinzufügen',
                'view' => 'list',
                'fields' => [
                    'date' => [
                        'type' => 'text',
                        'label' => 'Datum / Zeitraum',
                        'notice' => 'z.B. "2024", "März 2024", "Seit 2022"',
                        'col' => 4,
                    ],
                    'title' => [
                        'type' => 'text',
                        'label' => 'Titel / Meilenstein',
                        'required' => true,
                        'col' => 8,
                    ],
                    'text' => [
                        'type' => 'textarea',
                        'label' => 'Beschreibung (optional)',
                        'col' => 12,
                    ],
                    'icon' => [
                        'type' => 'text',
                        'label' => 'UIkit-Icon (optional)',
                        'notice' => 'Name des UIkit-Icons, z.B. "star", "check", "heart", "settings"',
                        'col' => 6,
                    ],
                    'badge' => [
                        'type' => 'text',
                        'label' => 'Badge-Text (optional)',
                        'notice' => 'Kleines Label am Punkt, z.B. "Neu", "Live"',
                        'col' => 6,
                    ],
                    'highlight' => [
                        'type' => 'checkbox',
                        'label' => 'Hervorheben',
                        'notice' => 'Eintrag als besonders wichtig markieren',
                        'col' => 12,
                    ],
                ],
            ],
            'style' => [
                'type' => 'choice',
                'label' => 'Stil',
                'choices' => [
                    'default'    => 'Standard (Punkt + Linie)',
                    'card'       => 'Karten (uk-card-default)',
                    'alternating' => 'Alternierend (links/rechts)',
                ],
                'default' => 'default',
            ],
            'icon_default' => [
                'type' => 'choice',
                'label' => 'Standard-Icon',
                'choices' => [
                    'circle'     => 'Gefüllter Kreis',
                    'check'      => 'Häkchen',
                    'star'       => 'Stern',
                    'bolt'       => 'Blitz',
                    'none'       => 'Keins (nur Punkt)',
                ],
                'default' => 'circle',
            ],
            'icon_color' => [
                'type' => 'choice',
                'label' => 'Icon-/Punkt-Farbe',
                'choices' => [
                    'primary'   => 'Primary',
                    'secondary' => 'Secondary',
                    'success'   => 'Success (grün)',
                    'warning'   => 'Warning (orange)',
                    'danger'    => 'Danger (rot)',
                    'muted'     => 'Muted',
                ],
                'default' => 'primary',
            ],
            'line_color' => [
                'type' => 'choice',
                'label' => 'Linien-Stil',
                'choices' => [
                    'solid'  => 'Durchgezogen',
                    'dashed' => 'Gestrichelt',
                    'dotted' => 'Gepunktet',
                ],
                'default' => 'solid',
            ],
        ],
        $config::getSectionFields()
    ),
];
