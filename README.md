# YForm Content Builder

Slice-based Content Builder für REDAXO YForm und Structure – erstelle flexible, wiederverwendbare Content-Elemente für beliebige CSS-Frameworks.

📖 [API-Dokumentation](API.md) · 🚀 [Tutorial](TUTORIAL.md) · 💻 [Developer Guide](DEV.md) · 📋 [Changelog](CHANGELOG.md)

---

## ✨ Features

### Content-Elemente
- **Elemente aus Core, Starter und externen Projekt-Addons**.
- **Modular konfigurierbar** – `YFORM_CONTENT_BUILDER_ELEMENT_MODE` steuert, ob externe Projekt-Elemente zusätzlich geladen (`merge`) oder ausschließlich externe Elemente genutzt werden (`replace`).
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

## 🏗️ Architektur (v3.1.0+)

### Addon-Strategie

Das System trennt klar zwischen Haupt-Addon und externen Projekt-Addons:

| Addon | Elemente | Framework | Zweck |
|-------|----------|-----------|-------|
| **yform_content_builder** | Core (4) + Starter Demo (6) | UIkit/Bootstrap/Plain | Foundation + Demo |
| **Externe Projekt-Addons** | Projekt-Elemente | Addon-abhängig | Produktive projektspezifische Bausteine |

**Begriffe im Gesamtsystem:**
- **Core-Elemente**: Basis-Bausteine im Haupt-Addon (`yform_content_builder`)
- **Starter-Elemente**: Demo-Bausteine im Haupt-Addon (`yform_content_builder`)
- **Projekt-Elemente**: Produktive externe Bausteine in separaten Projekt-Addons

**Element Mode konfigurieren:**
```php
// Backend > YForm Content Builder > Settings
// oder in REDAXO-Instanz-Config:
$addon_config['yform_content_builder']['element_mode'] = 'merge'; // 'merge' oder 'replace'
```

- `merge` (Default): Starter-Demo + externe Projekt-Elemente
- `replace`: Nur externe Projekt-Elemente (Demo ausblenden)

### Framework-Abstraktion

Die **neuen Config-Klassen** (v3.1.0) ermöglichen Framework-Flexibilität:

```
┌─────────────────────────────────────┐
│ Extension Points (registrierbar)    │
├─────────────────────────────────────┤
│ YFORM_CONTENT_BUILDER_FRAMEWORK_*   │
│ YFORM_CONTENT_BUILDER_EDITOR_*      │
│ YFORM_CONTENT_BUILDER_ELEMENT_*     │
└──────────────┬──────────────────────┘
               │
        ┌──────▼────────────────┐
        │  Config-Klassen       │
        ├───────────────────────┤
        │ • FrameworkConfig     │ ← Framework-Optionen (backgrounds, paddings, containers)
        │ • EditorConfig        │ ← Editor-Profile pro Element
        │ • ElementRegistry     │ ← Element-Verwaltung
        │ • TemplateEngine      │ ← Framework-Dispatch
        └───────────────────────┘
```

**Externe Frameworks hinzufügen (z.B. Bootstrap):**
```php
// In bootstrap_theme addon boot.php
rex_extension::register('YFORM_CONTENT_BUILDER_FRAMEWORK_OPTIONS', 
    function($ep) {
        if ('bootstrap' === $ep->getParam('framework')) {
            if ('backgrounds' === $ep->getParam('option_type')) {
                return [
                    '' => 'Keine',
                    'bg-primary' => 'Primary',
                    'bg-secondary' => 'Secondary',
                ];
            }
        }
        return $ep->getSubject();
    }
);
```

Vollständige Dokumentation: [Developer Guide](DEV.md)

---

## 📦 Verfügbare Elemente

### 🔷 Core-Elemente (immer verfügbar)

