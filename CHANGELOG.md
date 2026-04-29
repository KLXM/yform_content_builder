# Changelog

Alle wesentlichen Änderungen an diesem Projekt werden hier dokumentiert.

Das Format basiert auf [Keep a Changelog](https://keepachangelog.com/de/1.0.0/).

---



## [2.0.0] – 2026-04-29

### Elemente

Das bisherige Table-Element wurde durch den neuen `table_editor`-basierten Tabelleneditor ersetzt.

### Übersetzungen (i18n)

Elemente können jetzt übersetzt werden und auch mehrsprachige Hilfen verlinkt werden

### Komplette Namespace-Umstrukturierung (Breaking Change)

Alle Klassen des Addons wurden in den PSR-4-Namespace `KLXM\YFormContentBuilder` verschoben. Die Klassen sind jetzt in `lib/` und `lib/Api/` sowie `lib/fields/` organisiert und werden vollständig vom REDAXO-Autoloader geladen.

#### Neue Klassen-Namen (kanonisch)

| Alter Name (bis 1.x) | Neuer Name (2.0) |
|---|---|
| `yform_content_builder_module` | `KLXM\YFormContentBuilder\Module` |
| `yform_content_builder_helper` | `KLXM\YFormContentBuilder\Helper` |
| `yform_content_builder_config` | `KLXM\YFormContentBuilder\Config` |
| `YFormContentBuilderSvg` | `KLXM\YFormContentBuilder\Svg` |
| `YFormContentBuilderMediaAltResolver` | `KLXM\YFormContentBuilder\MediaAltResolver` |
| `YFormContentMediaManagerHelper` | `KLXM\YFormContentBuilder\MediaManagerHelper` |
| `YformListProfiles` | `KLXM\YFormContentBuilder\ListProfiles` |
| `YformListRenderer` | `KLXM\YFormContentBuilder\ListRenderer` |
| `ForcalListRenderer` | `KLXM\YFormContentBuilder\ForcalRenderer` |
| `ContentBuilderFieldRegistry` | `KLXM\YFormContentBuilder\Fields\FieldRegistry` |
| `ContentBuilderFieldAbstract` | `KLXM\YFormContentBuilder\Fields\FieldAbstract` |
| `ContentBuilderFieldInterface` | `KLXM\YFormContentBuilder\Fields\FieldInterface` |
| `rex_api_content_builder` | `KLXM\YFormContentBuilder\Api\ContentBuilderApi` |
| `rex_api_yform_list_columns` | `KLXM\YFormContentBuilder\Api\ListColumnsApi` |

#### Neue Sub-Namespaces

- **`KLXM\YFormContentBuilder`** – Kernklassen (`Module`, `Helper`, `Config`, `Svg`, `MediaAltResolver`, `MediaManagerHelper`, `ModalHelper`, `ListProfiles`, `ListRenderer`, `ForcalRenderer`)
- **`KLXM\YFormContentBuilder\Fields`** – Feldtypen-System (`FieldRegistry`, `FieldAbstract`, `FieldInterface`, `TextField`, `TextareaField`, `CheckboxField`, `ChoiceField`, `SelectField`, `RadioImageField`, `ColorSwatchesField`, `BeMediaField`, `BeLinkField`, `BeTableSelectField`, `Cke5Field`, `TinyMceField`, `RepeaterField`, `InfoField`, `YFormPickerField`)
- **`KLXM\YFormContentBuilder\Api`** – AJAX-API-Klassen (`ContentBuilderApi`, `ListColumnsApi`)

#### `boot.php` aufgeräumt

- Alle `require_once`-Aufrufe entfernt – REDAXO-Autoloader lädt alle Klassen in `lib/` und Unterordnern automatisch.
- `glob`-Schleife für Feldklassen entfernt – Felder werden von `FieldRegistry` on-demand geladen.
- `rex_api_function::register()` statt `class_alias` für API-Klassen (sauberere Integration).
- Interne `class_alias`-Einträge entfernt: `rex_api_content_builder`, `rex_api_yform_list_columns`, `yform_content_builder_help_modal_helper`.

#### Abwärtskompatibilität (class_alias)

Die folgenden alten Klassennamen bleiben über `class_alias` in `boot.php` verfügbar – bestehende Module und Projekte müssen **nicht** sofort angepasst werden:

```
yform_content_builder_module      → KLXM\YFormContentBuilder\Module
yform_content_builder_helper      → KLXM\YFormContentBuilder\Helper
yform_content_builder_config      → KLXM\YFormContentBuilder\Config
YFormContentBuilderSvg            → KLXM\YFormContentBuilder\Svg
YFormContentBuilderMediaAltResolver → KLXM\YFormContentBuilder\MediaAltResolver
YFormContentMediaManagerHelper    → KLXM\YFormContentBuilder\MediaManagerHelper
YformListProfiles                 → KLXM\YFormContentBuilder\ListProfiles
YformListRenderer                 → KLXM\YFormContentBuilder\ListRenderer
ForcalListRenderer                → KLXM\YFormContentBuilder\ForcalRenderer (nur wenn forcal aktiv)
```

### Modul-Installer

- Generierter Modul-Code (Input & Output) verwendet nun `use KLXM\YFormContentBuilder\Module;` + `Module::` statt des alten `yform_content_builder_module::`-Alias.
- Gilt für neu erstellte **und** aktualisierte Module (Button „Bestehende Module aktualisieren").

### Empfohlene Schreibweise für neuen Code

```php
// Input-Modul
<?php
use KLXM\YFormContentBuilder\Module;
echo Module::createByValueId('cards', 1, 'uikit')->renderInput();

// Output-Modul
<?php
use KLXM\YFormContentBuilder\Module;
$slice = $this->getCurrentSlice();
$rawValue = $slice ? (string) $slice->getValue(1) : '';
echo Module::create('cards', $rawValue, 'uikit', 1)->renderOutput();

// Frontend (YForm)
<?php
use KLXM\YFormContentBuilder\Helper;
echo Helper::render($page->getValue('content'), 'bootstrap');
```

### Dokumentation

- Alle Code-Beispiele in `README.md`, `API.md` und `TUTORIAL.md` verwenden jetzt durchgängig `use KLXM\YFormContentBuilder\Module;` bzw. `use KLXM\YFormContentBuilder\Helper;`.
- Namespace-Tippfehler `KLXM\YformContentBuilder` → `KLXM\YFormContentBuilder` in `TUTORIAL.md` korrigiert.
- Backward-Compat-Hinweise in allen Docs aktualisiert: klare Empfehlung für `use`-Importe in neuem Code.

---

## [1.14.0] – 2026-04-28

### Neu

- **6 neue Starter-Elemente** hinzugefügt mit vollständiger Template-Unterstützung (UIkit, Bootstrap, Plain):
  - **Starter-Text**: RichText-Element mit TinyMCE Editor
  - **Starter-Ueberschrift**: bewusst reduziertes Headline-Element (nur Text + HTML-Tag)
  - **Starter-Media-Split**: Media-Bild + Text-Inhalt nebeneinander
  - **Starter-Gallery**: Repeater-basierte Galerieliste
  - **Starter-Cards**: Repeater-basierte Kartenliste mit Bild, Title, Content
  - **Starter-Callout**: Highlight-Box mit Titel, Text und optionalem Link
- Modul-Installer-Seite mit Kategorien-Gruppierung für bessere Übersichtlichkeit
- Alle neuen Elemente unterstützen Content Builder's Framework-Abstraktion (UIkit/Bootstrap/Plain)
- **Zentrales Hilfe-System für Elemente**: Help-Modal-Logik wurde in eine gemeinsame Klasse ausgelagert, sodass Modul-Renderer, YForm-Value-Renderer und API-Renderer denselben Codepfad nutzen.
- **Neue Element-Hilfe fuer `cards`**: Das Karten-Element hat jetzt eine eigene redaktionelle Hilfe-Datei mit Erklaerungen zu Grid, Card-Layouts, Medieneinstellungen, Verlinkung und Animationen.

### Bugfix

- **TinyMCE Re-Open Bug**: TinyMCE-Editoren werden jetzt beim erneuten Öffnen eines Elements korrekt initialisiert (nicht nur "refreshed")
- Vereinfachte Initialisierungslogik in `editSlice()` durch Aufruf von `tiny_init()` statt komplexer `dispatch()`-Fallbacks

---

## [1.13.0] – 2026-04-28

### Neu

- Addon-Startseite erweitert: README kann als eigene Backend-Seite angezeigt werden (`Dokumentation`).
- Auf der Übersichtsseite werden alle Elemente mit Metadaten dargestellt (Icon, Beschreibung, Kategorie, Version, Key).
- In allen Element-`config.php` Dateien ist nun eine einheitliche top-level Versionsangabe enthalten (`'version' => '1.12.0'`).

### Verbesserungen

- Element-Menüs im Builder zeigen Beschreibungen jetzt als verzögertes Tooltip bei Hover (statt dauerhaft sichtbarer Zweitzeile).
- Tooltip-Logik gilt für statische Dropdown-Einträge und dynamisch per JavaScript erzeugte Insert-Menüs.
- Element-Übersicht auf der Hauptseite visuell überarbeitet und für BS3 robust per Inline-Styles umgesetzt.
- Element-Übersicht nochmals verdichtet: statt luftiger Kartenansicht jetzt kompaktere, kategorisierte Listen-Panels mit schnellerer Scanbarkeit.
- Kategorien in den Element-`config.php` Dateien vereinheitlicht und für alle mitgelieferten Elemente gesetzt.

### Modul-Integration

- `renderInput()` nutzt pro Instanz eindeutige IDs auf Basis von `slice_id` und `valueId`, wodurch mehrere Instanzen im selben Modul konfliktfrei funktionieren.
- Modul-Generator um den Button **„Bestehende Module aktualisieren“** erweitert (regeneriert vorhandene `yfcb_*` Module mit aktuellem Input-/Output-Code).

---

## [1.12.0] – 2026-04-28

### Bugfix: Mehrere `renderInput()`-Instanzen auf einer Seite

- `renderInput()` verwendete hardcodierte IDs (`yform_cb_data_storage`, `yform_cb_form`), was bei zwei oder mehr Aufrufen auf derselben Seite zu Kollisionen führte.
- Neue Methode `getInstanceId()` erzeugt eine eindeutige ID aus `s{sliceId}_v{valueId}`.
- Alle HTML-IDs und JavaScript-Selektoren nutzen nun diese eindeutigen IDs.
- Zwei Aufrufe wie `createByValueId('cards', 1)` und `createByValueId('text', 2)` in einem Modul funktionieren jetzt korrekt nebeneinander.

### Modul-Generator: Button „Bestehende Module aktualisieren"

- Neuer Button **„Bestehende Module aktualisieren"** auf der Modul-Generator-Seite.
- Findet alle vorhandenen `yfcb_*`-Module und schreibt deren `input`/`output`-Code neu.
- Nützlich nach Framework- oder API-Updates, um alle Module auf den aktuellen Stand zu bringen.
- Framework und VALUE-Slot werden aus dem Formular übernommen.

---

## [1.11.0] – 2026-04-28

### Modul-API für Value-Slots vereinfacht

- Neue, verständlichere Modul-Factory: `yform_content_builder_module::createByValueId($type, $valueId, $framework)`
- Dadurch muss in Modulen kein `REX_VALUE[...]`-String mehr manuell übergeben werden, wenn nur ein fester Slot genutzt wird.

### Modul-Generator aktualisiert

- Der Modul-Generator erzeugt neue Module jetzt mit `createByValueId(...)`.
- Der gewählte Value-Slot (1–20) wird weiterhin berücksichtigt und eindeutig für Laden/Speichern genutzt.

### Rückwärtskompatibilität

- Bestehende Module mit alter Schreibweise `create($type, 'REX_VALUE[...]', $framework)` funktionieren unverändert weiter.
- Bestehende Module auf `REX_VALUE[1]` bleiben vollständig kompatibel.

---

## [1.10.0] – 2026-04-27

### Zwei neue No-Code-Listen-Elemente

Mit dieser Version kommen zwei neue Daten-Elemente hinzu, mit denen Redakteure dynamische Listen direkt im Content Builder zusammenstellen – ganz ohne eigenes Modul oder PHP-Code.

#### `yform_list` – Listen aus YForm-Tabellen

Server-seitig gerenderte Auflistung aus beliebigen YForm-Tabellen (z. B. News, Produkte, Mitarbeiter, Veranstaltungen).

- **Profile in den Addon-Einstellungen**: Tabelle, anzuzeigende Spalten, Sortierung, Filter (z. B. `status=1`) und URL-Schema werden zentral als Profil hinterlegt.
- **Im Element wählt der Redakteur nur**: Profil, Layout (Cards / Liste / Kompakt), Anzahl, optional Headline & Beschreibung.
- **Layouts**: Cards mit Bild oben, Liste mit Bild + Anriss, Kompakt nur mit Titel.
- **Kontakt-Karten** (Layout `contact`): Speziell für Mitarbeiter-/Ansprechpartner-Tabellen. Mappt Vorname, Nachname, Freitext, Funktion, Telefon, Mobil und E-Mail auf eine Avatar-Card mit `tel:` / `mailto:`-Links. Mediamanager-Typ `avatar` ist Default – das Cropping wird ausschliesslich über den MM-Typ gesteuert, nicht über Code.
- **Kontakt kompakt** (Layout `contact_compact`): Schmale Card mit Avatar im Card-Header (UIkit `uk-card-header` + `uk-grid-small uk-flex-middle`), Name + Funktion, optional Telefon/Mobil/E-Mail im Body. Ideal für Sidebar oder dichte Kontakt-Übersichten.
- **Frameworks**: Templates für UIkit3, Bootstrap und Plain HTML.

#### `contact_picker` – Einzelne Kontakte pickern

Neues Daten-Element zum gezielten Auswählen einzelner Kontakte aus den hinterlegten Profilen.

- **Auswahl über Bootstrap-Selectpicker**: Multi-Select mit allen Einträgen aus allen Kontakt-Profilen (Format `[Profil-Label] Vorname Nachname`).
- **Reihenfolge der Auswahl bleibt erhalten**.
- **Layouts**: Kontakt kompakt, Kontakt-Karten (zentriert, ausführlich), Liste.
- **Verfügbarkeit**: Erscheint nur, wenn mindestens ein Kontakt-Profil (Profil mit gesetztem Vorname-Mapping) existiert.
- Nutzt denselben Renderer wie `yform_list` – Profil-Konfiguration (Tabelle, Felder, MM-Typ, URL-Schema) wird einmal zentral gepflegt.

#### `forcal_list` – Termine aus dem forcal-Kalender

Auflistung kommender Termine aus dem [forcal](https://github.com/FriendsOfREDAXO/forcal)-Addon.

- **Zwei Modi**:
  - *Nach Kategorie(n)* – kommende Termine, Mehrfachauswahl möglich.
  - *Wiederkehrender Termin* – die nächsten X Wiederholungen eines Serientermins.
- **Block-Listen-Gruppierung** (optional): keine, nach Tag, Monat, Jahr oder verschachtelt nach Jahr/Monat.
- **Konfigurierbare Headlines**: Tag (`h1`–`h6`) und UIkit-Style (`uk-heading-line`, `uk-heading-medium` …) sowohl für die Haupt-Überschrift als auch für die Gruppen-Überschriften.
- **Sektion**: Hintergrundfarbe, Hintergrundbild oder -video (mp4/webm), `uk-light` für helle Schrift auf dunklem Grund.
- **Bildausgabe optional**: Neue Checkbox „Bild anzeigen". Bild wird automatisch aus `image` / `entries_image` / `lang_image_<clang>` ermittelt; eigener Feldname konfigurierbar. Bilder laufen über den Media Manager Typ `card`.
- **Verfügbarkeit**: Das Element wird in der Element-Liste **nur angezeigt, wenn das forcal-Addon installiert und aktiviert ist**. Ohne forcal taucht es gar nicht erst auf – kein Leerlauf für Redakteure.
- **Hinweis zum URL-Schema**: Das Pattern für die Detailseite (`url_pattern`) ist aktuell ein technischer Platzhalter-String. Hier wird in einer der nächsten Versionen ein redakteursfreundlicherer Picker folgen.

### Loader-Änderung

`getAllElementsForDefinition()` überspringt jetzt Element-Configs, die kein Array zurückgeben. Damit können sich Elemente per `return null;` selbst deaktivieren, wenn ihre Voraussetzungen (z. B. ein Drittaddon) nicht erfüllt sind. `forcal_list` nutzt diesen Mechanismus.

---

## [1.9.0] – 2026-04-24

### Umstellung der WYSIWYG-Felder von CKEditor 5 auf TinyMCE

Alle im Addon ausgelieferten Element-Configs, die bisher den Feldtyp `cke5` verwendet haben, nutzen ab dieser Version den Feldtyp `tinymce`. Das Feld `TinyMceField` war bereits in der `ContentBuilderFieldRegistry` registriert und einsatzbereit – es fehlte nur die Umstellung der mitgelieferten Elemente.

**Betroffene Elemente und Profile**

| Element        | Feld      | Profil    |
| -------------- | --------- | --------- |
| `accordion`    | `content` | `default` |
| `media_text`   | `text`    | `default` |
| `cards`        | `text`    | `default` |
| `moving_tiles` | `text`    | `light`   |

Haupt-Inhaltsbereiche bekommen das umfangreiche `default`-Profil, kompakte Teaser-Texte in Kacheln das schlankere `light`-Profil.

**Migration / Kompatibilität**

- Bestehende Inhalte bleiben unverändert – gespeichert wird weiterhin HTML im selben Feld.
- Eigene Element-Configs mit `'type' => 'cke5'` funktionieren unverändert weiter (der `Cke5Field`-Typ ist nach wie vor registriert). Ein Umstellen auf TinyMCE ist optional und kann Element-weise erfolgen.
- Beim Umstellen reicht es, `'type' => 'cke5'` durch `'type' => 'tinymce'` zu ersetzen und bei Bedarf `'profile' => 'default'` bzw. `'profile' => 'light'` zu ergänzen.

---



### Online/Offline-Schaltung für Abschnitte (YForm-Variante, optional)

Einzelne Slices im YForm Content Builder können jetzt optional offline geschaltet werden, ohne sie zu löschen. Dies gilt **ausschließlich für die YForm-Variante** – die Modulvariante nutzt weiterhin die normalen REDAXO-Slice-Funktionen für Online/Offline.

**Konfigurierbar in den Einstellungen**
- Neue Option „Online/Offline pro Abschnitt" unter *Einstellungen* (Addon-Config `enable_online_toggle`)
- Standardmäßig **deaktiviert** – bestehende Installationen verhalten sich unverändert
- Nur wenn aktiv, erscheint der Augen-Button in der Slice-Toolbar

**Neu im Backend (bei aktivierter Option)**
- Augen-Button (`fa-eye` / `fa-eye-slash`) in der Slice-Toolbar zum Umschalten
- Offline geschaltete Slices werden visuell deutlich markiert: roter gestrichelter Rahmen, diagonale Streifen, reduzierte Opacity, Graustufen-Filter im Rendered-Bereich und „OFFLINE"-Badge oben rechts
- Dark-Mode-Support

**Frontend-Verhalten**
- `yform_content_builder_helper::render()` filtert offline geschaltete Slices automatisch aus der Ausgabe
- Auch `extractImages()` und `extractFirstText()` (z.B. für OG-Tags / Meta-Description) überspringen offline-Slices
- Das Beispiel in `examples/frontend_output.php` wurde entsprechend angepasst

**Datenformat**
- Im JSON erhält jeder Slice ein optionales Feld `online: true|false`
- Fehlt das Feld (Bestandsdaten), gilt der Slice als online – vollständig abwärtskompatibel
- Daten bleiben auch nach Deaktivierung der Option erhalten

---

## [1.7.0] – 2026-03-18

### Kontaktformular: Progressive AJAX + Security-Hardening

**Barrierefreies AJAX als Zusatz (Progressive Enhancement)**
- Neues Feld im Design-Tab: `ajax_enhancement`
- Ohne JavaScript bleibt das klassische POST-Verhalten vollständig erhalten.
- Mit JavaScript wird das Formular optional per Fetch gesendet, der Formularbereich aus der Server-Antwort ersetzt und Feedback per `aria-live` + Fokussteuerung zugänglich gemacht.
- Umsetzung über externe Datei `assets/contact_form/contact-form-ajax.js` (kein Inline-JS).

**Sicherheit verbessert**
- CSRF-Schutz ergänzt (`rex_csrf_token`) inkl. Hidden Field und Validierung beim Submit.
- `compare`-Validierung ohne `eval()`: sichere Parser-Logik für Vergleiche wie `{{feld}} < {{100}}` oder `{{feldA}} == {{feldB}}`.

**E-Mail-Versand verbessert**
- Feld `email_to` unterstützt jetzt mehrere Empfänger (Komma/Semikolon getrennt).
- Reply-To wird gesetzt, sobald eine Absender-E-Mail im Formular erkannt wurde.

---

## [1.6.0] – 2026-03-18

### Neue Elemente & Bildeffekte

**Neues Element: Timeline**
Vertikale Zeitlinie für Meilensteine, Prozesse oder Ereignisse. Jeder Eintrag hat Datum, Titel, Beschreibung, optionalen UIkit-Icon und Badge. Drei Stile: Standard (Punkt + Linie), Karten und Alternierend (links/rechts wechselnd). Farbe des Punktes und Linienstil konfigurierbar. Templates für UIkit, Bootstrap und Plain.

**Bild & Text – Bildstapel & Overlap**
Das `media_text`-Element erhält den neuen Parameter **Bild-Effekt** im Design-Tab:
- **Bildstapel**: Dekorative CSS-Pseudo-Elemente erzeugen einen gestapelten Tiefeneffekt hinter dem Hauptbild (kein zweites Bild notwendig).
- **Overlap**: Das Bild-Spalte ragt auf Desktop-Screens mit `margin-inline-end: -60px` in den Textbereich hinein – für einen modernen, aufmerksamkeitsstarken Look.

---

## [1.5.0] – 2026-03-18

### Hero Banner Parallax

Das Hero Banner Element unterstützt jetzt optionale Parallax-Effekte für Hintergrund und Textcontent.

**Neue Felder im Design-Tab des Hero Banners:**

- **Parallax-Hintergrund** (`parallax_bg`): Checkbox – aktiviert `uk-parallax bgy` auf dem Cover-Bild. Das Hintergrundbild scrollt langsamer als die Seite (klassischer Parallax-Effekt). Nur verfügbar bei Bild-Hintergrund, nicht bei Video.
- **Parallax-Stärke (Hintergrund)** (`parallax_bg_velocity`): Wahl zwischen Dezent (150), Mittel (300) und Intensiv (500).
- **Parallax-Text** (`parallax_content`): Checkbox – der Content-Block (Überschrift, Text, Buttons) hebt sich beim Scrollen leicht nach oben ab (`uk-parallax y: -60`).

**Technische Umsetzung:**
- Hintergrund: `uk-parallax="bgy: -{velocity}"` direkt auf dem `<img uk-cover>`-Tag – UIkit 3 verschiebt die absolut positionierte Cover-Image auf der y-Achse.
- Text: `uk-parallax="y: -60; easing: 1"` auf dem Content-Container.

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
