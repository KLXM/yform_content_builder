<?php
$headline = (string) ($elementData['headline'] ?? '');
$text = (string) ($elementData['text'] ?? '');
$position = (string) ($elementData['media_position'] ?? 'left');
$mediaFile = (string) ($elementData['media_file'] ?? '');
$mediaAlt = (string) ($elementData['media_alt'] ?? '');

$sectionBg = (string) ($elementData['section_bg'] ?? '');
$sectionPadding = (string) ($elementData['section_padding'] ?? '');
$containerWidth = (string) ($elementData['container_width'] ?? 'uk-container');
$sectionLight = !empty($elementData['section_light']);

if ($headline === '' && trim(strip_tags($text)) === '' && $mediaFile === '') {
    return;
}

$ext = strtolower((string) pathinfo($mediaFile, PATHINFO_EXTENSION));
$isVideo = in_array($ext, ['mp4', 'webm', 'ogg'], true);

$mediaLabel = YFormContentBuilderMediaAltResolver::resolve($mediaFile, $mediaAlt, $headline);

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
$mediaHtml = '';
if ($mediaFile !== '') {
    if ($isVideo) {
        $videoAriaLabel = $mediaLabel !== '' ? $mediaLabel : 'Video';
        $mediaHtml = '<video controls playsinline aria-label="' . rex_escape($videoAriaLabel) . '" style="max-width:100%;height:auto;"><source src="' . rex_url::media($mediaFile) . '" type="video/' . rex_escape($ext) . '"></video>';
    } else {
        $mediaHtml = '<img src="' . rex_url::media($mediaFile) . '" alt="' . rex_escape($mediaLabel) . '" loading="lazy" style="max-width:100%;height:auto;">';
    }
}
?>
<section<?= $sectionStyle !== '' ? ' style="' . rex_escape($sectionStyle) . '"' : '' ?>>
    <div style="<?= rex_escape($containerStyle) ?>">
        <div style="display:flex; flex-wrap:wrap; align-items:center; gap:20px;">
            <?php if ($position === 'left'): ?>
                <div style="flex:1 1 320px;"><?= $mediaHtml ?></div>
            <?php endif; ?>
            <div style="flex:1 1 320px;">
                <?php if ($headline !== ''): ?><h3 style="margin-top:0;"><?= rex_escape($headline) ?></h3><?php endif; ?>
                <?php if ($text !== ''): ?><div><?= $text ?></div><?php endif; ?>
            </div>
            <?php if ($position === 'right'): ?>
                <div style="flex:1 1 320px;"><?= $mediaHtml ?></div>
            <?php endif; ?>
        </div>
    </div>
</section>
