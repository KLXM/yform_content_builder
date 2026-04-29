<?php
$headline = (string) ($elementData['headline'] ?? '');
$items = $elementData['items'] ?? [];
$columns = max(1, min(6, (int) ($elementData['columns'] ?? 3)));
$gap = (string) ($elementData['gap'] ?? 'medium');

$sectionBg = (string) ($elementData['section_bg'] ?? '');
$sectionPadding = (string) ($elementData['section_padding'] ?? '');
$containerWidth = (string) ($elementData['container_width'] ?? 'uk-container');
$sectionLight = !empty($elementData['section_light']);

if (!is_array($items) || $items === []) { return; }

$bgMap = ['uk-background-default' => '#ffffff', 'uk-background-muted' => '#f7f7f7', 'uk-background-primary' => '#1e87f0', 'uk-background-secondary' => '#222222'];
$paddingMap = ['uk-padding-remove' => '0', 'uk-padding-small' => '18px 0', 'uk-padding' => '35px 0', 'uk-padding-large' => '55px 0'];
$sectionStyle = '';
if (isset($bgMap[$sectionBg])) { $sectionStyle .= 'background:' . $bgMap[$sectionBg] . ';'; }
if (isset($paddingMap[$sectionPadding])) { $sectionStyle .= 'padding:' . $paddingMap[$sectionPadding] . ';'; }
if ($sectionLight) { $sectionStyle .= 'color:#fff;'; }

$containerStyle = 'max-width:1140px;margin:0 auto;padding:0 15px;';
if ($containerWidth === '') { $containerStyle = 'padding:0 15px;'; }
$gapPx = $gap === 'collapse' ? '0px' : ($gap === 'small' ? '8px' : ($gap === 'large' ? '24px' : '15px'));
?>
<section<?= $sectionStyle !== '' ? ' style="' . rex_escape($sectionStyle) . '"' : '' ?>>
    <div style="<?= rex_escape($containerStyle) ?>">
        <?php if ($headline !== ''): ?><h3><?= rex_escape($headline) ?></h3><?php endif; ?>
        <ul style="list-style:none; margin:0; padding:0; display:grid; grid-template-columns: repeat(<?= $columns ?>, minmax(0, 1fr)); gap:<?= rex_escape($gapPx) ?>;">
            <?php foreach ($items as $index => $item): ?>
                <?php
                $itemTitle = (string) ($item['title'] ?? '');
                $itemText = (string) ($item['text'] ?? '');
                $itemLinkText = trim((string) ($item['link_text'] ?? 'Mehr erfahren'));
                if ($itemLinkText === '') {
                    $itemLinkText = 'Mehr erfahren';
                }
                $fallback = $headline !== '' ? $headline . ' ' . ($index + 1) : 'Karte ' . ($index + 1);
                $itemImageAlt = \KLXM\YFormContentBuilder\MediaAltResolver::resolve((string) ($item['image'] ?? ''), '', $itemTitle !== '' ? $itemTitle : $fallback);
                ?>
                <li>
                    <article style="border:1px solid #ddd; background:#fff; height:100%;">
                        <?php if (!empty($item['image'])): ?>
                            <img src="<?= rex_url::media((string) $item['image']) ?>" alt="<?= rex_escape($itemImageAlt) ?>" loading="lazy" style="max-width:100%;height:auto;display:block;">
                        <?php endif; ?>
                        <div style="padding:12px;">
                            <?php if ($itemTitle !== ''): ?><h4 style="margin:0 0 8px;"><?= rex_escape($itemTitle) ?></h4><?php endif; ?>
                            <?php if (trim(strip_tags($itemText)) !== ''): ?><div style="margin:0 0 8px;"><?= $itemText ?></div><?php endif; ?>
                            <?php if (!empty($item['link_url'])): ?><a href="<?= rex_escape((string) $item['link_url']) ?>" aria-label="<?= rex_escape($itemLinkText . ($itemTitle !== '' ? ' - ' . $itemTitle : '')) ?>"><?= rex_escape($itemLinkText) ?></a><?php endif; ?>
                        </div>
                    </article>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
