<?php

/**
 * Cards Grid Element - Konfiguration
 * Erweitert mit allen Optionen des Content Builder Pro Moduls
 * Unterstützt Integration mit uikit_theme_builder (DomainContext)
 */

// Prüfen ob uikit_theme_builder verfügbar ist für dynamische Farboptionen
$hasUikitThemeBuilder = rex_addon::get('uikit_theme_builder')->isAvailable();

// Theme-Auswahl Optionen (nur wenn Theme Builder verfügbar)
$themeChoices = [];
if ($hasUikitThemeBuilder && class_exists('UikitThemeBuilder\DomainContext')) {
    $themeChoices = ['' => '-- Automatisch (Domain) --'];
    $availableThemes = \UikitThemeBuilder\DomainContext::getAvailableThemes();
    $themeChoices = array_merge($themeChoices, $availableThemes);
}

// Standard Card-Style Optionen (für choice Fallback)
$cardStyleChoices = [
    'default' => 'Default (mit Rahmen)',
    'primary' => 'Primary',
    'secondary' => 'Secondary',
    'muted' => 'Muted (Grau)',
    'hover' => 'Hover Effect',
    'transparent' => 'Transparent'
];

// Card-Style Farben für color_swatches
$cardStyleColors = [
    'uk-card-default' => ['color' => '#ffffff', 'label' => 'Default (Weiß)'],
    'uk-card-primary' => ['color' => '#1e87f0', 'label' => 'Primary'],
    'uk-card-secondary' => ['color' => '#222222', 'label' => 'Secondary']
];

// Standard Background-Optionen
$backgroundChoices = [
    '' => 'Keine',
    'uk-background-default' => 'Default (Weiß)',
    'uk-background-muted' => 'Muted (Grau)',
    'uk-background-primary' => 'Primary',
    'uk-background-secondary' => 'Secondary'
];

// Background Farben für color_swatches
$backgroundColors = [
    '' => ['color' => 'transparent', 'label' => 'Keine'],
    'uk-background-default' => ['color' => '#ffffff', 'label' => 'Default (Weiß)'],
    'uk-background-muted' => ['color' => '#f8f8f8', 'label' => 'Muted (Grau)'],
    'uk-background-primary' => ['color' => '#1e87f0', 'label' => 'Primary'],
    'uk-background-secondary' => ['color' => '#222222', 'label' => 'Secondary']
];

// Dynamische Farben aus Theme laden wenn verfügbar
if ($hasUikitThemeBuilder && class_exists('UikitThemeBuilder\DomainContext')) {
    // Card-Style Optionen aus Theme (bereits im richtigen Format für color_swatches)
    $themeCardStyles = \UikitThemeBuilder\DomainContext::getCardStyleOptions();
    if (!empty($themeCardStyles)) {
        $cardStyleColors = $themeCardStyles;
        // Auch choice-Format für Fallback
        $cardStyleChoices = [];
        foreach ($themeCardStyles as $class => $data) {
            $cardStyleChoices[$class] = $data['label'] ?? ucfirst(str_replace(['uk-card-', 'uk-background-'], '', $class));
        }
    }
    
    // Background-Optionen aus Theme
    $themeBackgrounds = \UikitThemeBuilder\DomainContext::getBackgroundOptions();
    if (!empty($themeBackgrounds)) {
        $backgroundColors = ['' => ['color' => 'transparent', 'label' => 'Keine']];
        $backgroundChoices = ['' => 'Keine'];
        foreach ($themeBackgrounds as $class => $data) {
            $backgroundColors[$class] = $data;
            $backgroundChoices[$class] = $data['label'] ?? ucfirst(str_replace('uk-background-', '', $class));
        }
    }
}

// Layout-Optionen für Selectpicker
$layoutChoices = [
    'media-top' => 'Medium oben',
    'media-bottom' => 'Medium unten',
    'media-left' => 'Medium links',
    'media-right' => 'Medium rechts',
    'media-overlay' => 'Overlay'
];

