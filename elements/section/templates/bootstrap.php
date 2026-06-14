<?php
/**
 * Bootstrap Template für Section Element
 * @var array $elementData
 * @var string $closeType 'open', 'close', or 'single'
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

// Padding-Mapping
$paddingMap = [
    'none' => 'py-0',
    'small' => 'py-3',
    'medium' => 'py-5',
    'large' => 'py-7',
    'xlarge' => 'py-9'
];

// Background-Klassen
$bgClasses = [
    'none' => '',
    'transparent' => '',
    'muted' => 'bg-light',
    'primary' => 'bg-primary text-white',
    'secondary' => 'bg-secondary text-white',
    // Legacy-Werte aus älteren Konfigurationen
    'light' => 'bg-light',
    'dark' => 'bg-dark text-white',
    'white' => 'bg-white'
];

$classes = ['section-wrapper'];

// Background
if (!empty($bgImage)) {
    $classes[] = 'section-with-bg-image';
} elseif (isset($bgClasses[$bgColor])) {
    $classes[] = $bgClasses[$bgColor];
}

// Padding
if (isset($paddingMap[$paddingTop])) {
    $classes[] = str_replace('py-', 'pt-', $paddingMap[$paddingTop]);
}
if (isset($paddingMap[$paddingBottom])) {
    $classes[] = str_replace('py-', 'pb-', $paddingMap[$paddingBottom]);
}

// Text Align
if ($textAlign) {
    $classes[] = 'text-' . $textAlign;
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
            echo '    <div class="' . rex_escape($container) . '">' . "\n";
        }
        return;
    }
}

// Fallback: Normaler Output (sollte nicht verwendet werden)
?>
<!-- Section: <?= rex_escape($label ?: 'Unbenannt') ?> -->
<section class="<?= $classString ?>"<?= $idAttr ?><?= $style ?>>
    <?php if ($container !== 'none'): ?>
    <div class="<?= rex_escape($container) ?>">
    <?php endif; ?>
        <!-- Nachfolgende Elemente werden hier eingefügt -->
    <?php if ($container !== 'none'): ?>
    </div>
    <?php endif; ?>
</section>
