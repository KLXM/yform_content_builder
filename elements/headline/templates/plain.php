<?php
/**
 * Headline Element - Plain Template
 * @var array $elementData
 */

$text = $elementData['text'] ?? '';
$tag = $elementData['tag'] ?? 'h2';
$style = $elementData['style'] ?? 'default';
$alignment = $elementData['alignment'] ?? 'left';

if (empty($text)) {
    return;
}

// Simple classes
$classes = ['cb-headline'];
$classes[] = 'cb-headline-' . $style;
$classes[] = 'cb-text-' . $alignment;

$classStr = implode(' ', $classes);
?>

<<?= $tag ?> class="<?= $classStr ?>">
    <?= rex_escape($text) ?>
</<?= $tag ?>>
