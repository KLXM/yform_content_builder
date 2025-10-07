<?php
/**
 * Divider Element - Bootstrap Template
 * @var array $elementData
 */

$style = $elementData['style'] ?? 'simple';
$icon = $elementData['icon'] ?? 'fa fa-star';
$text = $elementData['text'] ?? '';
$color = $elementData['color'] ?? 'default';
$width = $elementData['width'] ?? 'full';
$spacingTop = $elementData['spacing_top'] ?? 'medium';
$spacingBottom = $elementData['spacing_bottom'] ?? 'medium';

$classes = [
    'cb-divider',
    'cb-divider-style-' . $style,
    'cb-divider-color-' . $color,
    'cb-divider-width-' . $width,
    'cb-divider-spacing-top-' . $spacingTop,
    'cb-divider-spacing-bottom-' . $spacingBottom
];

$classStr = implode(' ', $classes);
?>

<?php if ($style === 'scroll'): ?>
    <!-- Scroll Animation Style -->
    <div class="<?= $classStr ?> cb-divider-scroll">
        <div class="cb-divider-scroll-line"></div>
        <i class="fa fa-chevron-down cb-divider-scroll-chevron"></i>
    </div>

<?php elseif ($style === 'icon'): ?>
    <!-- Icon Style -->
    <div class="<?= $classStr ?>">
        <hr class="cb-divider-line">
        <i class="<?= rex_escape($icon) ?> cb-divider-icon"></i>
        <hr class="cb-divider-line">
    </div>

<?php elseif ($style === 'text'): ?>
    <!-- Text Style -->
    <div class="<?= $classStr ?>">
        <hr class="cb-divider-line">
        <span class="cb-divider-text"><?= rex_escape($text) ?></span>
        <hr class="cb-divider-line">
    </div>

<?php else: ?>
    <!-- Standard Line Styles -->
    <div class="<?= $classStr ?>">
        <hr class="cb-divider-line">
    </div>

<?php endif; ?>
