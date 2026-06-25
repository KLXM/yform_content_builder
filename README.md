# YForm Content Builder

Slice-based Content Builder für REDAXO YForm und Structure – erstelle flexible, wiederverwendbare Content-Elemente für beliebige CSS-Frameworks.

📖 [API-Dokumentation](API.md) · 🚀 [Tutorial](TUTORIAL.md) · 💻 [Developer Guide](DEV.md) · 📋 [Changelog](CHANGELOG.md)

---

## ✨ Features

### Content-Elemente
- **Elemente aus Core, Starter und externen Projekt-Addons**.
- **Modular konfigurierbar** – externe Element-Pfade werden per Extension Point registriert; `YFORM_CONTENT_BUILDER_ELEMENT_MODE` steuert, ob mitgelieferte Elemente zusätzlich geladen werden (`merge`) oder ausgeblendet bleiben (`replace`).
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
- **Theme-Provider über Extension Points (EP)** – Farben, Themes und Kontext, z. B. aus `uikit_theme_builder`

### Media Manager (ab v3.2)
- **Zentraler Basistyp:** Es wird nur noch der Typ `content_builder` angelegt.
- **Virtuelle Typen:** Ausgaben laufen über `cb_<preset>__<width>`, z. B. `cb_starter_cards_16_9__800`.
- **Preset-Auflösung zur Laufzeit:** `MEDIA_MANAGER_FILTERSET` mappt virtuelle Typen auf die Effektkette.
- **Effektreihenfolge:** `content_builder` läuft zuerst, optional `negotiator` immer als letzter Effekt.
- **Cache-Sicherheit bei Negotiation:** Bei aktivem `media_negotiator` ergänzt `MEDIA_MANAGER_INIT` den Cache-Key format-/qualitätsabhängig.
- **Responsive Image Helper:** `KLXM\YFormContentBuilder\Media\ResponsiveImage` erzeugt zentral `src`, `srcset`, `sizes` sowie optional Art-Direction (`<picture>/<source>`), inklusive Convenience-Renderern für `<img>` und `<picture>`.
- **focuspoint ist Pflicht**, damit ratio-basierte Zuschnitte konsistent sind.

### Theme-Provider per Extension Points (EP)

Der Content Builder nutzt für Theme-Funktionen ausschließlich Extension Points (EP).

Verfügbare Extension Points (EP):

- `YFORM_CONTENT_BUILDER_THEME_PROVIDER_AVAILABLE`
- `YFORM_CONTENT_BUILDER_THEME_CHOICES`
- `YFORM_CONTENT_BUILDER_THEME_CONTEXT_RESET`
- `YFORM_CONTENT_BUILDER_THEME_CONTEXT_SET`
- `YFORM_CONTENT_BUILDER_THEME_BACKGROUND_OPTIONS`
- `YFORM_CONTENT_BUILDER_THEME_TEXT_COLOR_OPTIONS`
- `YFORM_CONTENT_BUILDER_FRAMEWORK_NORMALIZE`

Damit kann jedes Addon Theme-Daten liefern und den Theme-Kontext steuern. (Der Begriff „Hook“ wird teils synonym verwendet.)

Beispiel: Theme-Provider registrieren (`boot.php` eines Provider-Addons):

```php
rex_extension::register('YFORM_CONTENT_BUILDER_THEME_PROVIDER_AVAILABLE', static function (rex_extension_point $ep): bool {
    return true;
});

rex_extension::register('YFORM_CONTENT_BUILDER_THEME_CHOICES', static function (rex_extension_point $ep): array {
    return [
        'default' => 'Default',
        'dark' => 'Dark',
        'brand_a' => 'Brand A',
    ];
});

rex_extension::register('YFORM_CONTENT_BUILDER_THEME_CONTEXT_RESET', static function (rex_extension_point $ep) {
    // Provider-internen Theme-Kontext zurücksetzen
    return $ep->getSubject();
});

rex_extension::register('YFORM_CONTENT_BUILDER_THEME_CONTEXT_SET', static function (rex_extension_point $ep) {
    $themeName = trim((string) $ep->getSubject());
    if ($themeName !== '') {
        // Provider-internes Theme aktivieren
        // z. B. DomainContext::setTheme($themeName)
    }
    return $ep->getSubject();
});
```

Beispiel: Theme-Hintergründe für UI-Auswahl und Preview liefern:

```php
rex_extension::register('YFORM_CONTENT_BUILDER_THEME_BACKGROUND_OPTIONS', static function (rex_extension_point $ep): array {
    $framework = (string) $ep->getParam('framework', 'uikit');
    if ($framework !== 'uikit') {
        return (array) $ep->getSubject();
    }

    return [
        'uk-background-default' => ['label' => 'Default', 'color' => '#ffffff'],
        'uk-background-muted' => ['label' => 'Muted', 'color' => '#f8f8f8'],
        'uk-background-primary' => ['label' => 'Primary', 'color' => '#1e87f0'],
        'uk-background-secondary' => ['label' => 'Secondary', 'color' => '#222222'],
    ];
});
```

Beispiel: Textfarben (z. B. für Divider) liefern:

```php
rex_extension::register('YFORM_CONTENT_BUILDER_THEME_TEXT_COLOR_OPTIONS', static function (rex_extension_point $ep): array {
    $framework = (string) $ep->getParam('framework', 'uikit');
    if ($framework !== 'uikit') {
        return (array) $ep->getSubject();
    }

    return [
        'uk-text-primary' => 'Primary',
        'uk-text-secondary' => 'Secondary',
        'uk-text-muted' => 'Muted',
    ];
});
```

Beispiel: Framework-Normalisierung (optional):

```php
rex_extension::register('YFORM_CONTENT_BUILDER_FRAMEWORK_NORMALIZE', static function (rex_extension_point $ep): string {
    $framework = trim((string) $ep->getSubject());
    if ($framework === 'bootstrap') {
        return 'uikit';
    }

    return $framework;
});
```

---

## 🏗️ Architektur

### Virtuelles Medientyp-Modell

Das Addon verwendet ein einheitliches, addongesteuertes Medientyp-System:

1. `install.php` legt nur den Basistyp `content_builder` mit Effekt `content_builder` an.
2. Die Klasse `MediaTypeRegistry` hält Presets (Ratio, Mode, Breiten).
3. Aus einem Preset wird ein virtueller Typ erzeugt:
   - `MediaTypeRegistry::buildVirtualType($preset, $width)`
   - Beispiel: `cb_klxm_card_1_1__400`
4. `MediaManagerFilterset::apply()` löst den virtuellen Typ in eine Effektkette auf.
5. Optional wird am Ende `negotiator` angehängt, wenn `media_negotiator` verfügbar ist.

Beispiel in Templates:

```php
<?php
$type = \KLXM\YFormContentBuilder\Config\MediaTypeRegistry::buildVirtualType('starter_cards_16_9', 1200);
echo rex_media_manager::getUrl($type, $file);
```

### Eigene Presets registrieren

Externe Addons registrieren zusätzliche Presets über den Extension Point `YFORM_CONTENT_BUILDER_MEDIA_TYPE_PRESETS`:

```php
rex_extension::register(
    'YFORM_CONTENT_BUILDER_MEDIA_TYPE_PRESETS',
    static function (rex_extension_point $ep): array {
        $presets = (array) $ep->getSubject();

        $presets['myaddon_card_3_2'] = [
            'ratio' => '3_2',
            'mode' => 'focuspoint',
            'widths' => [400, 800, 1200, 1600],
            'default_width' => 1200,
        ];

        return $presets;
    },
    rex_extension::EARLY
);
```

#### Preset-Optionen

| Option | Typ | Beschreibung |
|--------|-----|-------------|
| `ratio` | string | Seitenverhältnis des Zielbilds – siehe Tabelle unten |
| `mode` | string | Zuschnitt-Modus: `focuspoint` oder `resize` |
| `widths` | int[] | Liste der zu erzeugenden Pixelbreiten, z. B. `[400, 800, 1200]` |
| `default_width` | int | Standardbreite, wenn kein `$width`-Parameter übergeben wird |

#### Ratio-Werte

| Wert | Beispiel | Modus | Erzeugter Media-Typ | Beschreibung |
|------|---------|-------|---------------------|-------------|
| `original` | `'ratio' => 'original'` | `resize` | `cb_myaddon_card_original__800` | Originalverhältnis, nur Breite skalieren – kein Crop |
| `16_9` | `'ratio' => '16_9'` | `focuspoint` | `cb_myaddon_card_16_9__800` | 16:9 Widescreen |
| `4_3` | `'ratio' => '4_3'` | `focuspoint` | `cb_myaddon_card_4_3__800` | 4:3 klassisch |
| `3_2` | `'ratio' => '3_2'` | `focuspoint` | `cb_myaddon_card_3_2__800` | 3:2 (Foto-Standard) |
| `2_3` | `'ratio' => '2_3'` | `focuspoint` | `cb_myaddon_card_2_3__800` | 2:3 Hochformat |
| `1_1` | `'ratio' => '1_1'` | `focuspoint` | `cb_myaddon_card_1_1__800` | Quadratisch |
| `w_h` | `'ratio' => '21_9'` | `focuspoint` | `cb_myaddon_card_21_9__800` | Beliebiges Ganzzahl-Verhältnis |

