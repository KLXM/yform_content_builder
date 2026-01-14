<?php
/**
 * Text & Media Element - Konfiguration mit zentraler Config
 * Unterstützt Bilder und Videos mit Cover-Modus
 */

// Zentrale Konfigurationsklasse
$config = yform_content_builder_config::class;
$hasThemeBuilder = $config::hasThemeBuilder();

return [
    'label' => 'Text & Media',
    'icon' => 'fa fa-file-image-o',
    'description' => 'Text mit Bild oder Video kombinieren',
    'field_groups' => [
        'content' => [
            'label' => 'Inhalt',
            'icon' => 'fa-file-text-o',
            'fields' => ['headline', 'headline_tag', 'text', 'media', 'media_alt']
        ],
        'media_options' => [
            'label' => 'Media-Optionen',
            'icon' => 'fa-image',
            'fields' => ['media_ratio', 'media_cover', 'media_lightbox', 'video_controls']
        ],
        'link' => [
            'label' => 'Link / Button',
            'icon' => 'fa-link',
            'fields' => ['link_type', 'link_url', 'link_internal', 'link_text', 'link_target']
        ],
        'design' => [
            'label' => 'Design',
            'icon' => 'fa-paint-brush',
            'fields' => array_merge(['layout', 'spacing', 'light_text'], $config::getSectionFieldNames())
        ]
    ],
    'fields' => array_merge(
        // Element-spezifische Felder
        [
        'layout' => [
            'type' => 'choice',
            'label' => 'Layout',
            'choices' => [
                'media_text' => 'Media links, Text rechts',
                'text_media' => 'Text links, Media rechts'
            ],
            'default' => 'media_text'
        ],
        'headline' => [
            'type' => 'text',
            'label' => 'Überschrift',
            'notice' => 'Optional'
        ],
        'headline_tag' => [
            'type' => 'choice',
            'label' => 'Überschrift HTML-Tag',
            'choices' => [
                'h2' => 'H2',
                'h3' => 'H3',
                'h4' => 'H4',
                'h5' => 'H5'
            ],
            'default' => 'h2'
        ],
        'text' => [
            'type' => 'cke5',
            'label' => 'Text'
        ],
        'media' => [
            'type' => 'be_media',
            'label' => 'Bild oder Video',
            'notice' => 'Unterstützt: JPG, PNG, GIF, WebP, SVG, MP4, WebM, OGG'
        ],
        'media_alt' => [
            'type' => 'text',
            'label' => 'Alt-Text',
            'notice' => 'Wichtig für SEO und Barrierefreiheit'
        ],
        'media_ratio' => [
            'type' => 'choice',
            'label' => 'Seitenverhältnis',
            'choices' => [
                'auto' => 'Original',
                '1-1' => '1:1 (Quadratisch)',
                '4-3' => '4:3',
                '16-9' => '16:9 (Widescreen)',
                '21-9' => '21:9 (Ultrawide)'
            ],
            'default' => 'auto'
        ],
        'media_cover' => [
            'type' => 'checkbox',
            'label' => 'Cover-Modus',
            'notice' => 'Media füllt den gesamten Container aus (bei festem Seitenverhältnis automatisch aktiv)'
        ],
        'media_lightbox' => [
            'type' => 'checkbox',
            'label' => 'Lightbox aktivieren',
            'notice' => 'Bild/Video öffnet sich in Großansicht bei Klick'
        ],
        'video_controls' => [
            'type' => 'choice',
            'label' => 'Video-Steuerung',
            'choices' => [
                'autoplay' => 'Autoplay (stumm, Endlosschleife)',
                'hover' => 'Bei Hover abspielen',
                'controls' => 'Mit Steuerung (Benutzer startet)'
            ],
            'default' => 'autoplay',
            'notice' => 'Nur für Videos relevant'
        ],
        'link_type' => [
            'type' => 'choice',
            'label' => 'Link Typ',
            'choices' => [
                '' => 'Kein Link',
                'external' => 'Externe URL',
                'internal' => 'Interne Seite (Linkmap)'
            ],
            'default' => ''
        ],
        'link_url' => [
            'type' => 'text',
            'label' => 'Externe URL',
            'notice' => 'Nur wenn Link Typ = Externe URL'
        ],
        'link_internal' => [
            'type' => 'be_link',
            'label' => 'Interne Seite',
            'notice' => 'Nur wenn Link Typ = Interne Seite'
        ],
        'link_text' => [
            'type' => 'text',
            'label' => 'Link Text',
            'notice' => 'Text für Button'
        ],
        'link_target' => [
            'type' => 'choice',
            'label' => 'Link Ziel',
            'choices' => [
                '_self' => 'Gleiches Fenster',
                '_blank' => 'Neues Fenster'
            ],
            'default' => '_self'
        ],
        'spacing' => [
            'type' => 'choice',
            'label' => 'Abstand',
            'choices' => [
                'default' => 'Standard',
                'compact' => 'Kompakt',
                'spacious' => 'Großzügig'
            ],
            'default' => 'default'
        ],
        'light_text' => [
            'type' => 'checkbox',
            'label' => 'Helle Schrift (uk-light)',
            'notice' => 'Aktivieren bei dunklen Hintergründen für bessere Lesbarkeit'
        ]
        ],
        
        // Section-Felder aus zentraler Config (inkl. section_bg_image!)
        $config::getSectionFields()
    ),
];
