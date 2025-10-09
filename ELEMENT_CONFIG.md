# Element Config.php - Vollständige Dokumentation

Die `config.php` Datei definiert die Struktur und das Verhalten eines Content Builder Elements. Diese Dokumentation erklärt alle verfügbaren Optionen und Funktionen.

## 📁 Struktur

```
elements/
├── element_name/
│   ├── config.php          # Element-Konfiguration (diese Datei)
│   └── templates/
│       ├── bootstrap.php   # Bootstrap Template
│       ├── plain.php       # Plain HTML Template  
│       └── uikit.php       # UIKit Template
```

## 🏗️ Grundstruktur config.php

```php
<?php
/**
 * Element: Element Name
 * Beschreibung: Kurze Beschreibung des Elements
 */

return [
    // === ELEMENT METADATA ===
    'name' => 'element_name',                    // Eindeutiger Element-Name
    'title' => 'Element Titel',                 // Anzeigename im Backend
    'description' => 'Element Beschreibung',    // Beschreibung für User
    'icon' => 'fa-icon-name',                   // Font Awesome Icon
    
    // === FORMULAR-KONFIGURATION ===
    'fields' => [
        // Feldkonfigurationen hier...
    ],
    
    // === ERWEITERTE OPTIONEN ===
    'options' => [
        'tabs' => true,              // Tab-Gruppierung aktivieren
        'modal' => false,            // In Modal öffnen
        'repeater_view' => 'list',   // 'list' oder 'grid'
        'grid_columns' => 3,         // Anzahl Spalten bei Grid-View
    ]
];
```

## 🎯 Feldtypen Referenz

### Standard YForm Feldtypen

#### text - Einzeiliger Text
```php
[
    'type' => 'text',
    'name' => 'title',
    'label' => 'Titel',
    'required' => true,
    'placeholder' => 'Titel eingeben...',
    'attributes' => ['maxlength' => 100],
    'tab' => 'content'  // Optional: Tab-Zuordnung
]
```

#### textarea - Mehrzeiliger Text
```php
[
    'type' => 'textarea',
    'name' => 'description',
    'label' => 'Beschreibung',
    'attributes' => ['rows' => 4, 'cols' => 50]
]
```

#### ckeditor5 - Rich Text Editor
```php
[
    'type' => 'ckeditor5',
    'name' => 'content',
    'label' => 'Inhalt',
    'profile' => 'default',  // CKEditor Profil
    'height' => 300
]
```

#### select - Dropdown-Auswahl
```php
[
    'type' => 'select',
    'name' => 'alignment',
    'label' => 'Ausrichtung',
    'options' => [
        'left' => 'Links',
        'center' => 'Zentriert', 
        'right' => 'Rechts'
    ],
    'default' => 'left',
    'required' => true
]
```

#### checkbox - Einzelne Checkbox
```php
[
    'type' => 'checkbox',
    'name' => 'show_title',
    'label' => 'Titel anzeigen',
    'default' => 1
]
```

#### radio - Radio Buttons
```php
[
    'type' => 'radio',
    'name' => 'layout',
    'label' => 'Layout',
    'options' => [
        'horizontal' => 'Horizontal',
        'vertical' => 'Vertikal'
    ],
    'default' => 'horizontal'
]
```

### Enhanced Media Fieldtypes

#### be_media_enhanced - Enhanced Media Widget
```php
[
    'type' => 'be_media_enhanced',
    'name' => 'media',
    'label' => 'Media (Bild oder Video)',
    'allowed_types' => 'jpg,jpeg,png,gif,mp4,webm,mov',  // Erlaubte Dateitypen
    'aspect_ratio' => '16:9',        // Aspect Ratio für Preview
    'video_autoplay' => false,       // Autoplay für Videos
    'video_controls' => true,        // Video Controls anzeigen
    'video_muted' => true,          // Videos stumm starten
    'clickable_placeholder' => true, // Klickbarer Platzhalter
    'preview_size' => 'medium'       // Preview-Größe: small, medium, large
]
```

#### be_media - Standard Media Widget
```php
[  
    'type' => 'be_media',
    'name' => 'image',
    'label' => 'Bild',
    'types' => 'jpg,jpeg,png,gif',
    'preview' => 1,
    'category' => 1  // Media-Kategorie ID
]
```

