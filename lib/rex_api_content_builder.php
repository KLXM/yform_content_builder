<?php

use KLXM\YFormContentBuilder\Fields\ContentBuilderFieldRegistry;
use rex;
use rex_addon;
use rex_api_function;
use rex_api_result;
use rex_media_category;
use rex_request;
use rex_response;

/**
 * API Handler für YForm Content Builder
 * 
 * Stellt Endpunkte für AJAX-Requests bereit:
 * - load_slice_form: Lädt das Formular für ein Element
 * - render_slice: Rendert ein Element mit Template
 * - load_media_categories: Lädt Medienpool-Kategorien
 * - load_media_list: Lädt Medienliste
 * - get_media_preview: Erzeugt Media-Preview HTML
 * 
 * Aufruf: /redaxo/index.php?rex-api-call=content_builder&action=<action>
 */
class rex_api_content_builder extends rex_api_function
{
    protected $published = true;

    public function execute()
    {
        // Nur im Backend und für eingeloggte Benutzer
        if (!rex::isBackend() || !rex::getUser()) {
            return new rex_api_result(false, 'Zugriff verweigert');
        }

        rex_response::cleanOutputBuffers();

        $action = rex_request::request('action', 'string', '');

        switch ($action) {
            case 'load_slice_form':
                $this->loadSliceForm();
                break;

            case 'render_slice':
                $this->renderSlice();
                break;

            case 'load_media_categories':
                $this->loadMediaCategories();
                break;

            case 'load_media_list':
                $this->loadMediaList();
                break;

            case 'get_media_preview':
                $this->getMediaPreview();
                break;

            default:
                rex_response::sendJson(['error' => 'Unbekannte Aktion: ' . $action]);
                exit;
        }

        exit;
    }

    /**
     * Lädt das Formular für ein Element
     */
    protected function loadSliceForm(): void
    {
        $sliceType = rex_request::post('slice_type', 'string');
        $sliceData = rex_request::post('slice_data', 'array', []);

        $elementPath = $this->getElementPath($sliceType);
        $configFile = $elementPath . '/config.php';

        if (!file_exists($configFile)) {
            echo '<div class="alert alert-danger">Element-Konfiguration nicht gefunden: ' . rex_escape($sliceType) . '</div>';
            return;
        }

        $config = include $configFile;

        if (!isset($config['fields']) || !is_array($config['fields'])) {
            echo '<div class="alert alert-danger">Element-Konfiguration fehlerhaft: ' . rex_escape($sliceType) . '</div>';
            return;
        }

        // YForm-Formular generieren
        echo '<form class="slice-form">';

        $hasSettingsModal = isset($config['settings_modal']) && is_array($config['settings_modal']);
        $helpModalConfig = yform_content_builder_help_modal_helper::buildConfigForElementDir($elementPath . '/');

        if ($hasSettingsModal || $helpModalConfig !== null) {
            echo '<div class="clearfix" style="margin-bottom: 15px; display: flex; justify-content: flex-end; gap: 6px;">';

            if ($hasSettingsModal) {
                $this->renderSettingsModalButton($config, $sliceData, true);
            }

            if ($helpModalConfig !== null) {
                $helpModalConfig['_modal_id'] = yform_content_builder_help_modal_helper::createModalId();
                yform_content_builder_help_modal_helper::renderButton($helpModalConfig, true);
            }

            echo '</div>';

            if ($helpModalConfig !== null) {
                yform_content_builder_help_modal_helper::renderModal($helpModalConfig);
            }
        }

        // Prüfen ob Tabs definiert sind
        if (isset($config['field_groups']) && is_array($config['field_groups'])) {
            $this->renderFormWithTabs($config, $sliceData);
        } else {
            // Standard: Alle Felder ohne Tabs (außer die im Modal)
            $modalFields = [];
            if (isset($config['settings_modal']['fields'])) {
                $modalFields = $config['settings_modal']['fields'];
            }

            ContentBuilderFieldRegistry::renderFieldRowsGroup(
                $config['fields'],
                $modalFields,
                function (string $fieldName, array $fieldConfig) use ($sliceData): void {
                    ContentBuilderFieldRegistry::renderField($fieldName, $fieldConfig, $sliceData);
                }
            );
        }

        echo '<div class="form-group">';
        echo '<div class="btn-group pull-right">';
        echo '<button type="button" class="btn btn-success btn-slice-save"><i class="fa fa-check"></i> Übernehmen</button>';
        echo '<button type="button" class="btn btn-danger btn-slice-cancel" data-confirm="Bearbeitung abbrechen? Alle Änderungen gehen verloren."><i class="fa fa-times"></i> Abbrechen</button>';
        echo '</div>';
        echo '<div class="clearfix"></div>';
        echo '</div>';
        echo '</form>';
    }

