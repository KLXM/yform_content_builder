<?php
$headline = (string) ($elementData['headline'] ?? '');
$headlineTag = (string) ($elementData['headline_tag'] ?? 'h2');
$text = (string) ($elementData['text'] ?? '');

$allowedHeadlineTags = ['h1', 'h2', 'h3', 'h4', 'p'];
if (!in_array($headlineTag, $allowedHeadlineTags, true)) {
    $headlineTag = 'h2';
}

$sectionBg = (string) ($elementData['section_bg'] ?? '');
$sectionPadding = (string) ($elementData['section_padding'] ?? '');
$containerWidth = (string) ($elementData['container_width'] ?? 'uk-container');
$sectionLight = !empty($elementData['section_light']);

if ($headline === '' && trim(strip_tags($text)) === '') {
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

$containerClass = 'container';
if ($containerWidth === '') {
    $containerClass = '';
} elseif (strpos($containerWidth, 'expand') !== false || strpos($containerWidth, 'xlarge') !== false) {
    $containerClass = 'container-fluid';
}
?>
<section<?= $sectionStyle !== '' ? ' style="' . rex_escape($sectionStyle) . '"' : '' ?>>
    <?php if ($containerClass !== ''): ?>
    <div class="<?= $containerClass ?>">
    <?php endif; ?>
        <?php if ($headline !== ''): ?>
            <<?= $headlineTag ?> style="margin-top:0;"><?= rex_escape($headline) ?></<?= $headlineTag ?>>
        <?php endif; ?>
        <?php if ($text !== ''): ?>
            <div><?= $text ?></div>
        <?php endif; ?>
    <?php if ($containerClass !== ''): ?>
    </div>
    <?php endif; ?>
</section>
