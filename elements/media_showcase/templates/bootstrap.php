<?php
// Bootstrap Template für Media Showcase Element
if (!isset($elementData) || !is_array($elementData)) {
    return;
}

$mediaFile = $elementData['media_file'] ?? '';
$aspectRatio = $elementData['aspect_ratio'] ?? '16:9';
$title = $elementData['title'] ?? '';
$description = $elementData['description'] ?? '';
$autoplay = !empty($elementData['autoplay']);
$controls = !empty($elementData['controls']);
$muted = !empty($elementData['muted']);

// Helper function für Video-Erkennung
function isVideo($filename) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($ext, ['mp4', 'webm', 'mov', 'avi', 'mkv', 'ogg']);
}

// Helper function für Bild-Erkennung  
function isImage($filename) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']);
}

// Aspect Ratio CSS-Klassen
$aspectRatioClass = '';
$paddingBottom = '';

switch ($aspectRatio) {
    case '16:9':
        $paddingBottom = '56.25%'; // (9/16) * 100
        break;
    case '4:3':
        $paddingBottom = '75%'; // (3/4) * 100
        break;
    case '1:1':
        $paddingBottom = '100%';
        break;
    case '21:9':
        $paddingBottom = '42.86%'; // (9/21) * 100
        break;
    case '3:4':
        $paddingBottom = '133.33%'; // (4/3) * 100
        break;
    case '9:16':
        $paddingBottom = '177.78%'; // (16/9) * 100
        break;
    default:
        $paddingBottom = null; // Auto = kein festes Verhältnis
}
?>

<div class="media-showcase">
    <?php if ($title): ?>
        <h3 class="media-showcase-title"><?= htmlspecialchars($title) ?></h3>
    <?php endif; ?>
    
    <?php if ($mediaFile): ?>
        <div class="media-showcase-container" 
             <?php if ($paddingBottom): ?>
             style="position: relative; padding-bottom: <?= $paddingBottom ?>; height: 0; overflow: hidden;"
             <?php endif; ?>>
            
            <?php if (isImage($mediaFile)): ?>
                <!-- Bild anzeigen -->
                <img src="<?= rex_url::media($mediaFile) ?>" 
                     alt="<?= htmlspecialchars($title ?: $mediaFile) ?>"
                     <?php if ($paddingBottom): ?>
                     style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;"
                     <?php else: ?>
                     style="width: 100%; height: auto;"
                     <?php endif; ?>
                     class="img-responsive" />
                     
            <?php elseif (isVideo($mediaFile)): ?>
                <!-- Video anzeigen -->
                <video <?php if ($paddingBottom): ?>
                       style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;"
                       <?php else: ?>
                       style="width: 100%; height: auto;"
                       <?php endif; ?>
                       class="media-showcase-video"
                       <?= $autoplay ? 'autoplay' : '' ?>
                       <?= $controls ? 'controls' : '' ?>
                       <?= $muted ? 'muted' : '' ?>
                       preload="metadata">
                    <source src="<?= rex_url::media($mediaFile) ?>" type="video/mp4">
                    <p>Ihr Browser unterstützt das Video-Element nicht.</p>
                </video>
                
            <?php else: ?>
                <!-- Unbekannter Dateityp -->
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle"></i>
                    Nicht unterstützter Dateityp: <?= htmlspecialchars($mediaFile) ?>
                </div>
            <?php endif; ?>
        </div>
        
    <?php else: ?>
        <!-- Platzhalter wenn kein Medium ausgewählt -->
        <div class="media-showcase-placeholder" 
             <?php if ($paddingBottom): ?>
             style="position: relative; padding-bottom: <?= $paddingBottom ?>; height: 0; overflow: hidden; background: #f5f5f5; border: 2px dashed #ddd;"
             <?php else: ?>
             style="background: #f5f5f5; border: 2px dashed #ddd; padding: 60px 20px; text-align: center;"
             <?php endif; ?>>
            <?php if ($paddingBottom): ?>
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; color: #999;">
                    <i class="fa fa-file-image-o fa-3x"></i>
                    <p style="margin-top: 10px;">Bild oder Video auswählen<br>
                    <small>Seitenverhältnis: <?= htmlspecialchars($aspectRatio) ?></small></p>
                </div>
            <?php else: ?>
                <i class="fa fa-file-image-o fa-3x" style="color: #999;"></i>
                <p style="color: #999; margin-top: 10px;">Bild oder Video auswählen</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <?php if ($description): ?>
        <div class="media-showcase-description" style="margin-top: 15px;">
            <?= nl2br(htmlspecialchars($description)) ?>
        </div>
    <?php endif; ?>
</div>

<style>
.media-showcase {
    margin-bottom: 30px;
}

.media-showcase-title {
    margin-bottom: 15px;
    font-weight: 600;
}

.media-showcase-container {
    border-radius: 4px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.media-showcase-video {
    border-radius: 4px;
}

.media-showcase-placeholder {
    border-radius: 4px;
    transition: all 0.3s ease;
}

.media-showcase-placeholder:hover {
    border-color: #007bff;
    background-color: #f8f9fa;
}

.media-showcase-description {
    color: #666;
    line-height: 1.6;
}

@media (max-width: 768px) {
    .media-showcase-container[style*="177.78%"] {
        /* 9:16 Portrait auf Mobile anpassen */
        padding-bottom: 100% !important;
    }
}
</style>