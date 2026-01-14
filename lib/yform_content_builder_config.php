<?php

/**
 * YForm Content Builder - Zentrale Konfiguration
 * 
 * Stellt gemeinsame Felder und Optionen für alle Elemente bereit.
 * Ermöglicht konsistente Section-Einstellungen über alle Elemente hinweg.
 */
class yform_content_builder_config
{
    private static ?bool $hasUikitThemeBuilder = null;
    private static ?array $themeChoices = null;
    private static ?array $backgroundOptions = null;
    private static ?array $backgroundColors = null;
    private static ?array $paddingOptions = null;
    private static ?array $containerOptions = null;
    private static ?array $shadowOptions = null;
    private static ?array $shadowIcons = null;
    
    /**
     * Prüft ob UIkit Theme Builder verfügbar ist
     */
    public static function hasThemeBuilder(): bool
    {
        if (self::$hasUikitThemeBuilder === null) {
            self::$hasUikitThemeBuilder = rex_addon::get('uikit_theme_builder')->isAvailable() 
                && class_exists('UikitThemeBuilder\DomainContext');
        }
        return self::$hasUikitThemeBuilder;
    }
    
    /**
     * Liefert die Theme-Auswahl Optionen
     */
    public static function getThemeChoices(): array
    {
        if (self::$themeChoices === null) {
            self::$themeChoices = ['' => '-- Automatisch (Domain) --'];
            if (self::hasThemeBuilder()) {
                $availableThemes = \UikitThemeBuilder\DomainContext::getAvailableThemes();
                self::$themeChoices = array_merge(self::$themeChoices, $availableThemes);
            }
        }
        return self::$themeChoices;
    }
    
    /**
     * Liefert die Hintergrund-Optionen (für choice)
     */
    public static function getBackgroundChoices(): array
    {
        if (self::$backgroundOptions === null) {
            self::$backgroundOptions = [
                '' => 'Keine',
                'uk-background-default' => 'Default (Weiß)',
                'uk-background-muted' => 'Muted (Grau)',
                'uk-background-primary' => 'Primary',
                'uk-background-secondary' => 'Secondary'
            ];
            
            if (self::hasThemeBuilder()) {
                $themeBackgrounds = \UikitThemeBuilder\DomainContext::getBackgroundOptions();
                if (!empty($themeBackgrounds)) {
                    self::$backgroundOptions = ['' => 'Keine'];
                    foreach ($themeBackgrounds as $class => $data) {
                        self::$backgroundOptions[$class] = $data['label'] ?? ucfirst(str_replace('uk-background-', '', $class));
                    }
                }
            }
        }
        return self::$backgroundOptions;
    }
    
    /**
     * Liefert die Hintergrund-Farben (für color_swatches)
     */
    public static function getBackgroundColors(): array
    {
        if (self::$backgroundColors === null) {
            self::$backgroundColors = [
                '' => ['color' => 'transparent', 'label' => 'Keine'],
                'uk-background-default' => ['color' => '#ffffff', 'label' => 'Default (Weiß)'],
                'uk-background-muted' => ['color' => '#f8f8f8', 'label' => 'Muted (Grau)'],
                'uk-background-primary' => ['color' => '#1e87f0', 'label' => 'Primary'],
                'uk-background-secondary' => ['color' => '#222222', 'label' => 'Secondary']
            ];
            
            if (self::hasThemeBuilder()) {
                $themeBackgrounds = \UikitThemeBuilder\DomainContext::getBackgroundOptions();
                if (!empty($themeBackgrounds)) {
                    self::$backgroundColors = ['' => ['color' => 'transparent', 'label' => 'Keine']];
                    foreach ($themeBackgrounds as $class => $data) {
                        self::$backgroundColors[$class] = $data;
                    }
                }
            }
        }
        return self::$backgroundColors;
    }
    
    /**
     * Liefert die Padding-Optionen
     */
    public static function getPaddingOptions(): array
    {
        if (self::$paddingOptions === null) {
            self::$paddingOptions = [
                '' => 'Standard',
                'uk-padding-remove' => 'Keine Füllung',
                'uk-padding-small' => 'Klein',
                'uk-padding' => 'Mittel',
                'uk-padding-large' => 'Groß'
            ];
        }
        return self::$paddingOptions;
    }
    
