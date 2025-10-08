# Section / Auto-Close Element

## Konzept

Das **Section-Element** ist ein innovatives **Auto-Close-Container-Element**, das visuell Abschnitte im Content definiert, ohne die flache Hierarchie zu brechen.

### Prinzip

```
[Section: Grauer Hintergrund]     ← Öffnet Section
  └─ [Headline]                    ← Gehört zur Section
  └─ [Text & Bild]                 ← Gehört zur Section
  └─ [Cards]                       ← Gehört zur Section
[Section: Blauer Hintergrund]     ← Schließt vorherige, öffnet neue
  └─ [Headline]                    ← Neue Section
  └─ [Divider]                     ← Neue Section
[Headline]                         ← Schließt Section automatisch
```

**Im Output:**

```html
<section class="bg-gray py-5">
    <div class="container">
        <!-- Headline -->
        <!-- Text & Bild -->
        <!-- Cards -->
    </div>
</section>

<section class="bg-blue py-5">
    <div class="container">
        <!-- Headline -->
        <!-- Divider -->
    </div>
</section>

<!-- Headline (außerhalb Section) -->
```

## Flache vs. Verschachtelte Hierarchie

### ❌ Traditionell (verschachtelt):

```json
[
  {
    "type": "section",
    "children": [
      {"type": "headline", "data": {...}},
      {"type": "text_image", "data": {...}}
    ]
  }
]
```

**Probleme:**
- Komplexe Datenstruktur
- Schwierig zu bearbeiten
- Drag & Drop kompliziert
- Tiefe Verschachtelung

### ✅ Auto-Close (flach):

```json
[
  {"type": "section", "data": {...}},
  {"type": "headline", "data": {...}},
  {"type": "text_image", "data": {...}},
  {"type": "section", "data": {...}},
  {"type": "headline", "data": {...}}
]
```

**Vorteile:**
- ✅ Flache, einfache Struktur
- ✅ Einfaches Drag & Drop
- ✅ Klare Logik
- ✅ Visuell trotzdem gruppiert

## Verwendung

### Backend

Im Content Builder erscheint das Section-Element **visuell hervorgehoben**:

```
┌─────────────────────────────────────────┐
│ [Section: Grauer Hintergrund]          │ ← Blau umrandet
│ ╔═══════════════════════════════════╗  │
│ ║  [Headline]                       ║  │ ← Eingerückt
│ ║  [Text & Bild]                    ║  │ ← Eingerückt
│ ╚═══════════════════════════════════╝  │
└─────────────────────────────────────────┘
```

**Visuelle Hinweise:**
- Section-Element: Blaue Border, hellblauer Hintergrund
- Zugehörige Elemente: 30px eingerückt, linke Border
- Deutlich erkennbare Gruppierung

### Frontend

Im Frontend werden Sections korrekt geöffnet und geschlossen:

```php
use KLXM\YformContentBuilder\Helper;

$page = rex_yform_manager_dataset::get(1, 'rex_pages');
echo Helper::render($page->getValue('content'), 'bootstrap');
```

**Output:**

```html
<section class="bg-light py-5">
    <div class="container">
        <h2>Headline</h2>
        <!-- Text & Bild -->
    </div>
</section>

<section class="bg-dark text-white py-7">
    <div class="container">
        <h2>Neue Section</h2>
    </div>
</section>
```

## Auto-Close-Logik

### Regel 1: Section schließt vorherige Section

```
[Section A]
  └─ Element 1
  └─ Element 2
[Section B]  ← Schließt Section A automatisch
  └─ Element 3
```

### Regel 2: Normales Element schließt Section am Ende

```
[Section A]
  └─ Element 1
  └─ Element 2
[Element 3]  ← Schließt Section A
```

### Regel 3: Section wird am Ende automatisch geschlossen

```
[Section A]
  └─ Element 1
  └─ Element 2
[END]  ← Section A wird geschlossen
```

## Konfiguration

### Verfügbare Optionen

