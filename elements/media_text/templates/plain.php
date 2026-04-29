<?php

/**
 * Bild & Text Element - Plain Template (kein CSS-Framework)
 *
 * @var array $elementData
 */

$badge         = $elementData['badge'] ?? '';
$heading       = $elementData['heading'] ?? '';
$tag           = $elementData['tag'] ?? 'h2';
$subheading    = $elementData['subheading'] ?? '';
$text          = $elementData['text'] ?? '';
$image         = $elementData['image'] ?? '';
$imageAlt      = $elementData['image_alt'] ?? '';
$imageRatio    = $elementData['image_ratio'] ?? '';
$mediaPosition = $elementData['media_position'] ?? 'left';
$linkType      = $elementData['link_type'] ?? '';
$linkUrl       = $elementData['link_url'] ?? '';
$linkInternal  = $elementData['link_internal'] ?? '';
$linkText      = $elementData['link_text'] ?? 'Mehr erfahren';

if (empty($image) && empty($heading) && empty($text)) {
    return;
}

$finalLink = '';
if ($linkType === 'external' && $linkUrl) {
    $finalLink = $linkUrl;
} elseif ($linkType === 'internal' && $linkInternal) {
    $finalLink = rex_getUrl((int) $linkInternal);
}

$imageUrl = $image ? ($imageRatio ? rex_media_manager::getUrl('card_' . $imageRatio . '_w1200', $image) : rex_url::media($image)) : '';
$resolvedImageAlt = \KLXM\YFormContentBuilder\MediaAltResolver::resolve((string) $image, (string) $imageAlt, (string) $heading);

?>
<div class="cb-media-text cb-media-text--<?= rex_escape($mediaPosition) ?>">
    <?php if ($mediaPosition === 'left' && $image && $imageUrl): ?>
        <div class="cb-media-text__image">
            <img src="<?= rex_escape($imageUrl) ?>" alt="<?= rex_escape($resolvedImageAlt) ?>" loading="lazy">
        </div>
    <?php endif; ?>

    <div class="cb-media-text__content">
        <?php if ($badge): ?>
            <span class="cb-badge"><?= rex_escape($badge) ?></span>
        <?php endif; ?>
        <?php if ($heading): ?>
            <<?= $tag ?>><?= rex_escape($heading) ?></<?= $tag ?>>
        <?php endif; ?>
        <?php if ($subheading): ?>
            <p class="cb-subheading"><?= rex_escape($subheading) ?></p>
        <?php endif; ?>
        <?php if ($text): ?>
            <div class="cb-text"><?= $text ?></div>
        <?php endif; ?>
        <?php if ($finalLink && $linkText): ?>
            <a href="<?= rex_escape($finalLink) ?>" class="cb-button"><?= rex_escape($linkText) ?></a>
        <?php endif; ?>
    </div>

    <?php if ($mediaPosition === 'right' && $image && $imageUrl): ?>
        <div class="cb-media-text__image">
            <img src="<?= rex_escape($imageUrl) ?>" alt="<?= rex_escape($resolvedImageAlt) ?>" loading="lazy">
        </div>
    <?php endif; ?>
</div>
