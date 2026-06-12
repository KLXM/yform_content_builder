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
$sectionBgImage = (string) ($elementData['section_bg_image'] ?? '');
$sectionPadding = (string) ($elementData['section_padding'] ?? '');
$containerWidth = (string) ($elementData['container_width'] ?? 'uk-container');
$sectionLight = !empty($elementData['section_light']);
$enableSection = !isset($elementData['enable_section']) || !empty($elementData['enable_section']);
$enableContainer = !isset($elementData['enable_container']) || !empty($elementData['enable_container']);

if ($headline === '' && $text === '') {
    return;
}

$wrapper = new rex_fragment();
$wrapper->setVar('enable_section', $enableSection, false);
$wrapper->setVar('enable_container', $enableContainer, false);
$wrapper->setVar('section_bg', $sectionBg, false);
$wrapper->setVar('section_bg_image', $sectionBgImage, false);
$wrapper->setVar('section_padding', $sectionPadding, false);
$wrapper->setVar('container_width', $containerWidth, false);
$wrapper->setVar('section_light', $sectionLight, false);

$wrapperClose = new rex_fragment();
$wrapperClose->setVar('mode', 'close', false);
$wrapperClose->setVar('enable_section', $enableSection, false);
$wrapperClose->setVar('enable_container', $enableContainer, false);
$wrapperClose->setVar('section_bg_image', $sectionBgImage, false);
$wrapperClose->setVar('container_width', $containerWidth, false);
?>
<?= $wrapper->parse('ycb_elements/wrapper.php') ?>
        <div class="uk-card uk-card-default uk-card-body"<?= $buttonUrl === '' ? ' role="note"' : '' ?>>
            <?php if ($eyebrow !== ''): ?><div class="uk-text-meta uk-margin-small-bottom"><?= rex_escape($eyebrow) ?></div><?php endif; ?>
            <?php if ($headline !== ''): ?><h3 class="uk-margin-small-top"><?= rex_escape($headline) ?></h3><?php endif; ?>
            <?php if ($text !== ''): ?><p><?= rex_escape($text) ?></p><?php endif; ?>
            <?php if ($buttonUrl !== ''): ?><a class="uk-button uk-button-primary" href="<?= rex_escape($buttonUrl) ?>" aria-label="<?= rex_escape($buttonText . ($headline !== '' ? ' - ' . $headline : '')) ?>"><?= rex_escape($buttonText) ?></a><?php endif; ?>
        </div>
<?= $wrapperClose->parse('ycb_elements/wrapper.php') ?>
