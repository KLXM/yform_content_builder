<?php
/**
 * Content Output Helper für Cards
 * Variablen werden vom Parent-Template bereitgestellt:
 * $title, $subtitle, $text, $badge, $badgeColor, $href, $linkText, $linkCard, $isTransparent
 */

// Bei transparent Cards: uk-padding-remove für Header/Body/Footer
$transparentPadding = isset($isTransparent) && $isTransparent ? ' uk-padding-remove' : '';
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

<?php if (!empty($text)): ?>
    <div class="uk-card-body<?= $matchHeight ? ' uk-flex-1' : '' ?><?= $transparentPadding ?>">
        <div class="uk-text"><?= $text ?></div>
    </div>
<?php endif; ?>
