<?php
use KLXM\YFormContentBuilder\Starter\StarterConfig;

$headline    = (string) ($elementData['headline'] ?? '');
$items       = $elementData['items'] ?? [];
$cardStyle   = (string) ($elementData['card_style'] ?? 'default');
$columns     = (int) ($elementData['columns'] ?? 3);
$sectionBg   = (string) ($elementData['section_bg'] ?? '');
$sectionPadding  = (string) ($elementData['section_padding'] ?? '');
$containerWidth  = (string) ($elementData['container_width'] ?? 'uk-container');
$sectionLight    = !empty($elementData['section_light']);
$enableSection   = !isset($elementData['enable_section']) || !empty($elementData['enable_section']);
$enableContainer = !isset($elementData['enable_container']) || !empty($elementData['enable_container']);

if (!is_array($items) || $items === []) { return; }

$sectionClasses = array_filter([
    StarterConfig::mapBg($sectionBg, 'bootstrap'),
    StarterConfig::mapPadding($sectionPadding, 'bootstrap'),
    $sectionLight ? 'text-white' : '',
]);
$sectionClassStr = implode(' ', $sectionClasses);
$containerClass  = StarterConfig::mapContainer($containerWidth, 'bootstrap');

$cardBorderMap = [
    'default'     => 'border:1px solid #ddd;background:#fff;',
    'primary'     => 'background:#1e87f0;color:#fff;border:none;',
    'secondary'   => 'background:#222;color:#fff;border:none;',
    'muted'       => 'background:#f8f8f8;border:1px solid #eee;',
    'hover'       => 'border:1px solid #ddd;background:#fff;',
    'transparent' => '',
];
$cardStyleAttr = $cardBorderMap[$cardStyle] ?? 'border:1px solid #ddd;background:#fff;';

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
    if (!empty($item['link_url'])) {
        return ['href' => (string) $item['link_url'], 'target' => '', 'text' => $text];
    }
    return ['href' => '', 'target' => '', 'text' => $text];
};

$colMap = [1 => 'col-sm-12', 2 => 'col-sm-6', 3 => 'col-sm-4', 4 => 'col-sm-3', 5 => 'col-sm-2', 6 => 'col-sm-2'];
$colClass = $colMap[$columns] ?? 'col-sm-4';
?>
<?php if ($enableSection): ?>
<section<?= $sectionClassStr !== '' ? ' class="' . rex_escape($sectionClassStr) . '"' : '' ?>>
<?php endif; ?>
<?php if ($enableContainer && $containerClass !== ''): ?><div class="<?= rex_escape($containerClass) ?>"><?php endif; ?>
    <?php if ($headline !== ''): ?><h3><?= rex_escape($headline) ?></h3><?php endif; ?>
    <div class="row">
        <?php foreach ($items as $index => $item): ?>
            <?php
            $itemTitle = (string) ($item['title'] ?? '');
            $itemText  = (string) ($item['text'] ?? '');
            $link      = $resolveLink($item);
            $fallback  = $headline !== '' ? $headline . ' ' . ($index + 1) : 'Karte ' . ($index + 1);
            $imageAlt  = \KLXM\YFormContentBuilder\MediaAltResolver::resolve((string) ($item['image'] ?? ''), '', $itemTitle !== '' ? $itemTitle : $fallback);
            ?>
            <div class="<?= rex_escape($colClass) ?>" style="margin-bottom:15px;">
                <article style="height:100%;<?= rex_escape($cardStyleAttr) ?>">
                    <?php if (!empty($item['image'])): ?>
                        <img src="<?= rex_url::media((string) $item['image']) ?>" alt="<?= rex_escape($imageAlt) ?>" class="img-responsive" loading="lazy">
                    <?php endif; ?>
                    <div style="padding:12px;">
                        <?php if ($itemTitle !== ''): ?><h4 style="margin-top:0;"><?= rex_escape($itemTitle) ?></h4><?php endif; ?>
                        <?php if (trim(strip_tags($itemText)) !== ''): ?><div><?= $itemText ?></div><?php endif; ?>
                        <?php if ($link['href'] !== ''): ?>
                            <a href="<?= rex_escape($link['href']) ?>"
                               class="btn btn-link" style="padding:0;"
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
<?php if ($enableContainer && $containerClass !== ''): ?></div><?php endif; ?>
<?php if ($enableSection): ?></section><?php endif; ?>

<section<?= $sectionClassStr !== '' ? ' class="' . rex_escape($sectionClassStr) . '"' : '' ?>>
    <?php if ($containerClass !== ''): ?><div class="<?= rex_escape($containerClass) ?>"><?php endif; ?>
        <?php if ($headline !== ''): ?><h3><?= rex_escape($headline) ?></h3><?php endif; ?>
        <div class="row">
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
                <div class="<?= $colClass ?>" style="margin-bottom:15px;">
                    <article style="border:1px solid #ddd; background:#fff; height:100%;">
                        <?php if (!empty($item['image'])): ?>
                            <img src="<?= rex_url::media((string) $item['image']) ?>" alt="<?= rex_escape($itemImageAlt) ?>" class="img-responsive" loading="lazy">
                        <?php endif; ?>
                        <div style="padding:12px;">
                            <?php if ($itemTitle !== ''): ?><h4 style="margin-top:0;"><?= rex_escape($itemTitle) ?></h4><?php endif; ?>
                            <?php if (trim(strip_tags($itemText)) !== ''): ?><div><?= $itemText ?></div><?php endif; ?>
                            <?php if (!empty($item['link_url'])): ?>
                                <a href="<?= rex_escape((string) $item['link_url']) ?>" class="btn btn-link" style="padding:0;" aria-label="<?= rex_escape($itemLinkText . ($itemTitle !== '' ? ' - ' . $itemTitle : '')) ?>"><?= rex_escape($itemLinkText) ?></a>
                            <?php endif; ?>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    <?php if ($containerClass !== ''): ?></div><?php endif; ?>
</section>
