<?php
/**
 * Cards Grid Element - Plain Template
 * @var array $elementData
 */

$columns = $elementData['columns'] ?? '3';
$items = $elementData['items'] ?? [];

if (empty($items)) {
    return;
}

$gridClass = 'cb-cards-grid cb-cards-cols-' . $columns;
?>

<div class="<?= $gridClass ?>">
    <?php foreach ($items as $item): ?>
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
        ?>
        
        <div class="cb-card">
            <?php if (!empty($imageSrc)): ?>
                <div class="cb-card-image">
                    <img src="<?= $imageSrc ?>" alt="<?= rex_escape($title) ?>">
                </div>
            <?php endif; ?>
            
            <div class="cb-card-body">
                <?php if (!empty($badge)): ?>
                    <span class="cb-card-badge"><?= rex_escape($badge) ?></span>
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
                    <a href="<?= $href ?>" class="cb-card-link">
                        <?= rex_escape($linkText) ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        
    <?php endforeach; ?>
</div>
