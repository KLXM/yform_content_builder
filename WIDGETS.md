# Widget System - Erweiterung von Elementen

Das Widget-System ermöglicht es, bestehende Content Builder Elemente um zusätzliche Felder und Ausgaben zu erweitern, ohne die Element-Konfiguration direkt zu ändern.

## 📚 Konzept

**Widgets** sind kleine, wiederverwendbare Erweiterungen, die:
- Zusätzliche Felder zu Elementen hinzufügen
- Ihre eigene Frontend-Ausgabe haben
- Von externen AddOns registriert werden können
- In den Einstellungen aktiviert/deaktiviert werden können

## 🎯 Anwendungsfälle

- **Datum-Feld**: Datum zu Events, Artikeln hinzufügen
- **Social Media Links**: Facebook, Instagram, LinkedIn, Twitter, YouTube
- **Kontakt-Picker**: Kontakt aus YForm-Tabelle auswählen und ausgeben
- **Öffnungszeiten**: Strukturierte Öffnungszeiten-Daten
- **Bewertungen**: Sterne-Bewertung für Produkte/Artikel

## 🏗️ Architektur

```
lib/widgets/
├── ContentBuilderWidgetInterface.php    # Interface (muss implementiert werden)
├── ContentBuilderWidgetAbstract.php     # Abstrakte Basisklasse (empfohlen)
├── ContentBuilderWidgetRegistry.php     # Registry zum Registrieren/Abrufen
├── DateWidget.php                       # Demo: Datum-Feld
└── SocialMediaWidget.php                # Demo: Social Media Links
```

## 🚀 Eigenes Widget erstellen

### 1. Widget-Klasse erstellen

```php
<?php

namespace FriendsOfREDAXO\YFormContentBuilder\Widgets;

/**
 * Kontakt-Picker Widget
 * 
 * Ermöglicht die Auswahl eines Kontakts aus einer YForm-Tabelle
 */
class ContactPickerWidget extends ContentBuilderWidgetAbstract
{
    /**
     * Eindeutiger Typ-Name
     */
    public static function getType(): string
    {
        return 'contact_picker';
    }
    
    /**
     * Label (für Settings-Seite)
     */
    public static function getLabel(): string
    {
        return 'Kontakt-Picker';
    }
    
    /**
     * Beschreibung (für Settings-Seite)
     */
    public static function getDescription(): string
    {
        return 'Ermöglicht die Auswahl eines Kontakts aus einer YForm-Tabelle und zeigt dessen Daten an.';
    }
    
    /**
     * Felder, die das Widget hinzufügt
     */
    public function getFields(): array
    {
        return [
            'contact_id' => [
                'type' => 'select',
                'label' => 'Kontakt auswählen',
                'choices' => $this->getContactChoices(),
                'default' => ''
            ],
            'contact_show' => [
                'type' => 'checkbox',
                'label' => 'Kontakt anzeigen'
            ]
        ];
    }
    
    /**
     * Hook-Name (wo das Widget eingefügt wird)
     */
    public function getHookName(): string
    {
        return 'after_content';
    }
    
    /**
     * Frontend-Ausgabe
     */
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
            return '';
        }
        
        $output = '';
        
        switch ($framework) {
            case 'uikit':
                $output .= '<div class="uk-card uk-card-default uk-card-body uk-margin">';
                $output .= '<h3>' . $this->escape($contact->getValue('name')) . '</h3>';
                $output .= '<p>' . $this->escape($contact->getValue('email')) . '</p>';
                $output .= '<p>' . $this->escape($contact->getValue('phone')) . '</p>';
                $output .= '</div>';
                break;
                
            case 'bootstrap':
            default:
                $output .= '<div class="panel panel-default">';
                $output .= '<div class="panel-body">';
                $output .= '<h3>' . $this->escape($contact->getValue('name')) . '</h3>';
                $output .= '<p>' . $this->escape($contact->getValue('email')) . '</p>';
                $output .= '<p>' . $this->escape($contact->getValue('phone')) . '</p>';
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

### 2. Widget registrieren

**In boot.php deines AddOns:**

```php
<?php

use FriendsOfREDAXO\YFormContentBuilder\Widgets\ContentBuilderWidgetRegistry;

// Widget registrieren
rex_extension::register('PACKAGES_INCLUDED', function() {
    ContentBuilderWidgetRegistry::register(new ContactPickerWidget());
});
```

**Oder via Extension Point:**

```php
<?php

rex_extension::register('YFORM_CONTENT_BUILDER_WIDGETS', function(rex_extension_point $ep) {
    $widgets = $ep->getSubject();
    $widgets['contact_picker'] = new ContactPickerWidget();
    return $widgets;
});
```

### 3. Widget in Einstellungen aktivieren

1. Gehe zu **YForm Content Builder** → **Einstellungen**
2. Scrolle zur **Widget-Verwaltung**
3. Aktiviere das Widget per Checkbox
4. Klicke auf **Speichern**

### 4. Widget in Element nutzen

Das Widget fügt seine Felder automatisch zu allen Elementen hinzu (oder nur zu Elementen mit passendem Hook).

**Optional: Hook in Element-Config definieren:**

```php
<?php

