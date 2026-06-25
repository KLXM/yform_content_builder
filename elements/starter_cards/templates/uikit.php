<?php
use KLXM\YFormContentBuilder\Config\MediaTypeRegistry;

$headline    = (string) ($elementData['headline'] ?? '');
$items       = $elementData['items'] ?? [];
$cardStyle   = (string) ($elementData['card_style'] ?? 'default');
$imageRatio  = (string) ($elementData['image_ratio'] ?? '16_9');
$imageRatioMobile = (string) ($elementData['image_ratio_mobile'] ?? '');
$columns     = (string) ($elementData['columns'] ?? '3');
$columnsTablet = (string) ($elementData['columns_tablet'] ?? '2');
$columnsMobile = (string) ($elementData['columns_mobile'] ?? '1');
$gap         = (string) ($elementData['gap'] ?? 'medium');

$sectionBg       = (string) ($elementData['section_bg'] ?? '');
$sectionBgImage  = (string) ($elementData['section_bg_image'] ?? '');
$sectionPadding  = (string) ($elementData['section_padding'] ?? '');
$containerWidth  = (string) ($elementData['container_width'] ?? 'uk-container');
$sectionLight    = !empty($elementData['section_light']);
$enableSection   = !isset($elementData['enable_section']) || !empty($elementData['enable_section']);
$enableContainer = !isset($elementData['enable_container']) || !empty($elementData['enable_container']);

if (!is_array($items) || $items === []) {
    return;
}

$cardStyleMap = [
    'default'     => 'uk-card-default',
    'primary'     => 'uk-card-primary',
    'secondary'   => 'uk-card-secondary',
    'muted'       => 'uk-card-body uk-background-muted',
    'hover'       => 'uk-card-default uk-card-hover',
    'transparent' => 'uk-card-body',
];
$ukCardClass = $cardStyleMap[$cardStyle] ?? 'uk-card-default';

/** @param array<string,mixed> $item @return array{href:string,target:string,text:string} */
$resolveLink = static function (array $item): array {
    $type   = (string) ($item['link_type'] ?? '');
    $target = (string) ($item['link_target'] ?? '');
    $text   = trim((string) ($item['link_text'] ?? 'Mehr erfahren'));
    if ($text === '') { $text = 'Mehr erfahren'; }

    if ($type === 'external') {
        return ['href' => (string) ($item['link_url'] ?? ''), 'target' => $target, 'text' => $text];
    }
    if ($type === 'internal') {
        $id = (int) ($item['link_internal'] ?? 0);
        return ['href' => $id > 0 ? rex_getUrl($id) : '', 'target' => $target, 'text' => $text];
    }
    // legacy: direct link_url without link_type
    if (!empty($item['link_url'])) {
        return ['href' => (string) $item['link_url'], 'target' => '', 'text' => $text];
    }
    return ['href' => '', 'target' => '', 'text' => $text];
};

$wrapper = new rex_fragment();
$wrapper->setVar('enable_section', $enableSection, false);
$wrapper->setVar('enable_container', $enableContainer, false);
$wrapper->setVar('section_bg', $sectionBg, false);
$wrapper->setVar('section_bg_image', $sectionBgImage, false);
$wrapper->setVar('section_padding', $sectionPadding, false);
$wrapper->setVar('container_width', $containerWidth, false);
$wrapper->setVar('section_light', $sectionLight, false);

$wrapperClose = new rex_fragment();
$wrapperClose->setVar('mode', 'close', false);
$wrapperClose->setVar('enable_section', $enableSection, false);
$wrapperClose->setVar('enable_container', $enableContainer, false);
$wrapperClose->setVar('section_bg_image', $sectionBgImage, false);
$wrapperClose->setVar('container_width', $containerWidth, false);

$gridClass  = 'uk-child-width-1-' . $columnsMobile;
$gridClass .= ' uk-child-width-1-' . $columnsTablet . '@s';
$gridClass .= ' uk-child-width-1-' . $columns . '@m';
$gapClass   = $gap === 'collapse' ? '' : 'uk-grid-' . $gap;
$estimateContainerMaxPx = static function (string $container): int {
    if (str_contains($container, 'xsmall')) {
        return 640;
    }
    if (str_contains($container, 'small')) {
        return 900;
    }
    if (str_contains($container, 'xlarge')) {
        return 1600;
    }
    if (str_contains($container, 'large')) {
        return 1400;
    }
    if (str_contains($container, 'expand') || $container === '') {
        return 1920;
    }

    return 1200;
};
$containerMaxPx = $estimateContainerMaxPx($containerWidth);
$desktopPx = (int) max(220, round($containerMaxPx / max(1, (int) $columns)));
$tabletVw = (int) max(20, min(100, floor(100 / max(1, (int) $columnsTablet))));
$mobileVw = (int) max(30, min(100, floor(100 / max(1, (int) $columnsMobile))));
$mobileArtDirectionActive = $imageRatioMobile !== '' && $imageRatioMobile !== $imageRatio;

