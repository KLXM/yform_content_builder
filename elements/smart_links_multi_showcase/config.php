<?php

/**
 * Smart-Links Multi Showcase - Demo-Element
 */

$config = \KLXM\YFormContentBuilder\Config::class;

return [
    'label' => 'Smart-Links Multi Showcase',
    'icon' => 'fa fa-link',
    'description' => 'Demonstriert ein einzelnes smart_link Feld im Multiple-Modus.',
    'version' => '1.13.0',
    'category' => 'demo',

    'settings_modal' => [
        'label' => 'Layout & Sektion',
        'icon' => 'fa-cog',
        'fields' => $config::getSettingsModalFields([
            'headline',
            'intro',
            'show_preview',
        ]),
    ],

    'fields' => array_merge(
        $config::getGridFields(),
        [
            'headline' => [
                'type' => 'text',
                'label' => 'Ueberschrift',
                'default' => 'Mehrere Smart Links',
            ],
            'intro' => [
                'type' => 'text',
                'label' => 'Einleitung',
                'notice' => 'Optionaler Text oberhalb der Linkliste.',
            ],
            'show_preview' => [
                'type' => 'checkbox',
                'label' => 'Vorschaubilder anzeigen',
                'default' => true,
            ],
            'links' => [
                'type' => 'smart_link',
                'label' => 'Smart Links',
                'multiple' => true,
                'types' => 'auto,url,intern,media,mail,tel,yform',
                // Fuer YForm-Links optional setzen (z.B. rex_contacts / name)
                'yform_table' => '',
                'yform_field' => 'name',
                'notice' => 'Mehrfachauswahl aktiv: Ein Feld fuer viele Smart Links.',
            ],
        ],
        $config::getSectionFields()
    ),
];
