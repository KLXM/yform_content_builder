# Custom Elements

## ⚠️ WICHTIG: Demo-Elemente

**Die mitgelieferten Elemente sind NUR Demos!**

Sobald du **eigene Elemente** erstellst (via `project/elements/` oder Extension Point), werden die **Demo-Elemente NICHT mehr geladen**.

➡️ Kopiere benötigte Demo-Elemente in dein `project/elements/` Verzeichnis!

---

## Eigene Elemente hinzufügen

Es gibt 2 einfache Wege, eigene Content-Builder-Elemente zu erstellen:

---

## 1. Im `project` AddOn (Empfohlen) ✅

### Ordnerstruktur:
```
redaxo/src/addons/project/
└── elements/
    └── my_element/
        ├── config.php
        └── templates/
            ├── bootstrap.php
            └── uikit.php (optional)
```

### Beispiel `config.php`:
```php
<?php
return [
    'label' => 'Mein Element',
    'icon' => 'fa fa-star',
    'description' => 'Mein custom Element',
    'fields' => [
        'title' => [
            'type' => 'text',
            'label' => 'Titel'
        ],
        'text' => [
            'type' => 'cke5',
            'label' => 'Text'
        ]
    ]
];
```

### Beispiel `templates/bootstrap.php`:
```php
<?php
$title = $elementData['title'] ?? '';
$text = $elementData['text'] ?? '';
?>
<div class="my-element">
    <?php if ($title): ?>
        <h2><?= rex_escape($title) ?></h2>
    <?php endif; ?>
    <?php if ($text): ?>
        <div><?= $text ?></div>
    <?php endif; ?>
</div>
```

**Vorteile:**
- ✅ Automatisch erkannt
- ✅ Kein Code nötig
- ✅ Projekt-spezifisch
- ⚠️ **Demo-Elemente werden dann NICHT mehr geladen!**

### Demo-Elemente übernehmen:
```bash
# Kopiere benötigte Demo-Elemente
cp -r redaxo/src/addons/yform_content_builder/elements/text_image \
      redaxo/src/addons/project/elements/

cp -r redaxo/src/addons/yform_content_builder/elements/accordion \
      redaxo/src/addons/project/elements/
```

---

## 2. Via Extension Point (für AddOns)

### In `boot.php` eines beliebigen AddOns:

```php
<?php
rex_extension::register('YFORM_CONTENT_BUILDER_ELEMENT_PATHS', function($ep) {
    $paths = $ep->getSubject();
    
    // Eigene Element-Pfade hinzufügen
    $paths[] = rex_addon::get('mein_addon')->getPath('content_elements/');
    
    return $paths;
});
```

**Vorteile:**
- ✅ Mehrere Pfade möglich
- ✅ AddOn-spezifisch
- ✅ Wiederverwendbar
- ⚠️ **Demo-Elemente werden dann NICHT mehr geladen!**

---

## Verhalten

### OHNE custom elements:
```
✅ Demo-Elemente werden geladen
   (yform_content_builder/elements/)
```

### MIT custom elements (project oder Extension Point):
```
✅ NUR deine Custom-Elemente werden geladen
❌ Demo-Elemente werden NICHT geladen
```

**Grund:** Die Demos sind nur Beispiele. Du sollst deine eigenen Elemente bauen!

---

## Demo-Elemente als Basis nutzen

### Schritt 1: Demo kopieren
```bash
cp -r redaxo/src/addons/yform_content_builder/elements/text_image \
      redaxo/src/addons/project/elements/
```

### Schritt 2: Anpassen
Bearbeite `project/elements/text_image/config.php` und Templates nach deinen Wünschen.

### Schritt 3: Fertig!
Dein Element ist jetzt aktiv, Demo-Elemente sind deaktiviert.

---

## Element-Typen

### Verfügbare Feld-Typen:
- `text` - Einzeiliges Textfeld
- `textarea` - Mehrzeiliges Textfeld
- `cke5` - WYSIWYG-Editor
- `choice` - Dropdown/Select
- `checkbox` - Ja/Nein
- `be_media` - Medienpool-Auswahl
- `be_link` - Linkmap-Auswahl
- `repeater` - Wiederholbare Felder

### Repeater mit Modal:
```php
'items' => [
    'type' => 'repeater',
    'label' => 'Items',
    'item_modal' => [
        'label' => 'Erweiterte Optionen',
        'icon' => 'fa-cog',
        'fields' => ['subtitle', 'badge', 'link']
    ],
    'fields' => [
        'title' => [...],
        'text' => [...],
        'subtitle' => [...],  // Im Modal
        'badge' => [...],     // Im Modal
        'link' => [...]       // Im Modal
    ]
]
```

