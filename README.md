# YForm Content Builder

Slice-based Content Builder für REDAXO YForm - Erstelle flexible, wiederverwendbare Content-Elemente mit professioneller Media-Verwaltung, Grid-Layouts und intuitivem Workflow.

## ✨ Features

### 🎯 **Content-Elemente**
- **20+ fertige Elemente**: u.a. Section, Media Text, Accordion/Tabs, Headline, Divider, Cards, Slideshow, Gallery, Hero Banner, Feature Grid, Testimonial, Timeline, Kontaktformular, Moving Tiles
- **Professional Media Widget**: 16:9 Preview-Container, object-fit: contain, globaler Counter für eindeutige IDs
- **Professional Gallery**: Grid/Masonry Layout, Mixed Media (Bilder + Videos), Responsive Design
- **Auto-Close Sections**: Visueller Container für Abschnitte ohne verschachtelte Hierarchie

### 🚀 **Workflow & UX**
- **Click-to-Edit**: Intuitives Bearbeiten per Edit-Button  
- **⬆️⬇️ Pfeil-Sortierung**: Zuverlässiges Verschieben von Elementen nach oben/unten
- **Online/Offline pro Slice** *(YForm-Variante, optional)*: In den Einstellungen aktivierbar – einzelne Abschnitte lassen sich per Augen-Button deaktivieren, ohne sie zu löschen; offline geschaltete Slices werden im Frontend automatisch ausgeblendet
- **Modal-Gruppierung**: Übersichtliche Formulare durch Settings-Modals
- **Progressive AJAX Forms**: Optionales Enhancement mit vollständigem Non-JS-Fallback
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
- **Feld-Plugin-System**: Jeder Feldtyp als eigene Klasse, einfach erweiterbar via Extension Point
- **rex_api_function**: Dedizierte API für AJAX-Requests (`/redaxo/index.php?rex-api-call=content_builder`)
- **Element-Filter**: Kontrolle welche Elemente pro Feld verfügbar sind (Multiselect)
- **Settings-Modals**: Komplexe Optionen in übersichtlichen Modal-Dialogen
- **CKE5 Integration**: REDAXO's CKEditor 5 nahtlos integriert
- **Custom Media Widget**: Eigene Preview-Logik unabhängig von REDAXO Core, funktioniert in YForm und Modulen
- **Repeater-System**: Flexible Listen mit Add/Delete/Move-Funktionen
- **Linkmap-Widget**: Vollständige REDAXO Linkmap-Integration
- **Media Manager Integration**: Automatische Bild-URLs via `rex_media_manager::getUrl()`

### 🏗️ **Architecture**
- **Field Registry**: Plugin-System für Feldtypen mit Interface und Extension Point
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
    - **Wertevergleiche (ohne eval)**: `{{plz}} < {{99000}}`, `{{alter}} >= {{18}}`, `{{feldA}} == {{feldB}}`
  - **Längenprüfung**: Mindest- und Maximallänge
  - **Regex**: Eigene Muster
- **Spam-Schutz**: Honeypot-Feld und/oder Zeit-Check
- **CSRF-Schutz**: Token-basierte Sicherheitsprüfung beim Absenden
- **Bestätigungs-E-Mail**: Automatische Kopie an Absender mit anpassbarem Intro/Footer
- **4 Layout-Optionen**: Standard, Horizontal, Floating Labels, Kompakt
- **Privacy-Checkbox**: Mit Linkmap-Verknüpfung zur Datenschutzseite
- **Backend-Vorschau**: Sicherer Preview ohne Formular-Interferenz
- **Mehrfach-Empfänger**: Mehrere Zieladressen in `email_to` (Komma oder Semikolon)
- **Progressives AJAX-Enhancement**: Ohne JS klassischer POST, mit JS optional ohne Reload inkl. Fokus- und aria-live-Feedback

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

### YForm-Liste & Forcal-Termine (NEU in 1.10)

Zwei No-Code-Listen-Elemente für dynamische Inhalte direkt aus dem Content Builder – ohne eigenes Modul oder PHP.

#### YForm-Liste (`yform_list`)
Server-seitig gerenderte Auflistung aus beliebigen YForm-Tabellen (News, Produkte, Mitarbeiter …).

