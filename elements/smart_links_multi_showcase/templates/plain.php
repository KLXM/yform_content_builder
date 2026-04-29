<?php

/**
 * Smart-Links Multi Showcase - Plain Template
 *
 * @var array $elementData
 */

$headline = trim((string) ($elementData['headline'] ?? ''));
$intro = trim((string) ($elementData['intro'] ?? ''));
$showPreview = (bool) ($elementData['show_preview'] ?? false);
$rawLinks = $elementData['links'] ?? '';

$smartLinkClass = \KLXM\YFormContentBuilder\SmartLink::class;
$smartLinkViewClass = \KLXM\YFormContentBuilder\SmartLinkView::class;
$links = $smartLinkClass::normalize($rawLinks, true);

if ($links === []) {
    return;
}

?>
<div class="cb-smart-links-multi-showcase">
    <?php if ($headline !== ''): ?>
        <h2><?= rex_escape($headline) ?></h2>
    <?php endif; ?>

    <?php if ($intro !== ''): ?>
        <p><?= nl2br(rex_escape($intro)) ?></p>
    <?php endif; ?>

    <ul class="cb-smart-links-multi-showcase__list">
        <?php foreach ($links as $linkItem): ?>
            <?php
            $resolved = $smartLinkViewClass::resolveSingle(['items' => [$linkItem]]);
            if ($resolved === null) {
                continue;
            }

            $previewData = $smartLinkViewClass::resolvePreview(['items' => [$linkItem]]);
            $typeMeta = $smartLinkViewClass::getTypeMeta($resolved['type']);
            $target = $resolved['is_external'] ? ' target="_blank" rel="noopener"' : '';
            ?>
            <li class="cb-smart-links-multi-showcase__item">
                <?php if ($showPreview && $previewData !== null): ?>
                    <span class="cb-smart-links-multi-showcase__preview">
                        <?php if ($previewData['kind'] === 'video'): ?>
                            <video src="<?= rex_escape($previewData['src']) ?>" controls preload="metadata"></video>
                        <?php else: ?>
                            <img src="<?= rex_escape($previewData['src']) ?>" alt="" loading="lazy">
                        <?php endif; ?>
                    </span>
                <?php endif; ?>
                <a href="<?= rex_escape($resolved['href']) ?>"<?= $target ?>>
                    <?= rex_escape($resolved['label']) ?>
                </a>
                <small>[<?= rex_escape($typeMeta['label']) ?>]</small>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
