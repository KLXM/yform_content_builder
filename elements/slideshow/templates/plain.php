<?php
/**
 * Slideshow Element - Plain Template (kein Framework)
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

// Einfache Slideshow ohne JavaScript-Framework
$slideshowId = 'slideshow-' . uniqid();
echo '<div id="' . $slideshowId . '" class="wellings-slideshow-container" style="position: relative; overflow: hidden;">';
echo '<div class="wellings-slideshow-items" style="display: flex; transition: transform 0.5s ease;">';

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

    // Slide Style für Ratio
    $slideStyle = 'position: relative; flex: 0 0 100%; width: 100%;';
    if ($ratio === '16:9') {
        $slideStyle .= ' padding-bottom: 56.25%;';
    } elseif ($ratio === '4:3') {
        $slideStyle .= ' padding-bottom: 75%;';
    } elseif ($ratio === '1:1') {
        $slideStyle .= ' padding-bottom: 100%;';
    } elseif ($ratio === '21:9') {
        $slideStyle .= ' padding-bottom: 42.86%;';
    } elseif ($isViewport) {
        $slideStyle .= ' height: 100vh; min-height: 400px;';
    }

    echo '<div class="wellings-slide-item" style="' . $slideStyle . '">';

    // Bei Link-Type 'slide' wird der gesamte Slide verlinkt
    if ($hasLink && $linkType === 'slide') {
        echo '<a href="' . $finalLinkUrl . '" target="' . $linkTarget . '" style="text-decoration: none; color: inherit; display: block; height: 100%;">';
    }

    if ($mediaType === 'video') {
        echo '<video style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;" autoplay muted loop playsinline>';
        echo '<source src="' . rex_url::media($media) . '" type="video/' . pathinfo($media, PATHINFO_EXTENSION) . '">';
        echo '</video>';
    } else {
        echo '<img src="' . rex_media_manager::getUrl('image_hero', $media) . '" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;" alt="' . rex_escape($altText) . '">';
    }

    if (!empty($title) || !empty($text) || ($hasLink && $linkType === 'button')) {
        // Text-Overlay Position
        $overlayStyle = 'position: absolute; z-index: 2; padding: 20px;';
        if (strpos($textPosition, 'bottom') !== false) {
            $overlayStyle .= ' bottom: 0; left: 0; right: 0;';
        } elseif (strpos($textPosition, 'top') !== false) {
            $overlayStyle .= ' top: 0; left: 0; right: 0;';
        } else {
            $overlayStyle .= ' top: 50%; left: 50%; transform: translate(-50%, -50%);';
        }

        // Text-Hintergrund
        $bgStyle = '';
        if ($textBackground === 'glass') {
            $bgStyle = 'background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-radius: 8px; padding: 15px;';
        } elseif ($textBackground === 'dark') {
            $bgStyle = 'background: rgba(0, 0, 0, 0.7); color: white; border-radius: 8px; padding: 15px;';
        } elseif ($textBackground === 'light') {
            $bgStyle = 'background: rgba(255, 255, 255, 0.9); border-radius: 8px; padding: 15px;';
        }

        echo '<div class="wellings-slide-text wellings-text-' . $textBackground . ' ' . $textAlign . '" style="' . $overlayStyle . $bgStyle . '">';

        if (!empty($title)) {
            echo '<div class="wellings-textmarker-wrapper">';
            $titleClasses = [$titleSize, 'wellings-slide-title'];
            if ($titleHandwriting) $titleClasses[] = 'font-handwriting';
            if ($titleSlanted) $titleClasses[] = 'font-italic';
            $titleStyle = 'margin: 0 0 10px 0;';
            if ($titleHandwriting) $titleStyle .= ' font-family: cursive;';
            if ($titleSlanted) $titleStyle .= ' font-style: italic;';
            echo '<' . $titleSize . ' class="' . implode(' ', $titleClasses) . '" style="' . $titleStyle . '">';
            echo rex_escape($title);
            echo '</' . $titleSize . '>';
            echo '</div>';
        }

        if (!empty($text)) {
            echo '<div class="wellings-textmarker-wrapper">';
            $textStyle = 'margin: 0 0 15px 0;';
            if ($textSize === 'lead') $textStyle .= ' font-size: 1.1em; font-weight: 300;';
            elseif ($textSize === 'large') $textStyle .= ' font-size: 1.2em;';
            echo '<p class="' . $textSize . ' wellings-slide-description" style="' . $textStyle . '">';
            echo nl2br(rex_escape($text));
            echo '</p>';
            echo '</div>';
        }

        if ($hasLink && $linkType === 'button' && !empty($linkText)) {
            echo '<div style="margin-top: 15px;">';
            $buttonStyleAttr = 'display: inline-block; padding: 8px 16px; text-decoration: none; border-radius: 4px; border: none; cursor: pointer;';
            if ($buttonStyle === 'btn-primary') {
                $buttonStyleAttr .= ' background: #007bff; color: white;';
            } elseif ($buttonStyle === 'btn-secondary') {
                $buttonStyleAttr .= ' background: #6c757d; color: white;';
            } elseif ($buttonStyle === 'btn-success') {
                $buttonStyleAttr .= ' background: #28a745; color: white;';
            } else {
                $buttonStyleAttr .= ' background: #007bff; color: white;';
            }
            if ($buttonSize === 'btn-sm') {
                $buttonStyleAttr .= ' padding: 6px 12px; font-size: 0.875em;';
            } elseif ($buttonSize === 'btn-lg') {
                $buttonStyleAttr .= ' padding: 10px 20px; font-size: 1.125em;';
            }
            echo '<a href="' . $finalLinkUrl . '" target="' . $linkTarget . '" class="' . $buttonStyle . ' ' . $buttonSize . ' wellings-slide-button" style="' . $buttonStyleAttr . '">';
            echo rex_escape($linkText);
            if ($linkTarget === '_blank') {
                echo ' ↗';
            } else {
                echo ' →';
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
    echo '<button class="wellings-slidenav-previous" style="position: absolute; top: 50%; left: 10px; transform: translateY(-50%); background: rgba(0,0,0,0.5); color: white; border: none; padding: 10px; cursor: pointer; z-index: 3;" onclick="changeSlide(-1, \'' . $slideshowId . '\')">&larr;</button>';
    echo '<button class="wellings-slidenav-next" style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); background: rgba(0,0,0,0.5); color: white; border: none; padding: 10px; cursor: pointer; z-index: 3;" onclick="changeSlide(1, \'' . $slideshowId . '\')">&rarr;</button>';
}

if ($showDots) {
    echo '<div class="wellings-slideshow-dotnav" style="position: absolute; bottom: 10px; left: 50%; transform: translateX(-50%); z-index: 3;">';
    for ($i = 0; $i < $slideIndex; $i++) {
        $activeClass = $i === 0 ? ' active' : '';
        echo '<button style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: rgba(255,255,255,0.5); border: none; margin: 0 5px; cursor: pointer;' . ($i === 0 ? ' background: white;' : '') . '" onclick="goToSlide(' . $i . ', \'' . $slideshowId . '\')"></button>';
    }
    echo '</div>';
}

echo '</div>';

// JavaScript für einfache Slideshow-Funktionalität
if ($autoplay || $showNavigation || $showDots) {
    echo '<script>
    (function() {
        var slideshowId = "' . $slideshowId . '";
        var currentSlide = 0;
        var totalSlides = ' . $slideIndex . ';
        var autoplayInterval = ' . ($autoplay ? $interval : 'false') . ';
        var autoplayTimer = null;

        function updateSlideshow() {
            var container = document.querySelector("#" + slideshowId + " .wellings-slideshow-items");
            if (container) {
                container.style.transform = "translateX(-" + (currentSlide * 100) + "%)";
            }

            // Dots aktualisieren
            var dots = document.querySelectorAll("#" + slideshowId + " .wellings-slideshow-dotnav button");
            dots.forEach(function(dot, index) {
                dot.style.background = index === currentSlide ? "white" : "rgba(255,255,255,0.5)";
            });
        }

        window.changeSlide = function(direction, id) {
            if (id === slideshowId) {
                currentSlide = (currentSlide + direction + totalSlides) % totalSlides;
                updateSlideshow();
                resetAutoplay();
            }
        };

        window.goToSlide = function(slideIndex, id) {
            if (id === slideshowId) {
                currentSlide = slideIndex;
                updateSlideshow();
                resetAutoplay();
            }
        };

        function resetAutoplay() {
            if (autoplayTimer) {
                clearInterval(autoplayTimer);
                if (autoplayInterval !== "false") {
                    autoplayTimer = setInterval(function() {
                        currentSlide = (currentSlide + 1) % totalSlides;
                        updateSlideshow();
                    }, parseInt(autoplayInterval));
                }
            }
        }

        // Initialisierung
        updateSlideshow();
        if (autoplayInterval !== "false") {
            autoplayTimer = setInterval(function() {
                currentSlide = (currentSlide + 1) % totalSlides;
                updateSlideshow();
            }, parseInt(autoplayInterval));
        }
    })();
    </script>';
}

echo '</div>';

if (!empty($container)) {
    echo '</div>';
}

// Backend Wrapper schließen
echo backendWrapper(false);
?>