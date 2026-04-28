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

$sectionClasses = ['uk-section'];
if ($sectionBg !== '') {
    $sectionClasses[] = $sectionBg;
}
if ($sectionPadding !== '') {
    $sectionClasses[] = $sectionPadding;
}
if ($sectionLight) {
    $sectionClasses[] = 'uk-light';
}
?>
<section class="<?= rex_escape(implode(' ', $sectionClasses)) ?>">
    <?php if ($containerWidth !== ''): ?>
    <div class="<?= rex_escape($containerWidth) ?>">
    <?php endif; ?>
        <?php if ($headline !== ''): ?>
            <<?= $headlineTag ?> class="uk-margin-small-bottom"><?= rex_escape($headline) ?></<?= $headlineTag ?>>
        <?php endif; ?>
        <?php if ($text !== ''): ?>
            <div><?= $text ?></div>
        <?php endif; ?>
    <?php if ($containerWidth !== ''): ?>
    </div>
    <?php endif; ?>
</section>
