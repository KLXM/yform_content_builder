<?php

use FriendsOfREDAXO\YFormContentBuilder\Fields\ContentBuilderFieldRegistry;

/**
 * YForm Content Builder für Module
 * Ermöglicht die Nutzung von Content Builder Elementen in normalen REDAXO Modulen
 * Nutzt das Field Plugin System für konsistentes Rendering
 * 
 * Verwendung:
 * Input:  echo yform_content_builder_module::create('gallery')->renderInput();
 * Output: echo yform_content_builder_module::create('gallery', 'REX_VALUE[id=1 output=html]')->renderOutput();
 */
class yform_content_builder_module
{
    protected $elementType;
    protected $data;
    protected $rawValue;
    protected $framework = 'bootstrap';
    
    /**
     * Element erstellen
     * 
     * @param string $type Element-Typ (gallery, divider, cards, etc.)
     * @param mixed $rawValue Rohe REX_VALUE Daten oder JSON-String
     * @param string $framework CSS Framework für Output (bootstrap, uikit, plain)
     * @return self
     */
    public static function create($type, $rawValue = null, $framework = 'bootstrap')
    {
        $instance = new self();
        $instance->elementType = $type;
        $instance->rawValue = $rawValue;
        $instance->framework = $framework;
        
        // Daten normalisieren
        if (is_string($rawValue)) {
            // Wenn der String escaped ist (aus REX_VALUE), erst decoden
            $decoded = html_entity_decode($rawValue, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $instance->data = json_decode($decoded, true) ?: [];
        } elseif (is_array($rawValue)) {
            $instance->data = $rawValue;
        } else {
            $instance->data = [];
        }
        
        return $instance;
    }
    
    /**
     * Input-Formular ausgeben - nutzt YForm Content Builder Render-Logik
     * 
     * @return string HTML des Input-Formulars
     */
    public function renderInput()
    {
        // Config laden
        $config = $this->loadConfig();
        if (!$config) {
            return '<div class="alert alert-danger">Element-Config für "' . rex_escape($this->elementType) . '" nicht gefunden.</div>';
        }
        
        ob_start();
        
        echo '<div class="form-group yform-content-builder-module-input" data-element-type="' . rex_escape($this->elementType) . '">';
        
        // Hidden Field für JSON-Daten (wird von JavaScript gefüllt)
        echo '<input type="hidden" name="REX_INPUT_VALUE[1]" id="yform_cb_data_storage" value="' . rex_escape(json_encode($this->data)) . '" />';
        
        // Formular mit YForm Content Builder Render-Methoden
        echo '<div class="slice-form-container" id="yform_cb_form">';
        $this->renderFormFields($config, $this->data);
        echo '</div>';
        
        echo '</div>';
        
        // Script um Selectpicker mit sanitize:false zu initialisieren (für SVG/img data-content)
        ?>
        <script nonce="<?= rex_response::getNonce() ?>">
        $(function() {
            // Selectpicker mit sanitize:false neu initialisieren, damit SVG-Icons angezeigt werden
            $('#yform_cb_form .selectpicker').each(function() {
                var $select = $(this);
                // Wenn bereits initialisiert, zerstören
                if ($select.data('selectpicker')) {
                    $select.selectpicker('destroy');
                }
                // Mit sanitize:false neu initialisieren
                $select.selectpicker({
                    sanitize: false
                });
            });
        });
        </script>
        <?php
        
        // JavaScript für automatisches Data-Sync
        ?>
        <script nonce="<?= rex_response::getNonce() ?>">
        (function() {
            var storage = document.getElementById('yform_cb_data_storage');
            var form = document.getElementById('yform_cb_form');
            
            if (!storage || !form) {
                console.warn('YForm Content Builder: Storage or form not found');
                return;
            }
            
            // Funktion zum Sammeln aller Formulardaten
            function collectFormData() {
                // Erst CKEditor5-Inhalte in Textareas synchronisieren (REDAXO CKE5)
                if (typeof ckeditors !== 'undefined') {
                    form.querySelectorAll('textarea.cke5-editor').forEach(function(textarea) {
                        var editorId = textarea.id;
                        if (ckeditors[editorId]) {
                            // Daten aus Editor ins Textarea schreiben
                            textarea.value = ckeditors[editorId].getData();
                        }
                    });
                }
                
                var data = {};
                
                // Sammle Felder aus dem Hauptformular UND aus allen Modals
                var allFields = form.querySelectorAll('input[name], textarea[name], select[name]');
                
                // Auch Felder in Bootstrap Modals sammeln (die sind außerhalb des Forms)
                var modalFields = document.querySelectorAll('.modal input[name], .modal textarea[name], .modal select[name]');
                
                // Beide NodeLists kombinieren
                var combinedFields = Array.from(allFields).concat(Array.from(modalFields));
                
                var processedFields = new Set(); // Track welche Felder wir schon verarbeitet haben
                
                // Alle Inputs, Textareas und Selects sammeln
                combinedFields.forEach(function(field) {
                    var name = field.getAttribute('name');
                    
                    // Skip fields from template items (hidden repeater templates)
                    var isInTemplate = field.closest('.repeater-item-template');
                    if (isInTemplate) {
                        return; // Skip template fields
                    }
                    
                    // Radio-Buttons: Nur den gechecked Button verarbeiten
                    if (field.type === 'radio') {
                        if (!field.checked) {
                            return; // Nicht-gecheckte Radio-Buttons überspringen
                        }
                        // Gechecked Radio darf auch bei gleichem Namen verarbeitet werden
                    } else {
                        // Skip wenn wir dieses Feld schon verarbeitet haben
                        // (REDAXO kann mehrere Inputs mit gleichem Namen haben)
                        if (processedFields.has(name)) {
                            return;
                        }
                    }
                    processedFields.add(name);
                    
                    // Repeater-Felder (z.B. items[0][media]) zu verschachteltem Array konvertieren
                    var repeaterMatch = name.match(/^(\w+)\[(\d+)\]\[(\w+)\]$/);
                    if (repeaterMatch) {
                        var repeaterName = repeaterMatch[1];  // z.B. "items"
                        var index = parseInt(repeaterMatch[2]); // z.B. 0
                        var fieldName = repeaterMatch[3];      // z.B. "media"
                        
                        // Repeater-Array initialisieren
                        if (!data[repeaterName]) {
                            data[repeaterName] = [];
                        }
                        
                        // Index-Objekt initialisieren
                        if (!data[repeaterName][index]) {
                            data[repeaterName][index] = {};
                        }
                        
                        // Wert setzen
                        if (field.type === 'checkbox') {
                            data[repeaterName][index][fieldName] = field.checked ? '1' : '';
                        } else {
                            data[repeaterName][index][fieldName] = field.value;
                        }
                    }
                    // Normale Felder
                    else {
                        // Checkboxen
                        if (field.type === 'checkbox') {
                            data[name] = field.checked ? '1' : '';
                        }
                        // Normale Felder
                        else {
                            data[name] = field.value;
                        }
                    }
                });
                
                // Leere Arrays aus Repeatern bereinigen (nur Items mit Werten behalten)
                Object.keys(data).forEach(function(key) {
                    if (Array.isArray(data[key])) {
                        // Leere Objekte aus Array entfernen
                        data[key] = data[key].filter(function(item) {
                            if (typeof item !== 'object') return true;
                            // Prüfen ob Objekt mindestens einen nicht-leeren Wert hat
                            return Object.values(item).some(function(val) {
                                return val !== '' && val !== null && val !== undefined;
                            });
                        });
                        // Leere Arrays ganz entfernen
                        if (data[key].length === 0) {
                            delete data[key];
                        }
                    }
                });
                
                // JSON in Hidden Field speichern
                storage.value = JSON.stringify(data);
            }
            
            // Bei Änderungen Daten sammeln
            form.addEventListener('change', collectFormData);
            form.addEventListener('input', collectFormData);
            
            // Repeater item removal event
            $(form).on('repeater:item-removed', function() {
                collectFormData();
            });
            
            // REDAXO Media Widget Change Events (für REX_MEDIA_ Felder)
            // REDAXO schreibt in Felder mit ID REX_MEDIA_X, triggert aber jQuery change
            $(form).on('change', 'input[id^="REX_MEDIA_"]', function() {
                collectFormData();
            });
            
            // REDAXO Link Widget Change Events (für REX_LINK_ Felder)
            // REDAXO's Linkmap setzt das Hidden Field, aber triggert kein change Event
            $(form).on('change', 'input[id^="REX_LINK_"]', function() {
                collectFormData();
            });
            
            // REDAXO's rex:selectLink Event (wird nach Linkmap-Auswahl gefeuert)
            $(window).on('rex:selectLink', function(event, link, name) {
                // 300ms warten, damit REDAXO's Callback das Hidden Field sicher gesetzt hat
                setTimeout(function() {
                    collectFormData();
                }, 300);
            });
            
            // Bootstrap Modal Events - Daten sammeln wenn Modal geschlossen wird
            $('.modal').on('hidden.bs.modal', function() {
                collectFormData();
            });
            
            // Auch auf Änderungen in Modals reagieren
            $(document).on('change input', '.modal input, .modal textarea, .modal select', function() {
                collectFormData();
            });
            
            // CKEditor5 Change Events abfangen (REDAXO CKE5)
            // REDAXO CKE5 verwendet ClassicEditor und speichert Instanzen in window.ckeditors
            $(window).on('rex:cke5IsInit', function(event, editor, editorId) {
                // Prüfen ob dieser Editor zu unserem Formular gehört
                var textarea = form.querySelector('#' + editorId);
                if (textarea) {
                    // Change Event für Auto-Sync
                    editor.model.document.on('change:data', function() {
                        // Daten aus Editor ins Textarea schreiben
                        textarea.value = editor.getData();
                        collectFormData();
                    });
                }
            });
            
            // Für bereits initialisierte CKE5 Instanzen
            if (typeof ckeditors !== 'undefined') {
                form.querySelectorAll('textarea.cke5-editor').forEach(function(textarea) {
                    var editorId = textarea.id;
                    if (ckeditors[editorId]) {
                        var editor = ckeditors[editorId];
                        editor.model.document.on('change:data', function() {
                            textarea.value = editor.getData();
                            collectFormData();
                        });
                    }
                });
            }
            
            // Initial sammeln
            collectFormData();
            
            // Repeater-Funktionalität initialisieren (wird von content-builder.js bereitgestellt)
            if (typeof window.ContentBuilder !== 'undefined') {
                window.ContentBuilder.initRepeaters();
                
                // Nach Repeater-Änderungen auch Daten sammeln
                form.addEventListener('repeater:changed', collectFormData);
            }
        })();
        </script>
        <?php
        
        return ob_get_clean();
    }
    
    /**
     * Output-HTML ausgeben
     * 
     * @return string HTML des Elements
     */
    public function renderOutput()
    {
        // Config laden
        $config = $this->loadConfig();
        if (!$config) {
            return rex::isDebugMode() ? '<div class="alert alert-warning">Element-Config nicht gefunden</div>' : '';
        }
        
        // Wenn keine Daten vorhanden, nichts ausgeben
        if (empty($this->data)) {
            return '';
        }
        
        // Element-Template suchen - Framework-spezifisch
        $elementDir = rex_path::addon('yform_content_builder', 'elements/' . $this->elementType);
        
        // Erst Framework-spezifisches Template versuchen
        $elementFile = $elementDir . '/templates/' . $this->framework . '.php';
        
        // Fallback auf element.php (legacy)
        if (!file_exists($elementFile)) {
            $elementFile = $elementDir . '/element.php';
        }
        
        if (!file_exists($elementFile)) {
            return rex::isDebugMode() ? '<div class="alert alert-warning">Element-Template nicht gefunden: ' . $elementFile . '</div>' : '';
        }
        
        // Element-Template einbinden
        ob_start();
        $data = $this->data; // Für Template verfügbar machen
        $elementData = $this->data; // Alias für Template-Kompatibilität
        $config = $config; // Config auch verfügbar machen
        $framework = $this->framework; // Framework für Template verfügbar
        include $elementFile;
        return ob_get_clean();
    }
    
    /**
     * Config des Elements laden
     * 
     * @return array|null
     */
    protected function loadConfig()
    {
        $configFile = rex_path::addon('yform_content_builder', 'elements/' . $this->elementType . '/config.php');
        
        if (!file_exists($configFile)) {
            return null;
        }
        
        return include $configFile;
    }
    
    /**
     * Formular-Felder rendern - nutzt YForm Content Builder renderFormField
     */
    protected function renderFormFields(array $config, array $sliceData)
    {
        // Settings Modal Button (falls definiert)
        if (isset($config['settings_modal']) && is_array($config['settings_modal'])) {
            $this->renderSettingsModalButton($config, $sliceData);
        }
        
        // Prüfen ob Tabs definiert sind
        if (isset($config['field_groups']) && is_array($config['field_groups'])) {
            $this->renderFormWithTabs($config, $sliceData);
        } else {
            // Standard: Alle Felder ohne Tabs
            $modalFields = [];
            if (isset($config['settings_modal']['fields'])) {
                $modalFields = $config['settings_modal']['fields'];
            }
            
            $self = $this;
            ContentBuilderFieldRegistry::renderFieldRowsGroup(
                $config['fields'],
                $modalFields,
                function (string $fieldName, array $fieldConfig) use ($self, $sliceData): void {
                    $self->renderFormField($fieldName, $fieldConfig, $sliceData);
                }
            );
        }
    }
    
    /**
     * Einzelnes Form-Feld rendern - nutzt ContentBuilderFieldRegistry
     */
    protected function renderFormField(string $fieldName, array $fieldConfig, array $sliceData)
    {
        // Wert aus sliceData extrahieren
        $value = $this->getValueForField($fieldName, $sliceData);
        
        // Wenn ein Wert gefunden wurde, in sliceData einfügen
        if ($value !== null && $value !== '') {
            if (strpos($fieldName, '[') === false) {
                $sliceData[$fieldName] = $value;
            } else {
                $sliceData[$fieldName] = $value;
            }
        }
        
        // Field Registry nutzen für konsistentes Rendering
        ContentBuilderFieldRegistry::renderField($fieldName, $fieldConfig, $sliceData);
    }
    
    /**
     * Get value for a field from sliceData - handles nested arrays properly
     */
    protected function getValueForField(string $fieldName, array $sliceData)
    {
        // Einfacher Key ohne Brackets
        if (strpos($fieldName, '[') === false) {
            return $sliceData[$fieldName] ?? null;
        }
        
        // Repeater-Field: items[0][title]
        // Parsen: items[0][title] -> baseField=items, index=0, subField=title
        if (preg_match('/^(\w+)\[(\d+)\]\[(\w+)\]$/', $fieldName, $matches)) {
            $baseField = $matches[1];  // z.B. "items"
            $index = (int)$matches[2]; // z.B. 0
            $subField = $matches[3];   // z.B. "title"
            
            if (isset($sliceData[$baseField][$index][$subField])) {
                return $sliceData[$baseField][$index][$subField];
            }
        }
        
        return null;
    }
    
    /**
     * Settings Modal Button rendern
     */
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
     * Formular mit Tabs rendern
     */
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
                $groupFieldMap = [];
                foreach ($group['fields'] as $fieldName) {
                    if (isset($config['fields'][$fieldName])) {
                        $groupFieldMap[$fieldName] = $config['fields'][$fieldName];
                    }
                }
                $self = $this;
                ContentBuilderFieldRegistry::renderFieldRowsGroup(
                    $groupFieldMap,
                    [],
                    function (string $fieldName, array $fieldConfig) use ($self, $sliceData): void {
                        $self->renderFormField($fieldName, $fieldConfig, $sliceData);
                    }
                );
            }
            
            echo '</div>';
            $firstTab = false;
        }
        echo '</div>';
    }
}
