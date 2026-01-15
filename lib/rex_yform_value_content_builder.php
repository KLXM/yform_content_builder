<?php

/**
 * YForm Content Builder Field
 * Slice-based content management für YForm
 */
class rex_yform_value_content_builder extends rex_yform_value_abstract
{
    private static $widgetCounters = [
        'media' => 0,
        'link' => 0,
    ];
    
    /**
     * Get next unique media counter (global über alle Instanzen)
     */
    private static function getNextMediaCounter()
    {
        if (!isset($GLOBALS['yform_cb_media_counter'])) {
            $GLOBALS['yform_cb_media_counter'] = 0;
        }
        return ++$GLOBALS['yform_cb_media_counter'];
    }
    
    public function enterObject()
    {
        // AJAX-Anfragen behandeln - wenn AJAX, wird hier beendet
        if ($this->handleAjaxRequests()) {
            return; // AJAX wurde behandelt, normal processing stoppen
        }
        
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
            return;
        }
        
        $config = include $configFile;
        
        if (!isset($config['fields']) || !is_array($config['fields'])) {
            echo '<div class="alert alert-danger">Element-Konfiguration fehlerhaft: ' . rex_escape($sliceType) . '</div>';
            return;
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
        echo '<div class="btn-group pull-right">';
        echo '<button type="button" class="btn btn-success btn-slice-save"><i class="fa fa-check"></i> Übernehmen</button>';
        echo '<button type="button" class="btn btn-danger btn-slice-cancel" data-confirm="Bearbeitung abbrechen? Alle Änderungen gehen verloren."><i class="fa fa-times"></i> Abbrechen</button>';
        echo '</div>';
        echo '<div class="clearfix"></div>';
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
    
    /**
     * Rendert ein generisches Modal für ein Feld im Repeater
     * Kann für verschiedene Modal-Typen verwendet werden (item_modal, media_modal, etc.)
     */
    protected function renderRepeaterFieldModal(string $itemId, int $index, string $fieldName, array $fieldConfig, array $item, string $baseFieldName, string $modalKey)
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
        // Berechtigungsprüfung: Feld nicht rendern wenn Berechtigung fehlt
        if (isset($fieldConfig['perm'])) {
            // 'admin' => nur für Admins
            if ($fieldConfig['perm'] === 'admin' && !rex::getUser()?->isAdmin()) {
                return;
            }
        }
        
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
                // REDAXO Standard Media Widget
                $mediaCounter = self::getNextMediaCounter();
                
                $inputId = 'REX_MEDIA_' . $mediaCounter;
                
                // Robuste Behandlung von allowed_types
                $types = '';
                $allowedTypes = [];
                if (isset($fieldConfig['allowed_types'])) {
                    if (is_array($fieldConfig['allowed_types'])) {
                        $allowedTypes = $fieldConfig['allowed_types'];
                    } else {
                        $allowedTypes = array_map('trim', explode(',', $fieldConfig['allowed_types']));
                    }
                    $types = implode(',', $allowedTypes);
                }
                
                $wdgtClass = 'rex-js-widget rex-js-widget-media';
                
                echo '<div class="' . $wdgtClass . '">';
                echo '<div class="input-group">';
                echo '<input class="form-control content-builder-media-input" type="text" ';
                echo 'name="' . rex_escape($fieldName) . '" ';
                echo 'id="' . $inputId . '" ';
                echo 'value="' . rex_escape($value) . '" ';
                echo 'data-media-id="' . $mediaCounter . '" />';
                echo '<span class="input-group-btn">';
                
                $openMediaParams = $types ? ", '&types=" . rex_escape($types) . "'" : '';
                echo '<a href="#" class="btn btn-popup" ';
                echo 'onclick="openREXMedia(' . $mediaCounter . $openMediaParams . '); return false;" ';
                echo 'title="' . rex_i18n::msg('var_media_open') . '">';
                echo '<i class="rex-icon fa fa-folder-open"></i></a>';
                
                echo '<a href="#" class="btn btn-popup" ';
                echo 'onclick="viewREXMedia(' . $mediaCounter . $openMediaParams . '); return false;" ';
                echo 'title="' . rex_i18n::msg('var_media_view') . '">';
                echo '<i class="rex-icon fa fa-eye"></i></a>';
                
                echo '<a href="#" class="btn btn-popup btn-delete-cb-media" ';
                echo 'data-input-id="' . $inputId . '" ';
                echo 'onclick="return false;" ';
                echo 'title="' . rex_i18n::msg('var_media_remove') . '">';
                echo '<i class="rex-icon fa fa-trash"></i></a>';
                
                echo '</span></div>';
                
                // Eigene Preview Implementation
                echo '<div class="content-builder-media-preview" data-input-id="' . $inputId . '">';
                if ($value) {
                    $mediaPath = rex_path::media($value);
                    $isImage = yform_content_builder_helper::isImage($value);
                    $isVideo = yform_content_builder_helper::isVideo($value);
                    
                    if ($isImage && file_exists($mediaPath)) {
                        $mediaUrl = rex_url::media($value);
                        if (rex_addon::get('media_manager')->isAvailable()) {
                            $mediaUrl = rex_media_manager::getUrl('yform_content_builder_preview', $value);
                        }
                        echo '<div class="cb-media-preview-item">';
                        echo '<div class="cb-media-container">';
                        echo '<img src="' . $mediaUrl . '" alt="' . rex_escape($value) . '" />';
                        echo '</div>';
                        echo '<span class="cb-media-filename">' . rex_escape($value) . '</span>';
                        echo '</div>';
                    } elseif ($isVideo && file_exists($mediaPath)) {
                        $mediaUrl = rex_url::media($value);
                        echo '<div class="cb-media-preview-item cb-media-video">';
                        echo '<div class="cb-media-container">';
                        echo '<video controls preload="metadata">';
                        echo '<source src="' . $mediaUrl . '" />';
                        echo '</video>';
                        echo '</div>';
                        echo '<span class="cb-media-filename">' . rex_escape($value) . '</span>';
                        echo '</div>';
                    } else {
                        echo '<div class="cb-media-preview-item cb-media-file">';
                        echo '<i class="fa fa-file"></i>';
                        echo '<span class="cb-media-filename">' . rex_escape($value) . '</span>';
                        echo '</div>';
                    }
                }
                echo '</div>';
                echo '</div>';
                break;
                
            case 'be_link':
                // REDAXO Linkmap Widget
                self::$widgetCounters['link']++;
                $linkCounter = self::$widgetCounters['link'];
                
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
                break;
                
            case 'radio_image':
                // Radio-Buttons mit Bildern/SVGs für visuelle Layout-Auswahl
                $options = $fieldConfig['options'] ?? [];
                $default = $fieldConfig['default'] ?? '';
                
                if (empty($value) && !empty($default)) {
                    $value = $default;
                }
                
                // Eindeutiger Prefix für diese Radio-Gruppe (inkl. uniqid für Repeater)
                $groupId = 'radio_' . uniqid() . '_';
                
                echo '<div class="radio-image-group">';
                foreach ($options as $optValue => $optData) {
                    $checked = ($value == $optValue) ? ' checked' : '';
                    $inputId = $groupId . md5($optValue);
                    
                    // Label und Image aus optData extrahieren
                    $label = $optData;
                    $image = '';
                    if (is_array($optData)) {
                        $label = $optData['label'] ?? $optValue;
                        $image = $optData['image'] ?? '';
                    }
                    
                    echo '<div class="radio-image-item' . ($checked ? ' active' : '') . '">';
                    echo '<input type="radio" name="' . rex_escape($fieldName) . '" ';
                    echo 'id="' . $inputId . '" ';
                    echo 'value="' . rex_escape($optValue) . '"' . $checked . '>';
                    echo '<label for="' . $inputId . '">';
                    
                    if (!empty($image)) {
                        // Bild oder SVG Base64
                        if (strpos($image, 'data:image/svg+xml;base64,') === 0) {
                            echo '<img src="' . $image . '" alt="' . rex_escape($label) . '">';
                        } else {
                            echo '<img src="' . rex_escape($image) . '" alt="' . rex_escape($label) . '">';
                        }
                    }
                    
                    echo '<span class="radio-image-label">' . rex_escape($label) . '</span>';
                    echo '</label>';
                    echo '</div>';
                }
                echo '</div>';
                break;
            
            case 'color_swatches':
                // Farbauswahl mit visuellen Farbfeldern (wie MForm RadioColorField)
                $options = $fieldConfig['options'] ?? [];
                $default = $fieldConfig['default'] ?? '';
                
                if (empty($value) && !empty($default)) {
                    $value = $default;
                }
                
                // Eindeutiger Prefix für diese Radio-Gruppe
                $groupId = 'color_' . uniqid() . '_';
                
                echo '<div class="color-swatches-group">';
                foreach ($options as $optValue => $optData) {
                    $checked = ($value == $optValue) ? ' checked' : '';
                    $inputId = $groupId . md5($optValue);
                    
                    // Label und Color extrahieren
                    $label = $optData;
                    $color = '#cccccc';
                    if (is_array($optData)) {
                        $label = $optData['label'] ?? $optValue;
                        $color = $optData['color'] ?? '#cccccc';
                    }
                    
                    // Transparenz-Style für spezielle Farben
                    $bgStyle = '';
                    if ($color === 'transparent' || $color === '') {
                        $bgStyle = 'background: linear-gradient(135deg, #fff 0%, #fff 45%, #ff0000 50%, #fff 55%, #fff 100%);';
                    } else {
                        $bgStyle = 'background-color: ' . $color . ';';
                    }
                    
                    // Border für helle Farben
                    $borderStyle = '';
                    if ($color === '#ffffff' || $color === '#fff' || $color === 'white') {
                        $borderStyle = 'border: 1px solid #ccc;';
                    }
                    
                    echo '<div class="color-swatch-item' . ($checked ? ' active' : '') . '">';
                    echo '<input type="radio" name="' . rex_escape($fieldName) . '" ';
                    echo 'id="' . $inputId . '" ';
                    echo 'value="' . rex_escape($optValue) . '"' . $checked . '>';
                    echo '<label for="' . $inputId . '" title="' . rex_escape($label) . '">';
                    echo '<span class="color-swatch" style="' . $bgStyle . $borderStyle . '"></span>';
                    echo '</label>';
                    echo '</div>';
                }
                echo '</div>';
                break;
                
            case 'choice':
                $choices = $fieldConfig['choices'] ?? [];
                $default = $fieldConfig['default'] ?? '';
                // Selectpicker ist standardmäßig aktiviert für einheitliches Styling
                // Kann mit 'selectpicker' => false explizit deaktiviert werden
                $useSelectpicker = $fieldConfig['selectpicker'] ?? true;
                
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
                
                // Farbdaten für Selectpicker mit Farbvorschau
                $choiceColors = $fieldConfig['choice_colors'] ?? [];
                // Icons/Piktogramme für Selectpicker (z.B. Layout-Auswahl)
                $choiceIcons = $fieldConfig['choice_icons'] ?? [];
                
                $selectClass = 'form-control';
                if ($useSelectpicker) {
                    $selectClass .= ' selectpicker';
                }
                
                echo '<select class="' . $selectClass . '" name="' . rex_escape($fieldName) . '">';
                foreach ($choices as $choiceValue => $choiceLabel) {
                    $selected = ($value == $choiceValue) ? ' selected' : '';
                    
                    $dataContent = '';
                    
                    // Icon/Piktogramm für Selectpicker (z.B. SVG oder HTML)
                    if ($useSelectpicker && isset($choiceIcons[$choiceValue])) {
                        $iconHtml = $choiceIcons[$choiceValue];
                        // SVG/HTML für data-content - nur Anführungszeichen escapen, nicht das HTML
                        $escapedIcon = str_replace('"', '&quot;', $iconHtml);
                        $dataContent = ' data-content="' . $escapedIcon . ' ' . rex_escape($choiceLabel) . '"';
                    }
                    // Farbvorschau für Selectpicker wenn Farben definiert sind
                    elseif ($useSelectpicker && isset($choiceColors[$choiceValue])) {
                        $colorData = $choiceColors[$choiceValue];
                        $color = is_array($colorData) ? ($colorData['color'] ?? '') : $colorData;
                        if (!empty($color)) {
                            $borderStyle = ($color === '#ffffff' || $color === 'transparent' || $color === '#fff') 
                                ? 'border: 1px solid #ccc;' 
                                : '';
                            $bgColor = $color === 'transparent' 
                                ? 'background: repeating-linear-gradient(45deg, #f0f0f0, #f0f0f0 5px, #fff 5px, #fff 10px);'
                                : 'background-color: ' . rex_escape($color) . ';';
                            $dataContent = ' data-content="<span style=\'display:inline-block;width:16px;height:16px;margin-right:8px;vertical-align:middle;border-radius:3px;' . $bgColor . $borderStyle . '\'></span>' . rex_escape($choiceLabel) . '"';
                        }
                    }
                    
                    echo '<option value="' . rex_escape($choiceValue) . '"' . $selected . $dataContent . '>' . rex_escape($choiceLabel) . '</option>';
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
                
                // Alle Modals mit trigger_after sammeln (z.B. media_modal)
                $triggerModals = [];
                foreach ($fieldConfig as $configKey => $configValue) {
                    if (str_ends_with($configKey, '_modal') && $configKey !== 'item_modal' && is_array($configValue)) {
                        if (isset($configValue['trigger_after']) && isset($configValue['fields'])) {
                            $triggerModals[$configValue['trigger_after']] = $configKey;
                            // Diese Felder auch aus der Hauptansicht ausblenden
                            $itemModalFields = array_merge($itemModalFields, $configValue['fields']);
                        }
                    }
                }
                
                // View Config prüfen - kann auch dynamisch aus Slice-Daten kommen
                $view = $fieldConfig['view'] ?? 'list';
                $gridColumns = intval($fieldConfig['grid_columns'] ?? 3);
                
                // Prüfen ob view_mode in den Slice-Daten gesetzt ist (für dynamische Umschaltung)
                if (isset($sliceData['view_mode'])) {
                    $view = $sliceData['view_mode'];
                }
                
                $containerClass = 'repeater-container';
                
                if ($view === 'grid') {
                    $containerClass .= ' repeater-grid-view';
                }
                
                echo '<div class="repeater-wrapper">';
                echo '<div class="' . $containerClass . '" data-field="' . htmlspecialchars($fieldName) . '" data-view="' . htmlspecialchars($view, ENT_QUOTES, 'UTF-8') . '" data-grid-columns="' . htmlspecialchars($gridColumns, ENT_QUOTES, 'UTF-8') . '">';
                
                // Template-Item erstellen wenn keine Items vorhanden sind
                if (empty($items) && $hasItemModal) {
                    $templateId = 'repeater_item_template_' . uniqid();
                    echo '<div class="repeater-item repeater-item-template" data-index="0" id="' . $templateId . '" style="display:none;">';
                    
                    // Move Buttons für Template
                    echo '<div class="move-buttons">';
                    echo '<button type="button" class="btn-move btn-move-up" title="Nach oben"><i class="fa fa-chevron-up"></i></button>';
                    echo '<button type="button" class="btn-move btn-move-down" title="Nach unten"><i class="fa fa-chevron-down"></i></button>';
                    echo '</div>';
                    
                    // Template-Felder rendern
                    $templateFieldCount = 0;
                    foreach ($fieldConfig['fields'] as $subFieldName => $subFieldConfig) {
                        if (in_array($subFieldName, $itemModalFields)) {
                            continue;
                        }
                        
                        $fullFieldName = $fieldName . '[0][' . $subFieldName . ']';
                        $subData = [$baseFieldName => [0 => []]];
                        $this->renderFormField($fullFieldName, $subFieldConfig, $subData);
                        
                        $templateFieldCount++;
                        
                        // Trigger-Modal nach diesem Feld anzeigen
                        if (isset($triggerModals[$subFieldName])) {
                            $modalKey = $triggerModals[$subFieldName];
                            $this->renderRepeaterFieldModal($templateId, 0, $fieldName, $fieldConfig, [], $baseFieldName, $modalKey);
                        }
                        
                        // Item-Modal Button nach 2. Feld
                        if ($hasItemModal && $templateFieldCount === 2) {
                            $this->renderRepeaterItemModal($templateId, 0, $fieldName, $fieldConfig, [], $baseFieldName);
                        }
                    }
                    
                    echo '<button type="button" class="btn btn-sm btn-danger btn-remove-repeater"><i class="fa fa-trash"></i></button>';
                    echo '</div>';
                }
                
                foreach ($items as $index => $item) {
                    $itemId = 'repeater_item_' . uniqid();
                    echo '<div class="repeater-item" data-index="' . $index . '" id="' . $itemId . '">';
                    
                    // Move Buttons für alle Repeater
                    echo '<div class="move-buttons">';
                    echo '<button type="button" class="btn-move btn-move-up" title="Nach oben"><i class="fa fa-chevron-up"></i></button>';
                    echo '<button type="button" class="btn-move btn-move-down" title="Nach unten"><i class="fa fa-chevron-down"></i></button>';
                    echo '</div>';
                    
                    // Haupt-Felder (nicht im Modal)
                    $fieldsRendered = 0;
                    foreach ($fieldConfig['fields'] as $subFieldName => $subFieldConfig) {
                        // Überspringen wenn Feld im Modal ist
                        if (in_array($subFieldName, $itemModalFields)) {
                            continue;
                        }
                        
                        $subValue = $item[$subFieldName] ?? '';
                        $fullFieldName = $fieldName . '[' . $index . '][' . $subFieldName . ']';
                        
                        // Für Subfelder müssen wir die Daten anders übergeben
                        $subData = [$baseFieldName => [$index => $item]];
                        $this->renderFormField($fullFieldName, $subFieldConfig, $subData);
                        
                        $fieldsRendered++;
                        
                        // Trigger-Modal nach diesem Feld anzeigen (z.B. media_modal nach image)
                        if (isset($triggerModals[$subFieldName])) {
                            $modalKey = $triggerModals[$subFieldName];
                            $this->renderRepeaterFieldModal($itemId, $index, $fieldName, $fieldConfig, $item, $baseFieldName, $modalKey);
                        }
                        
                        // Item-Modal Button nach dem 2. Feld einfügen
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
     * Check if file is an image
     */
    protected function isImage(string $filename): bool
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']);
    }
    
    /**
     * Check if file is a video
     */
    protected function isVideo(string $filename): bool
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($ext, ['mp4', 'webm', 'mov', 'avi', 'mkv', 'ogg']);
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

    /**
     * Lädt ALLE verfügbaren Elemente (für Definition/Config)
     * Ohne Filter durch allowed_elements
     */
    protected function getAllElementsForDefinition(): array
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

    /**
     * Lädt verfügbare Elemente mit optionalem Filter durch allowed_elements
     */
    protected function getAvailableElements(): array
    {
        // Alle Elemente laden
        $allElements = $this->getAllElementsForDefinition();
        
        // Prüfen ob allowed_elements gesetzt ist
        $allowedElements = $this->getElement('allowed_elements');
        
        // Wenn allowed_elements leer ist oder nicht gesetzt: alle Elemente zurückgeben
        if (empty($allowedElements)) {
            return $allElements;
        }
        
        // allowed_elements kann als JSON-String gespeichert sein
        if (is_string($allowedElements)) {
            $decoded = json_decode($allowedElements, true);
            if (is_array($decoded)) {
                $allowedElements = $decoded;
            } else {
                // Komma-separierte Liste?
                $allowedElements = array_map('trim', explode(',', $allowedElements));
            }
        }
        
        // Nur erlaubte Elemente zurückgeben
        if (is_array($allowedElements) && !empty($allowedElements)) {
            return array_intersect_key($allElements, array_flip($allowedElements));
        }
        
        return $allElements;
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
        // Alle verfügbaren Elemente für Multiselect sammeln
        $elementChoices = $this->buildElementChoices();
        
        // Theme-Auswahl (wenn UIkit Theme Builder verfügbar)
        $themeChoices = ['' => '-- Domain-Standard --'];
        if (rex_addon::get('uikit_theme_builder')->isAvailable() && class_exists('UikitThemeBuilder\DomainContext')) {
            $availableThemes = \UikitThemeBuilder\DomainContext::getAvailableThemes();
            $themeChoices = array_merge($themeChoices, $availableThemes);
        }
        
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
                        'tailwind' => 'Tailwind',
                        'plain' => 'Plain HTML'
                    ],
                    'default' => 'bootstrap'
                ],
                'theme' => [
                    'type' => 'choice',
                    'label' => 'Theme',
                    'choices' => $themeChoices,
                    'default' => '',
                    'notice' => 'Theme für dieses Feld. Leer = automatisch nach Domain.'
                ],
                'allowed_elements' => [
                    'type' => 'choice',
                    'label' => 'Erlaubte Elemente (leer = alle)',
                    'choices' => $elementChoices,
                    'multiple' => true,
                    'expanded' => false,
                    'default' => '',
                ],
                'description' => ['type' => 'text', 'label' => 'Beschreibung'],
                'notice' => ['type' => 'text', 'label' => rex_i18n::msg('yform_values_defaults_notice')],
            ],
            'description' => 'Slice-based Content Builder',
            'db_type' => ['text'],
        ];
    }

    public static function getListValue($params)
    {
        $value = (string) $params['subject'];
        
        if (empty($value)) {
            return '<em>-- Leer --</em>';
        }
        
        $data = json_decode($value, true);
        
        if (!is_array($data) || empty($data)) {
            return '<em>-- Keine Elemente --</em>';
        }
        
        // Element-Übersicht mit Zähler
        $elements = [];
        foreach ($data as $slice) {
            $type = $slice['type'] ?? 'unknown';
            $label = ucfirst(str_replace('_', ' ', $type));
            
            if (!isset($elements[$label])) {
                $elements[$label] = 0;
            }
            $elements[$label]++;
        }
        
        $output = [];
        foreach ($elements as $label => $count) {
            if ($count > 1) {
                $output[] = $label . ' (' . $count . 'x)';
            } else {
                $output[] = $label;
            }
        }
        
        $summary = '<strong>' . count($data) . ' Element' . (count($data) !== 1 ? 'e' : '') . ':</strong> ' . implode(', ', $output);
        
        // Im Debug-Mode zusätzlich JSON anzeigen
        if (rex::isDebugMode()) {
            $summary .= '<br><details style="margin-top:5px;"><summary style="cursor:pointer; font-size:11px;">JSON (Debug)</summary>';
            $summary .= '<pre style="background:#f5f5f5; padding:10px; border-radius:3px; font-size:11px; overflow:auto; max-height:300px; margin-top:5px;">';
            $summary .= rex_escape(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $summary .= '</pre></details>';
        }
        
        return '<span>' . $summary . '</span>';
    }


    /**
     * List-Ansicht für Datenbank-Manager
     * Zeigt Element-Übersicht oder nur JSON mit Debug-Mode
     */
    /**
     * Baut Element-Choices für das Definitions-Formular
     * Statisch, damit es in getDefinitions() funktioniert
     */
    protected function buildElementChoices(): array
    {
        $addon = rex_addon::get('yform_content_builder');
        $choices = [];
        
        // 1. Extension Point prüfen
        $customPaths = rex_extension::registerPoint(new rex_extension_point(
            'YFORM_CONTENT_BUILDER_ELEMENT_PATHS',
            []
        ));
        
        // 2. project AddOn prüfen
        if (empty($customPaths) && rex_addon::exists('project') && rex_addon::get('project')->isAvailable()) {
            $projectPath = rex_addon::get('project')->getPath('elements/');
            if (is_dir($projectPath)) {
                $customPaths[] = $projectPath;
            }
        }
        
        // Custom Elemente
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
                    
                    $configFile = $customPath . $dir . '/config.php';
                    if (file_exists($configFile)) {
                        $config = include $configFile;
                        $choices[$dir] = $config['label'] ?? ucfirst($dir);
                    }
                }
            }
            return $choices;
        }
        
        // Demo Elemente
        $demoPath = $addon->getPath('elements/');
        if (is_dir($demoPath)) {
            $dirs = scandir($demoPath);
            foreach ($dirs as $dir) {
                if ($dir === '.' || $dir === '..') {
                    continue;
                }
                
                $configFile = $demoPath . $dir . '/config.php';
                if (file_exists($configFile)) {
                    $config = include $configFile;
                    $choices[$dir] = $config['label'] ?? ucfirst($dir);
                }
            }
        }
        
        return $choices;
    }
}
