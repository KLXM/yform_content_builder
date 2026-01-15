<?php

namespace FriendsOfREDAXO\YFormContentBuilder\Fields;

use rex;
use rex_escape;

/**
 * Abstrakte Basisklasse für Content Builder Feldtypen
 * 
 * Stellt gemeinsame Funktionalität bereit:
 * - Label-Rendering
 * - Verschachtelte Werte extrahieren
 * - Notice/Hilfetext
 * - Berechtigungsprüfung
 */
abstract class ContentBuilderFieldAbstract implements ContentBuilderFieldInterface
{
    /**
     * Statische Widget-Counter für eindeutige IDs
     */
    protected static array $widgetCounters = [
        'media' => 0,
        'link' => 0,
    ];

    /**
     * Referenz zur Registry (für verschachtelte Felder wie Repeater)
     */
    protected ?ContentBuilderFieldRegistry $registry = null;

    public function setRegistry(ContentBuilderFieldRegistry $registry): void
    {
        $this->registry = $registry;
    }

    /**
     * Prüft ob der Benutzer die Berechtigung hat dieses Feld zu sehen
     * 
     * @param array $fieldConfig Feldkonfiguration mit optionalem 'perm' Key
     * @return bool True wenn Feld sichtbar sein soll, false sonst
     */
    protected function hasPermission(array $fieldConfig): bool
    {
        if (!isset($fieldConfig['perm'])) {
            // Keine Berechtigung definiert = für alle sichtbar
            return true;
        }

        // Berechtigungsprüfung
        if ($fieldConfig['perm'] === 'admin') {
            // Nur für Admins
            return rex::getUser()?->isAdmin() ?? false;
        }

        // Unbekannte Berechtigung = erlauben (Fallback)
        return true;
    }

    /**
     * Standard-Wertverarbeitung (keine Änderung)
     */
    public function processValue($value, array $fieldConfig)
    {
        return $value;
    }

    /**
     * Rendert das Label für ein Feld
     */
    protected function renderLabel(string $label): void
    {
        echo '<label>' . rex_escape($label) . '</label>';
    }

    /**
     * Öffnet eine Formulargruppe
     */
    protected function openFormGroup(): void
    {
        echo '<div class="form-group">';
    }

    /**
     * Schließt eine Formulargruppe und rendert optional Notice
     */
    protected function closeFormGroup(?string $notice = null): void
    {
        if ($notice) {
            echo '<p class="help-block">' . rex_escape($notice) . '</p>';
        }
        echo '</div>';
    }

    /**
     * Holt Wert aus verschachteltem Array (z.B. "items[0][title]")
     */
    protected function getNestedValue(string $key, array $data)
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
     * Generiert eine eindeutige ID
     */
    protected function generateId(string $prefix = 'field'): string
    {
        return $prefix . '_' . uniqid();
    }

    /**
     * Holt nächsten Media-Counter (global eindeutig)
     */
    protected static function getNextMediaCounter(): int
    {
        if (!isset($GLOBALS['yform_cb_media_counter'])) {
            $GLOBALS['yform_cb_media_counter'] = 0;
        }
        return ++$GLOBALS['yform_cb_media_counter'];
    }

    /**
     * Holt nächsten Link-Counter
     */
    protected static function getNextLinkCounter(): int
    {
        return ++self::$widgetCounters['link'];
    }
}
