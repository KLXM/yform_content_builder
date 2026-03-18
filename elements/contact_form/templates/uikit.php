<?php
/**
 * Kontaktformular Element - UIkit Template
 * Mit PHPMailer Integration, Spam-Schutz und SQL-Optionen
 * 
 * @var array $elementData
 */

// Eindeutige Form-ID
$formId = 'contact_form_' . uniqid();

// Einstellungen
$emailTo = $elementData['email_to'] ?? '';
$emailSubject = $elementData['email_subject'] ?? 'Neue Kontaktanfrage';
$emailFromField = $elementData['email_from_field'] ?? 'email';
$successMessage = $elementData['success_message'] ?? 'Vielen Dank für Ihre Nachricht.';
$errorMessage = $elementData['error_message'] ?? 'Es ist ein Fehler aufgetreten.';
$spamProtection = $elementData['spam_protection'] ?? 'both';
$submitText = $elementData['submit_text'] ?? 'Nachricht senden';
$submitStyle = $elementData['submit_style'] ?? 'primary';
$layout = $elementData['layout'] ?? 'default';
$privacyCheckbox = !empty($elementData['privacy_checkbox']);
$privacyText = $elementData['privacy_text'] ?? '';
$privacyLink = $elementData['privacy_link'] ?? '';
$ajaxEnhancement = !empty($elementData['ajax_enhancement']);
$sendCopy = !empty($elementData['send_copy']);
$copySubject = $elementData['copy_subject'] ?? 'Ihre Anfrage';

// Formular-Überschrift
$formHeadline = $elementData['form_headline'] ?? '';
$formHeadlineTag = $elementData['form_headline_tag'] ?? 'h2';
$formIntro = $elementData['form_intro'] ?? '';

// Formular-Felder
$fields = $elementData['fields'] ?? [];
$formInstanceKey = substr(sha1((string) json_encode([$emailTo, $formHeadline, $fields], JSON_UNESCAPED_UNICODE)), 0, 16);
$csrfToken = rex_csrf_token::factory('cb_contact_form_' . $formInstanceKey);

// Section Settings
$sectionBg = $elementData['section_bg'] ?? '';
$sectionBgImage = $elementData['section_bg_image'] ?? '';
$sectionPadding = $elementData['section_padding'] ?? '';
$containerWidth = $elementData['container_width'] ?? 'uk-container';
$sectionLight = !empty($elementData['section_light']);

// Im Backend: kein echtes Formular
$isBackend = rex::isBackend();

// Keine E-Mail-Adresse konfiguriert
if (empty($emailTo) && !$isBackend) {
    echo '<div class="uk-alert-warning" uk-alert><p>Kontaktformular: Keine Empfänger E-Mail konfiguriert.</p></div>';
    return;
}

/**
 * Helper: Optionen für Select/Radio laden
 */
if (!function_exists('parseFieldOptions')) {
    function parseFieldOptions(array $field): array
    {
        $options = [];
        $source = $field['field_options_source'] ?? 'manual';
        
        if ($source === 'sql' && !empty($field['field_options_sql'])) {
            try {
                $sql = rex_sql::factory();
                $sql->setQuery($field['field_options_sql']);
                while ($sql->hasNext()) {
                    $optValue = $sql->getValue('value');
                    $optLabel = $sql->getValue('label') ?? $optValue;
                    $options[(string)$optValue] = (string)$optLabel;
                    $sql->next();
                }
            } catch (Exception $e) {
                if (rex::isDebugMode()) {
                    $options['error'] = 'SQL-Fehler: ' . $e->getMessage();
                }
            }
        } elseif (!empty($field['field_options'])) {
            $lines = array_filter(array_map('trim', explode("\n", $field['field_options'])));
            foreach ($lines as $line) {
                if (strpos($line, '|') !== false) {
                    [$optValue, $optLabel] = explode('|', $line, 2);
                } else {
                    $optValue = $optLabel = $line;
            }
            $options[(string)$optValue] = (string)$optLabel;
        }
    }
    
    return $options;
    }
}

/**
 * Helper: Select-Feld rendern
 */
if (!function_exists('renderSelect')) {
    function renderSelect(string $inputName, array $options, string $inputValue, string $placeholder, bool $required, string $errorClass): string
    {
        $html = '<select class="uk-select ' . $errorClass . '" id="' . $inputName . '" name="' . $inputName . '"' . ($required ? ' required' : '') . '>';
        $html .= '<option value="">' . rex_escape($placeholder ?: 'Bitte wählen...') . '</option>';
        foreach ($options as $optValue => $optLabel) {
            $selected = (string)$inputValue === (string)$optValue ? ' selected' : '';
            $html .= '<option value="' . rex_escape($optValue) . '"' . $selected . '>' . rex_escape($optLabel) . '</option>';
        }
        $html .= '</select>';
        return $html;
    }
}

