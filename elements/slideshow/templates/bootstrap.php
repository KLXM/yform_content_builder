<?php
/**
 * Slideshow Element - Bootstrap Template
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

// Slides abrufen
$slides = $elementData['slides'] ?? [];

if (empty($slides)) {
    return;
}

// Backend Wrapper
echo backendWrapper(true);

// Container-Wrapper falls definiert
if (!empty($container)) {
    echo '<div class="' . $container . '">';
}

echo '<div class="wellings-slideshow ' . $margin . ' ' . $customClasses . '"' . (!empty($customId) ? ' id="' . rex_escape($customId) . '"' : '') . '>';

// Bootstrap Carousel Konfiguration
$carouselId = 'carousel-' . uniqid();
$dataAttributes = 'data-ride="carousel"';
if ($autoplay) {
    $dataAttributes .= ' data-interval="' . $interval . '"';
} else {
    $dataAttributes .= ' data-interval="false"';
}

echo '<div id="' . $carouselId . '" class="carousel slide wellings-slideshow-container" ' . $dataAttributes . '>';
echo '<div class="carousel-inner">';

$slideIndex = 0;
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
    $buttonStyle = $slide['button_style'] ?? 'btn-primary';
    $buttonSize = $slide['button_size'] ?? '';

    // Text-Design
    $textPosition = $slide['text_position'] ?? 'text-center';
    $textBackground = $slide['text_background'] ?? 'light';
    $textAlign = $slide['text_align'] ?? 'text-center';
    $titleSize = $slide['title_size'] ?? 'h3';
    $textSize = $slide['text_size'] ?? 'lead';
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

    $activeClass = $slideIndex === 0 ? ' active' : '';
    echo '<div class="carousel-item' . $activeClass . ' wellings-slide-item">';

    // Bei Link-Type 'slide' wird der gesamte Slide verlinkt
    if ($hasLink && $linkType === 'slide') {
        echo '<a href="' . $finalLinkUrl . '" target="' . $linkTarget . '" class="text-decoration-none wellings-slide-link">';
    }

    if ($mediaType === 'video') {
        echo '<video class="d-block w-100 wellings-slide-media" autoplay muted loop playsinline>';
        echo '<source src="' . rex_url::media($media) . '" type="video/' . pathinfo($media, PATHINFO_EXTENSION) . '">';
        echo '</video>';
    } else {
        echo '<img src="' . rex_media_manager::getUrl('content_slideshow', $media) . '" class="d-block w-100 wellings-slide-image" alt="' . rex_escape($altText) . '">';
    }

    if (!empty($title) || !empty($text) || ($hasLink && $linkType === 'button')) {
        echo '<div class="carousel-caption d-none d-md-block wellings-slide-text wellings-text-' . $textBackground . ' ' . $textAlign . '">';

        if (!empty($title)) {
            echo '<div class="wellings-textmarker-wrapper">';
            $titleClasses = [$titleSize, 'wellings-slide-title'];
            if ($titleHandwriting) $titleClasses[] = 'font-handwriting';
            if ($titleSlanted) $titleClasses[] = 'font-italic';
            echo '<' . $titleSize . ' class="' . implode(' ', $titleClasses) . '">';
            echo rex_escape($title);
            echo '</' . $titleSize . '>';
            echo '</div>';
        }

        if (!empty($text)) {
            echo '<div class="wellings-textmarker-wrapper">';
            echo '<p class="' . $textSize . ' wellings-slide-description mb-3">';
            echo nl2br(rex_escape($text));
            echo '</p>';
            echo '</div>';
        }

        if ($hasLink && $linkType === 'button' && !empty($linkText)) {
            echo '<div class="mt-3">';
            $buttonClasses = ['btn', $buttonStyle, 'wellings-slide-button'];
            if (!empty($buttonSize)) $buttonClasses[] = $buttonSize;
            echo '<a href="' . $finalLinkUrl . '" target="' . $linkTarget . '" class="' . implode(' ', $buttonClasses) . '">';
            echo rex_escape($linkText);
            if ($linkTarget === '_blank') {
                echo ' <i class="fas fa-external-link-alt ms-1"></i>';
            } else {
                echo ' <i class="fas fa-chevron-right ms-1"></i>';
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
    $slideIndex++;
}

echo '</div>';

// Navigation
if ($showNavigation) {
    echo '<button class="carousel-control-prev wellings-slidenav-previous" type="button" data-bs-target="#' . $carouselId . '" data-bs-slide="prev">';
    echo '<span class="carousel-control-prev-icon" aria-hidden="true"></span>';
    echo '<span class="visually-hidden">Previous</span>';
    echo '</button>';
    echo '<button class="carousel-control-next wellings-slidenav-next" type="button" data-bs-target="#' . $carouselId . '" data-bs-slide="next">';
    echo '<span class="carousel-control-next-icon" aria-hidden="true"></span>';
    echo '<span class="visually-hidden">Next</span>';
    echo '</button>';
}

if ($showDots) {
    echo '<div class="carousel-indicators wellings-slideshow-dotnav">';
    for ($i = 0; $i < $slideIndex; $i++) {
        $activeClass = $i === 0 ? ' active' : '';
        echo '<button type="button" data-bs-target="#' . $carouselId . '" data-bs-slide-to="' . $i . '" class="' . $activeClass . '" aria-label="Slide ' . ($i + 1) . '"></button>';
    }
    echo '</div>';
}

echo '</div>';
echo '</div>';

if (!empty($container)) {
    echo '</div>';
}

// Backend Wrapper schließen
echo backendWrapper(false);
?>