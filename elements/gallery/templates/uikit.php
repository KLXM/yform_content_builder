<?php
/**
 * Gallery Element - UIkit Template
 * Grid/Masonry Layout für Medien-Galerie
 */

$headline = $elementData['headline'] ?? '';
$layout = $elementData['layout'] ?? 'grid';
$columns = intval($elementData['columns'] ?? 3);
$aspectRatio = $elementData['aspect_ratio'] ?? 'auto';
$gap = $elementData['gap'] ?? 'medium';
$items = $elementData['items'] ?? [];

// UIkit CSS-Klassen
$containerClass = 'gallery-container gallery-' . $layout;
$gridClass = 'uk-grid';

// Gap für UIkit
switch ($gap) {
    case 'small':
        $gridClass .= ' uk-grid-small';
        break;
    case 'large':
        $gridClass .= ' uk-grid-large';
        break;
    case 'medium':
    default:
        $gridClass .= ' uk-grid-medium';
        break;
}

// UIkit Width Klassen
$widthClass = 'uk-width-1-' . $columns . '@m';
if ($columns == 5) {
    $widthClass = 'uk-width-1-5@m';
}

// Aspect Ratio CSS
$aspectRatioStyle = '';
$aspectRatioClass = '';
switch ($aspectRatio) {
    case '16:9':
        $aspectRatioStyle = 'padding-bottom: 56.25%;';
        $aspectRatioClass = ' gallery-aspect-16-9';
        break;
    case '4:3':
        $aspectRatioStyle = 'padding-bottom: 75%;';
        $aspectRatioClass = ' gallery-aspect-4-3';
        break;
    case '1:1':
        $aspectRatioStyle = 'padding-bottom: 100%;';
        $aspectRatioClass = ' gallery-aspect-1-1';
        break;
    case '3:2':
        $aspectRatioStyle = 'padding-bottom: 66.67%;';
        $aspectRatioClass = ' gallery-aspect-3-2';
        break;
    case 'auto':
    default:
        $aspectRatioClass = ' gallery-aspect-auto';
        break;
}
?>

<div class="gallery-element">
    <?php if ($headline): ?>
        <h3 class="gallery-headline uk-heading-line"><?= htmlspecialchars($headline) ?></h3>
    <?php endif; ?>
    
    <div class="<?= $containerClass ?>" data-columns="<?= $columns ?>" data-layout="<?= $layout ?>">
        <div class="<?= $gridClass ?>" uk-grid>
            <?php foreach ($items as $index => $item): ?>
                <?php 
                $media = $item['media'] ?? '';
                $caption = $item['caption'] ?? '';
                $altText = $item['alt_text'] ?? '';
                
                if (!$media) continue;
                
                // Media-Typ bestimmen
                $isImage = $this->isImage($media);
                $isVideo = $this->isVideo($media);
                ?>
                
                <div class="<?= $widthClass ?> uk-width-1-2@s">
                    <div class="gallery-item<?= $aspectRatioClass ?> uk-card uk-card-default uk-card-hover" data-index="<?= $index ?>">
                        <div class="gallery-media-wrapper uk-card-media-top" <?php if ($aspectRatioStyle): ?>style="position: relative; <?= $aspectRatioStyle ?>"<?php endif; ?>>
                            <?php if ($isImage): ?>
                                <img src="<?= rex_url::media($media) ?>" 
                                     alt="<?= htmlspecialchars($altText ?: $caption) ?>"
                                     class="gallery-image"
                                     <?php if ($aspectRatioStyle): ?>style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;"<?php endif; ?> />
                                     
                            <?php elseif ($isVideo): ?>
                                <div class="gallery-video-container">
                                    <video preload="metadata" muted playsinline class="gallery-video"
                                           <?php if ($aspectRatioStyle): ?>style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;"<?php endif; ?>>
                                        <source src="<?= rex_url::media($media) ?>" 
                                                type="video/<?= strtolower(pathinfo($media, PATHINFO_EXTENSION)) ?>" />
                                        Ihr Browser unterstützt dieses Video-Format nicht.
                                    </video>
                                    <div class="gallery-video-overlay">
                                        <span uk-icon="icon: play-circle; ratio: 3"></span>
                                    </div>
                                    <div class="gallery-video-controls uk-position-bottom-left uk-position-small">
                                        <button class="btn-gallery-video-play uk-button uk-button-small uk-button-default" title="Abspielen">
                                            <span uk-icon="play"></span>
                                        </button>
                                        <button class="btn-gallery-video-mute uk-button uk-button-small uk-button-default uk-margin-small-left" title="Stumm schalten">
                                            <span uk-icon="sound"></span>
                                        </button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($caption): ?>
                            <div class="gallery-caption uk-card-body">
                                <p class="uk-text-small uk-margin-remove"><?= htmlspecialchars($caption) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
