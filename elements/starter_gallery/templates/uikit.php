<?php
$headline = (string) ($elementData['headline'] ?? '');
$layout = (string) ($elementData['layout'] ?? 'grid');
$items = $elementData['items'] ?? [];

$columns = (string) ($elementData['columns'] ?? '3');
$columnsTablet = (string) ($elementData['columns_tablet'] ?? '2');
$columnsMobile = (string) ($elementData['columns_mobile'] ?? '1');
$gap = (string) ($elementData['gap'] ?? 'medium');

$sectionBg = (string) ($elementData['section_bg'] ?? '');
$sectionPadding = (string) ($elementData['section_padding'] ?? '');
$containerWidth = (string) ($elementData['container_width'] ?? 'uk-container');
$sectionLight = !empty($elementData['section_light']);

if (!is_array($items) || $items === []) {
    return;
}

$sectionClasses = ['uk-section'];
if ($sectionBg !== '') {
    $sectionClasses[] = $sectionBg;
}
if ($sectionPadding !== '') {
    $sectionClasses[] = $sectionPadding;
}
if ($sectionLight) {
    $sectionClasses[] = 'uk-light';
}

$gridClass = 'uk-child-width-1-' . $columnsMobile;
$gridClass .= ' uk-child-width-1-' . $columnsTablet . '@s';
$gridClass .= ' uk-child-width-1-' . $columns . '@m';
$gapClass = $gap === 'collapse' ? '' : 'uk-grid-' . $gap;
$gridAttr = $layout === 'masonry' ? 'masonry: true' : '';
?>
<section class="<?= rex_escape(implode(' ', $sectionClasses)) ?>">
    <?php if ($containerWidth !== ''): ?><div class="<?= rex_escape($containerWidth) ?>"><?php endif; ?>
        <?php if ($headline !== ''): ?><h3><?= rex_escape($headline) ?></h3><?php endif; ?>
        <div class="<?= rex_escape(trim($gridClass . ' ' . $gapClass)) ?>" uk-grid<?= $gridAttr !== '' ? '="' . rex_escape($gridAttr) . '"' : '' ?>>
            <?php foreach ($items as $index => $item): ?>
                <?php $img = (string) ($item['image'] ?? ''); if ($img === '') { continue; } ?>
                <?php
                $caption = (string) ($item['caption'] ?? '');
                $fallback = $headline !== '' ? $headline . ' ' . ($index + 1) : 'Galeriebild ' . ($index + 1);
                $imageAlt = YFormContentBuilderMediaAltResolver::resolve($img, $caption, $fallback);
                ?>
                <div>
                    <figure class="uk-margin-remove">
                        <img src="<?= rex_url::media($img) ?>" alt="<?= rex_escape($imageAlt) ?>" class="uk-width-1-1" loading="lazy">
                        <?php if ($caption !== ''): ?>
                            <figcaption class="uk-text-small uk-margin-small-top"><?= rex_escape($caption) ?></figcaption>
                        <?php endif; ?>
                    </figure>
                </div>
            <?php endforeach; ?>
        </div>
    <?php if ($containerWidth !== ''): ?></div><?php endif; ?>
</section>
