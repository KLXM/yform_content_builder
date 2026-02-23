<?php

namespace FriendsOfREDAXO\YFormContentBuilder\Fields;

use rex_extension;
use rex_extension_point;

/**
 * Registry für Content Builder Feldtypen
 * 
 * Ermöglicht:
 * - Registrierung von Feldtypen
 * - Überschreiben von Feldtypen durch andere Addons
 * - Abrufen von Feldtypen nach Name
 * 
 * Verwendung:
 * ```php
 * // Feldtyp registrieren
 * ContentBuilderFieldRegistry::register(new TextField());
 * 
 * // Feldtyp abrufen und rendern
 * $field = ContentBuilderFieldRegistry::get('text');
 * $field->render('title', $config, $value, $sliceData);
 * ```
 */
class ContentBuilderFieldRegistry
{
    /**
     * @var array<string, ContentBuilderFieldInterface>
     */
    private static array $fields = [];

    /**
     * @var bool Wurden Standard-Felder bereits geladen?
     */
    private static bool $initialized = false;

    /**
     * Registriert einen Feldtyp
     * Überschreibt existierende Feldtypen mit gleichem Namen
     */
    public static function register(ContentBuilderFieldInterface $field): void
    {
        self::$fields[$field::getType()] = $field;
        
        // Registry-Referenz setzen für verschachtelte Felder
        if ($field instanceof ContentBuilderFieldAbstract) {
            $field->setRegistry(new self());
        }
    }

    /**
     * Gibt einen Feldtyp zurück
     * 
     * @param string $type Feldtyp-Name
     * @return ContentBuilderFieldInterface|null
     */
    public static function get(string $type): ?ContentBuilderFieldInterface
    {
        self::ensureInitialized();
        return self::$fields[$type] ?? null;
    }

    /**
     * Prüft ob ein Feldtyp registriert ist
     */
    public static function has(string $type): bool
    {
        self::ensureInitialized();
        return isset(self::$fields[$type]);
    }

    /**
     * Gibt alle registrierten Feldtypen zurück
     * 
     * @return array<string, ContentBuilderFieldInterface>
     */
    public static function getAll(): array
    {
        self::ensureInitialized();
        return self::$fields;
    }

    /**
     * Rendert ein Feld basierend auf Typ
     */
    public static function renderField(string $fieldName, array $fieldConfig, array $sliceData): void
    {
        $type = $fieldConfig['type'] ?? 'text';
        $field = self::get($type);

        if ($field === null) {
            // Fallback auf Text-Feld
            $field = self::get('text');
            if ($field === null) {
                echo '<div class="alert alert-danger">Unbekannter Feldtyp: ' . \rex_escape($type) . '</div>';
                return;
            }
        }

        // Wert aus sliceData extrahieren
        $value = self::getNestedValue($fieldName, $sliceData);

        $field->render($fieldName, $fieldConfig, $value, $sliceData);
    }

    /**
     * Initialisiert die Standard-Feldtypen
     */
    private static function ensureInitialized(): void
    {
        if (self::$initialized) {
            return;
        }

        self::$initialized = true;

        // Standard-Feldtypen registrieren
        self::registerDefaultFields();

        // Extension Point für zusätzliche Feldtypen
        rex_extension::registerPoint(new rex_extension_point(
            'YFORM_CONTENT_BUILDER_FIELDS',
            self::$fields
        ));
    }

    /**
     * Registriert alle Standard-Feldtypen
     */
    private static function registerDefaultFields(): void
    {
        $defaultFields = [
            new TextField(),
            new TextareaField(),
            new CheckboxField(),
            new SelectField(),
            new ChoiceField(),
            new Cke5Field(),
            new TinyMceField(),
            new BeMediaField(),
            new BeLinkField(),
            new RadioImageField(),
            new ColorSwatchesField(),
            new BeTableSelectField(),
            new YFormPickerField(),
            new RepeaterField(),
        ];

        foreach ($defaultFields as $field) {
            self::register($field);
        }
    }

    /**
     * Holt Wert aus verschachteltem Array
     */
    private static function getNestedValue(string $key, array $data)
    {
        if (strpos($key, '[') === false) {
            return $data[$key] ?? '';
        }

        preg_match_all('/([^\[\]]+)/', $key, $matches);
        $keys = $matches[1];

        $value = $data;
        foreach ($keys as $k) {
            if (is_array($value) && isset($value[$k])) {
                $value = $value[$k];
            } else {
                return '';
            }
        }

        return $value;
    }

    /**
     * Setzt die Registry zurück (für Tests)
     */
    public static function reset(): void
    {
        self::$fields = [];
        self::$initialized = false;
    }
}
