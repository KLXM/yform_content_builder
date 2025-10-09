# Element Config.php - Dokumentation

Die `config.php` Datei definiert die Struktur und das Verhalten eines Content Builder Elements. Diese Dokumentation beschreibt alle verfügbaren Optionen.

## 📁 Struktur

```
elements/
├── element_name/
│   ├── config.php          # Element-Konfiguration 
│   └── templates/
│       ├── bootstrap.php   # Bootstrap Template
│       ├── plain.php       # Plain HTML Template  
│       └── uikit.php       # UIKit Template
```

## 🏗️ Grundstruktur

```php
<?php
return [
    'label' => 'Element Name',           // Anzeigename im Backend
    'description' => 'Beschreibung',    // Hilfetext für User
    'icon' => 'fa-icon-name',           // Font Awesome Icon
    'category' => 'content',            // Optional: Kategorie
    
    'fields' => [
        // Feldkonfigurationen hier...
    ],
    
    'settings_modal' => [               // Optional: Erweiterte Optionen
        'label' => 'Einstellungen',
        'icon' => 'fa-cog',
        'fields' => ['field1', 'field2']
    ]
];
```

## 🎯 Verfügbare Feldtypen

### Standard Felder

#### text - Einzeiliger Text
```php
'title' => [
    'type' => 'text',
    'label' => 'Titel',
    'notice' => 'Hilfetext für den User',
    'default' => 'Standardwert'
]
```

#### textarea - Mehrzeiliger Text  
```php
'description' => [
    'type' => 'textarea',
    'label' => 'Beschreibung',
    'notice' => 'Längerer Text möglich'
]
```

#### choice - Dropdown-Auswahl
```php
'layout' => [
    'type' => 'choice',
    'label' => 'Layout',
    'choices' => [
        'left' => 'Links',
        'center' => 'Zentriert',
        'right' => 'Rechts'
    ],
    'default' => 'left'
]
```

#### checkbox - Ja/Nein Auswahl
```php
'show_title' => [
    'type' => 'checkbox',
    'label' => 'Titel anzeigen',
    'default' => '1'
]
```

#### ckeditor5 - Rich Text Editor
```php
'content' => [
    'type' => 'ckeditor5',
    'label' => 'Inhalt',
    'profile' => 'default'
]
```

### Enhanced Media Felder

#### be_media_enhanced - Enhanced Media Browser
```php
'media' => [
    'type' => 'be_media_enhanced',
    'label' => 'Bild oder Video',
    'allowed_types' => ['image', 'video'],  // Typ-Filter
    'notice' => 'Wählen Sie ein Medium aus'
]
```

#### be_media - Standard Media Browser
```php
'image' => [
    'type' => 'be_media',
    'label' => 'Bild',
    'types' => 'jpg,jpeg,png,gif'
]
```

#### be_link - Link/Linkmap Widget
```php
'link' => [
    'type' => 'be_link',
    'label' => 'Link',
    'types' => 'intern,extern,media,mailto,tel'
]
```

### Repeater - Listen von Elementen

```php
'items' => [
    'type' => 'repeater',
    'label' => 'Items',
    'add_label' => 'Item hinzufügen',
    'fields' => [
        'title' => [
            'type' => 'text',
            'label' => 'Titel'
        ],
        'image' => [
            'type' => 'be_media_enhanced',
            'label' => 'Bild',
            'allowed_types' => ['image']
        ]
    ]
]
```

## 🎨 Settings Modal

Komplexe Optionen können in einem Modal-Dialog organisiert werden:

```php
return [
    'label' => 'Gallery',
    'fields' => [
        'headline' => [
            'type' => 'text',
            'label' => 'Überschrift'
        ],
        'items' => [
            'type' => 'repeater',
            'label' => 'Bilder',
            'fields' => [/* ... */]
        ],
        
        // Diese Felder kommen ins Settings-Modal:
        'layout' => [
            'type' => 'choice',
            'label' => 'Layout',
            'choices' => ['grid' => 'Grid', 'masonry' => 'Masonry']
        ],
        'columns' => [
            'type' => 'choice', 
            'label' => 'Spalten',
            'choices' => ['2' => '2', '3' => '3', '4' => '4']
        ]
    ],
    
    'settings_modal' => [
        'label' => 'Layout-Einstellungen',
        'icon' => 'fa-cogs',
        'fields' => ['layout', 'columns']  // Diese Felder ins Modal
    ]
];
```

## 📱 Advanced Repeater Features

### Grid-View für kompakte Listen
```php
'items' => [
    'type' => 'repeater',
    'label' => 'Gallery Items',
    'view' => 'grid',           // Grid statt Liste
    'grid_columns' => 3,        // 3 Spalten
    'fields' => [
        'media' => [
            'type' => 'be_media_enhanced',
            'label' => 'Medium',
            'allowed_types' => ['image', 'video']
        ]
    ],
    'item_modal' => [           // Erweiterte Optionen pro Item
        'label' => 'Erweiterte Optionen',
        'icon' => 'fa-cog',
        'fields' => ['caption', 'alt_text']
    ]
]
```

