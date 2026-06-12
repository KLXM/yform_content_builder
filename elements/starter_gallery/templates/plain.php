<?php
$headline = (string) ($elementData['headline'] ?? '');
$layout = (string) ($elementData['layout'] ?? 'grid');
$rawItems = $elementData['items'] ?? [];

$columns = (int) ($elementData['columns'] ?? 3);
$gap = (string) ($elementData['gap'] ?? 'medium');

$sectionBg = (string) ($elementData['section_bg'] ?? '');
$sectionPadding = (string) ($elementData['section_padding'] ?? '');
$containerWidth = (string) ($elementData['container_width'] ?? 'uk-container');
$sectionLight = !empty($elementData['section_light']);

$items = [];
if (is_string($rawItems)) {
    $decodedItems = json_decode($rawItems, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $decodedItems = json_decode(html_entity_decode($rawItems, ENT_QUOTES | ENT_HTML5, 'UTF-8'), true);
    }
    if (is_array($decodedItems)) {
        $rawItems = $decodedItems;
    }
}

if (is_array($rawItems)) {
    if (isset($rawItems['image'])) {
        $items = [$rawItems];
    } elseif (array_is_list($rawItems)) {
        $items = $rawItems;
    } else {
        foreach ($rawItems as $maybeItem) {
            if (is_array($maybeItem)) {
                $items[] = $maybeItem;
            }
        }
    }
}

if (!is_array($items) || $items === []) {
    return;
}

$bgMap = [
    'uk-background-default' => '#ffffff',
    'uk-background-muted' => '#f7f7f7',
    'uk-background-primary' => '#1e87f0',
    'uk-background-secondary' => '#222222',
];
$paddingMap = [
    'uk-padding-remove' => '0',
    'uk-padding-small' => '18px 0',
    'uk-padding' => '35px 0',
    'uk-padding-large' => '55px 0',
];
$sectionStyle = '';
if (isset($bgMap[$sectionBg])) {
    $sectionStyle .= 'background:' . $bgMap[$sectionBg] . ';';
}
if (isset($paddingMap[$sectionPadding])) {
    $sectionStyle .= 'padding:' . $paddingMap[$sectionPadding] . ';';
}
if ($sectionLight) {
    $sectionStyle .= 'color:#fff;';
}
$containerStyle = 'max-width:1140px;margin:0 auto;padding:0 15px;';
if ($containerWidth === '') {
    $containerStyle = 'padding:0 15px;';
}
$gapPx = $gap === 'collapse' ? '0px' : ($gap === 'small' ? '8px' : ($gap === 'large' ? '24px' : '15px'));
?>
<section<?= $sectionStyle !== '' ? ' style="' . rex_escape($sectionStyle) . '"' : '' ?>>
    <div style="<?= rex_escape($containerStyle) ?>">
        <?php if ($headline !== ''): ?><h3><?= rex_escape($headline) ?></h3><?php endif; ?>
        <?php if ($layout === 'masonry'): ?>
            <ul style="list-style:none; padding:0; margin:0; column-count:<?= max(1, min(6, $columns)) ?>; column-gap:<?= rex_escape($gapPx) ?>;">
                <?php foreach ($items as $index => $item): ?>
                    <?php
                    if (!is_array($item)) {
                        continue;
                    }
                    $imageRaw = $item['image'] ?? '';
                    $img = '';
                    if (is_string($imageRaw)) {
                        $img = $imageRaw;
                    } elseif (is_array($imageRaw)) {
                        if (isset($imageRaw['value']) && is_string($imageRaw['value'])) {
                            $img = $imageRaw['value'];
                        } else {
                            $firstImage = reset($imageRaw);
                            if (is_string($firstImage)) {
                                $img = $firstImage;
                            }
                        }
                    }
                    if ($img === '') {
                        continue;
                    }
                    ?>
                    <?php
                    $caption = (string) ($item['caption'] ?? '');
                    $fallback = $headline !== '' ? $headline . ' ' . ($index + 1) : 'Galeriebild ' . ($index + 1);
                    $imageAlt = \KLXM\YFormContentBuilder\MediaAltResolver::resolve($img, $caption, $fallback);
                    ?>
                    <li style="break-inside: avoid; margin:0 0 <?= rex_escape($gapPx) ?> 0;">
                        <figure style="margin:0;">
                            <img src="<?= rex_url::media($img) ?>" alt="<?= rex_escape($imageAlt) ?>" loading="lazy" style="max-width:100%; height:auto; display:block;">
                            <?php if ($caption !== ''): ?>
                                <figcaption style="font-size:12px; margin-top:6px;"><?= rex_escape($caption) ?></figcaption>
                            <?php endif; ?>
                        </figure>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <ul style="list-style:none; padding:0; margin:0; display:grid; grid-template-columns: repeat(<?= max(1, min(6, $columns)) ?>, minmax(0, 1fr)); gap:<?= rex_escape($gapPx) ?>;">
                <?php foreach ($items as $index => $item): ?>
                    <?php
                    if (!is_array($item)) {
                        continue;
                    }
                    $imageRaw = $item['image'] ?? '';
                    $img = '';
                    if (is_string($imageRaw)) {
                        $img = $imageRaw;
                    } elseif (is_array($imageRaw)) {
                        if (isset($imageRaw['value']) && is_string($imageRaw['value'])) {
                            $img = $imageRaw['value'];
                        } else {
                            $firstImage = reset($imageRaw);
                            if (is_string($firstImage)) {
                                $img = $firstImage;
                            }
                        }
                    }
                    if ($img === '') {
                        continue;
                    }
                    ?>
                    <?php
                    $caption = (string) ($item['caption'] ?? '');
                    $fallback = $headline !== '' ? $headline . ' ' . ($index + 1) : 'Galeriebild ' . ($index + 1);
                    $imageAlt = \KLXM\YFormContentBuilder\MediaAltResolver::resolve($img, $caption, $fallback);
                    ?>
                    <li>
                        <figure style="margin:0;">
                            <img src="<?= rex_url::media($img) ?>" alt="<?= rex_escape($imageAlt) ?>" loading="lazy" style="max-width:100%; height:auto; display:block;">
                            <?php if ($caption !== ''): ?>
                                <figcaption style="font-size:12px; margin-top:6px;"><?= rex_escape($caption) ?></figcaption>
                            <?php endif; ?>
                        </figure>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</section>
