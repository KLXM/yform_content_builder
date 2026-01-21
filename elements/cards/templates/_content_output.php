<?php
/**
 * Content Output Helper für Cards
 * Variablen werden vom Parent-Template bereitgestellt:
 * $title, $subtitle, $text, $badge, $badgeColor, $href, $linkText, $linkCard, $isTransparent, $item
 */

// Bei transparent Cards: uk-padding-remove für Header/Body/Footer
$transparentPadding = isset($isTransparent) && $isTransparent ? ' uk-padding-remove' : '';

// Standard-Feldnamen die NICHT als Extra-Felder zählen
$standardFields = [
    'layout', 'image', 'image_alt', 'image_decorative', 'image_title', 'media_width', 'media_ratio',
    'media_lightbox', 'media_cover', 'video_display', 'video_controls',
    'title', 'text', 'subtitle', 'badge', 'badge_color',
    'card_width', 'card_style_override', 'card_shadow_override',
    'link_type', 'link_url', 'link_internal', 'link_text', 'link_card',
    'animation'
];

// Extra-Felder erkennen (alles was nicht in Standard-Feldliste)
$extraFields = [];
if (is_array($item)) {
    foreach ($item as $key => $value) {
        if (!in_array($key, $standardFields) && !empty($value)) {
            $extraFields[$key] = $value;
        }
    }
}

// Extra-Felder in HTML konvertieren wenn GetOutput vorhanden
$extraFieldsHtml = '';
if (!empty($extraFields)) {
    // Versuche die GetOutput Methode von CardsRepeaterExtra zu nutzen
    if (class_exists('CardsRepeaterExtra') && method_exists('CardsRepeaterExtra', 'GetOutput')) {
        $extraFieldsHtml = CardsRepeaterExtra::GetOutput($item);
    } else {
        // Fallback: Einfache Ausgabe der rohen Werte
        foreach ($extraFields as $fieldKey => $fieldValue) {
            $extraFieldsHtml .= '<div class="extra-field extra-field-' . rex_escape($fieldKey) . '">';
            $extraFieldsHtml .= $fieldValue;
            $extraFieldsHtml .= '</div>';
        }
    }
}
?>

<?php if (!empty($badge)): ?>
    <div class="uk-card-badge uk-label uk-label-<?= rex_escape($badgeColor) ?>"><?= rex_escape($badge) ?></div>
<?php endif; ?>

<?php if (!empty($title) || !empty($subtitle)): ?>
    <div class="uk-card-header<?= $transparentPadding ?>">
        <?php if (!empty($title)): ?>
            <h3 class="uk-card-title<?= $linkCard ? ' uk-link-heading' : '' ?>"><?= rex_escape($title) ?></h3>
        <?php endif; ?>
        <?php if (!empty($subtitle)): ?>
            <p class="uk-text-meta uk-margin-remove-top"><?= rex_escape($subtitle) ?></p>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if (!empty($extraFields)): ?>
    <div>
        <?php if (!empty($extraFieldsHtml)): ?>
            <?= $extraFieldsHtml ?>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if (!empty($text)): ?>
    <div class="uk-card-body<?= $matchHeight ? ' uk-flex-1' : '' ?><?= $transparentPadding ?>">
        <div class="uk-text"><?= $text ?></div>
    </div>
<?php endif; ?>


