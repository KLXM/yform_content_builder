<?php
$text = (string) ($elementData['text'] ?? '');

$sectionBg = (string) ($elementData['section_bg'] ?? '');
$sectionPadding = (string) ($elementData['section_padding'] ?? '');
$containerWidth = (string) ($elementData['container_width'] ?? 'uk-container');
$sectionLight = !empty($elementData['section_light']);
$enableSection = !empty($elementData['enable_section']);
$enableContainer = !empty($elementData['enable_container']);

if (trim(strip_tags($text)) === '') {
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
} elseif (str_contains($containerWidth, 'xsmall')) {
    $containerStyle = 'max-width:480px;margin:0 auto;padding:0 15px;';
} elseif (str_contains($containerWidth, 'small')) {
    $containerStyle = 'max-width:640px;margin:0 auto;padding:0 15px;';
} elseif (str_contains($containerWidth, 'large')) {
    $containerStyle = 'max-width:1320px;margin:0 auto;padding:0 15px;';
} elseif (str_contains($containerWidth, 'xlarge')) {
    $containerStyle = 'max-width:1600px;margin:0 auto;padding:0 15px;';
} elseif (str_contains($containerWidth, 'expand')) {
    $containerStyle = 'width:100%;padding:0 15px;';
}
?>
<?php if ($enableSection): ?>
<section<?= $sectionStyle !== '' ? ' style="' . rex_escape($sectionStyle) . '"' : '' ?>>
<?php endif; ?>
<?php if ($enableContainer): ?>
    <div style="<?= rex_escape($containerStyle) ?>">
<?php endif; ?>
        <?php if ($text !== ''): ?>
            <div><?= $text ?></div>
        <?php endif; ?>
<?php if ($enableContainer): ?>
    </div>
<?php endif; ?>
<?php if ($enableSection): ?>
</section>
<?php endif; ?>
