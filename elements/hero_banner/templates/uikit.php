<?php

/**
 * Hero Banner Element - UIkit Template
 *
 * @var array $elementData
 */

// --- Inhalt ---
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

// --- Medien ---
$image    = $elementData['image'] ?? '';
$imageAlt = $elementData['image_alt'] ?? '';
$video    = $elementData['video'] ?? '';

// --- Design ---
$height          = $elementData['height'] ?? 'large';
$contentAlign    = $elementData['content_align'] ?? 'left';
$contentVAlign   = $elementData['content_valign'] ?? 'middle';
$overlay         = $elementData['overlay'] ?? 'dark';
$textColor       = $elementData['text_color'] ?? 'light';
$parallaxBg      = !empty($elementData['parallax_bg']);
$parallaxVelocity = (int) ($elementData['parallax_bg_velocity'] ?? 300);
$parallaxContent = !empty($elementData['parallax_content']);

// --- Sektion ---
$sectionBg    = $elementData['section_bg'] ?? '';
$sectionPad   = $elementData['section_padding'] ?? '';
$container    = $elementData['container_width'] ?? 'uk-container';
$sectionLight = !empty($elementData['section_light']);

if (empty($heading) && empty($image)) {
    return;
}

// Button 1 Link
$btn1Link = '';
if ($btn1Type === 'external' && $btn1Url) {
    $btn1Link = $btn1Url;
} elseif ($btn1Type === 'internal' && $btn1Internal) {
    $btn1Link = rex_getUrl((int) $btn1Internal);
}

// Höhen-Klasse
$heightClass = match ($height) {
    'small'        => 'uk-height-small',
    'medium'       => 'uk-height-medium',
    'viewport'     => 'uk-height-viewport',
    'viewport-2-3' => 'uk-height-viewport uk-height-2-3',
    default        => 'cb-hero-height-large',
};

// Inhalt horizontal ausrichten
$hAlignClass = match ($contentAlign) {
    'center' => ' uk-text-center',
    'right'  => ' uk-text-right',
    default  => '',
};

// Inhalt vertikal
$vAlignClass = match ($contentVAlign) {
    'top'    => 'uk-flex-top uk-flex-first',
    'bottom' => 'uk-flex-bottom uk-flex-last',
    default  => 'uk-flex-middle',
};

// Overlay
$overlayStyle = match ($overlay) {
    'dark'       => 'background: rgba(0,0,0,.4);',
    'dark-heavy' => 'background: rgba(0,0,0,.65);',
    'light'      => 'background: rgba(255,255,255,.4);',
    default      => '',
};

// uk-light Klasse
$lightClass = $textColor === 'light' ? ' uk-light' : '';

// Hintergrundbild-URL
$bgImageUrl = '';
if ($image && !$video) {
    $bgImageUrl = rex_media_manager::getUrl('content_slideshow', $image);
}

$resolvedImageAlt = \KLXM\YFormContentBuilder\MediaAltResolver::resolve((string) $image, (string) $imageAlt, (string) $heading);

// Parallax-Attribute
$parallaxBgAttr = '';
if ($parallaxBg && $image && !$video) {
    // bgy: negativ = Bild scrollt langsamer nach oben → klassischer Parallax-Effekt
    $parallaxBgAttr = ' uk-parallax="bgy: -' . $parallaxVelocity . '"';
}

$parallaxContentAttr = '';
if ($parallaxContent) {
    // y: negativ = Content hebt sich beim Scrollen leicht ab
    $parallaxContentAttr = ' uk-parallax="y: -60; easing: 1"';
}

?>

<div class="uk-cover-container <?= $heightClass ?><?= $lightClass ?>">

    <?php if ($video): ?>
        <video src="<?= rex_escape(rex_url::media($video)) ?>"
               autoplay muted loop playsinline uk-cover
               aria-hidden="true"></video>
    <?php elseif ($image): ?>
        <img src="<?= rex_escape($bgImageUrl) ?>"
             alt="<?= rex_escape($resolvedImageAlt) ?>"
             uk-cover loading="eager"<?= $parallaxBgAttr ?>>
    <?php endif; ?>

    <?php if ($overlayStyle): ?>
        <div class="uk-position-cover" style="<?= $overlayStyle ?>"></div>
    <?php endif; ?>

    <div class="uk-position-cover uk-flex <?= $vAlignClass ?>">
        <div class="<?= rex_escape($container ?: 'uk-container') ?> uk-width-1-1<?= $hAlignClass ?>"<?= $parallaxContentAttr ?>>

            <?php if ($badge): ?>
                <span class="uk-badge uk-margin-small-bottom"><?= rex_escape($badge) ?></span>
            <?php endif; ?>

            <?php if ($heading): ?>
                <<?= $tag ?> class="uk-margin-small-top uk-margin-small-bottom">
                    <?= rex_escape($heading) ?>
                </<?= $tag ?>>
            <?php endif; ?>

            <?php if ($subheading): ?>
                <p class="uk-text-lead uk-margin-small-bottom uk-margin-remove-top">
                    <?= rex_escape($subheading) ?>
                </p>
            <?php endif; ?>

            <?php if ($text): ?>
                <p class="uk-margin-small-bottom"><?= nl2br(rex_escape($text)) ?></p>
            <?php endif; ?>

            <?php if ($btn1Link && $btn1Text): ?>
                <div class="uk-margin-top">
                    <a href="<?= rex_escape($btn1Link) ?>" class="uk-button <?= rex_escape($btn1Style) ?>">
                        <?= rex_escape($btn1Text) ?>
                    </a>
                    <?php if ($btn2Url && $btn2Text): ?>
                        <a href="<?= rex_escape($btn2Url) ?>"
                           class="uk-button uk-button-default uk-margin-small-left">
                            <?= rex_escape($btn2Text) ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>

</div>

<style>
.cb-hero-height-large { min-height: 500px; }
@media (min-width: 960px) { .cb-hero-height-large { min-height: 650px; } }
</style>