> **Hinweis:** Bei `ratio => 'original'` muss `mode => 'resize'` gesetzt werden (kein Focuspoint-Crop möglich). Der erzeugte Typ-Name lautet immer `cb_<preset_name>__<width>` – das `ratio` ist Bestandteil des Preset-Namens, nicht ein separates Segment im Typ-Namen.

Hinweise:
- Externe Addons sollen keine statischen MM-Typen mehr anlegen, sondern Presets registrieren.
- Namensschema für Presets: `<addon>_<zweck>_<ratio>` (z. B. `klxm_card_16_9`).

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
// In Provider-Addons per Extension Point:
rex_extension::register('YFORM_CONTENT_BUILDER_ELEMENT_MODE', static function (): string {
    return 'merge'; // oder 'replace'
});
```

- `merge`: Externe Projekt-Elemente + mitgelieferte Elemente
- `replace`: Externe Projekt-Elemente, mitgelieferte Elemente ausgeblendet
- **Priorität:** Wenn mindestens ein registriertes Provider-Addon `replace` signalisiert, gilt effektiv `replace`.
- **Ausnahmen im Replace-Modus:** Über „Core-Elemente trotz Replace-Modus“ können einzelne mitgelieferte Elemente gezielt freigegeben werden.
- **Globale Quellwahl:** In den Settings steuert „Verfügbare Element-AddOns“, aus welchen AddOn-Quellen Elemente angezeigt werden.

### Framework-Abstraktion

Die **Config-Klassen** ermöglichen Framework-Flexibilität:

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
    // Selbstverschachtelung für ausgewählte Elemente verhindern:
    // 'prevent_self_nesting' => 'columns,mein_element',
    // oder als Array:
    // 'prevent_self_nesting' => ['columns', 'mein_element'],
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

### Projektweite Defaults im YForm-Feld (ohne Modulcode)

Für das YForm-Wertfeld `content_builder` können Standardwerte direkt in der Felddefinition gesetzt werden.

Feldoptionen:

- `default_enable_section` – globale Voreinstellung für `enable_section` bei neuen Elementen
- `default_enable_container` – globale Voreinstellung für `enable_container` bei neuen Elementen
- `element_defaults_json` – optionale, erweiterte JSON-Defaults pro Elementtyp
- `prevent_self_nesting` – Elemente wählen, die nicht in sich selbst verschachtelt werden dürfen

Typischer Projektfall: Sektion und Container standardmäßig deaktivieren.

```json
{
    "*": {
        "enable_section": "0",
        "enable_container": "0"
    }
}
```

Hinweise:

- Die beiden einfachen Schalter (`default_enable_section`, `default_enable_container`) sind der schnellste Weg für projektweite Defaults.
- `element_defaults_json` ist für Feintuning je Elementtyp gedacht.
- Gespeicherte Inhalte werden nie rückwirkend überschrieben. Die Defaults gelten nur für neu hinzugefügte Elemente.

### Nesting-Regeln (Element, Modul, YForm)

Selbstverschachtelung kann auf drei Ebenen gesteuert werden:

- **Element-Config (`elements/<key>/config.php`)**
    - `allow_self_nesting` (`true|false`)
    - oder `prevent_self_nesting` (`true|false`)
- **Modul-Editor (`Module::createWithValue`)**
    - Option `prevent_self_nesting` als CSV oder Array von Element-Keys
- **YForm-Feldtyp `content_builder`**
    - Feldoption `prevent_self_nesting` als Mehrfachauswahl

Priorität:

1. Modul-Option `prevent_self_nesting`
2. YForm-Option `prevent_self_nesting`
3. Element-Config (`allow_self_nesting` / `prevent_self_nesting`)

Ohne gesetzte Regel bleibt Selbstverschachtelung erlaubt.

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
3. Optional: Module via **YForm Content Builder → Module** generieren

---

## 📄 Lizenz

Copyright © KLXM Crossmedia GmbH · Alle Rechte vorbehalten.

Diese Software ist proprietär und wird ausschließlich für **KLXM Crossmedia** sowie deren autorisierte **Partner und Kunden** lizenziert. Jede andere Nutzung, Verbreitung, Weitergabe oder Modifikation – auch in abgeleiteten Werken – ist ohne ausdrückliche schriftliche Genehmigung von KLXM Crossmedia untersagt.

Weitere Informationen: [https://klxm.de](https://klxm.de)

---

## 👤 Author

**KLXM Crossmedia / Thomas Skerbis**
Website: [https://klxm.de](https://klxm.de)