// Layout-Icons für Selectpicker (kleine SVG-Piktogramme)
$layoutIcons = [
    'media-top' => '<svg width="24" height="18" viewBox="0 0 24 18" style="vertical-align:middle;margin-right:6px;"><rect x="1" y="1" width="22" height="7" fill="#666" rx="1"/><rect x="1" y="10" width="22" height="7" fill="none" stroke="#ccc" rx="1"/></svg>',
    'media-bottom' => '<svg width="24" height="18" viewBox="0 0 24 18" style="vertical-align:middle;margin-right:6px;"><rect x="1" y="1" width="22" height="7" fill="none" stroke="#ccc" rx="1"/><rect x="1" y="10" width="22" height="7" fill="#666" rx="1"/></svg>',
    'media-left' => '<svg width="24" height="18" viewBox="0 0 24 18" style="vertical-align:middle;margin-right:6px;"><rect x="1" y="1" width="10" height="16" fill="#666" rx="1"/><rect x="13" y="1" width="10" height="16" fill="none" stroke="#ccc" rx="1"/></svg>',
    'media-right' => '<svg width="24" height="18" viewBox="0 0 24 18" style="vertical-align:middle;margin-right:6px;"><rect x="1" y="1" width="10" height="16" fill="none" stroke="#ccc" rx="1"/><rect x="13" y="1" width="10" height="16" fill="#666" rx="1"/></svg>',
    'media-overlay' => '<svg width="24" height="18" viewBox="0 0 24 18" style="vertical-align:middle;margin-right:6px;"><rect x="1" y="1" width="22" height="16" fill="#666" rx="1"/><rect x="3" y="10" width="18" height="5" fill="#333" opacity="0.7" rx="1"/></svg>'
];

// Schatten-Icons für Selectpicker (visualisieren die Schattenstärke)
$shadowChoices = [
    '' => 'Kein Schatten',
    'uk-box-shadow-small' => 'Klein',
    'uk-box-shadow-medium' => 'Mittel',
    'uk-box-shadow-large' => 'Groß',
    'uk-box-shadow-xlarge' => 'Extra Groß',
    'uk-card-hover' => 'Nur bei Hover'
];

$shadowIcons = [
    '' => '<svg width="24" height="18" viewBox="0 0 24 18" style="vertical-align:middle;margin-right:6px;"><rect x="2" y="2" width="20" height="14" fill="#fff" stroke="#ccc" rx="2"/></svg>',
    'uk-box-shadow-small' => '<svg width="24" height="18" viewBox="0 0 24 18" style="vertical-align:middle;margin-right:6px;"><rect x="3" y="3" width="20" height="14" fill="#e0e0e0" rx="2"/><rect x="1" y="1" width="20" height="14" fill="#fff" stroke="#ccc" rx="2"/></svg>',
    'uk-box-shadow-medium' => '<svg width="24" height="18" viewBox="0 0 24 18" style="vertical-align:middle;margin-right:6px;"><rect x="4" y="4" width="20" height="14" fill="#ccc" rx="2"/><rect x="1" y="1" width="20" height="14" fill="#fff" stroke="#bbb" rx="2"/></svg>',
    'uk-box-shadow-large' => '<svg width="24" height="18" viewBox="0 0 24 18" style="vertical-align:middle;margin-right:6px;"><rect x="5" y="5" width="20" height="14" fill="#aaa" rx="2"/><rect x="1" y="1" width="20" height="14" fill="#fff" stroke="#999" rx="2"/></svg>',
    'uk-box-shadow-xlarge' => '<svg width="24" height="18" viewBox="0 0 24 18" style="vertical-align:middle;margin-right:6px;"><rect x="6" y="6" width="20" height="14" fill="#888" rx="2"/><rect x="1" y="1" width="20" height="14" fill="#fff" stroke="#777" rx="2"/></svg>',
    'uk-card-hover' => '<svg width="24" height="18" viewBox="0 0 24 18" style="vertical-align:middle;margin-right:6px;"><rect x="2" y="2" width="20" height="14" fill="#fff" stroke="#ccc" rx="2" stroke-dasharray="2,2"/><text x="12" y="12" font-size="8" text-anchor="middle" fill="#999">↑</text></svg>'
];