return [
    'label' => 'Mein Element',
    'icon' => 'fa-star',
    
    // Widget-Hooks definieren (optional)
    'widget_hooks' => ['after_content'],
    
    'fields' => [
        // ... Element-Felder
    ]
];
```

## 🎨 Widget-Hooks

Widgets können an verschiedenen Punkten eingefügt werden:

| Hook | Beschreibung |
|------|--------------|
| `before_content` | Vor dem Hauptinhalt |
| `after_content` | Nach dem Hauptinhalt (Standard) |
| `in_sidebar` | In einer Sidebar |
| `in_footer` | Im Footer-Bereich |

## 🔧 Widget-API

### ContentBuilderWidgetInterface

**Methoden, die implementiert werden müssen:**

```php
// Eindeutiger Typ-Name
public static function getType(): string;

// Label (für Settings)
public static function getLabel(): string;

// Beschreibung (für Settings)
public static function getDescription(): string;

// Felder, die hinzugefügt werden
public function getFields(): array;

// Hook-Name
public function getHookName(): string;

// Frontend-Ausgabe
public function render(array $widgetData, string $framework = 'bootstrap'): string;
```

### ContentBuilderWidgetAbstract

**Hilfsmethoden:**

```php
// Prüft ob Widget aktiviert ist
public function isEnabled(): bool;

// Rendert ein Template
protected function renderTemplate(string $templatePath, array $data): string;

// Escaped HTML
protected function escape(string $value): string;
```

### ContentBuilderWidgetRegistry

**Methoden:**

```php
// Initialisiert Registry (lädt Standard-Widgets und Extension Point)
ContentBuilderWidgetRegistry::init();

// Registriert ein Widget
ContentBuilderWidgetRegistry::register(ContentBuilderWidgetInterface $widget);

// Gibt ein Widget zurück
ContentBuilderWidgetRegistry::get(string $type): ?ContentBuilderWidgetInterface;

// Gibt alle Widgets zurück
ContentBuilderWidgetRegistry::getAll(): array;

// Gibt nur aktivierte Widgets zurück
ContentBuilderWidgetRegistry::getEnabled(): array;

// Gibt Widgets für einen Hook zurück
ContentBuilderWidgetRegistry::getForHook(string $hookName): array;

// Gibt Widget-Felder für einen Hook zurück
ContentBuilderWidgetRegistry::getFieldsForHook(string $hookName, string $prefix = 'widget_'): array;
```

## 📝 Beispiel: Komplettes Widget

Siehe `lib/widgets/DateWidget.php` und `lib/widgets/SocialMediaWidget.php` für vollständige Beispiele.

## 🎯 Best Practices

1. **Prefix verwenden**: Alle Feldnamen werden automatisch mit `widget_{type}_` geprefixed
2. **Framework-Support**: Implementiere Templates für mindestens `bootstrap` und `uikit`
3. **Validierung**: Prüfe immer, ob Daten vorhanden sind, bevor du renderst
4. **Escaping**: Nutze `$this->escape()` für alle Ausgaben
5. **Performance**: Nutze Caching für teure Operationen (z.B. DB-Queries)
6. **Aktivierung**: Widget nur aktivieren, wenn es wirklich benötigt wird

## 🔒 Extension Point

**YFORM_CONTENT_BUILDER_WIDGETS**

Wird aufgerufen, wenn die Widget-Registry initialisiert wird.

```php
rex_extension::register('YFORM_CONTENT_BUILDER_WIDGETS', function(rex_extension_point $ep) {
    $widgets = $ep->getSubject();
    
    // Widget hinzufügen
    $widgets['my_widget'] = new MyCustomWidget();
    
    // Widget überschreiben
    $widgets['date'] = new MyDateWidget();
    
    // Widget entfernen
    unset($widgets['social_media']);
    
    return $widgets;
});
```

## 🐛 Troubleshooting

### Widget wird nicht angezeigt

1. Widget in Einstellungen aktiviert?
2. Hook-Name korrekt?
3. `widget_show` Checkbox aktiviert?
4. Felder korrekt benannt (mit Prefix)?

### Widget-Felder werden nicht gespeichert

1. Prüfe Browser-Konsole auf JavaScript-Fehler
2. Prüfe ob Feldnamen korrekt geprefixed sind
3. Prüfe ob `getFields()` korrekte Array-Struktur zurückgibt

### Widget-Ausgabe fehlt im Frontend

1. Prüfe ob `render()` Methode implementiert ist
2. Prüfe ob Widget aktiviert ist
3. Prüfe ob Widget-Daten in `$elementData` vorhanden sind
4. Debugge mit `var_dump($widgetData)` in `render()`

## 📖 Siehe auch

- [API.md](API.md) - Content Builder API
- [README.md](README.md) - Allgemeine Dokumentation
- [Extension Points](#) - Alle Extension Points
