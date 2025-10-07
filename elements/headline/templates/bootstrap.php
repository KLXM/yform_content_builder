<?php
/**
 * Headline Element - Bootstrap Template
 * @var array $elementData
 */

$text = $elementData['text'] ?? '';
$tag = $elementData['tag'] ?? 'h2';
$size = $elementData['size'] ?? '';
$alignment = $elementData['alignment'] ?? 'left';
$color = $elementData['color'] ?? '';
$spacingTop = $elementData['spacing_top'] ?? '';
$spacingBottom = $elementData['spacing_bottom'] ?? '';
$underline = !empty($elementData['underline']);
$linkType = $elementData['link_type'] ?? '';
$linkUrl = $elementData['link_url'] ?? '';
$linkInternal = $elementData['link_internal'] ?? '';

// Link URL bestimmen
$finalLink = '';
if ($linkType === 'external' && $linkUrl) {
    $finalLink = $linkUrl;
} elseif ($linkType === 'internal' && $linkInternal) {
    $finalLink = rex_getUrl($linkInternal);
}

// CSS Klassen zusammenstellen
$classes = ['cb-headline'];

// Alignment
if ($alignment === 'center') {
    $classes[] = 'text-center';
} elseif ($alignment === 'right') {
    $classes[] = 'text-right';
}

// Color
if ($color) {
    $classes[] = 'text-' . $color;
}

// Size
if ($size === 'large') {
    $classes[] = 'cb-headline-large';
} elseif ($size === 'small') {
    $classes[] = 'cb-headline-small';
}

// Spacing
if ($spacingTop) {
    $classes[] = 'cb-spacing-top-' . $spacingTop;
}
if ($spacingBottom) {
    $classes[] = 'cb-spacing-bottom-' . $spacingBottom;
}

// Underline
if ($underline) {
    $classes[] = 'cb-headline-underline';
}

$classStr = implode(' ', $classes);
?>

<<?= $tag ?> class="<?= $classStr ?>">
    <?php if ($finalLink): ?>
        <a href="<?= rex_escape($finalLink) ?>" class="cb-headline-link">
            <?= rex_escape($text) ?>
        </a>
    <?php else: ?>
        <?= rex_escape($text) ?>
    <?php endif; ?>
</<?= $tag ?>>

<style>
/* Headline Element Styles */
.cb-headline {
    margin: 0;
}

.cb-headline-large {
    font-size: 1.5em;
    font-weight: bold;
}

.cb-headline-small {
    font-size: 0.85em;
}

.cb-headline-underline {
    padding-bottom: 15px;
    border-bottom: 3px solid currentColor;
    display: inline-block;
}

.cb-headline-link {
    color: inherit;
    text-decoration: none;
    transition: opacity 0.3s;
}

.cb-headline-link:hover {
    opacity: 0.7;
    text-decoration: none;
    color: inherit;
}

/* Spacing Top */
.cb-spacing-top-none {
    margin-top: 0 !important;
}

.cb-spacing-top-small {
    margin-top: 15px;
}

.cb-spacing-top-medium {
    margin-top: 30px;
}

.cb-spacing-top-large {
    margin-top: 60px;
}

/* Spacing Bottom */
.cb-spacing-bottom-none {
    margin-bottom: 0 !important;
}

.cb-spacing-bottom-small {
    margin-bottom: 15px;
}

.cb-spacing-bottom-medium {
    margin-bottom: 30px;
}

.cb-spacing-bottom-large {
    margin-bottom: 60px;
}
</style>
