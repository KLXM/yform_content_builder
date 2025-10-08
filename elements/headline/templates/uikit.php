<?php
/**
 * Headline Element - UIkit Template
 * @var array $elementData
 */

$text = $elementData['text'] ?? '';
$tag = $elementData['tag'] ?? 'h2';
$style = $elementData['style'] ?? 'default';
$alignment = $elementData['alignment'] ?? 'left';

if (empty($text)) {
    return;
}

// UIkit Heading Classes
$classes = ['uk-heading'];

// Style mapping
$styleMap = [
    'default' => '',
    'divider' => 'uk-heading-divider',
    'bullet' => 'uk-heading-bullet',
    'line' => 'uk-heading-line'
];

if (isset($styleMap[$style]) && $styleMap[$style]) {
    $classes[] = $styleMap[$style];
}

// Alignment
$alignmentMap = [
    'left' => 'uk-text-left',
    'center' => 'uk-text-center',
    'right' => 'uk-text-right'
];

if (isset($alignmentMap[$alignment])) {
    $classes[] = $alignmentMap[$alignment];
}

$classStr = implode(' ', array_filter($classes));
?>

<<?= $tag ?> class="<?= $classStr ?>">
    <?php if ($style === 'line'): ?>
        <span><?= rex_escape($text) ?></span>
    <?php else: ?>
        <?= rex_escape($text) ?>
    <?php endif; ?>
</<?= $tag ?>>
