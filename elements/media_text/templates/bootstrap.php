<?php

/**
 * Bild & Text Element - Bootstrap Template
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
$imageWidth    = $elementData['image_width'] ?? '1-2';
$verticalAlign = $elementData['vertical_align'] ?? 'middle';
$imageRounded  = !empty($elementData['image_rounded']);
$imageShadow   = $elementData['image_shadow'] ?? '';
$linkType      = $elementData['link_type'] ?? '';
$linkUrl       = $elementData['link_url'] ?? '';
$linkInternal  = $elementData['link_internal'] ?? '';
$linkText      = $elementData['link_text'] ?? 'Mehr erfahren';
$linkStyle     = $elementData['link_style'] ?? 'btn-outline-primary';
$sectionBg     = $elementData['section_bg'] ?? '';
$sectionPad    = $elementData['section_padding'] ?? '';
$container     = $elementData['container_width'] ?? 'container';

if (empty($image) && empty($heading) && empty($text)) {
    return;
}

$finalLink = '';
if ($linkType === 'external' && $linkUrl) {
    $finalLink = $linkUrl;
} elseif ($linkType === 'internal' && $linkInternal) {
    $finalLink = rex_getUrl((int) $linkInternal);
}

// Bootstrap col-Breiten aus UIkit-Klassen ableiten
$bsColMap = [
    '1-3' => ['img' => 'col-md-4', 'txt' => 'col-md-8'],
    '2-5' => ['img' => 'col-md-5', 'txt' => 'col-md-7'],
    '1-2' => ['img' => 'col-md-6', 'txt' => 'col-md-6'],
    '3-5' => ['img' => 'col-md-7', 'txt' => 'col-md-5'],
    '2-3' => ['img' => 'col-md-8', 'txt' => 'col-md-4'],
];
$cols = $bsColMap[$imageWidth] ?? $bsColMap['1-2'];

$alignClass = $verticalAlign === 'middle' ? 'align-items-center' : ($verticalAlign === 'bottom' ? 'align-items-end' : 'align-items-start');

$imgClasses = ['img-fluid'];
if ($imageRounded) {
    $imgClasses[] = 'rounded';
}
if ($imageShadow) {
    $shadowBs = ['small' => 'shadow-sm', 'medium' => 'shadow', 'large' => 'shadow-lg', 'xlarge' => 'shadow-lg'];
    $imgClasses[] = $shadowBs[$imageShadow] ?? 'shadow';
}

$imageUrl = $image ? ($imageRatio ? rex_media_manager::getUrl('card_' . $imageRatio . '_w1200', $image) : rex_url::media($image)) : '';

$btnClass = match ($linkStyle) {
    'uk-button-primary'   => 'btn btn-primary',
    'uk-button-secondary' => 'btn btn-secondary',
    'uk-button-text'      => 'btn btn-link px-0',
    default               => 'btn btn-outline-primary',
};

?>
<div class="<?= rex_escape($container ?: 'container') ?> <?= rex_escape($sectionPad) ?>">
    <div class="row <?= $alignClass ?> g-4 g-lg-5">

        <?php if ($mediaPosition === 'left' && $image && $imageUrl): ?>
            <div class="<?= $cols['img'] ?>">
                <img src="<?= rex_escape($imageUrl) ?>" alt="<?= rex_escape($imageAlt ?: $heading) ?>"
                     class="<?= implode(' ', $imgClasses) ?>" loading="lazy">
            </div>
        <?php endif; ?>

        <div class="<?= $cols['txt'] ?>">
            <?php if ($badge): ?>
                <span class="badge bg-primary mb-2"><?= rex_escape($badge) ?></span>
            <?php endif; ?>
            <?php if ($heading): ?>
                <<?= $tag ?> class="mt-0"><?= rex_escape($heading) ?></<?= $tag ?>>
            <?php endif; ?>
            <?php if ($subheading): ?>
                <p class="lead"><?= rex_escape($subheading) ?></p>
            <?php endif; ?>
            <?php if ($text): ?>
                <div><?= $text ?></div>
            <?php endif; ?>
            <?php if ($finalLink && $linkText): ?>
                <div class="mt-3">
                    <a href="<?= rex_escape($finalLink) ?>" class="<?= $btnClass ?>">
                        <?= rex_escape($linkText) ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($mediaPosition === 'right' && $image && $imageUrl): ?>
            <div class="<?= $cols['img'] ?>">
                <img src="<?= rex_escape($imageUrl) ?>" alt="<?= rex_escape($imageAlt ?: $heading) ?>"
                     class="<?= implode(' ', $imgClasses) ?>" loading="lazy">
            </div>
        <?php endif; ?>

    </div>
</div>
