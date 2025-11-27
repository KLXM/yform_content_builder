# YForm Content Builder

Slice-based Content Builder für REDAXO YForm - Erstelle flexible, wiederverwendbare Content-Elemente mit professioneller Media-Verwaltung, Grid-Layouts und intuitivem Workflow.

## ✨ Features

### 🎯 **Content-Elemente**
- **9 fertige Elemente**: Section, Text & Bild, Accordion, Headline, Divider, Cards, Slideshow, Media Showcase, Gallery
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
- **Professional Media Preview**: Eigene Preview-Implementation mit 16:9 Container, object-fit: contain, immer sichtbar
- **Section Styling**: Visuelle Container mit Labels und Styling-Optionen

### 🔧 **Developer Experience**
- **Element-Filter**: Kontrolle welche Elemente pro Feld verfügbar sind (Multiselect)
- **Settings-Modals**: Komplexe Optionen in übersichtlichen Modal-Dialogen
- **CKE5 Integration**: REDAXO's CKEditor 5 nahtlos integriert
- **Custom Media Widget**: Eigene Preview-Logik unabhängig von REDAXO Core, funktioniert in YForm und Modulen
- **Repeater-System**: Flexible Listen mit Add/Delete/Move-Funktionen
- **Linkmap-Widget**: Vollständige REDAXO Linkmap-Integration

### 🏗️ **Architecture**
- **Element-Discovery**: Automatisches Laden aller Elemente aus `/elements/` Verzeichnis
- **Custom Elements**: Eigene Elemente via Extension Point oder `project/elements/`
- **Nested Data Structure**: Intelligente Verarbeitung verschachtelter Array-Daten
- **Framework Templates**: Automatische Template-Auswahl (Bootstrap/UIkit/Plain)
- **Production Ready**: Vollständig getestet mit echten Use Cases

## 🚀 Highlights

### Eigene Media-Preview Implementation
**Unabhängig von REDAXO Core** - Eigene Preview-Logik die überall funktioniert:
- **16:9 Aspect Ratio Container** mit padding-bottom Technik
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

// Content rendern
echo ContentBuilderHelper::render($contentData, 'bootstrap');
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

**Siehe**: `SECTION_ELEMENT.md` für Details

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

Siehe: `FIELD_GROUPS_TABS.md` für Details.

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
- **ELEMENT_CONFIG.md** - Vollständige config.php Referenz mit allen verfügbaren Feldtypen

### 📚 **Feature-Dokumentation**  
- **FIELD_GROUPS_TABS.md** - Tab-Gruppierung mit Icons
- **ELEMENT_UPDATES.md** - Changelog für Element-Updates
- **NEW_ELEMENTS.md** - Headline, Divider, Cards Dokumentation
- **DEBUG.md** - Debugging-Guide

### 🚀 **Implementierte Features**
- **Enhanced Media Browser**: Typ-Filter für Bilder/Videos, moderne Overlay-UI
- **Settings-Modals**: Erweiterte Optionen in übersichtlichen Dialogen
- **Move-Button System**: Zuverlässige ⬆️⬇️ Pfeil-Sortierung
- **Grid-View Repeater**: Kompakte Darstellung für Gallery-ähnliche Inhalte

## 🗺️ Roadmap

### ✅ **Aktueller Stand**
- [x] **9 Content-Elemente**: Section, Text&Image, Accordion, Headline, Divider, Cards, Slideshow, Media Showcase, Gallery
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
Lines of Code:     ~4.500
Elements:          9 (Section, Text&Image, Accordion, Headline, 
                     Divider, Cards, Slideshow, Media Showcase, Gallery)
Feldtypen:         8 (text, textarea, choice, checkbox, ckeditor5,
                     be_media, be_media_enhanced, be_link, repeater)
Templates:         27 (9 × 3 Frameworks)  
CSS Files:         3 
JS Files:          2 
Features:          Enhanced Media Browser, Settings Modals, 
                   Move Buttons, Grid-View Repeater
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
