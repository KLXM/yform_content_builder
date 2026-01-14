<?php

namespace FriendsOfREDAXO\YFormContentBuilder\Fields;

use rex_article;
use rex_clang;
use rex_escape;

/**
 * REDAXO Backend Link Widget
 */
class BeLinkField extends ContentBuilderFieldAbstract
{
    public static function getType(): string
    {
        return 'be_link';
    }

    public function render(string $fieldName, array $fieldConfig, $value, array $sliceData = []): void
    {
        $label = $fieldConfig['label'] ?? $fieldName;
        $notice = $fieldConfig['notice'] ?? null;
        $categoryId = $fieldConfig['category'] ?? 1;

        $linkCounter = self::getNextLinkCounter();
        $inputId = 'REX_LINK_' . $linkCounter;

        // Artikel-Name für Anzeige ermitteln
        $artName = '';
        if ($value) {
            $article = rex_article::get($value);
            if ($article) {
                $artName = $article->getName();
            }
        }

        $openParams = '&clang=' . rex_clang::getCurrentId() . '&category_id=' . $categoryId;

        $this->openFormGroup();
        $this->renderLabel($label);

        echo '<div class="input-group">';
        
        // Sichtbares Textfeld mit Artikel-Name
        echo '<input class="form-control" type="text" ';
        echo 'value="' . rex_escape($artName) . '" ';
        echo 'id="' . $inputId . '_NAME" ';
        echo 'readonly />';
        
        // Hidden Field mit Artikel-ID
        echo '<input type="hidden" ';
        echo 'name="' . rex_escape($fieldName) . '" ';
        echo 'id="' . $inputId . '" ';
        echo 'value="' . rex_escape($value) . '" />';
        
        echo '<span class="input-group-btn">';
        echo '<a href="#" class="btn btn-popup rex-linkmap-btn" ';
        echo 'data-id="' . $inputId . '" ';
        echo 'data-params="' . rex_escape($openParams) . '" ';
        echo 'title="Seite auswählen">';
        echo '<i class="rex-icon rex-icon-open-linkmap"></i>';
        echo '</a>';
        echo '<a href="#" class="btn btn-popup rex-linkmap-delete-btn" ';
        echo 'data-id="' . $inputId . '" ';
        echo 'data-counter="' . $linkCounter . '" ';
        echo 'title="Link entfernen">';
        echo '<i class="rex-icon rex-icon-delete-link"></i>';
        echo '</a>';
        echo '</span>';
        echo '</div>';

        $this->closeFormGroup($notice);
    }
}
