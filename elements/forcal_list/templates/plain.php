<?php

/** @var array<string,mixed> $elementData */

use KLXM\YFormContentBuilder\ForcalRenderer;
use KLXM\YFormContentBuilder\Starter\StarterConfig;

if (!class_exists(ForcalRenderer::class)) {
    return;
}

$result = ForcalRenderer::fetch($elementData);
$headline = (string) ($elementData['headline'] ?? '');
$description = (string) ($elementData['description'] ?? '');
$showLinks = !isset($elementData['show_links']) || !empty($elementData['show_links']);
$showCategoryColors = !empty($elementData['show_category_colors']);
$layout = (string) $result['layout'];
$items = (array) $result['items'];
$error = $result['error'];

$sectionBg = (string) ($elementData['section_bg'] ?? '');
$sectionPadding = (string) ($elementData['section_padding'] ?? '');
$containerWidth = (string) ($elementData['container_width'] ?? 'uk-container');
$sectionLight = !empty($elementData['section_light']);
$enableSection = !isset($elementData['enable_section']) || !empty($elementData['enable_section']);
$enableContainer = !isset($elementData['enable_container']) || !empty($elementData['enable_container']);

$sectionStyle = StarterConfig::mapBg($sectionBg, 'plain') . StarterConfig::mapPadding($sectionPadding, 'plain');
if ($sectionLight) {
    $sectionStyle .= 'color:#fff;';
}
$containerStyle = StarterConfig::mapContainer($containerWidth, 'plain');
?>
<?php if ($enableSection): ?><section<?= $sectionStyle !== '' ? ' style="' . rex_escape($sectionStyle) . '"' : '' ?>><?php endif; ?>
<?php if ($enableContainer): ?><div style="<?= rex_escape($containerStyle) ?>"><?php endif; ?>

<?php if ($headline !== ''): ?><h2><?= rex_escape($headline) ?></h2><?php endif; ?>
<?php if ($description !== ''): ?><p><?= nl2br(rex_escape($description)) ?></p><?php endif; ?>

<?php if ($error !== null): ?>
<p><?= rex_escape((string) $error) ?></p>
<?php elseif ($items === []): ?>
<p>Keine kommenden Termine.</p>
<?php elseif ($layout === 'cards'): ?>
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(min(260px,100%),1fr));gap:1rem;">
    <?php foreach ($items as $it): ?>
    <?php
    $title = rex_escape((string) ($it['title'] ?? ''));
    $teaser = rex_escape((string) ($it['teaser'] ?? ''));
    $href = $showLinks ? (string) ($it['href'] ?? '') : '';
    $dateStr = ForcalRenderer::formatDate($it);
    $imageUrl = (string) ($it['image_url'] ?? '');
    $categoryName = rex_escape((string) ($it['category_name'] ?? ''));
    $categoryColor = (string) ($it['category_color'] ?? '');
    $categoryHtml = '';
    if ($showCategoryColors && ($categoryName !== '' || $categoryColor !== '')) {
        $badgeStyle = 'display:inline-block;color:#fff;border-radius:999px;padding:.2rem .55rem;font-size:.75rem;line-height:1;';
        $badgeStyle .= $categoryColor !== '' ? 'background:' . rex_escape($categoryColor) . ';' : 'background:#6c757d;';
        $categoryHtml = '<div style="margin:.35rem 0 .15rem 0;"><span style="' . $badgeStyle . '">'
            . ($categoryName !== '' ? $categoryName : 'Kategorie') . '</span></div>';
    }
    ?>
    <article style="border:1px solid #ddd;border-radius:6px;overflow:hidden;<?= $showCategoryColors && $categoryColor !== '' ? 'border-top:4px solid ' . rex_escape($categoryColor) . ';' : '' ?>">
        <?php if ($imageUrl !== ''): ?><img src="<?= rex_escape($imageUrl) ?>" alt="" loading="lazy" style="width:100%;height:auto;display:block;"><?php endif; ?>
        <div style="padding:.9rem;">
            <div style="color:#666;font-size:.85rem;"><?= $dateStr ?></div>
            <?= $categoryHtml ?>
            <h3 style="margin:.35rem 0;"><?php if ($href !== ''): ?><a href="<?= rex_escape($href) ?>"><?= $title ?></a><?php else: ?><?= $title ?><?php endif; ?></h3>
            <?php if ($teaser !== ''): ?><p style="margin:0;"><?= $teaser ?></p><?php endif; ?>
        </div>
    </article>
    <?php endforeach; ?>
</div>
<?php else: ?>
<ul>
    <?php foreach ($items as $it): ?>
    <?php
    $title = rex_escape((string) ($it['title'] ?? ''));
    $teaser = rex_escape((string) ($it['teaser'] ?? ''));
    $href = $showLinks ? (string) ($it['href'] ?? '') : '';
    $dateStr = ForcalRenderer::formatDate($it);
    $categoryName = rex_escape((string) ($it['category_name'] ?? ''));
    $categoryColor = (string) ($it['category_color'] ?? '');
    $categoryHtml = '';
    if ($showCategoryColors && ($categoryName !== '' || $categoryColor !== '')) {
        $dotColor = $categoryColor !== '' ? $categoryColor : '#6c757d';
        $categoryHtml = '<span style="display:inline-block;width:.7rem;height:.7rem;border-radius:50%;background:' . rex_escape($dotColor) . ';margin-right:.45rem;vertical-align:middle;"></span>';
        if ($categoryName !== '') {
            $categoryHtml .= '<span style="font-size:.85rem;color:#555;">' . $categoryName . '</span>';
        }
    }
    ?>
    <li style="<?= $showCategoryColors && $categoryColor !== '' ? 'border-left:4px solid ' . rex_escape($categoryColor) . ';padding-left:.65rem;' : '' ?>"><small><?= $dateStr ?></small> <?php if ($href !== ''): ?><a href="<?= rex_escape($href) ?>"><?= $title ?></a><?php else: ?><?= $title ?><?php endif; ?><?php if ($categoryHtml !== ''): ?><div style="margin-top:.2rem;"><?= $categoryHtml ?></div><?php endif; ?><?php if ($layout === 'list' && $teaser !== ''): ?><div style="margin-top:.2rem;"><?= $teaser ?></div><?php endif; ?></li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if ($enableContainer): ?></div><?php endif; ?>
<?php if ($enableSection): ?></section><?php endif; ?>
