<?php
/**
 * DoForm2026 - Redakteursfreundlicher Formular-Generator
 * Schrittweise Konfiguration mit Presets und kontextbezogenen Optionen.
 */

$config = yform_content_builder_config::class;

$spamProtectionOptions = [
    'honeypot' => 'Honeypot (verstecktes Feld)',
    'time' => 'Zeit-Check (mind. 3 Sek.)',
    'both' => 'Beide',
    '' => 'Keiner',
];

return [
    'label' => 'DoForm2026',
    'icon' => 'fa fa-wpforms',
    'description' => 'Interaktive, schrittweise Formular-Konfiguration fuer Redakteure.',
    'version' => '1.0.0',
    'category' => 'form',
    'field_groups' => [
        'step_start' => [
            'label' => 'Schritt 1: Start',
            'icon' => 'fa-play-circle',
            'fields' => ['form_headline', 'form_headline_tag', 'form_intro'],
        ],
        'step_fields' => [
            'label' => 'Schritt 2: Felder',
            'icon' => 'fa-list-alt',
            'fields' => ['fields', 'submit_text'],
        ],
        'step_mail' => [
            'label' => 'Schritt 3: Versand',
            'icon' => 'fa-envelope',
            'fields' => ['email_to', 'email_subject', 'email_from_field', 'success_message', 'error_message'],
        ],
        'step_security' => [
            'label' => 'Schritt 4: Sicherheit',
            'icon' => 'fa-shield',
            'fields' => ['spam_protection', 'privacy_checkbox', 'privacy_text', 'privacy_link', 'send_copy', 'copy_subject', 'copy_intro', 'copy_footer', 'copy_mask_iban'],
        ],
        'step_layout' => [
            'label' => 'Schritt 5: Layout',
            'icon' => 'fa-columns',
            'fields' => ['layout', 'submit_style', 'ajax_enhancement', 'multistep_enabled', 'multistep_prev_label', 'multistep_next_label', 'container_width', 'section_padding'],
        ],
    ],
    'fields' => [
        'form_headline' => [
            'type' => 'text',
            'label' => 'Formular-Ueberschrift',
        ],
        'form_headline_tag' => [
            'type' => 'choice',
            'label' => 'Ueberschrift HTML-Tag',
            'selectpicker' => false,
            'choices' => [
                'h2' => 'H2',
                'h3' => 'H3',
                'h4' => 'H4',
                'h5' => 'H5',
            ],
            'default' => 'h2',
        ],
        'form_intro' => [
            'type' => 'textarea',
            'label' => 'Einleitungstext',
            'notice' => 'Optionaler Text vor dem Formular.',
        ],
        'fields' => [
            'type' => 'repeater',
            'label' => 'Formular-Felder',
            'min' => 0,
            'max' => 25,
            'item_label' => 'Feld',
            'add_label' => 'Feld hinzufuegen',
            'collapsed' => false,
            'fields' => [
                'field_type' => [
                    'type' => 'choice',
                    'label' => 'Feldtyp',
                    'selectpicker' => false,
                    'choices' => [
                        'text' => 'Textfeld',
                        'customer_number' => 'Kundennummer',
                        'meter_reading' => 'Zaehlerstand',
                        'email' => 'E-Mail',
                        'tel' => 'Telefon',
                        'file' => 'Datei-Upload',
                        'textarea' => 'Textbereich',
                        'select' => 'Auswahl (Dropdown)',
                        'checkbox' => 'Checkbox',
                        'radio' => 'Radio-Buttons',
                        'hidden' => 'Versteckt',
                        'fieldset' => 'Fieldset (Gruppierung)',
                        'fieldset_end' => 'Fieldset Ende',
                        'headline' => 'Zwischenueberschrift',
                        'divider' => 'Trennlinie',
                    ],
                    'default' => 'text',
                ],
                'field_name' => [
                    'type' => 'text',
                    'label' => 'Feld-Name',
                    'notice' => 'Technischer Name, z.B. vorname, kundennummer, zaehlerstand.',
                ],
                'field_label' => [
                    'type' => 'text',
                    'label' => 'Beschriftung',
                ],
                'field_placeholder' => [
                    'type' => 'text',
                    'label' => 'Platzhalter',
                ],
                'field_required' => [
                    'type' => 'checkbox',
                    'label' => 'Pflichtfeld',
                ],
                'field_width' => [
                    'type' => 'choice',
                    'label' => 'Breite',
                    'selectpicker' => false,
                    'choices' => [
                        '1-1' => 'Volle Breite',
                        '1-2' => 'Halbe Breite',
                        '1-3' => 'Ein Drittel',
                        '2-3' => 'Zwei Drittel',
                        '1-4' => 'Ein Viertel',
                        '3-4' => 'Drei Viertel',
                    ],
                    'default' => '1-1',
                ],
                'field_options_source' => [
                    'type' => 'choice',
                    'label' => 'Optionen-Quelle',
                    'selectpicker' => false,
                    'choices' => [
                        'manual' => 'Manuell eingeben',
                        'sql' => 'SQL-Abfrage',
                    ],
                    'default' => 'manual',
                ],
                'field_options' => [
                    'type' => 'textarea',
                    'label' => 'Manuelle Optionen',
                    'notice' => 'Eine Option pro Zeile (wert|Anzeige oder nur Anzeige).',
                ],
                'field_options_sql' => [
                    'type' => 'textarea',
                    'label' => 'SQL-Abfrage',
                    'notice' => 'SELECT id AS value, name AS label FROM ...',
                    'attributes' => ['rows' => 3],
                    'perm' => 'admin',
                ],
                'field_default' => [
                    'type' => 'text',
                    'label' => 'Standardwert',
                ],
                'field_validation_type' => [
                    'type' => 'choice',
                    'label' => 'Validierungstyp',
                    'selectpicker' => false,
                    'choices' => [
                        '' => 'Keine zusaetzliche Validierung',
                        'editor_rule' => 'Einfache Regel (z.B. KD-30000-99-AA)',
                        'customer_number' => 'Kundennummer (z.B. KD-123456)',
                        'meter_reading' => 'Zaehlerstand (z.B. 12345,67)',
                        'meter_reading_int' => 'Zaehlerstand ganzzahlig',
                        'iban' => 'IBAN',
                        'bic' => 'BIC/SWIFT',
                        'plz_de' => 'Postleitzahl (Deutschland)',
                        'plz_at' => 'Postleitzahl (Oesterreich)',
                        'plz_ch' => 'Postleitzahl (Schweiz)',
                        'phone' => 'Telefonnummer',
                        'url' => 'URL/Webseite',
                        'date_de' => 'Datum (TT.MM.JJJJ)',
                        'date_iso' => 'Datum (JJJJ-MM-TT)',
                        'time' => 'Uhrzeit (HH:MM)',
                        'number' => 'Nur Zahlen',
                        'alpha' => 'Nur Buchstaben',
                        'alphanumeric' => 'Buchstaben und Zahlen',
                        'min_length' => 'Mindestlaenge',
                        'max_length' => 'Maximallaenge',
                        'compare' => 'Wertevergleich',
                        'regex' => 'Eigenes Regex-Muster',
                    ],
                    'default' => '',
                ],
                'field_input_mode' => [
                    'type' => 'choice',
                    'label' => 'Schreibweise erzwingen',
                    'selectpicker' => false,
                    'choices' => [
                        '' => 'Keine automatische Anpassung',
                        'trim' => 'Leerzeichen am Anfang/Ende entfernen',
                        'uppercase' => 'In GROSSBUCHSTABEN umwandeln',
                        'lowercase' => 'In kleinbuchstaben umwandeln',
                        'no_spaces' => 'Alle Leerzeichen entfernen',
                        'digits_only' => 'Nur Ziffern behalten',
                        'alnum_upper' => 'Nur Buchstaben/Zahlen (GROSS)',
                        'meter_reading' => 'Zaehlerstand normalisieren (Komma/Punkt)',
                    ],
                    'default' => '',
                ],
                'field_pattern' => [
                    'type' => 'text',
                    'label' => 'HTML Pattern (optional)',
                    'notice' => 'Regex ohne /.../, z.B. [A-Z]{2}-[0-9]{6}',
                ],
                'field_validation_param' => [
                    'type' => 'text',
                    'label' => 'Validierungs-Parameter',
                    'notice' => 'Bei "Einfache Regel" z.B. KD-30000-99-AA (A=1 Buchstabe, 9=1 Ziffer, Zahl=Maximalwert).',
                ],
                'field_error_message' => [
                    'type' => 'text',
                    'label' => 'Fehlermeldung',
                ],
                'field_attributes' => [
                    'type' => 'text',
                    'label' => 'Zusaetzliche HTML-Attribute',
                ],
            ],
            'item_modal' => [
                'label' => 'Erweiterte Optionen',
                'icon' => 'fa-sliders',
                'fields' => ['field_options_source', 'field_options', 'field_options_sql', 'field_default', 'field_validation_type', 'field_input_mode', 'field_pattern', 'field_validation_param', 'field_error_message', 'field_attributes'],
            ],
        ],
        'submit_text' => [
            'type' => 'text',
            'label' => 'Button-Text',
            'default' => 'Nachricht senden',
        ],
        'email_to' => [
            'type' => 'text',
            'label' => 'Empfaenger E-Mail',
            'notice' => 'Eine oder mehrere E-Mail-Adressen (Komma oder Semikolon).',
            'required' => true,
        ],
        'email_subject' => [
            'type' => 'text',
            'label' => 'E-Mail Betreff',
            'default' => 'Neue Anfrage ueber DoForm2026',
            'notice' => 'Platzhalter: {name}, {email}, {subject}',
        ],
        'email_from_field' => [
            'type' => 'choice',
            'label' => 'Absender',
            'selectpicker' => false,
            'choices' => [
                'email' => 'E-Mail aus Formular',
                'system' => 'System E-Mail',
            ],
            'default' => 'email',
        ],
        'success_message' => [
            'type' => 'textarea',
            'label' => 'Erfolgsmeldung',
            'default' => 'Vielen Dank fuer Ihre Nachricht. Wir melden uns schnellstmoeglich.',
        ],
        'error_message' => [
            'type' => 'textarea',
            'label' => 'Fehlermeldung',
            'default' => 'Es ist ein Fehler aufgetreten. Bitte spaeter erneut versuchen.',
        ],
        'spam_protection' => [
            'type' => 'choice',
            'label' => 'Spam-Schutz',
            'selectpicker' => false,
            'choices' => $spamProtectionOptions,
            'default' => 'both',
        ],
        'privacy_checkbox' => [
            'type' => 'checkbox',
            'label' => 'Datenschutz-Checkbox anzeigen',
        ],
        'privacy_text' => [
            'type' => 'text',
            'label' => 'Datenschutz-Text',
            'default' => 'Ich habe die {link} gelesen und akzeptiere sie.',
            'notice' => 'Verwende {link} fuer den Link zur Datenschutzseite.',
        ],
        'privacy_link' => [
            'type' => 'be_link',
            'label' => 'Datenschutz-Seite',
        ],
        'send_copy' => [
            'type' => 'checkbox',
            'label' => 'Bestaetigungs-E-Mail an Absender senden',
        ],
        'copy_subject' => [
            'type' => 'text',
            'label' => 'Betreff Bestaetigungs-E-Mail',
            'default' => 'Ihre Anfrage bei uns',
        ],
        'copy_intro' => [
            'type' => 'textarea',
            'label' => 'Einleitungstext Bestaetigungs-E-Mail',
            'default' => "Vielen Dank fuer Ihre Nachricht!\n\nWir haben Ihre Anfrage erhalten und melden uns schnellstmoeglich.",
        ],
        'copy_footer' => [
            'type' => 'textarea',
            'label' => 'Abschlusstext Bestaetigungs-E-Mail',
            'default' => "Mit freundlichen Gruessen\nIhr Team",
        ],
        'copy_mask_iban' => [
            'type' => 'checkbox',
            'label' => 'IBAN in Bestaetigungs-E-Mail anonymisieren',
            'default' => true,
        ],
        'layout' => [
            'type' => 'choice',
            'label' => 'Formular-Layout',
            'selectpicker' => false,
            'choices' => [
                'default' => 'Standard (Labels oben)',
                'horizontal' => 'Horizontal (Labels links)',
                'floating' => 'Floating Labels',
                'stacked' => 'Kompakt gestapelt',
            ],
            'default' => 'default',
        ],
        'submit_style' => [
            'type' => 'choice',
            'label' => 'Button-Style',
            'selectpicker' => false,
            'choices' => [
                'primary' => 'Primary',
                'secondary' => 'Secondary',
                'default' => 'Default',
                'danger' => 'Danger',
                'success' => 'Success',
            ],
            'default' => 'primary',
        ],
        'ajax_enhancement' => [
            'type' => 'checkbox',
            'label' => 'AJAX-Verbesserung aktivieren',
            'notice' => 'Formular wird ohne kompletten Seitenreload abgesendet.',
        ],
        'multistep_enabled' => [
            'type' => 'checkbox',
            'label' => 'Multi-Step aktivieren',
            'notice' => 'Verwendet vorhandene Fieldsets als Schritte.',
        ],
        'multistep_prev_label' => [
            'type' => 'text',
            'label' => 'Button Zurueck',
            'default' => 'Zurueck',
        ],
        'multistep_next_label' => [
            'type' => 'text',
            'label' => 'Button Weiter',
            'default' => 'Weiter',
        ],
        'container_width' => [
            'type' => 'choice',
            'label' => 'Container-Breite',
            'choices' => $config::getContainerOptions(),
            'default' => 'uk-container',
        ],
        'section_padding' => [
            'type' => 'choice',
            'label' => 'Section-Breite',
            'choices' => [
                '' => 'Standard',
                'uk-section-xsmall' => 'Sehr kompakt',
                'uk-section-small' => 'Kompakt',
                'uk-section' => 'Normal',
                'uk-section-large' => 'Gross',
                'uk-section-xlarge' => 'Sehr gross',
            ],
            'default' => '',
        ],
    ],
];
