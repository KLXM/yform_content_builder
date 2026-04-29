# YForm Content Builder

Slice-based Content Builder für REDAXO YForm und Structure – erstelle flexible, wiederverwendbare Content-Elemente für beliebige CSS-Frameworks.

📖 [API-Dokumentation](API.md) · 🚀 [Tutorial](TUTORIAL.md) · 📋 [Changelog](CHANGELOG.md)

---

## ✨ Features

### Content-Elemente
- **26+ fertige Elemente** sofort einsatzbereit: Section, Text & Bild, Accordion, Cards, Gallery, Hero, Kontaktformular, YForm-Liste, Forcal-Termine, Moving Tiles u. v. m.
- **Repeater-System** – unbegrenzt wiederholbare Feldgruppen (Slider, Listen, Feature Grids …)
- **Online/Offline pro Slice** *(YForm-Variante)* – Abschnitte deaktivieren ohne zu löschen

### Workflow & UX
- **Click-to-Edit** – intuitives Bearbeiten per Edit-Button
- **⬆️⬇️ Pfeil-Sortierung** – zuverlässiges Verschieben von Elementen
- **Tab-Gruppierung** – komplexe Formulare übersichtlich mit Icons und Tabs
- **Settings-Modals** – selten benötigte Optionen ausgeblendet, auf Knopfdruck erreichbar

### Feldtypen
- **14 Feldtypen** inkl. `cke5`, `be_media`, `be_link`, `repeater`, `radio_image`, `color_swatches`, `be_table_select`, `yformpicker`
- **Plugin-System** – eigene Feldtypen via Extension Point registrieren
- **Permission-System** – Felder nach Benutzerrolle ein-/ausblenden

### Design & Templates
- **Framework-agnostic** – Templates für Bootstrap, UIkit und Plain HTML enthalten
- **Eigene Frameworks** – Tailwind, Foundation etc. per `templates/tailwind.php` ergänzen
- **Media Manager Integration** – automatische Bild-URLs via `rex_media_manager::getUrl()`

### Integrationen
- **YForm Table Manager** – Content Builder als Feldtyp direkt in YForm-Tabellen
- **YForm-Listen-Profile** – No-Code-Ausgabe aus beliebigen YForm-Tabellen (News, Produkte, Events …)
- **Forcal-Termine** – Veranstaltungen aus dem forcal-Addon direkt im Content Builder
- **uikit_theme_builder** – dynamische Farben aus dem DomainContext

---

## 📦 Verfügbare Elemente

| Element | Schlüssel | Beschreibung |
|---------|-----------|--------------|
| Section / Container | `section` | Visuelle Abschnitte mit Hintergrundfarbe/-bild, Auto-Close |
| Text & Bild | `media_text` | Text-Bild-Kombinationen, 4 Layouts, CKE5 |
| Accordion | `accordion` | Aufklappbare Inhaltsblöcke / Tab-Navigation |
| Headline | `headline` | H1–H6, Farbe, Ausrichtung, optionaler Link |
| Divider | `divider` | Trennlinien, 9 Styles inkl. animiertem Scroll-Chevron |
| Cards Grid | `cards` | UIkit/Bootstrap-Grid mit Repeater, Farb- und Layout-Auswahl |
| Slideshow | `slideshow` | Bild-/Video-Slideshow |
| Gallery | `gallery` | Grid & Masonry, Mixed Media (Bilder + Videos) |
| Hero Banner | `hero_banner` | Fullscreen-Banner mit Overlay und Call-to-Action |
| Feature Grid | `feature_grid` | Icon-Feature-Liste im Grid |
| Moving Tiles | `moving_tiles` | Parallax-Tiles mit alternierenden Layouts |
| Testimonial | `testimonial` | Zitate mit Autor und Bild |
| Timeline | `timeline` | Zeitstrahl-Element |
| Downloads | `downloads` | Dateiliste aus dem Mediapool |
| Countdown | `countdown` | Countdown bis zu einem Datum |
| Table | `table` | Einfache Tabellen-Ausgabe |
| YForm-Liste | `yform_list` | Datensatz-Listen aus YForm-Tabellen (über Profile) |
| Kontakt-Picker | `contact_picker` | Einzelne Kontakte aus YForm-Profilen auswählen |
| Forcal-Termine | `forcal_list` | Termine aus forcal (Addon vorausgesetzt) |
| Kontaktformular | `doform2026` | PHPMailer-Formular mit Validierung und AJAX |
| Starter Text | `starter_text` | Minimalistisches RichText-Element |
| Starter Headline | `starter_headline` | Reduziertes Headline-Element |
| Starter Media Split | `starter_media_split` | Medium und Text nebeneinander |
| Starter Gallery | `starter_gallery` | Einfache Galerieliste |
| Starter Cards | `starter_cards` | Karten-Liste (Repeater-basiert) |
| Starter Callout | `starter_callout` | Highlight-Box mit Titel, Text und Link |

---

## 🚀 Quick Start

### REDAXO-Modul (Single Element)

```php
// Eingabe (Input-Modul)
<?php
use KLXM\YFormContentBuilder\Module;

echo Module::createByValueId('cards', 1, 'uikit')->renderInput();
```

```php
// Ausgabe (Output-Modul)
<?php
use KLXM\YFormContentBuilder\Module;

echo Module::createByValueId('cards', 1, 'uikit')->renderOutput();
```

### Frontend-Ausgabe (YForm-Feld)

```php
<?php
use KLXM\YFormContentBuilder\Helper;

// 1) Datensatz bereits vorhanden
echo Helper::outputDataset($dataset, 'content_builder', 'uikit');

// 2) Direkt ueber Tabelle + ID
echo Helper::outputDatasetById('rex_pages', 42, 'content_builder', 'uikit');

// 3) Direkt mit Rohwert
echo Helper::outputRaw($dataset->getValue('content_builder'), 'uikit');
```

> Vollständige API-Referenz, alle Feldtypen, Extension Points und Beispiele für eigene Elemente: **[API.md](API.md)**
>
> Schritt-für-Schritt-Einstieg für Entwickler: **[TUTORIAL.md](TUTORIAL.md)**

### Element-i18n (Auto-Load)

Wenn ein Element einen `lang`-Ordner besitzt, werden die Sprachdateien automatisch geladen.

```text
elements/<element>/lang/de_de.lang
elements/<element>/lang/en_gb.lang
```

In `config.php` kannst du dann z. B. konsistent mit `Helper::elementTranslator('<element>')` arbeiten.

---

## 📋 Anforderungen

- REDAXO >= 5.15
- YForm >= 4.0
- PHP >= 8.1

---

## 📦 Installation

1. Addon in `/redaxo/src/addons/yform_content_builder/` entpacken
2. Im REDAXO-Backend unter **Addons** installieren und aktivieren
3. Optional: Neue Module via **YForm Content Builder → Module** generieren

---

## 📄 Lizenz

Copyright © KLXM Crossmedia GmbH · Alle Rechte vorbehalten.

Diese Software ist proprietär und wird ausschließlich für **KLXM Crossmedia** sowie deren autorisierte **Partner und Kunden** lizenziert. Jede andere Nutzung, Verbreitung, Weitergabe oder Modifikation – auch in abgeleiteten Werken – ist ohne ausdrückliche schriftliche Genehmigung von KLXM Crossmedia untersagt.

Weitere Informationen: [https://klxm.de](https://klxm.de)

---

## 👤 Author

**KLXM Crossmedia / Thomas Skerbis**
Website: [https://klxm.de](https://klxm.de)
