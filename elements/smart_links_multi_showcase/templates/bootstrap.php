<?php

/**
 * Smart-Links Multi Showcase - Bootstrap Template
 *
 * @var array $elementData
 */

$headline = trim((string) ($elementData['headline'] ?? ''));
$intro = trim((string) ($elementData['intro'] ?? ''));
$showPreview = (bool) ($elementData['show_preview'] ?? false);
$rawLinks = $elementData['links'] ?? '';

$columns = (int) ($elementData['columns'] ?? 3);
$columnsTablet = (int) ($elementData['columns_tablet'] ?? 2);
$containerWidth = (string) ($elementData['container_width'] ?? 'uk-container');
$sectionPadding = (string) ($elementData['section_padding'] ?? '');

$smartLinkClass = \KLXM\YFormContentBuilder\SmartLink::class;
$smartLinkViewClass = \KLXM\YFormContentBuilder\SmartLinkView::class;
$links = $smartLinkClass::normalize($rawLinks, true);

if ($links === []) {
    return;
}

$colMd = max(1, (int) round(12 / max(1, $columns)));
$colSm = max(1, (int) round(12 / max(1, $columnsTablet)));
$colClass = 'col-sm-' . $colSm . ' col-md-' . $colMd;

$containerMap = [
    'uk-container' => 'container',
    'uk-container uk-container-xsmall' => 'container',
    'uk-container uk-container-small' => 'container',
    'uk-container uk-container-large' => 'container-lg',
    'uk-container uk-container-xlarge' => 'container-xl',
    'uk-container uk-container-expand' => 'container-fluid',
    '' => 'container-fluid',
];
$containerClass = $containerMap[$containerWidth] ?? 'container';

$paddingMap = [
    'uk-padding-remove' => 'py-0',
    'uk-padding-small' => 'py-2',
    'uk-padding' => 'py-4',
    'uk-padding-large' => 'py-5',
    '' => '',
];
$sectionPadClass = $paddingMap[$sectionPadding] ?? '';

?>
<section class="<?= rex_escape($sectionPadClass) ?>">
    <div class="<?= rex_escape($containerClass) ?>">
        <?php if ($headline !== ''): ?>
            <h2 class="mb-3"><?= rex_escape($headline) ?></h2>
        <?php endif; ?>

        <?php if ($intro !== ''): ?>
            <p class="lead mb-4"><?= nl2br(rex_escape($intro)) ?></p>
        <?php endif; ?>

        <div class="row g-4 align-items-stretch">
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
                <div class="<?= rex_escape($colClass) ?> d-flex">
                    <article class="card h-100 shadow-sm w-100">
                        <div class="card-img-top d-flex align-items-center justify-content-center bg-light overflow-hidden" style="aspect-ratio:16 / 9;">
                            <?php if ($showPreview && $previewData !== null): ?>
                                <?php if ($previewData['kind'] === 'video'): ?>
                                    <video class="w-100 h-100" src="<?= rex_escape($previewData['src']) ?>" controls preload="metadata" style="object-fit:cover;"></video>
                                <?php else: ?>
                                    <img class="w-100 h-100" src="<?= rex_escape($previewData['src']) ?>" alt="" loading="lazy" style="object-fit:cover;">
                                <?php endif; ?>
                            <?php else: ?>
                                <i class="fa <?= rex_escape($typeMeta['fa_icon']) ?> fa-2x text-muted" aria-hidden="true"></i>
                            <?php endif; ?>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fa <?= rex_escape($typeMeta['fa_icon']) ?>" aria-hidden="true"></i>
                                    <strong><?= rex_escape($resolved['label']) ?></strong>
                                </div>
                                <span class="badge bg-light text-dark border"><?= rex_escape($typeMeta['label']) ?></span>
                            </div>

                            <a class="btn btn-outline-primary mt-auto" href="<?= rex_escape($resolved['href']) ?>"<?= $target ?>>
                                <?= rex_escape($resolved['label']) ?>
                            </a>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
