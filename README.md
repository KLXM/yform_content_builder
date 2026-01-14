# YForm Content Builder

Slice-based Content Builder für REDAXO YForm - Erstelle flexible, wiederverwendbare Content-Elemente mit professioneller Media-Verwaltung, Grid-Layouts und intuitivem Workflow.

## ✨ Features

### 🎯 **Content-Elemente**
- **10 fertige Elemente**: Section, Text & Bild, Accordion, Headline, Divider, Cards, Slideshow, Media Showcase, Gallery, Kontaktformular
- **Professional Media Widget**: 16:9 Preview-Container, object-fit: contain, globaler Counter für eindeutige IDs
- **Professional Gallery**: Grid/Masonry Layout, Mixed Media (Bilder + Videos), Responsive Design
- **Auto-Close Sections**: Visueller Container für Abschnitte ohne verschachtelte Hierarchie

### 🚀 **Workflow & UX**
- **Click-to-Edit**: Intuitives Bearbeiten per Edit-Button  
- **⬆️⬇️ Pfeil-Sortierung**: Zuverlässiges Verschieben von Elementen nach oben/unten
- **Modal-Gruppierung**: Übersichtliche Formulare durch Settings-Modals
- **AJAX Forms**: Dynamisches Laden und Speichern ohne Page-Reload
- **Responsive Backend**: Touch-optimierte Controls für mobile Nutzung

### 🎨 **Design & Templates**
- **Framework-agnostic**: Templates für Bootstrap 3, UIkit3 und Plain HTML
- **Responsive Design**: Mobile-optimierte Layouts und Touch-Controls
- **Professional Media Preview**: Kompakte Vorschau mit 200x120px, object-fit: contain
- **Section Styling**: Visuelle Container mit Labels, Hintergrundfarben und -bildern

### 🎨 **Visuelle Feldtypen**
- **`radio_image`**: Layout-Auswahl mit SVG-Vorschaubildern (z.B. Media oben/unten/links/rechts/Overlay)
- **`color_swatches`**: Farbauswahl mit visuellen Farbfeldern (wie MForm RadioColorField)
- **Integration mit uikit_theme_builder**: Dynamische Farben aus DomainContext

### 🔧 **Developer Experience**
- **Element-Filter**: Kontrolle welche Elemente pro Feld verfügbar sind (Multiselect)
- **Settings-Modals**: Komplexe Optionen in übersichtlichen Modal-Dialogen
- **CKE5 Integration**: REDAXO's CKEditor 5 nahtlos integriert
- **Custom Media Widget**: Eigene Preview-Logik unabhängig von REDAXO Core, funktioniert in YForm und Modulen
- **Repeater-System**: Flexible Listen mit Add/Delete/Move-Funktionen
- **Linkmap-Widget**: Vollständige REDAXO Linkmap-Integration
- **Media Manager Integration**: Automatische Bild-URLs via `rex_media_manager::getUrl()`

### 🏗️ **Architecture**
- **Element-Discovery**: Automatisches Laden aller Elemente aus `/elements/` Verzeichnis
- **Custom Elements**: Eigene Elemente via Extension Point oder `project/elements/`
- **Nested Data Structure**: Intelligente Verarbeitung verschachtelter Array-Daten
- **Framework Templates**: Automatische Template-Auswahl (Bootstrap/UIkit/Plain)
- **Production Ready**: Vollständig getestet mit echten Use Cases

## 🚀 Highlights

### Visuelle Feldtypen

#### Layout-Auswahl mit SVG-Vorschau (`radio_image`)
```php
'layout' => [
    'type' => 'radio_image',
    'label' => 'Layout',
    'options' => [
        'media-top' => ['image' => 'data:image/svg+xml;base64,...', 'label' => 'Medium oben'],
        'media-bottom' => ['image' => 'data:image/svg+xml;base64,...', 'label' => 'Medium unten'],
        'media-left' => ['image' => 'data:image/svg+xml;base64,...', 'label' => 'Medium links'],
        'media-right' => ['image' => 'data:image/svg+xml;base64,...', 'label' => 'Medium rechts'],
        'media-overlay' => ['image' => 'data:image/svg+xml;base64,...', 'label' => 'Overlay']
    ],
    'default' => 'media-top'
]
```

