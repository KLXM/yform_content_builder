# YForm Content Builder API

Diese Dokumentation beschreibt die API des YForm Content Builders, wie man eigene Elemente erstellt und das System mit eigenen Feldtypen erweitert.

## 📚 Inhaltsverzeichnis

- [API-Endpunkte](#api-endpunkte)
- [Modul-Integration](#modul-integration)
- [Feldtypen-System](#feldtypen-system)
- [Feld-Konfiguration](#feld-konfiguration)
  - [Gemeinsame Optionen](#gemeinsame-optionen-alle-felder)
  - [Permission System](#permission-system)
- [Eigene Elemente erstellen](#eigene-elemente-erstellen)
- [Eigene Feldtypen erstellen](#eigene-feldtypen-erstellen)
- [Extension Points](#extension-points)
- [Helper-Klassen](#helper-klassen)

---

## API-Endpunkte

Der Content Builder nutzt eine dedizierte `rex_api_function` für alle AJAX-Requests. Die URL ist immer gleich:

```
/redaxo/index.php?rex-api-call=content_builder&action=<action>
```

### Verfügbare Actions

| Action | Methode | Beschreibung | Parameter |
|--------|---------|--------------|-----------|
| `load_slice_form` | POST | Lädt das Bearbeitungsformular für ein Element | `slice_type`, `slice_data` |
| `render_slice` | POST | Rendert ein Element mit Template | `slice_type`, `slice_data`, `framework` |
| `load_media_categories` | POST | Lädt Medienpool-Kategorien | - |
| `load_media_list` | POST | Lädt Medienliste einer Kategorie | `category_id`, `type` |
| `get_media_preview` | POST | Erzeugt Media-Preview HTML | `media_file`, `types` |

### Beispiel: Element-Formular laden

```javascript
$.ajax({
    url: '/redaxo/index.php?rex-api-call=content_builder',
    method: 'POST',
    data: {
        action: 'load_slice_form',
        slice_type: 'text_image',
        slice_data: {
            headline: 'Mein Titel',
            text: '<p>Inhalt</p>'
        }
    },
    success: function(html) {
        // Formular HTML einfügen
    }
});
```

### Beispiel: Element rendern

```javascript
$.ajax({
    url: '/redaxo/index.php?rex-api-call=content_builder',
    method: 'POST',
    data: {
        action: 'render_slice',
        slice_type: 'headline',
        slice_data: { text: 'Willkommen', level: 'h1' },
        framework: 'uikit'
    },
    success: function(html) {
        // Gerenderten HTML-Output einfügen
    }
});
```

---

## Modul-Integration

Der Content Builder kann auf verschiedene Arten in REDAXO Modulen verwendet werden:

### A. Full Builder API (Multi Element)

Verwende den kompletten Content Builder mit mehreren Elementen und Drag & Drop.

**INPUT:**
```php
<?php
// Wert aus Slice holen
$currentValue = $this->getCurrentSlice()->getValue(1);

// Content Builder erstellen
$contentBuilder = yform_content_builder_module::createWithValue(1, $currentValue, [
    'framework' => 'bootstrap', // Framework für Backend-Preview
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
    'framework' => 'uikit' 
]);

// Frontend-Output ausgeben
echo $contentBuilder->renderOutput();
?>
```

### B. Single Element API (Einzelnes Element)

Verwende einzelne Content Builder Elemente direkt in Modulen.

**INPUT:**
```php
<?php
echo yform_content_builder_module::create('gallery', 'REX_VALUE[1]', 'bootstrap')->renderInput();
?>
```

**OUTPUT:**
```php
<?php
echo yform_content_builder_module::create('gallery', 'REX_VALUE[1]', 'uikit')->renderOutput();
?>
```

---

## Feldtypen-System

Das Addon nutzt ein **Plugin-System für Feldtypen**. Jeder Feldtyp ist eine eigene Klasse im Namespace `FriendsOfREDAXO\YFormContentBuilder\Fields`.

### Architektur

```
lib/fields/
├── ContentBuilderFieldInterface.php   # Interface (muss implementiert werden)
├── ContentBuilderFieldAbstract.php    # Abstrakte Basisklasse (empfohlen)
├── ContentBuilderFieldRegistry.php    # Registry zum Registrieren/Abrufen
└── [FieldName]Field.php               # Konkrete Feldtypen
```

### Verfügbare Feldtypen

| Typ | Klasse | Beschreibung |
|-----|--------|--------------|
| `text` | `TextField` | Einzeiliges Textfeld |
| `textarea` | `TextareaField` | Mehrzeiliges Textfeld |
| `checkbox` | `CheckboxField` | Checkbox (Boolean) |
| `select` | `SelectField` | Einfaches Select-Dropdown |
| `choice` | `ChoiceField` | Erweitertes Select mit Selectpicker, Farben, Icons |
| `cke5` | `Cke5Field` | CKEditor 5 WYSIWYG |
| `be_media` | `BeMediaField` | REDAXO Mediapool Widget |
| `be_link` | `BeLinkField` | REDAXO Linkmap Widget |
| `radio_image` | `RadioImageField` | Radio-Buttons mit Bildern/SVGs |
| `color_swatches` | `ColorSwatchesField` | Farbauswahl mit visuellen Farbfeldern |
| `repeater` | `RepeaterField` | Wiederholbare Feldgruppen |

---

## Feld-Konfiguration

Jeder Feldtyp unterstützt spezifische Konfigurationsoptionen:

### Gemeinsame Optionen (alle Felder)

| Option | Typ | Beschreibung |
|--------|-----|--------------|
| `type` | string | Feldtyp (z.B. `text`, `be_media`) |
| `label` | string | Beschriftung des Feldes |
| `notice` | string | Hilfetext unter dem Feld |
| `default` | mixed | Standardwert |
| `perm` | string/array | Berechtigungen - siehe Permission System unten |

### Permission System

Mit dem `perm` Key kannst du einzelne Felder nur für bestimmte Benutzerrollen sichtbar machen:

#### Format

**Einzelne Rolle:**
```php
'perm' => 'admin'      // Nur für Admins
'perm' => 'editor'     // Nur für Redakteure
```

**Mehrere Rollen (pipe-getrennt):**
```php
'perm' => 'editor|reviewer|admin'  // Eine dieser Rollen genügt
```

**Array-Format:**
```php
'perm' => ['editor', 'reviewer', 'admin']
```

#### Verfügbare Rollen

```php
'admin'      // Admin-Benutzer (isAdmin() = true)
'editor'     // Redakteur
'reviewer'   // Freigabekontrolle
'contributor' // Mitarbeiter
'power'      // Power User
// Plus alle benutzerdefinierten Rollen aus deinem REDAXO-System
```

#### Praktische Beispiele

```php
// Kontaktformular mit Admin-Feld
'fields' => [
    'form_title' => [
        'type' => 'text',
        'label' => 'Formular-Titel'
    ],
    
    // Nur Admins sehen diese Feld
    'sql_options' => [
        'type' => 'textarea',
        'label' => 'SQL-Optionen',
        'notice' => 'Nur für Administratoren sichtbar',
        'perm' => 'admin'
    ],
    
    // Nur Power-User und Admins
    'advanced_settings' => [
        'type' => 'repeater',
        'label' => 'Erweiterte Einstellungen',
        'perm' => 'power|admin'
    ]
]
```

#### Sicherheit

- ✅ Permission-Prüfung läuft **serverseitig** (nicht zu umgehen)
- ✅ Funktioniert in **allen Feldtypen**
- ✅ Funktioniert im **Backend-Editor** und **Frontend-Formular**
- ✅ Nicht berechtigt = gar nicht zu sehen

### text

```php
'title' => [
    'type' => 'text',
    'label' => 'Titel',
    'placeholder' => 'Titel eingeben...',
    'notice' => 'Max. 100 Zeichen'
]
```

### textarea

```php
'description' => [
    'type' => 'textarea',
    'label' => 'Beschreibung',
    'rows' => 5
]
```

### checkbox

```php
'show_title' => [
    'type' => 'checkbox',
    'label' => 'Titel anzeigen',
    'default' => 1
]
```

### select

```php
'size' => [
    'type' => 'select',
    'label' => 'Größe',
    'options' => [
        'small' => 'Klein',
        'medium' => 'Mittel',
        'large' => 'Groß'
    ]
]
```

### choice (erweitertes Select)

```php
'color' => [
    'type' => 'choice',
    'label' => 'Farbe',
    'choices' => [
        'primary' => 'Primär',
        'secondary' => 'Sekundär',
        'muted' => 'Gedämpft'
    ],
    'selectpicker' => true,  // Bootstrap Selectpicker
    'choice_colors' => [     // Farbvorschau im Dropdown
        'primary' => '#1e87f0',
        'secondary' => '#222222',
        'muted' => '#999999'
    ],
    'choice_icons' => [      // Icons im Dropdown
        'left' => '<svg>...</svg>',
        'center' => '<svg>...</svg>'
    ],
    'default' => 'primary'
]
```

### cke5

```php
'content' => [
    'type' => 'cke5',
    'label' => 'Inhalt',
    'profile' => 'default',  // CKE5 Profil
    'rows' => 10
]
```

### be_media

```php
'image' => [
    'type' => 'be_media',
    'label' => 'Bild',
    'allowed_types' => 'jpg,jpeg,png,gif,webp,svg'  // oder als Array
]
```

### be_link

```php
'link' => [
    'type' => 'be_link',
    'label' => 'Link',
    'category' => 1  // Start-Kategorie in Linkmap
]
```

### radio_image

```php
'layout' => [
    'type' => 'radio_image',
    'label' => 'Layout',
    'options' => [
        'left' => [
            'label' => 'Bild links',
            'image' => 'data:image/svg+xml;base64,...'
        ],
        'right' => [
            'label' => 'Bild rechts',
            'image' => 'data:image/svg+xml;base64,...'
        ]
    ],
    'default' => 'left'
]
```

### color_swatches

```php
'background' => [
    'type' => 'color_swatches',
    'label' => 'Hintergrund',
    'options' => [
        'white' => ['color' => '#ffffff', 'label' => 'Weiß'],
        'light' => ['color' => '#f8f8f8', 'label' => 'Hell'],
        'dark' => ['color' => '#222222', 'label' => 'Dunkel'],
        'transparent' => ['color' => 'transparent', 'label' => 'Transparent']
    ],
    'default' => 'white'
]
```

### repeater

```php
'items' => [
    'type' => 'repeater',
    'label' => 'Elemente',
    'add_label' => 'Element hinzufügen',
    'view' => 'list',        // 'list' oder 'grid'
    'grid_columns' => 3,     // Bei view: 'grid'
    'fields' => [
        'title' => ['type' => 'text', 'label' => 'Titel'],
        'image' => ['type' => 'be_media', 'label' => 'Bild'],
        'description' => ['type' => 'textarea', 'label' => 'Beschreibung']
    ],
    // Optional: Zusätzliche Felder in Modal auslagern
    'item_modal' => [
        'label' => 'Erweiterte Optionen',
        'icon' => 'fa-cog',
        'fields' => ['description'],  // Diese Felder im Modal
        // Optional: Modal nach einem bestimmten Feld positionieren
        'trigger_after' => 'title'  // Button wird nach 'title'-Feld gezeigt
        // Ohne trigger_after: Button erscheint ganz am Anfang
    ],
    // Optional: Weitere Modals mit trigger_after
    'settings_modal' => [
        'label' => 'Einstellungen',
        'icon' => 'fa-sliders',
        'trigger_after' => 'image',  // Button nach 'image'-Feld
        'fields' => ['alt_text', 'caption']
    ]
]
```

**Modal-Buttons Positionierung:**
- `item_modal` **ohne** `trigger_after`: Button erscheint ganz am Anfang (nach Move-Buttons)
- `item_modal` **mit** `trigger_after`: Button erscheint nach dem angegebenen Feld
- Beliebig viele Modals mit `trigger_after` möglich (z.B. `media_modal`, `settings_modal`, etc.)
- Alle Modal-Namen müssen mit `_modal` enden

---

## Eigene Elemente erstellen

### Verzeichnisstruktur

Elemente können an drei Orten definiert werden (Priorität von oben nach unten):

1. **project Addon**: `redaxo/src/addons/project/elements/`
2. **data Ordner**: `redaxo/data/addons/yform_content_builder/elements/`
3. **Addon selbst**: `redaxo/src/addons/yform_content_builder/elements/`

### Minimales Element

```
elements/
└── quote/
    ├── config.php
    └── templates/
        ├── bootstrap.php
        ├── uikit.php
        └── plain.php
```

### config.php

```php
<?php
return [
    // Pflichtfelder
    'label' => 'Zitat',
    'icon' => 'fa-quote-left',
    
    // Optionale Metadaten
    'description' => 'Hervorgehobenes Zitat mit Autor',
    'category' => 'content',  // Für Gruppierung
    
    // Feldkonfiguration
    'fields' => [
        'quote' => [
            'type' => 'textarea',
            'label' => 'Zitat-Text',
            'rows' => 4
        ],
        'author' => [
            'type' => 'text',
            'label' => 'Autor'
        ],
        'source' => [
            'type' => 'text',
            'label' => 'Quelle',
            'notice' => 'Optional: Buch, Website, etc.'
        ]
    ]
];
```

### Template (templates/bootstrap.php)

```php
<?php
/**
 * @var array $elementData Enthält alle Feldwerte
 */
$quote = $elementData['quote'] ?? '';
$author = $elementData['author'] ?? '';
$source = $elementData['source'] ?? '';

if (empty($quote)) {
    return;
}
?>
<blockquote class="blockquote">
    <p class="mb-0"><?= nl2br(rex_escape($quote)) ?></p>
    <?php if ($author || $source): ?>
        <footer class="blockquote-footer">
            <?= rex_escape($author) ?>
            <?php if ($source): ?>
                <cite title="<?= rex_escape($source) ?>"><?= rex_escape($source) ?></cite>
            <?php endif; ?>
        </footer>
    <?php endif; ?>
</blockquote>
```

### Template (templates/uikit.php)

```php
<?php
$quote = $elementData['quote'] ?? '';
$author = $elementData['author'] ?? '';
$source = $elementData['source'] ?? '';

if (empty($quote)) {
    return;
}
?>
<blockquote class="uk-margin">
    <p class="uk-margin-small-bottom"><?= nl2br(rex_escape($quote)) ?></p>
    <?php if ($author): ?>
        <footer>
            — <?= rex_escape($author) ?>
            <?php if ($source): ?>
                <cite><?= rex_escape($source) ?></cite>
            <?php endif; ?>
        </footer>
    <?php endif; ?>
</blockquote>
```

### Komplexes Element mit Tabs

```php
<?php
return [
    'label' => 'Hero Banner',
    'icon' => 'fa-image',
    'description' => 'Großes Banner mit Bild, Text und Call-to-Action',
    
    // Felder in Tabs gruppieren
    'field_groups' => [
        'content' => [
            'label' => 'Inhalt',
            'icon' => 'fa-file-text-o',
            'fields' => ['headline', 'subline', 'text']
        ],
        'media' => [
            'label' => 'Medien',
            'icon' => 'fa-image',
            'fields' => ['image', 'video']
        ],
        'cta' => [
            'label' => 'Call-to-Action',
            'icon' => 'fa-mouse-pointer',
            'fields' => ['button_text', 'button_link', 'button_style']
        ],
        'design' => [
            'label' => 'Design',
            'icon' => 'fa-paint-brush',
            'fields' => ['overlay_color', 'text_color', 'height']
        ]
    ],
    
    // Alle Felder definieren
    'fields' => [
        'headline' => [
            'type' => 'text',
            'label' => 'Überschrift'
        ],
        'subline' => [
            'type' => 'text',
            'label' => 'Unterzeile'
        ],
        'text' => [
            'type' => 'cke5',
            'label' => 'Text',
            'profile' => 'light'
        ],
        'image' => [
            'type' => 'be_media',
            'label' => 'Hintergrundbild',
            'allowed_types' => 'jpg,jpeg,png,webp'
        ],
        'video' => [
            'type' => 'be_media',
            'label' => 'Hintergrundvideo (optional)',
            'allowed_types' => 'mp4,webm'
        ],
        'button_text' => [
            'type' => 'text',
            'label' => 'Button Text'
        ],
        'button_link' => [
            'type' => 'be_link',
            'label' => 'Button Link'
        ],
        'button_style' => [
            'type' => 'choice',
            'label' => 'Button Style',
            'choices' => [
                'primary' => 'Primär',
                'secondary' => 'Sekundär',
                'outline' => 'Outline'
            ],
            'default' => 'primary'
        ],
        'overlay_color' => [
            'type' => 'color_swatches',
            'label' => 'Overlay',
            'options' => [
                'none' => ['color' => 'transparent', 'label' => 'Kein Overlay'],
                'dark' => ['color' => 'rgba(0,0,0,0.5)', 'label' => 'Dunkel'],
                'light' => ['color' => 'rgba(255,255,255,0.5)', 'label' => 'Hell']
            ],
            'default' => 'dark'
        ],
        'text_color' => [
            'type' => 'choice',
            'label' => 'Textfarbe',
            'choices' => [
                'light' => 'Hell',
                'dark' => 'Dunkel'
            ],
            'default' => 'light'
        ],
        'height' => [
            'type' => 'choice',
            'label' => 'Höhe',
            'choices' => [
                'small' => 'Klein (300px)',
                'medium' => 'Mittel (500px)',
                'large' => 'Groß (700px)',
                'fullscreen' => 'Vollbild'
            ],
            'default' => 'medium'
        ]
    ]
];
```

### Element mit Settings-Modal

Für Felder die selten gebraucht werden, können diese in ein Modal ausgelagert werden:

```php
<?php
return [
    'label' => 'Karte',
    'icon' => 'fa-square',
    
    // Settings-Modal für erweiterte Optionen
    'settings_modal' => [
        'label' => 'Erweiterte Einstellungen',
        'icon' => 'fa-cog',
        'fields' => ['css_class', 'anchor_id', 'animation']
    ],
    
    'fields' => [
        // Haupt-Felder (immer sichtbar)
        'title' => ['type' => 'text', 'label' => 'Titel'],
        'content' => ['type' => 'cke5', 'label' => 'Inhalt'],
        'image' => ['type' => 'be_media', 'label' => 'Bild'],
        
        // Diese Felder erscheinen nur im Modal
        'css_class' => ['type' => 'text', 'label' => 'CSS-Klasse'],
        'anchor_id' => ['type' => 'text', 'label' => 'Anker-ID'],
        'animation' => [
            'type' => 'choice',
            'label' => 'Animation',
            'choices' => [
                '' => 'Keine',
                'fade' => 'Einblenden',
                'slide-up' => 'Nach oben',
                'slide-left' => 'Von links'
            ]
        ]
    ]
];
```

### WICHTIG: Demo-Elemente

Das Verhalten ist über den Modus steuerbar:

- `replace` (Standard): nur eigene Elemente
- `merge`: Demo-Elemente + eigene Elemente

Bei Namensgleichheit gewinnt im `merge`-Modus immer das eigene Element.

### Registrierung via Extension Point (Alternative)

In `boot.php` eines beliebigen AddOns:

```php
rex_extension::register('YFORM_CONTENT_BUILDER_ELEMENT_PATHS', function($ep) {
    $paths = $ep->getSubject();
    $paths[] = rex_addon::get('mein_addon')->getPath('content_elements/');
    return $paths;
});

// Optional: Ladeverhalten steuern
rex_extension::register('YFORM_CONTENT_BUILDER_ELEMENT_MODE', static function(): string {
    return 'merge'; // 'merge' oder 'replace'
});
```

---

## Eigene Feldtypen erstellen

### Interface implementieren

Jeder Feldtyp muss das `ContentBuilderFieldInterface` implementieren:

```php
<?php
namespace FriendsOfREDAXO\YFormContentBuilder\Fields;

interface ContentBuilderFieldInterface
{
    /**
     * Gibt den Feldtyp-Namen zurück
     */
    public static function getType(): string;

    /**
     * Rendert das Formularfeld
     */
    public function render(string $fieldName, array $fieldConfig, $value, array $sliceData = []): void;

    /**
     * Verarbeitet den Wert (optional)
     */
    public function processValue($value, array $fieldConfig);
}
```

### Abstrakte Basisklasse nutzen (empfohlen)

Die `ContentBuilderFieldAbstract` Klasse bietet hilfreiche Methoden:

| Methode | Beschreibung |
|---------|--------------|
| `openFormGroup()` | Öffnet `<div class="form-group">` |
| `closeFormGroup($notice)` | Schließt Form-Group, optional mit Notice |
| `renderLabel($label)` | Rendert Label |
| `generateId($prefix)` | Generiert eindeutige ID |
| `getNestedValue($key, $data)` | Holt Wert aus verschachteltem Array |
| `getNextMediaCounter()` | Globaler Counter für Media-Widgets |
| `getNextLinkCounter()` | Counter für Link-Widgets |

### Beispiel: Einfaches Feld

```php
<?php
namespace FriendsOfREDAXO\YFormContentBuilder\Fields;

use rex_escape;

/**
 * E-Mail-Eingabefeld mit Validierung
 */
class EmailField extends ContentBuilderFieldAbstract
{
    public static function getType(): string
    {
        return 'email';
    }

    public function render(string $fieldName, array $fieldConfig, $value, array $sliceData = []): void
    {
        $label = $fieldConfig['label'] ?? $fieldName;
        $placeholder = $fieldConfig['placeholder'] ?? 'email@beispiel.de';
        $notice = $fieldConfig['notice'] ?? null;

        $this->openFormGroup();
        $this->renderLabel($label);

        echo '<input type="email" class="form-control" ';
        echo 'name="' . rex_escape($fieldName) . '" ';
        echo 'value="' . rex_escape($value) . '" ';
        echo 'placeholder="' . rex_escape($placeholder) . '">';

        $this->closeFormGroup($notice);
    }

    public function processValue($value, array $fieldConfig)
    {
        // E-Mail validieren/bereinigen
        return filter_var($value, FILTER_SANITIZE_EMAIL);
    }
}
```

### Beispiel: Komplexes Feld (Icon-Picker)

```php
<?php
namespace FriendsOfREDAXO\YFormContentBuilder\Fields;

use rex_escape;

/**
 * Icon-Auswahl mit Vorschau
 */
class IconPickerField extends ContentBuilderFieldAbstract
{
    private static array $icons = [
        'fa-home' => 'Home',
        'fa-user' => 'Benutzer',
        'fa-envelope' => 'E-Mail',
        'fa-phone' => 'Telefon',
        'fa-map-marker' => 'Standort',
        'fa-calendar' => 'Kalender',
        'fa-clock-o' => 'Uhr',
        'fa-star' => 'Stern',
        'fa-heart' => 'Herz',
        'fa-check' => 'Haken'
    ];

    public static function getType(): string
    {
        return 'icon_picker';
    }

    public function render(string $fieldName, array $fieldConfig, $value, array $sliceData = []): void
    {
        $label = $fieldConfig['label'] ?? 'Icon';
        $icons = $fieldConfig['icons'] ?? self::$icons;
        $notice = $fieldConfig['notice'] ?? null;

        $groupId = $this->generateId('icon');

        $this->openFormGroup();
        $this->renderLabel($label);

        echo '<div class="icon-picker-group">';
        
        foreach ($icons as $iconClass => $iconLabel) {
            $checked = ($value === $iconClass) ? ' checked' : '';
            $inputId = $groupId . '_' . md5($iconClass);
            $activeClass = $checked ? ' active' : '';

            echo '<div class="icon-picker-item' . $activeClass . '">';
            echo '<input type="radio" name="' . rex_escape($fieldName) . '" ';
            echo 'id="' . $inputId . '" ';
            echo 'value="' . rex_escape($iconClass) . '"' . $checked . '>';
            echo '<label for="' . $inputId . '" title="' . rex_escape($iconLabel) . '">';
            echo '<i class="fa ' . rex_escape($iconClass) . ' fa-2x"></i>';
            echo '</label>';
            echo '</div>';
        }
        
        echo '</div>';

        // CSS für Icon-Picker
        echo '<style>
            .icon-picker-group { display: flex; flex-wrap: wrap; gap: 8px; }
            .icon-picker-item { position: relative; }
            .icon-picker-item input { position: absolute; opacity: 0; }
            .icon-picker-item label { 
                display: flex; align-items: center; justify-content: center;
                width: 50px; height: 50px; border: 2px solid #ddd;
                border-radius: 4px; cursor: pointer; transition: all 0.2s;
            }
            .icon-picker-item label:hover { border-color: #999; }
            .icon-picker-item.active label,
            .icon-picker-item input:checked + label { 
                border-color: #3498db; background: #ecf5fc; 
            }
        </style>';

        $this->closeFormGroup($notice);
    }
}
```

### Feldtyp registrieren

#### Option 1: Direkt registrieren

```php
// In boot.php deines Addons
use FriendsOfREDAXO\YFormContentBuilder\Fields\ContentBuilderFieldRegistry;

if (rex_addon::get('yform_content_builder')->isAvailable()) {
    ContentBuilderFieldRegistry::register(new EmailField());
    ContentBuilderFieldRegistry::register(new IconPickerField());
}
```

#### Option 2: Per Extension Point

```php
rex_extension::register('YFORM_CONTENT_BUILDER_FIELDS', function(rex_extension_point $ep) {
    $fields = $ep->getSubject();
    
    // Neues Feld hinzufügen
    $fields['email'] = new EmailField();
    $fields['icon_picker'] = new IconPickerField();
    
    // Bestehendes Feld überschreiben
    $fields['text'] = new MyEnhancedTextField();
    
    return $fields;
});
```

### Bestehendes Feld erweitern

```php
<?php
namespace MyAddon\Fields;

use FriendsOfREDAXO\YFormContentBuilder\Fields\BeMediaField;

/**
 * Erweitertes Media-Feld mit Drag & Drop
 */
class EnhancedMediaField extends BeMediaField
{
    public static function getType(): string
    {
        return 'be_media';  // Überschreibt das Standard be_media
    }

    public function render(string $fieldName, array $fieldConfig, $value, array $sliceData = []): void
    {
        // Erst Standard-Rendering
        parent::render($fieldName, $fieldConfig, $value, $sliceData);
        
        // Dann eigene Erweiterungen
        echo '<div class="dropzone" data-field="' . rex_escape($fieldName) . '">';
        echo 'Datei hierher ziehen';
        echo '</div>';
    }
}
```

---

## Extension Points

### YFORM_CONTENT_BUILDER_FIELDS

Wird aufgerufen wenn die Feldtypen initialisiert werden.

```php
rex_extension::register('YFORM_CONTENT_BUILDER_FIELDS', function(rex_extension_point $ep) {
    $fields = $ep->getSubject();
    // $fields ist ein Array: ['type' => FieldInstance, ...]
    return $fields;
});
```

### YFORM_CONTENT_BUILDER_ELEMENTS

Wird aufgerufen beim Laden der verfügbaren Elemente.

```php
rex_extension::register('YFORM_CONTENT_BUILDER_ELEMENTS', function(rex_extension_point $ep) {
    $elements = $ep->getSubject();
    
    // Element hinzufügen
    $elements['my_element'] = [
        'label' => 'Mein Element',
        'icon' => 'fa-star',
        '_path' => rex_path::addon('my_addon', 'elements/my_element')
    ];
    
    // Element entfernen
    unset($elements['section']);
    
    return $elements;
});
```

### YFORM_CONTENT_BUILDER_ELEMENT_PATHS

Wird aufgerufen um zusätzliche Pfade für Elemente zu registrieren.

```php
rex_extension::register('YFORM_CONTENT_BUILDER_ELEMENT_PATHS', function($ep) {
    $paths = $ep->getSubject();
    $paths[] = rex_addon::get('mein_addon')->getPath('content_elements/');
    return $paths;
});

### YFORM_CONTENT_BUILDER_ELEMENT_MODE

Steuert, wie Demo- und Custom-Elemente kombiniert werden.

- `replace` (Default): Nur registrierte Custom-Pfade
- `merge`: Demo-Elemente plus Custom-Pfade

```php
rex_extension::register('YFORM_CONTENT_BUILDER_ELEMENT_MODE', static function(): string {
    return 'merge';
});
```
```

---

## Helper-Klassen

### ContentBuilderFieldRegistry

```php
use FriendsOfREDAXO\YFormContentBuilder\Fields\ContentBuilderFieldRegistry;

// Feld registrieren
ContentBuilderFieldRegistry::register(new MyField());

// Feld abrufen
$field = ContentBuilderFieldRegistry::get('text');

// Prüfen ob Feld existiert
if (ContentBuilderFieldRegistry::has('my_custom')) {
    // ...
}

// Alle Felder abrufen
$allFields = ContentBuilderFieldRegistry::getAll();

// Feld rendern (empfohlener Weg)
ContentBuilderFieldRegistry::renderField($fieldName, $fieldConfig, $sliceData);
```

### yform_content_builder_helper

```php
// Prüfen ob Datei ein Bild ist
yform_content_builder_helper::isImage('foto.jpg');  // true

// Prüfen ob Datei ein Video ist
yform_content_builder_helper::isVideo('clip.mp4');  // true

// Verfügbare Elemente abrufen
$elements = yform_content_builder_helper::getAvailableElements();

// Element-Konfiguration laden
$config = yform_content_builder_helper::getElementConfig('text_image');
```

### Frontend-Rendering

```php
use KLXM\YformContentBuilder\Helper as ContentBuilderHelper;

// Aus YForm-Daten
$page = rex_yform_manager_dataset::get(1, 'rex_pages');
$content = $page->getValue('content');

// Mit Bootstrap-Templates rendern
echo ContentBuilderHelper::render($content, 'bootstrap');

// Mit UIkit-Templates rendern
echo ContentBuilderHelper::render($content, 'uikit');

// Mit Plain HTML rendern
echo ContentBuilderHelper::render($content, 'plain');
```

---

## Best Practices

### Element-Templates

1. **Keine Funktionen definieren** - Templates werden mehrfach eingebunden
2. **Immer escapen** - `rex_escape()` für alle Ausgaben
3. **Leere Werte prüfen** - `$elementData['field'] ?? ''`
4. **Framework-agnostisch denken** - Logik im Template, Styling per CSS

### Feldtypen

1. **Basisklasse nutzen** - `ContentBuilderFieldAbstract` vereinfacht vieles
2. **Eindeutige IDs** - `$this->generateId()` verwenden
3. **processValue()** - Für Validierung/Transformation nutzen
4. **Kompatibilität** - CSS im Feld nur wenn nötig, besser in eigenem Stylesheet

### Performance

1. **Lazy Loading** - Felder werden erst bei Bedarf initialisiert
2. **Caching** - Element-Konfigurationen werden gecacht
3. **Minimale Requests** - API-Calls bündeln wo möglich
