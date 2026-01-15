<?php
/**
 * Divider Element - UIkit Template
 * @var array $elementData
 */

// Element-spezifische Felder
$style = $elementData['style'] ?? 'simple';
$icon = $elementData['icon'] ?? 'fa fa-star';
$text = $elementData['text'] ?? '';
$color = $elementData['color'] ?? 'default';
$width = $elementData['width'] ?? 'full';
$spacingTop = $elementData['spacing_top'] ?? 'medium';
$spacingBottom = $elementData['spacing_bottom'] ?? 'medium';

// Section-Einstellungen
$sectionBg = $elementData['section_bg'] ?? '';
$sectionBgImage = $elementData['section_bg_image'] ?? '';
$sectionPadding = $elementData['section_padding'] ?? '';
$containerWidth = $elementData['container_width'] ?? '';
$lightText = !empty($elementData['light_text']);

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

// Section-Klassen
$sectionClasses = ['uk-section'];
if ($sectionBg) $sectionClasses[] = $sectionBg;
if ($sectionPadding) $sectionClasses[] = $sectionPadding;
if ($lightText) $sectionClasses[] = 'uk-light';

// Section Background
$sectionStyle = '';
if (!empty($sectionBgImage)) {
    $bgMediaExt = strtolower(pathinfo($sectionBgImage, PATHINFO_EXTENSION));
    $videoExtensions = ['mp4', 'webm', 'ogg'];
    
    if (!in_array($bgMediaExt, $videoExtensions)) {
        $bgImageUrl = rex_media_manager::getUrl('content_slideshow', $sectionBgImage);
        $sectionStyle = ' style="background-image: url(\'' . $bgImageUrl . '\'); background-size: cover; background-position: center;"';
    }
}

$hasSection = $sectionBg || $sectionPadding || !empty($sectionBgImage);

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

<?php if ($hasSection): ?>
<section class="<?= implode(' ', $sectionClasses) ?>"<?= $sectionStyle ?>>
<?php endif; ?>

<?php if ($containerWidth): ?>
<div class="<?= $containerWidth ?>">
<?php endif; ?>

<div class="<?= $wrapperClasses ?>">
    <?php if ($needsContainer): ?>
    <div class="uk-flex uk-flex-center">
    <?php endif; ?>
    
    <?php switch ($style):
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
        <div style="width: <?= $widthStyle ?>;" class="uk-flex uk-flex-middle">
            <hr class="uk-hr uk-flex-1" style="border-top-color: <?= $lineColor ?>;">
            <span class="uk-margin-small-left uk-margin-small-right uk-text-muted"><?= rex_escape($text) ?></span>
            <hr class="uk-hr uk-flex-1" style="border-top-color: <?= $lineColor ?>;">
        </div>
        <?php break;
        
        case 'scroll': ?>
        <div class="uk-text-center">
            <a href="#" uk-scroll class="uk-icon-button" uk-icon="icon: chevron-down; ratio: 1.5"></a>
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

<?php if ($containerWidth): ?>
</div>
<?php endif; ?>

<?php if ($hasSection): ?>
</section>
<?php endif; ?>