#### Farbauswahl mit Swatches (`color_swatches`)
```php
'card_style' => [
    'type' => 'color_swatches',
    'label' => 'Karten-Farbe',
    'options' => [
        'uk-card-default' => ['color' => '#ffffff', 'label' => 'Default (Weiß)'],
        'uk-card-primary' => ['color' => '#1e87f0', 'label' => 'Primary'],
        'uk-card-secondary' => ['color' => '#222222', 'label' => 'Secondary']
    ],
    'default' => 'uk-card-default'
]
```

### Integration mit uikit_theme_builder

Wenn das `uikit_theme_builder` Addon installiert ist, werden Farben automatisch aus dem Theme geladen:

```php
// In config.php eines Elements
if ($hasUikitThemeBuilder && class_exists('UikitThemeBuilder\DomainContext')) {
    $cardStyleColors = \UikitThemeBuilder\DomainContext::getCardStyleOptions();
    $backgroundColors = \UikitThemeBuilder\DomainContext::getBackgroundOptions();
}
```

### Eigene Media-Preview Implementation
**Unabhängig von REDAXO Core** - Eigene Preview-Logik die überall funktioniert:
- **Kompakte Vorschau** mit 200x120px maximaler Größe
- **Object-fit: contain** für vollständige Medien ohne Beschnitt
- **Immer sichtbar** - keine Hover-Tricks mehr
- **Globaler Counter** via $GLOBALS für eindeutige REX_MEDIA IDs
- **Funktioniert in Modulen** - auch mit mehreren Medien pro Modul

### Pfeil-Sortierung
Zuverlässiges Verschieben ohne Drag & Drop:
```
⬆️ Nach oben    ✏️ Bearbeiten    🗑️ Löschen
⬇️ Nach unten
```
- Visuelles Feedback (blaues Highlight)
- Automatische Index-Updates
- Section-Klassen bleiben korrekt

### Innovatives Tab-System
Komplexe Elemente nutzen **Tab-Gruppierung** für übersichtliche Formulare:

```
┌────────────────────────────────┐
│ 📄 Inhalt  🔗 Link  🎨 Design  │ ← Tabs mit Icons!
├────────────────────────────────┤
│ [Formularfelder...]            │
└────────────────────────────────┘
```

### Intelligent Nested Data
Das System versteht **verschachtelte Strukturen** und speichert Repeater-Daten korrekt:

```javascript
// Input: "items[0][title]" = "Mein Titel"
// Gespeichert als: {"items": [{"title": "Mein Titel"}]}
```

## � **Erweiterte Features**
### Cards Grid Pro Element
- **5 Layout-Optionen**: Media oben, unten, links, rechts, Overlay
- **Visuelle Layout-Auswahl**: SVG-Vorschaubilder für intuitive Bedienung
- **Visuelle Farbauswahl**: Farbfelder statt Dropdown
- **Section-Hintergrundbild**: Optionales Hintergrundbild für die Card-Sektion
- **Media Manager**: Automatische Bild-URLs via `rex_media_manager::getUrl('content_card', $image)`
- **DomainContext-Integration**: Dynamische Farben aus uikit_theme_builder
### Media Showcase Element
- **Multi-Format Support**: Bilder (jpg, png, webp) und Videos (mp4, webm)  
- **Aspect Ratio Control**: 16:9, 4:3, 1:1, 21:9, Portrait-Modi und Auto
- **Video Controls**: Autoplay, Muted, Controls ein/aus
- **Enhanced Media Browser**: Typ-Filter für bessere Medien-Auswahl

### Gallery Element  
- **Layout-Optionen**: Grid (gleichmäßig) und Masonry (Pinterest-Style)
- **Flexible Spalten**: 2-5 Spalten konfigurierbar
- **Mixed Media**: Bilder und Videos in einer Gallery
- **Responsive Design**: Automatische Anpassung an verschiedene Bildschirmgrößen

### Kontaktformular Element (NEU)
Professionelles Kontaktformular mit PHPMailer-Integration:

