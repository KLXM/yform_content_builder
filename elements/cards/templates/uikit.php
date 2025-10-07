<?php
/**
 * Cards Grid Element - UIkit Template
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

// UIkit Grid-Attribute
$gridAttrs = 'uk-grid';
$gridClasses = ['uk-grid'];

// Gap Mapping
$gapMap = [
    'small' => 'uk-grid-small',
    'medium' => '', // Default
    'large' => 'uk-grid-large'
];
if (isset($gapMap[$gap]) && $gapMap[$gap]) {
    $gridClasses[] = $gapMap[$gap];
}

// Match Height
if ($matchHeight) {
    $gridClasses[] = 'uk-grid-match';
}

// Width Classes für Spalten
$widthDesktop = 'uk-child-width-1-' . $columns . '@m';
$widthTablet = 'uk-child-width-1-' . $columnsTablet . '@s';
$widthMobile = 'uk-child-width-1-' . $columnsMobile;

$gridClasses[] = $widthDesktop;
$gridClasses[] = $widthTablet;
$gridClasses[] = $widthMobile;

$gridClassStr = implode(' ', $gridClasses);

// Card Style Mapping
$cardStyleMap = [
    'default' => 'uk-card-default',
    'primary' => 'uk-card-primary',
    'secondary' => 'uk-card-secondary',
    'hover' => 'uk-card-hover'
];
$cardStyleClass = $cardStyleMap[$cardStyle] ?? 'uk-card-default';

// Card Size Mapping
$cardSizeMap = [
    'small' => 'uk-card-small',
    'default' => '',
    'large' => 'uk-card-large'
];
$cardSizeClass = $cardSizeMap[$cardSize] ?? '';

// Card Classes
$cardClasses = ['uk-card', $cardStyleClass];
if ($cardSizeClass) {
    $cardClasses[] = $cardSizeClass;
}
$cardClassStr = implode(' ', $cardClasses);
?>

<div class="<?= $gridClassStr ?>" uk-grid>
    <?php foreach ($items as $item): ?>
        <?php
        $title = $item['title'] ?? '';
        $subtitle = $item['subtitle'] ?? '';
        $text = $item['text'] ?? '';
        $image = $item['image'] ?? '';
        $imagePosition = $item['image_position'] ?? 'top';
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
        
        // Bild Pfad
        $imageSrc = '';
        if (!empty($image)) {
            $media = rex_media::get($image);
            if ($media) {
                $imageSrc = '/media/' . $image;
            }
        }
        
        // Badge Color Mapping
        $badgeColorMap = [
            'primary' => '',
            'success' => 'uk-label-success',
            'info' => '',
            'warning' => 'uk-label-warning',
            'danger' => 'uk-label-danger'
        ];
        $badgeClass = 'uk-label ' . ($badgeColorMap[$badgeColor] ?? '');
        ?>
        
        <div>
            <div class="<?= $cardClassStr ?>">
                <?php if (!empty($imageSrc) && $imagePosition === 'top'): ?>
                    <div class="uk-card-media-top">
                        <img src="<?= $imageSrc ?>" alt="<?= rex_escape($title) ?>">
                    </div>
                <?php endif; ?>
                
                <div class="uk-card-body">
                    <?php if (!empty($badge)): ?>
                        <span class="<?= $badgeClass ?>"><?= rex_escape($badge) ?></span>
                    <?php endif; ?>
                    
                    <?php if (!empty($title)): ?>
                        <h3 class="uk-card-title"><?= rex_escape($title) ?></h3>
                    <?php endif; ?>
                    
                    <?php if (!empty($subtitle)): ?>
                        <p class="uk-text-meta"><?= rex_escape($subtitle) ?></p>
                    <?php endif; ?>
                    
                    <?php if (!empty($text)): ?>
                        <div><?= $text ?></div>
                    <?php endif; ?>
                    
                    <?php if ($hasLink): ?>
                        <a href="<?= $href ?>" class="uk-button uk-button-primary">
                            <?= rex_escape($linkText) ?>
                        </a>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($imageSrc) && $imagePosition === 'bottom'): ?>
                    <div class="uk-card-media-bottom">
                        <img src="<?= $imageSrc ?>" alt="<?= rex_escape($title) ?>">
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
    <?php endforeach; ?>
</div>