## 🚀 Best Practices

### 1. Element-Naming
```php
// ✅ Gut
'label' => 'Text & Bild',
'icon' => 'fa-image',
'category' => 'content',

// ❌ Verwirrend
'label' => 'text_image',
'icon' => 'my-custom-icon'
```

### 2. User-Friendly Labels
```php
// ✅ Verständlich
'headline' => [
    'type' => 'text',
    'label' => 'Überschrift',
    'notice' => 'Erscheint über dem Inhalt'
]

// ❌ Kryptisch  
'h1' => [
    'type' => 'text',
    'label' => 'h1'
]
```

### 3. Sinnvolle Defaults
```php
// ✅ Gute Defaults
'alignment' => [
    'type' => 'choice',
    'label' => 'Ausrichtung',
    'choices' => ['left' => 'Links', 'center' => 'Mitte'],
    'default' => 'left'  // User muss nicht wählen
]
```

### 4. Enhanced Media für moderne Inhalte
```php
// ✅ Modern - unterstützt Bilder UND Videos
'media' => [
    'type' => 'be_media_enhanced',
    'label' => 'Medium',
    'allowed_types' => ['image', 'video']
]

// ❌ Eingeschränkt - nur Bilder
'image' => [
    'type' => 'be_media',
    'label' => 'Bild'
]
```

## 💡 Tipps & Tricks

### 1. Settings-Modal für komplexe Elemente
```php
// Häufig genutzte Felder normal, erweiterte Optionen ins Modal
'settings_modal' => [
    'label' => 'Erweiterte Einstellungen',
    'icon' => 'fa-cogs',
    'fields' => ['advanced_option1', 'advanced_option2']
]
```

### 2. Grid-View bei vielen Repeater-Items
```php
// Bei Galleries, Cards etc. - übersichtlicher als Liste
'items' => [
    'type' => 'repeater',
    'view' => 'grid',
    'grid_columns' => 4
]
```

### 3. Typ-Filter für Media
```php
// Nur relevante Medien anzeigen
'hero_image' => [
    'type' => 'be_media_enhanced',
    'allowed_types' => ['image']  // Keine Videos
]
```

## ⚠️ Häufige Fehler

### 1. Fehlende Required Keys
```php
// ❌ Fehlt 'label'
'title' => [
    'type' => 'text'
]

// ✅ Vollständig
'title' => [
    'type' => 'text',
    'label' => 'Titel'
]
```

### 2. Falsche Choice-Syntax
```php
// ❌ Falsch
'layout' => [
    'type' => 'choice',
    'choices' => ['Links', 'Rechts']  // Fehlende Keys
]

// ✅ Richtig
'layout' => [
    'type' => 'choice', 
    'choices' => [
        'left' => 'Links',
        'right' => 'Rechts'
    ]
]
```

### 3. Unklare Allowed Types
```php
// ❌ Unklar
'allowed_types' => 'image,video'

// ✅ Eindeutig
'allowed_types' => ['image', 'video']
```

## 📝 Vollständiges Beispiel

```php
<?php
return [
    'label' => 'Hero Section',
    'description' => 'Große Hero-Sektion mit Hintergrundbild und Text',
    'icon' => 'fa-image',
    'category' => 'content',
    
    'fields' => [
        'background' => [
            'type' => 'be_media_enhanced',
            'label' => 'Hintergrundbild',
            'allowed_types' => ['image'],
            'notice' => 'Empfohlene Größe: 1920x1080px'
        ],
        'headline' => [
            'type' => 'text',
            'label' => 'Hauptüberschrift',
            'default' => 'Willkommen'
        ],
        'subline' => [
            'type' => 'textarea',
            'label' => 'Untertitel',
            'notice' => 'Kurzer beschreibender Text'
        ],
        'cta_text' => [
            'type' => 'text',
            'label' => 'Button-Text',
            'default' => 'Mehr erfahren'
        ],
        'cta_link' => [
            'type' => 'be_link',
            'label' => 'Button-Link'
        ],
        
        // Erweiterte Optionen
        'text_color' => [
            'type' => 'choice',
            'label' => 'Textfarbe',
            'choices' => [
                'white' => 'Weiß',
                'dark' => 'Dunkel'
            ],
            'default' => 'white'
        ],
        'alignment' => [
            'type' => 'choice',
            'label' => 'Textausrichtung',
            'choices' => [
                'left' => 'Links',
                'center' => 'Zentriert',
                'right' => 'Rechts'
            ],
            'default' => 'center'
        ]
    ],
    
    'settings_modal' => [
        'label' => 'Design-Optionen',
        'icon' => 'fa-paint-brush',
        'fields' => ['text_color', 'alignment']
    ]
];
```

Diese Dokumentation zeigt alle tatsächlich verfügbaren Features des YForm Content Builders.