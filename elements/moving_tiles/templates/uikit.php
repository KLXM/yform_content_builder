<?php
/**
 * Showcase Features Template - Juno-Style
 * Alternierend: Text/Bild mit Parallax-Effekt auf den Grid-Spalten
 * Bild als Cover (füllt den ganzen Bereich)
 * Video autoplay/autopause beim Scrollen
 * Mobile: Feste Höhe für Bilder
 * @var array $elementData
 */

$sectionBg = $elementData['section_bg'] ?? '';
$sectionPadding = $elementData['section_padding'] ?? '';
$firstPosition = $elementData['first_position'] ?? 'left';
$parallaxEnabled = !empty($elementData['parallax_enabled']);
$parallaxOffset = (int)($elementData['parallax_offset'] ?? 30);
$tileStyle = $elementData['tile_style'] ?? 'uk-tile-default';
$fadeEnabled = !empty($elementData['fade_enabled']);

// uk-light für dunkle Hintergründe (primary, secondary)
$tileLight = '';
if (in_array($tileStyle, ['uk-tile-primary', 'uk-tile-secondary'])) {
    $tileLight = ' uk-light';
}

$items = $elementData['items'] ?? [];

if (empty($items)) {
    return;
}

// Section classes
$sectionClasses = ['uk-section', 'uk-padding-remove-vertical'];
if ($sectionBg) {
    $sectionClasses[] = $sectionBg;
}
if ($sectionPadding) {
    $sectionClasses[] = $sectionPadding;
}

// Hilfsfunktion: Prüfen ob Video
if (!function_exists('movingTilesIsVideo')) {
    function movingTilesIsVideo(string $filename): bool {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($ext, ['mp4', 'webm', 'ogg', 'mov']);
    }
}
?>

<style>
/* Showcase Features: Cover-Bild mit responsiver Höhe */
.tm-showcase-media {
    min-height: 300px; /* Mobile Fallback */
}
@media (min-width: 960px) {
    .tm-showcase-media {
        min-height: 0;
        height: 100%;
    }
}
</style>

<div class="<?= implode(' ', $sectionClasses) ?>">
    <?php 
    $itemIndex = 0;
    foreach ($items as $item): 
        $itemIndex++;
        
        $media = $item['image'] ?? '';
        $text = $item['text'] ?? '';
        $imageAlt = $item['image_alt'] ?? '';
        $imageLightbox = !empty($item['image_lightbox']);
        
        // Tile-Style: Item-spezifisch oder global
        $itemTileStyle = !empty($item['item_tile_style']) ? $item['item_tile_style'] : $tileStyle;
        
        // uk-light für dunkle Hintergründe (primary, secondary)
        $itemTileLight = '';
        if (in_array($itemTileStyle, ['uk-tile-primary', 'uk-tile-secondary'])) {
            $itemTileLight = ' uk-light';
        }
        
        $imageAlt = \KLXM\YFormContentBuilder\MediaAltResolver::resolve((string) $media, (string) $imageAlt, '');
        
        // Position berechnen: first_position bestimmt wo Element 1 startet
        $isFirstLeft = ($firstPosition === 'left');
        $mediaIsLeft = ($itemIndex % 2 === 1) ? $isFirstLeft : !$isFirstLeft;
        
        // Parallax Offsets - auf die Grid-Spalten
        // Fade wird direkt im Parallax mit opacity integriert
        $leftParallax = '';
        $rightParallax = '';
        if ($parallaxEnabled) {
            $fadeParam = $fadeEnabled ? ' opacity: 0,1;' : '';
            $leftParallax = ' uk-parallax="x: -' . $parallaxOffset . ',0;' . $fadeParam . ' easing: 1; media: @m; end: 50vh + 50%"';
            $rightParallax = ' uk-parallax="x: ' . $parallaxOffset . ',0;' . $fadeParam . ' easing: 1; media: @m; end: 50vh + 50%"';
        } elseif ($fadeEnabled) {
            // Nur Fade ohne Parallax-Bewegung
            $leftParallax = ' uk-parallax="opacity: 0,1; easing: 1; media: @m; end: 50vh + 50%"';
            $rightParallax = ' uk-parallax="opacity: 0,1; easing: 1; media: @m; end: 50vh + 50%"';
        }
        
        // Media HTML generieren (Bild oder Video)
        $mediaHtml = '';
        if (!empty($media)) {
            if (movingTilesIsVideo($media)) {
                // Video mit autoplay, muted, loop, playsinline
                // uk-video="autoplay: inview" pausiert automatisch wenn außerhalb
                $videoSrc = rex_url::media($media);
                $mediaHtml = '<video src="' . rex_escape($videoSrc) . '" autoplay muted loop playsinline uk-cover uk-video="autoplay: inview"></video>';
            } else {
                // Bild via Media Manager
                $imageSrc = rex_media_manager::getUrl('content_full', $media);
                $imgTag = '<img src="' . rex_escape($imageSrc) . '" alt="' . rex_escape($imageAlt) . '" loading="lazy" uk-cover>';
                if ($imageLightbox) {
                    $mediaHtml = '<a href="' . rex_escape($imageSrc) . '" uk-lightbox class="uk-display-block uk-height-1-1">' . $imgTag . '</a>';
                } else {
                    $mediaHtml = $imgTag;
                }
            }
        }
    ?>
    
    <div class="uk-grid tm-grid-expand uk-grid-collapse uk-margin-remove-vertical" uk-grid>
        
        <?php if ($mediaIsLeft): ?>
        <!-- MEDIA LINKS -->
        <div class="uk-grid-item-match uk-width-1-2@m uk-first-column"<?= $leftParallax ?>>
            <div class="uk-cover-container tm-showcase-media">
                <?= $mediaHtml ?>
            </div>
        </div>
        
        <!-- TEXT RECHTS -->
        <div class="uk-grid-item-match uk-width-1-2@m"<?= $rightParallax ?>>
            <div class="<?= $itemTileStyle ?> uk-tile uk-tile-xlarge uk-flex uk-flex-middle<?= $itemTileLight ?>">
                <div class="uk-panel uk-width-1-1">
                    <div class="uk-panel uk-margin-remove-first-child uk-margin-large uk-width-large uk-margin-auto uk-text-left">
                        <?= $text ?>
                    </div>
                </div>
            </div>
        </div>
        
        <?php else: ?>
        <!-- TEXT LINKS (auf Desktop), auf Mobile nach unten -->
        <div class="uk-grid-item-match uk-width-1-2@m uk-first-column"<?= $leftParallax ?>>
            <div class="<?= $itemTileStyle ?> uk-tile uk-tile-xlarge uk-flex uk-flex-middle<?= $itemTileLight ?>">
                <div class="uk-panel uk-width-1-1">
                    <div class="uk-panel uk-margin-remove-first-child uk-margin-large uk-width-large uk-margin-auto uk-text-left">
                        <?= $text ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- MEDIA RECHTS (auf Desktop), auf Mobile zuerst -->
        <div class="uk-grid-item-match uk-width-1-2@m uk-flex-first uk-flex-last@m"<?= $rightParallax ?>>
            <div class="uk-cover-container tm-showcase-media">
                <?= $mediaHtml ?>
            </div>
        </div>
        <?php endif; ?>
        
    </div>
    
    <?php endforeach; ?>
</div>
