<?php
/**
 * Gallery Element - Bootstrap Template
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

// Bootstrap Grid Klassen
$colClass = 'col-md-' . (12 / $columns);
if ($columns == 5) {
    $colClass = 'col-md-2'; // 5 Spalten = 12/5 ≈ 2.4, nehmen wir col-2
}
?>

<div class="gallery-element">
    <?php if ($headline): ?>
        <h3 class="gallery-headline"><?= htmlspecialchars($headline) ?></h3>
    <?php endif; ?>
    
    <div class="<?= $containerClass ?>" data-columns="<?= $columns ?>" data-layout="<?= $layout ?>">
        <div class="row">
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
                
                <div class="<?= $colClass ?> gallery-column">
                    <div class="<?= $itemClass . $aspectRatioClass ?>" data-index="<?= $index ?>">
                        <div class="gallery-media-wrapper" <?php if ($aspectRatioStyle): ?>style="position: relative; <?= $aspectRatioStyle ?>"<?php endif; ?>>
                            <?php if ($isImage): ?>
                                <img src="<?= rex_url::media($media) ?>" 
                                     alt="<?= htmlspecialchars($altText ?: $caption) ?>"
                                     class="gallery-image img-responsive"
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
                </div>
            <?php endforeach; ?>
        </div>
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
}

.gallery-container {
    width: 100%;
}

/* Gap Sizes */
.gallery-gap-small .gallery-column {
    padding: 5px;
}

.gallery-gap-medium .gallery-column {
    padding: 10px;
}

.gallery-gap-large .gallery-column {
    padding: 15px;
}

/* Gallery Items */
.gallery-item {
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    margin-bottom: 20px;
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

/* Masonry Layout */
.gallery-masonry {
    column-count: 3;
    column-gap: 20px;
}

.gallery-masonry .gallery-column {
    break-inside: avoid;
    margin-bottom: 20px;
    display: inline-block;
    width: 100%;
}

/* Masonry Responsive */
@media (max-width: 768px) {
    .gallery-masonry {
        column-count: 2;
    }
}

@media (max-width: 480px) {
    .gallery-masonry {
        column-count: 1;
    }
}

/* Grid Responsive */
@media (max-width: 768px) {
    .gallery-container[data-columns="4"] .col-md-3,
    .gallery-container[data-columns="5"] .col-md-2 {
        width: 50%;
    }
}

@media (max-width: 480px) {
    .gallery-container .gallery-column {
        width: 100% !important;
    }
}
</style>

<script>
$(document).ready(function() {
    // Gallery Video Controls
    $('.btn-gallery-video-play').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var $video = $(this).closest('.gallery-video-container').find('video')[0];
        var $button = $(this);
        
        if ($video.paused) {
            $video.play();
            $button.html('<i class="fa fa-pause"></i>');
            $button.attr('title', 'Pausieren');
        } else {
            $video.pause();
            $button.html('<i class="fa fa-play"></i>');
            $button.attr('title', 'Abspielen');
        }
    });
    
    $('.btn-gallery-video-mute').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var $video = $(this).closest('.gallery-video-container').find('video')[0];
        var $button = $(this);
        
        if ($video.muted) {
            $video.muted = false;
            $button.html('<i class="fa fa-volume-up"></i>');
            $button.attr('title', 'Stumm schalten');
        } else {
            $video.muted = true;
            $button.html('<i class="fa fa-volume-off"></i>');
            $button.attr('title', 'Ton an');
        }
    });
    
    // Video Click to Play
    $('.gallery-video, .gallery-video-overlay').on('click', function(e) {
        e.preventDefault();
        var $video = $(this).closest('.gallery-video-container').find('video')[0];
        var $playButton = $(this).closest('.gallery-video-container').find('.btn-gallery-video-play');
        
        if ($video.paused) {
            $video.play();
            $playButton.html('<i class="fa fa-pause"></i>');
            $playButton.attr('title', 'Pausieren');
        } else {
            $video.pause();
            $playButton.html('<i class="fa fa-play"></i>');
            $playButton.attr('title', 'Abspielen');
        }
    });
    
    // Masonry Layout Initialization (falls CSS-Grid nicht ausreicht)
    if (typeof $.fn.masonry !== 'undefined') {
        $('.gallery-masonry .row').masonry({
            itemSelector: '.gallery-column',
            columnWidth: '.gallery-column',
            percentPosition: true
        });
    }
});
</script>