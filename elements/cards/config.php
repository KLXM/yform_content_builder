<?php

use KLXM\YFormContentBuilder\Helper;

/**
 * Cards Grid Element - Konfiguration
 * Erweitert mit allen Optionen des Content Builder Pro Moduls
 * Unterstützt Integration mit uikit_theme_builder (DomainContext)
 */

// ============================================================================
// ELEMENT I18N
// ============================================================================
$_ci = Helper::elementTranslator('cards');

// ============================================================================
// EXTRA FELDER - von CardsRepeaterExtra Klasse befüllt
// ============================================================================
$extra = [];

// Lade CardsRepeaterExtra wenn vorhanden
if (class_exists('CardsRepeaterExtra') && method_exists('CardsRepeaterExtra', 'GetConfig')) {
    $extra = CardsRepeaterExtra::GetConfig();
    #dd('CardsRepeaterExtra gefunden!', $extra);
}

// Prüfen ob uikit_theme_builder verfügbar ist für dynamische Farboptionen
$hasUikitThemeBuilder = rex_addon::get('uikit_theme_builder')->isAvailable();

// Theme-Auswahl Optionen (nur wenn Theme Builder verfügbar)
$themeChoices = [];
if ($hasUikitThemeBuilder && class_exists('UikitThemeBuilder\DomainContext')) {
    $themeChoices = ['' => $_ci('cards_choice_auto_domain', '-- Automatisch (Domain) --')];
    $availableThemes = \UikitThemeBuilder\DomainContext::getAvailableThemes();
    $themeChoices = array_merge($themeChoices, $availableThemes);
}

// Standard Card-Style Optionen (stabile uk-card-* Keys)
$cardStyleChoices = [
    'uk-card-default' => $_ci('cards_choice_card_default', 'Standard (mit Rahmen)'),
    'uk-card-primary' => 'Primary',
    'uk-card-secondary' => 'Secondary',
    'uk-background-muted' => $_ci('cards_choice_muted', 'Muted (Grau)'),
    'uk-card-hover' => $_ci('cards_choice_hover_effect', 'Hover Effect'),
    'uk-card-transparent' => $_ci('cards_choice_transparent', 'Transparent'),
];

// Card-Style Farben für color_swatches
$cardStyleColors = [
    'uk-card-default' => ['color' => '#ffffff', 'label' => $_ci('cards_choice_default_white', 'Default (Weiß)')],
    'uk-card-primary' => ['color' => '#1e87f0', 'label' => 'Primary'],
    'uk-card-secondary' => ['color' => '#222222', 'label' => 'Secondary'],
    'uk-background-muted' => ['color' => '#f8f8f8', 'label' => $_ci('cards_choice_muted', 'Muted (Grau)')],
];

// Standard Background-Optionen
$backgroundChoices = [
    '' => $_ci('cards_choice_none', 'Keine'),
    'uk-background-default' => 'Default (White)',
    'uk-background-muted' => $_ci('cards_choice_muted', 'Muted (Grau)'),
    'uk-background-primary' => 'Primary',
    'uk-background-secondary' => 'Secondary',
];

// Background Farben für color_swatches
$backgroundColors = [
    '' => ['color' => 'transparent', 'label' => $_ci('cards_choice_none', 'Keine')],
    'uk-background-default' => ['color' => '#ffffff', 'label' => $_ci('cards_choice_default_white', 'Default (Weiß)')],
    'uk-background-muted' => ['color' => '#f8f8f8', 'label' => $_ci('cards_choice_muted', 'Muted (Grau)')],
    'uk-background-primary' => ['color' => '#1e87f0', 'label' => 'Primary'],
    'uk-background-secondary' => ['color' => '#222222', 'label' => 'Secondary'],
];

