<?php
/**
 * UIkit Template für Text & Bild Element
 * @var array $elementData
 */

$headline = $elementData['headline'] ?? '';
$headlineTag = $elementData['headline_tag'] ?? 'h2';
$text = $elementData['text'] ?? '';
$image = $elementData['image'] ?? '';
$imageAlt = $elementData['image_alt'] ?? $headline;
$imageRatio = $elementData['image_ratio'] ?? 'auto';
$layout = $elementData['layout'] ?? 'image_text';
$spacing = $elementData['spacing'] ?? 'default';

// Link
$linkType = $elementData['link_type'] ?? '';
$linkUrl = $elementData['link_url'] ?? '';
$linkInternal = $elementData['link_internal'] ?? '';
$linkText = $elementData['link_text'] ?? 'Mehr erfahren';
$linkTarget = ($elementData['link_target'] ?? '_self') === '_blank' ? ' target="_blank"' : '';

// Link URL ermitteln
$href = '';
if ($linkType === 'external' && $linkUrl) {
    $href = $linkUrl;
} elseif ($linkType === 'internal' && $linkInternal) {
    $href = rex_getUrl($linkInternal);
}

// Bild URL via Media Manager
$imageSrc = '';
if ($image) {
    $imageSrc = rex_media_manager::getUrl('content_text_image', $image);
}

// Spacing Mapping
$spacingMap = [
    'default' => '',
    'compact' => 'uk-grid-small',
    'spacious' => 'uk-grid-large'
];
$gridSpacing = $spacingMap[$spacing] ?? '';

// Horizontale Layouts (links/rechts)
$isHorizontal = in_array($layout, ['image_text', 'text_image']);

// Grid Klassen
$gridClasses = ['uk-grid-match', 'uk-flex-middle'];
if ($gridSpacing) {
    $gridClasses[] = $gridSpacing;
}
$gridClassStr = implode(' ', $gridClasses);

// Bild mit optionalem Ratio rendern
$renderImage = function($imageSrc, $imageAlt, $imageRatio) {
    if (empty($imageSrc)) return '';
    
    $output = '';
    
    if ($imageRatio && $imageRatio !== 'auto') {
        // Ratio-Container mit padding-bottom Trick für festes Seitenverhältnis
        $ratioMap = [
            '1-1' => '100%',      // 1:1
            '4-3' => '75%',       // 3/4 = 75%
            '16-9' => '56.25%',   // 9/16 = 56.25%
            '21-9' => '42.86%'    // 9/21 = 42.86%
        ];
        $paddingBottom = $ratioMap[$imageRatio] ?? '56.25%';
        
        $output .= '<div class="uk-inline-clip uk-transition-toggle" style="position: relative; width: 100%; padding-bottom: ' . $paddingBottom . '; overflow: hidden;">';
        $output .= '<img src="' . $imageSrc . '" alt="' . rex_escape($imageAlt) . '" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">';
        $output .= '</div>';
    } else {
        $output .= '<img src="' . $imageSrc . '" alt="' . rex_escape($imageAlt) . '" class="uk-width-1-1">';
    }
    
    return $output;
};

// Text-Content rendern
$renderContent = function($headline, $headlineTag, $text, $href, $linkText, $linkTarget) {
    $output = '';
    
    if ($headline) {
        $output .= '<' . $headlineTag . ' class="uk-margin-small-bottom">' . rex_escape($headline) . '</' . $headlineTag . '>';
    }
    if ($text) {
        $output .= '<div class="uk-text-default">' . $text . '</div>';
    }
    if ($href && $linkText) {
        $output .= '<p class="uk-margin-top"><a href="' . $href . '"' . $linkTarget . ' class="uk-button uk-button-default">' . rex_escape($linkText) . '</a></p>';
    }
    
    return $output;
};

$imageHtml = $renderImage($imageSrc, $imageAlt, $imageRatio);
$contentHtml = $renderContent($headline, $headlineTag, $text, $href, $linkText, $linkTarget);
?>

<div class="text-image-element">
    <?php if ($isHorizontal): ?>
        <!-- Horizontales Layout -->
        <div class="<?= $gridClassStr ?>" uk-grid>
            <?php if ($layout === 'image_text'): ?>
                <!-- Bild links, Text rechts -->
                <div class="uk-width-1-2@m">
                    <?= $imageHtml ?>
                </div>
                <div class="uk-width-1-2@m">
                    <?= $contentHtml ?>
                </div>
            <?php else: ?>
                <!-- Text links, Bild rechts -->
                <div class="uk-width-1-2@m">
                    <?= $contentHtml ?>
                </div>
                <div class="uk-width-1-2@m">
                    <?= $imageHtml ?>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <!-- Vertikales Layout (oben/unten) -->
        <?php if ($layout === 'image_top'): ?>
            <!-- Bild oben, Text unten -->
            <div class="uk-margin-bottom">
                <?= $imageHtml ?>
            </div>
            <div>
                <?= $contentHtml ?>
            </div>
        <?php else: ?>
            <!-- Text oben, Bild unten -->
            <div class="uk-margin-bottom">
                <?= $contentHtml ?>
            </div>
            <div>
                <?= $imageHtml ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
