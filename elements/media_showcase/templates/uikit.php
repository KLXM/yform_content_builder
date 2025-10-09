<?php
// UIKit// Aspect Ratio CSS-Klassen Media Showcase Element
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

// UIKit Aspect Ratio Klassen
$ukClass = '';
switch ($aspectRatio) {
    case '16:9':
        $ukClass = 'uk-height-viewport uk-flex uk-flex-center uk-flex-middle';
        break;
    case '4:3':
        $ukClass = 'uk-height-medium uk-flex uk-flex-center uk-flex-middle';
        break;
    case '1:1':
        $ukClass = 'uk-height-small uk-flex uk-flex-center uk-flex-middle';
        break;
    default:
        $ukClass = 'uk-flex uk-flex-center uk-flex-middle';
}
?>

<div class="uk-card uk-card-default uk-margin-medium-bottom">
    <?php if ($title): ?>
        <div class="uk-card-header">
            <h3 class="uk-card-title uk-margin-remove-bottom"><?= htmlspecialchars($title) ?></h3>
        </div>
    <?php endif; ?>
    
    <div class="uk-card-body uk-padding-remove">
        <?php if ($mediaFile): ?>
            
            <?php if (yform_content_builder_helper::isImage($mediaFile)): ?>
                <!-- Bild mit UIKit -->
                <div class="uk-inline uk-width-1-1">
                    <img src="<?= rex_url::media($mediaFile) ?>" 
                         alt="<?= htmlspecialchars($title ?: $mediaFile) ?>"
                         class="uk-width-1-1"
                         uk-img />
                </div>
                     
            <?php elseif (yform_content_builder_helper::isVideo($mediaFile)): ?>
                <!-- Video mit UIKit -->
                <div class="uk-inline uk-width-1-1">
                    <video class="uk-width-1-1"
                           <?= $autoplay ? 'autoplay' : '' ?>
                           <?= $controls ? 'controls' : '' ?>
                           <?= $muted ? 'muted' : '' ?>
                           preload="metadata"
                           uk-video>
                        <source src="<?= rex_url::media($mediaFile) ?>" type="video/mp4">
                        <p>Ihr Browser unterstützt das Video-Element nicht.</p>
                    </video>
                </div>
                
            <?php else: ?>
                <!-- Unbekannter Dateityp -->
                <div class="uk-alert-warning" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p><span uk-icon="warning"></span> Nicht unterstützter Dateityp: <?= htmlspecialchars($mediaFile) ?></p>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <!-- Platzhalter mit UIKit -->
            <div class="uk-placeholder uk-text-center uk-padding-large">
                <span uk-icon="icon: image; ratio: 3" class="uk-text-muted"></span>
                <p class="uk-text-muted uk-margin-small-top">
                    Bild oder Video auswählen<br>
                    <small>Seitenverhältnis: <?= htmlspecialchars($aspectRatio) ?></small>
                </p>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if ($description): ?>
        <div class="uk-card-footer">
            <div class="uk-text-muted">
                <?= nl2br(htmlspecialchars($description)) ?>
            </div>
        </div>
    <?php endif; ?>
</div>