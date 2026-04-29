<?php
/**
 * Forcal-Termine-Element
 *
 * Zeigt kommende Termine aus dem forcal-Addon an. Zwei Modi:
 *  - Nach Kategorie(n) (Kategorie-IDs kommagetrennt)
 *  - Naechste X Wiederholungen eines bestimmten Termins (Picker)
 *
 * Layouts: Cards / Liste / Kompakt.
 */

use KLXM\YFormContentBuilder\ForcalRenderer;

$config = \KLXM\YFormContentBuilder\Config::class;

// Element ausblenden, wenn forcal nicht installiert/aktiviert ist.
$forcalAvailable = rex_addon::exists('forcal')
    && rex_addon::get('forcal')->isAvailable()
    && class_exists(ForcalRenderer::class)
    && ForcalRenderer::isAvailable();

if (!$forcalAvailable) {
    return null;
}

$catChoices = ['' => '— Alle Kategorien —'];
$repeatChoices = ['' => '— Bitte Termin waehlen —'];

if (class_exists(ForcalRenderer::class)) {
    foreach (ForcalRenderer::getCategoryChoices() as $id => $name) {
        $catChoices[(string) $id] = $name . ' (#' . $id . ')';
    }
    foreach (ForcalRenderer::getRepeatingEntryChoices() as $id => $name) {
        $repeatChoices[(string) $id] = $name . ' (#' . $id . ')';
    }
}

// Kategorie-Choices fuer Multi-Select
$categoryChoices = [];
foreach ($catChoices as $key => $label) {
    if ('' === $key) {
        continue;
    }
    $categoryChoices[(string) $key] = $label;
}

