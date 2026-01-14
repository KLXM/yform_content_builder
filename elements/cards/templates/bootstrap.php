<?php
/**
 * Cards Grid Element - Bootstrap Template
 * @var array $elementData
 */

$columns = $elementData['columns'] ?? '3';
$columnsTablet = $elementData['columns_tablet'] ?? '2';
$columnsMobile = $elementData['columns_mobile'] ?? '1';
$gap = $elementData['gap'] ?? 'medium';
$matchHeight = !empty($elementData['match_height']);
$cardStyle = $elementData['card_style'] ?? 'default';
$cardSize = $elementData['card_size'] ?? 'default';
$items = $elementData['items'] ?? [];

if (empty($items)) {
    return;
}

// Grid Klassen
$gridClasses = [
    'cb-cards-grid',
    'cb-cards-cols-' . $columns,
    'cb-cards-cols-tablet-' . $columnsTablet,
    'cb-cards-cols-mobile-' . $columnsMobile,
    'cb-cards-gap-' . $gap
];

if ($matchHeight) {
    $gridClasses[] = 'cb-cards-match-height';
}

$gridClassStr = implode(' ', $gridClasses);

// Card Klassen
$cardClasses = [
    'cb-card',
    'cb-card-style-' . $cardStyle,
    'cb-card-size-' . $cardSize
];

$cardClassStr = implode(' ', $cardClasses);
?>

<div class="<?= $gridClassStr ?>">
    <?php foreach ($items as $index => $item): ?>
        <?php

        
        $title = $item['title'] ?? '';
        $subtitle = $item['subtitle'] ?? '';
        $text = $item['text'] ?? '';
        $image = $item['image'] ?? '';
        $imagePosition = $item['image_position'] ?? 'top';
        // Fix: Ensure imagePosition is never null or empty
        if (empty($imagePosition)) {
            $imagePosition = 'top';
        }
        $badge = $item['badge'] ?? '';
        $badgeColor = $item['badge_color'] ?? 'primary';
        $linkType = $item['link_type'] ?? '';
        $linkUrl = $item['link_url'] ?? '';
        $linkInternal = $item['link_internal'] ?? '';
        $linkText = $item['link_text'] ?? 'Mehr erfahren';
        
        // Link generieren
        $hasLink = false;
        $href = '';
        if ($linkType === 'external' && !empty($linkUrl)) {
            $hasLink = true;
            $href = $linkUrl;
        } elseif ($linkType === 'internal' && !empty($linkInternal)) {
            $hasLink = true;
            $href = rex_getUrl($linkInternal);
        }
        
        // Bild Pfad via Media Manager
        $imageSrc = '';
        
        if (!empty($image)) {
            $media = rex_media::get($image);
            if ($media) {
                $imageSrc = rex_media_manager::getUrl('content_card', $image);
            }
        }
        ?>
        
        <div class="cb-card-item">
            <div class="<?= $cardClassStr ?>">
                <?php if (!empty($imageSrc) && $imagePosition === 'top'): ?>
                    <div class="cb-card-image cb-card-image-top">
                        <img src="<?= $imageSrc ?>" alt="<?= rex_escape($title) ?>">
                    </div>
                <?php endif; ?>
                
                <div class="cb-card-body">
                    <?php if (!empty($badge)): ?>
                        <span class="cb-card-badge cb-card-badge-<?= $badgeColor ?>"><?= rex_escape($badge) ?></span>
                    <?php endif; ?>
                    
                    <?php if (!empty($title)): ?>
                        <h3 class="cb-card-title"><?= rex_escape($title) ?></h3>
                    <?php endif; ?>
                    
                    <?php if (!empty($subtitle)): ?>
                        <p class="cb-card-subtitle"><?= rex_escape($subtitle) ?></p>
                    <?php endif; ?>
                    
                    <?php if (!empty($text)): ?>
                        <div class="cb-card-text"><?= $text ?></div>
                    <?php endif; ?>
                    
                    <?php if ($hasLink): ?>
                        <a href="<?= $href ?>" class="cb-card-link btn btn-primary">
                            <?= rex_escape($linkText) ?>
                        </a>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($imageSrc) && $imagePosition === 'bottom'): ?>
                    <div class="cb-card-image cb-card-image-bottom">
                        <img src="<?= $imageSrc ?>" alt="<?= rex_escape($title) ?>">
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
    <?php endforeach; ?>
</div>
