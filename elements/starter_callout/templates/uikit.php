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

if ($headline === '' && $text === '') {
    return;
}

$sectionClasses = ['uk-section'];
if ($sectionBg !== '') { $sectionClasses[] = $sectionBg; }
if ($sectionPadding !== '') { $sectionClasses[] = $sectionPadding; }
if ($sectionLight) { $sectionClasses[] = 'uk-light'; }
?>
<section class="<?= rex_escape(implode(' ', $sectionClasses)) ?>">
    <?php if ($containerWidth !== ''): ?><div class="<?= rex_escape($containerWidth) ?>"><?php endif; ?>
        <div class="uk-card uk-card-default uk-card-body"<?= $buttonUrl === '' ? ' role="note"' : '' ?>>
            <?php if ($eyebrow !== ''): ?><div class="uk-text-meta uk-margin-small-bottom"><?= rex_escape($eyebrow) ?></div><?php endif; ?>
            <?php if ($headline !== ''): ?><h3 class="uk-margin-small-top"><?= rex_escape($headline) ?></h3><?php endif; ?>
            <?php if ($text !== ''): ?><p><?= rex_escape($text) ?></p><?php endif; ?>
            <?php if ($buttonUrl !== ''): ?><a class="uk-button uk-button-primary" href="<?= rex_escape($buttonUrl) ?>" aria-label="<?= rex_escape($buttonText . ($headline !== '' ? ' - ' . $headline : '')) ?>"><?= rex_escape($buttonText) ?></a><?php endif; ?>
        </div>
    <?php if ($containerWidth !== ''): ?></div><?php endif; ?>
</section>
