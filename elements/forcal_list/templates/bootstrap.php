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
$columns = max(1, min(6, (int) ($elementData['columns'] ?? 3)));
$columnsTablet = max(1, min(4, (int) ($elementData['columns_tablet'] ?? 2)));
$columnsMobile = max(1, min(2, (int) ($elementData['columns_mobile'] ?? 1)));

$sectionClass = trim(StarterConfig::mapBg($sectionBg, 'bootstrap') . ' ' . StarterConfig::mapPadding($sectionPadding, 'bootstrap'));
if ($sectionLight) {
    $sectionClass = trim($sectionClass . ' text-white');
}
$containerClass = trim(StarterConfig::mapContainer($containerWidth, 'bootstrap'));
$rowClass = sprintf('row g-3 row-cols-%d row-cols-md-%d row-cols-lg-%d', $columnsMobile, $columnsTablet, $columns);
?>
<?php if ($enableSection): ?><section<?= $sectionClass !== '' ? ' class="' . rex_escape($sectionClass) . '"' : '' ?>><?php endif; ?>
<?php if ($enableContainer): ?><div<?= $containerClass !== '' ? ' class="' . rex_escape($containerClass) . '"' : '' ?>><?php endif; ?>

<?php if ($headline !== ''): ?><h2 class="mb-3"><?= rex_escape($headline) ?></h2><?php endif; ?>
<?php if ($description !== ''): ?><p class="lead"><?= nl2br(rex_escape($description)) ?></p><?php endif; ?>

<?php if ($error !== null): ?>
<div class="alert alert-warning"><?= rex_escape((string) $error) ?></div>
<?php elseif ($items === []): ?>
<div class="alert alert-light">Keine kommenden Termine.</div>
<?php elseif ($layout === 'cards'): ?>
<div class="<?= rex_escape($rowClass) ?>">
    <?php foreach ($items as $it): ?>
    <?php
    $title = rex_escape((string) ($it['title'] ?? ''));
    $teaser = rex_escape((string) ($it['teaser'] ?? ''));
    $href = $showLinks ? (string) ($it['href'] ?? '') : '';
    $dateStr = ForcalRenderer::formatDate($it);
    $imageUrl = (string) ($it['image_url'] ?? '');
    $categoryName = rex_escape((string) ($it['category_name'] ?? ''));
    $categoryColor = (string) ($it['category_color'] ?? '');
    $showCategoryBadge = $showCategoryColors && ($categoryName !== '' || $categoryColor !== '');
    ?>
    <div class="col">
        <div class="card h-100"<?= $showCategoryColors && $categoryColor !== '' ? ' style="border-top:4px solid ' . rex_escape($categoryColor) . ';"' : '' ?>>
            <?php if ($imageUrl !== ''): ?><img class="card-img-top" src="<?= rex_escape($imageUrl) ?>" alt="" loading="lazy"><?php endif; ?>
            <div class="card-body">
                <div class="text-muted small mb-1"><?= $dateStr ?></div>
                <?php if ($showCategoryBadge): ?>
                    <div class="mb-2"><span class="badge<?= $categoryColor === '' ? ' text-bg-secondary' : '' ?>"<?= $categoryColor !== '' ? ' style="background: ' . rex_escape($categoryColor) . ';"' : '' ?>><?= $categoryName !== '' ? $categoryName : 'Kategorie' ?></span></div>
                <?php endif; ?>
                <h3 class="h5 card-title"><?php if ($href !== ''): ?><a href="<?= rex_escape($href) ?>" class="text-decoration-none"><?= $title ?></a><?php else: ?><?= $title ?><?php endif; ?></h3>
                <?php if ($teaser !== ''): ?><p class="card-text"><?= $teaser ?></p><?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php elseif ($layout === 'list'): ?>
<ul class="list-group list-group-flush">
    <?php foreach ($items as $it): ?>
    <?php
    $title = rex_escape((string) ($it['title'] ?? ''));
    $teaser = rex_escape((string) ($it['teaser'] ?? ''));
    $href = $showLinks ? (string) ($it['href'] ?? '') : '';
    $dateStr = ForcalRenderer::formatDate($it);
    $categoryName = rex_escape((string) ($it['category_name'] ?? ''));
    $categoryColor = (string) ($it['category_color'] ?? '');
    $showCategoryBadge = $showCategoryColors && ($categoryName !== '' || $categoryColor !== '');
    ?>
    <li class="list-group-item px-0"<?= $showCategoryColors && $categoryColor !== '' ? ' style="border-left:4px solid ' . rex_escape($categoryColor) . ';padding-left:1rem !important;"' : '' ?>>
        <div class="text-muted small"><?= $dateStr ?></div>
        <?php if ($showCategoryBadge): ?>
            <div class="small mb-1"><span class="badge<?= $categoryColor === '' ? ' text-bg-secondary' : '' ?>"<?= $categoryColor !== '' ? ' style="background: ' . rex_escape($categoryColor) . ';"' : '' ?>><?= $categoryName !== '' ? $categoryName : 'Kategorie' ?></span></div>
        <?php endif; ?>
        <h4 class="h6 mb-1"><?php if ($href !== ''): ?><a href="<?= rex_escape($href) ?>"><?= $title ?></a><?php else: ?><?= $title ?><?php endif; ?></h4>
        <?php if ($teaser !== ''): ?><p class="mb-0"><?= $teaser ?></p><?php endif; ?>
    </li>
    <?php endforeach; ?>
</ul>
<?php else: ?>
<ul class="list-unstyled mb-0">
    <?php foreach ($items as $it): ?>
    <?php
    $title = rex_escape((string) ($it['title'] ?? ''));
    $href = $showLinks ? (string) ($it['href'] ?? '') : '';
    $dateStr = ForcalRenderer::formatDate($it);
    $categoryName = rex_escape((string) ($it['category_name'] ?? ''));
    $categoryColor = (string) ($it['category_color'] ?? '');
    ?>
    <li class="mb-2"><small class="text-muted me-2"><?= $dateStr ?></small><?php if ($showCategoryColors && $categoryColor !== ''): ?><span class="me-2 align-middle" style="display:inline-block;width:.7rem;height:.7rem;border-radius:50%;background:<?= rex_escape($categoryColor) ?>;"></span><?php endif; ?><?php if ($href !== ''): ?><a href="<?= rex_escape($href) ?>"><?= $title ?></a><?php else: ?><?= $title ?><?php endif; ?><?php if ($showCategoryColors && $categoryName !== ''): ?><small class="text-muted ms-2"><?= $categoryName ?></small><?php endif; ?></li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if ($enableContainer): ?></div><?php endif; ?>
<?php if ($enableSection): ?></section><?php endif; ?>
