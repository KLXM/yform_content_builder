# Widget System - Beispiele

Dieses Dokument zeigt praktische Beispiele für die Verwendung des Widget-Systems.

## Beispiel 1: Öffnungszeiten-Widget

Ein Widget, das strukturierte Öffnungszeiten zu Elementen hinzufügt.

```php
<?php

namespace FriendsOfREDAXO\YFormContentBuilder\Widgets;

/**
 * Öffnungszeiten Widget
 */
class OpeningHoursWidget extends ContentBuilderWidgetAbstract
{
    public static function getType(): string
    {
        return 'opening_hours';
    }
    
    public static function getLabel(): string
    {
        return 'Öffnungszeiten';
    }
    
    public static function getDescription(): string
    {
        return 'Fügt strukturierte Öffnungszeiten hinzu (Mo-So mit Zeiten)';
    }
    
    public function getFields(): array
    {
        return [
            'hours_monday' => [
                'type' => 'text',
                'label' => 'Montag',
                'notice' => 'z.B. 9:00 - 18:00 oder "Geschlossen"'
            ],
            'hours_tuesday' => [
                'type' => 'text',
                'label' => 'Dienstag'
            ],
            'hours_wednesday' => [
                'type' => 'text',
                'label' => 'Mittwoch'
            ],
            'hours_thursday' => [
                'type' => 'text',
                'label' => 'Donnerstag'
            ],
            'hours_friday' => [
                'type' => 'text',
                'label' => 'Freitag'
            ],
            'hours_saturday' => [
                'type' => 'text',
                'label' => 'Samstag'
            ],
            'hours_sunday' => [
                'type' => 'text',
                'label' => 'Sonntag'
            ],
            'hours_show' => [
                'type' => 'checkbox',
                'label' => 'Öffnungszeiten anzeigen'
            ]
        ];
    }
    
    public function getHookName(): string
    {
        return 'after_content';
    }
    
    public function render(array $widgetData, string $framework = 'bootstrap'): string
    {
        if (!($widgetData['hours_show'] ?? false)) {
            return '';
        }
        
        $days = [
            'monday' => 'Montag',
            'tuesday' => 'Dienstag',
            'wednesday' => 'Mittwoch',
            'thursday' => 'Donnerstag',
            'friday' => 'Freitag',
            'saturday' => 'Samstag',
            'sunday' => 'Sonntag'
        ];
        
        $output = '';
        
        switch ($framework) {
            case 'uikit':
                $output .= '<div class="uk-card uk-card-default uk-card-body uk-margin">';
                $output .= '<h3 class="uk-card-title">Öffnungszeiten</h3>';
                $output .= '<dl class="uk-description-list">';
                
                foreach ($days as $key => $label) {
                    $hours = $widgetData['hours_' . $key] ?? '';
                    if (!empty($hours)) {
                        $output .= '<dt>' . $this->escape($label) . '</dt>';
                        $output .= '<dd>' . $this->escape($hours) . '</dd>';
                    }
                }
                
                $output .= '</dl>';
                $output .= '</div>';
                break;
                
            case 'bootstrap':
            default:
                $output .= '<div class="panel panel-default">';
                $output .= '<div class="panel-heading"><h3 class="panel-title">Öffnungszeiten</h3></div>';
                $output .= '<div class="panel-body">';
                $output .= '<dl class="dl-horizontal">';
                
                foreach ($days as $key => $label) {
                    $hours = $widgetData['hours_' . $key] ?? '';
                    if (!empty($hours)) {
                        $output .= '<dt>' . $this->escape($label) . '</dt>';
                        $output .= '<dd>' . $this->escape($hours) . '</dd>';
                    }
                }
                
                $output .= '</dl>';
                $output .= '</div>';
                $output .= '</div>';
                break;
        }
        
        return $output;
    }
}
```

## Beispiel 2: Kontakt-Picker Widget (mit YForm-Integration)

Ein Widget, das einen Kontakt aus einer YForm-Tabelle auswählt und dessen Daten anzeigt.