```php
'background_color' => [
    'none' => 'Keine',
    'light' => 'Hell (Grau)',
    'dark' => 'Dunkel',
    'primary' => 'Primary',
    'secondary' => 'Secondary',
    'muted' => 'Gedämpft',
    'white' => 'Weiß',
]

'padding_top' / 'padding_bottom' => [
    'none' => 'Kein',
    'small' => 'Klein',
    'medium' => 'Mittel',
    'large' => 'Groß',
    'xlarge' => 'Extra groß'
]

'container' => [
    'container' => 'Container (max-width)',
    'container-fluid' => 'Container Fluid (100%)',
    'none' => 'Kein Container'
]

'text_align' => [
    '' => 'Standard',
    'left' => 'Links',
    'center' => 'Zentriert',
    'right' => 'Rechts'
]
```

### Zusätzlich

- **label**: Interne Bezeichnung (nicht sichtbar)
- **background_image**: Hintergrundbild statt Farbe
- **custom_class**: Eigene CSS-Klasse
- **custom_id**: Eigene ID für Anker-Links

## Templates

### Bootstrap

```php
<section class="bg-light py-5">
    <div class="container">
        <!-- Content -->
    </div>
</section>
```

**Klassen:**
- `bg-light`, `bg-dark`, `bg-primary`, etc.
- `py-0`, `py-3`, `py-5`, `py-7`, `py-9`
- `pt-*`, `pb-*` für unterschiedliche Paddings
- `text-left`, `text-center`, `text-right`

### UIkit

```php
<section class="uk-section uk-section-muted">
    <div class="uk-container">
        <!-- Content -->
    </div>
</section>
```

**Klassen:**
- `uk-section-muted`, `uk-section-primary`, `uk-section-secondary`
- `uk-section-small`, `uk-section`, `uk-section-large`, `uk-section-xlarge`
- `uk-background-cover` für Hintergrundbilder
- `uk-text-left`, `uk-text-center`, `uk-text-right`

### Plain

```php
<section class="cb-section cb-section-bg-light cb-section-pt-medium cb-section-pb-medium">
    <div class="cb-container">
        <!-- Content -->
    </div>
</section>
```

**Klassen:**
- `cb-section-bg-{color}`
- `cb-section-pt-{size}`, `cb-section-pb-{size}`
- `cb-text-{align}`

## Beispiele

### Beispiel 1: Hero + Content + CTA

```json
[
  {
    "type": "section",
    "data": {
      "label": "Hero",
      "background_color": "primary",
      "padding_top": "xlarge",
      "padding_bottom": "xlarge",
      "text_align": "center"
    }
  },
  {
    "type": "headline",
    "data": {"text": "Willkommen", "level": "h1"}
  },
  {
    "type": "section",
    "data": {
      "label": "Content",
      "background_color": "white",
      "padding_top": "large",
      "padding_bottom": "large"
    }
  },
  {
    "type": "text_image",
    "data": {...}
  },
  {
    "type": "cards",
    "data": {...}
  },
  {
    "type": "section",
    "data": {
      "label": "CTA",
      "background_color": "dark",
      "padding_top": "medium",
      "padding_bottom": "medium",
      "text_align": "center"
    }
  },
  {
    "type": "headline",
    "data": {"text": "Jetzt loslegen!"}
  }
]
```

**Output:**

```html
<section class="bg-primary text-white py-9 text-center">
    <div class="container">
        <h1>Willkommen</h1>
    </div>
</section>

<section class="bg-white py-7">
    <div class="container">
        <!-- Text & Bild -->
        <!-- Cards -->
    </div>
</section>

<section class="bg-dark text-white py-5 text-center">
    <div class="container">
        <h2>Jetzt loslegen!</h2>
    </div>
</section>
```

### Beispiel 2: Parallax-Effekt

```json
[
  {
    "type": "section",
    "data": {
      "label": "Parallax Hero",
      "background_image": "hero-bg.jpg",
      "padding_top": "xlarge",
      "padding_bottom": "xlarge",
      "text_align": "center",
      "custom_class": "parallax-section"
    }
  },
  {
    "type": "headline",
    "data": {"text": "Scroll-Effekt", "level": "h1"}
  }
]
```

**Custom CSS:**

