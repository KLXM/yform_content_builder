<?php
/**
 * Plain Template für Section Element
 * @var array $elementData
 * @var string $closeType 'open', 'close', or null
 */

$label = $elementData['label'] ?? '';
$bgColor = $elementData['background_color'] ?? 'none';
$bgImage = $elementData['background_image'] ?? '';
$paddingTop = $elementData['padding_top'] ?? 'medium';
$paddingBottom = $elementData['padding_bottom'] ?? 'medium';
$container = $elementData['container'] ?? 'container';
$textAlign = $elementData['text_align'] ?? '';
$customClass = $elementData['custom_class'] ?? '';
$customId = $elementData['custom_id'] ?? '';

$classes = ['cb-section'];

// Background
if (!empty($bgImage)) {
    $classes[] = 'cb-section-bg-image';
} elseif ($bgColor && $bgColor !== 'none' && $bgColor !== 'transparent') {
    $classes[] = 'cb-section-bg-' . $bgColor;
}

// Padding
if ($paddingTop && $paddingTop !== 'none') {
    $classes[] = 'cb-section-pt-' . $paddingTop;
}
if ($paddingBottom && $paddingBottom !== 'none') {
    $classes[] = 'cb-section-pb-' . $paddingBottom;
}

// Text Align
if ($textAlign) {
    $classes[] = 'cb-text-' . $textAlign;
}

// Custom Class
if ($customClass) {
    $classes[] = $customClass;
}

$classString = implode(' ', $classes);

// Style für Hintergrundbild
$style = '';
if ($bgImage) {
    $style = ' style="background-image: url(' . rex_url::media($bgImage) . '); background-size: cover; background-position: center;"';
}

// ID-Attribut
$idAttr = $customId ? ' id="' . rex_escape($customId) . '"' : '';

// Wenn closeType gesetzt ist, nur öffnen oder schließen
if (isset($closeType)) {
    if ($closeType === 'close') {
        // Section schließen
        if ($container !== 'none') {
            echo '    </div>' . "\n"; // Container schließen
        }
        echo '</section>' . "\n";
        return;
    }
    
    if ($closeType === 'open') {
        // Section öffnen
        echo '<section class="' . $classString . '"' . $idAttr . $style . '>' . "\n";
        if ($container !== 'none') {
            echo '    <div class="cb-' . rex_escape($container) . '">' . "\n";
        }
        return;
    }
}

// Fallback: Normaler Output (sollte nicht verwendet werden)
?>
<!-- Section: <?= rex_escape($label ?: 'Unbenannt') ?> -->
<section class="<?= $classString ?>"<?= $idAttr ?><?= $style ?>>
    <?php if ($container !== 'none'): ?>
    <div class="cb-<?= rex_escape($container) ?>">
    <?php endif; ?>
        <!-- Nachfolgende Elemente werden hier eingefügt -->
    <?php if ($container !== 'none'): ?>
    </div>
    <?php endif; ?>
</section>
