<?php

/**
 * Hero Banner Element - Bootstrap Template
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
$btn1Style    = $elementData['btn1_style'] ?? 'uk-button-primary';
$btn2Text     = $elementData['btn2_text'] ?? '';
$btn2Url      = $elementData['btn2_url'] ?? '';
$image        = $elementData['image'] ?? '';
$imageAlt     = $elementData['image_alt'] ?? '';
$height       = $elementData['height'] ?? 'large';
$contentAlign = $elementData['content_align'] ?? 'left';
$overlay      = $elementData['overlay'] ?? 'dark';
$textColor    = $elementData['text_color'] ?? 'light';

if (empty($heading) && empty($image)) {
    return;
}

$btn1Link = '';
if ($btn1Type === 'external' && $btn1Url) {
    $btn1Link = $btn1Url;
} elseif ($btn1Type === 'internal' && $btn1Internal) {
    $btn1Link = rex_getUrl((int) $btn1Internal);
}

$heightPx = match ($height) {
    'small'  => '300px',
    'medium' => '450px',
    'large'  => '600px',
    default  => '600px',
};

$bgImageUrl = $image ? rex_media_manager::getUrl('content_slideshow', $image) : '';

$overlayColor = match ($overlay) {
    'dark'       => 'rgba(0,0,0,.4)',
    'dark-heavy' => 'rgba(0,0,0,.65)',
    'light'      => 'rgba(255,255,255,.4)',
    default      => 'transparent',
};

$textColorStyle = $textColor === 'light' ? 'color: #fff;' : '';

$btnClass = match ($btn1Style) {
    'uk-button-primary'   => 'btn btn-primary',
    'uk-button-secondary' => 'btn btn-secondary',
    'uk-button-danger'    => 'btn btn-danger',
    'uk-button-text'      => 'btn btn-link text-white',
    default               => 'btn btn-light',
};

$alignClass = match ($contentAlign) {
    'center' => 'text-center justify-content-center',
    'right'  => 'text-end justify-content-end',
    default  => '',
};

?>
<div class="position-relative overflow-hidden"
     style="min-height: <?= $heightPx ?>; background: <?= $bgImageUrl ? 'url(' . rex_escape($bgImageUrl) . ') center/cover no-repeat' : '#333' ?>;">
    <div class="position-absolute top-0 start-0 w-100 h-100"
         style="background: <?= $overlayColor ?>;"></div>
    <div class="position-relative h-100 d-flex align-items-center" style="min-height: <?= $heightPx ?>;">
        <div class="container">
            <div class="row <?= $alignClass ?>">
                <div class="col-lg-8" style="<?= $textColorStyle ?>">
                    <?php if ($badge): ?>
                        <span class="badge bg-primary mb-2"><?= rex_escape($badge) ?></span>
                    <?php endif; ?>
                    <?php if ($heading): ?>
                        <<?= $tag ?> class="mb-3"><?= rex_escape($heading) ?></<?= $tag ?>>
                    <?php endif; ?>
                    <?php if ($subheading): ?>
                        <p class="lead mb-2"><?= rex_escape($subheading) ?></p>
                    <?php endif; ?>
                    <?php if ($text): ?>
                        <p><?= nl2br(rex_escape($text)) ?></p>
                    <?php endif; ?>
                    <?php if ($btn1Link && $btn1Text): ?>
                        <div class="mt-4">
                            <a href="<?= rex_escape($btn1Link) ?>" class="<?= $btnClass ?> me-2">
                                <?= rex_escape($btn1Text) ?>
                            </a>
                            <?php if ($btn2Url && $btn2Text): ?>
                                <a href="<?= rex_escape($btn2Url) ?>" class="btn btn-outline-light">
                                    <?= rex_escape($btn2Text) ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
