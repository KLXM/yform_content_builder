# Changelog

Alle wesentlichen Änderungen an diesem Projekt werden hier dokumentiert.

Das Format basiert auf [Keep a Changelog](https://keepachangelog.com/de/1.0.0/).

---

## [1.4.0] – 2026-03-18

### Section als echter Grid-Container (UIkit Page Builder)

Das Section-Element kann nun als **Grid-Wrapper für alle nachfolgende Elemente** fungieren. Damit wird der Content Builder zu einem vollwertigen Page Builder: Statt eines starren Vollbild-Layouts kann jede Section ihre Kind-Elemente automatisch in ein responsives Spaltenlayout aufteilen.

**Neue Felder in der Section:**

| Feld | Funktion |
|------|----------|
| Grid-Modus aktivieren | Schaltet Grid für diese Section an |
| Spaltenbreite (Desktop) | `uk-child-width-X-X@m` – 1 bis 6 Spalten oder Auto |
| Spaltenbreite (Tablet) | `uk-child-width-X-X@s` |
| Spaltenbreite (Mobil) | Standard: volle Breite |
| Grid-Abstand | `uk-grid-small/-medium/-large` oder kein Abstand |
| Match Height | `uk-grid-match` – alle Zellen gleich hoch |
| Trennlinien | `uk-grid-divider` – Linien zwischen den Zellen |

**Technisch:**
- Die Render-Engine (`yform_content_builder_helper`) erkennt Grid-aktivierte Sections und wickelt jedes Kind-Element automatisch in ein `<div>` (Grid-Item)
- Der Grid-Wrapper wird sauber auf- und zugeklappt ohne Änderungen an den einzelnen Elementen
- Rückwärtskompatibel: Ohne aktivierten Grid-Modus verhält sich die Section identisch wie zuvor

**Typischer Anwendungsfall:**
```
[Section: Grid 1/3, match-height]
  [Feature A: Titel + Text]   → automatisch 1/3 Breite
  [Feature B: Titel + Text]   → automatisch 1/3 Breite
  [Feature C: Titel + Text]   → automatisch 1/3 Breite
[/Section]
```

---

## [1.3.0] – 2026-03-18

### Neu: 4 einfache Layout-Elemente

Ergänzung zu den komplexeren Elementen (z. B. Cards) – vier neue Elemente mit schlanker Bedienung und konsistenter Tab-Struktur für Redakteure.

#### `media_text` – Bild & Text
- Bild und Text nebeneinander (Bild links oder rechts)
- Bild-Breite wählbar (1/3 bis 2/3 der Gesamtbreite)
- Optionaler Button mit wählbarem Stil
- Bild-Format: Original, 16:9, 4:3, 1:1, 3:4
- Responsive Bilder via Media Manager (srcset/sizes)

#### `feature_grid` – Feature-Raster
- Repeater-Element ohne Modal-Overhead – alles direkt sichtbar
- Icon per Mediendatei (SVG, PNG) **oder** UIkit-Icon-Name
- Icon-Darstellung: einfach, Kreis oder Quadrat
- Wählbare Spaltenanzahl für Desktop, Tablet und Mobil
- Optional: Box-Stil (kein Rahmen / uk-card-default / uk-card-muted)

#### `hero_banner` – Hero Banner
- Vollbild-Hintergrund (Bild oder Video mit Autoplay/Loop)
- Overlay-Abdunklung wählbar (kein / dunkel / dunkel-stark / hell)
- Inhalt horizontal und vertikal ausrichtbar
- Haupt-Button + optionaler zweiter Button
- Verschiedene Höhen-Optionen inkl. Viewport-Höhe

#### `testimonial` – Testimonials / Zitate
- Repeater ohne Modal – Zitat, Name, Funktion, Foto direkt eingeben
- Optionale Sterne-Bewertung (3–5 ★)
- 3 Stile: Karte / Akzent (farbige Linie) / Minimal
- Fallback-Avatar (Initiale) wenn kein Bild vorhanden
- Semantisch korrekte `<blockquote>`-Auszeichnung

**Alle vier Elemente** haben Templates für UIkit, Bootstrap und Plain HTML sowie eine einheitliche Tab-Struktur: Inhalt – Bild/Design – Design – Sektion.

---

## [1.2.0] – 2026-03-17

### Cards Element – Formular-Redesign

- Neues Spalten-Layout für Felder (col-4, col-6) via `renderFieldRowsGroup()`
- Drei responsive Breiten-Felder statt einem Dropdown mit 22 Optionen:
  - `card_width_mobile` – Breite Mobil
  - `card_width_tablet` – Breite Tablet
  - `card_width` – Breite Desktop
- BC-Kompatibilität: `$stripBp`-Closure entfernt alte `@m`/`@s`-Suffixe aus gespeicherten Werten
- Neues Modal „Verlinkung" mit Button-Stil (`uk-button-text`, default, primary …) und Button-Ausrichtung
- Modal „Layout-Einstellungen" umbenannt (vorher: `item_modal`)
- Animation direkt im Hauptformular sichtbar (neben Layout und Farbe)
- Settings-Modal umbenannt: „Allgemeine Block-Einstellungen"
- Unterstützung für mehrere Modals am selben Trigger-Feld in `RepeaterField.php`

### Cover-Modus – Bug-Fixes

- **media-top/bottom**: Gestreckte Bilder behoben – CSS-Custom-Property `--card-ratio` mit `aspect-ratio` auf Desktop
- Neue CSS-Klasse `.cb-cover-ratio` für kontrolliertes Seitenverhältnis ab 960 px
- Mobil wird das Originalseitenverhältnis beibehalten

---

## [1.1.0] – 2025-XX-XX

### Allgemein

- Neue SVG-Icons für Layout-Auswahl (14 Icons in `assets/icons/`)
- API- und Modul-Loop-Code refaktoriert

### Cards Element

- `radio_image`-Feld für Layout-Auswahl mit SVG-Vorschaubildern
- Responsive Bilder: srcset/sizes dynamisch berechnet
- Canvas durch CSS `aspect-ratio` ersetzt
- `uk-cover` durch reines CSS ersetzt (Safari-Resize-Bugfix)

---

## [1.0.0] – 2025-XX-XX

### Erstveröffentlichung

- Slice-basierter Content Builder für REDAXO YForm
- 11 fertige Elemente: Section, Headline, Divider, Cards, Accordion, Slideshow, Gallery, Downloads, Kontaktformular, Moving Tiles, Pricing Table
- Field Registry: Plugin-System für Feldtypen (text, textarea, checkbox, choice, cke5, be_media, be_link, radio_image, color_swatches, repeater …)
- Templates für UIkit, Bootstrap und Plain HTML
- Repeater-System mit Modal-Gruppierung
- AJAX-API via `rex_api_function`
- Media Manager Integration (responsive Bilder)
- Integration mit `uikit_theme_builder` (dynamische Farben)
