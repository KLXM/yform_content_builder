<?php

/**
 * Feature-Raster Element - Plain Template
 *
 * @var array $elementData
 */

$items = $elementData['items'] ?? [];

if (empty($items)) {
    return;
}

?>
<div class="cb-feature-grid">
    <?php foreach ($items as $item): ?>
        <?php
        $itemIcon     = $item['icon'] ?? '';
        $itemHeading  = $item['heading'] ?? '';
        $itemText     = $item['text'] ?? '';
        $itemLinkUrl  = trim($item['link_url'] ?? '');
        $itemLinkText = $item['link_text'] ?? 'Mehr';

        if (empty($itemHeading) && empty($itemText)) {
            continue;
        }
        ?>
        <div class="cb-feature-grid__item">
            <?php if ($itemIcon): ?>
                <div class="cb-feature-grid__icon">
                    <img src="<?= rex_escape(rex_url::media($itemIcon)) ?>" alt="" aria-hidden="true" loading="lazy">
                </div>
            <?php endif; ?>
            <?php if ($itemHeading): ?>
                <h3><?= rex_escape($itemHeading) ?></h3>
            <?php endif; ?>
            <?php if ($itemText): ?>
                <p><?= nl2br(rex_escape($itemText)) ?></p>
            <?php endif; ?>
            <?php if ($itemLinkUrl && $itemLinkText): ?>
                <a href="<?= rex_escape($itemLinkUrl) ?>"><?= rex_escape($itemLinkText) ?></a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
