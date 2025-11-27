# YForm Content Builder - Developer Documentation

Diese Dokumentation richtet sich an Entwickler, die den YForm Content Builder in eigenen Modulen verwenden, eigene Elemente erstellen oder die API nutzen möchten.

## 📚 Inhalt

1. [Modul-Integration](#1-modul-integration)
2. [API & Helper](#2-api--helper)
3. [Element-Konfiguration](#3-element-konfiguration)
4. [Custom Elements](#4-custom-elements)

---

## 1. Modul-Integration

Der Content Builder kann auf zwei Arten in REDAXO Modulen verwendet werden:

### A. Einfache Element-API (Single Element)

Verwende einzelne Content Builder Elemente direkt in Modulen. Ideal für spezifische Module wie "Galerie", "Hero", etc.

**INPUT:**
```php
<?php
// Einzelnes Element (z.B. Galerie) - REX_VALUE[1] für gespeicherte Daten übergeben
echo yform_content_builder_module::create('gallery', 'REX_VALUE[1]')->renderInput();
?>
```

**OUTPUT:**
```php
<?php
// Element ausgeben - Daten aus REX_VALUE[1] übergeben
echo yform_content_builder_module::create('gallery', 'REX_VALUE[1]', 'bootstrap')->renderOutput();

// Oder mit UIkit Framework
echo yform_content_builder_module::create('gallery', 'REX_VALUE[1]', 'uikit')->renderOutput();
?>
```

### B. Full Builder API (Multi Element)

Verwende den kompletten Content Builder mit mehreren Elementen und Drag & Drop.

**INPUT:**
```php
<?php
// Wert aus Slice holen
$currentValue = $this->getCurrentSlice()->getValue(1);

// Content Builder erstellen
$contentBuilder = yform_content_builder_module::createWithValue(1, $currentValue, [
    'framework' => 'bootstrap',
    'label' => 'Seiteninhalt',
    'description' => 'Fügen Sie Content-Elemente hinzu',
    // Optional: Nur bestimmte Elemente erlauben
    // 'allowed_elements' => ['headline', 'text', 'gallery', 'hero']
]);

// Editor ausgeben
echo $contentBuilder->getEditor();
?>
```

**OUTPUT:**
```php
<?php
// Wert aus Slice holen
$currentValue = $this->getCurrentSlice()->getValue(1);

// Content Builder erstellen
$contentBuilder = yform_content_builder_module::createWithValue(1, $currentValue, [
    'framework' => 'bootstrap'
]);

// Frontend-Output ausgeben
echo $contentBuilder->renderOutput();
?>
```

---

## 2. API & Helper

Die Klasse `yform_content_builder_helper` bietet nützliche Methoden für die Frontend-Ausgabe.

### Content Rendern

```php
use KLXM\YformContentBuilder\Helper as ContentBuilderHelper;

// Daten aus YForm-Tabelle oder Modul
$contentData = $dataset->getValue('content');

// Rendern (automatische Template-Wahl)
echo ContentBuilderHelper::render($contentData, 'bootstrap');
```

### Utility Methoden

```php
// Prüfen ob Datei ein Bild ist
if (ContentBuilderHelper::isImage('mein_bild.jpg')) { ... }

// Prüfen ob Datei ein Video ist
if (ContentBuilderHelper::isVideo('mein_video.mp4')) { ... }

// MIME-Type ermitteln
$mime = ContentBuilderHelper::getMimeType('datei.pdf');

// Bilder aus Content extrahieren (z.B. für OG-Tags)
$images = ContentBuilderHelper::extractImages($jsonContent);

// Ersten Text extrahieren (z.B. für Meta-Description)
$text = ContentBuilderHelper::extractFirstText($jsonContent, 160);
```

---

## 3. Element-Konfiguration

Jedes Element wird durch eine `config.php` definiert.

### Grundstruktur

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

### Verfügbare Feldtypen

| Typ | Beschreibung | Beispiel |
|-----|--------------|----------|
| `text` | Einzeiliger Text | `'type' => 'text'` |
| `textarea` | Mehrzeiliger Text | `'type' => 'textarea'` |
| `choice` | Dropdown | `'type' => 'choice', 'choices' => ['a' => 'A', 'b' => 'B']` |
| `checkbox` | Checkbox | `'type' => 'checkbox', 'default' => '1'` |
| `ckeditor5` | Rich Text | `'type' => 'ckeditor5', 'profile' => 'default'` |
| `be_media_enhanced` | Media Browser (Bild/Video) | `'type' => 'be_media_enhanced', 'allowed_types' => ['image', 'video']` |
| `be_link` | Linkmap | `'type' => 'be_link'` |
| `repeater` | Wiederholbare Liste | `'type' => 'repeater', 'fields' => [...]` |

### Repeater Beispiel (Grid View)

```php
'items' => [
    'type' => 'repeater',
    'label' => 'Gallery Items',
    'view' => 'grid',           // Grid statt Liste
    'grid_columns' => 3,        // 3 Spalten
    'fields' => [
        'media' => [
            'type' => 'be_media_enhanced',
            'label' => 'Medium'
        ]
    ]
]
```

---

## 4. Custom Elements

Du kannst eigene Elemente erstellen, die automatisch vom Builder erkannt werden.

### Speicherort

Empfohlen: Im `project` AddOn.

```
redaxo/src/addons/project/
└── elements/
    └── my_element/
        ├── config.php
        └── templates/
            ├── bootstrap.php
            └── uikit.php (optional)
```

### WICHTIG: Demo-Elemente

Sobald du **eigene Elemente** erstellst (via `project/elements/` oder Extension Point), werden die mitgelieferten **Demo-Elemente NICHT mehr geladen**.

➡️ Kopiere benötigte Demo-Elemente aus `yform_content_builder/elements/` in dein `project/elements/` Verzeichnis!

### Beispiel Custom Element

**config.php:**
```php
<?php
return [
    'label' => 'Zitat',
    'icon' => 'fa-quote-left',
    'fields' => [
        'quote' => ['type' => 'textarea', 'label' => 'Zitat'],
        'author' => ['type' => 'text', 'label' => 'Autor']
    ]
];
```

**templates/bootstrap.php:**
```php
<?php
// Helper-Klasse nutzen statt globaler Funktionen!
$quote = $elementData['quote'] ?? '';
$author = $elementData['author'] ?? '';
?>
<blockquote>
    <p><?= nl2br(rex_escape($quote)) ?></p>
    <footer><?= rex_escape($author) ?></footer>
</blockquote>
```

### Registrierung via Extension Point (Alternative)

In `boot.php` eines beliebigen AddOns:

```php
rex_extension::register('YFORM_CONTENT_BUILDER_ELEMENT_PATHS', function($ep) {
    $paths = $ep->getSubject();
    $paths[] = rex_addon::get('mein_addon')->getPath('content_elements/');
    return $paths;
});
```
