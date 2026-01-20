<?php
/**
 * Media Output Helper für Cards
 * Variablen werden vom Parent-Template bereitgestellt:
 * $image, $imageSrc, $imageAlt, $imageTitle, $mediaLightbox, $mediaCover, $isVideo, $isImage
 * $videoDisplay (inline|poster), $videoControls (autoplay|controls|hover)
 */

if (empty($image)) return;

$isVideoFile = $isVideo($image);
$isImageFile = $isImage($image);

// Video-Optionen mit Defaults
$videoDisplay = $videoDisplay ?? 'inline';
$videoControls = $videoControls ?? 'autoplay';

if ($isVideoFile): ?>
    <?php
    $videoSrc = rex_url::media($image);
    
    // Poster via Media Manager - prüfen ob Typ existiert
    // Fallback: Video direkt als Poster (Browser zeigt erstes Frame) oder content_card Typ
    $posterSrc = '';
    $mediaManagerTypes = rex_media_manager::getSupportedEffects() ? true : false; // Prüfen ob MM aktiv
    
    // Versuche video_poster Typ
    $sql = rex_sql::factory();
    $sql->setQuery('SELECT id FROM ' . rex::getTable('media_manager_type') . ' WHERE name = ? LIMIT 1', ['video_poster']);
    if ($sql->getRows() > 0) {
        $posterSrc = rex_media_manager::getUrl('video_poster', $image);
    } else {
        // Fallback: content_card Typ (falls vorhanden) oder Video selbst
        $sql->setQuery('SELECT id FROM ' . rex::getTable('media_manager_type') . ' WHERE name = ? LIMIT 1', ['content_card']);
        if ($sql->getRows() > 0) {
            $posterSrc = rex_media_manager::getUrl('content_card', $image);
        } else {
            // Letzter Fallback: Video selbst (Browser zeigt erstes Frame)
            $posterSrc = $videoSrc;
        }
    }
    
    // ========================================================================
    // LIGHTBOX MODUS: Immer Standbild mit Play-Button zeigen
    // ========================================================================
    if ($mediaLightbox): ?>
        <div class="uk-inline uk-width-1-1 uk-transition-toggle" uk-lightbox="video-autoplay: true">
            <a href="<?= $videoSrc ?>" 
               data-caption="<?= rex_escape($imageTitle ?: $imageAlt) ?>" 
               data-type="video">
                <!-- Poster-Bild -->
                <img loading="lazy" 
                     src="<?= $posterSrc ?>" 
                     alt="<?= rex_escape($imageAlt ?: 'Video abspielen') ?>"
                     <?= $mediaCover ? 'uk-cover' : 'class="uk-width-1-1"' ?>>
                
                <!-- Play-Button Overlay -->
                <div class="uk-position-center">
                    <div class="uk-icon-button uk-box-shadow-large uk-transition-scale-up" 
                         style="background: rgba(0,0,0,0.6); width: 70px; height: 70px; border-radius: 50%;">
                        <span uk-icon="icon: play; ratio: 2.5" style="color: #fff;"></span>
                    </div>
                </div>
            </a>
        </div>
    
    <?php 
    // ========================================================================
    // POSTER MODUS: Standbild mit Play-Button, Video startet bei Klick
    // ========================================================================
    elseif ($videoDisplay === 'poster'): 
        $posterContainerId = 'video-poster-' . uniqid();
    ?>
        <div class="uk-inline uk-width-1-1 uk-position-relative" id="<?= $posterContainerId ?>">
            <!-- Poster-Bild -->
            <img loading="lazy" 
                 src="<?= $posterSrc ?>" 
                 alt="<?= rex_escape($imageAlt ?: 'Video abspielen') ?>"
                 class="video-poster-image uk-width-1-1"
                 <?= $mediaCover ? 'uk-cover' : '' ?>>
            
            <!-- Play-Button Overlay -->
            <div class="uk-position-center video-play-button" style="cursor: pointer;">
                <div class="uk-icon-button uk-box-shadow-large" 
                     style="background: rgba(0,0,0,0.6); width: 70px; height: 70px; border-radius: 50%; transition: transform 0.2s;">
                    <span uk-icon="icon: play; ratio: 2.5" style="color: #fff;"></span>
                </div>
            </div>
            
            <!-- Verstecktes Video (wird bei Klick eingeblendet) -->
            <video class="video-player uk-width-1-1" 
                   src="<?= $videoSrc ?>" 
                   style="display: none;"
                   <?= $videoControls === 'controls' ? 'controls' : 'muted loop' ?>
                   playsinline
                   <?= $mediaCover ? 'uk-cover' : '' ?>></video>
        </div>
        
        <script nonce="<?= rex_response::getNonce() ?>">
        (function() {
            var container = document.getElementById('<?= $posterContainerId ?>');
            if (!container || container.dataset.initialized) return;
            container.dataset.initialized = 'true';
            
            var poster = container.querySelector('.video-poster-image');
            var button = container.querySelector('.video-play-button');
            var video = container.querySelector('.video-player');
            
            if (button && video && poster) {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    poster.style.display = 'none';
                    button.style.display = 'none';
                    video.style.display = 'block';
                    video.play();
                });
            }
        })();
        </script>
    
    <?php 
    // ========================================================================
    // INLINE MODUS: Video direkt abspielen
    // ========================================================================
    else: 
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
    ?>
        <video loading="lazy" 
               src="<?= $videoSrc ?>" 
               poster="<?= $posterSrc ?>"
               <?= $videoAttrs ?>
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
