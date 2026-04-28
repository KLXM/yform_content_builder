<?php
/**
 * DoForm2026 - Template
 * Verwendet die bewaehrte contact_form-Renderlogik.
 *
 * @var array $elementData
 */

if (!isset($elementData['form_headline']) || trim((string) $elementData['form_headline']) === '') {
    $elementData['form_headline'] = 'DoForm2026';
}

if (!isset($elementData['submit_text']) || trim((string) $elementData['submit_text']) === '') {
    $elementData['submit_text'] = 'Absenden';
}

include rex_path::addon('yform_content_builder', 'elements/contact_form/templates/uikit.php');