return [
    'label' => 'Forcal-Termine',
    'description' => 'Kommende Termine aus dem forcal-Kalender (nach Kategorie oder Serientermin)',
    'version' => '1.13.0',
    'icon' => 'fa-calendar',
    'category' => 'data',

    'settings_modal' => [
        'label' => 'Layout & Sektion',
        'icon' => 'fa-cog',
        'fields' => $config::getSettingsModalFields([
            'teaser_length',
            'headline_tag',
            'headline_style',
            'group_by',
            'group_heading_tag',
            'group_heading_style',
            'show_links',
            'url_pattern',
            'image_field',
        ]),
    ],

    'fields' => array_merge(
        $config::getGridFields(),
        [
            'mode' => [
                'type' => 'choice',
                'label' => 'Modus',
                'choices' => [
                    'categories' => 'Nach Kategorie(n) – kommende Termine',
                    'repeat' => 'Wiederkehrender Termin – naechste X Wiederholungen',
                ],
                'default' => 'categories',
                'notice' => '',
            ],
            'categories' => [
                'type' => 'choice',
                'label' => 'Kategorien',
                'choices' => [] !== $categoryChoices
                    ? $categoryChoices
                    : ['' => '— keine Kategorien gefunden —'],
                'multiple' => true,
                'notice' => 'Modus „Nach Kategorie": Mehrfachauswahl möglich. Keine Auswahl = alle Kategorien.',
                'default' => [],
            ],
            'repeat_entry' => [
                'type' => 'choice',
                'label' => 'Serientermin',
                'choices' => $repeatChoices,
                'notice' => 'Modus „Wiederkehrender Termin": waehlt einen Eintrag aus, dessen naechste Wiederholungen aufgelistet werden.',
                'default' => '',
            ],
            'headline' => [
                'type' => 'text',
                'label' => 'Ueberschrift',
                'notice' => 'Optional ueber der Liste angezeigt',
            ],
            'description' => [
                'type' => 'textarea',
                'label' => 'Beschreibung',
                'notice' => 'Optional als Einleitungstext',
            ],
            'headline_tag' => [
                'type' => 'choice',
                'label' => 'Hauptüberschrift: HTML-Tag',
                'selectpicker' => false,
                'choices' => [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                    'div' => 'DIV (kein Heading)',
                ],
                'default' => 'h2',
                'notice' => 'Tag der Hauptüberschrift.',
            ],
            'headline_style' => [
                'type' => 'choice',
                'label' => 'Hauptüberschrift: Stil',
                'choices' => [
                    'plain' => 'Schlicht',
                    'uk-heading-line' => 'Mit Linie (uk-heading-line)',
                    'uk-heading-bullet' => 'Bullet (uk-heading-bullet)',
                    'uk-heading-divider' => 'Divider (uk-heading-divider)',
                    'uk-heading-small' => 'Klein (uk-heading-small)',
                    'uk-heading-medium' => 'Medium (uk-heading-medium)',
                    'uk-heading-large' => 'Groß (uk-heading-large)',
                    'uk-text-uppercase uk-text-meta' => 'Meta / Uppercase',
                ],
                'default' => 'uk-heading-line',
                'notice' => 'Visueller Stil – wirkt im UIkit-Template.',
            ],
            'layout' => [
                'type' => 'choice',
                'label' => 'Layout',
                'choices' => [
                    'cards' => 'Kacheln (Cards)',
                    'list' => 'Liste mit Datum + Anriss',
                    'compact' => 'Kompakt (nur Titel + Datum)',
                ],
                'default' => 'cards',
            ],
            'limit' => [
                'type' => 'text',
                'label' => 'Anzahl Termine',
                'notice' => '1–' . ForcalRenderer::MAX_LIMIT . '. Default: 5.',
                'default' => '5',
            ],
            'show_image' => [
                'type' => 'checkbox',
                'label' => 'Bild anzeigen',
                'notice' => 'Wenn aktiv, wird zu jedem Termin ein Bild ausgegeben (im Cards-Layout oben, in der Liste als Thumbnail).',
                'default' => false,
            ],
            'teaser_length' => [
                'type' => 'text',
                'label' => 'Teaser-Laenge in Zeichen',
                'notice' => '30–800. Default: 160',
                'default' => '160',
            ],
            'url_pattern' => [
                'type' => 'text',
                'label' => 'URL-Pattern für Termin-Detailseite',
                'notice' => 'Optional. Platzhalter <code>{id}</code>, z.B. <code>/termine/?id={id}</code>. Leer = keine Verlinkung.',
                'default' => '',
            ],
            'image_field' => [
                'type' => 'text',
                'label' => 'Forcal-Feldname für Bild',
                'notice' => 'Name des Media-Felds aus dem forcal-Fieldset (z.B. <code>image</code>, <code>bild</code>, <code>header_image</code>). Leer = kein Bild. Bei Cards-Layout wird das Bild oben angezeigt.',
                'default' => '',
            ],
            'show_links' => [
                'type' => 'checkbox',
                'label' => 'Termine verlinken (URL-Pattern muss gesetzt sein)',
                'default' => true,
            ],
            'group_by' => [
                'type' => 'choice',
                'label' => 'Trenner / Gruppierung',
                'choices' => [
                    '' => '— keine Trenner —',
                    'month' => 'Monatstrenner (z.B. „Mai 2026")',
                    'year' => 'Jahrestrenner (z.B. „2026")',
                    'year_month' => 'Jahres- + Monatstrenner (verschachtelt)',
                ],
                'default' => '',
                'notice' => 'Optional: Termine nach Monat oder Jahr gruppieren mit Zwischenüberschrift.',
            ],
            'group_heading_tag' => [
                'type' => 'choice',
                'label' => 'Trenner: HTML-Tag',
                'selectpicker' => false,
                'choices' => [
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                    'div' => 'DIV (kein Heading)',
                ],
                'default' => 'h3',
                'notice' => 'Tag der Trenner-Überschrift.',
            ],
            'group_heading_style' => [
                'type' => 'choice',
                'label' => 'Trenner: Stil',
                'choices' => [
                    'plain' => 'Schlicht',
                    'uk-heading-line' => 'Mit Linie (uk-heading-line)',
                    'uk-heading-bullet' => 'Bullet (uk-heading-bullet)',
                    'uk-heading-divider' => 'Divider (uk-heading-divider)',
                    'uk-heading-small' => 'Klein (uk-heading-small)',
                    'uk-heading-medium' => 'Medium (uk-heading-medium)',
                    'uk-heading-large' => 'Groß (uk-heading-large)',
                    'uk-text-uppercase uk-text-meta' => 'Meta / Uppercase',
                ],
                'default' => 'uk-heading-line',
                'notice' => 'Visueller Stil – wirkt im UIkit-Template.',
            ],
        ],
        $config::getSectionFields(),
    ),
];
