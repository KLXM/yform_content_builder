<?php

return [
    'label' => 'Text & Bild',
    'icon' => 'fa fa-file-image-o',
    'description' => 'Text mit Bild kombinieren',
    'field_groups' => [
        'content' => [
            'label' => 'Inhalt',
            'icon' => 'fa-file-text-o',
            'fields' => ['headline', 'headline_tag', 'text', 'image', 'image_alt']
        ],
        'link' => [
            'label' => 'Link / Button',
            'icon' => 'fa-link',
            'fields' => ['link_type', 'link_url', 'link_internal', 'link_text', 'link_target']
        ],
        'design' => [
            'label' => 'Design',
            'icon' => 'fa-paint-brush',
            'fields' => ['layout', 'image_ratio', 'background_color', 'spacing']
        ]
    ],
    'fields' => [
        'layout' => [
            'type' => 'choice',
            'label' => 'Layout',
            'choices' => [
                'image_text' => 'Bild links, Text rechts',
                'text_image' => 'Text links, Bild rechts',
                'image_top' => 'Bild oben, Text unten',
                'text_top' => 'Text oben, Bild unten'
            ],
            'default' => 'image_text'
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
        'image' => [
            'type' => 'be_media',
            'label' => 'Bild'
        ],
        'image_alt' => [
            'type' => 'text',
            'label' => 'Bild Alt-Text',
            'notice' => 'Wichtig für SEO und Barrierefreiheit'
        ],
        'image_ratio' => [
            'type' => 'choice',
            'label' => 'Bildverhältnis',
            'choices' => [
                '1-1' => '1:1 (Quadratisch)',
                '4-3' => '4:3',
                '16-9' => '16:9 (Widescreen)',
                '21-9' => '21:9 (Ultrawide)',
                'auto' => 'Original'
            ],
            'default' => 'auto'
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
        'background_color' => [
            'type' => 'choice',
            'label' => 'Hintergrundfarbe',
            'choices' => [
                '' => 'Keine',
                'bg-light' => 'Hell',
                'bg-dark' => 'Dunkel',
                'bg-primary' => 'Primary',
                'bg-success' => 'Success',
                'bg-info' => 'Info'
            ],
            'default' => ''
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
        ]
    ],
];
