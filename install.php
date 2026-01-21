<?php

/**
 * Install-Script für YForm Content Builder
 */

// Prüfen ob YForm installiert ist
if (!rex_addon::get('yform')->isInstalled()) {
    throw new rex_functional_exception('Dieses Addon benötigt das "yform" Addon.');
}

// Media Manager Typen anlegen
if (rex_addon::get('media_manager')->isAvailable()) {
    $mm = YFormContentMediaManagerHelper::factory();
    
    // Prüfen ob focuspoint AddOn verfügbar ist
    $hasFocuspoint = rex_addon::get('focuspoint')->isAvailable();
    
    // =============================================================================
    // CARD BILDER - Verschiedene Formate für Responsive Design
    // =============================================================================
    $ratios = [
        '16_9' => ['w' => 16, 'h' => 9],
        '21_9' => ['w' => 21, 'h' => 9],
        '4_3' => ['w' => 4, 'h' => 3],
        '1_1' => ['w' => 1, 'h' => 1]
    ];
    
    $widths = [400, 800, 1200, 1600];
    
    foreach ($ratios as $ratioKey => $ratio) {
        foreach ($widths as $width) {
            $typeName = 'card_' . $ratioKey . '_w' . $width;
            $height = round(($width / $ratio['w']) * $ratio['h']);
            
            $mm->addType($typeName, 'Card ' . str_replace('_', ':', $ratioKey) . ' (' . $width . 'px)');
            
            // 1. Resize auf max Breite
            $mm->addEffect($typeName, 'resize', [
                'width' => $width,
                'height' => $height,
                'style' => 'maximum',
                'allow_enlarge' => 'not_enlarge'
            ], 1);
            
            // 2. Zuschneiden mit Fokuspunkt
            if ($hasFocuspoint) {
                $mm->addEffect($typeName, 'focuspoint_fit', [
                    'width' => $ratio['w'] . 'fr',
                    'height' => $ratio['h'] . 'fr',
                    'zoom' => '0',
                    'meta' => 'med_focuspoint',
                    'focus' => '50.0,50.0'
                ], 2);
            } else {
                $mm->addEffect($typeName, 'crop', [
                    'width' => $width,
                    'height' => $height,
                    'offset_width' => '',
                    'offset_height' => '',
                    'hpos' => 'center',
                    'vpos' => 'middle'
                ], 2);
            }
        }
    }

    // Default Card Typ (Abwärtskompatibilität)
    $mm->addType('content_card', 'Content Builder: Card Bild (Default 16:9)');
    $mm->addEffect('content_card', 'resize', [
        'width' => 1200,
        'height' => 1200,
        'style' => 'maximum',
        'allow_enlarge' => 'not_enlarge'
    ], 1);
    
    if ($hasFocuspoint) {
        $mm->addEffect('content_card', 'focuspoint_fit', [
            'width' => '16fr',
            'height' => '9fr',
            'zoom' => '0',
            'meta' => 'med_focuspoint',
            'focus' => '50.0,50.0'
        ], 2);
    } else {
        $mm->addEffect('content_card', 'crop', [
            'width' => 800,
            'height' => 450,
            'offset_width' => '',
            'offset_height' => '',
            'hpos' => 'center',
            'vpos' => 'middle'
        ], 2);
    }
    
    // =============================================================================
    // GALLERY RESIZE - Nur Resize ohne Cropping für Masonry + Original Ratio
    // =============================================================================
    $mm->addType('gallery_resize', 'Content Builder: Gallery Resize (Original Ratio)');
    
    $mm->addEffect('gallery_resize', 'resize', [
        'width' => 1200,
        'height' => 1200,
        'style' => 'maximum',
        'allow_enlarge' => 'not_enlarge'
    ], 1);
    
    // =============================================================================
    // GALLERY THUMBNAILS - Quadratisch mit Fokuspunkt
    // =============================================================================
    $mm->addType('gallery_thumb', 'Content Builder: Gallery Thumbnail (1:1 mit Fokuspunkt)');
    
    $mm->addEffect('gallery_thumb', 'resize', [
        'width' => 600,
        'height' => 600,
        'style' => 'maximum',
        'allow_enlarge' => 'not_enlarge'
    ], 1);
    
    if ($hasFocuspoint) {
        $mm->addEffect('gallery_thumb', 'focuspoint_fit', [
            'width' => '1fr',
            'height' => '1fr',
            'zoom' => '0',
            'meta' => 'med_focuspoint',
            'focus' => '50.0,50.0'
        ], 2);
    } else {
        $mm->addEffect('gallery_thumb', 'crop', [
            'width' => 400,
            'height' => 400,
            'offset_width' => '',
            'offset_height' => '',
            'hpos' => 'center',
            'vpos' => 'middle'
        ], 2);
    }
    
    // =============================================================================
    // GALLERY FULL - Volle Ansicht 16:9
    // =============================================================================
    $mm->addType('gallery_full', 'Content Builder: Gallery Vollbild (16:9 mit Fokuspunkt)');
    
    $mm->addEffect('gallery_full', 'resize', [
        'width' => 1920,
        'height' => 1920,
        'style' => 'maximum',
        'allow_enlarge' => 'not_enlarge'
    ], 1);
    
    if ($hasFocuspoint) {
        $mm->addEffect('gallery_full', 'focuspoint_fit', [
            'width' => '16fr',
            'height' => '9fr',
            'zoom' => '0',
            'meta' => 'med_focuspoint',
            'focus' => '50.0,50.0'
        ], 2);
    } else {
        $mm->addEffect('gallery_full', 'crop', [
            'width' => 1920,
            'height' => 1080,
            'offset_width' => '',
            'offset_height' => '',
            'hpos' => 'center',
            'vpos' => 'middle'
        ], 2);
    }
    
    // =============================================================================
    // SLIDESHOW - 16:9 Format
    // =============================================================================
    $mm->addType('content_slideshow', 'Content Builder: Slideshow (16:9 mit Fokuspunkt)');
    
    $mm->addEffect('content_slideshow', 'resize', [
        'width' => 1920,
        'height' => 1920,
        'style' => 'maximum',
        'allow_enlarge' => 'not_enlarge'
    ], 1);
    
    if ($hasFocuspoint) {
        $mm->addEffect('content_slideshow', 'focuspoint_fit', [
            'width' => '16fr',
            'height' => '9fr',
            'zoom' => '0',
            'meta' => 'med_focuspoint',
            'focus' => '50.0,50.0'
        ], 2);
    } else {
        $mm->addEffect('content_slideshow', 'crop', [
            'width' => 1920,
            'height' => 1080,
            'offset_width' => '',
            'offset_height' => '',
            'hpos' => 'center',
            'vpos' => 'middle'
        ], 2);
    }
    
    // =============================================================================
    // TEXT-IMAGE - Flexibles Format mit max Größe
    // =============================================================================
    $mm->addType('content_text_image', 'Content Builder: Text-Bild Element');
    
    $mm->addEffect('content_text_image', 'resize', [
        'width' => 1200,
        'height' => 1200,
        'style' => 'maximum',
        'allow_enlarge' => 'not_enlarge'
    ], 1);
    
    // =============================================================================
    // MEDIA SHOWCASE - 16:9 Format
    // =============================================================================
    $mm->addType('content_media_showcase', 'Content Builder: Media Showcase (16:9 mit Fokuspunkt)');
    
    $mm->addEffect('content_media_showcase', 'resize', [
        'width' => 1920,
        'height' => 1920,
        'style' => 'maximum',
        'allow_enlarge' => 'not_enlarge'
    ], 1);
    
    if ($hasFocuspoint) {
        $mm->addEffect('content_media_showcase', 'focuspoint_fit', [
            'width' => '16fr',
            'height' => '9fr',
            'zoom' => '0',
            'meta' => 'med_focuspoint',
            'focus' => '50.0,50.0'
        ], 2);
    } else {
        $mm->addEffect('content_media_showcase', 'crop', [
            'width' => 1920,
            'height' => 1080,
            'offset_width' => '',
            'offset_height' => '',
            'hpos' => 'center',
            'vpos' => 'middle'
        ], 2);
    }
    
    // =============================================================================
    // VIDEO POSTER - Thumbnail aus Video mit convert2img (16:9)
    // Für Cards und andere Elemente die Video-Vorschaubilder brauchen
    // =============================================================================
    $mm->addType('video_poster', 'Content Builder: Video Poster (16:9, ffmpeg/convert)');
    
    // 1. Zuerst Video zu Bild konvertieren (ffmpeg für Videos, ImageMagick für PDFs etc.)
    $mm->addEffect('video_poster', 'convert2img', [
        'convert_to' => 'jpg',
        'density' => '150',
        'color' => '#ffffff'
    ], 1);
    
    // 2. Auf maximale Größe begrenzen
    $mm->addEffect('video_poster', 'resize', [
        'width' => 1200,
        'height' => 1200,
        'style' => 'maximum',
        'allow_enlarge' => 'not_enlarge'
    ], 2);
    
    // 3. Dann auf 16:9 zuschneiden
    if ($hasFocuspoint) {
        $mm->addEffect('video_poster', 'focuspoint_fit', [
            'width' => '16fr',
            'height' => '9fr',
            'zoom' => '0',
            'meta' => 'med_focuspoint',
            'focus' => '50.0,50.0'
        ], 3);
    } else {
        $mm->addEffect('video_poster', 'crop', [
            'width' => 800,
            'height' => 450,
            'offset_width' => '',
            'offset_height' => '',
            'hpos' => 'center',
            'vpos' => 'middle'
        ], 3);
    }
    
    // Alle Typen installieren
    $mm->install();
}

$this->setProperty('install', true);