// Card-Width Optionen für individuelle Breiten (responsive)
$cardWidthChoices = [
    '' => 'Standard (aus Grid)',
    '1-1' => '100% (alle Screens)',
    '1-2' => '50% (alle Screens)',
    '1-3' => '33% (alle Screens)',
    '2-3' => '66% (alle Screens)',
    '1-4' => '25% (alle Screens)',
    '3-4' => '75% (alle Screens)',
    '1-5' => '20% (alle Screens)',
    '2-5' => '40% (alle Screens)',
    '3-5' => '60% (alle Screens)',
    '4-5' => '80% (alle Screens)',
    'expand' => 'Ausdehnen',
    'auto' => 'Automatisch',
    // Responsive Varianten ab Medium
    '1-1@m' => '100% (ab Medium)',
    '1-2@m' => '50% (ab Medium)',
    '1-3@m' => '33% (ab Medium)',
    '2-3@m' => '66% (ab Medium)',
    '1-4@m' => '25% (ab Medium)',
    '3-4@m' => '75% (ab Medium)',
    'expand@m' => 'Ausdehnen (ab Medium)',
    'auto@m' => 'Automatisch (ab Medium)'
];

// Optional: Layouts aus SVG-Klasse laden wenn verfügbar
if (class_exists('YFormContentBuilderSvg')) {
    $rawLayouts = YFormContentBuilderSvg::getLayoutOptions();
    if (!empty($rawLayouts)) {
        $layoutChoices = [];
        foreach ($rawLayouts as $key => $data) {
            $layoutChoices[$key] = $data['label'] ?? $key;
            // Wenn SVG vorhanden, für Icon verwenden
            if (!empty($data['img'])) {
                $layoutIcons[$key] = '<img src="' . $data['img'] . '" style="width:24px;height:18px;vertical-align:middle;margin-right:6px;">';
            }
        }
    }
}

