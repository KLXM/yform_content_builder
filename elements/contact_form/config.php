<?php
/**
 * Kontaktformular Element - Konfiguration
 * Flexibler Formular-Builder mit PHPMailer Integration
 */

// Zentrale Konfigurationsklasse
$config = yform_content_builder_config::class;

// PHPMailer verfügbar?
$hasPhpmailer = rex_addon::get('phpmailer')->isAvailable();

// Spam-Schutz Optionen
$spamProtectionOptions = [
    'honeypot' => 'Honeypot (verstecktes Feld)',
    'time' => 'Zeit-Check (mind. 3 Sek.)',
    'both' => 'Beide',
    '' => 'Keiner'
];

return [
    'label' => 'Kontaktformular',
    'icon' => 'fa fa-envelope',
    'description' => 'Flexibles Kontaktformular mit E-Mail-Versand',
    'version' => '1.13.0',
    'category' => 'form',
    
    // Tab-Gruppierung
    'field_groups' => [
        'fields_tab' => [
            'label' => 'Formular-Felder',
            'icon' => 'fa-list-alt',
            'fields' => ['form_headline', 'form_headline_tag', 'form_intro', 'fields', 'submit_text']
        ],
        'email_tab' => [
            'label' => 'E-Mail',
            'icon' => 'fa-envelope',
            'fields' => ['email_to', 'email_subject', 'from_email', 'from_name', 'success_message', 'error_message', 'spam_protection']
        ],
        'design_tab' => [
            'label' => 'Design',
            'icon' => 'fa-paint-brush',
            'fields' => ['submit_style', 'layout', 'multistep_enabled', 'multistep_prev_label', 'multistep_next_label', 'privacy_checkbox', 'privacy_text', 'privacy_link', 'ajax_enhancement']
        ],
        'copy_tab' => [
            'label' => 'Bestätigung',
            'icon' => 'fa-reply',
            'fields' => ['send_copy', 'copy_subject', 'copy_intro', 'copy_footer', 'copy_mask_iban']
        ],
        'section_tab' => [
            'label' => 'Sektion',
            'icon' => 'fa-columns',
            'fields' => $config::getSectionFieldNames()
        ]
    ],
    
    'fields' => array_merge(
        [
            // === TAB 1: FORMULAR-FELDER ===
            'form_headline' => [
                'type' => 'text',
                'label' => 'Formular-Überschrift',
                'notice' => 'Optional: Überschrift über dem Formular'
            ],
            'form_headline_tag' => [
                'type' => 'choice',
                'label' => 'Überschrift HTML-Tag',
                'selectpicker' => false,
                'choices' => [
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5'
                ],
                'default' => 'h2'
            ],
            'form_intro' => [
                'type' => 'textarea',
                'label' => 'Einleitungstext',
                'notice' => 'Optional: Text vor dem Formular'
            ],
            'fields' => [
                'type' => 'repeater',
                'label' => 'Formular-Felder',
                'min' => 1,
                'max' => 20,
                'item_label' => 'Feld',
                'add_label' => 'Feld hinzufügen',
                'collapsed' => false,
                'fields' => [
                    'field_type' => [
                        'type' => 'choice',
                        'label' => 'Feldtyp',
                        'selectpicker' => false,
                        'choices' => [
                            'text' => 'Textfeld',
                            'customer_number' => 'Kundennummer',
                            'meter_reading' => 'Zählerstand',
                            'email' => 'E-Mail',
                            'tel' => 'Telefon',
                            'file' => 'Datei-Upload',
                            'textarea' => 'Textbereich',
                            'select' => 'Auswahl (Dropdown)',
                            'checkbox' => 'Checkbox',
                            'radio' => 'Radio-Buttons',
                            'hidden' => 'Versteckt',
                            'fieldset' => '── Fieldset (Gruppierung) ──',
                            'fieldset_end' => '── Fieldset Ende ──',
                            'headline' => 'Zwischenüberschrift',
                            'divider' => 'Trennlinie'
                        ],
                        'default' => 'text'
                    ],
                    'field_name' => [
                        'type' => 'text',
                        'label' => 'Feld-Name',
                        'notice' => 'Technischer Name (ohne Leerzeichen, z.B. "vorname")'
                    ],
                    'field_label' => [
                        'type' => 'text',
                        'label' => 'Beschriftung'
                    ],
                    'field_placeholder' => [
                        'type' => 'text',
                        'label' => 'Platzhalter'
                    ],
                    'field_required' => [
                        'type' => 'checkbox',
                        'label' => 'Pflichtfeld'
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
                            '3-4' => 'Drei Viertel'
                        ],
                        'default' => '1-1'
                    ],
                    // Erweiterte Optionen im Item-Modal
                    'field_options_source' => [
                        'type' => 'choice',
                        'label' => 'Optionen-Quelle',
                        'selectpicker' => false,
                        'choices' => [
                            'manual' => 'Manuell eingeben',
                            'sql' => 'SQL-Abfrage'
                        ],
                        'default' => 'manual',
                        'notice' => 'Für Select/Radio: Woher kommen die Auswahlmöglichkeiten?'
                    ],
                    'field_options' => [
                        'type' => 'textarea',
                        'label' => 'Manuelle Optionen',
                        'notice' => 'Eine Option pro Zeile (wert|Anzeige oder nur Anzeige)'
                    ],
                    'field_options_sql' => [
                        'type' => 'textarea',
                        'label' => 'SQL-Abfrage',
                        'notice' => 'z.B.: SELECT id AS value, name AS label FROM rex_category WHERE status = 1 ORDER BY name',
                        'attributes' => ['rows' => 3],
                        'perm' => 'admin'
                    ],
                    'field_default' => [
                        'type' => 'text',
                        'label' => 'Standardwert'
                    ],
                    'field_validation_type' => [
                        'type' => 'choice',
                        'label' => 'Validierungstyp',
                        'selectpicker' => false,
                        'choices' => [
                            '' => 'Keine zusätzliche Validierung',
                            'editor_rule' => 'Einfache Regel (z.B. KD-30000-99-AA)',
                            'customer_number' => 'Kundennummer (z.B. KD-123456)',
                            'meter_reading' => 'Zählerstand (z.B. 12345,67)',
                            'meter_reading_int' => 'Zählerstand ganzzahlig',
                            'iban' => 'IBAN',
                            'bic' => 'BIC/SWIFT',
                            'plz_de' => 'Postleitzahl (Deutschland)',
                            'plz_at' => 'Postleitzahl (Österreich)',
                            'plz_ch' => 'Postleitzahl (Schweiz)',
                            'phone' => 'Telefonnummer',
                            'url' => 'URL/Webseite',
                            'date_de' => 'Datum (TT.MM.JJJJ)',
                            'date_iso' => 'Datum (JJJJ-MM-TT)',
                            'time' => 'Uhrzeit (HH:MM)',
                            'number' => 'Nur Zahlen',
                            'alpha' => 'Nur Buchstaben',
                            'alphanumeric' => 'Buchstaben und Zahlen',
                            'min_length' => 'Mindestlänge',
                            'max_length' => 'Maximallänge',
                            'compare' => 'Wertevergleich',
                            'regex' => 'Eigenes Regex-Muster'
                        ],
                        'default' => ''
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
                            'meter_reading' => 'Zählerstand normalisieren (Komma/Punkt)'
                        ],
                        'default' => ''
                    ],
                    'field_pattern' => [
                        'type' => 'text',
                        'label' => 'HTML Pattern (optional)',
                        'notice' => 'Regex ohne /.../ für Browser-Validierung, z.B. [A-Z]{2}-[0-9]{6}'
                    ],
                    'field_validation_param' => [
                        'type' => 'text',
                        'label' => 'Validierungs-Parameter',
                        'notice' => 'Je nach Typ: Mindest-/Maximallänge, Vergleich wie {{feldname}} < {{99000}}, Regex oder bei "Einfache Regel" z.B. KD-30000-99-AA (A=1 Buchstabe, 9=1 Ziffer, Zahl=Maximalwert)'
                    ],
                    'field_error_message' => [
                        'type' => 'text',
                        'label' => 'Fehlermeldung'
                    ],
                    'field_attributes' => [
                        'type' => 'text',
                        'label' => 'Zusätzliche HTML-Attribute',
                        'notice' => 'Beispiele: data-custom="wert", uk-tooltip="Hilfetext", class="my-class", autocomplete="off"'
                    ]
                ],
                'item_modal' => [
                    'label' => 'Erweiterte Optionen',
                    'icon' => 'fa-sliders',
                    'fields' => ['field_options_source', 'field_options', 'field_options_sql', 'field_default', 'field_validation_type', 'field_input_mode', 'field_pattern', 'field_validation_param', 'field_error_message', 'field_attributes']
                ]
            ],
            'submit_text' => [
                'type' => 'text',
                'label' => 'Button-Text',
                'default' => 'Nachricht senden'
            ],
            
            // === TAB 2: E-MAIL ===
            'email_to' => [
                'type' => 'text',
                'label' => 'Empfänger E-Mail',
                'notice' => 'Eine oder mehrere E-Mail-Adressen (mit Komma oder Semikolon trennen)',
                'required' => true
            ],
            'email_subject' => [
                'type' => 'text',
                'label' => 'E-Mail Betreff',
                'default' => 'Neue Kontaktanfrage',
                'notice' => 'Platzhalter: {name}, {email}, {subject}'
            ],
            'from_email' => [
                'type' => 'text',
                'label' => 'Absender E-Mail-Adresse',
                'notice' => 'Leer = System-Standard aus PHPMailer-Konfiguration. Die E-Mail des Ausfüllenden wird nur als Reply-To gesetzt.',
                'default' => ''
            ],
            'from_name' => [
                'type' => 'text',
                'label' => 'Absender Name',
                'notice' => 'Leer = System-Standard aus PHPMailer-Konfiguration.',
                'default' => ''
            ],
            'success_message' => [
                'type' => 'textarea',
                'label' => 'Erfolgsmeldung',
                'default' => 'Vielen Dank für Ihre Nachricht. Wir werden uns schnellstmöglich bei Ihnen melden.'
            ],
            'error_message' => [
                'type' => 'textarea',
                'label' => 'Fehlermeldung',
                'default' => 'Es ist ein Fehler aufgetreten. Bitte versuchen Sie es später erneut.'
            ],
            'spam_protection' => [
                'type' => 'choice',
                'label' => 'Spam-Schutz',
                'selectpicker' => false,
                'choices' => $spamProtectionOptions,
                'default' => 'both'
            ],
            
            // === TAB 3: DESIGN ===
            'submit_style' => [
                'type' => 'choice',
                'label' => 'Button-Style',
                'selectpicker' => false,
                'choices' => [
                    'primary' => 'Primary',
                    'secondary' => 'Secondary',
                    'default' => 'Default',
                    'danger' => 'Danger',
                    'success' => 'Success'
                ],
                'default' => 'primary'
            ],
            'layout' => [
                'type' => 'choice',
                'label' => 'Formular-Layout',
                'selectpicker' => false,
                'choices' => [
                    'default' => 'Standard (Labels oben)',
                    'horizontal' => 'Horizontal (Labels links)',
                    'floating' => 'Floating Labels',
                    'stacked' => 'Kompakt gestapelt'
                ],
                'default' => 'default'
            ],
            'multistep_enabled' => [
                'type' => 'checkbox',
                'label' => 'Multi-Step aktivieren',
                'notice' => 'Verwendet vorhandene Fieldsets als einzelne Schritte.'
            ],
            'multistep_prev_label' => [
                'type' => 'text',
                'label' => 'Button Zurück',
                'default' => 'Zurück'
            ],
            'multistep_next_label' => [
                'type' => 'text',
                'label' => 'Button Weiter',
                'default' => 'Weiter'
            ],
            'privacy_checkbox' => [
                'type' => 'checkbox',
                'label' => 'Datenschutz-Checkbox anzeigen'
            ],
            'privacy_text' => [
                'type' => 'text',
                'label' => 'Datenschutz-Text',
                'default' => 'Ich habe die {link} gelesen und akzeptiere sie.',
                'notice' => 'Verwende {link} für den Link zur Datenschutzseite'
            ],
            'privacy_link' => [
                'type' => 'be_link',
                'label' => 'Datenschutz-Seite'
            ],
            'ajax_enhancement' => [
                'type' => 'checkbox',
                'label' => 'AJAX als progressive Verbesserung aktivieren',
                'default' => true,
                'notice' => 'Barrierefrei: Ohne JavaScript bleibt das normale Formular-Posting aktiv. Mit JavaScript wird nur die Übertragung ohne Seitenreload ergänzt.'
            ],
            
            // === TAB 4: BESTÄTIGUNG ===
            'send_copy' => [
                'type' => 'checkbox',
                'label' => 'Bestätigungs-E-Mail aktivieren',
                'notice' => 'Der Absender erhält eine Kopie seiner Anfrage'
            ],
            'copy_subject' => [
                'type' => 'text',
                'label' => 'Betreff',
                'default' => 'Ihre Anfrage bei uns'
            ],
            'copy_intro' => [
                'type' => 'textarea',
                'label' => 'Einleitungstext',
                'default' => "Vielen Dank für Ihre Nachricht!\n\nWir haben Ihre Anfrage erhalten und werden uns schnellstmöglich bei Ihnen melden.\n\nNachfolgend eine Kopie Ihrer Anfrage:",
                'notice' => 'Dieser Text erscheint vor der Kopie der Formulardaten'
            ],
            'copy_footer' => [
                'type' => 'textarea',
                'label' => 'Abschlusstext / Signatur',
                'default' => "Mit freundlichen Grüßen\nIhr Team",
                'notice' => 'Dieser Text erscheint am Ende der E-Mail'
            ],
            'copy_mask_iban' => [
                'type' => 'checkbox',
                'label' => 'IBAN in Bestätigungs-E-Mail anonymisieren',
                'default' => true,
                'notice' => 'Maskiert IBAN-Werte in der Kopie an den Absender.'
            ]
        ],
        
        // Section-Felder (TAB 5)
        $config::getSectionFields()
    )
];
