<?php
/**
 * Media Output Helper für Cards
 * Variablen werden vom Parent-Template bereitgestellt:
 * $image, $imageSrc, $imageAlt, $imageTitle, $mediaLightbox, $mediaCover, $isVideo, $isImage
 */

if (empty($image)) return;

$isVideoFile = $isVideo($image);
$isImageFile = $isImage($image);

if ($isVideoFile): ?>
    <?php
    // Video-Ausgabe
    $videoSrc = rex_url::media($image);
    $posterSrc = rex_media_manager::getUrl('content_card', $image);
    
    if ($mediaLightbox): ?>
        <div class="uk-inline uk-width-1-1" uk-lightbox="video-autoplay: true">
            <video loading="lazy" src="<?= $videoSrc ?>" 
                   poster="<?= $posterSrc ?>" 
                   uk-video="automute: true; autoplay: false" 
                   playsinline 
                   <?= $mediaCover ? 'uk-cover' : 'class="uk-width-1-1"' ?>></video>
            <div class="uk-position-center uk-light">
                <a class="uk-icon-button uk-box-shadow-large" 
                   style="background: rgba(0,0,0,0.5); width: 60px; height: 60px;" 
                   href="<?= $videoSrc ?>" 
                   data-caption="<?= rex_escape($imageTitle ?: $imageAlt) ?>" 
                   data-type="video">
                    <span uk-icon="icon: play-circle; ratio: 2"></span>
                </a>
            </div>
        </div>
    <?php else: ?>
        <video loading="lazy" src="<?= $videoSrc ?>" 
               poster="<?= $posterSrc ?>" 
               uk-video="automute: true; autoplay: inview" 
               muted loop playsinline 
               <?= $mediaCover ? 'uk-cover' : 'class="uk-width-1-1"' ?>></video>
    <?php endif; ?>
    
<?php elseif ($isImageFile && $imageSrc): ?>
    <?php if ($mediaLightbox): ?>
        <div uk-lightbox>
            <a href="<?= rex_url::media($image) ?>" data-caption="<?= rex_escape($imageTitle ?: $imageAlt) ?>">
                <img loading="lazy" 
                     src="<?= $imageSrc ?>" 
                     alt="<?= rex_escape($imageAlt) ?>" 
                     <?= $mediaCover ? 'uk-cover' : 'class="uk-width-1-1"' ?>>
            </a>
        </div>
    <?php else: ?>
        <img loading="lazy" 
             src="<?= $imageSrc ?>" 
             alt="<?= rex_escape($imageAlt) ?>"
             title="<?= rex_escape($imageTitle) ?>"
             <?= $mediaCover ? 'uk-cover' : 'class="uk-width-1-1"' ?>>
    <?php endif; ?>
<?php endif; ?>
