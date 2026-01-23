<?php
/**
 * Cards Grid Element - UIkit Template (Pro Version)
 * Unterstützt alle Layouts: media-top, media-bottom, media-left, media-right, media-overlay
 * @var array $elementData
 */

// Grid-Einstellungen
$columns = $elementData['columns'] ?? '3';
$columnsTablet = $elementData['columns_tablet'] ?? '2';
$columnsMobile = $elementData['columns_mobile'] ?? '1';
$gap = $elementData['gap'] ?? 'medium';
$matchHeight = !empty($elementData['match_height']);
$cardStyle = $elementData['card_style'] ?? 'default';
$cardSize = $elementData['card_size'] ?? 'default';
$cardShadow = $elementData['card_shadow'] ?? '';

// Animation-Einstellungen (Global)
$animationsEnabled = !empty($elementData['animations_enabled']);
$animationsScrollspy = !empty($elementData['animations_scrollspy']);
$animationsDelay = $elementData['animations_delay'] ?? '100';
$animationsRepeat = !empty($elementData['animations_repeat']);
$animationsCascading = !empty($elementData['animations_cascading']);

// Sektion-Einstellungen
$sectionBg = $elementData['section_bg'] ?? '';
$sectionBgImage = $elementData['section_bg_image'] ?? '';
$sectionPadding = $elementData['section_padding'] ?? '';
$containerWidth = $elementData['container_width'] ?? 'uk-container';

$items = $elementData['items'] ?? [];

if (empty($items)) {
    return;
}

// Hilfsfunktionen für Media
$isVideo = function($filename) {
    if (empty($filename)) return false;
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($ext, ['mp4', 'webm', 'ogg', 'avi', 'mov']);
};

$isImage = function($filename) {
    if (empty($filename)) return false;
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
};

// Card-Style zu UIkit-Klasse konvertieren
$getCardStyleClass = function($style) {
    if (empty($style)) return 'uk-card-default';
    if (strpos($style, 'uk-card-') === 0) return $style;
    return 'uk-card-' . $style;
};

// Schatten-Klasse ermitteln
$getShadowClass = function($shadow) {
    if (empty($shadow)) return 'uk-box-shadow-medium';
    if ($shadow === 'uk-shadow-remove') return '';
    return $shadow;
};

// UIkit Grid-Attribute
$gridClasses = ['uk-grid'];

// Gap Mapping
$gapMap = [
    'collapse' => 'uk-grid-collapse',
    'column-collapse' => 'uk-grid-column-collapse',
    'small' => 'uk-grid-small',
    'medium' => '',
    'large' => 'uk-grid-large'
];
if (isset($gapMap[$gap]) && $gapMap[$gap]) {
    $gridClasses[] = $gapMap[$gap];
}

// Match Height
if ($matchHeight) {
    $gridClasses[] = 'uk-grid-match';
}

// Width Classes für Spalten
$gridClasses[] = 'uk-child-width-1-' . $columnsMobile;
$gridClasses[] = 'uk-child-width-1-' . $columnsTablet . '@s';
$gridClasses[] = 'uk-child-width-1-' . $columns . '@m';

$gridClassStr = implode(' ', $gridClasses);

// Card Size Mapping
$cardSizeMap = [
    'small' => 'uk-card-small',
    'default' => '',
    'large' => 'uk-card-large'
];
$cardSizeClass = $cardSizeMap[$cardSize] ?? '';

// Sektion-Klassen
$sectionClasses = ['uk-section'];
if ($sectionBg) {
    $sectionClasses[] = $sectionBg;
}
if ($sectionPadding) {
    $sectionClasses[] = $sectionPadding;
}

// Hintergrundbild oder -video
$sectionStyle = '';
$sectionBgVideoHtml = '';
$isSectionBgVideo = false;

