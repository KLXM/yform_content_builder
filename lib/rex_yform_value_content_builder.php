<?php

/**
 * YForm Content Builder Field
 * Slice-based content management für YForm
 */
class rex_yform_value_content_builder extends rex_yform_value_abstract
{
    public function enterObject()
    {
        // AJAX-Anfragen behandeln
        $this->handleAjaxRequests();
        
        // Wert normalisieren
        if (!is_string($this->getValue())) {
            $this->setValue('');
        }

        // Default-Wert wenn leer
        if ('' == $this->getValue() && !$this->params['send']) {
            $this->setValue($this->getElement('default'));
        }

        // Werte für E-Mail und Datenbank setzen
        $this->params['value_pool']['email'][$this->getName()] = $this->getValue();
        
        if ($this->saveInDb()) {
            $this->params['value_pool']['sql'][$this->getName()] = $this->getValue();
        }

        // Template-Ausgabe
        if ($this->needsOutput() && $this->isViewable()) {
            $this->params['form_output'][$this->getId()] = $this->parse(
                'value.content_builder.tpl.php',
                $this->getTemplateVars()
            );
        }

        // POST-Daten verarbeiten
        if (isset($_POST['FORM']) && isset($_POST['FORM'][$this->params['form_name']]['send'])) {
            $this->processFormData();
        }
    }

    private function handleAjaxRequests(): bool
    {
        if (rex_request::isXmlHttpRequest()) {
            $action = rex_request::request('action', 'string', '');
            
            if ($action === 'load_slice_form') {
                $this->ajaxLoadSliceForm();
                return true;
            }
            
            if ($action === 'render_slice') {
                $this->ajaxRenderSlice();
                return true;
            }
            
            if ($action === 'load_media_categories') {
                $this->ajaxLoadMediaCategories();
                return true;
            }
            
            if ($action === 'load_media_list') {
                $this->ajaxLoadMediaList();
                return true;
            }
        }
        
        return false;
    }

    protected function ajaxLoadSliceForm()
    {
        rex_response::cleanOutputBuffers();
        
        $sliceType = rex_request::post('slice_type', 'string');
        $sliceData = rex_request::post('slice_data', 'array', []);
        
        // Element-Pfad via getElementPath (unterstützt Overrides)
        $elementPath = $this->getElementPath($sliceType);
        $configFile = $elementPath . '/config.php';
        
        if (!file_exists($configFile)) {
            echo '<div class="alert alert-danger">Element-Konfiguration nicht gefunden: ' . rex_escape($sliceType) . '</div>';
            exit;
        }
        
        $config = include $configFile;
        
        // Debug
        if (!isset($config['fields']) || !is_array($config['fields'])) {
            echo '<div class="alert alert-danger">Config hat keine fields: ' . rex_escape(print_r($config, true)) . '</div>';
            exit;
        }
        
        // YForm-Formular generieren
        echo '<form class="slice-form">';
        
        // Settings Modal Button (falls definiert)
        if (isset($config['settings_modal']) && is_array($config['settings_modal'])) {
            $this->renderSettingsModalButton($config, $sliceData);
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
            
            foreach ($config['fields'] as $fieldName => $fieldConfig) {
                // Felder die im Modal sind überspringen
                if (!in_array($fieldName, $modalFields)) {
                    $this->renderFormField($fieldName, $fieldConfig, $sliceData);
                }
            }
        }
        
        echo '<div class="form-group">';
        echo '<div class="btn-group">';
        echo '<button type="button" class="btn btn-success btn-slice-save"><i class="fa fa-check"></i> Speichern</button>';
        echo '<button type="button" class="btn btn-default btn-slice-cancel"><i class="fa fa-times"></i> Abbrechen</button>';
        echo '</div>';
        echo '</div>';
        echo '</form>';
        
        exit;
    }
    
