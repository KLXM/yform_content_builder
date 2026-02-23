<?php

namespace FriendsOfREDAXO\YFormContentBuilder\Fields;

use rex_escape;

/**
 * TinyMCE WYSIWYG-Feld
 */
class TinyMceField extends ContentBuilderFieldAbstract
{
    public static function getType(): string
    {
        return 'tinymce';
    }

    public function render(string $fieldName, array $fieldConfig, $value, array $sliceData = []): void
    {
        // Berechtigungsprüfung: Feld nicht rendern wenn Berechtigung fehlt
        if (!$this->hasPermission($fieldConfig)) {
            return;
        }

        $label = $fieldConfig['label'] ?? $fieldName;
        $profile = $fieldConfig['profile'] ?? 'default';
        $rows = $fieldConfig['rows'] ?? 10;
        $notice = $fieldConfig['notice'] ?? null;

        $editorId = 'tinymce' . uniqid();

        $this->openFormGroup();
        $this->renderLabel($label);

        echo '<textarea id="' . $editorId . '" ';
        echo 'class="form-control tiny-editor" ';
        echo 'name="' . rex_escape($fieldName) . '" ';
        echo 'data-profile="' . rex_escape($profile) . '" ';
        echo 'rows="' . intval($rows) . '">';
        echo rex_escape($value);
        echo '</textarea>';

        $this->closeFormGroup($notice);
    }
}