// Dynamische Farben aus Theme laden wenn verfügbar
if ($hasUikitThemeBuilder && class_exists('UikitThemeBuilder\DomainContext')) {
    // Card-Style Optionen aus Theme (bereits im richtigen Format für color_swatches)
    $themeCardStyles = \UikitThemeBuilder\DomainContext::getCardStyleOptions();
    if (!empty($themeCardStyles)) {
        $cardStyleColors = array_merge($cardStyleColors, $themeCardStyles);

        // Theme-Styles ergänzen, aber bestehende Kern-Labels nicht überschreiben
        foreach ($themeCardStyles as $class => $data) {
            if (!isset($cardStyleChoices[$class])) {
                $cardStyleChoices[$class] = $data['label'] ?? ucfirst(str_replace(['uk-card-', 'uk-background-'], '', $class));
            }
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
    'media-top' => $_ci('cards_choice_media_top', 'Medium oben'),
    'media-bottom' => $_ci('cards_choice_media_bottom', 'Medium unten'),
    'media-left' => $_ci('cards_choice_media_left', 'Medium links'),
    'media-right' => $_ci('cards_choice_media_right', 'Medium rechts'),
    'media-overlay' => $_ci('cards_choice_media_overlay', 'Overlay'),
];

// Layout-Icons für Selectpicker – externe SVG-Dateien aus assets/icons/
$_cbIconImg = function(string $name): string {
    if (class_exists(\KLXM\YFormContentBuilder\Svg::class)) {
        return \KLXM\YFormContentBuilder\Svg::iconImg($name);
    }
    return '';
};
$layoutIcons = [
    'media-top'     => $_cbIconImg('layout-media-top'),
    'media-bottom'  => $_cbIconImg('layout-media-bottom'),
    'media-left'    => $_cbIconImg('layout-media-left'),
    'media-right'   => $_cbIconImg('layout-media-right'),
    'media-overlay' => $_cbIconImg('layout-media-overlay'),
];

// Schatten-Icons für Selectpicker (visualisieren die Schattenstärke)
$shadowChoices = [
    '' => $_ci('cards_choice_no_shadow', 'Kein Schatten'),
    'uk-box-shadow-small' => $_ci('cards_choice_small', 'Klein'),
    'uk-box-shadow-medium' => $_ci('cards_choice_medium', 'Mittel'),
    'uk-box-shadow-large' => $_ci('cards_choice_large', 'Groß'),
    'uk-box-shadow-xlarge' => $_ci('cards_choice_extra_large', 'Extra Groß'),
    'uk-card-hover' => $_ci('cards_choice_hover_effect', 'Nur bei Hover'),
];

$shadowIcons = [
    ''                     => $_cbIconImg('shadow-none'),
    'uk-box-shadow-small'  => $_cbIconImg('shadow-small'),
    'uk-box-shadow-medium' => $_cbIconImg('shadow-medium'),
    'uk-box-shadow-large'  => $_cbIconImg('shadow-large'),
    'uk-box-shadow-xlarge' => $_cbIconImg('shadow-xlarge'),
    'uk-card-hover'        => $_cbIconImg('shadow-hover'),
];

// Vertical Align Icons – externe SVG-Dateien
$vAlignIcons = [
    ''       => $_cbIconImg('valign-top'),
    'middle' => $_cbIconImg('valign-middle'),
    'bottom' => $_cbIconImg('valign-bottom'),
];

// Card-Width Optionen – gleiche Werte für alle Breakpoints (Suffix wird im Template ergänzt)
$cardWidthChoicesBase = [
    ''      => $_ci('cards_choice_width_default', 'Standard (aus Grid)'),
    '1-1'   => $_ci('cards_choice_width_full', 'Vollbreite (100%)'),
    '3-4'   => $_ci('cards_choice_width_3_4', '3/4 Breite (75%)'),
    '2-3'   => $_ci('cards_choice_width_2_3', '2/3 Breite (66%)'),
    '1-2'   => $_ci('cards_choice_width_1_2', '1/2 Breite (50%)'),
    '1-3'   => $_ci('cards_choice_width_1_3', '1/3 Breite (33%)'),
    '1-4'   => $_ci('cards_choice_width_1_4', '1/4 Breite (25%)'),
];

// Optional: Layouts aus SVG-Klasse laden wenn verfügbar
if (class_exists(\KLXM\YFormContentBuilder\Svg::class)) {
    $rawLayouts = \KLXM\YFormContentBuilder\Svg::getLayoutOptions();
    if (!empty($rawLayouts)) {
        foreach ($rawLayouts as $key => $data) {
            // Bereits lokalisierte Standard-Labels nicht überschreiben
            if (!isset($layoutChoices[$key])) {
                $layoutChoices[$key] = $data['label'] ?? $key;
            }
        }
    }
}

return [
    'label' => $_ci('cards_element_label', 'Layout-Baukasten'),
    'icon' => 'fa fa-th-large',
    'description' => $_ci('cards_element_description', 'Karten-Grid mit flexiblen Layouts und Media-Optionen'),
    'version' => '1.13.0',
    'category' => 'content',
    
    // Settings Modal für Grid/Section-Einstellungen
    'settings_modal' => [
        'label' => $_ci('cards_settings_modal_label', 'Allgemeine Block-Einstellungen'),
        'icon' => 'fa-cog',
        'fields' => ['columns', 'columns_tablet', 'columns_mobile', 'gap', 'match_height', 'card_style', 'card_size', 'card_shadow', 'section_bg', 'section_bg_image', 'section_padding', 'container_width', 'animations_enabled', 'animations_scrollspy', 'animations_delay', 'animations_repeat', 'animations_cascading']
    ],
    
    'fields' => [
        // =============================================================================
        // GRID EINSTELLUNGEN
        // =============================================================================
        'columns' => [
            'type' => 'choice',
            'label' => $_ci('cards_field_columns', 'Spalten (Desktop)'),
            'choices' => [
                '1' => $_ci('cards_choice_1_col_full', '1 Spalte (100%)'),
                '2' => $_ci('cards_choice_2_cols', '2 Spalten'),
                '3' => $_ci('cards_choice_3_cols', '3 Spalten'),
                '4' => $_ci('cards_choice_4_cols', '4 Spalten'),
                '5' => $_ci('cards_choice_5_cols', '5 Spalten'),
                '6' => $_ci('cards_choice_6_cols', '6 Spalten')
            ],
            'default' => '3'
        ],
        'columns_tablet' => [
            'type' => 'choice',
            'label' => $_ci('cards_field_columns_tablet', 'Spalten (Tablet)'),
            'choices' => [
                '1' => $_ci('cards_choice_1_col', '1 Spalte'),
                '2' => $_ci('cards_choice_2_cols', '2 Spalten'),
                '3' => $_ci('cards_choice_3_cols', '3 Spalten'),
                '4' => $_ci('cards_choice_4_cols', '4 Spalten')
            ],
            'default' => '2'
        ],
        'columns_mobile' => [
            'type' => 'choice',
            'label' => $_ci('cards_field_columns_mobile', 'Spalten (Mobile)'),
            'choices' => [
                '1' => $_ci('cards_choice_1_col', '1 Spalte'),
                '2' => $_ci('cards_choice_2_cols', '2 Spalten')
            ],
            'default' => '1'
        ],
        'gap' => [
            'type' => 'choice',
            'label' => $_ci('cards_field_gap', 'Abstand zwischen Cards'),
            'choices' => [
                'collapse' => $_ci('cards_choice_no_gap_all', 'Kein Abstand (alle Richtungen)'),
                'column-collapse' => $_ci('cards_choice_no_gap_horizontal', 'Kein Abstand (links/rechts)'),
                'small' => $_ci('cards_choice_small', 'Klein') . ' (15px)',
                'medium' => $_ci('cards_choice_medium', 'Mittel') . ' (30px)',
                'large' => $_ci('cards_choice_large', 'Groß') . ' (40px)'
            ],
            'default' => 'medium'
        ],
        'match_height' => [
            'type' => 'checkbox',
            'label' => $_ci('cards_field_match_height', 'Gleiche Höhe für alle Cards')
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
            'default' => 'small'
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
            'label' => $_ci('cards_field_section_bg', 'Sektions-Hintergrund'),
            'choices' => $backgroundChoices,
            'default' => ''
        ],
        'section_bg_image' => [
            'type' => 'be_media',
            'label' => $_ci('cards_field_section_bg_image', 'Sektions-Hintergrund (Bild/Video)'),
            'notice' => $_ci('cards_field_section_bg_image_notice', 'Hintergrundbild oder -video (MP4, WebM). Video wird automatisch mit Autoplay und Loop abgespielt.')
        ],
        'section_padding' => [
            'type' => 'choice',
            'label' => $_ci('cards_field_section_padding', 'Sektions-Padding'),
            'choices' => [
                '' => $_ci('cards_choice_standard', 'Standard'),
                'uk-padding-remove' => $_ci('cards_choice_no_padding', 'Keine Füllung'),
                'uk-padding-small' => $_ci('cards_choice_small', 'Klein'),
                'uk-padding' => $_ci('cards_choice_medium', 'Mittel'),
                'uk-padding-large' => $_ci('cards_choice_large', 'Groß')
            ],
            'default' => ''
        ],
        'container_width' => [
            'type' => 'choice',
            'label' => $_ci('cards_field_container_width', 'Container-Breite'),
            'choices' => [
                'uk-container' => $_ci('cards_choice_standard', 'Standard'),
                'uk-container uk-container-xsmall' => $_ci('cards_choice_extra_narrow', 'Extra schmal'),
                'uk-container uk-container-small' => $_ci('cards_choice_narrow', 'Schmal'),
                'uk-container uk-container-large' => $_ci('cards_choice_wide', 'Weit'),
                'uk-container uk-container-xlarge' => $_ci('cards_choice_extra_wide', 'Extra weit'),
                'uk-container uk-container-expand' => $_ci('cards_choice_max_width', 'Maximale Breite'),
                '' => $_ci('cards_choice_full_width', 'Volle Breite (kein Container)')
            ],
            'default' => 'uk-container'
        ],
        
        // =============================================================================
        // GLOBALE ANIMATION EINSTELLUNGEN (UIkit)
        // =============================================================================
        'animations_enabled' => [
            'type' => 'checkbox',
            'label' => $_ci('cards_field_animations_enabled', 'Animationen aktivieren (UIkit)'),
            'notice' => $_ci('cards_field_animations_enabled_notice', 'Aktiviert ScrollSpy-Animationen beim Scrollen (nur UIkit)')
        ],
        'animations_scrollspy' => [
            'type' => 'checkbox',
            'label' => $_ci('cards_field_animations_scrollspy', 'ScrollSpy aktivieren'),
            'notice' => $_ci('cards_field_animations_scrollspy_notice', 'Animationen starten wenn Cards in den Viewport kommen (statt sofort). Gilt für alle Cards.')
        ],
        'animations_delay' => [
            'type' => 'text',
            'label' => $_ci('cards_field_animations_delay', 'Animationsverzögerung (ms)'),
            'notice' => $_ci('cards_field_animations_delay_notice', 'Verzögerung zwischen Card-Animationen. Standardwert: 100'),
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
            'label' => $_ci('cards_items_label', 'Cards'),
            
            // Modal für Medieneinstellungen (wird nach image-Feld angezeigt)
            'media_modal' => [
                'label' => $_ci('cards_media_modal_label', 'Medieneinstellungen'),
                'icon' => 'fa-sliders',
                'trigger_after' => 'image',
                'fields' => [
                    'image_alt', 'image_decorative', 'image_title', 'media_width', 'media_ratio',
                    'video_display', 'video_controls', 'media_lightbox', 'media_cover'
                ]
            ],

            // Modal: Individuelle Layout-Einstellungen
            'item_modal' => [
                'label' => $_ci('cards_item_modal_label', 'Layout-Einstellungen'),
                'icon' => 'fa-cog',
                'trigger_after' => 'layout',
                'fields' => [
                    'card_width_mobile', 'card_width_tablet', 'card_width',
                    'badge', 'badge_color', 'media_vertical_align',
                    'card_shadow_override',
                ]
            ],

            // Modal: Verlinkung
            'link_modal' => [
                'label' => $_ci('cards_link_modal_label', 'Verlinkung'),
                'icon' => 'fa-link',
                'trigger_after' => 'card_style_override',
                'fields' => [
                    'link_type', 'link_url', 'link_internal', 'link_text', 'link_button_style', 'link_button_align', 'link_card'
                ]
            ],
            
            // Modal für Extra-Felder (nur wenn Extra-Felder vorhanden sind)
            ...(!empty($extra) ? [
                'extras_modal' => [
                    'label' => $_ci('cards_extras_modal_label', 'Extras'),
                    'icon' => 'fa-star',
                    'trigger_after' => 'title',
                    'fields' => array_keys($extra)
                ]
            ] : []),
            
            'fields' => [
                // Zeile 1: Layout / Farbe / Animation nebeneinander
                'layout' => [
                    'type' => 'choice',
                    'label' => $_ci('cards_field_layout', 'Layout'),
                    'choices' => $layoutChoices,
                    'choice_icons' => $layoutIcons,
                    'selectpicker' => true,
                    'default' => 'media-top',
                    'col' => 4,
                ],
                'card_style_override' => [
                    'type' => 'choice',
                    'label' => $_ci('cards_field_card_style_override', 'Karten-Farbe'),
                    'choices' => array_merge(['' => $_ci('cards_choice_inherited', 'Geerbt (Globale Einstellung)')], $cardStyleChoices),
                    'choice_colors' => array_merge(['' => ['color' => 'transparent', 'label' => $_ci('cards_choice_inherited_short', 'Geerbt')]], $cardStyleColors),
                    'selectpicker' => true,
                    'notice' => $_ci('cards_field_card_style_override_notice', 'Leer = Farbe aus den Block-Einstellungen übernehmen. Auswahl = individuelle Farbe für diese Karte.'),
                    'default' => '',
                    'col' => 4,
                ],
                'animation' => [
                    'type' => 'choice',
                    'label' => $_ci('cards_field_animation', 'Animation'),
                    'choices' => [
                        '' => $_ci('cards_choice_none', 'Keine'),
                        'uk-animation-fade' => $_ci('cards_choice_fade_in', 'Fade In'),
                        'uk-animation-scale-up' => $_ci('cards_choice_scale_up', 'Scale Up'),
                        'uk-animation-scale-down' => $_ci('cards_choice_scale_down', 'Scale Down'),
                        'uk-animation-slide-top-small' => $_ci('cards_choice_slide_top', 'Slide von oben'),
                        'uk-animation-slide-bottom-small' => $_ci('cards_choice_slide_bottom', 'Slide von unten'),
                        'uk-animation-slide-left-small' => $_ci('cards_choice_slide_left', 'Slide von links'),
                        'uk-animation-slide-right-small' => $_ci('cards_choice_slide_right', 'Slide von rechts'),
                    ],
                    'default' => '',
                    'col' => 4,
                ],
                
                // Zeile 2: Individuelle Breite (Mobil / Tablet / Desktop) → jetzt im Layout-Modal
                'card_width_mobile' => [
                    'type' => 'choice',
                    'label' => 'Breite Mobil',
                    'choices' => $cardWidthChoicesBase,
                    'default' => '',
                    'col' => 4,
                ],
                'card_width_tablet' => [
                    'type' => 'choice',
                    'label' => 'Breite Tablet',
                    'choices' => $cardWidthChoicesBase,
                    'default' => '',
                    'col' => 4,
                ],
                'card_width' => [
                    'type' => 'choice',
                    'label' => 'Breite Desktop',
                    'choices' => $cardWidthChoicesBase,
                    'default' => '',
                    'col' => 4,
                ],
                
                // Media - volle Breite
                'image' => [
                    'type' => 'be_media',
                    'label' => $_ci('cards_field_image', 'Bild oder Video'),
                    'preview' => true,
                    'notice' => $_ci('cards_field_image_notice', 'Bilder und Videos werden unterstützt')
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
                'media_ratio' => [
                    'type' => 'choice',
                    'label' => 'Seitenverhältnis',
                    'choices' => [
                        '16-9' => '16:9 (Standard)',
                        '21-9' => '21:9 (Cinema)',
                        '4-3' => '4:3',
                        '1-1' => '1:1',
                        'original' => 'Original (kein Crop)'
                    ],
                    'default' => '16-9'
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
                
                // Content - Titel und Untertitel nebeneinander (2 Spalten)
                'title' => [
                    'type' => 'text',
                    'label' => $_ci('cards_field_title', 'Titel'),
                    'col' => 6,
                ],
                'subtitle' => [
                    'type' => 'text',
                    'label' => $_ci('cards_field_subtitle', 'Untertitel'),
                    'notice' => $_ci('cards_field_subtitle_notice', 'Optional'),
                    'col' => 6,
                ],
                'text' => [
                    'type' => 'tinymce',
                    'profile' => 'default',
                    'label' => $_ci('cards_field_text', 'Text')
                ],
                
                // Erweiterte Optionen (im Modal)
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
                'media_vertical_align' => [
                    'type' => 'choice',
                    'label' => 'Vertikale Ausrichtung (Horizontal-Layout)',
                    'choices' => [
                        '' => 'Oben',
                        'middle' => 'Mittig',
                        'bottom' => 'Unten'
                    ],
                    'choice_icons' => $vAlignIcons,
                    'selectpicker' => true,
                    'default' => '',
                    'notice' => 'Greift nur bei Layouts mit Bild links oder rechts'
                ],
                
                // Card-spezifische Überschreibungen (jetzt im Modal)
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
                    'label' => $_ci('cards_field_link_type', 'Link'),
                    'choices' => [
                        '' => $_ci('cards_choice_no_link', 'Kein Link'),
                        'external' => $_ci('cards_choice_external_url', 'Externe URL'),
                        'internal' => $_ci('cards_choice_internal_page', 'Interne Seite')
                    ],
                    'default' => ''
                ],
                'link_url' => [
                    'type' => 'text',
                    'label' => $_ci('cards_field_link_url', 'Externe URL'),
                    'visible_if' => [
                        'link_type' => 'external',
                    ],
                ],
                'link_internal' => [
                    'type' => 'be_link',
                    'label' => $_ci('cards_field_link_internal', 'Interne Seite'),
                    'visible_if' => [
                        'link_type' => 'internal',
                    ],
                ],
                'link_text' => [
                    'type' => 'text',
                    'label' => $_ci('cards_field_link_text', 'Link Text'),
                    'default' => $_ci('cards_default_link_text', 'Mehr erfahren'),
                    'col' => 6,
                    'visible_if' => [
                        'link_type' => ['external', 'internal'],
                    ],
                ],
                'link_button_style' => [
                    'type' => 'choice',
                    'label' => 'Button-Stil',
                    'choices' => [
                        'uk-button-text'      => 'Text (mit Pfeil)',
                        'uk-button-default'   => 'Standard',
                        'uk-button-primary'   => 'Primary',
                        'uk-button-secondary' => 'Secondary',
                        'uk-button-danger'    => 'Danger',
                    ],
                    'default' => 'uk-button-text',
                    'col' => 6,
                    'visible_if' => [
                        'link_type' => ['external', 'internal'],
                    ],
                ],
                'link_button_align' => [
                    'type' => 'choice',
                    'label' => $_ci('cards_field_link_button_align', 'Button-Ausrichtung'),
                    'choices' => [
                        ''                => $_ci('cards_choice_left', 'Links'),
                        'uk-text-center'  => $_ci('cards_choice_center', 'Zentriert'),
                        'uk-text-right'   => $_ci('cards_choice_right', 'Rechts'),
                    ],
                    'default' => 'uk-text-center',
                    'visible_if' => [
                        'link_type' => ['external', 'internal'],
                    ],
                ],
                'link_card' => [
                    'type' => 'checkbox',
                    'label' => $_ci('cards_field_link_card', 'Gesamte Card verlinken'),
                    'visible_if' => [
                        'link_type' => ['external', 'internal'],
                    ],
                ],
                
                // Extra-Felder (im Modal "Extras" sichtbar)
                ...$extra
            ]
        ]
    ],
];