```php
<?php

namespace FriendsOfREDAXO\YFormContentBuilder\Widgets;

/**
 * Kontakt-Picker Widget
 */
class ContactPickerWidget extends ContentBuilderWidgetAbstract
{
    public static function getType(): string
    {
        return 'contact_picker';
    }
    
    public static function getLabel(): string
    {
        return 'Kontakt-Picker';
    }
    
    public static function getDescription(): string
    {
        return 'Wählt einen Kontakt aus der YForm-Kontakte-Tabelle aus und zeigt dessen Daten an.';
    }
    
    public function getFields(): array
    {
        return [
            'contact_id' => [
                'type' => 'select',
                'label' => 'Kontakt auswählen',
                'choices' => $this->getContactChoices(),
                'default' => ''
            ],
            'contact_fields' => [
                'type' => 'checkbox',
                'label' => 'Anzuzeigende Felder',
                'notice' => 'Welche Kontakt-Felder sollen angezeigt werden?',
                // Hier könnte man Checkboxen für einzelne Felder anbieten
            ],
            'contact_show' => [
                'type' => 'checkbox',
                'label' => 'Kontakt anzeigen'
            ]
        ];
    }
    
    public function getHookName(): string
    {
        return 'after_content';
    }
    
    public function render(array $widgetData, string $framework = 'bootstrap'): string
    {
        $contactId = $widgetData['contact_id'] ?? '';
        $showContact = $widgetData['contact_show'] ?? false;
        
        if (!$showContact || empty($contactId)) {
            return '';
        }
        
        // Kontakt aus YForm-Tabelle laden
        $contact = \rex_yform_manager_dataset::get($contactId, 'rex_contacts');
        
        if (!$contact) {
            return '<!-- Kontakt nicht gefunden -->';
        }
        
        $output = '';
        
        switch ($framework) {
            case 'uikit':
                $output .= '<div class="uk-card uk-card-default uk-card-body uk-margin">';
                $output .= '<div class="uk-grid-small" uk-grid>';
                
                // Bild (falls vorhanden)
                if ($contact->getValue('image')) {
                    $output .= '<div class="uk-width-auto">';
                    $output .= '<img src="' . \rex_url::media($contact->getValue('image')) . '" class="uk-border-circle" width="80" height="80" alt="' . $this->escape($contact->getValue('name')) . '">';
                    $output .= '</div>';
                }
                
                $output .= '<div class="uk-width-expand">';
                $output .= '<h3 class="uk-card-title uk-margin-remove-bottom">' . $this->escape($contact->getValue('name')) . '</h3>';
                
                if ($contact->getValue('position')) {
                    $output .= '<p class="uk-text-meta uk-margin-remove-top">' . $this->escape($contact->getValue('position')) . '</p>';
                }
                
                if ($contact->getValue('email')) {
                    $output .= '<p><a href="mailto:' . $this->escape($contact->getValue('email')) . '">' . $this->escape($contact->getValue('email')) . '</a></p>';
                }
                
                if ($contact->getValue('phone')) {
                    $output .= '<p><a href="tel:' . $this->escape($contact->getValue('phone')) . '">' . $this->escape($contact->getValue('phone')) . '</a></p>';
                }
                
                $output .= '</div>';
                $output .= '</div>';
                $output .= '</div>';
                break;
                
            case 'bootstrap':
            default:
                $output .= '<div class="panel panel-default">';
                $output .= '<div class="panel-body">';
                $output .= '<div class="media">';
                
                // Bild (falls vorhanden)
                if ($contact->getValue('image')) {
                    $output .= '<div class="media-left">';
                    $output .= '<img src="' . \rex_url::media($contact->getValue('image')) . '" class="media-object img-circle" style="width:80px;" alt="' . $this->escape($contact->getValue('name')) . '">';
                    $output .= '</div>';
                }
                
                $output .= '<div class="media-body">';
                $output .= '<h4 class="media-heading">' . $this->escape($contact->getValue('name')) . '</h4>';
                
                if ($contact->getValue('position')) {
                    $output .= '<p class="text-muted">' . $this->escape($contact->getValue('position')) . '</p>';
                }
                
                if ($contact->getValue('email')) {
                    $output .= '<p><a href="mailto:' . $this->escape($contact->getValue('email')) . '"><i class="fa fa-envelope"></i> ' . $this->escape($contact->getValue('email')) . '</a></p>';
                }
                
                if ($contact->getValue('phone')) {
                    $output .= '<p><a href="tel:' . $this->escape($contact->getValue('phone')) . '"><i class="fa fa-phone"></i> ' . $this->escape($contact->getValue('phone')) . '</a></p>';
                }
                
                $output .= '</div>';
                $output .= '</div>';
                $output .= '</div>';
                $output .= '</div>';
                break;
        }
        
        return $output;
    }
    
    /**
     * Hilfsfunktion: Kontakt-Optionen für Select
     */
    private function getContactChoices(): array
    {
        $choices = ['' => '-- Bitte wählen --'];
        
        // Prüfe ob Tabelle existiert
        if (!\rex_yform_manager_table::get('rex_contacts')) {
            return $choices;
        }
        
        $contacts = \rex_yform_manager_dataset::query('rex_contacts')
            ->orderBy('name', 'ASC')
            ->find();
        
        foreach ($contacts as $contact) {
            $choices[$contact->getId()] = $contact->getValue('name');
        }
        
        return $choices;
    }
}
```

## Beispiel 3: Bewertungs-Widget

Ein Widget für Sterne-Bewertungen.