/**
 * Helper: Radio-Buttons rendern
 */
if (!function_exists('renderRadio')) {
    function renderRadio(string $inputName, array $options, string $inputValue, bool $required): string
    {
        $html = '';
        $idx = 0;
        foreach ($options as $optValue => $optLabel) {
            $checked = (string)$inputValue === (string)$optValue ? ' checked' : '';
            $req = ($required && $idx === 0) ? ' required' : '';
            $html .= '<label class="uk-margin-small-right"><input class="uk-radio" type="radio" name="' . $inputName . '" value="' . rex_escape($optValue) . '"' . $checked . $req . '> ' . rex_escape($optLabel) . '</label>';
            $idx++;
        }
        return $html;
    }
}

/**
 * Helper: Erweiterte Validierung durchführen
 * @param string $value Der zu validierende Wert
 * @param string $type Validierungstyp
 * @param string $param Parameter (Länge, Regex, Vergleichsausdruck)
 * @param array $formData Alle Formulardaten für Vergleiche
 * @return bool|string true wenn gültig, sonst Fehlermeldung
 */
if (!function_exists('validateField')) {
    function validateField(string $value, string $type, string $param, array $formData)
    {
        if (empty($value) || empty($type)) {
            return true;
        }
        
        // Vorgefertigte Regex-Muster
        $patterns = [
            'iban' => '/^[A-Z]{2}[0-9]{2}[A-Z0-9]{4,30}$/',
            'bic' => '/^[A-Z]{4}[A-Z]{2}[A-Z0-9]{2}([A-Z0-9]{3})?$/',
            'plz_de' => '/^[0-9]{5}$/',
            'plz_at' => '/^[0-9]{4}$/',
            'plz_ch' => '/^[0-9]{4}$/',
            'phone' => '/^[\+]?[0-9\s\-\/\(\)]{6,20}$/',
            'url' => '/^https?:\/\/[^\s]+$/',
            'date_de' => '/^(0[1-9]|[12][0-9]|3[01])\.(0[1-9]|1[0-2])\.[0-9]{4}$/',
            'date_iso' => '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/',
            'time' => '/^([01][0-9]|2[0-3]):[0-5][0-9]$/',
            'number' => '/^[0-9]+$/',
            'alpha' => '/^[a-zA-ZäöüÄÖÜß\s]+$/',
            'alphanumeric' => '/^[a-zA-Z0-9äöüÄÖÜß\s]+$/',
        ];
        
        $errorMessages = [
            'iban' => 'Ungültige IBAN (Format: DE89370400440532013000)',
            'bic' => 'Ungültiger BIC/SWIFT-Code',
            'plz_de' => 'Ungültige deutsche Postleitzahl (5 Ziffern)',
            'plz_at' => 'Ungültige österreichische Postleitzahl (4 Ziffern)',
            'plz_ch' => 'Ungültige Schweizer Postleitzahl (4 Ziffern)',
            'phone' => 'Ungültige Telefonnummer',
            'url' => 'Ungültige URL (muss mit http:// oder https:// beginnen)',
            'date_de' => 'Ungültiges Datum (Format: TT.MM.JJJJ)',
            'date_iso' => 'Ungültiges Datum (Format: JJJJ-MM-TT)',
            'time' => 'Ungültige Uhrzeit (Format: HH:MM)',
            'number' => 'Nur Zahlen erlaubt',
            'alpha' => 'Nur Buchstaben erlaubt',
            'alphanumeric' => 'Nur Buchstaben und Zahlen erlaubt',
        ];
        
        // IBAN: Leerzeichen entfernen und Großschreibung
        if ($type === 'iban') {
            $value = strtoupper(str_replace(' ', '', $value));
        }
        
        // BIC: Großschreibung
        if ($type === 'bic') {
            $value = strtoupper($value);
        }
        
        // Pattern-basierte Validierung
        if (isset($patterns[$type])) {
            if (!preg_match($patterns[$type], $value)) {
                return $errorMessages[$type];
            }
            return true;
        }
        
        // Mindestlänge
        if ($type === 'min_length') {
            $minLen = (int)$param;
            if (mb_strlen($value) < $minLen) {
                return "Mindestens {$minLen} Zeichen erforderlich";
            }
            return true;
        }
        
        // Maximallänge
        if ($type === 'max_length') {
            $maxLen = (int)$param;
            if (mb_strlen($value) > $maxLen) {
                return "Maximal {$maxLen} Zeichen erlaubt";
            }
            return true;
        }
        
        // Eigenes Regex-Muster
        if ($type === 'regex' && !empty($param)) {
            if (!preg_match('/' . $param . '/', $value)) {
                return 'Ungültiges Format';
            }
            return true;
        }
        
        // Wertevergleich: {{feldname}} < {{99000}} oder {{PLZ}} >= {{10000}}
        if ($type === 'compare' && !empty($param)) {
            if (!preg_match('/^\s*\{\{\s*([a-zA-Z0-9_]+)\s*\}\}\s*(<=|>=|==|!=|<|>)\s*\{\{\s*(.+?)\s*\}\}\s*$/', $param, $parts)) {
                return 'Ungültiges Vergleichsformat. Nutze {{feld}} < {{wert}}';
            }

            $leftRef = $parts[1];
            $operator = $parts[2];
            $rightRaw = $parts[3];

            $leftValue = (string) ($formData[$leftRef] ?? $value);
            $rightValue = '';

            if (preg_match('/^[a-zA-Z0-9_]+$/', $rightRaw) && array_key_exists($rightRaw, $formData)) {
                $rightValue = (string) $formData[$rightRaw];
            } else {
                $rightValue = (string) $rightRaw;
            }

            $leftIsNumeric = is_numeric($leftValue);
            $rightIsNumeric = is_numeric($rightValue);

            if ($leftIsNumeric && $rightIsNumeric) {
                $leftNumber = (float) $leftValue;
                $rightNumber = (float) $rightValue;

                return match ($operator) {
                    '<' => $leftNumber < $rightNumber,
                    '>' => $leftNumber > $rightNumber,
                    '<=' => $leftNumber <= $rightNumber,
                    '>=' => $leftNumber >= $rightNumber,
                    '==' => $leftNumber === $rightNumber,
                    '!=' => $leftNumber !== $rightNumber,
                    default => false,
                } ? true : 'Wert entspricht nicht den Vorgaben';
            }

            $leftText = mb_strtolower(trim($leftValue));
            $rightText = mb_strtolower(trim($rightValue));

            return match ($operator) {
                '==' => $leftText === $rightText,
                '!=' => $leftText !== $rightText,
                '<' => $leftText < $rightText,
                '>' => $leftText > $rightText,
                '<=' => $leftText <= $rightText,
                '>=' => $leftText >= $rightText,
                default => false,
            } ? true : 'Wert entspricht nicht den Vorgaben';
        }
        
        return true;
    }
}

