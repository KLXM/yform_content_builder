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

$sectionClasses = [];
if ($enableSection) {
    $sectionClasses[] = 'uk-section';
    if ($sectionBg !== '') {
        $sectionClasses[] = $sectionBg;
    }
    if ($sectionPadding !== '') {
        $sectionClasses[] = $sectionPadding;
    }
    if ($sectionLight) {
        $sectionClasses[] = 'uk-light';
    }
}
?>
<?php if ($enableSection): ?>
<section class="<?= rex_escape(implode(' ', $sectionClasses)) ?>">
<?php endif; ?>
<?php if ($enableContainer): ?>
    <div class="<?= rex_escape($containerWidth) ?>">
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
