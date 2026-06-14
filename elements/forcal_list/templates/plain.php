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
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:1rem;">
    <?php foreach ($items as $it): ?>
    <?php
    $title = rex_escape((string) ($it['title'] ?? ''));
    $teaser = rex_escape((string) ($it['teaser'] ?? ''));
    $href = $showLinks ? (string) ($it['href'] ?? '') : '';
    $dateStr = ForcalRenderer::formatDate($it);
    $imageUrl = (string) ($it['image_url'] ?? '');
    ?>
    <article style="border:1px solid #ddd;border-radius:6px;overflow:hidden;">
        <?php if ($imageUrl !== ''): ?><img src="<?= rex_escape($imageUrl) ?>" alt="" loading="lazy" style="width:100%;height:auto;display:block;"><?php endif; ?>
        <div style="padding:.9rem;">
            <div style="color:#666;font-size:.85rem;"><?= $dateStr ?></div>
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
    $href = $showLinks ? (string) ($it['href'] ?? '') : '';
    $dateStr = ForcalRenderer::formatDate($it);
    ?>
    <li><small><?= $dateStr ?></small> <?php if ($href !== ''): ?><a href="<?= rex_escape($href) ?>"><?= $title ?></a><?php else: ?><?= $title ?><?php endif; ?></li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if ($enableContainer): ?></div><?php endif; ?>
<?php if ($enableSection): ?></section><?php endif; ?>