### Settings Modal:
```php
'settings_modal' => [
    'label' => 'Einstellungen',
    'icon' => 'fa-cog',
    'fields' => ['columns', 'gap', 'style']
]
```

---

## Best Practices

### ✅ DO:
- Verwende `rex_escape()` für alle Textausgaben
- CKE5-HTML **nicht** escapen: `<?= $text ?>`
- Prüfe Werte mit `?? ''` oder `!empty()`
- Nutze `project/elements/` für projektspezifische Elemente
- Kopiere benötigte Demo-Elemente in dein Projekt

### ❌ DON'T:
- Demo-Elemente in `yform_content_builder/elements/` NICHT ändern!
- Sie sind nur Beispiele und werden bei Updates überschrieben
- CKE5-HTML nicht escapen: `<?= rex_escape($text) ?>` ❌

---

## Element-Source Info

Jedes Element bekommt ein `_source` Flag:
- `demo` - Nur wenn KEINE custom elements vorhanden
- `custom` - Aus project oder anderem AddOn

Im Code prüfbar:
```php
$elements = $this->getAvailableElements();
if ($elements['text_image']['_source'] === 'demo') {
    // Achtung: Demo-Element!
}
```

---

## Debugging

### Element wird nicht angezeigt?
1. Prüfe Ordnerstruktur: `elements/name/config.php`
2. Prüfe PHP-Syntax in `config.php`
3. Cache leeren: REDAXO Backend → System → Cache löschen

### Template-Fehler?
1. Prüfe Pfad: `templates/bootstrap.php` muss existieren
2. Prüfe PHP-Syntax
3. Prüfe `$elementData` Array-Keys

---

## Beispiel: Vollständiges Custom Element

**Datei: `project/elements/testimonial/config.php`**
```php
<?php
return [
    'label' => 'Testimonial',
    'icon' => 'fa fa-quote-left',
    'description' => 'Kundenstimme mit Bild',
    'fields' => [
        'quote' => [
            'type' => 'textarea',
            'label' => 'Zitat'
        ],
        'author' => [
            'type' => 'text',
            'label' => 'Autor'
        ],
        'author_title' => [
            'type' => 'text',
            'label' => 'Titel/Firma'
        ],
        'image' => [
            'type' => 'be_media',
            'label' => 'Foto'
        ],
        'rating' => [
            'type' => 'choice',
            'label' => 'Bewertung',
            'choices' => [
                '5' => '5 Sterne',
                '4' => '4 Sterne',
                '3' => '3 Sterne'
            ],
            'default' => '5'
        ]
    ]
];
```

**Datei: `project/elements/testimonial/templates/bootstrap.php`**
```php
<?php
$quote = $elementData['quote'] ?? '';
$author = $elementData['author'] ?? '';
$authorTitle = $elementData['author_title'] ?? '';
$image = $elementData['image'] ?? '';
$rating = (int)($elementData['rating'] ?? 5);

$imageSrc = '';
if ($image) {
    $media = rex_media::get($image);
    if ($media) {
        $imageSrc = '/media/' . $image;
    }
}
?>

<div class="testimonial">
    <div class="testimonial-content">
        <?php if ($quote): ?>
            <blockquote class="testimonial-quote">
                <i class="fa fa-quote-left"></i>
                <?= nl2br(rex_escape($quote)) ?>
                <i class="fa fa-quote-right"></i>
            </blockquote>
        <?php endif; ?>
        
        <div class="testimonial-rating">
            <?php for ($i = 0; $i < $rating; $i++): ?>
                <i class="fa fa-star"></i>
            <?php endfor; ?>
        </div>
    </div>
    
    <div class="testimonial-author">
        <?php if ($imageSrc): ?>
            <img src="<?= $imageSrc ?>" alt="<?= rex_escape($author) ?>" class="testimonial-image">
        <?php endif; ?>
        <div class="testimonial-info">
            <?php if ($author): ?>
                <strong><?= rex_escape($author) ?></strong>
            <?php endif; ?>
            <?php if ($authorTitle): ?>
                <span class="text-muted"><?= rex_escape($authorTitle) ?></span>
            <?php endif; ?>
        </div>
    </div>
</div>
```

Fertig! 🎉