/** @return array{src:string,srcset:string} */
$resolveImage = static function (string $image, string $ratio): array {
    if ($image === '') {
        return ['src' => '', 'srcset' => ''];
    }

    if (!rex_addon::get('media_manager')->isAvailable()) {
        return ['src' => rex_url::media($image), 'srcset' => ''];
    }

    $preset = 'starter_cards_' . $ratio;
    $widths = [400, 800, 1200, 1600];
    $src = rex_media_manager::getUrl(MediaTypeRegistry::buildVirtualType($preset, 1200), $image);
    $srcset = [];
    foreach ($widths as $w) {
        $srcset[] = rex_media_manager::getUrl(MediaTypeRegistry::buildVirtualType($preset, $w), $image) . ' ' . $w . 'w';
    }

    return ['src' => $src, 'srcset' => implode(', ', $srcset)];
};
?>
<?= $wrapper->parse('ycb_elements/wrapper.php') ?>
<?php if ($headline !== ''): ?><h3><?= rex_escape($headline) ?></h3><?php endif; ?>
<div class="<?= rex_escape(trim($gridClass . ' ' . $gapClass)) ?>" uk-grid>
    <?php foreach ($items as $index => $item): ?>
        <?php
        $itemTitle = (string) ($item['title'] ?? '');
        $itemText  = (string) ($item['text'] ?? '');
        $link      = $resolveLink($item);
        $fallback  = $headline !== '' ? $headline . ' ' . ($index + 1) : 'Karte ' . ($index + 1);
        $imageAlt  = \KLXM\YFormContentBuilder\MediaAltResolver::resolve((string) ($item['image'] ?? ''), '', $itemTitle !== '' ? $itemTitle : $fallback);
        $imageInfo = $resolveImage((string) ($item['image'] ?? ''), $imageRatio);
        $imageInfoMobile = $mobileArtDirectionActive
            ? $resolveImage((string) ($item['image'] ?? ''), $imageRatioMobile)
            : ['src' => '', 'srcset' => ''];
        ?>
        <div>
            <article class="uk-card <?= rex_escape($ukCardClass) ?> uk-card-small uk-height-1-1">
                <?php if ($imageInfo['src'] !== ''): ?>
                    <div class="uk-card-media-top">
                        <picture>
                            <?php if ($mobileArtDirectionActive && $imageInfoMobile['src'] !== ''): ?>
                                <source
                                    media="(max-width: 639px)"
                                    <?= $imageInfoMobile['srcset'] !== '' ? 'srcset="' . rex_escape($imageInfoMobile['srcset']) . '"' : 'srcset="' . rex_escape($imageInfoMobile['src']) . '"' ?>
                                    sizes="<?= $mobileVw ?>vw"
                                >
                            <?php endif; ?>
                            <img
                                src="<?= rex_escape($imageInfo['src']) ?>"
                                <?= $imageInfo['srcset'] !== '' ? 'srcset="' . rex_escape($imageInfo['srcset']) . '"' : '' ?>
                                sizes="(min-width: 1200px) <?= $desktopPx ?>px, (min-width: 640px) <?= $tabletVw ?>vw, <?= $mobileVw ?>vw"
                                alt="<?= rex_escape($imageAlt) ?>"
                                class="uk-width-1-1"
                                loading="lazy"
                            >
                        </picture>
                    </div>
                <?php endif; ?>
                <div class="uk-card-body">
                    <?php if ($itemTitle !== ''): ?><h4 class="uk-margin-small-bottom"><?= rex_escape($itemTitle) ?></h4><?php endif; ?>
                    <?php if (trim(strip_tags($itemText)) !== ''): ?><div><?= $itemText ?></div><?php endif; ?>
                    <?php if ($link['href'] !== ''): ?>
                        <a href="<?= rex_escape($link['href']) ?>"
                           class="uk-button uk-button-text uk-margin-small-top"
                           <?= $link['target'] !== '' ? 'target="' . rex_escape($link['target']) . '" rel="noopener"' : '' ?>
                           aria-label="<?= rex_escape($link['text'] . ($itemTitle !== '' ? ' – ' . $itemTitle : '')) ?>">
                            <?= rex_escape($link['text']) ?>
                        </a>
                    <?php endif; ?>
                </div>
            </article>
        </div>
    <?php endforeach; ?>
</div>
<?= $wrapperClose->parse('ycb_elements/wrapper.php') ?>
