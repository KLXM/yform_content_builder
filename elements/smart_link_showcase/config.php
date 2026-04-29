<?php

/**
 * Smart-Link Showcase - Demo-Element
 */

$config = \KLXM\YFormContentBuilder\Config::class;

return [
    'label' => 'Smart-Link Showcase',
    'icon' => 'fa fa-link',
    'description' => 'Demo-Linkliste mit smart_link, automatischer Medienvorschau und Icon-Fallback.',
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
                'label' => 'Überschrift',
                'default' => 'Linkliste',
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
            'items' => [
                'type' => 'repeater',
                'label' => 'Links',
                'add_label' => 'Link hinzufügen',
                'view' => 'list',
                'fields' => [
                    'title' => [
                        'type' => 'text',
                        'label' => 'Titel',
                        'col' => 12,
                    ],
                    'text' => [
                        'type' => 'text',
                        'label' => 'Beschreibung',
                    ],
                    'link' => [
                        'type' => 'smart_link',
                        'label' => 'Link',
                        'multiple' => false,
                        'types' => 'auto,url,intern,media,mail,tel,yform',
                        // yform_table: YForm-Tabellenname (z.B. 'rex_contacts')
                        // yform_field: Anzeigefeld der Einträge (z.B. 'name')
                        // Ohne Konfiguration erscheint im Widget ein Demo-Hinweis.
                        'yform_table' => '',
                        'yform_field' => 'name',
                        'notice' => 'Alle üblichen Linktypen sind aktiv.',
                    ],
                ],
            ],
        ],
        $config::getSectionFields()
    ),
];