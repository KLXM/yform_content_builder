<?php
/**
 * Divider Element - UIkit Template
 * @var array $elementData
 */

$style = $elementData['style'] ?? 'simple';
$icon = $elementData['icon'] ?? 'fa fa-star';
$text = $elementData['text'] ?? '';
$color = $elementData['color'] ?? 'default';
$width = $elementData['width'] ?? 'full';
$spacingTop = $elementData['spacing_top'] ?? 'medium';
$spacingBottom = $elementData['spacing_bottom'] ?? 'medium';

// Width Mapping
$widthMap = [
    'full' => '100%',
    'wide' => '80%',
    'medium' => '60%',
    'narrow' => '40%'
];
$widthStyle = $widthMap[$width] ?? '100%';

// Spacing Mapping (UIkit utility classes)
$spacingMapTop = [
    'small' => 'uk-margin-small-top',
    'medium' => 'uk-margin-top',
    'large' => 'uk-margin-large-top'
];
$spacingMapBottom = [
    'small' => 'uk-margin-small-bottom',
    'medium' => 'uk-margin-bottom',
    'large' => 'uk-margin-large-bottom'
];

$marginClasses = [];
$marginClasses[] = $spacingMapTop[$spacingTop] ?? 'uk-margin-top';
$marginClasses[] = $spacingMapBottom[$spacingBottom] ?? 'uk-margin-bottom';

// Color Mapping
$colorMap = [
    'default' => '#e5e5e5',
    'primary' => 'var(--uk-primary, #1e87f0)',
    'secondary' => 'var(--uk-secondary, #222)',
    'success' => '#32d296',
    'warning' => '#faa05a',
    'danger' => '#f0506e'
];
$lineColor = $colorMap[$color] ?? $colorMap['default'];

// Container für zentrierte Ausrichtung bei nicht-100% Breite
$needsContainer = ($width !== 'full');
$containerStart = $needsContainer ? '<div class="uk-flex uk-flex-center">' : '';
$containerEnd = $needsContainer ? '</div>' : '';

// Wrapper-Klassen
$wrapperClasses = implode(' ', $marginClasses);

switch ($style) {
    case 'simple':
        echo '<div class="' . $wrapperClasses . '">';
        echo $containerStart;
        echo '<hr class="uk-hr" style="width: ' . $widthStyle . '; border-top-color: ' . $lineColor . ';">';
        echo $containerEnd;
        echo '</div>';
        break;

    case 'double':
        echo '<div class="' . $wrapperClasses . '">';
        echo $containerStart;
        echo '<div style="width: ' . $widthStyle . '; border-top: 1px solid ' . $lineColor . '; border-bottom: 1px solid ' . $lineColor . '; height: 4px;"></div>';
        echo $containerEnd;
        echo '</div>';
        break;

    case 'dotted':
        echo '<div class="' . $wrapperClasses . '">';
        echo $containerStart;
        echo '<hr style="width: ' . $widthStyle . '; border: none; border-top: 2px dotted ' . $lineColor . ';">';
        echo $containerEnd;
        echo '</div>';
        break;

    case 'dashed':
        echo '<div class="' . $wrapperClasses . '">';
        echo $containerStart;
        echo '<hr style="width: ' . $widthStyle . '; border: none; border-top: 2px dashed ' . $lineColor . ';">';
        echo $containerEnd;
        echo '</div>';
        break;

    case 'thick':
        echo '<div class="' . $wrapperClasses . '">';
        echo $containerStart;
        echo '<hr style="width: ' . $widthStyle . '; border: none; border-top: 4px solid ' . $lineColor . ';">';
        echo $containerEnd;
        echo '</div>';
        break;

    case 'gradient':
        echo '<div class="' . $wrapperClasses . '">';
        echo $containerStart;
        echo '<hr style="width: ' . $widthStyle . '; border: none; height: 2px; background: linear-gradient(90deg, transparent, ' . $lineColor . ', transparent);">';
        echo $containerEnd;
        echo '</div>';
        break;

    case 'icon':
        // UIkit Divider mit Icon
        echo '<div class="' . $wrapperClasses . '">';
        echo $containerStart;
        echo '<div style="width: ' . $widthStyle . ';">';
        echo '<hr class="uk-divider-icon">';
        echo '</div>';
        echo $containerEnd;
        echo '</div>';
        break;

    case 'text':
        // Linie mit Text in der Mitte
        echo '<div class="' . $wrapperClasses . ' uk-flex uk-flex-middle" style="width: ' . $widthStyle . '; margin-left: auto; margin-right: auto;">';
        echo '<hr class="uk-hr uk-flex-1" style="border-top-color: ' . $lineColor . ';">';
        echo '<span class="uk-margin-small-left uk-margin-small-right uk-text-muted">' . rex_escape($text) . '</span>';
        echo '<hr class="uk-hr uk-flex-1" style="border-top-color: ' . $lineColor . ';">';
        echo '</div>';
        break;

    case 'scroll':
        // Scroll-Animation mit Chevron
        echo '<div class="' . $wrapperClasses . ' uk-text-center">';
        echo '<a href="#" uk-scroll class="uk-icon-button" uk-icon="icon: chevron-down; ratio: 1.5"></a>';
        echo '</div>';
        break;

    default:
        // Fallback: Einfache Linie
        echo '<div class="' . $wrapperClasses . '">';
        echo '<hr class="uk-hr">';
        echo '</div>';
        break;
}
