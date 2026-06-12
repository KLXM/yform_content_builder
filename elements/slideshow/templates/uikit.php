<?php
/**
 * Slideshow Element - UIkit Template
 * @var array $elementData
 */

// Hilfsfunktionen
if (!function_exists('getMediaType')) {
    function getMediaType($media) {
        if (empty($media)) return 'image';
        $extension = strtolower(pathinfo($media, PATHINFO_EXTENSION));
        $videoExtensions = ['mp4', 'webm', 'ogg', 'avi', 'mov'];
        return in_array($extension, $videoExtensions) ? 'video' : 'image';
    }
}

if (!function_exists('backendWrapper')) {
    function backendWrapper($open = true) {
        if (!rex::isBackend()) return '';
        return $open ? '<div class="rex-content-builder-backend-wrapper">' : '</div>';
    }
}

// Globale Slideshow-Einstellungen
$ratio = $elementData['ratio'] ?? '16:9';
$animation = $elementData['animation'] ?? 'fade';
$autoplay = !empty($elementData['autoplay']);
$interval = $elementData['interval'] ?? '6000';
$showNavigation = !empty($elementData['show_navigation']);
$showDots = !empty($elementData['show_dots']);
$isViewport = !empty($elementData['is_viewport']);

// Layout-Einstellungen
$container = $elementData['container'] ?? '';
$margin = $elementData['margin'] ?? '';
$customId = $elementData['custom_id'] ?? '';
$customClasses = $elementData['custom_classes'] ?? '';

// Section-Einstellungen
$sectionBg = $elementData['section_bg'] ?? '';
$sectionBgImage = $elementData['section_bg_image'] ?? '';
$sectionPadding = $elementData['section_padding'] ?? '';
$sectionLight = !empty($elementData['section_light']);
$enableSection = !isset($elementData['enable_section']) || !empty($elementData['enable_section']);
$enableContainer = !isset($elementData['enable_container']) || !empty($elementData['enable_container']);

// Slides abrufen
$slides = $elementData['slides'] ?? [];

if (empty($slides)) {
    return;
}

// Backend Wrapper
echo backendWrapper(true);

$wrapper = new rex_fragment();
$wrapper->setVar('enable_section', $enableSection, false);
$wrapper->setVar('enable_container', $enableContainer, false);
$wrapper->setVar('section_bg', $sectionBg, false);
$wrapper->setVar('section_bg_image', $sectionBgImage, false);
$wrapper->setVar('section_padding', $sectionPadding, false);
$wrapper->setVar('container_width', $container, false);
$wrapper->setVar('section_light', $sectionLight, false);

$wrapperClose = new rex_fragment();
$wrapperClose->setVar('mode', 'close', false);
$wrapperClose->setVar('enable_section', $enableSection, false);
$wrapperClose->setVar('enable_container', $enableContainer, false);
$wrapperClose->setVar('section_bg_image', $sectionBgImage, false);
$wrapperClose->setVar('container_width', $container, false);

echo $wrapper->parse('ycb_elements/wrapper.php');

echo '<div class="wellings-slideshow ' . $margin . ' ' . $customClasses . '"' . (!empty($customId) ? ' id="' . rex_escape($customId) . '"' : '') . '>';

// Slideshow-Konfiguration
$slideshowConfig = [
    'animation' => $animation,
    'autoplay' => $autoplay ? 'true' : 'false',
    'autoplay-interval' => $interval,
    'pause-on-hover' => 'false'
];

// Ratio konfigurieren
if ($ratio === 'viewport' || $isViewport) {
    $slideshowConfig['ratio'] = 'false';
    $viewportHeight = ' uk-height-viewport="min-height: 400"';
} else {
    $slideshowConfig['ratio'] = $ratio;
    $viewportHeight = '';
}

$configString = implode('; ', array_map(fn($k, $v) => "$k: $v", array_keys($slideshowConfig), $slideshowConfig));

echo '<div class="uk-position-relative uk-visible-toggle uk-light wellings-slideshow-container" uk-slideshow="' . $configString . '">';
echo '<div class="uk-slideshow-items"' . $viewportHeight . '>';

