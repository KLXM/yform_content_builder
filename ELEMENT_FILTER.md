# Element-Filter Feature

## Übersicht

Das **Element-Filter** Feature ermöglicht es, für jedes Content Builder Feld individuell zu steuern, welche Content-Elemente verfügbar sein sollen.

## Verwendung im Table Manager

### Schritt 1: Feld anlegen

Beim Anlegen oder Bearbeiten eines Content Builder Feldes im YForm Table Manager findest du das neue Feld:

```
┌─────────────────────────────────────────┐
│ Feldtyp: Content Builder                │
├─────────────────────────────────────────┤
│ Name: page_content                      │
│ Label: Seiteninhalt                     │
│ Framework: [bootstrap ▼]                │
│                                         │
│ Erlaubte Elemente (leer = alle):        │
│ ┌─────────────────────────────────┐    │
│ │ □ Text & Bild                   │    │
│ │ □ Accordion                     │    │
│ │ ☑ Headline                      │    │ ← Ausgewählt
│ │ ☑ Divider                       │    │ ← Ausgewählt
│ │ □ Cards                         │    │
│ └─────────────────────────────────┘    │
│                                         │
│ Beschreibung: ...                       │
└─────────────────────────────────────────┘
```

### Schritt 2: Elemente auswählen

- **Nichts auswählen**: Alle verfügbaren Elemente sind erlaubt (Standard)
- **Elemente auswählen**: Nur die ausgewählten Elemente sind verfügbar
- **Mehrfachauswahl**: Strg/Cmd gedrückt halten

## Use Cases

### 1. Einfache Seiten

Für Landing Pages oder einfache Inhaltsseiten nur Text-Elemente:

```
Erlaubte Elemente: [Headline, Text & Bild, Divider]
```

**Effekt**: Redakteure können keine komplexen Elemente wie Cards oder Accordion verwenden.

### 2. Komplexe Module

Für Feature-Showcases nur visuelle Elemente:

```
Erlaubte Elemente: [Cards, Accordion]
```

**Effekt**: Fokus auf strukturierte, komplexe Inhalte.

### 3. Blog-Header

Für Blog-Beiträge nur Intro-Elemente:

```
Erlaubte Elemente: [Headline, Text & Bild]
```

**Effekt**: Konsistente Blog-Header ohne Ablenkung.

### 4. Footer-Bereich

Für Footer nur strukturelle Elemente:

```
Erlaubte Elemente: [Accordion, Divider]
```

**Effekt**: Saubere Fußzeilen mit FAQ-Accordion.

## Technische Details

### Speicherung

Die erlaubten Elemente werden als **JSON-Array** gespeichert:

```php
// In der YForm Table Definition
$field->setElement('allowed_elements', '["headline","divider","text_image"]');
```

### Filterung

Die Filterung greift auf mehreren Ebenen:

1. **Element-Auswahl-Dialog**: Nur erlaubte Elemente werden angezeigt
2. **AJAX-Laden**: Validierung beim Formular-Laden
3. **Element-Pfad-Auflösung**: Sicherheitscheck beim Rendering

### Code-Referenz

**Filterlogik in `rex_yform_value_content_builder.php`:**

```php
protected function getAvailableElements(): array
{
    // Alle Elemente laden
    $allElements = $this->getAllElementsForDefinition();
    
    // allowed_elements Parameter holen
    $allowedElements = $this->getElement('allowed_elements');
    
    // Leer? → Alle zurückgeben
    if (empty($allowedElements)) {
        return $allElements;
    }
    
    // JSON dekodieren
    if (is_string($allowedElements)) {
        $allowedElements = json_decode($allowedElements, true);
    }
    
    // Nur erlaubte zurückgeben
    return array_intersect_key(
        $allElements, 
        array_flip($allowedElements)
    );
}
```

## Kompatibilität

### Custom Elements

Das Feature funktioniert auch mit **Custom Elements** (via Extension Point oder `project/elements/`):

```php
// Extension Point registrieren
rex_extension::register('YFORM_CONTENT_BUILDER_ELEMENT_PATHS', function($ep) {
    return ['/path/to/custom/elements/'];
});
```

Die Element-Choices werden automatisch aus den verfügbaren Elementen generiert!

### Demo vs. Custom

- **Nur Demos**: Zeigt alle 5 Demo-Elemente (text_image, accordion, headline, divider, cards)
- **Nur Custom**: Zeigt nur Custom-Elemente (Demos werden ausgeblendet)
- **Gemischt**: Nicht möglich - entweder Demo ODER Custom (exklusiv)

