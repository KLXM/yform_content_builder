<?php
/**
 * UIkit Template für Text & Bild Element
 * @var array $elementData
 */

$headline = $elementData['headline'] ?? '';
$text = $elementData['text'] ?? '';
$image = $elementData['image'] ?? '';
$imagePosition = $elementData['image_position'] ?? 'right';
?>

<div class="text-image-element">
    <div class="uk-grid" data-uk-grid>
        <?php if ($imagePosition === 'left'): ?>
            <div class="uk-width-1-2@m">
                <?php if ($image): ?>
                    <img src="<?= rex_media_manager::getUrl('content_text_image', $image) ?>" alt="<?= rex_escape($headline) ?>" class="uk-width-1-1">
                <?php endif; ?>
            </div>
            <div class="uk-width-1-2@m">
                <?php if ($headline): ?>
                    <h2 class="uk-heading-medium"><?= rex_escape($headline) ?></h2>
                <?php endif; ?>
                <?php if ($text): ?>
                    <div class="text-content"><?= $text ?></div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="uk-width-1-2@m">
                <?php if ($headline): ?>
                    <h2 class="uk-heading-medium"><?= rex_escape($headline) ?></h2>
                <?php endif; ?>
                <?php if ($text): ?>
                    <div class="text-content"><?= $text ?></div>
                <?php endif; ?>
            </div>
            <div class="uk-width-1-2@m">
                <?php if ($image): ?>
                    <img src="<?= rex_media_manager::getUrl('content_text_image', $image) ?>" alt="<?= rex_escape($headline) ?>" class="uk-width-1-1">
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
