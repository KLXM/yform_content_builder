<?php

/**
 * Testimonial Element - Bootstrap Template
 *
 * @var array $elementData
 */

$items         = $elementData['items'] ?? [];
$style         = $elementData['style'] ?? 'card';
$columns       = (int) ($elementData['columns'] ?? 2);
$columnsTablet = (int) ($elementData['columns_tablet'] ?? 1);
$container     = $elementData['container_width'] ?? 'container';
$sectionPad    = $elementData['section_padding'] ?? '';

if (empty($items)) {
    return;
}

$colMd    = (int) round(12 / $columns);
$colSm    = (int) round(12 / $columnsTablet);
$colClass = "col-sm-{$colSm} col-md-{$colMd}";

$cardClass = match ($style) {
    'accent'  => 'border-start border-primary border-4 ps-3',
    'minimal' => 'border-0 p-0',
    default   => 'card h-100',
};

?>
<div class="<?= rex_escape($container ?: 'container') ?> <?= rex_escape($sectionPad) ?>">
    <div class="row g-4">
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
            <div class="<?= $colClass ?>">
                <div class="<?= $cardClass ?> h-100">
                    <?php if ($style === 'card'): ?>
                        <div class="card-body">
                    <?php endif; ?>

                    <?php if ($rating): ?>
                        <div class="text-warning mb-2"><?= str_repeat('★', (int) $rating) . str_repeat('☆', 5 - (int) $rating) ?></div>
                    <?php endif; ?>

                    <blockquote class="blockquote fst-italic mb-3">
                        <p><?= nl2br(rex_escape($quote)) ?></p>
                    </blockquote>

                    <div class="d-flex align-items-center mt-auto">
                        <?php if ($avatarUrl): ?>
                            <img src="<?= rex_escape($avatarUrl) ?>" alt="<?= rex_escape($avatarAlt) ?>"
                                 class="rounded-circle me-3" width="48" height="48"
                                 style="object-fit:cover;" loading="lazy">
                        <?php endif; ?>
                        <div>
                            <strong><?= rex_escape($authorName) ?></strong>
                            <?php if ($authorRole): ?>
                                <br><small class="text-muted"><?= rex_escape($authorRole) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($style === 'card'): ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