if (!empty($sectionBgImage)) {
    // Prüfen ob Video oder Bild
    $bgMediaExt = strtolower(pathinfo($sectionBgImage, PATHINFO_EXTENSION));
    $videoExtensions = ['mp4', 'webm', 'ogg'];
    
    if (in_array($bgMediaExt, $videoExtensions)) {
        // Video-Hintergrund
        $isSectionBgVideo = true;
        $videoSrc = rex_url::media($sectionBgImage);
        $sectionBgVideoHtml = '<video class="uk-cover" autoplay loop muted playsinline uk-cover><source src="' . $videoSrc . '" type="video/' . $bgMediaExt . '"></video>';
        $sectionClasses[] = 'uk-cover-container';
        $sectionClasses[] = 'uk-position-relative';
    } else {
        // Bild-Hintergrund
        $bgImageUrl = rex_media_manager::getUrl('content_slideshow', $sectionBgImage);
        $sectionStyle = ' style="background-image: url(\'' . $bgImageUrl . '\'); background-size: cover; background-position: center;"';
        $sectionClasses[] = 'uk-background-cover';
    }
}

// Prüfen ob Section nötig
$hasSection = $sectionBg || $sectionPadding || !empty($sectionBgImage);
?>

<?php if ($hasSection): ?>
<section class="<?= implode(' ', $sectionClasses) ?>"<?= $sectionStyle ?>>
<?php if ($isSectionBgVideo): ?>
<?= $sectionBgVideoHtml ?>
<div class="uk-position-relative">
<?php endif; ?>
<?php endif; ?>

<?php if ($containerWidth): ?>
<div class="<?= $containerWidth ?>">
<?php endif; ?>

