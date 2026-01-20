<?php
/**
 * Headline Element - UIkit Template
 * @var array $elementData
 */

// Element-spezifische Felder
$text = $elementData['text'] ?? '';
$tag = $elementData['tag'] ?? 'h2';
$size = $elementData['size'] ?? '';
$modifier = $elementData['modifier'] ?? '';
$alignment = $elementData['alignment'] ?? 'left';
$color = $elementData['color'] ?? '';
$spacingTop = $elementData['spacing_top'] ?? '';
$spacingBottom = $elementData['spacing_bottom'] ?? '';
$underline = !empty($elementData['underline']);
$linkType = $elementData['link_type'] ?? '';
$linkUrl = $elementData['link_url'] ?? '';
$linkInternal = $elementData['link_internal'] ?? '';

// Section-Einstellungen
$sectionBg = $elementData['section_bg'] ?? '';
$sectionBgImage = $elementData['section_bg_image'] ?? '';
$sectionPadding = $elementData['section_padding'] ?? '';
$containerWidth = $elementData['container_width'] ?? '';
$sectionLight = !empty($elementData['section_light']);

if (empty($text)) {
    return;
}

// Link URL bestimmen
$finalLink = '';
if ($linkType === 'external' && $linkUrl) {
    $finalLink = $linkUrl;
} elseif ($linkType === 'internal' && $linkInternal) {
    $finalLink = rex_getUrl($linkInternal);
}

// UIkit Heading Classes
$classes = [];

// Size mapping (UIkit heading size modifiers)
$sizeMap = [
    '' => '',
    'small' => 'uk-heading-small',
    'medium' => 'uk-heading-medium',
    'large' => 'uk-heading-large',
    'xlarge' => 'uk-heading-xlarge',
    '2xlarge' => 'uk-heading-2xlarge',
    '3xlarge' => 'uk-heading-3xlarge'
];

if (!empty($size) && isset($sizeMap[$size])) {
    $classes[] = $sizeMap[$size];
}

// Modifier mapping
$modifierMap = [
    '' => '',
    'divider' => 'uk-heading-divider',
    'bullet' => 'uk-heading-bullet',
    'line' => 'uk-heading-line'
];

if (!empty($modifier) && isset($modifierMap[$modifier])) {
    $classes[] = $modifierMap[$modifier];
}

// Alignment
$alignmentMap = [
    'left' => 'uk-text-left',
    'center' => 'uk-text-center',
    'right' => 'uk-text-right'
];

if (isset($alignmentMap[$alignment])) {
    $classes[] = $alignmentMap[$alignment];
}

// Color
if (!empty($color)) {
    $classes[] = 'uk-text-' . $color;
}

// Spacing Top
$spacingMapTop = [
    'none' => 'uk-margin-remove-top',
    'small' => 'uk-margin-small-top',
    'medium' => 'uk-margin-top',
    'large' => 'uk-margin-large-top'
];
if (!empty($spacingTop) && isset($spacingMapTop[$spacingTop])) {
    $classes[] = $spacingMapTop[$spacingTop];
}

// Spacing Bottom
$spacingMapBottom = [
    'none' => 'uk-margin-remove-bottom',
    'small' => 'uk-margin-small-bottom',
    'medium' => 'uk-margin-bottom',
    'large' => 'uk-margin-large-bottom'
];
if (!empty($spacingBottom) && isset($spacingMapBottom[$spacingBottom])) {
    $classes[] = $spacingMapBottom[$spacingBottom];
}

// Underline als zusätzliche Klasse
if ($underline) {
    $classes[] = 'cb-headline-underline';
}

$classStr = implode(' ', array_filter($classes));

// Section-Klassen
$sectionClasses = [];
if ($sectionBg) $sectionClasses[] = $sectionBg;
if ($sectionPadding) $sectionClasses[] = $sectionPadding;
if ($sectionLight) $sectionClasses[] = 'uk-light';

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

$hasSection = !empty($sectionClasses) || !empty($sectionBgImage);

?>

<?php if ($hasSection): ?>
<section class="<?= implode(' ', $sectionClasses) ?>"<?= $sectionStyle ?>>
<?php endif; ?>

<?php if ($containerWidth): ?>
<div class="<?= $containerWidth ?>">
<?php endif; ?>

<<?= $tag ?> class="<?= $classStr ?>">
    <?php if ($finalLink): ?>
        <a href="<?= rex_escape($finalLink) ?>" class="uk-link-reset">
            <?php if ($modifier === 'line'): ?>
                <span><?= rex_escape($text) ?></span>
            <?php else: ?>
                <?= rex_escape($text) ?>
            <?php endif; ?>
        </a>
    <?php else: ?>
        <?php if ($modifier === 'line'): ?>
            <span><?= rex_escape($text) ?></span>
        <?php else: ?>
            <?= rex_escape($text) ?>
        <?php endif; ?>
    <?php endif; ?>
</<?= $tag ?>>

<?php if ($containerWidth): ?>
</div>
<?php endif; ?>

<?php if ($hasSection): ?>
</section>
<?php endif; ?>

<?php if ($underline): ?>
<style>
.cb-headline-underline {
    padding-bottom: 15px;
    border-bottom: 3px solid currentColor;
    display: inline-block;
}
</style>
<?php endif; ?>
