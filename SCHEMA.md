# JSON Schema für Element-Konfiguration

Das YForm Content Builder JSON Schema ermöglicht Validierung und IDE-Unterstützung für Element-Konfigurationen.

## 📋 Schema-Datei

**Location**: `/schema/element-config.schema.json`

**Schema URL**: `https://redaxo.org/schemas/yform_content_builder_element.json`

## 🔧 IDE-Integration

### VS Code

Erstelle eine `.vscode/settings.json` in deinem Projekt:

```json
{
  "json.schemas": [
    {
      "fileMatch": [
        "**/elements/*/config.php"
      ],
      "url": "./src/addons/yform_content_builder/schema/element-config.schema.json"
    }
  ],
  "php.validate.enable": true,
  "php.suggest.basic": true
}
```

Für PHP-Dateien mit Array-Return kannst du einen JSON-Kommentar hinzufügen:

```php
<?php
/**
 * @schema element-config.schema.json
 */

return [
    "name" => "my_element",
    "title" => "My Element",
    // ... rest of config
];
```

### PhpStorm

1. **Settings** → **Languages & Frameworks** → **Schemas and DTDs** → **JSON Schema Mappings**
2. **Add new mapping**:
   - **Schema file or URL**: `./schema/element-config.schema.json`
   - **File pattern**: `*/elements/*/config.php`

### Andere IDEs

Die meisten modernen IDEs unterstützen JSON Schema. Konsultiere die Dokumentation deiner IDE für die spezifische Konfiguration.

## ✅ Schema-Validierung

### Online-Validierung

