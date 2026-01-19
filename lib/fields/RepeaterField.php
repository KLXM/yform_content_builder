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
        // Berechtigungsprüfung: Feld nicht rendern wenn Berechtigung fehlt
        if (!$this->hasPermission($fieldConfig)) {
            return;
        }

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

        // Template-Item IMMER erstellen (für JS-Klonen beim Hinzufügen)
        if (empty($items)) {
            // Bei leerem Repeater ein erstes echtes Item rendern, damit Daten beim ersten Speichern vorhanden sind
            $items = [[]]; // Ein leeres Item hinzufügen
        }

        // Vorhandene Items rendern
        foreach ($items as $index => $item) {
            $this->renderItem($fieldName, $fieldConfig, $baseFieldName, $index, $item, $itemModalFields, $triggerModals, $hasItemModal);
        }
        
        // Template-Item für JS-Klonen (versteckt)
        $this->renderTemplateItem($fieldName, $fieldConfig, $baseFieldName, $itemModalFields, $triggerModals, $hasItemModal);

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
        
        // Arrays zum Sammeln der Modals (werden am Ende gerendert)
        $modalsToRender = [];
        
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

            // Item-Modal Button nach 2. Feld - nur Button rendern, Modal später
            if ($hasItemModal && $templateFieldCount === 2) {
                $this->renderItemModalButton($templateId, $fieldConfig);
                $modalsToRender['item_modal'] = ['itemId' => $templateId, 'index' => 0, 'item' => []];
            }

            // Trigger-Modal Button nach diesem Feld - nur Button rendern, Modal später
            if (isset($triggerModals[$subFieldName])) {
                $modalKey = $triggerModals[$subFieldName];
                $this->renderFieldModalButton($templateId, $fieldConfig, $modalKey);
                $modalsToRender[$modalKey] = ['itemId' => $templateId, 'index' => 0, 'item' => [], 'modalKey' => $modalKey];
            }
        }

        echo '<button type="button" class="btn btn-sm btn-danger btn-remove-repeater"><i class="fa fa-trash"></i></button>';
        
        // JETZT alle Modals am Ende rendern (Bootstrap findet sie dann korrekt per data-target)
        foreach ($modalsToRender as $type => $data) {
            if ($type === 'item_modal') {
                $this->renderItemModalDialog($data['itemId'], $data['index'], $fieldName, $fieldConfig, $data['item'], $baseFieldName);
            } else {
                $this->renderFieldModalDialog($data['itemId'], $data['index'], $fieldName, $fieldConfig, $data['item'], $baseFieldName, $data['modalKey']);
            }
        }
        
        echo '</div>';
    }

    /**
     * Rendert ein einzelnes Repeater-Item
     */
    protected function renderItem(string $fieldName, array $fieldConfig, string $baseFieldName, int $index, array $item, array $itemModalFields, array $triggerModals, bool $hasItemModal): void
    {
        $itemId = 'repeater_item_' . uniqid();
        
        // Arrays zum Sammeln der Modals (werden am Ende gerendert)
        $modalsToRender = [];
        
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

            // Item-Modal Button nach 2. Feld - nur Button rendern, Modal später
            if ($hasItemModal && $fieldsRendered === 2) {
                $this->renderItemModalButton($itemId, $fieldConfig);
                $modalsToRender['item_modal'] = ['itemId' => $itemId, 'index' => $index, 'item' => $item];
            }

            // Trigger-Modal Button nach diesem Feld - nur Button rendern, Modal später
            if (isset($triggerModals[$subFieldName])) {
                $modalKey = $triggerModals[$subFieldName];
                $this->renderFieldModalButton($itemId, $fieldConfig, $modalKey);
                $modalsToRender[$modalKey] = ['itemId' => $itemId, 'index' => $index, 'item' => $item, 'modalKey' => $modalKey];
            }
        }

        echo '<button type="button" class="btn btn-sm btn-danger btn-remove-repeater"><i class="fa fa-trash"></i></button>';
        
        // JETZT alle Modals am Ende rendern (Bootstrap findet sie dann korrekt per data-target)
        foreach ($modalsToRender as $type => $data) {
            if ($type === 'item_modal') {
                $this->renderItemModalDialog($data['itemId'], $data['index'], $fieldName, $fieldConfig, $data['item'], $baseFieldName);
            } else {
                $this->renderFieldModalDialog($data['itemId'], $data['index'], $fieldName, $fieldConfig, $data['item'], $baseFieldName, $data['modalKey']);
            }
        }
        
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
     * Rendert nur den Item-Modal Button (Modal-Dialog wird später gerendert)
     */
    protected function renderItemModalButton(string $itemId, array $fieldConfig): void
    {
        $modalId = $itemId . '_item_modal';
        $modalConfig = $fieldConfig['item_modal'];
        $label = $modalConfig['label'] ?? 'Erweiterte Optionen';
        $icon = $modalConfig['icon'] ?? 'fa-ellipsis-h';

        echo '<div class="form-group">';
        echo '<button type="button" class="btn btn-default btn-sm btn-block" data-toggle="modal" data-target="#' . $modalId . '">';
        echo '<i class="fa ' . rex_escape($icon) . '"></i> ' . rex_escape($label);
        echo '</button>';
        echo '</div>';
    }

    /**
     * Rendert den Item-Modal Dialog (ohne Button)
     */
    protected function renderItemModalDialog(string $itemId, int $index, string $fieldName, array $fieldConfig, array $item, string $baseFieldName): void
    {
        $modalId = $itemId . '_item_modal';
        $modalConfig = $fieldConfig['item_modal'];
        $label = $modalConfig['label'] ?? 'Erweiterte Optionen';
        $icon = $modalConfig['icon'] ?? 'fa-ellipsis-h';

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
     * Rendert nur den Field-Modal Button (Modal-Dialog wird später gerendert)
     */
    protected function renderFieldModalButton(string $itemId, array $fieldConfig, string $modalKey): void
    {
        $modalConfig = $fieldConfig[$modalKey];
        $modalId = $itemId . '_' . $modalKey;
        $label = $modalConfig['label'] ?? 'Optionen';
        $icon = $modalConfig['icon'] ?? 'fa-sliders';

        echo '<div class="form-group">';
        echo '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#' . $modalId . '">';
        echo '<i class="fa ' . rex_escape($icon) . '"></i> ' . rex_escape($label);
        echo '</button>';
        echo '</div>';
    }

    /**
     * Rendert den Field-Modal Dialog (ohne Button)
     */
    protected function renderFieldModalDialog(string $itemId, int $index, string $fieldName, array $fieldConfig, array $item, string $baseFieldName, string $modalKey): void
    {
        $modalConfig = $fieldConfig[$modalKey];
        $modalId = $itemId . '_' . $modalKey;
        $label = $modalConfig['label'] ?? 'Optionen';
        $icon = $modalConfig['icon'] ?? 'fa-sliders';

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
     * @deprecated Alte Methode - wird nicht mehr verwendet (Button und Dialog sind nun getrennt)
     */
    protected function renderItemModal(string $itemId, int $index, string $fieldName, array $fieldConfig, array $item, string $baseFieldName): void
    {
        $modalId = $itemId . '_item_modal';
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
}
