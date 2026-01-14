<?php
/**
 * Media Showcase Element - Konfiguration mit zentraler Config
 */

// Zentrale Konfigurationsklasse
$config = yform_content_builder_config::class;
$hasThemeBuilder = $config::hasThemeBuilder();

return [
    'label' => 'Media Showcase',
    'description' => 'Zeigt Bilder oder Videos mit korrekten Seitenverhältnissen und Platzhaltern an',
    'icon' => 'fa-play-circle',
    'category' => 'media',
    'settings_modal' => [
        'label' => 'Section-Einstellungen',
        'icon' => 'fa-cog',
        'fields' => $hasThemeBuilder
            ? array_merge(['theme_override'], $config::getSectionFieldNames())
            : $config::getSectionFieldNames()
    ],
    'fields' => array_merge(
        // Theme Override (nur wenn Theme Builder verfügbar)
        $hasThemeBuilder ? ['theme_override' => $config::getThemeOverrideField()] : [],
        
        // Element-spezifische Felder
        [
        'media_file' => [
            'type' => 'be_media',
            'label' => 'Bild oder Video',
            'allowed_types' => 'image,video',
            'notice' => 'Wählen Sie ein Bild (jpg, png, webp) oder Video (mp4, webm) aus'
        ],
        'aspect_ratio' => [
            'type' => 'choice',
            'label' => 'Seitenverhältnis',
            'choices' => [
                '16:9' => '16:9 (Widescreen)',
                '4:3' => '4:3 (Standard)',
                '1:1' => '1:1 (Quadrat)',
                '21:9' => '21:9 (Ultrawide)',
                '3:4' => '3:4 (Portrait)',
                '9:16' => '9:16 (Mobile Portrait)',
                'auto' => 'Original Seitenverhältnis'
            ],
            'default' => '16:9'
        ],
        'title' => [
            'type' => 'text',
            'label' => 'Titel',
            'notice' => 'Optional: Titel für das Medium'
        ],
        'description' => [
            'type' => 'textarea',
            'label' => 'Beschreibung',
            'notice' => 'Optional: Beschreibung des Mediums'
        ],
        'autoplay' => [
            'type' => 'checkbox',
            'label' => 'Video automatisch abspielen (nur für Videos)',
            'notice' => 'Achtung: Autoplay funktioniert meist nur bei stummen Videos'
        ],
        'controls' => [
            'type' => 'checkbox',
            'label' => 'Video-Steuerung anzeigen (nur für Videos)',
            'default' => '1'
        ],
        'muted' => [
            'type' => 'checkbox',
            'label' => 'Video stumm schalten (nur für Videos)',
            'notice' => 'Empfohlen für Autoplay'
        ]
        ],
        
        // Section-Felder aus zentraler Config
        $config::getSectionFields()
    ),
];