- **Tab-basierte Konfiguration**: 5 Tabs (Formular-Felder, E-Mail, Design, Bestätigung, Sektion)
- **12 Feldtypen**: Text, E-Mail, Telefon, Textarea, Select, Checkbox, Radio, Hidden, Fieldset, Fieldset-Ende, Zwischenüberschrift, Trennlinie
- **SQL-Optionen**: Dynamische Select/Radio-Optionen aus Datenbank (`SELECT id AS value, name AS label FROM tabelle`)
- **Erweiterte Validierung**:
  - **Vorgefertigte Typen**: IBAN, BIC, PLZ (DE/AT/CH), Telefon, URL, Datum, Uhrzeit, Zahlen, Buchstaben
  - **Wertevergleiche**: `{{plz}} < {{99000}}`, `{{alter}} >= {{18}}`
  - **Längenprüfung**: Mindest- und Maximallänge
  - **Regex**: Eigene Muster
- **Spam-Schutz**: Honeypot-Feld und/oder Zeit-Check
- **Bestätigungs-E-Mail**: Automatische Kopie an Absender mit anpassbarem Intro/Footer
- **4 Layout-Optionen**: Standard, Horizontal, Floating Labels, Kompakt
- **Privacy-Checkbox**: Mit Linkmap-Verknüpfung zur Datenschutzseite
- **Backend-Vorschau**: Sicherer Preview ohne Formular-Interferenz

**SQL-Optionen Beispiele:**
```sql
-- Kategorien aus REDAXO
SELECT id AS value, name AS label FROM rex_category WHERE status = 1 ORDER BY name

-- Custom Tabelle
SELECT code AS value, bezeichnung AS label FROM rex_laender ORDER BY bezeichnung
```

**Validierungs-Beispiele:**
| Typ | Parameter | Beschreibung |
|-----|-----------|-------------|
| `iban` | - | DE89370400440532013000 |
| `plz_de` | - | 5-stellige deutsche PLZ |
| `min_length` | `10` | Mindestens 10 Zeichen |
| `compare` | `{{plz}} < {{99000}}` | PLZ-Bereich prüfen |
| `regex` | `^[A-Z]{3}$` | Eigenes Muster |

### Enhanced Workflow
- **Settings-Modals**: Erweiterte Optionen in übersichtlichen Dialogen  
- **Move-Button System**: Zuverlässige Sortierung mit ⬆️⬇️ Buttons
- **Enhanced Media Browser**: Moderne Overlay-UI mit Typ-Filtering
- **Intelligent Forms**: Automatische Feld-Gruppierung und -Organisation

## 📦 Installation

1. Addon in `/redaxo/src/addons/yform_content_builder/` entpacken
2. Im REDAXO-Backend unter "Addons" installieren und aktivieren
3. Sicherstellen, dass YForm (>= 4.0) installiert ist
4. Optional: Blocks Addon für Sortable.js (sonst manuell einbinden)

## 🔧 Anforderungen

- REDAXO >= 5.15
- YForm >= 4.0
- jQuery (im REDAXO Backend vorhanden)
- Bootstrap 3 (REDAXO Backend)
- Font Awesome (für Icons)
- Optional: Blocks Addon (für Sortable.js)

## 📝 Verwendung

### Im YForm Tablemanager

1. Neue Spalte erstellen
2. Feldtyp wählen: **Content Builder**
3. Framework wählen (bootstrap/uikit/plain)
4. **Optional**: Erlaubte Elemente auswählen (leer = alle Elemente erlaubt)
5. Speichern

**Element-Filter:**
Mit dem Multiselect-Feld "Erlaubte Elemente" kannst du steuern, welche Content-Elemente für dieses Feld verfügbar sein sollen. Praktisch wenn du z.B. nur Headlines und Divider erlauben möchtest, aber keine komplexen Cards oder Accordions.

### Frontend-Ausgabe

```php
use KLXM\YformContentBuilder\Helper as ContentBuilderHelper;

// Daten aus YForm-Tabelle holen
$page = rex_yform_manager_dataset::get(1, 'rex_my_pages');
$contentData = $page->getValue('page_content');

// Content rendern - Framework explizit wählen
echo ContentBuilderHelper::render($contentData, 'bootstrap');

// Oder Tailwind nutzen (lädt templates/tailwind.php)
echo ContentBuilderHelper::render($contentData, 'tailwind');
```

## 🎨 Frameworks & Templates

Das Addon ist **Framework-agnostic**. Das bedeutet, es ist ihm egal, welches CSS-Framework du nutzt. Es lädt einfach die passende Template-Datei.

