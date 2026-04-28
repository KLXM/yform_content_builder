<?php
$eyebrow = (string) ($elementData['eyebrow'] ?? '');
$headline = (string) ($elementData['headline'] ?? '');
$text = (string) ($elementData['text'] ?? '');
$buttonText = (string) ($elementData['button_text'] ?? 'Mehr erfahren');
$buttonUrl = (string) ($elementData['button_url'] ?? '');

$buttonText = trim($buttonText);
if ($buttonText === '') {
    $buttonText = 'Mehr erfahren';
}

$sectionBg = (string) ($elementData['section_bg'] ?? '');
$sectionPadding = (string) ($elementData['section_padding'] ?? '');
$containerWidth = (string) ($elementData['container_width'] ?? 'uk-container');
$sectionLight = !empty($elementData['section_light']);

if ($headline === '' && $text === '') { return; }

$bgMap = ['uk-background-default' => '#ffffff', 'uk-background-muted' => '#f7f7f7', 'uk-background-primary' => '#1e87f0', 'uk-background-secondary' => '#222222'];
$paddingMap = ['uk-padding-remove' => '0', 'uk-padding-small' => '18px 0', 'uk-padding' => '35px 0', 'uk-padding-large' => '55px 0'];
$sectionStyle = '';
if (isset($bgMap[$sectionBg])) { $sectionStyle .= 'background:' . $bgMap[$sectionBg] . ';'; }
if (isset($paddingMap[$sectionPadding])) { $sectionStyle .= 'padding:' . $paddingMap[$sectionPadding] . ';'; }
if ($sectionLight) { $sectionStyle .= 'color:#fff;'; }
$containerStyle = 'max-width:1140px;margin:0 auto;padding:0 15px;';
if ($containerWidth === '') { $containerStyle = 'padding:0 15px;'; }
?>
<section<?= $sectionStyle !== '' ? ' style="' . rex_escape($sectionStyle) . '"' : '' ?>>
    <div style="<?= rex_escape($containerStyle) ?>">
        <div style="border:1px solid #ddd; background:#fff; padding:16px;"<?= $buttonUrl === '' ? ' role="note"' : '' ?>>
            <?php if ($eyebrow !== ''): ?><div style="font-size:12px; color:#666; margin-bottom:6px;"><?= rex_escape($eyebrow) ?></div><?php endif; ?>
            <?php if ($headline !== ''): ?><h3 style="margin-top:0;"><?= rex_escape($headline) ?></h3><?php endif; ?>
            <?php if ($text !== ''): ?><p><?= rex_escape($text) ?></p><?php endif; ?>
            <?php if ($buttonUrl !== ''): ?><a href="<?= rex_escape($buttonUrl) ?>" aria-label="<?= rex_escape($buttonText . ($headline !== '' ? ' - ' . $headline : '')) ?>" style="display:inline-block; padding:8px 12px; background:#1e87f0; color:#fff; text-decoration:none; border-radius:3px;"><?= rex_escape($buttonText) ?></a><?php endif; ?>
        </div>
    </div>
</section>
