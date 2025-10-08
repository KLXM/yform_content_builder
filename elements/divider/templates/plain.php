<?php
/**
 * Divider Element - Plain Template
 * @var array $elementData
 */

$style = $elementData['style'] ?? 'solid';

$classes = ['cb-divider'];
$classes[] = 'cb-divider-' . $style;

$classStr = implode(' ', $classes);
?>

<hr class="<?= $classStr ?>">