### Backend vs. Frontend

Du kannst für das Backend (Preview) und das Frontend unterschiedliche Frameworks nutzen.

1.  **Backend Preview**: Wird in der YForm-Felddefinition eingestellt ("Framework").
    *   **Default**: `bootstrap` (da das REDAXO Backend auf Bootstrap basiert).
    *   **Empfehlung**: Lasse dies auf `bootstrap`, damit die Vorschau im Backend sauber aussieht, auch wenn du im Frontend Tailwind nutzt.
    *   **Custom**: Du kannst auch `tailwind` wählen, wenn du z.B. Tailwind-CSS im Backend lädst.

2.  **Frontend Output**: Wird beim Aufruf von `ContentBuilderHelper::render($data, 'framework')` festgelegt.
    *   Hier hast du die volle Freiheit: `bootstrap`, `uikit`, `tailwind`, `foundation`, etc.

### Template-Struktur

Das System sucht automatisch nach der Datei: `elements/{element}/templates/{framework}.php`.

```text
elements/
└── hero/
    ├── config.php
    └── templates/
        ├── bootstrap.php   <-- Wird geladen bei render($data, 'bootstrap')
        ├── uikit.php       <-- Wird geladen bei render($data, 'uikit')
        ├── tailwind.php    <-- Wird geladen bei render($data, 'tailwind')
        └── plain.php       <-- Fallback
```

## 🎯 Verfügbare Elemente

### 1. Section / Container (Auto-Close)
**Innovativ**: Definiert visuelle Abschnitte mit Hintergrund, ohne die flache Hierarchie zu brechen.

- **Auto-Close**: Nächste Section schließt vorherige automatisch
- **Visuell gruppiert**: Eingerückte Darstellung im Backend
- **Hintergründe**: Farben oder Bilder
- **Abstände**: 5 Padding-Stufen (none bis xlarge)
- **Container**: max-width, fluid oder kein Container
- **Anker-IDs**: Für Navigation und Scroll-Links

**Siehe**: `DEV.md` für Details

### 2. Text & Bild
Flexibles Element für Text-Bild-Kombinationen mit 4 Layouts, CKE5, Bildverhältnissen, Links (extern/intern), Farben und Spacing.

### 3. Accordion / Tabs
Aufklappbare Inhaltsblöcke oder Tab-Navigation mit 4 Styles, Icons und unbegrenzt Items.

### 4. Headline
Überschrift-Element mit H1-H6, 3 Größen, Ausrichtung, 7 Farben, optionaler Unterstreichung und Links.

### 5. Divider
Trennlinien mit 9 Styles inkl. **animiertem Scroll-Chevron**, Icons, Text und Farbverlauf.

### 6. Cards Grid
UIkit-inspiriertes Grid mit Match Height, 4 Card-Styles, responsive Spalten und CKE5.

### 6. Weitere Elemente
Einfach neue Elemente in `/elements/` erstellen - automatisch verfügbar!

## 🏗️ Eigenes Element erstellen

### Minimales Beispiel

**Struktur:**
```
/elements/quote/
  ├── config.php
  └── templates/
      └── bootstrap.php
```

**config.php:**
```php
<?php
return [
    'label' => 'Zitat',
    'icon' => 'fa-quote-left',
    'description' => 'Hervorgehobenes Zitat',
    'fields' => [
        'quote' => ['type' => 'textarea', 'label' => 'Zitat-Text'],
        'author' => ['type' => 'text', 'label' => 'Autor']
    ]
];
```

**templates/bootstrap.php:**
```php
<?php
$quote = $elementData['quote'] ?? '';
$author = $elementData['author'] ?? '';
?>
<blockquote>
    <p><?= nl2br(rex_escape($quote)) ?></p>
    <?php if ($author): ?>
        <footer><?= rex_escape($author) ?></footer>
    <?php endif; ?>
</blockquote>
```

Fertig! Element ist sofort verfügbar. 🚀

### ⚠️ Template Best Practices

**Wichtig:** Vermeide das Definieren von PHP-Funktionen direkt in den Template-Dateien (z.B. `function myHelper() { ... }`). Da Templates mehrfach eingebunden werden können (z.B. in Schleifen oder bei mehreren Blöcken des gleichen Typs), führt dies zu einem **"Cannot redeclare function"** Fatal Error.

