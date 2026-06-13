<?php
use KLXM\YFormContentBuilder\Starter\StarterConfig;

$headline        = (string) ($elementData['headline'] ?? '');
$items           = $elementData['items'] ?? [];
$cardStyle       = (string) ($elementData['card_style'] ?? 'default');
$columns         = max(1, min(6, (int) ($elementData['columns'] ?? 3)));
$gap             = (string) ($elementData['gap'] ?? 'medium');
$sectionBg       = (string) ($elementData['section_bg'] ?? '');
$sectionPadding  = (string) ($elementData['section_padding'] ?? '');
$containerWidth  = (string) ($elementData['container_width'] ?? 'uk-container');
$sectionLight    = !empty($elementData['section_light']);
$enableSection   = !isset($elementData['enable_section']) || !empty($elementData['enable_section']);
$enableContainer = !isset($elementData['enable_container']) || !empty($elementData['enable_container']);

if (!is_array($items) || $items === []) {
    return;
}

$sectionStyle  = StarterConfig::mapBg($sectionBg, 'plain');
$sectionStyle .= StarterConfig::mapPadding($sectionPadding, 'plain');
if ($sectionLight) {
    $sectionStyle .= 'color:#fff;';
}
$containerStyle = StarterConfig::mapContainer($containerWidth, 'plain');

$gapPx = $gap === 'collapse' ? '0' : ($gap === 'small' ? '8px' : ($gap === 'large' ? '24px' : '15px'));

$cardStyleMap = [
    'default'     => 'border:1px solid #ddd;background:#fff;',
    'primary'     => 'background:#1e87f0;color:#fff;',
    'secondary'   => 'background:#222;color:#fff;',
    'muted'       => 'background:#f8f8f8;',
    'hover'       => 'border:1px solid #ddd;background:#fff;',
    'transparent' => '',
];
$cardStyleAttr = $cardStyleMap[$cardStyle] ?? 'border:1px solid #ddd;background:#fff;';

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
?>
<?php if ($enableSection): ?><section<?= $sectionStyle !== '' ? ' style="' . rex_escape($sectionStyle) . '"' : '' ?>><?php endif; ?>
<?php if ($enableContainer): ?><div style="<?= rex_escape($containerStyle) ?>"><?php endif; ?>
<?php if ($headline !== ''): ?><h3><?= rex_escape($headline) ?></h3><?php endif; ?>
<ul style="list-style:none;margin:0;padding:0;display:grid;grid-template-columns:repeat(<?= $columns ?>,minmax(0,1fr));gap:<?= rex_escape($gapPx) ?>;">
    <?php foreach ($items as $index => $item): ?>
        <?php
        $itemTitle = (string) ($item['title'] ?? '');
        $itemText  = (string) ($item['text'] ?? '');
        $link      = $resolveLink($item);
        $fallback  = $headline !== '' ? $headline . ' ' . ($index + 1) : 'Karte ' . ($index + 1);
        $imageAlt  = \KLXM\YFormContentBuilder\MediaAltResolver::resolve((string) ($item['image'] ?? ''), '', $itemTitle !== '' ? $itemTitle : $fallback);
        ?>
        <li>
            <article style="height:100%;<?= rex_escape($cardStyleAttr) ?>">
                <?php if (!empty($item['image'])): ?>
                    <img src="<?= rex_url::media((string) $item['image']) ?>" alt="<?= rex_escape($imageAlt) ?>" loading="lazy" style="max-width:100%;height:auto;display:block;">
                <?php endif; ?>
                <div style="padding:12px;">
                    <?php if ($itemTitle !== ''): ?><h4 style="margin:0 0 8px;"><?= rex_escape($itemTitle) ?></h4><?php endif; ?>
                    <?php if (trim(strip_tags($itemText)) !== ''): ?><div style="margin:0 0 8px;"><?= $itemText ?></div><?php endif; ?>
                    <?php if ($link['href'] !== ''): ?>
                        <a href="<?= rex_escape($link['href']) ?>"
                           <?= $link['target'] !== '' ? 'target="' . rex_escape($link['target']) . '" rel="noopener"' : '' ?>
                           aria-label="<?= rex_escape($link['text'] . ($itemTitle !== '' ? ' – ' . $itemTitle : '')) ?>">
                            <?= rex_escape($link['text']) ?>
                        </a>
                    <?php endif; ?>
                </div>
            </article>
        </li>
    <?php endforeach; ?>
</ul>
<?php if ($enableContainer): ?></div><?php endif; ?>
<?php if ($enableSection): ?></section><?php endif; ?>
