<?php

/**
 * Testimonial Element - UIkit Template
 *
 * @var array $elementData
 */

// --- Repeater ---
$items = $elementData['items'] ?? [];

// --- Design ---
$style         = $elementData['style'] ?? 'card';
$columns       = $elementData['columns'] ?? '2';
$columnsTablet = $elementData['columns_tablet'] ?? '1';

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
$widthDesktop = 'uk-width-1-' . $columns . '@m';
$widthTablet  = 'uk-width-1-' . $columnsTablet . '@s';
$itemWidthClass = $widthTablet . ' ' . $widthDesktop;

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
    <div class="uk-grid uk-grid-medium uk-grid-match" uk-grid>

        <?php foreach ($items as $item): ?>
            <?php
            $quote       = $item['quote'] ?? '';
            $authorName  = $item['author_name'] ?? '';
            $authorRole  = $item['author_role'] ?? '';
            $authorImage = $item['author_image'] ?? '';
            $rating      = $item['rating'] ?? '';

            if (empty($quote) || empty($authorName)) {
                continue;
            }

            $avatarUrl = '';
            $avatarAlt = '';
            if ($authorImage) {
                $avatarUrl = rex_media_manager::getUrl('card_1_1_w400', $authorImage);
                $avatarAlt = YFormContentBuilderMediaAltResolver::resolve((string) $authorImage, '', (string) $authorName);
            }
            ?>
            <div class="<?= $itemWidthClass ?>">

                <?php if ($style === 'card'): ?>
                    <div class="uk-card uk-card-default uk-card-body">
                <?php elseif ($style === 'accent'): ?>
                    <div class="cb-testimonial--accent">
                <?php else: ?>
                    <div class="cb-testimonial--minimal">
                <?php endif; ?>

                    <?php if ($rating): ?>
                        <div class="cb-rating uk-margin-small-bottom" aria-label="Bewertung: <?= rex_escape($rating) ?> von 5 Sternen">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span aria-hidden="true"><?= $i <= (int) $rating ? '★' : '☆' ?></span>
                            <?php endfor; ?>
                        </div>
                    <?php endif; ?>

                    <blockquote class="cb-testimonial__quote uk-margin-small-bottom">
                        <p><?= nl2br(rex_escape($quote)) ?></p>
                    </blockquote>

                    <div class="cb-testimonial__author uk-flex uk-flex-middle uk-margin-top">
                        <?php if ($avatarUrl): ?>
                            <div class="uk-margin-small-right" style="flex-shrink:0;">
                                <img src="<?= rex_escape($avatarUrl) ?>"
                                      alt="<?= rex_escape($avatarAlt) ?>"
                                     class="uk-border-circle"
                                     style="width:48px; height:48px; object-fit:cover;"
                                     loading="lazy">
                            </div>
                        <?php else: ?>
                            <div class="cb-testimonial__avatar-placeholder uk-margin-small-right" aria-hidden="true">
                                <?= strtoupper(substr($authorName, 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                        <div>
                            <strong class="uk-display-block"><?= rex_escape($authorName) ?></strong>
                            <?php if ($authorRole): ?>
                                <span class="uk-text-small uk-text-muted"><?= rex_escape($authorRole) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>
        <?php endforeach; ?>

    </div>
</div>

<?php if ($hasSection): ?>
</section>
<?php endif; ?>

<style>
/* Testimonial Basis-Stile */
.cb-testimonial__quote { margin: 0; font-style: italic; }
.cb-testimonial__quote p { margin: 0; }
.cb-testimonial__quote p::before { content: '\201C'; }
.cb-testimonial__quote p::after  { content: '\201D'; }

/* Akzent-Stil */
.cb-testimonial--accent {
    padding: 24px;
    border-left: 4px solid var(--uk-color-primary, #1e87f0);
    background: rgba(var(--uk-color-primary-rgb, 30,135,240), .05);
    border-radius: 0 4px 4px 0;
    height: 100%;
}

/* Minimal-Stil */
.cb-testimonial--minimal { padding: 8px 0; height: 100%; }

/* Avatar-Platzhalter */
.cb-testimonial__avatar-placeholder {
    width: 48px; height: 48px; border-radius: 50%; flex-shrink: 0;
    background: var(--uk-color-primary, #1e87f0); color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-weight: bold; font-size: 1.2rem;
}

/* Sterne-Bewertung */
.cb-rating { color: #f5a623; font-size: 1.1rem; letter-spacing: 2px; }
</style>