    /**
     * Rendert ein Element mit Template
     */
    protected function renderSlice(): void
    {
        $sliceType = rex_request::post('slice_type', 'string');
        $sliceData = rex_request::post('slice_data', 'array', []);
        $framework = rex_request::post('framework', 'string', 'bootstrap');

        $elementPath = $this->getElementPath($sliceType);

        $templateFile = $elementPath . '/templates/' . $framework . '.php';
        if (!file_exists($templateFile)) {
            $templateFile = $elementPath . '/templates/plain.php';
        }

        if (file_exists($templateFile)) {
            $elementData = $sliceData;
            include $templateFile;
        } else {
            echo '<div class="alert alert-danger">Template nicht gefunden</div>';
        }
    }

    /**
     * Ermittelt den Pfad zu einem Element (mit Override-Support)
     */
    protected function getElementPath(string $elementType): string
    {
        // Zuerst in project/elements suchen (Override)
        $projectPath = rex_addon::get('project')->getPath('elements/' . $elementType);
        if (is_dir($projectPath)) {
            return $projectPath;
        }

        // Dann in data/addons/yform_content_builder/elements (User-Override)
        $dataPath = rex_addon::get('yform_content_builder')->getDataPath('elements/' . $elementType);
        if (is_dir($dataPath)) {
            return $dataPath;
        }

        // Standard: Addon-Pfad
        return rex_addon::get('yform_content_builder')->getPath('elements/' . $elementType);
    }

    /**
     * Rendert Tabs für das Formular
     */
    protected function renderFormWithTabs(array $config, array $sliceData): void
    {
        $tabId = 'tab_' . uniqid();

        echo '<ul class="nav nav-tabs" role="tablist">';
        $firstTab = true;
        foreach ($config['field_groups'] as $groupKey => $group) {
            $active = $firstTab ? ' class="active"' : '';
            echo '<li role="presentation"' . $active . '>';
            echo '<a href="#' . $tabId . '_' . $groupKey . '" role="tab" data-toggle="tab">';

            if (!empty($group['icon'])) {
                echo '<i class="fa ' . rex_escape($group['icon']) . '"></i> ';
            }

            echo rex_escape($group['label'] ?? $groupKey);
            echo '</a>';
            echo '</li>';
            $firstTab = false;
        }
        echo '</ul>';

        echo '<div class="tab-content" style="padding-top: 20px;">';
        $firstTab = true;
        foreach ($config['field_groups'] as $groupKey => $group) {
            $active = $firstTab ? ' active' : '';
            echo '<div role="tabpanel" class="tab-pane' . $active . '" id="' . $tabId . '_' . $groupKey . '">';

            if (isset($group['fields']) && is_array($group['fields'])) {
                $groupFieldMap = [];
                foreach ($group['fields'] as $fieldName) {
                    if (isset($config['fields'][$fieldName])) {
                        $groupFieldMap[$fieldName] = $config['fields'][$fieldName];
                    }
                }
                ContentBuilderFieldRegistry::renderFieldRowsGroup(
                    $groupFieldMap,
                    [],
                    function (string $fieldName, array $fieldConfig) use ($sliceData): void {
                        ContentBuilderFieldRegistry::renderField($fieldName, $fieldConfig, $sliceData);
                    }
                );
            }

            echo '</div>';
            $firstTab = false;
        }
        echo '</div>';
    }