```css
.parallax-section {
    background-attachment: fixed;
    background-size: cover;
    position: relative;
}
.parallax-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
}
.parallax-section .container {
    position: relative;
    z-index: 1;
    color: white;
}
```

## Technische Details

### Helper-Methode

Die `yform_content_builder_helper::render()` Methode behandelt Auto-Close automatisch:

```php
public static function render(string $jsonContent, string $framework): string
{
    // ...
    
    foreach ($slices as $index => $slice) {
        $isSection = ($slice['type'] === 'section');
        
        if ($isSection) {
            // Vorherige Section schließen
            if ($openSection) {
                $output .= self::renderSectionClose($framework);
            }
            
            // Neue Section öffnen
            $output .= self::renderSlice($slice, $framework, 'open');
            $openSection = true;
        } else {
            // Normales Element
            $output .= self::renderSlice($slice, $framework);
        }
    }
    
    // Offene Section am Ende schließen
    if ($openSection) {
        $output .= self::renderSectionClose($framework);
    }
}
```

### Template-Variable `$closeType`

Section-Templates erwarten die Variable `$closeType`:

- **`'open'`**: Nur Section öffnen (`<section><div class="container">`)
- **`'close'`**: Nur Section schließen (`</div></section>`)
- **`null`**: Komplette Section (Fallback)

```php
if (isset($closeType)) {
    if ($closeType === 'close') {
        echo '    </div></section>';
        return;
    }
    
    if ($closeType === 'open') {
        echo '<section class="..."><div class="container">';
        return;
    }
}
```

## Best Practices

### 1. Sinnvolle Gruppierung

✅ **Gut:**

```
[Section: Hero]
  └─ Headline
  └─ Text
[Section: Features]
  └─ Cards
[Section: CTA]
  └─ Headline
```

❌ **Schlecht:**

```
[Section]
  └─ Headline
[Section]
  └─ Text
[Section]
  └─ Cards
```

### 2. Konsistente Abstände

Nutze definierte Padding-Größen statt Custom CSS:

```
Hero: xlarge
Content: large
Features: medium
Footer: medium
```

### 3. Aussagekräftige Labels

Labels helfen bei der Orientierung im Backend:

```
✅ "Hero-Bereich mit Bild"
✅ "Feature-Showcase"
✅ "Call-to-Action Footer"

❌ "Section 1"
❌ "Grauer Bereich"
```

### 4. Custom IDs für Anker

```json
{
  "type": "section",
  "data": {
    "label": "Kontakt",
    "custom_id": "kontakt",
    "background_color": "light"
  }
}
```

**Ermöglicht:**

```html
<a href="#kontakt">Zum Kontaktbereich</a>
```

## Troubleshooting

### Section wird nicht geschlossen

**Problem:** Im Output fehlt `</section>`

**Lösung:** Helper-Methode prüfen:

```php
// Am Ende MUSS stehen:
if ($openSection) {
    $output .= self::renderSectionClose($framework);
}
```

### Elemente nicht eingerückt im Backend

**Problem:** Visuelle Gruppierung fehlt

**Lösung:** CSS prüfen:

```css
.content-builder-slice.in-section {
    margin-left: 30px;
    border-left: 3px solid #3c8dbc;
}
```

### Container fehlt

**Problem:** Section hat keinen inneren Container

**Lösung:** Template prüfen:

```php
if ($container !== 'none') {
    echo '<div class="container">';
}
```

## Zusammenfassung

**Section-Element:**

- 🎯 **Auto-Close**: Nächste Section schließt vorherige
- 📊 **Flache Hierarchie**: Keine Verschachtelung
- 👁️ **Visuell erkennbar**: Einrückung im Backend
- 🎨 **Gestaltung**: Hintergrund, Abstände, Ausrichtung
- 🖼️ **Bilder**: Hintergrundbilder mit Cover
- 🔗 **Anker**: Custom IDs für Navigation
- 📱 **Responsive**: Container steuern Breite

**Made with 🤖 by GitHub Copilot**

---

**Version**: 1.0.0  
**Feature seit**: Oktober 2025