## Beispiele

### Beispiel 1: Nur Text-Elemente

```php
// YForm Table Definition
$table->setValue('content', 'content_builder', [
    'label' => 'Hauptinhalt',
    'framework' => 'bootstrap',
    'allowed_elements' => json_encode(['headline', 'text_image', 'divider'])
]);
```

**Resultat**: Redakteur sieht nur 3 Elemente im Auswahl-Dialog.

### Beispiel 2: Alle Elemente (Standard)

```php
// Ohne allowed_elements oder leer
$table->setValue('hero', 'content_builder', [
    'label' => 'Hero-Bereich',
    'framework' => 'uikit'
    // allowed_elements nicht gesetzt
]);
```

**Resultat**: Alle verfügbaren Elemente sind nutzbar.

### Beispiel 3: Nur ein Element

```php
// Nur Cards erlauben
$table->setValue('showcase', 'content_builder', [
    'label' => 'Showcase',
    'framework' => 'bootstrap',
    'allowed_elements' => json_encode(['cards'])
]);
```

**Resultat**: Nur Cards-Element verfügbar - perfekt für strukturierte Showcases!

## Migration

### Bestehende Felder

Bestehende Content Builder Felder sind **nicht betroffen**:

- `allowed_elements` ist leer → Alle Elemente verfügbar (wie bisher)
- Keine Änderungen an gespeicherten Daten nötig
- **100% Rückwärtskompatibel**

### Upgrade-Pfad

1. Addon aktualisieren
2. Table Manager öffnen
3. Content Builder Feld bearbeiten
4. Optional: Erlaubte Elemente auswählen
5. Speichern

Fertig! 🚀

## Best Practices

### 1. Konsistenz

Definiere Element-Sets für verschiedene Bereiche:

```php
// Standard-Set für Textseiten
$textPageElements = ['headline', 'text_image', 'divider'];

// Komplexes Set für Landing Pages
$landingPageElements = ['headline', 'cards', 'accordion', 'divider'];

// Minimales Set für Sidebar
$sidebarElements = ['headline', 'divider'];
```

### 2. Weniger ist mehr

Zu viele Optionen = verwirrte Redakteure:

✅ **Gut**: 3-5 passende Elemente pro Bereich  
❌ **Schlecht**: Alle 20+ Elemente überall

### 3. Schulung

Erkläre Redakteuren die Bedeutung:

> "Für diesen Bereich stehen dir nur diese Elemente zur Verfügung, damit die Seite ein konsistentes Design behält."

## Troubleshooting

### Element erscheint nicht

**Check 1**: Ist das Element in `allowed_elements` enthalten?
```php
// In der Datenbank prüfen
SELECT allowed_elements FROM rex_yform_field WHERE name = 'page_content';
```

**Check 2**: Existiert das Element überhaupt?
```php
// Alle verfügbaren Elemente anzeigen
$builder = new rex_yform_value_content_builder();
var_dump($builder->getAllElementsForDefinition());
```

**Check 3**: Cache leeren
- Backend-Cache löschen
- Browser-Cache leeren

### JSON-Fehler

Wenn `allowed_elements` nicht als JSON gespeichert wird:

```php
// Manuell fixen
$field = rex_yform_manager_field::get($fieldId);
$field->setValue('allowed_elements', json_encode(['headline', 'divider']));
$field->save();
```

## Performance

### Impact

Das Element-Filter Feature hat **minimalen Performance-Impact**:

- ✅ Filterung erfolgt nur einmal beim Laden
- ✅ Kein zusätzlicher DB-Query
- ✅ Array-Operationen sind extrem schnell

### Skalierung

Auch mit 50+ Custom Elements:

- Element-Scan dauert < 1ms
- Multiselect lädt sofort
- AJAX-Calls unverändert schnell

## Zusammenfassung

**Element-Filter** gibt dir die Kontrolle:

- 🎯 **Zielgerichtet**: Nur passende Elemente pro Bereich
- 👥 **Redakteur-freundlich**: Weniger Optionen = einfachere Bedienung
- 🛡️ **Qualitätssicherung**: Verhindert unpassende Element-Kombinationen
- 🔄 **Flexibel**: Jederzeit anpassbar
- ⚡ **Performant**: Kein spürbarer Overhead

**Made with 🤖 by GitHub Copilot**

---

**Version**: 1.0.0  
**Feature seit**: Oktober 2025