    /**
     * Rendert den Settings-Modal Button
     */
    protected function renderSettingsModalButton(array $config, array $sliceData, bool $toolbarButton = false): void
    {
        $modalId = 'settings_modal_' . uniqid();
        $modalConfig = $config['settings_modal'];
        $label = $modalConfig['label'] ?? 'Einstellungen';
        $icon = $modalConfig['icon'] ?? 'fa-cog';

        if ($toolbarButton) {
            echo '<button type="button" class="btn btn-default" data-toggle="modal" data-target="#' . $modalId . '">';
        } else {
            echo '<div class="form-group">';
            echo '<button type="button" class="btn btn-default btn-block" data-toggle="modal" data-target="#' . $modalId . '">';
        }

        echo '<i class="fa ' . rex_escape($icon) . '"></i> ' . rex_escape($label);
        echo '</button>';

        if (!$toolbarButton) {
            echo '</div>';
        }

        // Modal HTML
        echo '<div class="modal fade" id="' . $modalId . '" tabindex="-1" role="dialog">';
        echo '<div class="modal-dialog modal-lg" role="document">';
        echo '<div class="modal-content">';
        echo '<div class="modal-header">';
        echo '<button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>';
        echo '<h4 class="modal-title"><i class="fa ' . rex_escape($icon) . '"></i> ' . rex_escape($label) . '</h4>';
        echo '</div>';
        echo '<div class="modal-body">';

        if (isset($modalConfig['fields']) && is_array($modalConfig['fields'])) {
            $modalFieldMap = [];
            foreach ($modalConfig['fields'] as $fieldName) {
                if (isset($config['fields'][$fieldName])) {
                    $modalFieldMap[$fieldName] = $config['fields'][$fieldName];
                }
            }
            ContentBuilderFieldRegistry::renderFieldRowsGroup(
                $modalFieldMap,
                [],
                function (string $fieldName, array $fieldConfig) use ($sliceData): void {
                    ContentBuilderFieldRegistry::renderField($fieldName, $fieldConfig, $sliceData);
                }
            );
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
     * Lädt Medienpool-Kategorien
     */
    protected function loadMediaCategories(): void
    {
        $categories = [];
        $categoryList = rex_media_category::getRootCategories();

        foreach ($categoryList as $category) {
            $categories[] = [
                'id' => $category->getId(),
                'name' => $category->getName(),
            ];
            $this->addSubcategories($category, $categories);
        }

        rex_response::sendJson(['success' => true, 'categories' => $categories]);
    }

    /**
     * Fügt Unterkategorien rekursiv hinzu
     */
    protected function addSubcategories(rex_media_category $parent, array &$categories, int $level = 1): void
    {
        foreach ($parent->getChildren() as $child) {
            $categories[] = [
                'id' => $child->getId(),
                'name' => str_repeat('— ', $level) . $child->getName(),
            ];
            $this->addSubcategories($child, $categories, $level + 1);
        }
    }

    /**
     * Lädt Medienliste für Kategorie
     */
    protected function loadMediaList(): void
    {
        $categoryId = rex_request::post('category_id', 'int', 0);
        $type = rex_request::post('type', 'string', 'image');
        $media = [];

        $sql = rex_sql::factory();
        $where = $categoryId > 0 ? 'category_id = ' . $categoryId : 'category_id = 0 OR category_id IS NULL';

        // Nach Typ filtern
        $typeCondition = '';
        if ($type === 'image') {
            $typeCondition = " AND (filetype LIKE 'image/%' OR filename REGEXP '\\.(jpg|jpeg|png|gif|svg|webp)$')";
        } elseif ($type === 'video') {
            $typeCondition = " AND (filetype LIKE 'video/%' OR filename REGEXP '\\.(mp4|webm|mov|avi)$')";
        }

        $sql->setQuery('SELECT filename, title FROM ' . rex::getTable('media') . ' WHERE (' . $where . ')' . $typeCondition . ' ORDER BY filename');

        foreach ($sql as $row) {
            $media[] = [
                'filename' => $row->getValue('filename'),
                'title' => $row->getValue('title'),
            ];
        }

        rex_response::sendJson(['success' => true, 'media' => $media]);
    }

    /**
     * Erzeugt Media-Preview HTML
     */
    protected function getMediaPreview(): void
    {
        $filename = rex_request::post('media_file', 'string', '');
        $types = rex_request::post('types', 'string', 'image');

        if (empty($filename)) {
            rex_response::sendJson(['success' => false]);
            return;
        }

        $isImage = yform_content_builder_helper::isImage($filename);
        $isVideo = yform_content_builder_helper::isVideo($filename);

        $html = '';
        if ($isImage) {
            $html = '<img src="' . rex_url::media($filename) . '" style="max-height:100px; max-width:100%;">';
        } elseif ($isVideo) {
            $html = '<video src="' . rex_url::media($filename) . '" style="max-height:100px; max-width:100%;" controls muted></video>';
        } else {
            $html = '<span class="label label-default">' . rex_escape($filename) . '</span>';
        }

        rex_response::sendJson(['success' => true, 'html' => $html]);
    }
}
