<?php
$text = trim((string) ($elementData['text'] ?? ''));
$subline = trim((string) ($elementData['subline'] ?? ''));
$tag = (string) ($elementData['tag'] ?? 'h2');
$containerWidth = (string) ($elementData['container_width'] ?? 'uk-container');
$sectionPadding = (string) ($elementData['section_padding'] ?? '');

$allowedTags = ['h1', 'h2', 'h3', 'h4'];
if (!in_array($tag, $allowedTags, true)) {
    $tag = 'h2';
}

if ($text === '') {
    return;
}

$sectionClass = trim('uk-section ' . $sectionPadding);
?>
<section class="<?= rex_escape($sectionClass) ?>">
    <?php if ($containerWidth !== ''): ?>
    <div class="<?= rex_escape($containerWidth) ?>">
    <?php endif; ?>

    <<?= $tag ?>><?= rex_escape($text) ?></<?= $tag ?>>
    <?php if ($subline !== ''): ?>
    <p class="uk-text-meta"><?= rex_escape($subline) ?></p>
    <?php endif; ?>

    <?php if ($containerWidth !== ''): ?>
    </div>
    <?php endif; ?>
</section>
