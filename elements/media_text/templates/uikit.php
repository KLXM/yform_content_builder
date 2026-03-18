<?php

/**
 * Bild & Text Element - UIkit Template
 *
 * @var array $elementData
 */

// --- Inhalt ---
$badge      = $elementData['badge'] ?? '';
$heading    = $elementData['heading'] ?? '';
$tag        = $elementData['tag'] ?? 'h2';
$subheading = $elementData['subheading'] ?? '';
$text       = $elementData['text'] ?? '';

// --- Bild ---
$image      = $elementData['image'] ?? '';
$imageAlt   = $elementData['image_alt'] ?? '';
$imageRatio = $elementData['image_ratio'] ?? '';

// --- Design ---
$mediaPosition = $elementData['media_position'] ?? 'left';
$imageWidth    = $elementData['image_width'] ?? '1-2';
$verticalAlign = $elementData['vertical_align'] ?? 'middle';
$imageRounded  = !empty($elementData['image_rounded']);
$imageShadow   = $elementData['image_shadow'] ?? '';
$imageStyle    = $elementData['image_style'] ?? '';

// --- Link ---
$linkType     = $elementData['link_type'] ?? '';
$linkUrl      = $elementData['link_url'] ?? '';
$linkInternal = $elementData['link_internal'] ?? '';
$linkText     = $elementData['link_text'] ?? 'Mehr erfahren';
$linkStyle    = $elementData['link_style'] ?? 'uk-button-default';

// --- Sektion ---
$sectionBg    = $elementData['section_bg'] ?? '';
$sectionBgImg = $elementData['section_bg_image'] ?? '';
$sectionPad   = $elementData['section_padding'] ?? '';
$container    = $elementData['container_width'] ?? 'uk-container';
$sectionLight = !empty($elementData['section_light']);

// Abbruch wenn kein Inhalt
if (empty($image) && empty($heading) && empty($text)) {
    return;
}

// Link-URL ermitteln
$finalLink = '';
if ($linkType === 'external' && $linkUrl) {
    $finalLink = $linkUrl;
} elseif ($linkType === 'internal' && $linkInternal) {
    $finalLink = rex_getUrl((int) $linkInternal);
}

// Textbreite aus Bildbreite ableiten
$textWidthMap = [
    '1-3' => '2-3',
    '2-5' => '3-5',
    '1-2' => '1-2',
    '3-5' => '2-5',
    '2-3' => '1-3',
];
$imageWidthClass = 'uk-width-' . $imageWidth . '@m';
$textWidthClass  = 'uk-width-' . ($textWidthMap[$imageWidth] ?? '1-2') . '@m';

// Vertikale Ausrichtung
$verticalClass = match ($verticalAlign) {
    'middle' => 'uk-flex-middle',
    'bottom' => 'uk-flex-bottom',
    default  => '',
};

// Bild-Klassen
$imgClasses = ['uk-width-1-1'];
if ($imageRounded) {
    $imgClasses[] = 'uk-border-rounded';
}
if ($imageShadow) {
    $imgClasses[] = 'uk-box-shadow-' . $imageShadow;
}

// Bild-URL über Media Manager
$imageUrl = '';
if ($image) {
    if ($imageRatio) {
        $imageUrl = rex_media_manager::getUrl('card_' . $imageRatio . '_w1200', $image);
    } else {
        $imageUrl = rex_url::media($image);
    }
}

// Srcset für responsive Bilder
$srcset = '';
if ($image) {
    $sizes = [400, 800, 1200, 1600];
    $parts = [];
    foreach ($sizes as $w) {
        $type = $imageRatio ? 'card_' . $imageRatio . '_w' . $w : 'card_original_w' . $w;
        $parts[] = rex_media_manager::getUrl($type, $image) . ' ' . $w . 'w';
    }
    $srcset = implode(', ', $parts);
}

// Sektion aufbauen
$sectionClasses = [];
if ($sectionBg) {
    $sectionClasses[] = $sectionBg;
}
if ($sectionPad) {
    $sectionClasses[] = $sectionPad;
}
if ($sectionLight) {
    $sectionClasses[] = 'uk-light';
}

$sectionStyle = '';
if ($sectionBgImg) {
    $ext = strtolower(pathinfo($sectionBgImg, PATHINFO_EXTENSION));
    if (!in_array($ext, ['mp4', 'webm', 'ogg'], true)) {
        $bgUrl = rex_media_manager::getUrl('content_slideshow', $sectionBgImg);
        $sectionStyle = ' style="background-image: url(\'' . $bgUrl . '\'); background-size: cover; background-position: center;"';
    }
}

$hasSection = !empty($sectionClasses) || !empty($sectionBgImg);

?>

