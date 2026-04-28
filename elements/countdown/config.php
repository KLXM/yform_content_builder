<?php
/**
 * Countdown Element - Countdown Timer mit verschiedenen Modi
 */

$config = yform_content_builder_config::class;

return [
    'label' => 'Countdown',
    'icon' => 'fa fa-hourglass-end',
    'description' => 'Countdown-Timer bis zu einem Zieldatum',
    'version' => '1.13.0',
    'category' => 'content',
    'field_groups' => [
        'content' => [
            'label' => 'Inhalt',
            'icon' => 'fa-hourglass-end',
            'fields' => ['countdown_date', 'countdown_time', 'countdown_language', 'countdown_mode', 'countdown_size', 'countdown_align']
        ],
        'options' => [
            'label' => 'Optionen',
            'icon' => 'fa-cogs',
            'fields' => ['countdown_separator', 'countdown_labels', 'countdown_reload']
        ],
        'design' => [
            'label' => 'Design',
            'icon' => 'fa-paint-brush',
            'fields' => array_merge(['countdown_text_color'], $config::getSectionFieldNames())
        ]
    ],
    'fields' => array_merge(
        [
            'countdown_date' => [
                'type' => 'text',
                'label' => 'Zieldatum',
                'notice' => 'Format: YYYY-MM-DD (z.B. 2026-01-20)',
                'attributes' => ['placeholder' => '2026-01-20']
            ],
            'countdown_time' => [
                'type' => 'text',
                'label' => 'Zieluhrzeit',
                'notice' => 'Format: HH:MM:SS (z.B. 10:30:00)',
                'attributes' => ['placeholder' => '10:30:00']
            ],
            'countdown_language' => [
                'type' => 'choice',
                'label' => 'Sprache',
                'choices' => [
                    'de' => 'Deutsch (Tage, Stunden, Minuten, Sekunden)',
                    'en' => 'English (Days, Hours, Minutes, Seconds)'
                ],
                'default' => 'de'
            ],
            'countdown_mode' => [
                'type' => 'choice',
                'label' => 'Anzeigemodus',
                'choices' => [
                    'simple' => 'Zahlen nur: 05 10 34 30',
                    'separator' => 'Mit Trennzeichen: 05:10:34:30',
                    'labels' => 'Mit Labels: 05 Days : 10 Hours : 34 Minutes : 30 Seconds',
                    'compact' => 'Kompakt: 5d 10h 34m 30s'
                ],
                'default' => 'separator'
            ],
            'countdown_size' => [
                'type' => 'choice',
                'label' => 'Größe',
                'choices' => [
                    'uk-text-large' => 'Klein',
                    'uk-h3' => 'Standard (H3)',
                    'uk-h2' => 'Groß (H2)',
                    'uk-h1' => 'Sehr groß (H1)'
                ],
                'default' => 'uk-h2'
            ],
            'countdown_align' => [
                'type' => 'choice',
                'label' => 'Ausrichtung',
                'choices' => [
                    '' => 'Links',
                    'uk-text-center' => 'Zentriert',
                    'uk-text-right' => 'Rechts'
                ],
                'default' => 'uk-text-center'
            ],
            'countdown_separator' => [
                'type' => 'text',
                'label' => 'Trennzeichen',
                'value' => ':',
                'notice' => 'Für "Mit Trennzeichen" Modus'
            ],
            'countdown_labels' => [
                'type' => 'checkbox',
                'label' => 'Labels anzeigen (Tage, Stunden, etc.)',
                'notice' => 'Nur für "Mit Labels" Modus relevant'
            ],
            'countdown_reload' => [
                'type' => 'checkbox',
                'label' => 'Seite neuladen nach Ablauf'
            ],
            'countdown_text_color' => [
                'type' => 'choice',
                'label' => 'Textfarbe',
                'choices' => [
                    '' => 'Standard',
                    'uk-text-primary' => 'Primary',
                    'uk-text-secondary' => 'Secondary',
                    'uk-text-success' => 'Success',
                    'uk-text-danger' => 'Danger',
                    'uk-text-warning' => 'Warning',
                    'uk-light' => 'Hell',
                    'uk-dark' => 'Dunkel'
                ],
                'default' => ''
            ]
        ],
        $config::getSectionFields()
    ),
];