foreach ($slides as $slide) {
    $media = $slide['media'] ?? '';
    if (empty($media)) continue;

    $title = $slide['title'] ?? '';
    $text = $slide['text'] ?? '';

    // Link-Einstellungen
    $linkType = $slide['link_type'] ?? 'button';
    $link = $slide['link'] ?? '';
    $linkText = $slide['link_text'] ?? '';
    $linkTarget = $slide['link_target'] ?? '_self';
    $buttonStyle = $slide['button_style'] ?? 'uk-button-primary';
    $buttonSize = $slide['button_size'] ?? '';

    // Text-Design
    $textPosition = $slide['text_position'] ?? 'uk-position-bottom-center uk-text-center';
    $textBackground = $slide['text_background'] ?? 'glass';
    $textAlign = $slide['text_align'] ?? 'uk-text-center';
    $titleSize = $slide['title_size'] ?? 'uk-heading-large';
    $textSize = $slide['text_size'] ?? 'uk-text-lead';
    $titleHandwriting = !empty($slide['title_handwriting']);
    $titleSlanted = !empty($slide['title_slanted']);

    // Media-Type ermitteln
    $mediaType = getMediaType($media);
    $mediaObj = rex_media::get($media);
    $altText = $mediaObj ? $mediaObj->getValue('title') : '';

    // Link-URL aufbereiten
    $hasLink = !empty($link);
    $finalLinkUrl = '';
    if ($hasLink) {
        if (is_numeric($link)) {
            $finalLinkUrl = rex_getUrl($link);
        } else {
            $finalLinkUrl = $link;
        }
    }

    echo '<div class="wellings-slide-item">';

    // Bei Link-Type 'slide' wird der gesamte Slide verlinkt
    if ($hasLink && $linkType === 'slide') {
        echo '<a href="' . $finalLinkUrl . '" target="' . $linkTarget . '" class="uk-link-reset wellings-slide-link">';
    }

    if ($mediaType === 'video') {
        echo '<video class="uk-width-1-1 wellings-slide-media" autoplay muted loop playsinline uk-cover>';
        echo '<source src="' . rex_url::media($media) . '" type="video/' . pathinfo($media, PATHINFO_EXTENSION) . '">';
        echo '</video>';
    } else {
        echo '<div class="uk-position-cover wellings-slide-media">';
        echo '<img src="' . rex_media_manager::getUrl('content_slideshow', $media) . '" alt="' . rex_escape($altText) . '" class="wellings-slide-image" uk-cover>';
        echo '</div>';
    }

    if (!empty($title) || !empty($text) || ($hasLink && $linkType === 'button')) {
        echo '<div class="' . $textPosition . ' uk-position-medium wellings-slide-text wellings-text-' . $textBackground . ' ' . $textAlign . '">';

        if (!empty($title)) {
            echo '<div class="wellings-textmarker-wrapper">';
            $titleClasses = [$titleSize];
            if ($titleHandwriting) $titleClasses[] = 'uk-text-handwriting';
            if ($titleSlanted) $titleClasses[] = 'uk-text-slanted';
            $titleClasses[] = 'wellings-slide-title';
            echo '<h2 class="' . implode(' ', $titleClasses) . '" uk-slideshow-parallax="x: 200,0,0;">';
            echo rex_escape($title);
            echo '</h2>';
            echo '</div>';
        }

        if (!empty($text)) {
            echo '<div class="wellings-textmarker-wrapper">';
            echo '<p class="' . $textSize . ' wellings-slide-description" uk-slideshow-parallax="x: 300,0,0;">';
            echo nl2br(rex_escape($text));
            echo '</p>';
            echo '</div>';
        }

        if ($hasLink && $linkType === 'button' && !empty($linkText)) {
            echo '<div class="uk-margin-top" uk-slideshow-parallax="x: 400,0,0;">';
            $buttonClasses = ['uk-button', $buttonStyle];
            if (!empty($buttonSize)) $buttonClasses[] = $buttonSize;
            $buttonClasses[] = 'wellings-slide-button';
            echo '<a href="' . $finalLinkUrl . '" target="' . $linkTarget . '" class="' . implode(' ', $buttonClasses) . '">';
            echo rex_escape($linkText);
            if ($linkTarget === '_blank') {
                echo '<span uk-icon="external-link" class="uk-margin-small-left"></span>';
            } else {
                echo '<span uk-icon="chevron-right" class="uk-margin-small-left"></span>';
            }
            echo '</a>';
            echo '</div>';
        }

        echo '</div>';
    }

    // Schließen des Link-Tags bei Link-Type 'slide'
    if ($hasLink && $linkType === 'slide') {
        echo '</a>';
    }

    echo '</div>';
}

echo '</div>';

// Navigation
if ($showNavigation) {
    echo '<a class="uk-position-center-left uk-position-small uk-hidden-hover wellings-slidenav-previous" href="#" uk-slidenav-previous uk-slideshow-item="previous" aria-label="Vorheriger Slide"></a>';
    echo '<a class="uk-position-center-right uk-position-small uk-hidden-hover wellings-slidenav-next" href="#" uk-slidenav-next uk-slideshow-item="next" aria-label="Nächster Slide"></a>';
}

if ($showDots) {
    echo '<div class="uk-position-bottom-center uk-position-small">';
    echo '<ul class="uk-slideshow-nav uk-dotnav uk-flex-center wellings-slideshow-dotnav"></ul>';
    echo '</div>';
}

echo '</div>';
echo '</div>';

echo $wrapperClose->parse('ycb_elements/wrapper.php');

// Backend Wrapper schließen
echo backendWrapper(false);
?>
