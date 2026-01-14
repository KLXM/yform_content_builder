<?php
/**
 * Plain HTML Template für Text & Bild Element
 * @var array $elementData
 */

$headline = $elementData['headline'] ?? '';
$headlineTag = $elementData['headline_tag'] ?? 'h2';
$text = $elementData['text'] ?? '';
$image = $elementData['image'] ?? '';
$imageAlt = $elementData['image_alt'] ?? $headline;
$layout = $elementData['layout'] ?? 'image_text';

// Section-Einstellungen
$sectionBg = $elementData['section_bg'] ?? '';
$sectionBgImage = $elementData['section_bg_image'] ?? '';
$sectionPadding = $elementData['section_padding'] ?? '';

// Link
$linkType = $elementData['link_type'] ?? '';
$linkUrl = $elementData['link_url'] ?? '';
$linkInternal = $elementData['link_internal'] ?? '';
$linkText = $elementData['link_text'] ?? 'Mehr erfahren';
$linkTarget = ($elementData['link_target'] ?? '_self') === '_blank' ? ' target="_blank" rel="noopener"' : '';

// Link URL ermitteln
$href = '';
if ($linkType === 'external' && $linkUrl) {
    $href = $linkUrl;
} elseif ($linkType === 'internal' && $linkInternal) {
    $href = rex_getUrl($linkInternal);
}

// Layout
$imageFirst = in_array($layout, ['image_text', 'image_top']);
$isVertical = in_array($layout, ['image_top', 'text_top']);

// Section Styles
$sectionStyle = '';
if (!empty($sectionBgImage)) {
    $ext = strtolower(pathinfo($sectionBgImage, PATHINFO_EXTENSION));
    if (!in_array($ext, ['mp4', 'webm', 'ogg'])) {
        $bgImageUrl = rex_media_manager::getUrl('content_slideshow', $sectionBgImage);
        $sectionStyle = ' style="background-image: url(\'' . $bgImageUrl . '\'); background-size: cover; background-position: center;"';
    }
}

$hasSection = $sectionBg || $sectionPadding || !empty($sectionBgImage);
?>

<?php if ($hasSection): ?>
<section class="text-image-section <?= rex_escape($sectionBg) ?> <?= rex_escape($sectionPadding) ?>"<?= $sectionStyle ?>>
<?php endif; ?>

<div class="text-image-element text-image-<?= $isVertical ? 'vertical' : 'horizontal' ?>">
    <?php if ($imageFirst): ?>
        <?php if ($image): ?>
            <div class="image-wrapper">
                <img src="<?= rex_media_manager::getUrl('content_text_image', $image) ?>" alt="<?= rex_escape($imageAlt) ?>">
            </div>
        <?php endif; ?>
        <div class="text-wrapper">
            <?php if ($headline): ?>
                <<?= $headlineTag ?>><?= rex_escape($headline) ?></<?= $headlineTag ?>>
            <?php endif; ?>
            <?php if ($text): ?>
                <div class="text-content"><?= $text ?></div>
            <?php endif; ?>
            <?php if ($href && $linkText): ?>
                <p><a href="<?= $href ?>"<?= $linkTarget ?>><?= rex_escape($linkText) ?></a></p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="text-wrapper">
            <?php if ($headline): ?>
                <<?= $headlineTag ?>><?= rex_escape($headline) ?></<?= $headlineTag ?>>
            <?php endif; ?>
            <?php if ($text): ?>
                <div class="text-content"><?= $text ?></div>
            <?php endif; ?>
            <?php if ($href && $linkText): ?>
                <p><a href="<?= $href ?>"<?= $linkTarget ?>><?= rex_escape($linkText) ?></a></p>
            <?php endif; ?>
        </div>
        <?php if ($image): ?>
            <div class="image-wrapper">
                <img src="<?= rex_media_manager::getUrl('content_text_image', $image) ?>" alt="<?= rex_escape($imageAlt) ?>">
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php if ($hasSection): ?>
</section>
<?php endif; ?>
