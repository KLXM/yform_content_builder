<?php
/**
 * Divider Element - UIkit Template
 * @var array $elementData
 */

// Element-spezifische Felder
$style = $elementData['style'] ?? 'simple';
$icon = $elementData['icon'] ?? 'fa fa-star';
$text = $elementData['text'] ?? '';
$textPosition = $elementData['text_position'] ?? 'center';
$color = $elementData['color'] ?? 'default';
$width = $elementData['width'] ?? 'full';
$spacingTop = $elementData['spacing_top'] ?? 'medium';
$spacingBottom = $elementData['spacing_bottom'] ?? 'medium';
$scrollAnchor = $elementData['scroll_anchor'] ?? '#';

// Section-Einstellungen
$sectionBg = $elementData['section_bg'] ?? '';
$sectionBgImage = $elementData['section_bg_image'] ?? '';
$sectionPadding = $elementData['section_padding'] ?? '';
$containerWidth = $elementData['container_width'] ?? '';
$sectionLight = !empty($elementData['section_light']);
$enableSection = !isset($elementData['enable_section']) || !empty($elementData['enable_section']);
$enableContainer = !isset($elementData['enable_container']) || !empty($elementData['enable_container']);

// Width Mapping
$widthMap = [
    'full' => '100%',
    'wide' => '80%',
    'medium' => '60%',
    'narrow' => '40%'
];
$widthStyle = $widthMap[$width] ?? '100%';

// Spacing Mapping
$spacingMapTop = [
    'none' => '',
    'small' => 'uk-margin-small-top',
    'medium' => 'uk-margin-top',
    'large' => 'uk-margin-large-top',
    'xlarge' => 'uk-margin-xlarge-top'
];
$spacingMapBottom = [
    'none' => '',
    'small' => 'uk-margin-small-bottom',
    'medium' => 'uk-margin-bottom',
    'large' => 'uk-margin-large-bottom',
    'xlarge' => 'uk-margin-xlarge-bottom'
];

$marginClasses = [];
$marginClasses[] = $spacingMapTop[$spacingTop] ?? 'uk-margin-top';
$marginClasses[] = $spacingMapBottom[$spacingBottom] ?? 'uk-margin-bottom';

$wrapper = new rex_fragment();
$wrapper->setVar('enable_section', $enableSection, false);
$wrapper->setVar('enable_container', $enableContainer, false);
$wrapper->setVar('section_bg', $sectionBg, false);
$wrapper->setVar('section_bg_image', $sectionBgImage, false);
$wrapper->setVar('section_padding', $sectionPadding, false);
$wrapper->setVar('container_width', $containerWidth, false);
$wrapper->setVar('section_light', $sectionLight, false);

$wrapperClose = new rex_fragment();
$wrapperClose->setVar('mode', 'close', false);
$wrapperClose->setVar('enable_section', $enableSection, false);
$wrapperClose->setVar('enable_container', $enableContainer, false);
$wrapperClose->setVar('section_bg_image', $sectionBgImage, false);
$wrapperClose->setVar('container_width', $containerWidth, false);

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

$wrapperClasses = implode(' ', $marginClasses);
$needsContainer = ($width !== 'full');

?>

<?= $wrapper->parse('ycb_elements/wrapper.php') ?>

<div class="<?= $wrapperClasses ?>">
    <?php if ($needsContainer): ?>
    <div class="uk-flex uk-flex-center">
    <?php endif; ?>
    
    <?php switch ($style):
        case 'none': ?>
        <!-- Keine Linie - nur Abstand -->
        <?php break;
        
        case 'simple': ?>
        <hr class="uk-hr" style="width: <?= $widthStyle ?>; border-top-color: <?= $lineColor ?>;">
        <?php break;
        
        case 'double': ?>
        <div style="width: <?= $widthStyle ?>; border-top: 1px solid <?= $lineColor ?>; border-bottom: 1px solid <?= $lineColor ?>; height: 4px;"></div>
        <?php break;
        
        case 'dotted': ?>
        <hr style="width: <?= $widthStyle ?>; border: none; border-top: 2px dotted <?= $lineColor ?>;">
        <?php break;
        
        case 'dashed': ?>
        <hr style="width: <?= $widthStyle ?>; border: none; border-top: 2px dashed <?= $lineColor ?>;">
        <?php break;
        
        case 'thick': ?>
        <hr style="width: <?= $widthStyle ?>; border: none; border-top: 4px solid <?= $lineColor ?>;">
        <?php break;
        
        case 'gradient': ?>
        <hr style="width: <?= $widthStyle ?>; border: none; height: 2px; background: linear-gradient(90deg, transparent, <?= $lineColor ?>, transparent);">
        <?php break;
        
        case 'icon': ?>
        <div style="width: <?= $widthStyle ?>;">
            <hr class="uk-divider-icon">
        </div>
        <?php break;
        
        case 'text': ?>
        <div style="width: <?= $widthStyle ?>;" class="uk-flex uk-flex-middle<?php if ($textPosition === 'left'): ?> uk-flex-left<?php endif; ?>">
            <?php if ($textPosition === 'left'): ?>
                <span class="uk-margin-small-right uk-text-muted"><?= rex_escape($text) ?></span>
                <hr class="uk-hr uk-flex-1" style="border-top-color: <?= $lineColor ?>;">
            <?php else: ?>
                <hr class="uk-hr uk-flex-1" style="border-top-color: <?= $lineColor ?>;">
                <span class="uk-margin-small-left uk-margin-small-right uk-text-muted"><?= rex_escape($text) ?></span>
                <hr class="uk-hr uk-flex-1" style="border-top-color: <?= $lineColor ?>;">
            <?php endif; ?>
        </div>
        <?php break;
        
        case 'scroll': ?>
        <div class="uk-text-center">
            <a href="<?= rex_escape($scrollAnchor) ?>" uk-scroll class="uk-icon-button" uk-icon="icon: chevron-down; ratio: 1.5"></a>
        </div>
        <?php break;
        
        default: ?>
        <hr class="uk-hr">
        <?php break;
    endswitch; ?>
    
    <?php if ($needsContainer): ?>
    </div>
    <?php endif; ?>
</div>

<?= $wrapperClose->parse('ycb_elements/wrapper.php') ?>
