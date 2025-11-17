# YForm Content Builder - Modul Helper

Der `yform_content_builder_module` Helper ist jetzt Teil des YForm Content Builder Addons.

## Verwendung in Modulen

**INPUT:**
```php
<?php
echo yform_content_builder_module::create('gallery')->renderInput();
?>
```

**OUTPUT:**
```php
<?php
echo yform_content_builder_module::create('gallery', 'REX_VALUE[id=1 output=html]')->renderOutput();
?>
```

## Element-Struktur im Addon

```
redaxo/src/addons/yform_content_builder/
  elements/
    gallery/
      config.php    - Feld-Definition
      element.php   - Template für Ausgabe
    divider/
      config.php
      element.php
    cards/
      config.php
      element.php
```

## Funktionsweise

1. **Config laden**: Liest automatisch `elements/{type}/config.php` aus dem Addon
2. **Input**: Generiert Formular basierend auf config.php
3. **Speichern**: Alle Daten als JSON in REX_VALUE[1]
4. **Output**: Lädt `elements/{type}/element.php` mit `$data` Array

## Beispiel: Galerie Element

### config.php
```php
<?php
return [
    'label' => 'Galerie',
    'fields' => [
        'headline' => [
            'type' => 'text',
            'label' => 'Überschrift',
        ],
        'layout' => [
            'type' => 'choice',
            'label' => 'Layout',
            'choices' => [
                'grid' => 'Raster',
                'masonry' => 'Mauerwerk',
            ],
            'default' => 'grid',
        ],
    ],
];
```

### element.php
```php
<?php
// Verfügbare Variablen:
// $data - Array mit allen Felddaten
// $config - Element-Config

$headline = $data['headline'] ?? '';
?>
<div class="gallery">
    <?php if ($headline): ?>
        <h2><?= rex_escape($headline) ?></h2>
    <?php endif; ?>
</div>
```

## Unterstützte Feld-Typen

- `text` - Einzeiliges Textfeld
- `textarea` - Mehrzeiliges Textfeld  
- `choice` - Dropdown mit `choices` Array
- `repeater` - Wiederholbare Felder (als JSON textarea)
- `be_media_enhanced` - Media-Browser (in Entwicklung)

## Installation

Die Klasse wird automatisch über `boot.php` des Addons geladen. Keine zusätzliche Konfiguration nötig.
