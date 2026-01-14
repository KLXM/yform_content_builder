<?php

namespace FriendsOfREDAXO\YFormContentBuilder\Fields;

use rex_escape;

/**
 * Select-Feld (einfaches Dropdown)
 */
class SelectField extends ContentBuilderFieldAbstract
{
    public static function getType(): string
    {
        return 'select';
    }

    public function render(string $fieldName, array $fieldConfig, $value, array $sliceData = []): void
    {
        $label = $fieldConfig['label'] ?? $fieldName;
        $options = $fieldConfig['options'] ?? [];
        $notice = $fieldConfig['notice'] ?? null;

        $this->openFormGroup();
        $this->renderLabel($label);

        echo '<select class="form-control" name="' . rex_escape($fieldName) . '">';
        
        foreach ($options as $optValue => $optLabel) {
            $selected = ($value == $optValue) ? ' selected' : '';
            echo '<option value="' . rex_escape($optValue) . '"' . $selected . '>';
            echo rex_escape($optLabel);
            echo '</option>';
        }
        
        echo '</select>';

        $this->closeFormGroup($notice);
    }
}
