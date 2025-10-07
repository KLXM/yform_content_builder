<?php
/**
 * Text & Image Element - Bootstrap Template (Extended)
 * @var array $elementData
 */

$layout = $elementData['layout'] ?? 'image_text';
$headline = $elementData['headline'] ?? '';
$headlineTag = $elementData['headline_tag'] ?? 'h2';
$text = $elementData['text'] ?? '';
$image = $elementData['image'] ?? '';
$imageAlt = $elementData['image_alt'] ?? $headline;
$imageRatio = $elementData['image_ratio'] ?? 'auto';
$linkType = $elementData['link_type'] ?? '';
$linkUrl = $elementData['link_url'] ?? '';
$linkInternal = $elementData['link_internal'] ?? '';
$linkText = $elementData['link_text'] ?? 'Mehr erfahren';
$linkTarget = $elementData['link_target'] ?? '_self';
$bgColor = $elementData['background_color'] ?? '';
$spacing = $elementData['spacing'] ?? 'default';

// Link URL bestimmen
$finalLinkUrl = '';
if ($linkType === 'external' && $linkUrl) {
    $finalLinkUrl = $linkUrl;
} elseif ($linkType === 'internal' && $linkInternal) {
    $finalLinkUrl = rex_getUrl($linkInternal);
}

// Spacing Klassen
$spacingClass = '';
switch ($spacing) {
    case 'compact':
        $spacingClass = 'text-image-compact';
        break;
    case 'spacious':
        $spacingClass = 'text-image-spacious';
        break;
}

// Image Ratio Klassen
$ratioClass = '';
if ($imageRatio !== 'auto') {
    $ratioClass = 'img-ratio img-ratio-' . $imageRatio;
}

// Layout Klassen
$isVertical = in_array($layout, ['image_top', 'text_top']);
$imageFirst = in_array($layout, ['image_text', 'image_top']);
?>

<div class="text-image-element <?= $spacingClass ?> <?= $bgColor ?> <?= $isVertical ? 'text-image-vertical' : '' ?>">
    <div class="container-fluid">
        <div class="row <?= !$isVertical ? 'align-items-center' : '' ?>">
            
            <?php if ($imageFirst && $image): ?>
                <!-- Image First -->
                <div class="col-md-<?= $isVertical ? '12' : '6' ?>">
                    <div class="text-image-media">
                        <?php if ($ratioClass): ?>
                            <div class="<?= $ratioClass ?>">
                                <img src="<?= rex_url::media($image) ?>" 
                                     alt="<?= rex_escape($imageAlt) ?>" 
                                     class="img-responsive">
                            </div>
                        <?php else: ?>
                            <img src="<?= rex_url::media($image) ?>" 
                                 alt="<?= rex_escape($imageAlt) ?>" 
                                 class="img-responsive">
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Text Content -->
            <div class="col-md-<?= $isVertical ? '12' : '6' ?>">
                <div class="text-image-content">
                    <?php if ($headline): ?>
                        <<?= $headlineTag ?> class="text-image-headline">
                            <?= rex_escape($headline) ?>
                        </<?= $headlineTag ?>>
                    <?php endif; ?>
                    
                    <?php if ($text): ?>
                        <div class="text-image-text">
                            <?= $text // CKE5 HTML - NICHT escapen! ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($finalLinkUrl): ?>
                        <div class="text-image-action">
                            <a href="<?= rex_escape($finalLinkUrl) ?>" 
                               class="btn btn-primary" 
                               target="<?= rex_escape($linkTarget) ?>"
                               <?= $linkTarget === '_blank' ? 'rel="noopener noreferrer"' : '' ?>>
                                <?= rex_escape($linkText) ?>
                                <?php if ($linkTarget === '_blank'): ?>
                                    <i class="fa fa-external-link"></i>
                                <?php endif; ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if (!$imageFirst && $image): ?>
                <!-- Image Last -->
                <div class="col-md-<?= $isVertical ? '12' : '6' ?>">
                    <div class="text-image-media">
                        <?php if ($ratioClass): ?>
                            <div class="<?= $ratioClass ?>">
                                <img src="<?= rex_url::media($image) ?>" 
                                     alt="<?= rex_escape($imageAlt) ?>" 
                                     class="img-responsive">
                            </div>
                        <?php else: ?>
                            <img src="<?= rex_url::media($image) ?>" 
                                 alt="<?= rex_escape($imageAlt) ?>" 
                                 class="img-responsive">
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            
        </div>
    </div>
</div>

<style>
/* Text Image Element Styles */
.text-image-element {
    padding: 40px 0;
}

.text-image-element.bg-light {
    background-color: #f8f9fa;
}

.text-image-element.bg-dark {
    background-color: #343a40;
    color: #fff;
}

.text-image-compact {
    padding: 20px 0;
}

.text-image-spacious {
    padding: 80px 0;
}

.text-image-content {
    padding: 20px;
}

.text-image-headline {
    margin-bottom: 20px;
}

.text-image-text {
    margin-bottom: 20px;
}

.text-image-action {
    margin-top: 30px;
}

.text-image-media img {
    width: 100%;
    height: auto;
}

/* Image Ratio */
.img-ratio {
    position: relative;
    overflow: hidden;
}

.img-ratio img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.img-ratio-1-1 {
    padding-bottom: 100%;
}

.img-ratio-4-3 {
    padding-bottom: 75%;
}

.img-ratio-16-9 {
    padding-bottom: 56.25%;
}

.img-ratio-21-9 {
    padding-bottom: 42.86%;
}

/* Vertical Layout */
.text-image-vertical .text-image-media {
    margin-bottom: 30px;
}
</style>
