<?php
/**
 * Plain HTML Template für Text & Bild Element
 * @var array $elementData
 */

$headline = $elementData['headline'] ?? '';
$text = $elementData['text'] ?? '';
$image = $elementData['image'] ?? '';
$imagePosition = $elementData['image_position'] ?? 'right';
?>

<div class="text-image-element text-image-<?= rex_escape($imagePosition) ?>">
    <?php if ($imagePosition === 'left'): ?>
        <?php if ($image): ?>
            <div class="image-wrapper">
                <img src="<?= rex_media_manager::getUrl('content_text_image', $image) ?>" alt="<?= rex_escape($headline) ?>">
            </div>
        <?php endif; ?>
        <div class="text-wrapper">
            <?php if ($headline): ?>
                <h2><?= rex_escape($headline) ?></h2>
            <?php endif; ?>
            <?php if ($text): ?>
                <div class="text-content"><?= $text ?></div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="text-wrapper">
            <?php if ($headline): ?>
                <h2><?= rex_escape($headline) ?></h2>
            <?php endif; ?>
            <?php if ($text): ?>
                <div class="text-content"><?= $text ?></div>
            <?php endif; ?>
        </div>
        <?php if ($image): ?>
            <div class="image-wrapper">
                <img src="<?= rex_media_manager::getUrl('content_text_image', $image) ?>" alt="<?= rex_escape($headline) ?>">
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