// Formular verarbeiten (nur im Frontend)
$formSubmitted = false;
$formSuccess = false;
$formErrors = [];
$formData = [];

if (!$isBackend && rex_request::server('REQUEST_METHOD', 'string') === 'POST' && isset($_POST[$formId . '_submit'])) {
    $formSubmitted = true;

    // CSRF-Check
    if (!$csrfToken->isValid()) {
        $formErrors[] = 'Sicherheitsprüfung fehlgeschlagen. Bitte Formular erneut absenden.';
    }
    
    // Spam-Check: Honeypot
    if (in_array($spamProtection, ['honeypot', 'both'])) {
        if (!empty($_POST[$formId . '_website'])) {
            $formErrors[] = 'Spam erkannt.';
        }
    }
    
    // Spam-Check: Zeit
    if (in_array($spamProtection, ['time', 'both'])) {
        $timestamp = (int)($_POST[$formId . '_ts'] ?? 0);
        if (time() - $timestamp < 3) {
            $formErrors[] = 'Zu schnell abgesendet.';
        }
    }
    
    // Felder validieren
    foreach ($fields as $field) {
        $fieldName = $field['field_name'] ?? '';
        $fieldType = $field['field_type'] ?? 'text';
        $fieldRequired = !empty($field['field_required']);
        $fieldLabel = $field['field_label'] ?? $fieldName;
        $fieldValidationType = $field['field_validation_type'] ?? '';
        $fieldValidationParam = $field['field_validation_param'] ?? '';
        $fieldErrorMsg = $field['field_error_message'] ?? '';
        
        if (empty($fieldName) || in_array($fieldType, ['headline', 'fieldset', 'fieldset_end', 'divider'])) {
            continue;
        }
        
        $value = $_POST[$formId . '_' . $fieldName] ?? '';
        $formData[$fieldName] = $value;
        
        // Pflichtfeld
        if ($fieldRequired && empty($value)) {
            $formErrors[$fieldName] = $fieldErrorMsg ?: 'Das Feld "' . $fieldLabel . '" ist erforderlich.';
            continue;
        }
        
        // E-Mail Validierung (immer bei E-Mail-Feldern)
        if ($fieldType === 'email' && !empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $formErrors[$fieldName] = $fieldErrorMsg ?: 'Bitte geben Sie eine gültige E-Mail-Adresse ein.';
            continue;
        }
        
        // Erweiterte Validierung
        if (!empty($fieldValidationType) && !empty($value)) {
            $validationResult = validateField($value, $fieldValidationType, $fieldValidationParam, $formData);
            if ($validationResult !== true) {
                $formErrors[$fieldName] = $fieldErrorMsg ?: $validationResult;
            }
        }
    }
    
    // Datenschutz-Checkbox prüfen
    if ($privacyCheckbox && empty($_POST[$formId . '_privacy'])) {
        $formErrors['privacy'] = 'Bitte akzeptieren Sie die Datenschutzerklärung.';
    }
    
    // E-Mail senden wenn keine Fehler
    if (empty($formErrors)) {
        try {
            // E-Mail Body erstellen
            $body = '<table style="width: 100%; border-collapse: collapse;">';
            foreach ($fields as $field) {
                $fieldName = $field['field_name'] ?? '';
                $fieldLabel = $field['field_label'] ?? $fieldName;
                $fieldType = $field['field_type'] ?? 'text';
                
                if (empty($fieldName) || in_array($fieldType, ['headline', 'hidden', 'fieldset', 'fieldset_end', 'divider'])) {
                    continue;
                }
                
                $value = $formData[$fieldName] ?? '';
                
                // Bei Select/Radio: Label statt Wert anzeigen
                if (in_array($fieldType, ['select', 'radio']) && !empty($value)) {
                    $options = parseFieldOptions($field);
                    $value = $options[$value] ?? $value;
                }
                
                if ($fieldType === 'checkbox') {
                    $value = !empty($value) ? 'Ja' : 'Nein';
                }
                
                $body .= '<tr>';
                $body .= '<td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold; width: 30%;">' . rex_escape($fieldLabel) . '</td>';
                $body .= '<td style="padding: 8px; border-bottom: 1px solid #ddd;">' . nl2br(rex_escape($value)) . '</td>';
                $body .= '</tr>';
            }
            $body .= '</table>';
            
            // Absender E-Mail ermitteln
            $senderEmail = '';
            $senderName = '';
            foreach ($fields as $field) {
                $fieldType = $field['field_type'] ?? 'text';
                $fieldName = $field['field_name'] ?? '';
                
                if ($fieldType === 'email' && !empty($formData[$fieldName])) {
                    $senderEmail = $formData[$fieldName];
                }
                if ($fieldName === 'name' || str_contains($fieldName, 'name')) {
                    $senderName = $formData[$fieldName] ?? '';
                }
            }
            
            // Betreff mit Platzhaltern
            $subject = $emailSubject;
            $subject = str_replace('{name}', $senderName, $subject);
            $subject = str_replace('{email}', $senderEmail, $subject);
            $subject = str_replace('{subject}', $formData['subject'] ?? $formData['betreff'] ?? '', $subject);
            
            // PHPMailer verwenden
            $mail = new rex_mailer();
            $emailRecipients = preg_split('/\s*[,;]\s*/', (string) $emailTo, -1, PREG_SPLIT_NO_EMPTY) ?: [];
            $validRecipients = [];
            foreach ($emailRecipients as $recipient) {
                $recipient = trim($recipient);
                if ('' !== $recipient && filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                    $validRecipients[] = $recipient;
                }
            }

            if ([] === $validRecipients) {
                throw new RuntimeException('Keine gültige Empfängeradresse konfiguriert.');
            }

            foreach ($validRecipients as $recipient) {
                $mail->addAddress($recipient);
            }
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->isHTML(true);
            
            // Reply-To setzen, wenn eine Absender-Mail aus den Feldern ermittelt wurde
            if (!empty($senderEmail)) {
                $mail->addReplyTo($senderEmail, $senderName);
            }
            
            $mail->send();
            
            // Bestätigungs-E-Mail an Absender
            if ($sendCopy && !empty($senderEmail)) {
                $copyIntro = $elementData['copy_intro'] ?? "Vielen Dank für Ihre Nachricht!\n\nWir haben Ihre Anfrage erhalten und werden uns schnellstmöglich bei Ihnen melden.\n\nNachfolgend eine Kopie Ihrer Anfrage:";
                $copyFooter = $elementData['copy_footer'] ?? "Mit freundlichen Grüßen\nIhr Team";
                
                $copyBody = '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">';
                $copyBody .= '<div style="white-space: pre-line; margin-bottom: 20px;">' . rex_escape($copyIntro) . '</div>';
                $copyBody .= '<hr style="border: none; border-top: 1px solid #ddd; margin: 20px 0;">';
                $copyBody .= '<h3 style="margin-bottom: 15px;">Ihre Nachricht:</h3>';
                $copyBody .= $body;
                $copyBody .= '<hr style="border: none; border-top: 1px solid #ddd; margin: 20px 0;">';
                $copyBody .= '<div style="white-space: pre-line; color: #666;">' . rex_escape($copyFooter) . '</div>';
                $copyBody .= '</div>';
                
                $copyMail = new rex_mailer();
                $copyMail->addAddress($senderEmail);
                $copyMail->Subject = $copySubject;
                $copyMail->Body = $copyBody;
                $copyMail->isHTML(true);
                $copyMail->send();
            }
            
            $formSuccess = true;
            $formData = [];
            
        } catch (Exception $e) {
            $formErrors[] = $errorMessage;
            if (rex::isDebugMode()) {
                $formErrors[] = 'Debug: ' . $e->getMessage();
            }
        }
    }
}

