<?php

/**
 * Testimonial Element - Plain Template
 *
 * @var array $elementData
 */

$items = $elementData['items'] ?? [];

if (empty($items)) {
    return;
}

?>
<div class="cb-testimonials">
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

        $avatarUrl = $authorImage ? rex_media_manager::getUrl('card_1_1_w400', $authorImage) : '';
        $avatarAlt = \KLXM\YFormContentBuilder\MediaAltResolver::resolve((string) $authorImage, '', (string) $authorName);
        ?>
        <div class="cb-testimonials__item">
            <?php if ($rating): ?>
                <div class="cb-rating" aria-label="<?= (int) $rating ?> von 5 Sternen">
                    <?= str_repeat('★', (int) $rating) . str_repeat('☆', 5 - (int) $rating) ?>
                </div>
            <?php endif; ?>
            <blockquote class="cb-testimonials__quote">
                <p><?= nl2br(rex_escape($quote)) ?></p>
            </blockquote>
            <div class="cb-testimonials__author">
                <?php if ($avatarUrl): ?>
                    <img src="<?= rex_escape($avatarUrl) ?>" alt="<?= rex_escape($avatarAlt) ?>"
                         class="cb-testimonials__avatar" loading="lazy">
                <?php endif; ?>
                <div>
                    <strong><?= rex_escape($authorName) ?></strong>
                    <?php if ($authorRole): ?>
                        <span><?= rex_escape($authorRole) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
