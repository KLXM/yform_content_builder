# YForm Content Builder

Slice-based Content Builder für REDAXO YForm - Erstelle flexible, wiederverwendbare Content-Elemente mit Drag & Drop, Click-to-Edit und Framework-agnostischen Templates.

## ✨ Features

- **6 Content-Elemente**: Text & Bild, Accordion/Tabs, Headline, Divider, Cards Grid - sofort einsatzbereit
- **Click-to-Edit Workflow**: Intuitives Bearbeiten per Edit-Button
- **Drag & Drop**: Sortierbare Elemente mit Sortable.js (aus Blocks Addon)
- **Framework-agnostic**: Templates für Bootstrap 3, UIkit3 und Plain HTML
- **AJAX Forms**: Dynamisches Laden und Speichern ohne Page-Reload
- **CKE5 Integration**: REDAXO's CKEditor 5 nahtlos integriert
- **Custom Media Browser**: Moderner Overlay-basierter Medienbrowser
- **Linkmap-Widget**: Vollständige REDAXO Linkmap-Integration
- **Repeater-Felder**: Dynamische Unterelemente (z.B. Accordion-Items, Cards)
- **Tab-Gruppierung**: Übersichtliche Formulare durch Tab-Organisation mit Icons
- **Element-Discovery**: Automatisches Laden aller Elemente aus `/elements/` Verzeichnis
- **Custom CSS Support**: Eigene Stylesheets für spezielle Elemente (Divider, Cards)
- **Nested Data Structure**: Intelligente Verarbeitung verschachtelter Array-Daten
- **Production Ready**: Vollständig getestet mit echten Use Cases

## 🚀 Highlights

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

### 1. Text & Bild
Flexibles Element für Text-Bild-Kombinationen mit 4 Layouts, CKE5, Bildverhältnissen, Links (extern/intern), Farben und Spacing.

### 2. Accordion / Tabs
Aufklappbare Inhaltsblöcke oder Tab-Navigation mit 4 Styles, Icons und unbegrenzt Items.

### 3. Headline
Überschrift-Element mit H1-H6, 3 Größen, Ausrichtung, 7 Farben, optionaler Unterstreichung und Links.

### 4. Divider
Trennlinien mit 9 Styles inkl. **animiertem Scroll-Chevron**, Icons, Text und Farbverlauf.

### 5. Cards Grid
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

- **FIELD_GROUPS_TABS.md** - Tab-Gruppierung mit Icons
- **ELEMENT_UPDATES.md** - Changelog für Element-Updates
- **NEW_ELEMENTS.md** - Headline, Divider, Cards Dokumentation
- **DEBUG.md** - Debugging-Guide

## 🗺️ Roadmap

- [ ] Content Builder für normale Module (REX_CONTENT_BUILDER)
- [ ] Video-Element (YouTube/Vimeo)
- [ ] Gallery-Element (Lightbox)
- [ ] Testimonial-Element
- [ ] Timeline-Element
- [ ] UIkit Templates für alle Elemente
- [ ] Element-Bibliothek (Community)

## 📊 Stats

```
Lines of Code:     ~4.500
Elements:          6 (erweiterbar)
Feldtypen:         8
Templates:         18 (6 × 3 Frameworks)
CSS Files:         3
JS Files:          2
Dokumentation:     5 MD-Files
Development Time:  1 intensive Session 🤖
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
