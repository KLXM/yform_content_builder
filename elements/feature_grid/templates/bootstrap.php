<?php

/**
 * Feature-Raster Element - Bootstrap Template
 *
 * @var array $elementData
 */

$items         = $elementData['items'] ?? [];
$columns       = (int) ($elementData['columns'] ?? 3);
$columnsTablet = (int) ($elementData['columns_tablet'] ?? 2);
$cardStyle     = $elementData['card_style'] ?? '';
$textAlign     = $elementData['text_align'] ?? 'left';
$container     = $elementData['container_width'] ?? 'container';
$sectionPad    = $elementData['section_padding'] ?? '';

if (empty($items)) {
    return;
}

$colMd  = (int) round(12 / $columns);
$colSm  = (int) round(12 / $columnsTablet);
$colClass = "col-sm-{$colSm} col-md-{$colMd}";
$alignClass = $textAlign === 'center' ? ' text-center' : '';

?>
<div class="<?= rex_escape($container ?: 'container') ?> <?= rex_escape($sectionPad) ?>">
    <div class="row g-4">
        <?php foreach ($items as $item): ?>
            <?php
            $itemIcon      = $item['icon'] ?? '';
            $itemUikitIcon = trim($item['icon_uikit'] ?? '');
            $itemHeading   = $item['heading'] ?? '';
            $itemText      = $item['text'] ?? '';
            $itemLinkUrl   = trim($item['link_url'] ?? '');
            $itemLinkText  = $item['link_text'] ?? 'Mehr';

            if (empty($itemHeading) && empty($itemText)) {
                continue;
            }
            ?>
            <div class="<?= $colClass ?>">
                <div class="<?= $cardStyle ? 'card h-100' : 'cb-feature-item h-100' ?><?= $alignClass ?>">
                    <?php if ($cardStyle): ?>
                        <div class="card-body<?= $alignClass ?>">
                    <?php endif; ?>

                    <?php if ($itemIcon): ?>
                        <div class="mb-3">
                            <img src="<?= rex_escape(rex_url::media($itemIcon)) ?>" alt="" aria-hidden="true"
                                 style="max-width:48px; max-height:48px; object-fit:contain;" loading="lazy">
                        </div>
                    <?php endif; ?>

                    <?php if ($itemHeading): ?>
                        <h3 class="h5"><?= rex_escape($itemHeading) ?></h3>
                    <?php endif; ?>

                    <?php if ($itemText): ?>
                        <p><?= nl2br(rex_escape($itemText)) ?></p>
                    <?php endif; ?>

                    <?php if ($itemLinkUrl && $itemLinkText): ?>
                        <a href="<?= rex_escape($itemLinkUrl) ?>" class="btn btn-link px-0">
                            <?= rex_escape($itemLinkText) ?> &rarr;
                        </a>
                    <?php endif; ?>

                    <?php if ($cardStyle): ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