**Falsch:**
```php
// ❌ Führt zu Fehler bei mehrfacher Verwendung
function formatPrice($price) {
    return number_format($price, 2, ',', '.');
}
echo formatPrice($price);
```

**Richtig:**
Nutze stattdessen die bereitgestellte Helper-Klasse `yform_content_builder_helper` oder anonyme Funktionen (Closures), wenn die Logik nur lokal benötigt wird.

```php
// ✅ Nutzung der Helper-Klasse
if (yform_content_builder_helper::isImage($file)) { ... }

// ✅ Oder anonyme Funktion (Closure)
$formatPrice = function($price) {
    return number_format($price, 2, ',', '.');
};
echo $formatPrice($price);

// ✅ Oder mit function_exists prüfen (weniger elegant)
if (!function_exists('formatPrice')) {
    function formatPrice($price) {
        return number_format($price, 2, ',', '.');
    }
}
echo formatPrice($price);
```

### 💡 Architektur-Konzept: Custom vs. Demo Elemente

**"Exclusive OR" Prinzip für maximale Kontrolle**

Das Addon folgt einer strikten Philosophie für den produktiven Einsatz:

1.  **Demo-Modus**: Solange keine eigenen Elemente registriert sind, lädt das Addon die mitgelieferten Demo-Elemente (Section, Text & Bild, etc.), damit du sofort starten und testen kannst.
2.  **Production-Modus**: Sobald du **auch nur ein einziges eigenes Element** registrierst (z.B. im `project` Addon oder via Extension Point), werden die **Demo-Elemente automatisch deaktiviert**.

**Warum?**
In einem echten Kundenprojekt möchtest du volle Kontrolle. Du willst nicht, dass Updates des Addons plötzlich neue Demo-Elemente in dein sorgfältig kuratiertes Backend spülen. Du definierst exakt, welche Bausteine zur Verfügung stehen.

**Wie nutze ich die Demo-Elemente trotzdem?**
Kopiere einfach die gewünschten Elemente aus `redaxo/src/addons/yform_content_builder/elements/` in deinen eigenen Elements-Ordner (z.B. `redaxo/src/addons/project/elements/`). So werden sie zu "deinen" Elementen und du hast die volle Hoheit über Code und Updates.

## 🎨 Tab-Gruppierung (Advanced)

Für komplexe Elemente mit vielen Feldern:

```php
'field_groups' => [
    'content' => [
        'label' => 'Inhalt',
        'icon' => 'fa-file-text-o',
        'fields' => ['headline', 'text', 'image']
    ],
    'settings' => [
        'label' => 'Einstellungen',
        'icon' => 'fa-cog',
        'fields' => ['layout', 'color']
    ]
],
'fields' => [
    // ... alle Felder
]
```

**Resultat:** Übersichtliches Formular mit Icons! 📄 ⚙️

Siehe: `DEV.md` für Details.

## 📚 Feldtypen

| Typ | Beschreibung |
|-----|--------------|
| `text` | Einzeiliges Textfeld |
| `textarea` | Mehrzeiliges Textfeld |
| `cke5` | CKEditor 5 Rich Text |
| `be_media` | REDAXO Mediapool |
| `be_link` | REDAXO Linkmap |
| `choice` | Select Dropdown |
| `checkbox` | Checkbox |
| `repeater` | Wiederholbare Felder |
| `radio_image` | Layout-Auswahl mit SVG-Vorschaubildern |
| `color_swatches` | Farbauswahl mit visuellen Farbfeldern |

### Repeater-Beispiel

```php
'items' => [
    'type' => 'repeater',
    'label' => 'Elemente',
    'fields' => [
        'title' => ['type' => 'text', 'label' => 'Titel'],
        'content' => ['type' => 'cke5', 'label' => 'Inhalt']
    ]
]
```

## 🗄️ Datenstruktur

Content wird als **JSON-Array** gespeichert:

```json
[
    {
        "element_type": "text_image",
        "data": {
            "headline": "Willkommen",
            "text": "<p>Hallo Welt</p>",
            "image": "header.jpg"
        },
        "slice_id": "slice_abc123"
    }
]
```

