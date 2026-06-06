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
- **Legacy-Migration** – ältere HTML-Inhalte können direkt im Backend in den modernen Content-Builder überführt werden

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
- **MediaPool In-Use-Check** – verwendet Medien in `content_builder`-Feldern und schützt sie vor versehentlichem Löschen
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

### REDAXO-Modul (Multi Element Builder)

```php
// Eingabe (Input-Modul)
<?php
use KLXM\YFormContentBuilder\Module;

$contentBuilder = Module::createWithValue(1, null, [
	'framework' => 'bootstrap',
	'label' => 'Seiteninhalt',
	'description' => 'Fügen Sie Content-Elemente hinzu',
	// 'allowed_elements' => ['headline', 'gallery', 'section'],
]);

echo $contentBuilder->getEditor();
```

```php
// Ausgabe (Output-Modul)
<?php
use KLXM\YFormContentBuilder\Module;

$contentBuilder = Module::createWithValue(1, 'REX_VALUE[1]', [
	'framework' => 'uikit',
]);

echo $contentBuilder->renderOutput();
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

### Legacy-HTML Migration

Wenn ältere Inhalte noch als HTML im Feld liegen, zeigt der Content Builder einen Hinweis zum Wechsel in den modernen Editor. Der Wechsel übernimmt den Inhalt direkt in ein `starter_text`-Element und kann sofort gespeichert werden.

### MediaPool-Schutz

Medien, die in `content_builder`-Feldern verwendet werden, erscheinen im MediaPool als in Benutzung. Die Warnung verlinkt direkt auf den betroffenen YForm-Datensatz.

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

## Entwickler-Hinweis: Element-Overrides (z. B. CSE)

Wenn ein Projekt eigene Elemente aus einem separaten Addon bereitstellt (z. B. `cse_elements`), sollten die Core-Dateien von `yform_content_builder` nicht direkt angepasst werden.

Stattdessen über Extension Points arbeiten:

- `YFORM_CONTENT_BUILDER_ELEMENT_PATHS` für zusätzliche/alternative Element-Pfade
- `YFORM_CONTENT_BUILDER_ELEMENT_MODE` mit `replace` oder `merge`

Empfehlung für unterscheidbare Menüs bei `merge`:

- Eigene Element-Keys mit Präfix (`cse_*`)
- Sichtbares Label ebenfalls mit Präfix, z. B. `CSE Text`, `CSE Cards`

So bleiben Original-Elemente und Projekt-Elemente parallel wartbar und eindeutig unterscheidbar.

## Entwickler-Hinweis: Conditional Toggles (visible_if)

Der Content Builder unterstuetzt generische, konfigurierbare Sichtbarkeitsregeln pro Feld.

Syntax in `elements/<element>/config.php`:

```php
'fields' => [
	'enable_section' => [
		'type' => 'checkbox',
		'label' => 'Sektion aktivieren',
	],
	'section_bg' => [
		'type' => 'choice',
		'label' => 'Sektions-Hintergrund',
		'choices' => ['' => 'Keine', 'uk-background-muted' => 'Muted'],
		'visible_if' => ['enable_section' => '1'],
	],
],
```

Regeln:

- `visible_if` ist ein Mapping aus `feldname => erwarteter_wert`
- Mehrere Bedingungen werden als UND ausgewertet
- Erwartete Werte koennen Strings oder Arrays sein
- Unterstuetzte Quellfelder:
	- Checkbox: Wert ist `1` (aktiv) oder `0` (inaktiv)
	- Radio: Wert ist der `value` des ausgewaehlten Radio-Buttons
	- Select (single): Wert ist der ausgewaehlte `value`
	- Select (multiple): Wert ist ein Array der ausgewaehlten Werte

Beispiele:

```php
'visible_if' => ['enable_section' => '1']
```

```php
'visible_if' => ['layout_variant' => 'cards']
```

```php
'visible_if' => ['image_mode' => ['cover', 'contain']]
```

Wichtig zum Scope:

- Funktioniert in der YForm-Variante
- Funktioniert ebenfalls in Modul-Editoren (Module::createWithValue / createByValueId), da dieselbe Render-/JS-Logik verwendet wird

Damit koennen Element-Configs ohne Zusatz-JS kontextunabhaengig gesteuert werden.

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