<?php if ($hasSection): ?>
<section class="<?= implode(' ', $sectionClasses) ?>"<?= $sectionStyle ?>>
<?php endif; ?>

<div class="<?= rex_escape($container ?: 'uk-container') ?>">
    <div class="uk-grid uk-grid-large <?= $verticalClass ?>" uk-grid>

        <?php
        // Bild-Element (als eigenes Fragment)
        $mediaBlock = static function () use ($imageUrl, $srcset, $imageAlt, $heading, $imgClasses, $image, $imageStyle): void {
            if (empty($image) || empty($imageUrl)) {
                return;
            }

            $wrapClass = 'uk-margin-remove';
            if ($imageStyle === 'stacked') {
                $wrapClass .= ' cb-image-stack';
            } elseif ($imageStyle === 'overlap') {
                $wrapClass .= ' cb-image-overlap';
            }
            ?>
            <figure class="<?= $wrapClass ?>">
                <img
                    src="<?= rex_escape($imageUrl) ?>"
                    <?= $srcset ? 'srcset="' . rex_escape($srcset) . '"' : '' ?>
                    sizes="(min-width: 960px) 50vw, 100vw"
                    alt="<?= rex_escape($imageAlt ?: $heading) ?>"
                    class="<?= implode(' ', $imgClasses) ?>"
                    loading="lazy"
                >
            </figure>
            <?php
        };
        ?>

        <?php if ($mediaPosition === 'left'): ?>
            <div class="<?= $imageWidthClass ?>">
                <?php $mediaBlock(); ?>
            </div>
        <?php endif; ?>

        <div class="<?= $textWidthClass ?>">

            <?php if ($badge): ?>
                <span class="uk-badge uk-margin-small-bottom"><?= rex_escape($badge) ?></span>
            <?php endif; ?>

            <?php if ($heading): ?>
                <<?= $tag ?> class="uk-margin-small-top uk-margin-small-bottom">
                    <?= rex_escape($heading) ?>
                </<?= $tag ?>>
            <?php endif; ?>

            <?php if ($subheading): ?>
                <p class="uk-text-lead uk-margin-small-bottom">
                    <?= rex_escape($subheading) ?>
                </p>
            <?php endif; ?>

            <?php if ($text): ?>
                <div class="uk-margin"><?= $text ?></div>
            <?php endif; ?>

            <?php if ($finalLink && $linkText): ?>
                <div class="uk-margin-top">
                    <a href="<?= rex_escape($finalLink) ?>" class="uk-button <?= rex_escape($linkStyle) ?>">
                        <?= rex_escape($linkText) ?>
                        <?php if ($linkStyle === 'uk-button-text'): ?>
                            <span uk-icon="chevron-right"></span>
                        <?php endif; ?>
                    </a>
                </div>
            <?php endif; ?>

        </div>

        <?php if ($mediaPosition === 'right'): ?>
            <div class="<?= $imageWidthClass ?>">
                <?php $mediaBlock(); ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php if ($hasSection): ?>
</section>
<?php endif; ?>

<?php if ($imageStyle === 'stacked' || $imageStyle === 'overlap'): ?>
<style>
/* --- Bildstapel / Overlap Effekte --- */
.cb-image-stack {
    position: relative;
    padding: 24px 24px 0 0;
    overflow: visible;
}
.cb-image-stack img {
    position: relative;
    z-index: 2;
    display: block;
}
.cb-image-stack::before {
    content: '';
    position: absolute;
    inset: auto 0 -16px -16px;
    width: 75%;
    height: 75%;
    background: var(--uk-color-primary, #1e87f0);
    opacity: 0.12;
    border-radius: 4px;
    z-index: 1;
}
.cb-image-stack::after {
    content: '';
    position: absolute;
    inset: 0 -16px -16px auto;
    width: 60%;
    height: 60%;
    background: var(--uk-color-primary, #1e87f0);
    opacity: 0.06;
    border-radius: 4px;
    z-index: 0;
}

.cb-image-overlap {
    overflow: visible;
    position: relative;
    z-index: 2;
}
@media (min-width: 960px) {
    .cb-image-overlap {
        margin-inline-end: -60px;
    }
    .cb-image-overlap img {
        filter: drop-shadow(0 8px 24px rgba(0,0,0,.18));
    }
}

/* Dark Mode kompatibel */
body.rex-theme-dark .cb-image-stack::before,
body.rex-theme-dark .cb-image-stack::after {
    opacity: 0.08;
}
@media (prefers-color-scheme: dark) {
    body.rex-has-theme:not(.rex-theme-light) .cb-image-stack::before,
    body.rex-has-theme:not(.rex-theme-light) .cb-image-stack::after {
        opacity: 0.08;
    }
}
</style>
<?php endif; ?>