// Section Wrapper
$hasSection = !empty($sectionBg) || !empty($sectionBgImage) || !empty($sectionPadding);
$sectionClasses = array_filter([$sectionBg, $sectionPadding]);
if ($sectionLight) $sectionClasses[] = 'uk-light';

if ($hasSection): ?>
<section class="<?= implode(' ', $sectionClasses) ?>"<?php if ($sectionBgImage): ?> style="background-image: url('<?= rex_url::media($sectionBgImage) ?>'); background-size: cover; background-position: center;"<?php endif; ?>>
<?php endif; ?>

<?php if ($containerWidth): ?>
<div class="<?= rex_escape($containerWidth) ?>">
<?php endif; ?>

<?php // Formular-Überschrift ?>
<?php if (!empty($formHeadline)): ?>
    <<?= $formHeadlineTag ?> class="uk-margin-bottom"><?= rex_escape($formHeadline) ?></<?= $formHeadlineTag ?>>
<?php endif; ?>

<?php // Einleitungstext ?>
<?php if (!empty($formIntro)): ?>
    <div class="uk-margin-bottom">
        <?= nl2br(rex_escape($formIntro)) ?>
    </div>
<?php endif; ?>

<div data-cb-contact-form-wrapper="1" data-cb-contact-form-key="<?= rex_escape($formInstanceKey) ?>" data-cb-ajax-enhancement="<?= (!$isBackend && $ajaxEnhancement) ? '1' : '0' ?>">
    <div class="uk-hidden" data-cb-form-live="1" aria-live="polite" aria-atomic="true"></div>