```php
<?php

namespace FriendsOfREDAXO\YFormContentBuilder\Widgets;

/**
 * Bewertungs-Widget
 */
class RatingWidget extends ContentBuilderWidgetAbstract
{
    public static function getType(): string
    {
        return 'rating';
    }
    
    public static function getLabel(): string
    {
        return 'Bewertung';
    }
    
    public static function getDescription(): string
    {
        return 'Fügt eine Sterne-Bewertung (1-5 Sterne) hinzu.';
    }
    
    public function getFields(): array
    {
        return [
            'rating_stars' => [
                'type' => 'select',
                'label' => 'Bewertung',
                'choices' => [
                    '' => '-- Keine Bewertung --',
                    '1' => '★ (1 von 5)',
                    '2' => '★★ (2 von 5)',
                    '3' => '★★★ (3 von 5)',
                    '4' => '★★★★ (4 von 5)',
                    '5' => '★★★★★ (5 von 5)'
                ],
                'default' => ''
            ],
            'rating_text' => [
                'type' => 'text',
                'label' => 'Bewertungs-Text',
                'notice' => 'z.B. "Ausgezeichnet", "Sehr gut"'
            ],
            'rating_show' => [
                'type' => 'checkbox',
                'label' => 'Bewertung anzeigen'
            ]
        ];
    }
    
    public function getHookName(): string
    {
        return 'after_content';
    }
    
    public function render(array $widgetData, string $framework = 'bootstrap'): string
    {
        $stars = (int)($widgetData['rating_stars'] ?? 0);
        $text = $widgetData['rating_text'] ?? '';
        $show = $widgetData['rating_show'] ?? false;
        
        if (!$show || $stars < 1) {
            return '';
        }
        
        // Schema.org JSON-LD für SEO
        $jsonLd = [
            '@type' => 'Rating',
            'ratingValue' => $stars,
            'bestRating' => 5,
            'worstRating' => 1
        ];
        
        $output = '<script type="application/ld+json">' . json_encode($jsonLd) . '</script>';
        
        switch ($framework) {
            case 'uikit':
                $output .= '<div class="uk-margin rating-widget">';
                $output .= '<div class="uk-flex uk-flex-middle">';
                
                // Sterne
                for ($i = 1; $i <= 5; $i++) {
                    if ($i <= $stars) {
                        $output .= '<span class="uk-text-warning">★</span>';
                    } else {
                        $output .= '<span class="uk-text-muted">☆</span>';
                    }
                }
                
                // Text
                if (!empty($text)) {
                    $output .= '<span class="uk-margin-small-left">' . $this->escape($text) . '</span>';
                }
                
                $output .= '</div>';
                $output .= '</div>';
                break;
                
            case 'bootstrap':
            default:
                $output .= '<div class="rating-widget">';
                
                // Sterne
                for ($i = 1; $i <= 5; $i++) {
                    if ($i <= $stars) {
                        $output .= '<span class="text-warning">★</span>';
                    } else {
                        $output .= '<span class="text-muted">☆</span>';
                    }
                }
                
                // Text
                if (!empty($text)) {
                    $output .= ' <span class="text-muted">' . $this->escape($text) . '</span>';
                }
                
                $output .= '</div>';
                break;
        }
        
        return $output;
    }
}
```

## Widget registrieren (boot.php)

```php
<?php

// In der boot.php deines AddOns/Projekts

use FriendsOfREDAXO\YFormContentBuilder\Widgets\ContentBuilderWidgetRegistry;

// Widgets registrieren
rex_extension::register('PACKAGES_INCLUDED', function() {
    // Öffnungszeiten
    ContentBuilderWidgetRegistry::register(new \YourNamespace\Widgets\OpeningHoursWidget());
    
    // Kontakt-Picker
    ContentBuilderWidgetRegistry::register(new \YourNamespace\Widgets\ContactPickerWidget());
    
    // Bewertung
    ContentBuilderWidgetRegistry::register(new \YourNamespace\Widgets\RatingWidget());
});
```

## Widget-Hooks in Element-Config definieren

```php
<?php

// In der config.php eines Elements

return [
    'label' => 'Team-Mitglied',
    'icon' => 'fa-user',
    
    // Widget-Hooks definieren (optional)
    // Wenn nicht definiert, wird 'after_content' verwendet
    'widget_hooks' => ['after_content', 'in_sidebar'],
    
    'fields' => [
        'name' => ['type' => 'text', 'label' => 'Name'],
        'position' => ['type' => 'text', 'label' => 'Position'],
        'bio' => ['type' => 'cke5', 'label' => 'Biografie']
    ]
];
```

Mit dieser Konfiguration werden Widgets, die den Hook `after_content` oder `in_sidebar` verwenden, automatisch zu diesem Element hinzugefügt.