    protected function renderFormWithTabs(array $config, array $sliceData)
    {
        $tabId = 'tab_' . uniqid();
        
        echo '<ul class="nav nav-tabs" role="tablist">';
        $firstTab = true;
        foreach ($config['field_groups'] as $groupKey => $group) {
            $active = $firstTab ? ' class="active"' : '';
            echo '<li role="presentation"' . $active . '>';
            echo '<a href="#' . $tabId . '_' . $groupKey . '" role="tab" data-toggle="tab">';
            
            // Icon anzeigen falls vorhanden
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
            
            // Felder dieser Gruppe rendern
            if (isset($group['fields']) && is_array($group['fields'])) {
                foreach ($group['fields'] as $fieldName) {
                    if (isset($config['fields'][$fieldName])) {
                        $this->renderFormField($fieldName, $config['fields'][$fieldName], $sliceData);
                    }
                }
            }
            
            echo '</div>';
            $firstTab = false;
        }
        echo '</div>';
    }
    
    protected function renderSettingsModalButton(array $config, array $sliceData)
    {
        $modalId = 'settings_modal_' . uniqid();
        $modalConfig = $config['settings_modal'];
        $label = $modalConfig['label'] ?? 'Einstellungen';
        $icon = $modalConfig['icon'] ?? 'fa-cog';
        
        echo '<div class="form-group">';
        echo '<button type="button" class="btn btn-default btn-block" data-toggle="modal" data-target="#' . $modalId . '">';
        echo '<i class="fa ' . rex_escape($icon) . '"></i> ' . rex_escape($label);
        echo '</button>';
        echo '</div>';
        
        // Modal HTML
        echo '<div class="modal fade" id="' . $modalId . '" tabindex="-1" role="dialog">';
        echo '<div class="modal-dialog modal-lg" role="document">';
        echo '<div class="modal-content">';
        echo '<div class="modal-header">';
        echo '<button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>';
        echo '<h4 class="modal-title"><i class="fa ' . rex_escape($icon) . '"></i> ' . rex_escape($label) . '</h4>';
        echo '</div>';
        echo '<div class="modal-body">';
        
        // Felder im Modal rendern
        if (isset($modalConfig['fields']) && is_array($modalConfig['fields'])) {
            foreach ($modalConfig['fields'] as $fieldName) {
                if (isset($config['fields'][$fieldName])) {
                    $this->renderFormField($fieldName, $config['fields'][$fieldName], $sliceData);
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
    
    protected function renderRepeaterItemModal(string $itemId, int $index, string $fieldName, array $fieldConfig, array $item, string $baseFieldName)
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
        
        // Modal HTML
        echo '<div class="modal fade" id="' . $modalId . '" tabindex="-1" role="dialog">';
        echo '<div class="modal-dialog" role="document">';
        echo '<div class="modal-content">';
        echo '<div class="modal-header">';
        echo '<button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>';
        echo '<h4 class="modal-title"><i class="fa ' . rex_escape($icon) . '"></i> ' . rex_escape($label) . '</h4>';
        echo '</div>';
        echo '<div class="modal-body">';
        
        // Modal-Felder rendern
        if (isset($modalConfig['fields']) && is_array($modalConfig['fields'])) {
            foreach ($modalConfig['fields'] as $subFieldName) {
                if (isset($fieldConfig['fields'][$subFieldName])) {
                    $subValue = $item[$subFieldName] ?? '';
                    $fullFieldName = $fieldName . '[' . $index . '][' . $subFieldName . ']';
                    
                    // Für Subfelder müssen wir die Daten anders übergeben
                    $subData = [$baseFieldName => [$index => $item]];
                    $this->renderFormField($fullFieldName, $fieldConfig['fields'][$subFieldName], $subData);
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

    protected function renderFormField(string $fieldName, array $fieldConfig, array $sliceData)
    {
        // Wert aus verschachtelten Arrays extrahieren (z.B. "items[0][title]")
        $value = $this->getNestedValue($fieldName, $sliceData);
        $label = $fieldConfig['label'] ?? $fieldName;
        $type = $fieldConfig['type'] ?? 'text';
        
        // Checkbox hat eigenes Label-Handling
        if ($type !== 'checkbox') {
            echo '<div class="form-group">';
            echo '<label>' . rex_escape($label) . '</label>';
        }
        
        switch ($type) {
            case 'text':
                echo '<input type="text" class="form-control" name="' . rex_escape($fieldName) . '" value="' . rex_escape($value) . '">';
                break;
                
            case 'checkbox':
                $checked = !empty($value) ? ' checked' : '';
                echo '<div class="form-group">';
                echo '<div class="checkbox">';
                echo '<label>';
                echo '<input type="checkbox" name="' . rex_escape($fieldName) . '" value="1"' . $checked . '> ';
                echo rex_escape($label);
                echo '</label>';
                echo '</div>';
                break;
                
            case 'textarea':
                echo '<textarea class="form-control" name="' . rex_escape($fieldName) . '" rows="5">' . rex_escape($value) . '</textarea>';
                break;
                
            case 'cke5':
                $editorId = 'ck' . uniqid();
                echo '<textarea id="' . $editorId . '" ';
                echo 'class="form-control cke5-editor" ';
                echo 'name="' . rex_escape($fieldName) . '" ';
                echo 'data-profile="default" ';
                echo 'rows="10">';
                echo rex_escape($value);
                echo '</textarea>';
                break;
                
            case 'be_media':
                // Custom Media Browser statt openREXMedia
                static $widgetCounter = 0;
                $widgetCounter++;
                
                $inputId = 'media_' . uniqid();
                
                echo '<div class="input-group media-widget">';
                echo '<input type="text" ';
                echo 'name="' . rex_escape($fieldName) . '" ';
                echo 'id="' . $inputId . '" ';
                echo 'class="form-control media-input" ';
                echo 'value="' . rex_escape($value) . '" ';
                echo 'readonly />';
                echo '<span class="input-group-btn">';
                echo '<button type="button" class="btn btn-default btn-select-media" ';
                echo 'data-input-id="' . $inputId . '" ';
                echo 'title="Medium auswählen">';
                echo '<i class="rex-icon rex-icon-open-mediapool"></i>';
                echo '</button>';
                echo '<button type="button" class="btn btn-default btn-delete-media" ';
                echo 'data-input-id="' . $inputId . '" ';
                echo 'title="Medium entfernen">';
                echo '<i class="rex-icon rex-icon-delete-media"></i>';
                echo '</button>';
                echo '</span>';
                echo '</div>';
                
                // Vorschau wenn Bild vorhanden
                if ($value && $this->isImage($value)) {
                    echo '<div class="media-preview" id="preview_' . $inputId . '">';
                    echo '<img src="' . rex_url::media($value) . '" alt="" />';
                    echo '</div>';
                } else {
                    echo '<div class="media-preview" id="preview_' . $inputId . '" style="display:none;"></div>';
                }
                break;
                
            case 'be_link':
                // REDAXO Linkmap Widget
                static $linkCounter = 0;
                $linkCounter++;
                
                $inputId = 'REX_LINK_' . $linkCounter;
                $categoryId = $fieldConfig['category'] ?? 1;
                
                // Artikel-Name für Anzeige ermitteln
                $artName = '';
                if ($value) {
                    $article = rex_article::get($value);
                    if ($article) {
                        $artName = $article->getName();
                    }
                }
                
                $openParams = '&clang=' . rex_clang::getCurrentId() . '&category_id=' . $categoryId;
                
                echo '<div class="input-group">';
                // Sichtbares Textfeld mit Artikel-Name
                echo '<input class="form-control" type="text" ';
                echo 'value="' . rex_escape($artName) . '" ';
                echo 'id="' . $inputId . '_NAME" ';
                echo 'readonly />';
                // Hidden Field mit Artikel-ID (wird vom Formular submitted)
                echo '<input type="hidden" ';
                echo 'name="' . rex_escape($fieldName) . '" ';
                echo 'id="' . $inputId . '" ';
                echo 'value="' . rex_escape($value) . '" />';
                echo '<span class="input-group-btn">';
                echo '<a href="#" class="btn btn-popup" ';
                echo 'onclick="openLinkMap(\'' . $inputId . '\', \'' . $openParams . '\'); return false;" ';
                echo 'title="Seite auswählen">';
                echo '<i class="rex-icon rex-icon-open-linkmap"></i>';
                echo '</a>';
                echo '<a href="#" class="btn btn-popup" ';
                echo 'onclick="deleteREXLink(' . $linkCounter . '); return false;" ';
                echo 'title="Link entfernen">';
                echo '<i class="rex-icon rex-icon-delete-link"></i>';
                echo '</a>';
                echo '</span>';
                echo '</div>';
                break;
                
            case 'choice':
                $choices = $fieldConfig['choices'] ?? [];
                $default = $fieldConfig['default'] ?? '';
                
                // Falls choices als String übergeben wurde (legacy)
                if (is_string($choices)) {
                    $parsed = [];
                    foreach (explode(',', $choices) as $choice) {
                        if (strpos($choice, '=') !== false) {
                            [$key, $val] = explode('=', $choice, 2);
                            $parsed[trim($key)] = trim($val);
                        } else {
                            $parsed[trim($choice)] = trim($choice);
                        }
                    }
                    $choices = $parsed;
                }
                
                // Default verwenden wenn kein Wert gesetzt
                if (empty($value) && !empty($default)) {
                    $value = $default;
                }
                
                echo '<select class="form-control" name="' . rex_escape($fieldName) . '">';
                foreach ($choices as $choiceValue => $choiceLabel) {
                    $selected = ($value == $choiceValue) ? ' selected' : '';
                    echo '<option value="' . rex_escape($choiceValue) . '"' . $selected . '>' . rex_escape($choiceLabel) . '</option>';
                }
                echo '</select>';
                break;
                
            case 'repeater':
                // Repeater-Felder
                // Wert aus sliceData extrahieren - nur der erste Teil des Feldnamens
                $baseFieldName = preg_replace('/\[.*$/', '', $fieldName);
                $items = $sliceData[$baseFieldName] ?? [];
                if (!is_array($items)) {
                    $items = [];
                }
                
                // Item Modal Config prüfen
                $hasItemModal = isset($fieldConfig['item_modal']) && is_array($fieldConfig['item_modal']);
                $itemModalFields = $hasItemModal ? $fieldConfig['item_modal']['fields'] : [];
                
                echo '<div class="repeater-wrapper">';
                echo '<div class="repeater-container" data-field="' . rex_escape($fieldName) . '">';
                
                // Template-Item erstellen wenn keine Items vorhanden sind
                if (empty($items) && $hasItemModal) {
                    $templateId = 'repeater_item_template_' . uniqid();
                    echo '<div class="repeater-item repeater-item-template" data-index="0" id="' . $templateId . '" style="display:none;">';
                    
                    // Template-Felder rendern
                    $templateItem = [];
                    foreach ($fieldConfig['fields'] as $subFieldName => $subFieldConfig) {
                        if ($hasItemModal && in_array($subFieldName, $itemModalFields)) {
                            continue;
                        }
                        
                        $fullFieldName = $fieldName . '[0][' . $subFieldName . ']';
                        $subData = [$baseFieldName => [0 => []]];
                        $this->renderFormField($fullFieldName, $subFieldConfig, $subData);
                        
                        // Modal Button nach 2. Feld
                        static $templateFieldCount = 0;
                        $templateFieldCount++;
                        if ($templateFieldCount === 2) {
                            $this->renderRepeaterItemModal($templateId, 0, $fieldName, $fieldConfig, [], $baseFieldName);
                        }
                    }
                    
                    echo '<button type="button" class="btn btn-sm btn-danger btn-remove-repeater"><i class="fa fa-trash"></i></button>';
                    echo '</div>';
                }
                
                foreach ($items as $index => $item) {
                    $itemId = 'repeater_item_' . uniqid();
                    echo '<div class="repeater-item" data-index="' . $index . '" id="' . $itemId . '">';
                    
                    // Haupt-Felder (nicht im Modal)
                    $fieldsRendered = 0;
                    foreach ($fieldConfig['fields'] as $subFieldName => $subFieldConfig) {
                        // Überspringen wenn Feld im Modal ist
                        if ($hasItemModal && in_array($subFieldName, $itemModalFields)) {
                            continue;
                        }
                        
                        $subValue = $item[$subFieldName] ?? '';
                        $fullFieldName = $fieldName . '[' . $index . '][' . $subFieldName . ']';
                        
                        // Für Subfelder müssen wir die Daten anders übergeben
                        $subData = [$baseFieldName => [$index => $item]];
                        $this->renderFormField($fullFieldName, $subFieldConfig, $subData);
                        
                        $fieldsRendered++;
                        
                        // Modal Button nach dem 2. Feld einfügen (nach image + image_position)
                        if ($hasItemModal && $fieldsRendered === 2) {
                            $this->renderRepeaterItemModal($itemId, $index, $fieldName, $fieldConfig, $item, $baseFieldName);
                        }
                    }
                    
                    echo '<button type="button" class="btn btn-sm btn-danger btn-remove-repeater"><i class="fa fa-trash"></i></button>';
                    echo '</div>';
                }
                
                echo '</div>'; // .repeater-container
                echo '<button type="button" class="btn btn-sm btn-primary btn-add-repeater"><i class="fa fa-plus"></i> ' . ($fieldConfig['add_label'] ?? 'Hinzufügen') . '</button>';
                echo '</div>'; // .repeater-wrapper
                break;
        }
        
        if (isset($fieldConfig['notice'])) {
            echo '<p class="help-block">' . rex_escape($fieldConfig['notice']) . '</p>';
        }
        
        echo '</div>';
    }

    protected function ajaxRenderSlice()
    {
        rex_response::cleanOutputBuffers();
        
        $sliceType = rex_request::post('slice_type', 'string');
        $sliceData = rex_request::post('slice_data', 'array', []);
        $framework = rex_request::post('framework', 'string', 'bootstrap');
        
        // Element-Pfad via getElementPath (unterstützt Overrides)
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
        
        exit;
    }
    
    /**
     * AJAX: Load media categories
     */
    protected function ajaxLoadMediaCategories(): void
    {
        rex_response::cleanOutputBuffers();
        
        $categories = [];
        $sql = rex_sql::factory();
        $sql->setQuery('SELECT id, name FROM ' . rex::getTable('media_category') . ' ORDER BY name');
        
        foreach ($sql->getArray() as $row) {
            $categories[] = [
                'id' => $row['id'],
                'name' => $row['name']
            ];
        }
        
        header('Content-Type: application/json');
        echo json_encode(['categories' => $categories]);
        exit;
    }
    
    /**
     * AJAX: Load media list
     */
    protected function ajaxLoadMediaList(): void
    {
        rex_response::cleanOutputBuffers();
        
        $categoryId = rex_request::request('category_id', 'int', 0);
        $search = rex_request::request('search', 'string', '');
        
        $sql = rex_sql::factory();
        $query = 'SELECT filename, title, category_id FROM ' . rex::getTable('media');
        $where = [];
        
        if ($categoryId > 0) {
            $where[] = 'category_id = ' . $categoryId;
        }
        
        if ($search) {
            $search = $sql->escape('%' . $search . '%');
            $where[] = '(filename LIKE ' . $search . ' OR title LIKE ' . $search . ')';
        }
        
        if (!empty($where)) {
            $query .= ' WHERE ' . implode(' AND ', $where);
        }
        
        $query .= ' ORDER BY filename';
        $sql->setQuery($query);
        
        $media = [];
        foreach ($sql->getArray() as $row) {
            $media[] = [
                'filename' => $row['filename'],
                'title' => $row['title'] ?: $row['filename'],
                'url' => rex_url::media($row['filename']),
                'is_image' => $this->isImage($row['filename'])
            ];
        }
        
        header('Content-Type: application/json');
        echo json_encode(['media' => $media]);
        exit;
    }
    
    /**
     * Check if file is an image
     */
    protected function isImage(string $filename): bool
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']);
    }
    
    /**
     * Get value from nested array using bracket notation (e.g. "items[0][title]")
     */
    protected function getNestedValue(string $key, array $data)
    {
        // Einfacher Key ohne Brackets
        if (strpos($key, '[') === false) {
            return $data[$key] ?? '';
        }
        
        // Bracket-Notation parsen: items[0][title] -> ['items', '0', 'title']
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

    protected function processFormData()
    {
        $formName = $this->params['form_name'];
        $fieldId = $this->getId();
        
        if (isset($_POST['FORM'][$formName][$fieldId])) {
            $postValue = $_POST['FORM'][$formName][$fieldId];
            
            // Wenn bereits JSON-String (aus Hidden Field), direkt verwenden
            if (is_string($postValue)) {
                // Validiere dass es gültiges JSON ist
                $decoded = json_decode($postValue, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $this->setValue($postValue);
                } else {
                    $this->setValue('');
                }
            } else {
                // Falls als Array, zu JSON konvertieren
                $jsonValue = json_encode($postValue, JSON_UNESCAPED_UNICODE);
                $this->setValue($jsonValue);
            }
            
            $this->params['value_pool']['email'][$this->getName()] = $this->getValue();
            
            if ($this->saveInDb()) {
                $this->params['value_pool']['sql'][$this->getName()] = $this->getValue();
            }
        }
    }

    protected function getTemplateVars(): array
    {
        $value = $this->parseValue();
        $framework = $this->getElement('framework', 'bootstrap');
        
        return [
            'value' => $value,
            'field_type' => 'content_builder',
            'field_name' => $this->getName(),
            'field_id' => $this->getFieldId(),
            'label' => $this->getLabel(),
            'attributes' => $this->getElement('attributes', ''),
            'notice' => $this->getElement('notice'),
            'required' => $this->getElement('required') ? true : false,
            'description' => $this->getElement('description', ''),
            'framework' => $framework,
            'available_elements' => $this->getAvailableElements(),
        ];
    }

    protected function parseValue(): array
    {
        $value = $this->getValue();
        
        if (empty($value)) {
            return [];
        }

        $data = json_decode($value, true);
        
        if (!is_array($data)) {
            return [];
        }

        return $data;
    }

    protected function getAvailableElements(): array
    {
        $addon = rex_addon::get('yform_content_builder');
        $elements = [];
        
        // 1. Extension Point: Andere AddOns können Pfade registrieren
        $customPaths = rex_extension::registerPoint(new rex_extension_point(
            'YFORM_CONTENT_BUILDER_ELEMENT_PATHS',
            []
        ));
        
        // 2. Automatisch: project AddOn prüfen (wenn kein Extension Point)
        if (empty($customPaths) && rex_addon::exists('project') && rex_addon::get('project')->isAvailable()) {
            $projectPath = rex_addon::get('project')->getPath('elements/');
            if (is_dir($projectPath)) {
                $customPaths[] = $projectPath;
            }
        }
        
        // WENN Extension Point ODER project/elements verwendet wird:
        // → NUR Custom-Elemente laden (Demos werden NICHT geladen!)
        if (!empty($customPaths)) {
            foreach ($customPaths as $customPath) {
                if (!is_dir($customPath)) {
                    continue;
                }
                
                $dirs = scandir($customPath);
                foreach ($dirs as $dir) {
                    if ($dir === '.' || $dir === '..') {
                        continue;
                    }
                    
                    $elementPath = $customPath . $dir;
                    $configFile = $elementPath . '/config.php';
                    
                    if (is_dir($elementPath) && file_exists($configFile)) {
                        $config = include $configFile;
                        $config['_source'] = 'custom';
                        $config['_path'] = $elementPath;
                        $elements[$dir] = $config;
                    }
                }
            }
            
            return $elements;
        }
        
        // FALLBACK: Wenn KEIN Extension Point registriert ist
        // → Demo-Elemente aus diesem AddOn laden
        $demoPath = $addon->getPath('elements/');
        if (is_dir($demoPath)) {
            $dirs = scandir($demoPath);
            foreach ($dirs as $dir) {
                if ($dir === '.' || $dir === '..') {
                    continue;
                }
                
                $elementPath = $demoPath . $dir;
                $configFile = $elementPath . '/config.php';
                
                if (is_dir($elementPath) && file_exists($configFile)) {
                    $config = include $configFile;
                    $config['_source'] = 'demo';
                    $config['_path'] = $elementPath;
                    $elements[$dir] = $config;
                }
            }
        }
        
        return $elements;
    }
    
    protected function getElementPath(string $elementType): ?string
    {
        $elements = $this->getAvailableElements();
        
        if (isset($elements[$elementType]['_path'])) {
            return $elements[$elementType]['_path'];
        }
        
        // Fallback: Original Pfad
        return rex_addon::get('yform_content_builder')->getPath('elements/' . $elementType);
    }

    public function renderSlice(string $elementType, array $elementData, int $index, string $sliceId, string $framework): string
    {
        $elementPath = $this->getElementPath($elementType);
        
        if (!$elementPath || !is_dir($elementPath)) {
            return '<div class="alert alert-danger">Element not found: ' . rex_escape($elementType) . '</div>';
        }
        
        // Template-Datei finden
        $templateFile = $elementPath . '/templates/' . $framework . '.php';
        if (!file_exists($templateFile)) {
            $templateFile = $elementPath . '/templates/plain.php';
        }
        
        if (!file_exists($templateFile)) {
            return '<div class="alert alert-danger">Template not found: ' . rex_escape($elementType) . '</div>';
        }
        
        // Slice-Wrapper
        ob_start();
        ?>
        <div class="content-builder-slice" 
             data-slice-id="<?= rex_escape($sliceId) ?>"
             data-slice-type="<?= rex_escape($elementType) ?>"
             data-slice-index="<?= $index ?>">
            
            <div class="slice-toolbar">
                <button type="button" class="btn btn-xs btn-default btn-slice-edit" title="Bearbeiten">
                    <i class="fa fa-pencil"></i>
                </button>
                <button type="button" class="btn btn-xs btn-default btn-slice-move" title="Verschieben">
                    <i class="fa fa-arrows"></i>
                </button>
                <button type="button" class="btn btn-xs btn-danger btn-slice-delete" title="Löschen">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
            
            <div class="slice-rendered">
                <?php include $templateFile; ?>
            </div>
            
            <div class="slice-edit-form" style="display: none;">
                <!-- Wird per JS mit YForm-Formular gefüllt -->
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function getDescription(): string
    {
        return 'content_builder';
    }

    public function getDefinitions(): array
    {
        return [
            'type' => 'value',
            'name' => 'content_builder',
            'values' => [
                'name' => ['type' => 'name', 'label' => rex_i18n::msg('yform_values_defaults_name')],
                'label' => ['type' => 'text', 'label' => rex_i18n::msg('yform_values_defaults_label')],
                'framework' => [
                    'type' => 'choice',
                    'label' => 'Framework',
                    'choices' => [
                        'bootstrap' => 'Bootstrap',
                        'uikit' => 'UIkit',
                        'plain' => 'Plain HTML'
                    ],
                    'default' => 'bootstrap'
                ],
                'description' => ['type' => 'text', 'label' => 'Beschreibung'],
                'notice' => ['type' => 'text', 'label' => rex_i18n::msg('yform_values_defaults_notice')],
            ],
            'description' => 'Slice-based Content Builder',
            'db_type' => ['text'],
        ];
    }
}
