<?php
/**
 * UIkit Template für Text & Media Element
 * Unterstützt Bilder und Videos mit Cover-Modus
 * @var array $elementData
 */

$headline = $elementData['headline'] ?? '';
$headlineTag = $elementData['headline_tag'] ?? 'h2';
$text = $elementData['text'] ?? '';
$layout = $elementData['layout'] ?? 'media_text';
$spacing = $elementData['spacing'] ?? 'default';

// Abwärtskompatibilität: image -> media
$media = $elementData['media'] ?? $elementData['image'] ?? '';
$mediaAlt = $elementData['media_alt'] ?? $elementData['image_alt'] ?? $headline;
$mediaRatio = $elementData['media_ratio'] ?? $elementData['image_ratio'] ?? 'auto';
$mediaCover = !empty($elementData['media_cover']);
$mediaLightbox = !empty($elementData['media_lightbox']);
$videoControls = $elementData['video_controls'] ?? 'autoplay';

// Abwärtskompatibilität: Layout-Namen
if ($layout === 'image_text') $layout = 'media_text';
if ($layout === 'text_image') $layout = 'text_media';

// Section-Einstellungen
$sectionBg = $elementData['section_bg'] ?? '';
$sectionBgImage = $elementData['section_bg_image'] ?? '';
$sectionPadding = $elementData['section_padding'] ?? '';
$containerWidth = $elementData['container_width'] ?? '';
$lightText = !empty($elementData['light_text']);

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

// Media URL via Media Manager (nur für Bilder)
$mediaSrc = '';
if ($media && yform_content_builder_helper::isImage($media)) {
    $mediaSrc = rex_media_manager::getUrl('content_text_image', $media);
}

// Spacing Mapping
$spacingMap = [
    'default' => '',
    'compact' => 'uk-grid-small',
    'spacious' => 'uk-grid-large'
];
$gridSpacing = $spacingMap[$spacing] ?? '';

// Grid Klassen
$gridClasses = ['uk-grid-match', 'uk-flex-middle'];
if ($gridSpacing) {
    $gridClasses[] = $gridSpacing;
}
$gridClassStr = implode(' ', $gridClasses);

// Sektion-Klassen
$sectionClasses = ['uk-section'];
if ($sectionBg) {
    $sectionClasses[] = $sectionBg;
}
if ($sectionPadding) {
    $sectionClasses[] = $sectionPadding;
}
if ($lightText) {
    $sectionClasses[] = 'uk-light';
}

// Hintergrundbild oder -video
$sectionStyle = '';
$sectionBgVideoHtml = '';
$isSectionBgVideo = false;

if (!empty($sectionBgImage)) {
    $bgMediaExt = strtolower(pathinfo($sectionBgImage, PATHINFO_EXTENSION));
    $videoExtensions = ['mp4', 'webm', 'ogg'];
    
    if (in_array($bgMediaExt, $videoExtensions)) {
        $isSectionBgVideo = true;
        $videoSrc = rex_url::media($sectionBgImage);
        $sectionBgVideoHtml = '<video class="uk-cover" autoplay loop muted playsinline uk-cover><source src="' . $videoSrc . '" type="video/' . $bgMediaExt . '"></video>';
        $sectionClasses[] = 'uk-cover-container';
        $sectionClasses[] = 'uk-position-relative';
    } else {
        $bgImageUrl = rex_media_manager::getUrl('content_slideshow', $sectionBgImage);
        $sectionStyle = ' style="background-image: url(\'' . $bgImageUrl . '\'); background-size: cover; background-position: center;"';
        $sectionClasses[] = 'uk-background-cover';
    }
}

// Prüfen ob Section nötig
$hasSection = $sectionBg || $sectionPadding || !empty($sectionBgImage);

// Volle Breite ohne Padding = randlos
$isEdgeless = empty($containerWidth) || $sectionPadding === 'uk-padding-remove';

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

$contentHtml = $renderContent($headline, $headlineTag, $text, $href, $linkText, $linkTarget);

// Media HTML via Include
ob_start();
include __DIR__ . '/_media_output.php';
$mediaHtml = ob_get_clean();
?>

<?php if ($hasSection): ?>
<section class="<?= implode(' ', $sectionClasses) ?>"<?= $sectionStyle ?>>
<?php if ($isSectionBgVideo): ?>
<?= $sectionBgVideoHtml ?>
<div class="uk-position-relative">
<?php endif; ?>
<?php endif; ?>

<?php if (!empty($containerWidth)): ?>
<div class="<?= $containerWidth ?>">
<?php endif; ?>

<div class="text-media-element"<?php if ($isEdgeless): ?> style="width: 100%;"<?php endif; ?>>
    <div class="<?= $gridClassStr ?><?php if ($isEdgeless): ?> uk-grid-collapse<?php endif; ?>" uk-grid>
        <?php if ($layout === 'media_text'): ?>
            <!-- Media links, Text rechts -->
            <div class="uk-width-1-2@m">
                <?= $mediaHtml ?>
            </div>
            <div class="uk-width-1-2@m<?php if ($isEdgeless): ?> uk-padding<?php endif; ?>">
                <?= $contentHtml ?>
            </div>
        <?php else: ?>
            <!-- Text links, Media rechts -->
            <div class="uk-width-1-2@m<?php if ($isEdgeless): ?> uk-padding<?php endif; ?>">
                <?= $contentHtml ?>
            </div>
            <div class="uk-width-1-2@m">
                <?= $mediaHtml ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($containerWidth)): ?>
</div>
<?php endif; ?>

<?php if ($hasSection): ?>
<?php if ($isSectionBgVideo): ?>
</div>
<?php endif; ?>
</section>
<?php endif; ?>
