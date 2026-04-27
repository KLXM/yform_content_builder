<?php
/**
 * Kontakt-Picker-Element
 *
 * Picked einzelne Kontakte aus den unter „YForm-Listen-Profile" konfigurierten
 * Kontakt-Profilen (Profile mit Vorname-Mapping). Nutzt denselben Renderer wie
 * `yform_list`, daher reicht ein zentrales Profil zur Konfiguration der Tabelle/Felder.
 *
 * Element nur verfuegbar, wenn mindestens ein Kontakt-Profil existiert.
 */

$config = yform_content_builder_config::class;

if (!class_exists('YformListProfiles')) {
    return null;
}

$contactChoices = YformListProfiles::getContactPickerChoices();

if ([] === $contactChoices) {
    return null;
}

return [
    'label' => 'Kontakt-Picker',
    'description' => 'Einzelne Kontakte aus YForm pickern (basiert auf den Kontakt-Profilen)',
    'icon' => 'fa-id-card',
    'category' => 'data',

    'settings_modal' => [
        'label' => 'Layout & Sektion',
        'icon' => 'fa-cog',
        'fields' => $config::getSettingsModalFields([
            'teaser_length',
        ]),
    ],

    'fields' => array_merge(
        $config::getGridFields(),
        [
            'items' => [
                'type' => 'repeater',
                'label' => 'Kontakte',
                'add_label' => 'Kontakt hinzufügen',
                'view' => 'list',
                'fields' => [
                    'contact' => [
                        'type' => 'choice',
                        'label' => 'Kontakt',
                        'choices' => $contactChoices,
                        'notice' => 'Kontakt aus den hinterlegten Profilen wählen.',
                    ],
                ],
            ],
            'headline' => [
                'type' => 'text',
                'label' => 'Überschrift',
                'notice' => 'Optional über der Liste angezeigt',
            ],
            'description' => [
                'type' => 'textarea',
                'label' => 'Beschreibung',
                'notice' => 'Optional als Einleitungstext',
            ],
            'layout' => [
                'type' => 'choice',
                'label' => 'Layout',
                'choices' => [
                    'contact_compact' => 'Kontakt kompakt (Card-Header mit Avatar)',
                    'contact' => 'Kontakt-Karten (zentriert, ausführlich)',
                    'list' => 'Liste mit Bild + Anriss',
                ],
                'default' => 'contact_compact',
            ],
            'show_links' => [
                'type' => 'checkbox',
                'label' => 'Namen verlinken (Detail-URL aus Profil)',
                'default' => false,
            ],
        ],
        $config::getSectionFields(),
    ),
];