#### be_link - Link/Linkmap Widget
```php
[
    'type' => 'be_link',
    'name' => 'link',
    'label' => 'Link',
    'types' => 'intern,extern,media,mailto,tel'
]
```

### Repeater-Felder

#### Simple Repeater
```php
[
    'type' => 'repeater',
    'name' => 'items',
    'label' => 'Items',
    'fields' => [
        [
            'type' => 'text',
            'name' => 'title',
            'label' => 'Titel'
        ],
        [
            'type' => 'textarea', 
            'name' => 'description',
            'label' => 'Beschreibung'
        ]
    ],
    'min_items' => 1,           // Minimum Anzahl Items
    'max_items' => 10,          // Maximum Anzahl Items
    'add_label' => 'Item hinzufügen',
    'delete_label' => 'Löschen',
    'sortable' => true,         // Sortierbar mit Move-Buttons
    'view_mode' => 'list',      // 'list' oder 'grid'
    'grid_columns' => 3,        // Spalten bei Grid-View
    'modal' => false            // Items in Modal bearbeiten
]
```

#### Advanced Repeater mit Enhanced Media
```php
[
    'type' => 'repeater',
    'name' => 'gallery_items',
    'label' => 'Gallery Items',
    'fields' => [
        [
            'type' => 'be_media_enhanced',
            'name' => 'media',
            'label' => 'Media',
            'allowed_types' => 'jpg,jpeg,png,gif,mp4,webm',
            'aspect_ratio' => '16:9'
        ],
        [
            'type' => 'text',
            'name' => 'caption',
            'label' => 'Bildunterschrift',
            'tab' => 'content'
        ],
        [
            'type' => 'be_link',
            'name' => 'link',
            'label' => 'Link',
            'tab' => 'settings'
        ]
    ],
    'view_mode' => 'grid',
    'grid_columns' => 4,
    'modal' => true,            // Items in Modal für bessere UX
    'sortable' => true
]
```

## 🎨 Tab-Gruppierung

Große Formulare können in Tabs organisiert werden:

```php
return [
    'name' => 'complex_element',
    'title' => 'Komplexes Element',
    'options' => [
        'tabs' => true
    ],
    'fields' => [
        // Content Tab
        [
            'type' => 'text',
            'name' => 'title',
            'label' => 'Titel',
            'tab' => 'content'
        ],
        [
            'type' => 'ckeditor5',
            'name' => 'text',
            'label' => 'Text',
            'tab' => 'content'
        ],
        
        // Settings Tab
        [
            'type' => 'select',
            'name' => 'layout',
            'label' => 'Layout',
            'options' => ['left' => 'Links', 'right' => 'Rechts'],
            'tab' => 'settings'
        ],
        [
            'type' => 'checkbox',
            'name' => 'show_border',
            'label' => 'Rahmen anzeigen',
            'tab' => 'settings'
        ],
        
        // Style Tab
        [
            'type' => 'text',
            'name' => 'css_class',
            'label' => 'CSS Klassen',
            'tab' => 'style'
        ],
        [
            'type' => 'select',
            'name' => 'background_color',
            'label' => 'Hintergrundfarbe',
            'options' => [
                '' => 'Standard',
                'primary' => 'Primary',
                'secondary' => 'Secondary'
            ],
            'tab' => 'style'
        ]
    ]
];
```

**Tab-Labels** werden automatisch generiert:
- `content` → "Inhalt"
- `settings` → "Einstellungen" 
- `style` → "Design"
- Eigene Tabs: Uppercase first letter

## 📱 Grid-View für Repeater

Repeater können als kompakte Grid-Ansicht dargestellt werden:

```php
[
    'type' => 'repeater',
    'name' => 'cards',
    'label' => 'Cards',
    'fields' => [
        [
            'type' => 'be_media_enhanced',
            'name' => 'image',
            'label' => 'Bild',
            'aspect_ratio' => '16:9'
        ],
        [
            'type' => 'text',
            'name' => 'title',
            'label' => 'Titel'
        ]
    ],
    'view_mode' => 'grid',      // Grid statt List
    'grid_columns' => 3,        // 3 Spalten
    'modal' => true             // Bearbeitung im Modal
]
```