Nutze Online-Tools wie [jsonschemavalidator.net](https://www.jsonschemavalidator.net/):

1. Schema aus `element-config.schema.json` einfügen
2. Deine Element-Konfiguration als JSON einfügen
3. Validierung ausführen

### Command Line (Node.js)

Installation:
```bash
npm install -g ajv-cli
```

Validierung:
```bash
ajv validate -s schema/element-config.schema.json -d "elements/*/config.json"
```

### PHP-Validierung

```php
<?php
use JsonSchema\Validator;
use JsonSchema\Constraints\Constraint;

// Config als JSON laden
$config = json_decode(file_get_contents('elements/gallery/config.json'));
$schema = json_decode(file_get_contents('schema/element-config.schema.json'));

// Validierung
$validator = new Validator();
$validator->validate($config, $schema, Constraint::CHECK_MODE_COERCE_TYPES);

if ($validator->isValid()) {
    echo "✅ Configuration is valid\n";
} else {
    echo "❌ Validation errors:\n";
    foreach ($validator->getErrors() as $error) {
        echo sprintf("- %s: %s\n", $error['property'], $error['message']);
    }
}
```

## 🏗️ Schema-Struktur

### Root-Eigenschaften

- **name**: Eindeutiger Element-Identifier (snake_case)
- **title**: Anzeigename für Users
- **description**: Optionale Beschreibung
- **icon**: Font Awesome oder REDAXO Icon
- **fields**: Array von Feld-Definitionen
- **options**: Element-weite Optionen

### Feld-Typen

Schema unterstützt alle verfügbaren Feldtypen:

- **Basis**: text, textarea, select, checkbox, radio, number
- **Rich Content**: ckeditor5, html
- **Media**: be_media, be_media_enhanced
- **Links**: be_link
- **Struktur**: repeater, fieldset, hidden
- **Datum**: date, datetime, time

### Validierungsregeln

#### Namen-Validierung
```json
{
  "pattern": "^[a-z][a-z0-9_]*[a-z0-9]$"
}
```
- Muss mit Buchstabe beginnen
- Nur Kleinbuchstaben, Zahlen, Underscores
- Muss mit Buchstabe oder Zahl enden

#### Icon-Validierung
```json
{
  "pattern": "^(fa-[a-z0-9-]+|rex-icon [a-z0-9-]+)$"
}
```
- Font Awesome: `fa-` prefix
- REDAXO Icons: `rex-icon ` prefix

#### Conditional Validation

Schema nutzt `if/then/else` für feldtyp-spezifische Validierung:

```json
{
  "if": {
    "properties": {"type": {"const": "select"}}
  },
  "then": {
    "required": ["options"],
    "properties": {
      "options": {
        "type": "object",
        "patternProperties": {
          ".*": {"type": "string"}
        }
      }
    }
  }
}
```

## 📝 Beispiel-Konfigurationen

### Basis-Element

```json
{
  "name": "simple_text",
  "title": "Simple Text",
  "icon": "fa-font",
  "fields": [
    {
      "type": "text",
      "name": "title",
      "label": "Title",
      "required": true
    },
    {
      "type": "textarea",
      "name": "content",
      "label": "Content"
    }
  ]
}
```

### Komplexes Element mit Tabs

```json
{
  "name": "advanced_hero",
  "title": "Advanced Hero",
  "description": "Hero section with media, text and advanced styling options",
  "icon": "fa-image",
  "options": {
    "tabs": true,
    "modal": false
  },
  "fields": [
    {
      "type": "be_media_enhanced",
      "name": "background",
      "label": "Background Media",
      "aspect_ratio": "16:9",
      "allowed_types": "jpg,jpeg,png,gif,mp4,webm",
      "video_autoplay": true,
      "video_muted": true,
      "tab": "content"
    },
    {
      "type": "text",
      "name": "headline",
      "label": "Headline",
      "required": true,
      "tab": "content"
    },
    {
      "type": "select",
      "name": "alignment",
      "label": "Text Alignment",
      "options": {
        "left": "Left",
        "center": "Center",
        "right": "Right"
      },
      "default": "center",
      "tab": "settings"
    }
  ]
}
```

### Gallery mit Grid-Repeater

```json
{
  "name": "media_gallery",
  "title": "Media Gallery",
  "icon": "fa-images",
  "options": {
    "repeater_view": "grid",
    "grid_columns": 4
  },
  "fields": [
    {
      "type": "repeater",
      "name": "items",
      "label": "Gallery Items",
      "view_mode": "grid",
      "grid_columns": 4,
      "modal": true,
      "sortable": true,
      "min_items": 1,
      "fields": [
        {
          "type": "be_media_enhanced",
          "name": "media",
          "label": "Media",
          "aspect_ratio": "1:1",
          "allowed_types": "jpg,jpeg,png,gif,mp4,webm",
          "required": true
        },
        {
          "type": "text",
          "name": "caption",
          "label": "Caption"
        }
      ]
    }
  ]
}
```

## 🚨 Häufige Validierungsfehler

### 1. Ungültiger Element-Name
```
❌ Error: name "MyElement" does not match pattern
✅ Fix: Use "my_element" instead
```

### 2. Fehlende Required Properties
```
❌ Error: Missing required property "options" for select field
✅ Fix: Add "options": {"key": "value"} to select field
```

### 3. Ungültiger Icon
```
❌ Error: icon "my-icon" does not match pattern  
✅ Fix: Use "fa-my-icon" or "rex-icon rex-icon-my-icon"
```

### 4. Falsche Array-Struktur
```
❌ Error: fields should be array of objects
✅ Fix: Wrap each field in array: [{"type": "text", ...}]
```

## 🔄 Schema-Updates

Das Schema wird kontinuierlich erweitert:

- **Neue Feldtypen** werden automatisch hinzugefügt
- **Breaking Changes** werden versioniert
- **Rückwärtskompatibilität** wird gewährleistet

### Versioning

Schema folgt Semantic Versioning:
- **Major**: Breaking Changes (neue Required Fields)
- **Minor**: Neue Features (neue Feldtypen, Optionen)  
- **Patch**: Bugfixes (verbesserte Validierung)

### Migration

Bei Schema-Updates prüfe:
1. Bestehende Konfigurationen validieren
2. Neue Features nutzen
3. Deprecated Features ersetzen

## 🛠️ Development

### Schema erweitern

Neue Feldtypen hinzufügen:

```json
{
  "if": {
    "properties": {"type": {"const": "my_new_field"}}
  },
  "then": {
    "properties": {
      "my_option": {
        "type": "string",
        "description": "My new option"
      }
    }
  }
}
```

### Testing

Schema-Tests mit verschiedenen Konfigurationen:

```bash
# Valid configs
ajv validate -s schema.json -d test/valid/*.json

# Invalid configs (should fail)
ajv validate -s schema.json -d test/invalid/*.json
```

## 📚 Ressourcen

- [JSON Schema Specification](https://json-schema.org/)
- [Understanding JSON Schema](https://json-schema.org/understanding-json-schema/)
- [AJV Validator](https://ajv.js.org/)
- [VS Code JSON Schema Support](https://code.visualstudio.com/docs/languages/json#_json-schemas-and-settings)