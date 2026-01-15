<?php
/**
 * Headline Element - UIkit Template
 * @var array $elementData
 */

$text = $elementData['text'] ?? '';
$tag = $elementData['tag'] ?? 'h2';
$size = $elementData['size'] ?? '';
$modifier = $elementData['modifier'] ?? '';
$alignment = $elementData['alignment'] ?? 'left';
$color = $elementData['color'] ?? '';

if (empty($text)) {
    return;
}

// UIkit Heading Classes
$classes = [];

// Size mapping (UIkit heading size modifiers)
$sizeMap = [
    '' => '',
    'small' => 'uk-heading-small',
    'medium' => 'uk-heading-medium',
    'large' => 'uk-heading-large',
    'xlarge' => 'uk-heading-xlarge',
    '2xlarge' => 'uk-heading-2xlarge',
    '3xlarge' => 'uk-heading-3xlarge'
];

if (!empty($size) && isset($sizeMap[$size])) {
    $classes[] = $sizeMap[$size];
}

// Modifier mapping
$modifierMap = [
    '' => '',
    'divider' => 'uk-heading-divider',
    'bullet' => 'uk-heading-bullet',
    'line' => 'uk-heading-line'
];

if (!empty($modifier) && isset($modifierMap[$modifier])) {
    $classes[] = $modifierMap[$modifier];
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

// Color
if (!empty($color)) {
    $classes[] = 'uk-text-' . $color;
}

$classStr = implode(' ', array_filter($classes));
?>

<<?= $tag ?> class="<?= $classStr ?>">
    <?php if ($modifier === 'line'): ?>
        <span><?= rex_escape($text) ?></span>
    <?php else: ?>
        <?= rex_escape($text) ?>
    <?php endif; ?>
</<?= $tag ?>>
