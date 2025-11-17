# YForm Content Builder - Modul Verwendung

## Übersicht

Der YForm Content Builder kann auf zwei Arten in REDAXO Modulen verwendet werden:

1. **Einfache API** - Für einzelne Elemente (neu)
2. **Full Builder API** - Für mehrere Elemente mit Drag & Drop

---

## 1. Einfache Element-API (Neu)

Verwende einzelne Content Builder Elemente direkt in Modulen.

### INPUT

```php
<?php
// Einzelnes Element (z.B. Galerie) - REX_VALUE[1] für gespeicherte Daten übergeben
echo yform_content_builder_module::create('gallery', 'REX_VALUE[1]')->renderInput();
?>
```

### OUTPUT

```php
<?php
// Element ausgeben - Daten aus REX_VALUE[1] übergeben
echo yform_content_builder_module::create('gallery', 'REX_VALUE[1]', 'bootstrap')->renderOutput();

// Oder mit UIkit Framework
echo yform_content_builder_module::create('gallery', 'REX_VALUE[1]', 'uikit')->renderOutput();
?>
```

### Verfügbare Elemente

Alle Elemente aus `yform_content_builder/elements/`:
- `gallery` - Bildergalerie mit Layout-Optionen
- `divider` - Trennlinien/Abstandshalter
- `cards` - Karten-Layout
- `hero` - Hero-Section
- `text` - Textblock
- `text_image` - Text mit Bild
- Und alle weiteren Custom Elements

### Element-Struktur

```
yform_content_builder/elements/
  gallery/
    config.php      # Feld-Definition
    element.php     # Template für Frontend
```

**config.php Beispiel:**
```php
<?php
return [
    'label' => 'Galerie',
    'fields' => [
        'headline' => [
            'type' => 'text',
            'label' => 'Überschrift',
        ],
        'layout' => [
            'type' => 'choice',
            'label' => 'Layout',
            'choices' => [
                'grid' => 'Raster',
                'masonry' => 'Mauerwerk',
            ],
            'default' => 'grid',
        ],
    ],
];
```

**element.php Beispiel:**
```php
<?php
// Verfügbare Variablen:
// $data - Array mit Felddaten
// $config - Element-Config

$headline = $data['headline'] ?? '';
$layout = $data['layout'] ?? 'grid';
?>
<div class="gallery gallery--<?= $layout ?>">
    <?php if ($headline): ?>
        <h2><?= rex_escape($headline) ?></h2>
    <?php endif; ?>
</div>
```

### Unterstützte Feld-Typen

- `text` - Einzeiliges Textfeld
- `textarea` - Mehrzeiliges Textfeld
- `choice` - Dropdown mit Optionen
- `repeater` - Wiederholbare Felder (JSON textarea)
- `be_media_enhanced` - Media-Browser (in Entwicklung)

---

## 2. Full Builder API

Verwende den kompletten Content Builder mit mehreren Elementen und Drag & Drop.

### INPUT

```php
<?php
/**
 * Content Builder - Modul Eingabe
 * Nutzt YForm Content Builder für flexible Inhalte
 */

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

### OUTPUT

```php
<?php
/**
 * Content Builder - Modul Ausgabe
 * Rendert die Content Builder Elemente im Frontend
 */

// Wert aus Slice holen
$currentValue = $this->getCurrentSlice()->getValue(1);

// Content Builder erstellen
$contentBuilder = yform_content_builder_module::createWithValue(1, $currentValue, [
    'framework' => 'bootstrap'  // oder 'uikit', 'plain'
]);

// Frontend-Output ausgeben
echo $contentBuilder->renderOutput();
?>
```

### Optionen

```php
[
    'framework' => 'bootstrap',     // CSS Framework: bootstrap, uikit, plain
    'label' => 'Inhalt',            // Label für das Feld
    'description' => '...',         // Beschreibung
    'allowed_elements' => [...],    // Nur bestimmte Elemente erlauben
    'max_elements' => 10,           // Max. Anzahl Elemente
]
```

---

## Vergleich der APIs

| Feature | Einfache API | Full Builder |
|---------|--------------|--------------|
| Ein Element | ✅ | ❌ |
| Mehrere Elemente | ❌ | ✅ |
| Drag & Drop | ❌ | ✅ |
| Einfache Verwendung | ✅ | ❌ |
| Backend-Interface | Einfach | Komplex |
| Daten-Speicherung | JSON in REX_VALUE[1] | JSON mit Elementen-Array |

---

## Wann welche API?

**Einfache API verwenden wenn:**
- Du nur ein Element pro Modul brauchst
- Galerie, Hero-Section, oder einzelne Komponente
- Einfache Backend-Bedienung gewünscht
- Schnelle Integration wichtig

**Full Builder verwenden wenn:**
- Mehrere verschiedene Elemente kombiniert werden sollen
- Drag & Drop Sortierung benötigt wird
- Flexibler Seitenaufbau gewünscht
- Page Builder ähnliche Funktionalität

---

## Beispiel: Einfaches Galerie-Modul

### INPUT
```php
<?php
echo yform_content_builder_module::create('gallery', 'REX_VALUE[1]')->renderInput();
?>
```

### OUTPUT
```php
<?php
echo yform_content_builder_module::create('gallery', 'REX_VALUE[1]')->renderOutput();
?>
```

Das war's! Keine weitere Konfiguration nötig. Das Element lädt automatisch seine Config aus `elements/gallery/config.php`.

---

## Custom Elements erstellen

Erstelle eigene Elemente unter:
```
yform_content_builder/elements/mein_element/
  config.php    # Feld-Definition
  element.php   # Frontend-Template
```

Siehe `CUSTOM_ELEMENTS.md` für Details.

---

## Debugging

**Backend Debug-Modus aktivieren:**
```php
// In boot.php
if (rex::isDebugMode()) {
    rex_logger::factory()->log('debug', 'Content Builder Data: ' . print_r($data, true));
}
```

**Daten im Frontend anzeigen:**
```php
<?php
if (rex::isDebugMode()) {
    dump($data); // Benötigt Debug-Addon
}
?>
```

---

## Migration von alter API

**Alt (verbose):**
```php
yform_content_builder_module::create('gallery')
    ->setVar(1, 'medialist', 'Bilder')
    ->setVar(2, 'select', 'Layout', ['choices' => [...]])
    ->renderOutput([1 => 'REX_VALUE[1]', 2 => 'REX_VALUE[2]']);
```

**Neu (automatisch):**
```php
yform_content_builder_module::create('gallery', 'REX_VALUE[id=1 output=html]')
    ->renderOutput();
```

Die neue API liest die Config automatisch!

---

## Support

- **Dokumentation:** Siehe `ELEMENT_CONFIG.md`, `CUSTOM_ELEMENTS.md`
- **Beispiele:** `examples/` Verzeichnis im Addon
- **GitHub:** Issues und Fragen im Repository