return [
    'label' => 'Cards Grid Pro',
    'icon' => 'fa fa-th-large',
    'description' => 'Karten-Grid mit flexiblen Layouts und Media-Optionen',
    
    // Settings Modal für Grid/Section-Einstellungen
    'settings_modal' => [
        'label' => 'Grid & Sektion Einstellungen',
        'icon' => 'fa-cog',
        'fields' => ['columns', 'columns_tablet', 'columns_mobile', 'gap', 'match_height', 'card_style', 'card_size', 'card_shadow', 'section_bg', 'section_bg_image', 'section_padding', 'container_width', 'animations_enabled', 'animations_scrollspy', 'animations_delay', 'animations_repeat', 'animations_cascading']
    ],
    
    'fields' => [
        // =============================================================================
        // GRID EINSTELLUNGEN
        // =============================================================================
        'columns' => [
            'type' => 'choice',
            'label' => 'Spalten (Desktop)',
            'choices' => [
                '1' => '1 Spalte (100%)',
                '2' => '2 Spalten',
                '3' => '3 Spalten',
                '4' => '4 Spalten',
                '5' => '5 Spalten',
                '6' => '6 Spalten'
            ],
            'default' => '3'
        ],
        'columns_tablet' => [
            'type' => 'choice',
            'label' => 'Spalten (Tablet)',
            'choices' => [
                '1' => '1 Spalte',
                '2' => '2 Spalten',
                '3' => '3 Spalten',
                '4' => '4 Spalten'
            ],
            'default' => '2'
        ],
        'columns_mobile' => [
            'type' => 'choice',
            'label' => 'Spalten (Mobile)',
            'choices' => [
                '1' => '1 Spalte',
                '2' => '2 Spalten'
            ],
            'default' => '1'
        ],
        'gap' => [
            'type' => 'choice',
            'label' => 'Abstand zwischen Cards',
            'choices' => [
                'collapse' => 'Kein Abstand (alle Richtungen)',
                'column-collapse' => 'Kein Abstand (links/rechts)',
                'small' => 'Klein (15px)',
                'medium' => 'Mittel (30px)',
                'large' => 'Groß (40px)'
            ],
            'default' => 'medium'
        ],
        'match_height' => [
            'type' => 'checkbox',
            'label' => 'Gleiche Höhe für alle Cards'
        ],
        
        // =============================================================================
        // GLOBALE CARD EINSTELLUNGEN
        // =============================================================================
        'card_style' => [
            'type' => 'choice',
            'label' => 'Karten-Farbe',
            'choices' => $cardStyleChoices,
            'choice_colors' => $cardStyleColors,
            'selectpicker' => true,
            'default' => 'uk-card-default'
        ],
        'card_size' => [
            'type' => 'choice',
            'label' => 'Card Padding',
            'choices' => [
                'small' => 'Klein',
                'default' => 'Standard',
                'large' => 'Groß'
            ],
            'default' => 'default'
        ],
        'card_shadow' => [
            'type' => 'choice',
            'label' => 'Schatten',
            'choices' => array_merge(['' => 'Standard (mit Schatten)'], $shadowChoices),
            'choice_icons' => array_merge(['' => '<svg width="24" height="18" viewBox="0 0 24 18" style="vertical-align:middle;margin-right:6px;"><rect x="3" y="3" width="20" height="14" fill="#ddd" rx="2"/><rect x="1" y="1" width="20" height="14" fill="#fff" stroke="#ccc" rx="2"/></svg>'], $shadowIcons),
            'selectpicker' => true,
            'default' => ''
        ],
        
        // =============================================================================
        // SEKTION EINSTELLUNGEN
        // =============================================================================
        'section_bg' => [
            'type' => 'choice',
            'label' => 'Sektions-Hintergrund',
            'choices' => $backgroundChoices,
            'default' => ''
        ],
        'section_bg_image' => [
            'type' => 'be_media',
            'label' => 'Sektions-Hintergrund (Bild/Video)',
            'notice' => 'Hintergrundbild oder -video (MP4, WebM). Video wird automatisch mit Autoplay und Loop abgespielt.'
        ],
        'section_padding' => [
            'type' => 'choice',
            'label' => 'Sektions-Padding',
            'choices' => [
                '' => 'Standard',
                'uk-padding-remove' => 'Keine Füllung',
                'uk-padding-small' => 'Klein',
                'uk-padding' => 'Mittel',
                'uk-padding-large' => 'Groß'
            ],
            'default' => ''
        ],
        'container_width' => [
            'type' => 'choice',
            'label' => 'Container-Breite',
            'choices' => [
                'uk-container' => 'Standard',
                'uk-container uk-container-xsmall' => 'Extra schmal',
                'uk-container uk-container-small' => 'Schmal',
                'uk-container uk-container-large' => 'Weit',
                'uk-container uk-container-xlarge' => 'Extra weit',
                'uk-container uk-container-expand' => 'Maximale Breite',
                '' => 'Volle Breite (kein Container)'
            ],
            'default' => 'uk-container'
        ],
        
        // =============================================================================
        // GLOBALE ANIMATION EINSTELLUNGEN (UIkit)
        // =============================================================================
        'animations_enabled' => [
            'type' => 'checkbox',
            'label' => 'Animationen aktivieren (UIkit)',
            'notice' => 'Aktiviert ScrollSpy-Animationen beim Scrollen (nur UIkit)'
        ],
        'animations_scrollspy' => [
            'type' => 'checkbox',
            'label' => 'ScrollSpy aktivieren',
            'notice' => 'Animationen starten wenn Cards in den Viewport kommen (statt sofort). Gilt für alle Cards.'
        ],
        'animations_delay' => [
            'type' => 'text',
            'label' => 'Animationsverzögerung (ms)',
            'notice' => 'Verzögerung zwischen Card-Animationen. Standardwert: 100',
            'default' => '100'
        ],
        'animations_repeat' => [
            'type' => 'checkbox',
            'label' => 'Animationen wiederholen',
            'notice' => 'Wenn aktiviert, werden Animationen jedes Mal wiederholt wenn die Cards sichtbar werden'
        ],
        'animations_cascading' => [
            'type' => 'checkbox',
            'label' => 'Kaskadierende Verzögerung',
            'notice' => 'Wenn aktiviert, wird die Verzögerung für jede Card addiert (1. Card: 100ms, 2. Card: 200ms, etc.)'
        ],
        
        // =============================================================================
        // CARDS REPEATER
        // =============================================================================
        'items' => [
            'type' => 'repeater',
            'label' => 'Cards',
            
            // Modal für erweiterte Card-Optionen
            'item_modal' => [
                'label' => 'Erweiterte Optionen',
                'icon' => 'fa-ellipsis-h',
                'fields' => [
                    'subtitle', 'badge', 'badge_color', 
                    'card_width', 'card_style_override', 'card_shadow_override',
                    'link_type', 'link_url', 'link_internal', 'link_text', 'link_card',
                    'animation'
                ]
            ],
            
            // Medien-Modal für erweiterte Bildoptionen
            'media_modal' => [
                'label' => 'Medieneinstellungen',
                'icon' => 'fa-sliders',
                'trigger_after' => 'image',
                'fields' => [
                    'image_alt', 'image_decorative', 'image_title', 'media_width', 
                    'video_display', 'video_controls', 'media_lightbox', 'media_cover'
                ]
            ],
            
            'fields' => [
                // Layout-Auswahl
                'layout' => [
                    'type' => 'choice',
                    'label' => 'Layout',
                    'choices' => $layoutChoices,
                    'choice_icons' => $layoutIcons,
                    'selectpicker' => true,
                    'default' => 'media-top'
                ],
                
                // Media - nur Hauptfeld sichtbar
                'image' => [
                    'type' => 'be_media',
                    'label' => 'Bild oder Video',
                    'preview' => true,
                    'notice' => 'Bilder und Videos werden unterstützt'
                ],
                
                // Medien-Optionen (im Modal)
                'image_alt' => [
                    'type' => 'text',
                    'label' => 'Alt-Text',
                    'notice' => 'Überschreibt den Alt-Text aus dem Medienpool'
                ],
                'image_decorative' => [
                    'type' => 'checkbox',
                    'label' => 'Dekoratives Bild (kein Alt-Text nötig)',
                    'notice' => 'Automatisch aktiv wenn die gesamte Card verlinkt ist'
                ],
                'image_title' => [
                    'type' => 'text',
                    'label' => 'Bildunterschrift',
                    'notice' => 'Optional'
                ],
                'media_width' => [
                    'type' => 'choice',
                    'label' => 'Medien-Breite (bei links/rechts)',
                    'choices' => [
                        '1-4@m' => '25% (Schmal)',
                        '1-3@m' => '33% (Standard)',
                        '1-2@m' => '50% (Mittel)',
                        '2-3@m' => '66% (Breit)'
                    ],
                    'default' => '1-3@m'
                ],
                'media_lightbox' => [
                    'type' => 'checkbox',
                    'label' => 'Bild/Video in Lightbox öffnen'
                ],
                'media_cover' => [
                    'type' => 'checkbox',
                    'label' => 'Cover-Modus (füllt den Bereich aus)'
                ],
                
                // Video-spezifische Optionen
                'video_display' => [
                    'type' => 'choice',
                    'label' => 'Video-Darstellung',
                    'selectpicker' => false,
                    'choices' => [
                        'inline' => 'Video direkt abspielen',
                        'poster' => 'Standbild mit Play-Button'
                    ],
                    'default' => 'inline',
                    'notice' => 'Bei Lightbox wird immer ein Standbild mit Button gezeigt'
                ],
                'video_controls' => [
                    'type' => 'choice',
                    'label' => 'Video-Steuerung',
                    'selectpicker' => false,
                    'choices' => [
                        'autoplay' => 'Autoplay (stumm, Loop)',
                        'controls' => 'Mit Player-Controls',
                        'hover' => 'Abspielen bei Hover'
                    ],
                    'default' => 'autoplay',
                    'notice' => 'Nur wenn Video direkt abgespielt wird'
                ],
                
                // Content - Hauptfelder sichtbar
                'title' => [
                    'type' => 'text',
                    'label' => 'Titel'
                ],
                'text' => [
                    'type' => 'cke5',
                    'label' => 'Text'
                ],
                
                // Erweiterte Optionen (im Modal)
                'subtitle' => [
                    'type' => 'text',
                    'label' => 'Untertitel',
                    'notice' => 'Optional'
                ],
                'badge' => [
                    'type' => 'text',
                    'label' => 'Badge',
                    'notice' => 'z.B. "NEU", "SALE"'
                ],
                'badge_color' => [
                    'type' => 'choice',
                    'label' => 'Badge Farbe',
                    'choices' => [
                        'primary' => 'Primary',
                        'success' => 'Success',
                        'warning' => 'Warning',
                        'danger' => 'Danger'
                    ],
                    'default' => 'primary'
                ],
                
                // Card-spezifische Überschreibungen
                'card_width' => [
                    'type' => 'choice',
                    'label' => 'Card Breite (individuell)',
                    'choices' => $cardWidthChoices,
                    'default' => ''
                ],
                'card_style_override' => [
                    'type' => 'choice',
                    'label' => 'Card Farbe (überschreiben)',
                    'choices' => array_merge(['' => 'Standard (aus Grid)'], $cardStyleChoices),
                    'choice_colors' => array_merge(['' => ['color' => 'transparent', 'label' => 'Standard']], $cardStyleColors),
                    'selectpicker' => true,
                    'default' => ''
                ],
                'card_shadow_override' => [
                    'type' => 'choice',
                    'label' => 'Schatten (überschreiben)',
                    'choices' => array_merge(['' => 'Standard (aus Grid)'], $shadowChoices),
                    'choice_icons' => array_merge(['' => '<svg width="24" height="18" viewBox="0 0 24 18" style="vertical-align:middle;margin-right:6px;"><rect x="2" y="2" width="20" height="14" fill="#f5f5f5" stroke="#ddd" rx="2" stroke-dasharray="2,2"/></svg>'], $shadowIcons),
                    'selectpicker' => true,
                    'default' => ''
                ],
                
                // Link-Optionen
                'link_type' => [
                    'type' => 'choice',
                    'label' => 'Link',
                    'choices' => [
                        '' => 'Kein Link',
                        'external' => 'Externe URL',
                        'internal' => 'Interne Seite'
                    ],
                    'default' => ''
                ],
                'link_url' => [
                    'type' => 'text',
                    'label' => 'Externe URL'
                ],
                'link_internal' => [
                    'type' => 'be_link',
                    'label' => 'Interne Seite'
                ],
                'link_text' => [
                    'type' => 'text',
                    'label' => 'Link Text',
                    'default' => 'Mehr erfahren'
                ],
                'link_card' => [
                    'type' => 'checkbox',
                    'label' => 'Gesamte Card verlinken'
                ],
                
                // Animation Optionen (nur UIkit, im Modal)
                'animation' => [
                    'type' => 'choice',
                    'label' => 'Animation',
                    'choices' => [
                        '' => 'Keine',
                        'uk-animation-fade' => 'Fade In',
                        'uk-animation-scale-up' => 'Scale Up',
                        'uk-animation-scale-down' => 'Scale Down',
                        'uk-animation-slide-top' => 'Slide from Top',
                        'uk-animation-slide-bottom' => 'Slide from Bottom',
                        'uk-animation-slide-left' => 'Slide from Left',
                        'uk-animation-slide-right' => 'Slide from Right',
                        'uk-animation-slide-top-small' => 'Slide from Top (Small)',
                        'uk-animation-slide-bottom-small' => 'Slide from Bottom (Small)',
                        'uk-animation-slide-left-small' => 'Slide from Left (Small)',
                        'uk-animation-slide-right-small' => 'Slide from Right (Small)',
                        'uk-animation-shake' => 'Shake'
                    ],
                    'default' => '',
                    'notice' => 'Nur sichtbar wenn Animationen global aktiviert sind'
                ]
            ]
        ]
    ],
];