    /**
     * Liefert die Container-Breiten Optionen
     */
    public static function getContainerOptions(): array
    {
        if (self::$containerOptions === null) {
            self::$containerOptions = [
                'uk-container' => 'Standard',
                'uk-container uk-container-xsmall' => 'Extra schmal',
                'uk-container uk-container-small' => 'Schmal',
                'uk-container uk-container-large' => 'Weit',
                'uk-container uk-container-xlarge' => 'Extra weit',
                'uk-container uk-container-expand' => 'Maximale Breite',
                '' => 'Volle Breite (kein Container)'
            ];
        }
        return self::$containerOptions;
    }
    
    /**
     * Liefert die Schatten-Optionen
     */
    public static function getShadowOptions(): array
    {
        if (self::$shadowOptions === null) {
            self::$shadowOptions = [
                '' => 'Kein Schatten',
                'uk-box-shadow-small' => 'Klein',
                'uk-box-shadow-medium' => 'Mittel',
                'uk-box-shadow-large' => 'Groß',
                'uk-box-shadow-xlarge' => 'Extra Groß',
                'uk-card-hover' => 'Nur bei Hover'
            ];
        }
        return self::$shadowOptions;
    }
    
    /**
     * Liefert die Schatten-Icons für Selectpicker
     */
    public static function getShadowIcons(): array
    {
        if (self::$shadowIcons === null) {
            self::$shadowIcons = [
                '' => '<svg width="24" height="18" viewBox="0 0 24 18" style="vertical-align:middle;margin-right:6px;"><rect x="2" y="2" width="20" height="14" fill="#fff" stroke="#ccc" rx="2"/></svg>',
                'uk-box-shadow-small' => '<svg width="24" height="18" viewBox="0 0 24 18" style="vertical-align:middle;margin-right:6px;"><rect x="3" y="3" width="20" height="14" fill="#e0e0e0" rx="2"/><rect x="1" y="1" width="20" height="14" fill="#fff" stroke="#ccc" rx="2"/></svg>',
                'uk-box-shadow-medium' => '<svg width="24" height="18" viewBox="0 0 24 18" style="vertical-align:middle;margin-right:6px;"><rect x="4" y="4" width="20" height="14" fill="#ccc" rx="2"/><rect x="1" y="1" width="20" height="14" fill="#fff" stroke="#bbb" rx="2"/></svg>',
                'uk-box-shadow-large' => '<svg width="24" height="18" viewBox="0 0 24 18" style="vertical-align:middle;margin-right:6px;"><rect x="5" y="5" width="20" height="14" fill="#aaa" rx="2"/><rect x="1" y="1" width="20" height="14" fill="#fff" stroke="#999" rx="2"/></svg>',
                'uk-box-shadow-xlarge' => '<svg width="24" height="18" viewBox="0 0 24 18" style="vertical-align:middle;margin-right:6px;"><rect x="6" y="6" width="20" height="14" fill="#888" rx="2"/><rect x="1" y="1" width="20" height="14" fill="#fff" stroke="#777" rx="2"/></svg>',
                'uk-card-hover' => '<svg width="24" height="18" viewBox="0 0 24 18" style="vertical-align:middle;margin-right:6px;"><rect x="2" y="2" width="20" height="14" fill="#fff" stroke="#ccc" rx="2" stroke-dasharray="2,2"/><text x="12" y="12" font-size="8" text-anchor="middle" fill="#999">↑</text></svg>'
            ];
        }
        return self::$shadowIcons;
    }
    
    /**
     * Liefert die Standard Section-Felder
     * Können in jedem Element verwendet werden
     */
    public static function getSectionFields(): array
    {
        return [
            'section_bg' => [
                'type' => 'choice',
                'label' => 'Sektions-Hintergrund',
                'choices' => self::getBackgroundChoices(),
                'choice_colors' => self::getBackgroundColors(),
                'selectpicker' => true,
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
                'choices' => self::getPaddingOptions(),
                'default' => ''
            ],
            'container_width' => [
                'type' => 'choice',
                'label' => 'Container-Breite',
                'choices' => self::getContainerOptions(),
                'default' => 'uk-container'
            ]
        ];
    }
    
    /**
     * Liefert die Section-Feldnamen für settings_modal
     */
    public static function getSectionFieldNames(): array
    {
        return ['section_bg', 'section_bg_image', 'section_padding', 'container_width'];
    }
    
