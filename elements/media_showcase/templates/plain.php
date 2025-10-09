<?php
// Plain HTML Template für Media Showcase Element
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
$paddingBottom = '';

switch ($aspectRatio) {
    case '16:9':
        $paddingBottom = '56.25%';
        break;
    case '4:3':
        $paddingBottom = '75%';
        break;
    case '1:1':
        $paddingBottom = '100%';
        break;
    case '21:9':
        $paddingBottom = '42.86%';
        break;
    case '3:4':
        $paddingBottom = '133.33%';
        break;
    case '9:16':
        $paddingBottom = '177.78%';
        break;
    default:
        $paddingBottom = null;
}
?>

<div class="media-showcase">
    <?php if ($title): ?>
        <h3 class="media-showcase-title"><?= htmlspecialchars($title) ?></h3>
    <?php endif; ?>
    
    <?php if ($mediaFile): ?>
        <div class="media-showcase-container" 
             <?php if ($paddingBottom): ?>
             style="position: relative; padding-bottom: <?= $paddingBottom ?>; height: 0; overflow: hidden; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"
             <?php endif; ?>>
            
            <?php if (isImage($mediaFile)): ?>
                <img src="<?= rex_url::media($mediaFile) ?>" 
                     alt="<?= htmlspecialchars($title ?: $mediaFile) ?>"
                     <?php if ($paddingBottom): ?>
                     style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; border-radius: 4px;"
                     <?php else: ?>
                     style="width: 100%; height: auto; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"
                     <?php endif; ?> />
                     
            <?php elseif (isVideo($mediaFile)): ?>
                <video <?php if ($paddingBottom): ?>
                       style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; border-radius: 4px;"
                       <?php else: ?>
                       style="width: 100%; height: auto; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"
                       <?php endif; ?>
                       <?= $autoplay ? 'autoplay' : '' ?>
                       <?= $controls ? 'controls' : '' ?>
                       <?= $muted ? 'muted' : '' ?>
                       preload="metadata">
                    <source src="<?= rex_url::media($mediaFile) ?>" type="video/mp4">
                    <p>Ihr Browser unterstützt das Video-Element nicht.</p>
                </video>
                
            <?php else: ?>
                <div style="padding: 20px; background: #ffe6e6; border: 1px solid #ffcccc; border-radius: 4px; color: #d00;">
                    ⚠️ Nicht unterstützter Dateityp: <?= htmlspecialchars($mediaFile) ?>
                </div>
            <?php endif; ?>
        </div>
        
    <?php else: ?>
        <div class="media-showcase-placeholder" 
             <?php if ($paddingBottom): ?>
             style="position: relative; padding-bottom: <?= $paddingBottom ?>; height: 0; overflow: hidden; background: #f5f5f5; border: 2px dashed #ddd; border-radius: 4px;"
             <?php else: ?>
             style="background: #f5f5f5; border: 2px dashed #ddd; padding: 60px 20px; text-align: center; border-radius: 4px;"
             <?php endif; ?>>
            <?php if ($paddingBottom): ?>
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; color: #999;">
                    <div style="font-size: 48px;">📷</div>
                    <p style="margin: 10px 0 0 0; font-size: 14px;">Bild oder Video auswählen<br>
                    <small>Seitenverhältnis: <?= htmlspecialchars($aspectRatio) ?></small></p>
                </div>
            <?php else: ?>
                <div style="font-size: 48px; color: #999;">📷</div>
                <p style="color: #999; margin: 10px 0 0 0;">Bild oder Video auswählen</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <?php if ($description): ?>
        <div class="media-showcase-description" style="margin-top: 15px; color: #666; line-height: 1.6;">
            <?= nl2br(htmlspecialchars($description)) ?>
        </div>
    <?php endif; ?>
</div>