<div class="<?= $gridClassStr ?>" uk-grid>
    <?php 
    $cardIndex = 0;
    foreach ($items as $item): 
    // Index nur für animierte Cards erhöhen
    $itemAnimation = $item['animation'] ?? '';
    if (!empty($itemAnimation)) {
        $cardIndex++;
    }
    ?>
        <?php
        // Item-Daten
        $layout = $item['layout'] ?? 'media-top';
        $title = $item['title'] ?? '';
        $subtitle = $item['subtitle'] ?? '';
        $text = $item['text'] ?? '';
        $image = $item['image'] ?? '';
        $imageTitle = $item['image_title'] ?? '';
        $mediaWidth = $item['media_width'] ?? '1-3@m';
        $mediaVerticalAlign = $item['media_vertical_align'] ?? '';
        $badge = $item['badge'] ?? '';
        $badgeColor = $item['badge_color'] ?? 'primary';
        // $itemAnimation bereits oben im Loop definiert
        
        // Card-spezifische Überschreibungen
        $itemCardWidth = $item['card_width'] ?? '';
        $itemCardStyle = $item['card_style_override'] ?? '';
        $itemCardShadow = $item['card_shadow_override'] ?? '';
        
        // Link
        $linkType = $item['link_type'] ?? '';
        $linkUrl = $item['link_url'] ?? '';
        $linkInternal = $item['link_internal'] ?? '';
        $linkText = $item['link_text'] ?? 'Mehr erfahren';
        $linkCard = !empty($item['link_card']);
        
        // Media-Optionen
        $mediaLightbox = !empty($item['media_lightbox']);
        $mediaCoverCheckbox = !empty($item['media_cover']); // Original Checkbox-Wert
        $imageDecorative = !empty($item['image_decorative']);
        
        // Video-Optionen
        $videoDisplay = $item['video_display'] ?? 'inline';
        $videoControls = $item['video_controls'] ?? 'autoplay';
        
        // Alt-Text Logik:
        // 1. Wenn Feld ausgefüllt -> dieses verwenden
        // 2. Sonst: med_alt aus Medienpool holen
        // 3. Dekorativ wenn: Checkbox gesetzt ODER gesamte Card verlinkt
        $imageAlt = '';
        $mediaPoolTitle = '';
        $altWarning = false;
        
        if (!empty($image) && $isImage($image)) {
            $media = rex_media::get($image);
            
            // Manuell eingegebener Alt-Text hat Vorrang
            if (!empty($item['image_alt'])) {
                $imageAlt = $item['image_alt'];
            } elseif ($media) {
                $imageAlt = $media->getValue('med_alt') ?: '';
            }
            
            // Titel aus Medienpool für Fallbacks holen
            if ($media) {
                $mediaPoolTitle = $media->getValue('title') ?: '';
            }
            
            // Dekorativ wenn Checkbox oder wenn gesamte Card verlinkt
            $isDecorative = $imageDecorative || $linkCard;
            
            // Warnung wenn weder dekorativ noch Alt-Text vorhanden
            if (!$isDecorative && empty($imageAlt)) {
                $altWarning = true;
            }
            
            // Bei dekorativen Bildern leerer Alt-Text
            if ($isDecorative) {
                $imageAlt = '';
            }
        }
        
        // Layout-spezifische Klassen
        $isHorizontal = in_array($layout, ['media-left', 'media-right']);
        $isOverlay = $layout === 'media-overlay';
        $mediaRatio = $item['media_ratio'] ?? '16-9';

        // Ratio Spacer Logic
        $canvasW = 16; $canvasH = 9;
        if ($mediaRatio === '21-9') { $canvasW = 21; $canvasH = 9; }
        elseif ($mediaRatio === '4-3') { $canvasW = 4; $canvasH = 3; }
        elseif ($mediaRatio === '1-1') { $canvasW = 1; $canvasH = 1; }
        
        // Cover-Logik: 
        // - Cover aktiv (Checkbox): Bild füllt Container-Höhe aus, kein festes Ratio (kein Canvas)
        // - Cover inaktiv: Festes Ratio via Canvas (außer bei 'original')
        // - Overlay: Immer Canvas für Ratio
        // - Top/Bottom: Immer Canvas für Ratio (außer bei 'original')
        
        $useCanvas = false;
        $applyCover = false; // Diese Variable wird an _media_output.php übergeben
        
        if ($isOverlay) {
            // Overlay: Immer Canvas + Cover
            $useCanvas = true;
            $applyCover = true;
        } elseif ($isHorizontal) {
            // Horizontal: Abhängig von Checkbox
            if ($mediaCoverCheckbox) {
                // Cover aktiv: Kein Canvas, Bild füllt Container-Höhe
                $useCanvas = false;
                $applyCover = true;
            } else {
                // Cover inaktiv: Canvas für Ratio (außer original)
                $useCanvas = ($mediaRatio !== 'original');
                $applyCover = ($mediaRatio !== 'original');
            }
        } else {
            // Top/Bottom: Kein Canvas/Cover - Ratio kommt vom Media Manager
            $useCanvas = false;
            $applyCover = false;
        }

        // Link generieren
        $href = '';
        if ($linkType === 'external' && !empty($linkUrl)) {
            $href = $linkUrl;
        } elseif ($linkType === 'internal' && !empty($linkInternal)) {
            $href = rex_getUrl($linkInternal);
        }
        
        // Card-Klassen berechnen
        $itemCardStyleClass = $itemCardStyle ? $getCardStyleClass($itemCardStyle) : $getCardStyleClass($cardStyle);
        $itemShadowClass = $itemCardShadow ? $getShadowClass($itemCardShadow) : $getShadowClass($cardShadow);
        
        // Bei transparent: Schatten und Padding entfernen
        $isTransparent = $itemCardStyleClass === 'uk-card-transparent';
        if ($isTransparent) {
            $itemShadowClass = ''; // Kein Schatten bei transparent
        }
        
        $cardClasses = ['uk-card', $itemCardStyleClass, 'uk-overflow-hidden'];
        if ($cardSizeClass && !$isTransparent) $cardClasses[] = $cardSizeClass; // Kein Size-Padding bei transparent
        if ($itemShadowClass) $cardClasses[] = $itemShadowClass;
        if ($linkCard) {
            $cardClasses[] = 'uk-link-reset';
            if ($href) {
                $cardClasses[] = 'uk-link-toggle';
            }
        }
        
        // Width-Klassen für Item
        $itemWidthClass = $itemCardWidth ? 'uk-width-' . $itemCardWidth : '';
        
        // Bild-URL via Media Manager
        $imageSrc = '';
        if (!empty($image) && $isImage($image)) {
            $imageSrc = rex_media_manager::getUrl('content_card', $image);
        }
        
        // Backend-Warnung bei fehlendem Alt-Text (nur im Backend sichtbar)
        $altWarningHtml = '';
        // Backend-Prüfung: rex::isBackend() oder wenn wir in der Modulvorschau sind
        $isInBackend = rex::isBackend() || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && rex::getUser());
        if ($altWarning && $isInBackend) {
            // Badge wird direkt auf dem Bild angezeigt
            $altWarningHtml = '<span class="uk-badge" style="position:absolute;top:5px;right:5px;z-index:10;background:#f0506e;font-size:10px;padding:3px 8px;" title="Alt-Text fehlt! Bitte Alt-Text eingeben oder als dekorativ markieren.">⚠ Alt fehlt</span>';
        }
        ?>
        
        <?php 
        // Match-Height Klassen nur wenn aktiviert
        $matchHeightClasses = $matchHeight ? ' uk-height-1-1 uk-flex uk-flex-column' : '';
        
        // ScrollSpy nur wenn:
        // 1. Animationen aktiviert UND
        // 2. ScrollSpy aktiviert UND  
        // 3. Animation auf dieser Card ausgewählt
        // Delay wird optional kaskadierend addiert pro Card-Position
        $scrollspyAttr = '';
        if ($animationsEnabled && $animationsScrollspy && !empty($itemAnimation)) {
            $cardDelay = $animationsCascading ? intval($animationsDelay) * $cardIndex : intval($animationsDelay);
            $repeatAttr = $animationsRepeat ? ' repeat: true;' : '';
            $scrollspyAttr = ' uk-scrollspy="cls: ' . $itemAnimation . ';' . $repeatAttr . ' delay: ' . $cardDelay . '"';
        }
        ?>
        <div<?= $itemWidthClass ? ' class="' . $itemWidthClass . '"' : '' ?><?= $scrollspyAttr ?> data-animation="<?= $itemAnimation ?>" data-card-index="<?= $cardIndex ?>">
            <div class="<?= implode(' ', $cardClasses) ?><?= $matchHeightClasses ?> uk-position-relative">
                <?php if ($linkCard && $href): ?>
                    <a href="<?= $href ?>" class="uk-position-cover" style="z-index: 2;"></a>
                <?php endif; ?>
                
                <?php if ($isHorizontal): ?>
                    <!-- Horizontales Layout (links/rechts) -->
                    <div class="uk-grid-small uk-child-width-expand uk-grid-match" uk-grid>
                        <?php if ($layout === 'media-left' && $image): ?>
                            <div class="uk-card-media-left uk-width-<?= $mediaWidth ?>">
                                <?php if ($useCanvas): ?>
                                    <!-- Festes Ratio via Canvas -->
                                    <div class="uk-cover-container uk-position-relative">
                                        <canvas width="<?= $canvasW ?>" height="<?= $canvasH ?>"></canvas>
                                        <?= $altWarningHtml ?>
                                        <?php $mediaCover = $applyCover; include __DIR__ . '/_media_output.php'; ?>
                                    </div>
                                <?php elseif ($applyCover): ?>
                                    <!-- Cover-Modus: Mobile ausgewähltes Ratio, ab @m Container-Höhe -->
                                    <div class="uk-cover-container uk-height-1-1@m uk-position-relative" style="overflow: hidden;">
                                        <canvas width="<?= $canvasW ?>" height="<?= $canvasH ?>"></canvas>
                                        <?= $altWarningHtml ?>
                                        <?php $mediaCover = true; include __DIR__ . '/_media_output.php'; ?>
                                    </div>
                                <?php else: ?>
                                    <!-- Original: Natürliche Proportionen -->
                                    <div class="uk-position-relative">
                                        <?= $altWarningHtml ?>
                                        <?php $mediaCover = false; include __DIR__ . '/_media_output.php'; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php
                        $contentWrapperClass = '';
                        if ($mediaVerticalAlign === 'middle') {
                            $contentWrapperClass = ' uk-flex uk-flex-column uk-flex-center';
                        } elseif ($mediaVerticalAlign === 'bottom') {
                            $contentWrapperClass = ' uk-flex uk-flex-column uk-flex-right';
                        }
                        
                        // Der Wrapper muss volle Höhe haben damit Flex-Alignment innerhalb funktioniert
                        if ($isHorizontal) {
                            $contentWrapperClass .= ' uk-height-1-1';
                        }
                        ?>
                        <div class="uk-width-expand">
                            <?php include __DIR__ . '/_content_output.php'; ?>
                        </div>
                        
                        <?php if ($layout === 'media-right' && $image): ?>
                            <div class="uk-card-media-right uk-width-<?= $mediaWidth ?>">
                                <?php if ($useCanvas): ?>
                                    <!-- Festes Ratio via Canvas -->
                                    <div class="uk-cover-container uk-position-relative">
                                        <canvas width="<?= $canvasW ?>" height="<?= $canvasH ?>"></canvas>
                                        <?= $altWarningHtml ?>
                                        <?php $mediaCover = $applyCover; include __DIR__ . '/_media_output.php'; ?>
                                    </div>
                                <?php elseif ($applyCover): ?>
                                    <!-- Cover-Modus: Mobile ausgewähltes Ratio, ab @m Container-Höhe -->
                                    <div class="uk-cover-container uk-height-1-1@m uk-position-relative" style="overflow: hidden;">
                                        <canvas width="<?= $canvasW ?>" height="<?= $canvasH ?>"></canvas>
                                        <?= $altWarningHtml ?>
                                        <?php $mediaCover = true; include __DIR__ . '/_media_output.php'; ?>
                                    </div>
                                <?php else: ?>
                                    <!-- Original: Natürliche Proportionen -->
                                    <div class="uk-position-relative">
                                        <?= $altWarningHtml ?>
                                        <?php $mediaCover = false; include __DIR__ . '/_media_output.php'; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php elseif ($isOverlay && $image): ?>
                    <!-- Overlay Layout - nimmt volle Card-Höhe bei match-height -->
                    <div class="uk-cover-container uk-position-relative<?= ($matchHeight) ? ' uk-flex-1' : '' ?>">
                        <canvas width="<?= $canvasW ?>" height="<?= $canvasH ?>" style="display: block; width: 100%; height: auto; visibility: hidden;"></canvas>
                        <?= $altWarningHtml ?>
                        <?php $mediaCover = true; include __DIR__ . '/_media_output.php'; ?>
                        <div class="uk-overlay uk-overlay-primary uk-position-bottom uk-light">
                            <?php if ($title): ?>
                                <h3 class="uk-card-title"><?= rex_escape($title) ?></h3>
                            <?php endif; ?>
                            <?php if ($text): ?>
                                <div class="uk-text-small"><?= $text ?></div>
                            <?php endif; ?>
                            <?php if ($href && !$linkCard): ?>
                                <div class="uk-margin-small-top">
                                    <a href="<?= $href ?>" class="uk-button uk-button-text">
                                        <?= rex_escape($linkText) ?> <span uk-icon="chevron-right"></span>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                <?php else: ?>
                    <!-- Vertikales Layout (oben/unten) -->
                    <?php if ($layout === 'media-top' && $image): ?>
                        <div class="uk-card-media-top uk-position-relative">
                            <?= $altWarningHtml ?>
                            <?php $mediaCover = false; include __DIR__ . '/_media_output.php'; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php include __DIR__ . '/_content_output.php'; ?>

                    <?php if ($layout === 'media-bottom' && $image): ?>
                        <div class="uk-card-media-bottom uk-position-relative">
                            <?= $altWarningHtml ?>
                            <?php $mediaCover = false; include __DIR__ . '/_media_output.php'; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        
    <?php endforeach; ?>
</div>

<?php if ($containerWidth): ?>
</div>
<?php endif; ?>

<?php if ($hasSection): ?>
<?php if ($isSectionBgVideo): ?>
</div>
<?php endif; ?>
</section>
<?php endif; ?>
