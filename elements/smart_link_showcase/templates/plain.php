<?php

/**
 * Smart-Link Showcase - Plain Template
 *
 * @var array $elementData
 */

$headline = trim((string) ($elementData['headline'] ?? ''));
$intro = trim((string) ($elementData['intro'] ?? ''));
$items = $elementData['items'] ?? [];
$showPreview = !empty($elementData['show_preview']);

if ($items === []) {
    return;
}

?>
<div class="cb-smart-link-showcase">
    <?php if ($headline !== ''): ?>
        <h2><?= rex_escape($headline) ?></h2>
    <?php endif; ?>

    <?php if ($intro !== ''): ?>
        <p><?= nl2br(rex_escape($intro)) ?></p>
    <?php endif; ?>

    <ul class="cb-smart-link-showcase__list">
        <?php foreach ($items as $item): ?>
            <?php
            $title = trim((string) ($item['title'] ?? ''));
            $text = trim((string) ($item['text'] ?? ''));
            $smartLinkViewClass = \KLXM\YFormContentBuilder\SmartLinkView::class;
            $resolved = $smartLinkViewClass::resolveSingle($item['link'] ?? '', $title);
            $previewData = call_user_func([$smartLinkViewClass, 'resolvePreview'], $item['link'] ?? '');

            if ($resolved === null) {
                continue;
            }

            $typeMeta = \KLXM\YFormContentBuilder\SmartLinkView::getTypeMeta($resolved['type']);
            $target = $resolved['is_external'] ? ' target="_blank" rel="noopener"' : '';
            ?>
            <li class="cb-smart-link-showcase__item">
                <div class="cb-smart-link-showcase__preview">
                    <?php if ($showPreview && $previewData !== null): ?>
                        <?php if ($previewData['kind'] === 'video'): ?>
                            <video src="<?= rex_escape($previewData['src']) ?>" controls preload="metadata"></video>
                        <?php else: ?>
                            <img src="<?= rex_escape($previewData['src']) ?>" alt="" loading="lazy">
                        <?php endif; ?>
                    <?php else: ?>
                        <span>[<?= rex_escape($typeMeta['label']) ?>]</span>
                    <?php endif; ?>
                </div>
                <div class="cb-smart-link-showcase__content">
                    <div class="cb-smart-link-showcase__meta">
                        <span>[<?= rex_escape($typeMeta['label']) ?>]</span>
                        <strong><?= rex_escape($title !== '' ? $title : $resolved['label']) ?></strong>
                    </div>
                    <?php if ($text !== ''): ?>
                        <p><?= nl2br(rex_escape($text)) ?></p>
                    <?php endif; ?>
                    <a href="<?= rex_escape($resolved['href']) ?>"<?= $target ?>><?= rex_escape($resolved['label']) ?></a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>