/* Gallery UIkit Specific Styles */
.gallery-element {
    margin: 40px 0;
}

.gallery-headline {
    margin-bottom: 30px;
}

/* Gallery Items */
.gallery-item {
    transition: all 0.3s ease;
    overflow: hidden;
}

.gallery-media-wrapper {
    position: relative;
    overflow: hidden;
}

/* Images */
.gallery-image {
    width: 100%;
    height: auto;
    display: block;
    transition: transform 0.3s ease;
}

.gallery-item:hover .gallery-image {
    transform: scale(1.05);
}

/* Videos */
.gallery-video-container {
    position: relative;
    width: 100%;
    height: 100%;
}

.gallery-video {
    width: 100%;
    height: 100%;
    display: block;
    background: #000;
}

.gallery-video-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: rgba(255, 255, 255, 0.9);
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
    pointer-events: none;
    transition: all 0.3s ease;
    z-index: 10;
}

.gallery-item:hover .gallery-video-overlay {
    color: rgba(255, 255, 255, 1);
    transform: translate(-50%, -50%) scale(1.1);
}

.gallery-video-controls {
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 20;
}

.gallery-item:hover .gallery-video-controls {
    opacity: 1;
}

.gallery-video-controls .uk-button {
    backdrop-filter: blur(10px);
    background: rgba(0, 0, 0, 0.7);
    color: white;
    border: none;
}

.gallery-video-controls .uk-button:hover {
    background: rgba(0, 0, 0, 0.9);
}

/* Masonry Layout for UIkit */
.gallery-masonry .uk-grid {
    column-count: 3;
    column-gap: 20px;
}

.gallery-masonry .uk-grid > div {
    break-inside: avoid;
    margin-bottom: 20px;
    display: inline-block;
    width: 100%;
}

.gallery-masonry[data-columns="2"] .uk-grid {
    column-count: 2;
}

.gallery-masonry[data-columns="4"] .uk-grid {
    column-count: 4;
}

.gallery-masonry[data-columns="5"] .uk-grid {
    column-count: 5;
}

/* Responsive Masonry */
@media (max-width: 768px) {
    .gallery-masonry .uk-grid {
        column-count: 2;
    }
}

@media (max-width: 480px) {
    .gallery-masonry .uk-grid {
        column-count: 1;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gallery Video Controls
    document.querySelectorAll('.btn-gallery-video-play').forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var video = this.closest('.gallery-video-container').querySelector('video');
            var icon = this.querySelector('[uk-icon]');
            
            if (video.paused) {
                video.play();
                icon.setAttribute('uk-icon', 'pause');
                this.setAttribute('title', 'Pausieren');
            } else {
                video.pause();
                icon.setAttribute('uk-icon', 'play');
                this.setAttribute('title', 'Abspielen');
            }
        });
    });
    
    document.querySelectorAll('.btn-gallery-video-mute').forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var video = this.closest('.gallery-video-container').querySelector('video');
            var icon = this.querySelector('[uk-icon]');
            
            if (video.muted) {
                video.muted = false;
                icon.setAttribute('uk-icon', 'sound');
                this.setAttribute('title', 'Stumm schalten');
            } else {
                video.muted = true;
                icon.setAttribute('uk-icon', 'ban');
                this.setAttribute('title', 'Ton an');
            }
        });
    });
    
    // Video Click to Play
    document.querySelectorAll('.gallery-video, .gallery-video-overlay').forEach(function(element) {
        element.addEventListener('click', function(e) {
            e.preventDefault();
            var video = this.closest('.gallery-video-container').querySelector('video');
            var playButton = this.closest('.gallery-video-container').querySelector('.btn-gallery-video-play');
            var icon = playButton.querySelector('[uk-icon]');
            
            if (video.paused) {
                video.play();
                icon.setAttribute('uk-icon', 'pause');
                playButton.setAttribute('title', 'Pausieren');
            } else {
                video.pause();
                icon.setAttribute('uk-icon', 'play');
                playButton.setAttribute('title', 'Abspielen');
            }
        });
    });
});
</script>