## 🔧 API & Helper

```php
use KLXM\YformContentBuilder\Helper as ContentBuilderHelper;

// Content rendern
$html = ContentBuilderHelper::render($jsonData, 'bootstrap');

// Verfügbare Elemente
$elements = ContentBuilderHelper::getAvailableElements();

// Element-Config laden
$config = ContentBuilderHelper::getElementConfig('text_image');
```

## 🐛 Troubleshooting

### Element wird nicht angezeigt
1. Prüfe Ordnerstruktur: `/elements/mein_element/config.php`
2. Valides PHP in `config.php`?
3. Backend-Cache löschen

### CKE5 initialisiert nicht
1. CKE5 Addon installiert?
2. Browser-Konsole prüfen
3. ID muss mit "ck" beginnen

### Linkmap funktioniert nicht
1. Naming: `REX_LINK_X` und `REX_LINK_X_NAME`
2. `deleteREXLink()` verfügbar?

### Repeater-Daten nicht persistent
1. Prüfe `setNestedValue()` in `content-builder.js`
2. Browser-Konsole auf JSON-Fehler prüfen

## 📖 Dokumentation

### 🎯 **Entwickler-Dokumentation**
- **DEV.md** - Umfassende Dokumentation für Entwickler (API, Config, Custom Elements)
- **SCHEMA.md** - JSON Schema Referenz

### 🚀 **Implementierte Features**
- **Enhanced Media Browser**: Typ-Filter für Bilder/Videos, moderne Overlay-UI
- **Settings-Modals**: Erweiterte Optionen in übersichtlichen Dialogen
- **Move-Button System**: Zuverlässige ⬆️⬇️ Pfeil-Sortierung
- **Grid-View Repeater**: Kompakte Darstellung für Gallery-ähnliche Inhalte

## 🗺️ Roadmap

### ✅ **Aktueller Stand**
- [x] **10 Content-Elemente**: Section, Text&Image, Accordion, Headline, Divider, Cards, Slideshow, Media Showcase, Gallery, Kontaktformular
- [x] **Kontaktformular**: PHPMailer, SQL-Optionen, erweiterte Validierung (IBAN, PLZ, Wertevergleiche)
- [x] **Enhanced Media Browser**: Typ-Filter, moderne Overlay-UI, klickbare Platzhalter
- [x] **Settings-Modals**: Erweiterte Optionen in übersichtlichen Dialogen
- [x] **Move-Button System**: Zuverlässige ⬆️⬇️ Sortierung
- [x] **Grid-View Repeater**: Kompakte Darstellung für Gallery-Inhalte

### 🔮 **Geplante Features**
- [ ] Content Builder für normale Module (REX_CONTENT_BUILDER)
- [ ] External Video-Element (YouTube/Vimeo Embeds)
- [ ] Testimonial-Element
- [ ] Timeline-Element
- [ ] Element-Bibliothek (Community)
- [ ] Import/Export für Konfigurationen

## 📊 Stats

```
Lines of Code:     ~5.000
Elements:          10 (Section, Text&Image, Accordion, Headline, 
                     Divider, Cards, Slideshow, Media Showcase, 
                     Gallery, Kontaktformular)
Feldtypen:         10 (text, textarea, choice, checkbox, ckeditor5,
                     be_media, be_media_enhanced, be_link, repeater,
                     radio_image, color_swatches)
Templates:         30 (10 × 3 Frameworks)  
CSS Files:         3 
JS Files:          2 
Features:          Enhanced Media Browser, Settings Modals, 
                   Move Buttons, Grid-View Repeater, SQL-Optionen,
                   Erweiterte Validierung (IBAN, PLZ, Compare)
Dokumentation:     5 MD-Files
Development Time:  Mehrere intensive Sessions 🤖
```

## 📄 Lizenz

MIT License

## 👤 Author

**KLXM Crossmedia / Thomas Skerbis**  
Website: [https://klxm.de](https://klxm.de)


## 🔗 Links

- [REDAXO](https://redaxo.org/)
- [YForm](https://github.com/yakamara/redaxo_yform)
- [Sortable.js](https://sortablejs.github.io/Sortable/)
- [UIkit](https://getuikit.com/)
- [Bootstrap](https://getbootstrap.com/docs/3.4/)
