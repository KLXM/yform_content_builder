# YForm Content Builder - JSON Schema

This addon includes a formal JSON Schema to validate Element Configurations.

## 📋 Schema File

**File:** `element-config.schema.json`  
**Path:** `redaxo/src/addons/yform_content_builder/element-config.schema.json`

## 🤖 Usage for AI Agents

When asking an AI to generate a new element, you can provide this schema to ensure the output is valid.

**Prompt Example:**
> "Create a new YForm Content Builder element for a 'Testimonial Slider'. Please follow the structure defined in `element-config.schema.json`."

## 🔧 Usage in IDEs (VS Code)

You can configure VS Code to use this schema for validation and autocompletion in your `config.php` files (if you use a JSON-to-PHP mapping or just for reference).

Since `config.php` files are PHP, direct JSON schema validation isn't native without plugins, but you can use the schema to understand the structure.

## 📄 Structure Overview

The schema defines:
- **Root Properties**: `label`, `description`, `icon`, `fields`.
- **Field Types**: `text`, `textarea`, `choice`, `checkbox`, `ckeditor5`, `be_media`, `be_media_enhanced`, `be_link`, `repeater`.
- **Recursive Definitions**: `repeater` fields can contain other fields.

See `element-config.schema.json` for the full definition.

## 📂 Physical Element Structure

To create a valid element, an AI must understand the file system structure.

### Directory Layout

Each element resides in its own subdirectory within the `elements/` folder. The folder name is the **Element Key**.

```text
elements/
└── {element_key}/              # e.g. "hero_header" (snake_case)
    ├── config.php              # Returns the Configuration Array (see Schema)
    └── templates/              # Output Templates
        ├── bootstrap.php       # Required: Bootstrap 3/4/5 Template
        ├── uikit.php           # Optional: UIkit 3 Template
        ├── tailwind.php        # Optional: Tailwind CSS Template
        └── plain.php           # Optional: Fallback/Plain HTML
```

### File Contents

#### 1. `config.php`
Must return a PHP array matching the JSON Schema structure.

```php
<?php
return [
    'label' => 'Hero Header',
    'icon' => 'fa-header',
    'fields' => [ ... ]
];
```

#### 2. Templates (`templates/*.php`)
Standard PHP templates. Variables are available via `$elementData` array.

```php
<?php
/** @var array $elementData */
// Access fields defined in config.php
$headline = $elementData['headline'] ?? '';
?>
<div class="hero">
    <h1><?= rex_escape($headline) ?></h1>
</div>
```

## 🧠 System Prompt Context

If you want to feed this context to an AI, you can copy the following block:

> **YForm Content Builder Context:**
> 1. **Structure**: Elements are folders in `elements/{key}/`.
> 2. **Config**: `config.php` returns a PHP array defining fields (text, textarea, be_media, repeater, etc.).
> 3. **Templates**: `templates/bootstrap.php` is the default output. Use `$elementData['fieldname']` to access values.
> 4. **Schema**: Follow the `element-config.schema.json` for field definitions.