**Grid-View Optionen:**
- `grid_columns`: 2-6 Spalten möglich
- `modal`: Items in Modal bearbeiten (empfohlen bei Grid)
- Automatische Responsive Anpassung
- Move-Buttons für Sortierung

## 🚀 Best Practices

### 1. Element-Naming
```php
// ✅ Gut
'name' => 'hero_section',
'title' => 'Hero Section',

// ❌ Schlecht  
'name' => 'HeroSection',  // CamelCase vermeiden
'title' => 'hero_section', // Kein Underscore in Title
```

### 2. Required Fields
```php
// ✅ Wichtige Felder als required markieren
[
    'type' => 'text',
    'name' => 'title',
    'label' => 'Titel',
    'required' => true
]
```

### 3. Default Values
```php
// ✅ Sinnvolle Defaults setzen
[
    'type' => 'select',
    'name' => 'alignment',
    'label' => 'Ausrichtung',
    'options' => ['left' => 'Links', 'center' => 'Mitte'],
    'default' => 'left'
]
```

### 4. Responsive Design
```php
// ✅ Enhanced Media für responsive Images
[
    'type' => 'be_media_enhanced',
    'name' => 'hero_image',
    'label' => 'Hero Bild',
    'aspect_ratio' => '16:9',  // Konsistente Proportionen
    'allowed_types' => 'jpg,jpeg,png,webp'
]
```

### 5. User Experience
```php
// ✅ Grid-View bei vielen Items
[
    'type' => 'repeater',
    'name' => 'gallery',
    'view_mode' => 'grid',
    'grid_columns' => 4,
    'modal' => true  // Bessere UX bei komplexen Items
]
```

## 🔧 Erweiterte Optionen

### Modal-Modus
```php
'options' => [
    'modal' => true  // Gesamtes Element in Modal öffnen
]
```

### Custom Icons
```php
'icon' => 'fa-images',           // Font Awesome Icon
'icon' => 'rex-icon rex-icon-module',  // REDAXO Icon
```

### Validation
```php
[
    'type' => 'text',
    'name' => 'email',
    'label' => 'E-Mail',
    'attributes' => [
        'type' => 'email',       // HTML5 Validation
        'pattern' => '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$'
    ]
]
```

## ⚠️ Häufige Fehler

### 1. Falsche Array-Struktur
```php
// ❌ Falsch
'fields' => [
    'type' => 'text',  // Fehlt Array-Wrapper
    'name' => 'title'
]

// ✅ Richtig
'fields' => [
    [
        'type' => 'text',
        'name' => 'title'
    ]
]
```

### 2. Reserved Names
```php
// ❌ Vermeiden - Reserved Names
'name' => 'id',
'name' => 'element_type',
'name' => 'sort_order',

// ✅ Besser
'name' => 'item_id',
'name' => 'content_type',
'name' => 'display_order'
```

### 3. Missing Required Keys
```php
// ❌ Fehlt 'name' 
[
    'type' => 'text',
    'label' => 'Titel'
]

// ✅ Alle Required Keys
[
    'type' => 'text',
    'name' => 'title',  // Required!
    'label' => 'Titel'
]
```

## 💡 Tipps & Tricks

### 1. Conditional Fields
```php
// Feld nur bei bestimmter Auswahl anzeigen
[
    'type' => 'select',
    'name' => 'show_image',
    'options' => ['0' => 'Nein', '1' => 'Ja'],
    'onchange' => 'toggleImageField(this.value)'
]
```

### 2. Bulk Operations
```php
// Repeater mit Bulk-Aktionen
[
    'type' => 'repeater',
    'name' => 'items',
    'bulk_actions' => [
        'duplicate' => 'Duplizieren',
        'delete_all' => 'Alle löschen'
    ]
]
```

### 3. Custom CSS Classes
```php
[
    'type' => 'text',
    'name' => 'title',
    'label' => 'Titel',
    'wrapper_class' => 'col-md-6',    // Bootstrap Grid
    'input_class' => 'form-control-lg' // Input-Styling
]
```

Diese Dokumentation deckt alle wichtigen Aspekte der Element-Konfiguration ab. Für spezielle Use Cases können weitere Feldtypen und Optionen über Extension Points hinzugefügt werden.