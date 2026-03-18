<?php

/**
 * Feature-Raster Element - UIkit Template
 *
 * @var array $elementData
 */

// --- Repeater ---
$items = $elementData['items'] ?? [];

// --- Design ---
$columns       = $elementData['columns'] ?? '3';
$columnsTablet = $elementData['columns_tablet'] ?? '2';
$columnsMobile = $elementData['columns_mobile'] ?? '1';
$gap           = $elementData['gap'] ?? 'medium';
$iconStyle     = $elementData['icon_style'] ?? 'plain';
$cardStyle     = $elementData['card_style'] ?? '';
$textAlign     = $elementData['text_align'] ?? 'left';

// --- Sektion ---
$sectionBg    = $elementData['section_bg'] ?? '';
$sectionBgImg = $elementData['section_bg_image'] ?? '';
$sectionPad   = $elementData['section_padding'] ?? '';
$container    = $elementData['container_width'] ?? 'uk-container';
$sectionLight = !empty($elementData['section_light']);

if (empty($items)) {
    return;
}

// Grid-Klassen
$gridGapClass = $gap !== 'medium' ? ' uk-grid-' . $gap : '';

$widthDesktop = 'uk-width-1-' . $columns . '@m';
$widthTablet  = 'uk-width-1-' . $columnsTablet . '@s';
$widthMobile  = $columnsMobile === '2' ? 'uk-width-1-2' : 'uk-width-1-1';

$itemWidthClass = implode(' ', array_filter([$widthMobile, $widthTablet, $widthDesktop]));

// Text-Ausrichtung
$alignClass = $textAlign === 'center' ? ' uk-text-center' : '';

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

// Icon-Stil-Klassen
$iconWrapClasses = ['cb-feature-icon'];
if ($iconStyle === 'circle') {
    $iconWrapClasses[] = 'cb-feature-icon--circle';
} elseif ($iconStyle === 'box') {
    $iconWrapClasses[] = 'cb-feature-icon--box';
}
$iconWrapClass = implode(' ', $iconWrapClasses);

?>

<?php if ($hasSection): ?>
<section class="<?= implode(' ', $sectionClasses) ?>"<?= $sectionStyle ?>>
<?php endif; ?>

<div class="<?= rex_escape($container ?: 'uk-container') ?>">
    <div class="uk-grid<?= $gridGapClass ?> uk-grid-match" uk-grid>

        <?php foreach ($items as $item): ?>
            <?php
            $itemIcon     = $item['icon'] ?? '';
            $itemUikitIcon = trim($item['icon_uikit'] ?? '');
            $itemHeading  = $item['heading'] ?? '';
            $itemText     = $item['text'] ?? '';
            $itemLinkUrl  = trim($item['link_url'] ?? '');
            $itemLinkText = $item['link_text'] ?? 'Mehr';

            if (empty($itemHeading) && empty($itemText)) {
                continue;
            }

            $wrapTag   = $itemLinkUrl ? 'a' : 'div';
            $wrapAttrs = $itemLinkUrl ? ' href="' . rex_escape($itemLinkUrl) . '"' : '';
            ?>
            <div class="<?= $itemWidthClass ?>">
                <?php if ($cardStyle): ?>
                    <div class="uk-card uk-card-<?= rex_escape($cardStyle) ?> uk-card-body<?= $alignClass ?>">
                <?php else: ?>
                    <div class="uk-panel<?= $alignClass ?>">
                <?php endif; ?>

                <?php if ($itemIcon || $itemUikitIcon): ?>
                    <div class="<?= $iconWrapClass ?> uk-margin-small-bottom">
                        <?php if ($itemIcon): ?>
                            <?php $iconExt = strtolower(pathinfo($itemIcon, PATHINFO_EXTENSION)); ?>
                            <?php if ($iconExt === 'svg'): ?>
                                <img src="<?= rex_escape(rex_url::media($itemIcon)) ?>"
                                     alt="" aria-hidden="true" class="cb-feature-icon__img" loading="lazy">
                            <?php else: ?>
                                <img src="<?= rex_escape(rex_media_manager::getUrl('card_1_1_w400', $itemIcon)) ?>"
                                     alt="" aria-hidden="true" class="cb-feature-icon__img" loading="lazy">
                            <?php endif; ?>
                        <?php elseif ($itemUikitIcon): ?>
                            <span uk-icon="icon: <?= rex_escape($itemUikitIcon) ?>; ratio: 2.5" class="cb-feature-icon__svg"></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($itemHeading): ?>
                    <h3 class="uk-card-title<?= $alignClass ?>">
                        <?= rex_escape($itemHeading) ?>
                    </h3>
                <?php endif; ?>

                <?php if ($itemText): ?>
                    <p><?= nl2br(rex_escape($itemText)) ?></p>
                <?php endif; ?>

                <?php if ($itemLinkUrl && $itemLinkText): ?>
                    <a href="<?= rex_escape($itemLinkUrl) ?>" class="uk-button uk-button-text uk-margin-small-top">
                        <?= rex_escape($itemLinkText) ?>
                        <span uk-icon="chevron-right"></span>
                    </a>
                <?php endif; ?>

                </div>
            </div>
        <?php endforeach; ?>

    </div>
</div>

<?php if ($hasSection): ?>
</section>
<?php endif; ?>

<style>
.cb-feature-icon { display: inline-flex; align-items: center; justify-content: center; color: currentColor; }
.cb-feature-icon--circle { width: 64px; height: 64px; background: rgba(var(--uk-color-primary-rgb, 30,135,240), .12); border-radius: 50%; }
.cb-feature-icon--box    { width: 64px; height: 64px; background: rgba(var(--uk-color-primary-rgb, 30,135,240), .12); border-radius: 4px; }
.cb-feature-icon__img    { max-width: 48px; max-height: 48px; object-fit: contain; }
.cb-feature-icon__svg    { display: block; }
</style>