    /**
     * Liefert Standard Grid-Spalten Optionen
     */
    public static function getColumnOptions(string $device = 'desktop'): array
    {
        switch ($device) {
            case 'mobile':
                return [
                    '1' => '1 Spalte',
                    '2' => '2 Spalten'
                ];
            case 'tablet':
                return [
                    '1' => '1 Spalte',
                    '2' => '2 Spalten',
                    '3' => '3 Spalten',
                    '4' => '4 Spalten'
                ];
            default: // desktop
                return [
                    '1' => '1 Spalte (100%)',
                    '2' => '2 Spalten',
                    '3' => '3 Spalten',
                    '4' => '4 Spalten',
                    '5' => '5 Spalten',
                    '6' => '6 Spalten'
                ];
        }
    }
    
    /**
     * Liefert Grid-Gap Optionen
     */
    public static function getGapOptions(): array
    {
        return [
            'collapse' => 'Kein Abstand',
            'small' => 'Klein (15px)',
            'medium' => 'Mittel (30px)',
            'large' => 'Groß (40px)'
        ];
    }
    
    /**
     * Liefert die Standard Grid-Felder für Repeater-Elemente
     */
    public static function getGridFields(): array
    {
        return [
            'columns' => [
                'type' => 'choice',
                'label' => 'Spalten (Desktop)',
                'choices' => self::getColumnOptions('desktop'),
                'default' => '3'
            ],
            'columns_tablet' => [
                'type' => 'choice',
                'label' => 'Spalten (Tablet)',
                'choices' => self::getColumnOptions('tablet'),
                'default' => '2'
            ],
            'columns_mobile' => [
                'type' => 'choice',
                'label' => 'Spalten (Mobile)',
                'choices' => self::getColumnOptions('mobile'),
                'default' => '1'
            ],
            'gap' => [
                'type' => 'choice',
                'label' => 'Abstand zwischen Elementen',
                'choices' => self::getGapOptions(),
                'default' => 'medium'
            ]
        ];
    }
    
    /**
     * Liefert die Grid-Feldnamen für settings_modal
     */
    public static function getGridFieldNames(): array
    {
        return ['columns', 'columns_tablet', 'columns_mobile', 'gap'];
    }
    
    /**
     * Kombiniert Section- und Grid-Felder
     * Praktisch für settings_modal arrays
     */
    public static function getSettingsModalFields(array $additionalFields = []): array
    {
        $fields = [];
        
        // Grid-Felder
        $fields = array_merge($fields, self::getGridFieldNames());
        
        // Zusätzliche Element-spezifische Felder
        $fields = array_merge($fields, $additionalFields);
        
        // Section-Felder am Ende
        $fields = array_merge($fields, self::getSectionFieldNames());
        
        return $fields;
    }
    
    /**
     * Liefert alle Standard-Konfigurationsfelder
     * Kombiniert Grid und Section Felder
     * Theme wird auf YForm-Feldebene definiert
     */
    public static function getStandardFields(array $additionalFields = []): array
    {
        $fields = [];
        
        // Grid-Felder
        $fields = array_merge($fields, self::getGridFields());
        
        // Zusätzliche Felder
        $fields = array_merge($fields, $additionalFields);
        
        // Section-Felder
        $fields = array_merge($fields, self::getSectionFields());
        
        return $fields;
    }
    
    /**
     * Liefert Link-Felder für Cards, Downloads etc.
     */
    public static function getLinkFields(): array
    {
        return [
            'link_type' => [
                'type' => 'choice',
                'label' => 'Link',
                'choices' => [
                    '' => 'Kein Link',
                    'external' => 'Externe URL',
                    'internal' => 'Interne Seite',
                    'download' => 'Download'
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
            'link_target' => [
                'type' => 'choice',
                'label' => 'Link öffnen in',
                'choices' => [
                    '' => 'Gleiches Fenster',
                    '_blank' => 'Neues Fenster/Tab'
                ],
                'default' => ''
            ]
        ];
    }
    
    /**
     * Liefert die Link-Feldnamen
     */
    public static function getLinkFieldNames(): array
    {
        return ['link_type', 'link_url', 'link_internal', 'link_text', 'link_target'];
    }
}