| Element | Schlüssel | Beschreibung |
|---------|-----------|--------------|
| Section / Container | `section` | Visuelle Abschnitte mit Hintergrundfarbe/-bild, Auto-Close |
| Columns | `columns` | Mehrspaltige Container mit verschachtelten Elementen |
| Divider | `divider` | Trennlinien, 9 Styles inkl. animiertem Scroll-Chevron |

### ⭐ Starter-Elemente (Demo, im Haupt-Addon)

| Element | Schlüssel | Beschreibung |
|---------|-----------|--------------|
| Starter Text | `starter_text` | Minimalistisches RichText-Element mit TinyMCE |
| Starter Headline | `starter_headline` | Reduziertes Headline-Element mit Rich-Headline-Feldtyp |
| Starter Media Split | `starter_media_split` | Medium und Text nebeneinander, flexibel konfigurierbar |
| Starter Cards | `starter_cards` | Karten-Liste (Repeater-basiert), mit Link-Optionen |
| YForm-Liste | `yform_list` | Dynamische Liste auf Basis zentral konfigurierter YForm-Profile |
| Forcal-Liste | `forcal_list` | Kommende Termine aus dem forcal-Addon als Cards/Liste/Kompakt |
| Tabelle | `table` | Barrierefreie Tabelle mit Table-Editor und responsiver Ausgabe |
| Smart-Link Showcase | `smart_link_showcase` | Demo-Linkliste mit smart_link und Medienvorschau |
| Smart-Links Multi Showcase | `smart_links_multi_showcase` | Mehrere Smart Links in einem Feld (multiple-Modus) |

Die Modul-Erstellung bleibt zentral auf `index.php?page=yform_content_builder/modules`.

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

#### Single Element mit Initialwerten

Initialwerte greifen **nur wenn der Slot noch leer ist** – gespeicherte Inhalte werden nie überschrieben. Du musst **nur die Felder angeben, die vom Element-Default abweichen sollen** – leere Strings für unveränderte Felder sind nicht nötig.

```php
// Eingabe (Input-Modul) – Galerie mit Sektion + Container aktiv
<?php
use KLXM\YFormContentBuilder\Module;

echo Module::createByValueId('gallery', 2, 'bootstrap', [
    'enable_section'   => '1',
    'enable_container' => '1',
    'container_width'  => 'uk-container',
    'layout'           => 'grid',
    'lightbox'         => '1',
])->renderInput();
```

### REDAXO-Modul (Multi Element Builder)

```php
// Eingabe (Input-Modul)
<?php
use KLXM\YFormContentBuilder\Module;

$contentBuilder = Module::createWithValue(1, null, [
    'framework'   => 'bootstrap',
    'label'       => 'Seiteninhalt',
    'description' => 'Fügen Sie Content-Elemente hinzu',
    // 'allowed_elements' => ['headline', 'gallery', 'section'],
]);

echo $contentBuilder->getEditor();
```

Mit **`initial_slices`** und **`element_defaults`** lässt sich das Verhalten weiter steuern. Es müssen dabei **nur Felder angegeben werden, die vom Element-Standard abweichen** – alle anderen Felder bleiben auf ihrem jeweiligen Default:

