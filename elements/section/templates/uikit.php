<?php
/**
 * UIkit Template für Section Element
 * @var array $elementData
 * @var string $closeType 'open', 'close', or null
 */

// Theme Override: Falls gesetzt, DomainContext anpassen
$themeOverride = $elementData['theme_override'] ?? '';
if (!empty($themeOverride) && class_exists('UikitThemeBuilder\DomainContext')) {
    \UikitThemeBuilder\DomainContext::setTheme($themeOverride);
}

$label = $elementData['label'] ?? '';
$bgColor = $elementData['background_color'] ?? 'light';
$bgImage = $elementData['background_image'] ?? '';
$paddingTop = $elementData['padding_top'] ?? 'medium';
$paddingBottom = $elementData['padding_bottom'] ?? 'medium';
$container = $elementData['container'] ?? 'container';
$textAlign = $elementData['text_align'] ?? '';
$customClass = $elementData['custom_class'] ?? '';
$customId = $elementData['custom_id'] ?? '';

// Grid-Optionen
$gridEnabled        = !empty($elementData['grid_enabled']);
$gridChildWidth     = $elementData['grid_child_width'] ?? '1-3';
$gridChildWidthTab  = $elementData['grid_child_width_tablet'] ?? '1-2';
$gridChildWidthMob  = $elementData['grid_child_width_mobile'] ?? '1-1';
$gridGap            = $elementData['grid_gap'] ?? '';
$gridMatch          = !empty($elementData['grid_match']);
$gridDivider        = !empty($elementData['grid_divider']);

// Padding-Mapping für UIkit
$paddingMap = [
    'none' => '',
    'small' => 'uk-section-small',
    'medium' => 'uk-section',
    'large' => 'uk-section-large',
    'xlarge' => 'uk-section-xlarge'
];

// Background-Klassen für UIkit
$bgClasses = [
    'none' => '',
    'light' => 'uk-section-muted',
    'dark' => 'uk-section-secondary',
    'primary' => 'uk-section-primary',
    'secondary' => 'uk-section-secondary',
    'muted' => 'uk-section-muted',
    'white' => ''
];

$classes = [];

// Section class
if (isset($paddingMap[$paddingTop]) && isset($paddingMap[$paddingBottom])) {
    // Bei unterschiedlichen Paddings: Basis verwenden
    if ($paddingTop === $paddingBottom && !empty($paddingMap[$paddingTop])) {
        $classes[] = $paddingMap[$paddingTop];
    } else {
        $classes[] = 'uk-section';
    }
}

// Background
if (!empty($bgImage)) {
    $classes[] = 'uk-background-cover uk-background-center-center';
} elseif (isset($bgClasses[$bgColor]) && $bgClasses[$bgColor]) {
    $classes[] = $bgClasses[$bgColor];
}

// Text Align
if ($textAlign) {
    $classes[] = 'uk-text-' . $textAlign;
}

// Custom Class
if ($customClass) {
    $classes[] = $customClass;
}

$classString = implode(' ', $classes);

// Style für Hintergrundbild
$style = '';
if ($bgImage) {
    $style = ' style="background-image: url(' . rex_url::media($bgImage) . ');"';
}

// ID-Attribut
$idAttr = $customId ? ' id="' . rex_escape($customId) . '"' : '';

// Container-Klasse für UIkit
$containerClass = 'uk-container';
if ($container === 'container-fluid') {
    $containerClass = 'uk-container uk-container-expand';
}

// Wenn closeType gesetzt ist, nur öffnen oder schließen
if (isset($closeType)) {
    if ($closeType === 'close') {
        // Grid-Wrapper schließen wenn aktiviert
        if ($gridEnabled) {
            echo '        </div>' . "\n"; // uk-grid
        }
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
            echo '    <div class="' . rex_escape($containerClass) . '">' . "\n";
        }

        // Grid-Wrapper öffnen wenn aktiviert
        if ($gridEnabled) {
            $gridClasses = ['uk-grid'];
            if ($gridGap) {
                $gridClasses[] = 'uk-grid-' . $gridGap;
            }
            if ($gridMatch) {
                $gridClasses[] = 'uk-grid-match';
            }
            if ($gridDivider) {
                $gridClasses[] = 'uk-grid-divider';
            }
            // uk-child-width für mobile, tablet, desktop
            if ($gridChildWidthMob && $gridChildWidthMob !== '1-1') {
                $gridClasses[] = 'uk-child-width-' . $gridChildWidthMob;
            } else {
                $gridClasses[] = 'uk-child-width-1-1';
            }
            $gridClasses[] = 'uk-child-width-' . $gridChildWidthTab . '@s';
            $gridClasses[] = 'uk-child-width-' . $gridChildWidth . '@m';

            echo '        <div class="' . implode(' ', $gridClasses) . '" uk-grid>' . "\n";
        }

        return;
    }
}

// Fallback: Normaler Output (sollte nicht verwendet werden)
?>
<!-- Section: <?= rex_escape($label ?: 'Unbenannt') ?> -->
<section class="<?= $classString ?>"<?= $idAttr ?><?= $style ?>>
    <?php if ($container !== 'none'): ?>
    <div class="<?= rex_escape($containerClass) ?>">
    <?php endif; ?>
        <!-- Nachfolgende Elemente werden hier eingefügt -->
    <?php if ($container !== 'none'): ?>
    </div>
    <?php endif; ?>
</section>
