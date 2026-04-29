<?php
$headline = (string) ($elementData['headline'] ?? '');
$items = $elementData['items'] ?? [];
$columns = (int) ($elementData['columns'] ?? 3);

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

$containerClass = 'container';
if ($containerWidth === '') { $containerClass = ''; }
elseif (strpos($containerWidth, 'expand') !== false || strpos($containerWidth, 'xlarge') !== false) { $containerClass = 'container-fluid'; }

$colMap = [1 => 'col-sm-12', 2 => 'col-sm-6', 3 => 'col-sm-4', 4 => 'col-sm-3', 5 => 'col-sm-2', 6 => 'col-sm-2'];
$colClass = $colMap[$columns] ?? 'col-sm-4';
?>
<section<?= $sectionStyle !== '' ? ' style="' . rex_escape($sectionStyle) . '"' : '' ?>>
    <?php if ($containerClass !== ''): ?><div class="<?= $containerClass ?>"><?php endif; ?>
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
