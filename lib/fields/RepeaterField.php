<?php

namespace FriendsOfREDAXO\YFormContentBuilder\Fields;

use rex_escape;

/**
 * Repeater-Feld für wiederholbare Feldgruppen
 */
class RepeaterField extends ContentBuilderFieldAbstract
{
    public static function getType(): string
    {
        return 'repeater';
    }

    public function render(string $fieldName, array $fieldConfig, $value, array $sliceData = []): void
    {
        $label = $fieldConfig['label'] ?? $fieldName;
        $notice = $fieldConfig['notice'] ?? null;

        // Wert aus sliceData extrahieren - nur der erste Teil des Feldnamens
        $baseFieldName = preg_replace('/\[.*$/', '', $fieldName);
        $items = $sliceData[$baseFieldName] ?? [];
        if (!is_array($items)) {
            $items = [];
        }

        // Item Modal Config prüfen
        $hasItemModal = isset($fieldConfig['item_modal']) && is_array($fieldConfig['item_modal']);
        $itemModalFields = $hasItemModal ? $fieldConfig['item_modal']['fields'] : [];

        // Alle Modals mit trigger_after sammeln
        $triggerModals = [];
        foreach ($fieldConfig as $configKey => $configValue) {
            if (str_ends_with($configKey, '_modal') && $configKey !== 'item_modal' && is_array($configValue)) {
                if (isset($configValue['trigger_after']) && isset($configValue['fields'])) {
                    $triggerModals[$configValue['trigger_after']] = $configKey;
                    $itemModalFields = array_merge($itemModalFields, $configValue['fields']);
                }
            }
        }

        // View Config
        $view = $fieldConfig['view'] ?? 'list';
        $gridColumns = intval($fieldConfig['grid_columns'] ?? 3);

        // Dynamische Umschaltung
        if (isset($sliceData['view_mode'])) {
            $view = $sliceData['view_mode'];
        }

        $containerClass = 'repeater-container';
        if ($view === 'grid') {
            $containerClass .= ' repeater-grid-view';
        }

        $this->openFormGroup();
        $this->renderLabel($label);

        echo '<div class="repeater-wrapper">';
        echo '<div class="' . $containerClass . '" ';
        echo 'data-field="' . htmlspecialchars($fieldName, ENT_QUOTES, 'UTF-8') . '" ';
        echo 'data-view="' . htmlspecialchars($view, ENT_QUOTES, 'UTF-8') . '" ';
        echo 'data-grid-columns="' . htmlspecialchars($gridColumns, ENT_QUOTES, 'UTF-8') . '">';

        // Template-Item erstellen wenn keine Items vorhanden sind
        if (empty($items) && $hasItemModal) {
            $this->renderTemplateItem($fieldName, $fieldConfig, $baseFieldName, $itemModalFields, $triggerModals, $hasItemModal);
        }

        // Vorhandene Items rendern
        foreach ($items as $index => $item) {
            $this->renderItem($fieldName, $fieldConfig, $baseFieldName, $index, $item, $itemModalFields, $triggerModals, $hasItemModal);
        }

        echo '</div>'; // .repeater-container
        
        $addLabel = $fieldConfig['add_label'] ?? 'Hinzufügen';
        echo '<button type="button" class="btn btn-sm btn-primary btn-add-repeater">';
        echo '<i class="fa fa-plus"></i> ' . rex_escape($addLabel);
        echo '</button>';
        
        echo '</div>'; // .repeater-wrapper

        $this->closeFormGroup($notice);
    }

    /**
     * Rendert ein Template-Item (für JS-Klonen)
     */
    protected function renderTemplateItem(string $fieldName, array $fieldConfig, string $baseFieldName, array $itemModalFields, array $triggerModals, bool $hasItemModal): void
    {
        $templateId = 'repeater_item_template_' . uniqid();
        
        echo '<div class="repeater-item repeater-item-template" data-index="0" id="' . $templateId . '" style="display:none;">';
        
        $this->renderMoveButtons();

        $templateFieldCount = 0;
        foreach ($fieldConfig['fields'] as $subFieldName => $subFieldConfig) {
            if (in_array($subFieldName, $itemModalFields)) {
                continue;
            }

            $fullFieldName = $fieldName . '[0][' . $subFieldName . ']';
            $subData = [$baseFieldName => [0 => []]];
            
            ContentBuilderFieldRegistry::renderField($fullFieldName, $subFieldConfig, $subData);
            $templateFieldCount++;

            // Trigger-Modal nach diesem Feld
            if (isset($triggerModals[$subFieldName])) {
                $modalKey = $triggerModals[$subFieldName];
                $this->renderFieldModal($templateId, 0, $fieldName, $fieldConfig, [], $baseFieldName, $modalKey);
            }

            // Item-Modal Button nach 2. Feld
            if ($hasItemModal && $templateFieldCount === 2) {
                $this->renderItemModal($templateId, 0, $fieldName, $fieldConfig, [], $baseFieldName);
            }
        }

        echo '<button type="button" class="btn btn-sm btn-danger btn-remove-repeater"><i class="fa fa-trash"></i></button>';
        echo '</div>';
    }

