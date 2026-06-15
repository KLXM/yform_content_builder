<?php
/**
 * Forcal-Termine-Element.
 */

use KLXM\YFormContentBuilder\ForcalRenderer;
use KLXM\YFormContentBuilder\Starter\StarterConfig;

$forcalAvailable = rex_addon::exists('forcal')
    && rex_addon::get('forcal')->isAvailable()
    && class_exists(ForcalRenderer::class)
    && ForcalRenderer::isAvailable();

if (!$forcalAvailable) {
    return null;
}

$catChoices = ['' => '— Alle Kategorien —'];
$repeatChoices = ['' => '— Bitte Termin waehlen —'];

foreach (ForcalRenderer::getCategoryChoices() as $id => $name) {
    $catChoices[(string) $id] = $name . ' (#' . $id . ')';
}
foreach (ForcalRenderer::getRepeatingEntryChoices() as $id => $name) {
    $repeatChoices[(string) $id] = $name . ' (#' . $id . ')';
}

$categoryChoices = [];
foreach ($catChoices as $key => $label) {
    if ($key !== '') {
        $categoryChoices[(string) $key] = $label;
    }
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
        'fields' => array_merge(
            StarterConfig::getGridFieldNames(),
            [
                'teaser_length',
                'headline_tag',
                'headline_style',
                'group_by',
                'group_heading_tag',
                'group_heading_style',
                'show_links',
                'url_pattern',
                'image_field',
            ],
            StarterConfig::getOptionalSectionFieldNames()
        ),
    ],

    'fields' => array_merge(
        StarterConfig::getGridFields(),
        [
            'mode' => [
                'type' => 'choice',
                'label' => 'Modus',
                'choices' => [
                    'categories' => 'Nach Kategorie(n) - kommende Termine',
                    'repeat' => 'Wiederkehrender Termin - naechste X Wiederholungen',
                ],
                'default' => 'categories',
            ],
            'categories' => [
                'type' => 'choice',
                'label' => 'Kategorien',
                'choices' => [] !== $categoryChoices
                    ? $categoryChoices
                    : ['' => '— keine Kategorien gefunden —'],
                'multiple' => true,
                'notice' => 'Modus "Nach Kategorie": Mehrfachauswahl moeglich. Keine Auswahl = alle Kategorien.',
                'default' => [],
                'visible_if' => [
                    'mode' => 'categories',
                ],
            ],
            'repeat_entry' => [
                'type' => 'choice',
                'label' => 'Serientermin',
                'choices' => $repeatChoices,
                'notice' => 'Modus "Wiederkehrender Termin": waehlt einen Eintrag aus, dessen naechste Wiederholungen aufgelistet werden.',
                'default' => '',
                'visible_if' => [
                    'mode' => 'repeat',
                ],
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
                'label' => 'Hauptueberschrift: HTML-Tag',
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
                'notice' => 'Tag der Hauptueberschrift.',
            ],
            'headline_style' => [
                'type' => 'choice',
                'label' => 'Hauptueberschrift: Stil',
                'choices' => [
                    'plain' => 'Schlicht',
                    'uk-heading-line' => 'Mit Linie (uk-heading-line)',
                    'uk-heading-bullet' => 'Bullet (uk-heading-bullet)',
                    'uk-heading-divider' => 'Divider (uk-heading-divider)',
                    'uk-heading-small' => 'Klein (uk-heading-small)',
                    'uk-heading-medium' => 'Medium (uk-heading-medium)',
                    'uk-heading-large' => 'Gross (uk-heading-large)',
                    'uk-text-uppercase uk-text-meta' => 'Meta / Uppercase',
                ],
                'default' => 'uk-heading-line',
                'notice' => 'Visueller Stil - wirkt im UIkit-Template.',
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
                'notice' => '1-' . ForcalRenderer::MAX_LIMIT . '. Default: 5.',
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
                'notice' => '30-800. Default: 160',
                'default' => '160',
            ],
            'url_pattern' => [
                'type' => 'text',
                'label' => 'URL-Pattern fuer Termin-Detailseite',
                'notice' => 'Optional. Platzhalter <code>{id}</code>, z.B. <code>/termine/?id={id}</code>. Leer = keine Verlinkung.',
                'default' => '',
            ],
            'image_field' => [
                'type' => 'text',
                'label' => 'Forcal-Feldname fuer Bild',
                'notice' => 'Name des Media-Felds aus dem forcal-Fieldset (z.B. <code>image</code>, <code>bild</code>, <code>header_image</code>). Leer = kein Bild.',
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
                    'month' => 'Monatstrenner (z.B. "Mai 2026")',
                    'year' => 'Jahrestrenner (z.B. "2026")',
                    'year_month' => 'Jahres- + Monatstrenner (verschachtelt)',
                ],
                'default' => '',
                'notice' => 'Optional: Termine nach Monat oder Jahr gruppieren mit Zwischenueberschrift.',
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
                'notice' => 'Tag der Trenner-Ueberschrift.',
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
                    'uk-heading-large' => 'Gross (uk-heading-large)',
                    'uk-text-uppercase uk-text-meta' => 'Meta / Uppercase',
                ],
                'default' => 'uk-heading-line',
                'notice' => 'Visueller Stil - wirkt im UIkit-Template.',
            ],
        ],
        StarterConfig::getOptionalSectionFields()
    ),
];