```php
// Eingabe (Input-Modul) – mit Startlayout und Voreinstellungen
<?php
use KLXM\YFormContentBuilder\Module;

$contentBuilder = Module::createWithValue(1, null, [
    'framework'   => 'bootstrap',
    'label'       => 'Seiteninhalt',
    'description' => 'Fügen Sie Content-Elemente hinzu',

    // Startlayout: wird nur beim ersten Aufruf (leerer Slot) angezeigt
    'initial_slices' => [
        [
            'type'   => 'section',
            'online' => true,
            'data'   => [
                'label'     => 'Hauptbereich',
                'container' => 'container',
            ],
        ],
        [
            'type'   => 'starter_text',
            'online' => true,
            'data'   => [
                'text'             => '<p>Willkommen auf der Seite.</p>',
                'enable_section'   => '1',
                'enable_container' => '1',
                'container_width'  => 'uk-container',
            ],
        ],
    ],

    // Element-Defaults: Voreinstellungen für jeden neu hinzugefügten Slice dieses Typs
    // '*' gilt für ALLE Element-Typen; typ-spezifische Einträge überschreiben ihn
    'element_defaults' => [
        '*' => [
            // Gilt für jedes neu angelegte Element
            'enable_section'   => '1',
            'enable_container' => '1',
            'container_width'  => 'uk-container',
        ],
        // Typ-spezifische Ergänzungen / Überschreibungen:
        'gallery' => [
            'layout'   => 'grid',
            'lightbox' => '1',
        ],
    ],
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

> **`initial_slices`** – Startlayout für den ersten Aufruf (leerer Slot). Aliase `initial_values` / `initial_value` funktionieren gleichwertig.  
> **`element_defaults`** – Voreinstellungen pro Element-Typ für jeden neu hinzugefügten Slice. Der Wildcard-Key `'*'` gilt für **alle** Typen; typ-spezifische Einträge überschreiben ihn. Alternativ: `global_defaults` als eigenständige Option. Bereits gespeicherte Inhalte werden **nie** überschrieben.  
> Es müssen nur Felder angegeben werden, die vom jeweiligen Element-Standard abweichen. Beide Optionen können frei kombiniert werden.

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

### Legacy-HTML Migration & Editor-Auswahl

Wenn ältere Inhalte noch als HTML im Feld liegen, zeigt der Content Builder einen Hinweis zum Wechsel in den modernen Editor. Features:

- **Editor-neutral**: Unterstützt **CKE5** und **TinyMCE** in der Legacy-Bearbeitung – konfigurierbar via `legacy_editor_attributes`.
- **Flexible Migration**: Ziel-Element und Ziel-Feld sind frei konfigurierbar (`legacy_migration_target`, `legacy_migration_field`).
- **Automatisches Speichern**: Der Wechsel speichert den Inhalt direkt nach dem Klick.
- **Fallback-Logik**: Wenn Ziel-Element nicht verfügbar, wird automatisch auf `starter_text` oder erstes verfügbares Element ausgewichen.

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

## 🏗️ Element-Management

### Konfiguration: Replace vs. Merge

In den Einstellungen des jeweiligen Projekt-Addons kann gewählt werden:

- **Replace**: Nur externe Projekt-Elemente laden. Starter/Core-Demo-Elemente aus dem Haupt-Addon werden nicht angezeigt.
- **Merge** (Standard): Starter/Core-Elemente + externe Projekt-Elemente gemeinsam anzeigen.

Technisch über `YFORM_CONTENT_BUILDER_ELEMENT_MODE` Extension Point gesteuert.

### Entwickler-Hinweis: Eigene Element-Addons

Wenn du weitere Projekt-Elemente in ein separates Addon auslagern möchtest (z. B. `custom_elements`):

```php
// In deinem Addon boot.php
rex_extension::register(
    'YFORM_CONTENT_BUILDER_ELEMENT_PATHS',
    static function(rex_extension_point $ep) {
        $ep->setResult([
            rex_path::addon('custom_elements', 'elements'),
        ]);
    },
    rex_extension::EARLY,
);
```

- **Extension Points verfügbar**:
  - `YFORM_CONTENT_BUILDER_ELEMENT_PATHS`: Zusätzliche Element-Pfade (Array von Pfad-Strings)
  - `YFORM_CONTENT_BUILDER_ELEMENT_MODE`: Steuert, ob externe Elemente `merge` (zusätzlich) oder `replace` (nur externe) laden

- **Best Practice**:
  - Eigene Element-Keys mit Präfix (`custom_*`, `myproject_*`)
  - Labels ebenfalls mit Präfix für Klarheit im Menü
  - So bleiben Core-, Starter- und Projekt-Elemente eindeutig unterscheidbar

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