    /**
     * Rendert ein einzelnes Repeater-Item
     */
    protected function renderItem(string $fieldName, array $fieldConfig, string $baseFieldName, int $index, array $item, array $itemModalFields, array $triggerModals, bool $hasItemModal): void
    {
        $itemId = 'repeater_item_' . uniqid();
        
        echo '<div class="repeater-item" data-index="' . $index . '" id="' . $itemId . '">';
        
        $this->renderMoveButtons();

        $fieldsRendered = 0;
        foreach ($fieldConfig['fields'] as $subFieldName => $subFieldConfig) {
            if (in_array($subFieldName, $itemModalFields)) {
                continue;
            }

            $fullFieldName = $fieldName . '[' . $index . '][' . $subFieldName . ']';
            $subData = [$baseFieldName => [$index => $item]];
            
            ContentBuilderFieldRegistry::renderField($fullFieldName, $subFieldConfig, $subData);
            $fieldsRendered++;

            // Trigger-Modal nach diesem Feld
            if (isset($triggerModals[$subFieldName])) {
                $modalKey = $triggerModals[$subFieldName];
                $this->renderFieldModal($itemId, $index, $fieldName, $fieldConfig, $item, $baseFieldName, $modalKey);
            }

            // Item-Modal Button nach 2. Feld
            if ($hasItemModal && $fieldsRendered === 2) {
                $this->renderItemModal($itemId, $index, $fieldName, $fieldConfig, $item, $baseFieldName);
            }
        }

        echo '<button type="button" class="btn btn-sm btn-danger btn-remove-repeater"><i class="fa fa-trash"></i></button>';
        echo '</div>';
    }

    /**
     * Rendert die Move-Buttons
     */
    protected function renderMoveButtons(): void
    {
        echo '<div class="move-buttons">';
        echo '<button type="button" class="btn-move btn-move-up" title="Nach oben"><i class="fa fa-chevron-up"></i></button>';
        echo '<button type="button" class="btn-move btn-move-down" title="Nach unten"><i class="fa fa-chevron-down"></i></button>';
        echo '</div>';
    }

    /**
     * Rendert ein Item-Modal
     */
    protected function renderItemModal(string $itemId, int $index, string $fieldName, array $fieldConfig, array $item, string $baseFieldName): void
    {
        $modalId = $itemId . '_modal';
        $modalConfig = $fieldConfig['item_modal'];
        $label = $modalConfig['label'] ?? 'Erweiterte Optionen';
        $icon = $modalConfig['icon'] ?? 'fa-cog';

        echo '<div class="form-group">';
        echo '<button type="button" class="btn btn-default btn-sm btn-block" data-toggle="modal" data-target="#' . $modalId . '">';
        echo '<i class="fa ' . rex_escape($icon) . '"></i> ' . rex_escape($label);
        echo '</button>';
        echo '</div>';

        echo '<div class="modal fade" id="' . $modalId . '" tabindex="-1" role="dialog">';
        echo '<div class="modal-dialog" role="document">';
        echo '<div class="modal-content">';
        echo '<div class="modal-header">';
        echo '<button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>';
        echo '<h4 class="modal-title"><i class="fa ' . rex_escape($icon) . '"></i> ' . rex_escape($label) . '</h4>';
        echo '</div>';
        echo '<div class="modal-body">';

        if (isset($modalConfig['fields']) && is_array($modalConfig['fields'])) {
            foreach ($modalConfig['fields'] as $subFieldName) {
                if (isset($fieldConfig['fields'][$subFieldName])) {
                    $fullFieldName = $fieldName . '[' . $index . '][' . $subFieldName . ']';
                    $subData = [$baseFieldName => [$index => $item]];
                    ContentBuilderFieldRegistry::renderField($fullFieldName, $fieldConfig['fields'][$subFieldName], $subData);
                }
            }
        }

        echo '</div>';
        echo '<div class="modal-footer">';
        echo '<button type="button" class="btn btn-primary" data-dismiss="modal">Übernehmen</button>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }

    /**
     * Rendert ein Feld-Modal (z.B. media_modal)
     */
    protected function renderFieldModal(string $itemId, int $index, string $fieldName, array $fieldConfig, array $item, string $baseFieldName, string $modalKey): void
    {
        $modalConfig = $fieldConfig[$modalKey];
        $modalId = $itemId . '_' . $modalKey;
        $label = $modalConfig['label'] ?? 'Optionen';
        $icon = $modalConfig['icon'] ?? 'fa-cog';

        echo '<div class="form-group">';
        echo '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#' . $modalId . '">';
        echo '<i class="fa ' . rex_escape($icon) . '"></i> ' . rex_escape($label);
        echo '</button>';
        echo '</div>';

        echo '<div class="modal fade" id="' . $modalId . '" tabindex="-1" role="dialog">';
        echo '<div class="modal-dialog" role="document">';
        echo '<div class="modal-content">';
        echo '<div class="modal-header">';
        echo '<button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>';
        echo '<h4 class="modal-title"><i class="fa ' . rex_escape($icon) . '"></i> ' . rex_escape($label) . '</h4>';
        echo '</div>';
        echo '<div class="modal-body">';

        if (isset($modalConfig['fields']) && is_array($modalConfig['fields'])) {
            foreach ($modalConfig['fields'] as $subFieldName) {
                if (isset($fieldConfig['fields'][$subFieldName])) {
                    $fullFieldName = $fieldName . '[' . $index . '][' . $subFieldName . ']';
                    $subData = [$baseFieldName => [$index => $item]];
                    ContentBuilderFieldRegistry::renderField($fullFieldName, $fieldConfig['fields'][$subFieldName], $subData);
                }
            }
        }

        echo '</div>';
        echo '<div class="modal-footer">';
        echo '<button type="button" class="btn btn-primary" data-dismiss="modal">Übernehmen</button>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
}
