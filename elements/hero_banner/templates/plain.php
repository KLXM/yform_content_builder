<?php

/**
 * Hero Banner Element - Plain Template
 *
 * @var array $elementData
 */

$badge        = $elementData['badge'] ?? '';
$heading      = $elementData['heading'] ?? '';
$tag          = $elementData['tag'] ?? 'h1';
$subheading   = $elementData['subheading'] ?? '';
$text         = $elementData['text'] ?? '';
$btn1Text     = $elementData['btn1_text'] ?? '';
$btn1Type     = $elementData['btn1_link_type'] ?? '';
$btn1Url      = $elementData['btn1_url'] ?? '';
$btn1Internal = $elementData['btn1_internal'] ?? '';
$btn2Text     = $elementData['btn2_text'] ?? '';
$btn2Url      = $elementData['btn2_url'] ?? '';
$image        = $elementData['image'] ?? '';
$imageAlt     = $elementData['image_alt'] ?? '';

if (empty($heading) && empty($image)) {
    return;
}

$btn1Link = '';
if ($btn1Type === 'external' && $btn1Url) {
    $btn1Link = $btn1Url;
} elseif ($btn1Type === 'internal' && $btn1Internal) {
    $btn1Link = rex_getUrl((int) $btn1Internal);
}

$bgImageUrl = $image ? rex_media_manager::getUrl('content_slideshow', $image) : '';

?>
<div class="cb-hero" style="<?= $bgImageUrl ? 'background-image: url(' . rex_escape($bgImageUrl) . '); background-size: cover; background-position: center;' : '' ?>">
    <div class="cb-hero__overlay"></div>
    <div class="cb-hero__content">
        <?php if ($badge): ?>
            <span class="cb-badge"><?= rex_escape($badge) ?></span>
        <?php endif; ?>
        <?php if ($heading): ?>
            <<?= $tag ?>><?= rex_escape($heading) ?></<?= $tag ?>>
        <?php endif; ?>
        <?php if ($subheading): ?>
            <p class="cb-hero__sub"><?= rex_escape($subheading) ?></p>
        <?php endif; ?>
        <?php if ($text): ?>
            <p><?= nl2br(rex_escape($text)) ?></p>
        <?php endif; ?>
        <?php if ($btn1Link && $btn1Text): ?>
            <div class="cb-hero__buttons">
                <a href="<?= rex_escape($btn1Link) ?>" class="cb-button"><?= rex_escape($btn1Text) ?></a>
                <?php if ($btn2Url && $btn2Text): ?>
                    <a href="<?= rex_escape($btn2Url) ?>" class="cb-button cb-button--outline">
                        <?= rex_escape($btn2Text) ?>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