<?php if ($formSuccess): ?>
    <div class="uk-alert-success" uk-alert>
        <a class="uk-alert-close" uk-close></a>
        <p><?= nl2br(rex_escape($successMessage)) ?></p>
    </div>
<?php else: ?>

    <?php if ($formSubmitted && !empty($formErrors)): ?>
        <div class="uk-alert-danger" uk-alert>
            <a class="uk-alert-close" uk-close></a>
            <?php foreach ($formErrors as $error): ?>
                <p><?= rex_escape(is_string($error) ? $error : '') ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php
    // Form-Klasse je nach Layout
    $formClass = 'uk-form-stacked';
    if ($layout === 'horizontal') {
        $formClass = 'uk-form-horizontal';
    }
    
    $formTag = $isBackend ? 'div' : 'form';
    ?>
    <<?= $formTag ?> id="<?= $formId ?>"<?= !$isBackend ? ' method="post"' : '' ?> class="<?= $formClass ?>" data-cb-contact-form="1">
        
        <?php if ($isBackend): ?>
            <div class="uk-alert-primary" uk-alert>
                <p><strong>Vorschau:</strong> Das Formular ist im Backend deaktiviert.</p>
            </div>
        <?php endif; ?>

        <?php if (!$isBackend): ?>
            <?= $csrfToken->getHiddenField() ?>
        <?php endif; ?>
        
        <!-- Spam Protection -->
        <?php if (!$isBackend && in_array($spamProtection, ['honeypot', 'both'])): ?>
            <div style="position: absolute; left: -9999px;">
                <input type="text" name="<?= $formId ?>_website" value="" tabindex="-1" autocomplete="off">
            </div>
        <?php endif; ?>
        
        <?php if (!$isBackend && in_array($spamProtection, ['time', 'both'])): ?>
            <input type="hidden" name="<?= $formId ?>_ts" value="<?= time() ?>">
        <?php endif; ?>
        
        <div class="uk-grid-small<?= $layout === 'stacked' ? ' uk-grid-collapse' : '' ?>" uk-grid>
            <?php foreach ($fields as $field):
                $fieldType = $field['field_type'] ?? 'text';
                $fieldName = $field['field_name'] ?? '';
                $fieldLabel = $field['field_label'] ?? '';
                $fieldPlaceholder = $field['field_placeholder'] ?? '';
                $fieldRequired = !empty($field['field_required']);
                $fieldDefault = $field['field_default'] ?? '';
                $fieldWidth = $field['field_width'] ?? '1-1';
                $fieldAttributes = $field['field_attributes'] ?? '';
                
                // Optionen für Select/Radio laden
                $parsedOptions = parseFieldOptions($field);
                
                // Struktur-Elemente brauchen keinen Namen
                if (empty($fieldName) && !in_array($fieldType, ['headline', 'fieldset', 'fieldset_end', 'divider'])) continue;
                
                $inputName = $formId . '_' . $fieldName;
                $inputValue = $formData[$fieldName] ?? $fieldDefault;
                $hasError = isset($formErrors[$fieldName]);
                $errorClass = $hasError ? 'uk-form-danger' : '';
                
                // Zusätzliche Attribute
                $extraAttrs = $fieldAttributes ? ' ' . $fieldAttributes : '';
            ?>
                
                <?php if ($fieldType === 'fieldset'): ?>
                    </div><!-- Ende Grid für neues Fieldset -->
                    <fieldset class="uk-fieldset uk-margin-top">
                        <?php if (!empty($fieldLabel)): ?>
                            <legend class="uk-legend"><?= rex_escape($fieldLabel) ?></legend>
                        <?php endif; ?>
                        <div class="uk-grid-small" uk-grid>
                
                <?php elseif ($fieldType === 'fieldset_end'): ?>
                        </div><!-- Ende Grid im Fieldset -->
                    </fieldset>
                    <div class="uk-grid-small<?= $layout === 'stacked' ? ' uk-grid-collapse' : '' ?>" uk-grid>
                
                <?php elseif ($fieldType === 'headline'): ?>
                    <div class="uk-width-1-1">
                        <h4 class="uk-heading-line uk-margin-top"><span><?= rex_escape($fieldLabel) ?></span></h4>
                    </div>
                
                <?php elseif ($fieldType === 'divider'): ?>
                    <div class="uk-width-1-1">
                        <hr class="uk-divider-icon">
                    </div>
                
                <?php elseif ($fieldType === 'hidden'): ?>
                    <input type="hidden" name="<?= $inputName ?>" value="<?= rex_escape($fieldDefault) ?>">
                
                <?php elseif ($layout === 'horizontal'): ?>
                    <!-- Horizontal Layout -->
                    <div class="uk-width-<?= $fieldWidth ?>@s uk-margin">
                        <?php if ($fieldType !== 'checkbox'): ?>
                            <label class="uk-form-label" for="<?= $inputName ?>">
                                <?= rex_escape($fieldLabel) ?>
                                <?php if ($fieldRequired): ?><span class="uk-text-danger">*</span><?php endif; ?>
                            </label>
                        <?php endif; ?>
                        <div class="uk-form-controls">
                            <?php if ($fieldType === 'textarea'): ?>
                                <textarea class="uk-textarea <?= $errorClass ?>" id="<?= $inputName ?>" name="<?= $inputName ?>" rows="5" placeholder="<?= rex_escape($fieldPlaceholder) ?>"<?= $extraAttrs ?> <?= !$isBackend && $fieldRequired ? 'required' : '' ?>><?= rex_escape($inputValue) ?></textarea>
                            <?php elseif ($fieldType === 'select'): ?>
                                <select class="uk-select <?= $errorClass ?>" id="<?= $inputName ?>" name="<?= $inputName ?>"<?= $extraAttrs ?> <?= !$isBackend && $fieldRequired ? 'required' : '' ?>>
                                    <option value=""><?= rex_escape($fieldPlaceholder ?: 'Bitte wählen...') ?></option>
                                    <?php foreach ($parsedOptions as $optValue => $optLabel): ?>
                                        <option value="<?= rex_escape($optValue) ?>"<?= (string)$inputValue === (string)$optValue ? ' selected' : '' ?>><?= rex_escape($optLabel) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php elseif ($fieldType === 'radio'): ?>
                                <div>
                                    <?php foreach ($parsedOptions as $optValue => $optLabel): ?>
                                        <label class="uk-margin-small-right"><input class="uk-radio" type="radio" name="<?= $inputName ?>" value="<?= rex_escape($optValue) ?>"<?= (string)$inputValue === (string)$optValue ? ' checked' : '' ?><?= $extraAttrs ?> <?= !$isBackend && $fieldRequired ? 'required' : '' ?>> <?= rex_escape($optLabel) ?></label>
                                    <?php endforeach; ?>
                                </div>
                            <?php elseif ($fieldType === 'checkbox'): ?>
                                <label><input class="uk-checkbox" type="checkbox" name="<?= $inputName ?>" value="1" <?= !empty($inputValue) ? 'checked' : '' ?><?= $extraAttrs ?> <?= !$isBackend && $fieldRequired ? 'required' : '' ?>> <?= rex_escape($fieldLabel) ?><?php if ($fieldRequired): ?><span class="uk-text-danger">*</span><?php endif; ?></label>
                            <?php else: ?>
                                <input class="uk-input <?= $errorClass ?>" type="<?= $fieldType ?>" id="<?= $inputName ?>" name="<?= $inputName ?>" value="<?= rex_escape($inputValue) ?>" placeholder="<?= rex_escape($fieldPlaceholder) ?>"<?= $extraAttrs ?> <?= !$isBackend && $fieldRequired ? 'required' : '' ?>>
                            <?php endif; ?>
                            <?php if ($hasError): ?><span class="uk-text-danger uk-text-small"><?= rex_escape($formErrors[$fieldName]) ?></span><?php endif; ?>
                        </div>
                    </div>
                
                <?php elseif ($layout === 'floating'): ?>
                    <!-- Floating Labels -->
                    <div class="uk-width-<?= $fieldWidth ?>@s uk-margin">
                        <?php if ($fieldType === 'textarea'): ?>
                            <textarea class="uk-textarea <?= $errorClass ?>" id="<?= $inputName ?>" name="<?= $inputName ?>" rows="5" placeholder="<?= rex_escape($fieldLabel . ($fieldRequired ? ' *' : '')) ?>"<?= $extraAttrs ?> <?= !$isBackend && $fieldRequired ? 'required' : '' ?>><?= rex_escape($inputValue) ?></textarea>
                        <?php elseif ($fieldType === 'select'): ?>
                            <select class="uk-select <?= $errorClass ?>" id="<?= $inputName ?>" name="<?= $inputName ?>"<?= $extraAttrs ?> <?= !$isBackend && $fieldRequired ? 'required' : '' ?>>
                                <option value=""><?= rex_escape($fieldLabel . ($fieldRequired ? ' *' : '')) ?></option>
                                <?php foreach ($parsedOptions as $optValue => $optLabel): ?>
                                    <option value="<?= rex_escape($optValue) ?>"<?= (string)$inputValue === (string)$optValue ? ' selected' : '' ?>><?= rex_escape($optLabel) ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php elseif ($fieldType === 'checkbox'): ?>
                            <label><input class="uk-checkbox" type="checkbox" name="<?= $inputName ?>" value="1" <?= !empty($inputValue) ? 'checked' : '' ?><?= $extraAttrs ?> <?= !$isBackend && $fieldRequired ? 'required' : '' ?>> <?= rex_escape($fieldLabel) ?><?php if ($fieldRequired): ?><span class="uk-text-danger">*</span><?php endif; ?></label>
                        <?php elseif ($fieldType === 'radio'): ?>
                            <div class="uk-margin-small-bottom uk-text-muted"><?= rex_escape($fieldLabel) ?><?php if ($fieldRequired): ?><span class="uk-text-danger">*</span><?php endif; ?></div>
                            <?php foreach ($parsedOptions as $optValue => $optLabel): ?>
                                <label class="uk-margin-small-right"><input class="uk-radio" type="radio" name="<?= $inputName ?>" value="<?= rex_escape($optValue) ?>"<?= (string)$inputValue === (string)$optValue ? ' checked' : '' ?><?= $extraAttrs ?> <?= !$isBackend && $fieldRequired ? 'required' : '' ?>> <?= rex_escape($optLabel) ?></label>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <input class="uk-input <?= $errorClass ?>" type="<?= $fieldType ?>" id="<?= $inputName ?>" name="<?= $inputName ?>" value="<?= rex_escape($inputValue) ?>" placeholder="<?= rex_escape($fieldLabel . ($fieldRequired ? ' *' : '')) ?>"<?= $extraAttrs ?> <?= !$isBackend && $fieldRequired ? 'required' : '' ?>>
                        <?php endif; ?>
                        <?php if ($hasError): ?><span class="uk-text-danger uk-text-small"><?= rex_escape($formErrors[$fieldName]) ?></span><?php endif; ?>
                    </div>
                
                <?php else: ?>
                    <!-- Standard/Stacked Layout -->
                    <div class="uk-width-<?= $fieldWidth ?>@s uk-margin<?= $layout === 'stacked' ? '-small' : '' ?>">
                        <?php if ($fieldType !== 'checkbox'): ?>
                            <label class="uk-form-label" for="<?= $inputName ?>">
                                <?= rex_escape($fieldLabel) ?>
                                <?php if ($fieldRequired): ?><span class="uk-text-danger">*</span><?php endif; ?>
                            </label>
                        <?php endif; ?>
                        <div class="uk-form-controls">
                            <?php if ($fieldType === 'textarea'): ?>
                                <textarea class="uk-textarea <?= $errorClass ?>" id="<?= $inputName ?>" name="<?= $inputName ?>" rows="5" placeholder="<?= rex_escape($fieldPlaceholder) ?>"<?= $extraAttrs ?> <?= !$isBackend && $fieldRequired ? 'required' : '' ?>><?= rex_escape($inputValue) ?></textarea>
                            <?php elseif ($fieldType === 'select'): ?>
                                <select class="uk-select <?= $errorClass ?>" id="<?= $inputName ?>" name="<?= $inputName ?>"<?= $extraAttrs ?> <?= !$isBackend && $fieldRequired ? 'required' : '' ?>>
                                    <option value=""><?= rex_escape($fieldPlaceholder ?: 'Bitte wählen...') ?></option>
                                    <?php foreach ($parsedOptions as $optValue => $optLabel): ?>
                                        <option value="<?= rex_escape($optValue) ?>"<?= (string)$inputValue === (string)$optValue ? ' selected' : '' ?>><?= rex_escape($optLabel) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php elseif ($fieldType === 'radio'): ?>
                                <div>
                                    <?php foreach ($parsedOptions as $optValue => $optLabel): ?>
                                        <label class="uk-margin-small-right"><input class="uk-radio" type="radio" name="<?= $inputName ?>" value="<?= rex_escape($optValue) ?>"<?= (string)$inputValue === (string)$optValue ? ' checked' : '' ?><?= $extraAttrs ?> <?= !$isBackend && $fieldRequired ? 'required' : '' ?>> <?= rex_escape($optLabel) ?></label>
                                    <?php endforeach; ?>
                                </div>
                            <?php elseif ($fieldType === 'checkbox'): ?>
                                <label><input class="uk-checkbox" type="checkbox" name="<?= $inputName ?>" value="1" <?= !empty($inputValue) ? 'checked' : '' ?><?= $extraAttrs ?> <?= !$isBackend && $fieldRequired ? 'required' : '' ?>> <?= rex_escape($fieldLabel) ?><?php if ($fieldRequired): ?><span class="uk-text-danger">*</span><?php endif; ?></label>
                            <?php else: ?>
                                <input class="uk-input <?= $errorClass ?>" type="<?= $fieldType ?>" id="<?= $inputName ?>" name="<?= $inputName ?>" value="<?= rex_escape($inputValue) ?>" placeholder="<?= rex_escape($fieldPlaceholder) ?>"<?= $extraAttrs ?> <?= !$isBackend && $fieldRequired ? 'required' : '' ?>>
                            <?php endif; ?>
                            <?php if ($hasError): ?><span class="uk-text-danger uk-text-small"><?= rex_escape($formErrors[$fieldName]) ?></span><?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
            
            <?php if ($privacyCheckbox): ?>
                <div class="uk-width-1-1 uk-margin">
                    <label>
                        <input class="uk-checkbox" type="checkbox" name="<?= $formId ?>_privacy" value="1" <?= !$isBackend ? 'required' : '' ?>>
                        <?php 
                        $privacyTextOutput = $privacyText;
                        if ($privacyLink && strpos($privacyTextOutput, '{link}') !== false) {
                            $article = rex_article::get($privacyLink);
                            if ($article) {
                                $linkHtml = '<a href="' . $article->getUrl() . '" target="_blank">' . $article->getName() . '</a>';
                                $privacyTextOutput = str_replace('{link}', $linkHtml, $privacyTextOutput);
                            }
                        }
                        echo $privacyTextOutput;
                        ?>
                        <span class="uk-text-danger">*</span>
                    </label>
                    <?php if (isset($formErrors['privacy'])): ?>
                        <div class="uk-text-danger uk-text-small"><?= rex_escape($formErrors['privacy']) ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <div class="uk-width-1-1 uk-margin-top">
                <button type="<?= $isBackend ? 'button' : 'submit' ?>" name="<?= $formId ?>_submit" value="1" class="uk-button uk-button-<?= $submitStyle ?>"<?= $isBackend ? ' disabled' : '' ?>>
                    <?= rex_escape($submitText) ?>
                </button>
            </div>
        </div>
    </<?= $formTag ?>>
<?php endif; ?>

</div>

<?php if ($containerWidth): ?>
</div>
<?php endif; ?>

<?php if ($hasSection): ?>
</section>
<?php endif; ?>

<?php
if (!$isBackend && $ajaxEnhancement && !defined('YFORM_CB_CONTACT_FORM_AJAX_JS_INCLUDED')) {
    define('YFORM_CB_CONTACT_FORM_AJAX_JS_INCLUDED', true);
    ?>
    <script src="<?= rex_escape(rex_url::addonAssets('yform_content_builder', 'contact_form/contact-form-ajax.js')) ?>"></script>
    <?php
}
?>
