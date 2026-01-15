<?php
/**
 * Gallery Element - Plain Template
 * Grid/Masonry Layout für Medien-Galerie
 */

$headline = $elementData['headline'] ?? '';
$layout = $elementData['layout'] ?? 'grid';
$columns = intval($elementData['columns'] ?? 3);
$aspectRatio = $elementData['aspect_ratio'] ?? 'auto';
$gap = $elementData['gap'] ?? 'medium';
$items = $elementData['items'] ?? [];

// CSS-Klassen basierend auf Einstellungen
$containerClass = 'gallery-container gallery-' . $layout . ' gallery-gap-' . $gap;
$itemClass = 'gallery-item';

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

// Spalten-Style für CSS Grid
$gridColumns = 'repeat(' . $columns . ', 1fr)';
?>

<div class="gallery-element">
    <?php if ($headline): ?>
        <h3 class="gallery-headline"><?= htmlspecialchars($headline) ?></h3>
    <?php endif; ?>
    
    <div class="<?= $containerClass ?>" data-columns="<?= $columns ?>" data-layout="<?= $layout ?>"
         <?php if ($layout === 'grid'): ?>style="display: grid; grid-template-columns: <?= $gridColumns ?>;"<?php endif; ?>>
        
        <?php foreach ($items as $index => $item): ?>
            <?php 
            $media = $item['media'] ?? '';
            $caption = $item['caption'] ?? '';
            $altText = $item['alt_text'] ?? '';
            
            if (!$media) continue;
            
            // Media-Typ bestimmen
            $isImage = yform_content_builder_helper::isImage($media);
            $isVideo = yform_content_builder_helper::isVideo($media);
            ?>
            
            <div class="<?= $itemClass . $aspectRatioClass ?>" data-index="<?= $index ?>">
                <div class="gallery-media-wrapper" <?php if ($aspectRatioStyle): ?>style="position: relative; <?= $aspectRatioStyle ?>"<?php endif; ?>>
                    <?php if ($isImage): ?>
                        <?php 
                        // MediaManager Typ auswählen:
                        // - gallery_resize: Nur Resize ohne Cropping (für aspect_ratio='auto' + masonry)
                        // - gallery_thumb: Mit Cropping zu 1:1 (für andere aspect ratios)
                        $mmType = ($aspectRatio === 'auto') ? 'gallery_resize' : 'gallery_thumb';
                        ?>
                        <img src="<?= rex_media_manager::getUrl($mmType, $media) ?>" 
                             alt="<?= htmlspecialchars($altText ?: $caption) ?>"
                             class="gallery-image"
                             data-full="<?= rex_media_manager::getUrl('gallery_full', $media) ?>"
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
                                <i class="fa fa-play-circle"></i>
                            </div>
                            <div class="gallery-video-controls">
                                <button class="btn-gallery-video-play" title="Abspielen">
                                    <i class="fa fa-play"></i>
                                </button>
                                <button class="btn-gallery-video-mute" title="Stumm schalten">
                                    <i class="fa fa-volume-up"></i>
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($caption): ?>
                        <div class="gallery-caption">
                            <p><?= htmlspecialchars($caption) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
/* Gallery Base Styles */
.gallery-element {
    margin: 20px 0;
}

.gallery-headline {
    margin-bottom: 20px;
    color: #333;
    font-size: 24px;
    font-weight: bold;
}

.gallery-container {
    width: 100%;
}

/* Grid Layout */
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.gallery-gap-small.gallery-grid {
    gap: 10px;
}

.gallery-gap-medium.gallery-grid {
    gap: 20px;
}

.gallery-gap-large.gallery-grid {
    gap: 30px;
}

/* Masonry Layout */
.gallery-masonry {
    column-count: 3;
    column-gap: 20px;
}

.gallery-masonry .gallery-item {
    break-inside: avoid;
    margin-bottom: 20px;
    display: inline-block;
    width: 100%;
}

.gallery-gap-small.gallery-masonry {
    column-gap: 10px;
}

.gallery-gap-medium.gallery-masonry {
    column-gap: 20px;
}

.gallery-gap-large.gallery-masonry {
    column-gap: 30px;
}

/* Gallery Items */
.gallery-item {
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.gallery-item:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    transform: translateY(-2px);
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
    font-size: 48px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
    pointer-events: none;
    transition: all 0.3s ease;
    z-index: 10;
}

.gallery-item:hover .gallery-video-overlay {
    color: rgba(255, 255, 255, 1);
    font-size: 54px;
}

.gallery-video-controls {
    position: absolute;
    bottom: 10px;
    left: 10px;
    right: 10px;
    background: rgba(0, 0, 0, 0.7);
    border-radius: 4px;
    padding: 5px 10px;
    display: none;
    z-index: 20;
}

.gallery-item:hover .gallery-video-controls {
    display: flex;
    gap: 10px;
}

.gallery-video-controls button {
    background: none;
    border: none;
    color: white;
    padding: 5px 8px;
    border-radius: 3px;
    cursor: pointer;
    transition: background 0.2s;
}

.gallery-video-controls button:hover {
    background: rgba(255, 255, 255, 0.3);
}

/* Captions */
.gallery-caption {
    padding: 15px;
    background: #fff;
}

.gallery-caption p {
    margin: 0;
    color: #666;
    font-size: 14px;
    line-height: 1.4;
}

/* Responsive */
@media (max-width: 768px) {
    .gallery-grid {
        grid-template-columns: repeat(2, 1fr) !important;
    }
    
    .gallery-masonry {
        column-count: 2;
    }
}

@media (max-width: 480px) {
    .gallery-grid {
        grid-template-columns: 1fr !important;
    }
    
    .gallery-masonry {
        column-count: 1;
    }
}

/* Column Count Variations */
.gallery-container[data-columns="2"] {
    grid-template-columns: repeat(2, 1fr);
}

.gallery-container[data-columns="3"] {
    grid-template-columns: repeat(3, 1fr);
}

.gallery-container[data-columns="4"] {
    grid-template-columns: repeat(4, 1fr);
}

.gallery-container[data-columns="5"] {
    grid-template-columns: repeat(5, 1fr);
}

.gallery-masonry[data-columns="2"] {
    column-count: 2;
}

.gallery-masonry[data-columns="3"] {
    column-count: 3;
}

.gallery-masonry[data-columns="4"] {
    column-count: 4;
}

.gallery-masonry[data-columns="5"] {
    column-count: 5;
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
            
            if (video.paused) {
                video.play();
                this.innerHTML = '<i class="fa fa-pause"></i>';
                this.setAttribute('title', 'Pausieren');
            } else {
                video.pause();
                this.innerHTML = '<i class="fa fa-play"></i>';
                this.setAttribute('title', 'Abspielen');
            }
        });
    });
    
    document.querySelectorAll('.btn-gallery-video-mute').forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var video = this.closest('.gallery-video-container').querySelector('video');
            
            if (video.muted) {
                video.muted = false;
                this.innerHTML = '<i class="fa fa-volume-up"></i>';
                this.setAttribute('title', 'Stumm schalten');
            } else {
                video.muted = true;
                this.innerHTML = '<i class="fa fa-volume-off"></i>';
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
            
            if (video.paused) {
                video.play();
                playButton.innerHTML = '<i class="fa fa-pause"></i>';
                playButton.setAttribute('title', 'Pausieren');
            } else {
                video.pause();
                playButton.innerHTML = '<i class="fa fa-play"></i>';
                playButton.setAttribute('title', 'Abspielen');
            }
        });
    });
});
</script>