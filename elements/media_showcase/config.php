<?php

return [
    'label' => 'Media Showcase',
    'description' => 'Zeigt Bilder oder Videos mit korrekten Seitenverhältnissen und Platzhaltern an',
    'icon' => 'fa-play-circle',
    'category' => 'media',
    'fields' => [
        'media_file' => [
            'type' => 'be_media_enhanced',
            'label' => 'Bild oder Video',
            'notice' => 'Wählen Sie ein Bild (jpg, png, webp) oder Video (mp4, webm) aus',
            'allowed_types' => ['image', 'video'],
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
    ]
];