- **Profile** in den Addon-Einstellungen: Tabelle, Spalten, Sortierung, Filter und URL-Schema werden zentral hinterlegt.
- **Im Element** wählt der Redakteur nur: Profil, Layout (Cards / Liste / Kompakt), Anzahl, optional Headline & Beschreibung.
- **Templates** für UIkit, Bootstrap und Plain HTML.

#### Forcal-Termine (`forcal_list`)
Kommende Termine aus dem [forcal](https://github.com/FriendsOfREDAXO/forcal)-Kalender.

- **Zwei Modi**: nach Kategorie(n) (Mehrfachauswahl) oder nächste X Wiederholungen eines Serientermins.
- **Block-Listen-Gruppierung**: keine / Tag / Monat / Jahr / Jahr→Monat verschachtelt.
- **Headlines konfigurierbar**: Tag (h1–h6) und UIkit-Style sowohl für Haupt- als auch Gruppen-Überschriften.
- **Sektion**: Hintergrundfarbe, -bild oder -video (mp4/webm), `uk-light` für helle Schrift auf dunklem Grund.
- **Bildausgabe optional**: Checkbox „Bild anzeigen", automatische Erkennung von `image` / `entries_image` / `lang_image_<clang>`, Media Manager Typ `card`.
- **Smart Visibility**: Das Element erscheint **nur**, wenn das forcal-Addon installiert und aktiviert ist.

> Element-Configs können sich seit 1.10 selbst deaktivieren, indem sie `return null;` zurückgeben – z. B. wenn ein benötigtes Drittaddon fehlt.

### Moving Tiles Element (NEU)
Parallax Tiles mit alternierenden Layouts - inspiriert vom Juno Template:

- **Alternierende Layouts**: Bild links/rechts wechselt automatisch
- **Parallax-Effekt**: Horizontale Bewegung beim Scrollen (konfigurierbar)
- **Fade-In Animation**: Optional, integriert im Parallax
- **Video-Support**: MP4, WebM mit autoplay/pause beim Scrollen (`uk-video="autoplay: inview"`)
- **Mobile-First**: 
  - Bilder immer zuerst auf Mobile (`uk-flex-first`)
  - Feste Höhe auf Mobile, Cover auf Desktop
- **Tile-Farben**: Global oder pro Item (Default, Muted, Primary, Secondary)
- **Section-Hintergrund**: Transparent, Default, Muted, Primary, Secondary

**Konfigurationsoptionen:**
| Option | Beschreibung |
|--------|-------------|
| `tile_style` | Globale Textbereich-Farbe |
| `section_bg` | Hintergrund der Section |
| `section_padding` | Padding (klein/standard/groß/extra groß) |
| `first_position` | Erstes Bild links oder rechts |
| `parallax_enabled` | Parallax-Effekt an/aus |
| `parallax_offset` | Versatz in Pixel (z.B. 30) |
| `fade_enabled` | Fade-In Animation an/aus |

**Pro Item:**
- Bild oder Video
- Text (CKE5)
- Optionale eigene Tile-Farbe (überschreibt global)
- Alt-Text, Dekorativ-Flag, Lightbox

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

## 📋 YForm-Liste (zentrale Profile)

Mit dem Element **„YForm-Liste“** lassen sich Datensätze aus beliebigen YForm-Tabellen (News, Produkte, Events, …) als Karten-, Listen- oder Kompakt-Ausgabe in den Content Builder einbinden – ohne dass Redakteure Tabellennamen oder Spalten kennen müssen.

### Konzept

Tabelle, Spalten-Mapping, **Filter/Queries** (z. B. `status = 1`, Datums-Constraints), URL-Pattern und Sortierung werden **einmal zentral** als **Profil** in den Addon-Einstellungen hinterlegt. Im Element wählt der Redakteur dann nur:

- das gewünschte Profil (z. B. „News", „Produkte")
- Anzahl der Einträge
- Layout / Design (Cards / Liste / Kompakt)
- optional Headline & Beschreibung

> Filter werden **ausschließlich auf Profil-Ebene** gesetzt – Redakteure können sie nicht überschreiben. So bleibt z. B. eine Bedingung wie „nur veröffentlichte" zuverlässig aktiv.

### 1. Profile verwalten

Backend → **YForm Content Builder → Einstellungen → YForm-Listen-Profile → Neues Profil anlegen**

Pro Profil pflegst du:

| Feld | Bedeutung |
|------|-----------|
| **Profil-ID** | technische Slug-ID (`news`, `products`, …) – nur `a-z0-9_` |
| **Label** | Anzeigename in der Profil-Auswahl |
| **YForm-Tabelle** | Tabelle aus dem YForm-Tablemanager |
| **Titel-Spalte** | Pflicht – der Hauptkopf jedes Items |
| **Anriss-Spalte** | optional – Teaser-/Beschreibungsspalte (HTML wird gestrippt + gekürzt) |
| **Bild-Spalte** | optional – Mediapool-Datei oder absolute URL |
| **Sortier-Spalte / Richtung** | z. B. `createdate DESC` oder `id DESC` |
| **URL-Pattern** | Detail-Link mit Platzhaltern, z. B. `/news/?id={id}` oder `/article/{slug}` |
| **Mediamanager-Typ** | optional, z. B. `card_16_9_w800` |
| **Default-Layout / Default-Anzahl** | Vorbelegung im Element |
| **Filter / WHERE-Bedingungen** | dauerhafter Query-Filter, z. B. `status = 1`, `publish_date <= NOW` (siehe Filter-Syntax) |

> Tipp: Tabelle wählen → Spalten- und URL-Profil-Listen werden per AJAX automatisch befüllt.

### 2. Element einfügen

Im Content Builder das Element **„YForm-Liste"** hinzufügen und im Settings-Modal nur noch:

- Profil wählen
- ggf. Anzahl, Layout, Anriss-Länge wählen
- optional Headline / Beschreibung

### Filter-Syntax (Profil-Ebene)

Pro Zeile eine Bedingung. Bedingungen werden mit `AND` verknüpft:

```
status = 1
publish_date <= NOW
expire_date >= TODAY
category LIKE %news%
```

**Operatoren:** `=`, `!=`, `<`, `<=`, `>`, `>=`, `LIKE`

**Datums-Platzhalter** (werden bei jeder Anfrage zur Laufzeit aufgelöst):

| Platzhalter | Ersetzt durch |
|-------------|---------------|
| `NOW` | aktueller Zeitstempel `Y-m-d H:i:s` |
| `TODAY` | heutiges Datum `Y-m-d` |
| `TODAY+N` / `TODAY-N` | heutiges Datum ± N Tage |
| `NOW+NH` / `NOW-NH` | jetzt ± N Stunden |
| `NOW+NM` / `NOW-NM` | jetzt ± N Minuten |
| `NOW+ND` / `NOW-ND` | jetzt ± N Tage (mit Zeit) |

Werte werden technisch über `?`-Parameter gebunden – kein Risiko für SQL-Injection.

### URL-Pattern

Platzhalter werden aus jeder Zeile per Spaltenname ersetzt:

```
/news/?id={id}
/article/{slug}
/{category}/{slug}.html
```

Werte werden mit `rawurlencode()` URL-encoded eingebaut.

### URL-Profil (Url-Addon)

Wenn das **Url-Addon** installiert ist, erscheint im Profil-Editor zusätzlich ein Auswahlfeld **„URL-Profil (Url-Addon)“**. Dort lassen sich die zur gewählten Tabelle passenden Url-Addon-Profile direkt auswählen — die Detail-URL wird dann via

```php
rex_getUrl('', '', ['<namespace>' => $datasetId])
```

erzeugt. In dem Fall wird das URL-Pattern ignoriert. Liefert das Url-Addon keine URL (z. B. weil das Profil noch nicht generiert wurde), greift automatisch das Pattern als Fallback.

### Virtual URLs (Alternative)

Wenn das **virtual_urls-Addon** installiert ist, erscheint zusätzlich die Checkbox **„URLs über das `virtual_urls`-Addon erzeugen"**. Ist sie aktiv, wird die Detail-URL via

```php
FriendsOfRedaxo\VirtualUrl\VirtualUrlsHelper::getUrl($table, $datasetId)
```

erzeugt. Diese Strategie hat **Vorrang** vor dem Url-Addon-Profil und dem URL-Pattern.

**Auflösungs-Reihenfolge im Frontend:**

1. virtual_urls (wenn aktiviert)
2. Url-Addon-Profil (wenn gesetzt)
3. URL-Pattern (Fallback)

### Sicherheit

- **Whitelist**: Nur Tabellen mit existierendem Profil sind anwählbar.
- **Spalten-Validierung**: Title/Teaser/Bild/Sort/Filter-Felder werden gegen die echten YForm-Spalten der Tabelle geprüft.
- **Parameter-Binding**: Filter-Werte werden über `?`-Platzhalter gebunden, nie konkateniert.
- **Identifier-Escaping**: Tabellen- und Spaltennamen über `rex_sql::escapeIdentifier()`.
- Limit ist hart auf 1–200 begrenzt, Sortier-Richtung auf `ASC|DESC`.


## 📅 Forcal-Termine

Mit dem Element **„Forcal-Termine"** lassen sich kommende Veranstaltungen aus dem [`forcal`-Addon](https://github.com/FriendsOfREDAXO/forcal) im Content Builder ausgeben – als Cards, Liste oder Kompakt-Variante.

> Voraussetzung: Das `forcal`-Addon muss installiert und aktiviert sein. Sonst wird das Element ausgegraut angezeigt.

### Modi

1. **Nach Kategorie(n)** – zeigt die nächsten X kommenden Termine aus einer (oder allen) Kategorie(n). Mehrere Kategorie-IDs kommagetrennt im Element angeben (z. B. `1,3,5`). Leer = alle Kategorien.
2. **Wiederkehrender Termin** – wählt einen einzelnen Serien-Eintrag aus dem Picker und listet die nächsten X Wiederholungen.

### Element-Felder (Redakteur)

| Feld | Bedeutung |
|------|-----------|
| **Modus** | Kategorie-Liste oder Serientermin |
| **Kategorie-ID(s)** | nur Modus „Kategorie": kommagetrennte IDs, leer = alle |
| **Serientermin** | nur Modus „Wiederkehrend": Picker aus aktiven Repeating-Entries |
| **Anzahl Termine** | 1–50 |
| **Layout** | Cards / Liste / Kompakt |
| **URL-Pattern** | optional, Platzhalter `{id}`, z. B. `/termine/?id={id}` |
| **Headline / Beschreibung** | optional über der Liste |
| **Termine verlinken** | nutzt URL-Pattern |

### Datenquelle

Es wird die offizielle `forCalEventsFactory`-Fluent-API genutzt:

```php
forCal\Factory\forCalEventsFactory::create()
    ->from('now')
    ->to('+24 months')
    ->inCategories([1,3])
    ->sortBy('start_date', 'asc')
    ->get();
```

Wiederholungen werden via `getEntryById()` abgefragt und über `$entry->dates[]` ausgewertet – nur kommende Wiederholungen ab `now` werden berücksichtigt.

### Layouts

- **Cards** – Kacheln mit Datum, Titel, Teaser, Veranstaltungsort und Kategorie-Farbleiste oben
- **Liste** – Datum + Titel + Teaser, Kategorie-Farbe als linker Rahmen
- **Kompakt** – nur Datum + Titel als Liste


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

Das Addon unterstützt zwei Betriebsmodi beim Laden eigener Elemente:

1. **`replace`** (Standard):
    - Sobald eigene Element-Pfade registriert sind, werden nur Custom-Elemente geladen.
    - Demo-Elemente aus dem Addon sind ausgeblendet.
2. **`merge`**:
    - Demo-Elemente und Custom-Elemente werden gemeinsam geladen.
    - Bei Namensgleichheit überschreibt das Custom-Element das Demo-Element.

Der Modus wird über den Extension Point `YFORM_CONTENT_BUILDER_ELEMENT_MODE` gesteuert.

```php
rex_extension::register('YFORM_CONTENT_BUILDER_ELEMENT_MODE', static function (): string {
     return 'merge'; // 'merge' oder 'replace'
});
```

Eigene Element-Pfade registrierst du weiterhin über `YFORM_CONTENT_BUILDER_ELEMENT_PATHS`.

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

Das Addon nutzt ein **Plugin-System für Feldtypen**. Jeder Feldtyp ist eine eigene Klasse und kann einfach erweitert oder überschrieben werden.

| Typ | Klasse | Beschreibung |
|-----|--------|--------------|
| `text` | `TextField` | Einzeiliges Textfeld |
| `textarea` | `TextareaField` | Mehrzeiliges Textfeld |
| `cke5` | `Cke5Field` | CKEditor 5 Rich Text |
| `be_media` | `BeMediaField` | REDAXO Mediapool Widget |
| `be_link` | `BeLinkField` | REDAXO Linkmap Widget |
| `select` | `SelectField` | Einfaches Select Dropdown |
| `choice` | `ChoiceField` | Erweitertes Select mit Selectpicker |
| `checkbox` | `CheckboxField` | Checkbox |
| `repeater` | `RepeaterField` | Wiederholbare Feldgruppen |
| `radio_image` | `RadioImageField` | Layout-Auswahl mit SVG-Vorschaubildern |
| `color_swatches` | `ColorSwatchesField` | Farbauswahl mit visuellen Farbfeldern |

### Eigenen Feldtyp erstellen

```php
<?php
namespace FriendsOfREDAXO\YFormContentBuilder\Fields;

class MyCustomField extends ContentBuilderFieldAbstract
{
    public static function getType(): string
    {
        return 'my_custom';
    }

    public function render(string $fieldName, array $fieldConfig, $value, array $sliceData = []): void
    {
        $label = $fieldConfig['label'] ?? $fieldName;
        
        $this->openFormGroup();
        $this->renderLabel($label);
        
        // Eigene Render-Logik hier
        echo '<input type="text" class="form-control" name="' . rex_escape($fieldName) . '" value="' . rex_escape($value) . '">';
        
        $this->closeFormGroup();
    }
}
```

### Feldtyp registrieren

```php
// In boot.php deines Addons oder per Extension Point
use FriendsOfREDAXO\YFormContentBuilder\Fields\ContentBuilderFieldRegistry;

// Direkt registrieren
ContentBuilderFieldRegistry::register(new MyCustomField());

// Oder per Extension Point
rex_extension::register('YFORM_CONTENT_BUILDER_FIELDS', function(rex_extension_point $ep) {
    $fields = $ep->getSubject();
    $fields['my_custom'] = new MyCustomField();
    return $fields;
});
```

### Bestehenden Feldtyp überschreiben

```php
// Eigene Implementierung von be_media mit zusätzlichen Features
ContentBuilderFieldRegistry::register(new MyEnhancedMediaField());
```

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

## � Permission System

Mit dem Permission System kannst du einzelne Felder nur für bestimmte Benutzerrollen sichtbar machen. Das ist ideal für Admin-Funktionen, die nicht für alle Redakteure sichtbar sein sollen.

### Basis-Nutzung

Füge einfach `'perm'` zu deiner Feld-Konfiguration hinzu:

```php
'fields' => [
    'title' => ['type' => 'text', 'label' => 'Titel'],
    
    // Nur für Admins
    'sql_query' => [
        'type' => 'textarea',
        'label' => 'SQL-Abfrage',
        'perm' => 'admin'
    ],
    
    // Nur für Redakteure
    'internal_notes' => [
        'type' => 'textarea',
        'label' => 'Interne Notizen',
        'perm' => 'editor'
    ]
]
```

### Mehrere Rollen kombinieren

**Pipe-Format** (einfach und lesbar):
```php
'perm' => 'editor|reviewer|admin'  // Eine dieser Rollen genügt
```

**Array-Format** (für mehr Struktur):
```php
'perm' => ['editor', 'reviewer', 'admin']
```

### Praktische Beispiele

```php
// Kontaktformular Element
'fields' => [
    'form_fields' => ['type' => 'repeater', ...],
    
    // Nur Admins können technische SQL-Optionen bearbeiten
    'field_options_sql' => [
        'type' => 'textarea',
        'label' => 'Erweiterte SQL-Optionen',
        'perm' => 'admin'
    ],
    
    // Nur Power-User und Admins sehen erweiterte Settings
    'advanced_settings' => [
        'type' => 'repeater',
        'label' => 'Erweiterte Einstellungen',
        'perm' => 'power|admin'
    ]
]
```

### Verfügbare Rollen

Die Rollen stammen aus deinem REDAXO-System:

```php
// Standard REDAXO Rollen (falls definiert)
'admin'      // Admin-Benutzer (isAdmin() = true)
'editor'     // Redakteur
'reviewer'   // Freigabekontrolle
'contributor' // Mitarbeiter
'power'      // Power User
// Plus alle benutzerdefinierten Rollen aus deinem System
```

### Sicherheit

- ✅ Permission-Prüfung läuft **serverseitig** - Felder sind für nicht berechtigte Benutzer unsichtbar
- ✅ Funktioniert in **allen Feldtypen** (text, textarea, cke5, repeater, etc.)
- ✅ Funktioniert im **Frontend-Formular** und **Backend-Editor**
- ✅ Keine Umwege möglich - nicht berechtigt = nicht zu sehen

## �🐛 Troubleshooting

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

## 🎯 Extra-Felder System

Das Extra-Felder System ermöglicht es, beliebige zusätzliche Felder zu bestehenden Elementen hinzuzufügen, **ohne den Element-Code zu modifizieren**. Perfekt für projektspezifische Erweiterungen!

### Wie Extra-Felder funktionieren

1. **Extra-Klasse erstellen** - Eine externe PHP-Klasse definiert zusätzliche Felder
2. **Backend Rendering** - Felder werden automatisch in Modal oder Extras-Tab angezeigt
3. **Datenspeicherung** - Werte werden mit den Element-Daten gespeichert
4. **Frontend Output** - Eine `GetOutput()` Methode formatiert die Werte für die Ausgabe

### Extra-Klasse erstellen

**Beispiel: CardsRepeaterExtra.php** (im Projekt-Addon)

```php
<?php

/**
 * Projekt-spezifische Extra-Felder für Cards Element (Repeater Items)
 */
class CardsRepeaterExtra
{
    /**
     * Definiert zusätzliche Felder für Backend
     * Rückgabe: Array mit Feldkonfigurationen wie in Element-Config
     */
    public static function GetConfig()
    {
        return [
            // Text-Feld Beispiel
            'card_rocket' => [
                'type' => 'text',
                'label' => '🚀 Raketenprogramm',
                'notice' => 'Zusätzliche Feature-Info'
            ],
            
            // Choice-Feld Beispiel
            'card_premium' => [
                'type' => 'choice',
                'label' => '💎 Premium-Status',
                'choices' => [
                    'free' => '🆓 Kostenlos',
                    'standard' => '💰 Standard',
                    'premium' => '💎 Premium',
                    'platinum' => '👑 Platinum'
                ],
                'default' => 'standard'
            ],
            
            // Datensatz-Picker Beispiel (neuer Field Type)
            'card_events' => [
                'type' => 'be_table_select',
                'label' => '📅 Termine',
                'table' => 'rex_yform_calendar',
                'field' => 'title',
                'multiple' => true,
                'notice' => 'Mehrere Termine können verknüpft werden'
            ]
        ];
    }

    /**
     * Formatiert Extra-Felder zu HTML für Frontend
     * Wird in Card-Templates aufgerufen
     */
    public static function GetOutput($item)
    {
        $html = '';

        // Raketenprogramm
        if (!empty($item['card_rocket'])) {
            $html .= '<div class="card-extra-rocket">';
            $html .= '<span class="label" style="display: inline-block; background: #ff6b6b; color: white; padding: 4px 8px; border-radius: 3px; font-size: 12px;">';
            $html .= '🚀 ' . rex_escape($item['card_rocket']);
            $html .= '</span>';
            $html .= '</div>';
        }

        // Premium-Status
        if (!empty($item['card_premium'])) {
            $statusEmoji = [
                'free' => '🆓',
                'standard' => '💰',
                'premium' => '💎',
                'platinum' => '👑'
            ];
            $emoji = $statusEmoji[$item['card_premium']] ?? '📌';
            
            $html .= '<div class="card-extra-premium" style="padding: 10px; background: rgba(100, 200, 255, 0.1); border-left: 4px solid #64c8ff; border-radius: 3px;">';
            $html .= '<strong>' . $emoji . ' ' . rex_escape($item['card_premium']) . '</strong>';
            $html .= '</div>';
        }

        // Termine
        if (!empty($item['card_events'])) {
            $eventIds = array_map('trim', explode(',', $item['card_events']));
            
            if (!empty($eventIds)) {
                $html .= '<div class="card-extra-events" style="padding: 10px; background: rgba(255, 200, 0, 0.1); border-left: 4px solid #ffc800; border-radius: 3px; margin-top: 10px;">';
                $html .= '<strong>📅 Verknüpfte Termine:</strong>';
                $html .= '<ul style="margin: 5px 0 0 20px; padding: 0;">';
                
                try {
                    $sql = rex_sql::factory();
                    $placeholders = implode(',', array_fill(0, count($eventIds), '?'));
                    $sql->setQuery("SELECT id, title FROM rex_yform_calendar WHERE id IN ({$placeholders})", array_map('intval', $eventIds));
                    
                    while ($sql->hasNext()) {
                        $html .= '<li>' . rex_escape($sql->getValue('title')) . '</li>';
                        $sql->next();
                    }
                } catch (\Exception $e) {
                    // Fallback auf IDs
                    foreach ($eventIds as $id) {
                        $html .= '<li>#' . rex_escape($id) . '</li>';
                    }
                }
                
                $html .= '</ul>';
                $html .= '</div>';
            }
        }

        return $html;
    }
}
```

### Element-Config für Extra-Felder anpassen

In der `config.php` des Elements werden die Extra-Felder automatisch geladen:

```php
<?php

// Extra-Felder von Projekt-Addon laden
$extra = [];
if (class_exists('CardsRepeaterExtra') && method_exists('CardsRepeaterExtra', 'GetConfig')) {
    $extra = CardsRepeaterExtra::GetConfig();
}

return [
    'items' => [
        'type' => 'repeater',
        'label' => 'Cards',
        
        // Extras Modal - nur wenn Extra-Felder vorhanden
        ...(!empty($extra) ? [
            'extras_modal' => [
                'label' => 'Extras',
                'icon' => 'fa-star',
                'trigger_after' => 'title',
                'fields' => array_keys($extra)
            ]
        ] : []),
        
        'fields' => [
            // Standard-Felder...
            'title' => ['type' => 'text', 'label' => 'Titel'],
            'text' => ['type' => 'cke5', 'label' => 'Text'],
            
            // Extra-Felder integrieren
            ...$extra
        ]
    ]
];
```

### Frontend Template mit Extra-Ausgabe

In den Card-Templates werden Extra-Felder automatisch ausgegeben:

```php
<?php
// _content_output.php

// Extra-Felder erkennen
$standardFields = ['layout', 'image', 'title', 'text', ...];
$extraFields = [];
if (is_array($item)) {
    foreach ($item as $key => $value) {
        if (!in_array($key, $standardFields) && !empty($value)) {
            $extraFields[$key] = $value;
        }
    }
}

// GetOutput() aufrufen wenn vorhanden
$extraFieldsHtml = '';
if (!empty($extraFields)) {
    if (class_exists('CardsRepeaterExtra') && method_exists('CardsRepeaterExtra', 'GetOutput')) {
        $extraFieldsHtml = CardsRepeaterExtra::GetOutput($item);
    }
}
?>

<!-- Extra-Felder Ausgabe -->
<?php if (!empty($extraFields)): ?>
    <div>
        <?php if (!empty($extraFieldsHtml)): ?>
            <?= $extraFieldsHtml ?>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- Standard Content -->
<div class="uk-card-body">
    <div class="uk-text"><?= $text ?></div>
</div>
```

---

## 🎛️ Neuer Feldtyp: `be_table_select`

Der neue `be_table_select` Feldtyp ermöglicht es, Datensätze aus beliebigen Tabellen auszuwählen mit Live-Search Funktionalität.

### Features

- ✅ **Beliebige Tabellen**: YForm-Tabellen oder Native REDAXO Tabellen
- ✅ **Single & Multiple**: Einzelauswahl oder Mehrfachauswahl
- ✅ **Live Search**: selectpicker mit `data-live-search="true"`
- ✅ **Komma-getrennte Speicherung**: Bei Multiple werden Werte als `"1,2,3"` gespeichert

### Verwendung

```php
'events' => [
    'type' => 'be_table_select',
    'label' => 'Termine verknüpfen',
    'table' => 'rex_yform_calendar',    // Tabelle
    'field' => 'title',                  // Anzeige-Feld
    'multiple' => true,                  // true/false
    'notice' => 'Mehrere Einträge möglich'
]
```

### Parameter

| Parameter | Typ | Erforderlich | Beschreibung |
|-----------|-----|--------------|-------------|
| `table` | string | ✅ | Tabellenname (z.B. `rex_yform_calendar`) |
| `field` | string | ✅ | Spaltenname für Anzeige (z.B. `title`) |
| `multiple` | bool | ❌ | Mehrfachauswahl (default: `false`) |
| `label` | string | ✅ | Feldbezeichnung im Backend |
| `notice` | string | ❌ | Hinweis-Text |

### Backend Ausgabe

```
📅 Termine verknüpfen
┌─────────────────────────┐
│ [🔍 Live Search]        │
│ ☑ cvcv [#1]             │
│ ☐ Termin 2 [#2]         │
│ ☐ Termin 3 [#3]         │
└─────────────────────────┘
```

### In Extra-Klasse verwenden

```php
'card_events' => [
    'type' => 'be_table_select',
    'label' => '📅 Termine',
    'table' => 'rex_yform_calendar',
    'field' => 'title',
    'multiple' => true,
    'notice' => 'Mehrere Termine können verknüpft werden'
]
```

---

## 🎛️ Datensatz-Picker Feldtypen

Es gibt **zwei verschiedene Feldtypen** für Datensatz-Auswahl mit unterschiedlichen Möglichkeiten:

### 1️⃣ `be_table_select` - Einfacher Datensatz-Picker (Selectpicker)

Der `be_table_select` Feldtyp ist ein **leichtgewichtiger selectpicker** für einfache Datensatz-Auswahl.

**Features:**
- ✅ **Single & Multiple**: Einzelauswahl oder Mehrfachauswahl
- ✅ **Live Search**: selectpicker mit `data-live-search="true"`
- ✅ **Komma-getrennte Speicherung**: Bei Multiple als `"1,2,3"`
- ✅ **Responsive Dropdown**: Platzsparend

**Beispiel:**
```php
'featured_event' => [
    'type' => 'be_table_select',
    'label' => '⭐ Highlight Termin',
    'table' => 'rex_yform_calendar',
    'field' => 'title',
    'multiple' => false
```

---

### 2️⃣ `yformpicker` - YForm Datensatz-Picker (Popup)

Das `yformpicker` Feld öffnet den YForm-Manager in einem Popup zur Auswahl von Datensäzten. Ideal für große Datenmengen.

**Features:**
- ✅ **Native YForm Integration**: Nutzt das Standard YForm Widget
- ✅ **Single & Multiple**: Unterstützt beide Modi
- ✅ **Sortierbar**: Drag & Drop oder Move-Buttons bei Multiple
- ✅ **Große Datenmengen**: Durch Pagination im Modal kein Problem

**Beispiel:**
```php
'main_event' => [
    'type' => 'yformpicker',
    'label' => '🎯 Haupttermin',
    'table' => 'rex_yform_calendar',
    'field' => 'title',
    'multiple' => false
]
```

---

## 📊 Stats

```
Lines of Code:     ~6.000
Elements:          10 (Section, Text&Image, Accordion, Headline, 
                     Divider, Cards, Slideshow, Media Showcase, 
                     Gallery, Kontaktformular)
Feldtypen:         13 (text, textarea, select, choice, checkbox, 
                     cke5, be_media, be_link, repeater,
                     radio_image, color_swatches,
                     be_table_select, yformpicker)
Field Classes:     16 (Interface, Abstract, Registry + 13 Felder)
Templates:         30 (10 × 3 Frameworks)  
CSS Files:         3 
JS Files:          2 
API:               rex_api_content_builder (5 Actions)
Features:          Feld-Plugin-System, Enhanced Media Browser, 
                   Settings Modals, Move Buttons, Grid-View Repeater, 
                   SQL-Optionen, Erweiterte Validierung
Dokumentation:     5 MD-Files
Development Time:  Mehrere intensive Sessions 🤖
```

## 📄 Lizenz

KLXM License

## 👤 Author

**KLXM Crossmedia / Thomas Skerbis**  
Website: [https://klxm.de](https://klxm.de)


## 🔗 Links

- [REDAXO](https://redaxo.org/)
- [YForm](https://github.com/yakamara/redaxo_yform)
- [Sortable.js](https://sortablejs.github.io/Sortable/)
- [UIkit](https://getuikit.com/)
- [Bootstrap](https://getbootstrap.com/docs/3.4/)
