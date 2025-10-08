<?php
/**
 * Divider Element - UIkit Template
 * @var array $elementData
 */

$style = $elementData['style'] ?? 'solid';

// UIkit Divider
$classes = ['uk-divider'];

// Style mapping
if ($style === 'icon') {
    $classes[] = 'uk-divider-icon';
} elseif ($style === 'vertical') {
    $classes[] = 'uk-divider-vertical';
} elseif ($style === 'small') {
    $classes[] = 'uk-divider-small';
}

$classStr = implode(' ', $classes);

// Vertical divider braucht spezielles Markup
if ($style === 'vertical') {
    echo '<span class="' . $classStr . '"></span>';
} else {
    echo '<hr class="' . $classStr . '">';
}
