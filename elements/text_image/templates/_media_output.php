<?php
/**
 * Media Output Helper für Text & Media Element
 * Variablen werden vom Parent-Template bereitgestellt:
 * $media, $mediaSrc, $mediaAlt, $mediaCover, $mediaRatio, $mediaLightbox
 * $videoControls (autoplay|controls|hover)
 */

if (empty($media)) return;

// Media-Typ ermitteln
$isVideoFile = yform_content_builder_helper::isVideo($media);
$isImageFile = yform_content_builder_helper::isImage($media);

// Video-Optionen mit Defaults
$videoControls = $videoControls ?? 'autoplay';

// Ratio-Container wenn gewünscht
$ratioMap = [
    '1-1' => '100%',
    '4-3' => '75%',
    '16-9' => '56.25%',
    '21-9' => '42.86%'
];
$hasRatio = $mediaRatio && $mediaRatio !== 'auto' && isset($ratioMap[$mediaRatio]);
$paddingBottom = $hasRatio ? $ratioMap[$mediaRatio] : '';

// Cover-Container Styles
// Bei Ratio: padding-bottom Trick für festes Seitenverhältnis
// Bei Cover ohne Ratio: Bild normal anzeigen mit object-fit: cover
$useAbsolutePositioning = $hasRatio; // Nur bei festem Ratio absolute positionieren
$coverContainerStyle = '';
$coverMediaStyle = '';

if ($hasRatio) {
    // Festes Seitenverhältnis mit padding-bottom Trick
    $coverContainerStyle = 'position: relative; width: 100%; overflow: hidden; padding-bottom: ' . $paddingBottom . ';';
    $coverMediaStyle = 'position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;';
} elseif ($mediaCover) {
    // Cover ohne Ratio: Bild füllt Container normal aus
    $coverMediaStyle = 'width: 100%; height: 100%; object-fit: cover;';
}

if ($isVideoFile): ?>
    <?php
    $videoSrc = rex_url::media($media);
    $videoExt = strtolower(pathinfo($media, PATHINFO_EXTENSION));
    
    // Attribute je nach Controls-Einstellung
    $videoAttrs = '';
    switch ($videoControls) {
        case 'controls':
            $videoAttrs = 'controls preload="metadata"';
            break;
        case 'hover':
            $videoAttrs = 'muted loop playsinline preload="metadata" uk-video="autoplay: hover"';
            break;
        case 'autoplay':
        default:
            $videoAttrs = 'muted loop playsinline uk-video="autoplay: inview"';
            break;
    }
    
    if ($mediaLightbox): ?>
        <!-- Video mit Lightbox -->
        <div class="uk-inline uk-width-1-1 uk-transition-toggle" uk-lightbox="video-autoplay: true"<?php if ($coverContainerStyle): ?> style="<?= $coverContainerStyle ?>"<?php endif; ?>>
            <a href="<?= $videoSrc ?>" data-caption="<?= rex_escape($mediaAlt) ?>" data-type="video">
                <!-- Erstes Frame als Poster via Video -->
                <video <?= $videoAttrs ?> 
                       src="<?= $videoSrc ?>"
                       <?php if ($coverMediaStyle): ?>style="<?= $coverMediaStyle ?>"<?php else: ?>class="uk-width-1-1"<?php endif; ?>></video>
                
                <!-- Play-Button Overlay -->
                <div class="uk-position-center uk-transition-fade">
                    <div class="uk-icon-button uk-box-shadow-large" 
                         style="background: rgba(0,0,0,0.6); width: 70px; height: 70px; border-radius: 50%;">
                        <span uk-icon="icon: play; ratio: 2.5" style="color: #fff;"></span>
                    </div>
                </div>
            </a>
        </div>
    <?php else: ?>
        <!-- Video inline -->
        <?php if ($coverContainerStyle): ?>
        <div class="uk-cover-container" style="<?= $coverContainerStyle ?>">
            <video <?= $videoAttrs ?> 
                   src="<?= $videoSrc ?>"
                   uk-cover></video>
        </div>
        <?php else: ?>
        <video <?= $videoAttrs ?> 
               src="<?= $videoSrc ?>"
               class="uk-width-1-1"></video>
        <?php endif; ?>
    <?php endif; ?>
    
<?php elseif ($isImageFile && $mediaSrc): ?>
    <?php if ($mediaLightbox): ?>
        <!-- Bild mit Lightbox -->
        <?php if ($coverContainerStyle): ?>
        <div uk-lightbox style="<?= $coverContainerStyle ?>">
            <a href="<?= rex_url::media($media) ?>" data-caption="<?= rex_escape($mediaAlt) ?>">
                <img loading="lazy" 
                     src="<?= $mediaSrc ?>" 
                     alt="<?= rex_escape($mediaAlt) ?>"
                     style="<?= $coverMediaStyle ?>">
            </a>
        </div>
        <?php else: ?>
        <div uk-lightbox>
            <a href="<?= rex_url::media($media) ?>" data-caption="<?= rex_escape($mediaAlt) ?>">
                <img loading="lazy" 
                     src="<?= $mediaSrc ?>" 
                     alt="<?= rex_escape($mediaAlt) ?>"
                     <?php if ($coverMediaStyle): ?>style="<?= $coverMediaStyle ?>"<?php else: ?>class="uk-width-1-1"<?php endif; ?>>
            </a>
        </div>
        <?php endif; ?>
    <?php else: ?>
        <!-- Bild normal -->
        <?php if ($coverContainerStyle): ?>
        <div class="uk-inline-clip" style="<?= $coverContainerStyle ?>">
            <img loading="lazy" 
                 src="<?= $mediaSrc ?>" 
                 alt="<?= rex_escape($mediaAlt) ?>"
                 style="<?= $coverMediaStyle ?>">
        </div>
        <?php elseif ($coverMediaStyle): ?>
        <img loading="lazy" 
             src="<?= $mediaSrc ?>" 
             alt="<?= rex_escape($mediaAlt) ?>"
             style="<?= $coverMediaStyle ?>">
        <?php else: ?>
        <img loading="lazy" 
             src="<?= $mediaSrc ?>" 
             alt="<?= rex_escape($mediaAlt) ?>"
             class="uk-width-1-1">
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>
