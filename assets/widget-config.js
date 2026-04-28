/**
 * Widget Configuration Dynamic Form Builder
 * Lädt Widget-Felder dynamisch basierend auf der Auswahl
 */

(function($) {
    'use strict';
    
    // Widget-Daten werden vom Server injiziert
    window.ContentBuilderWidgets = window.ContentBuilderWidgets || {};
    
    const WidgetConfig = {
        
        retryCount: 0,
        maxRetries: 10,
        
        init: function() {
            // Prüfen ob Widget-Daten verfügbar sind
            if (!window.ContentBuilderWidgets || Object.keys(window.ContentBuilderWidgets).length === 0) {
                this.retryCount++;
                
                if (this.retryCount > this.maxRetries) {
                    console.error('ContentBuilderWidgets not available after', this.maxRetries, 'retries. Stopping.');
                    console.log('window.ContentBuilderWidgets:', window.ContentBuilderWidgets);
                    console.log('window.rex:', window.rex);
                    return;
                }
                
                console.warn('ContentBuilderWidgets not available yet, retrying... (', this.retryCount, '/', this.maxRetries, ')');
                setTimeout(function() {
                    WidgetConfig.init();
                }, 200);
                return;
            }
            
            console.log('Initializing WidgetConfig with', Object.keys(window.ContentBuilderWidgets).length, 'widgets');
            this.bindEvents();
            this.initializeExisting();
            this.observeDOMChanges();
        },
        
        observeDOMChanges: function() {
            // MutationObserver für neue Repeater-Items
            if (typeof MutationObserver === 'undefined') return;
            
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.addedNodes.length) {
                        mutation.addedNodes.forEach(function(node) {
                            if (node.nodeType === 1) { // Element node
                                // Prüfen ob es ein Repeater-Item ist oder eines enthält
                                const $node = $(node);
                                if ($node.hasClass('repeater-item') || $node.find('.repeater-item').length) {
                                    console.log('New repeater item detected, initializing widgets...');
                                    setTimeout(function() {
                                        WidgetConfig.initializeExisting();
                                    }, 100);
                                }
                            }
                        });
                    }
                });
            });
            
            // Body beobachten
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        },
        
        bindEvents: function() {
            // Bei widget_type Änderung
            $(document).on('change', '[name*="[widget_type]"]', function() {
                const $select = $(this);
                const widgetType = $select.val();
                const $repeaterItem = $select.closest('.repeater-item, .form-group').parent();
                
                console.log('Widget type changed:', widgetType);
                console.log('Container:', $repeaterItem);
                
                WidgetConfig.renderFields($repeaterItem, widgetType);
            });
            
            // Bei widget_enabled Änderung
            $(document).on('change', '[name*="[widget_enabled]"]', function() {
                const $checkbox = $(this);
                const $repeaterItem = $checkbox.closest('.repeater-item, .form-group').parent();
                const isEnabled = $checkbox.is(':checked');
                
                console.log('Widget enabled changed:', isEnabled);
                
                // Widget-Felder ein/ausblenden
                $repeaterItem.find('.widget-dynamic-fields').toggle(isEnabled);
                $repeaterItem.find('[name*="[widget_type]"]').closest('.form-group').toggle(isEnabled);
                $repeaterItem.find('[name*="[widget_position]"]').closest('.form-group').toggle(isEnabled);
            });
            
            // Formular-Submit: Felder in JSON serialisieren
            $(document).on('submit', 'form', function() {
                console.log('Form submitting, serializing widgets...');
                WidgetConfig.serializeAllWidgets($(this));
            });
        },
        
        initializeExisting: function() {
            console.log('Initializing existing widgets...');
            console.log('Available widgets:', window.ContentBuilderWidgets);
            
            // Bestehende Widget-Typen beim Laden initialisieren
            $('[name*="[widget_type]"]').each(function() {
                const $select = $(this);
                const widgetType = $select.val();
                
                console.log('Found widget_type field:', $select, 'value:', widgetType);
                
                if (widgetType) {
                    const $repeaterItem = $select.closest('.repeater-item, .form-group').parent();
                    WidgetConfig.renderFields($repeaterItem, widgetType);
                    
                    // Bestehende Config laden
                    WidgetConfig.loadExistingConfig($repeaterItem);
                }
            });
            
            // Widget-enabled Status anwenden
            $('[name*="[widget_enabled]"]').each(function() {
                const $checkbox = $(this);
                const isEnabled = $checkbox.is(':checked');
                const $repeaterItem = $checkbox.closest('.repeater-item, .form-group').parent();
                
                console.log('Found widget_enabled checkbox:', $checkbox, 'checked:', isEnabled);
                
                $repeaterItem.find('.widget-dynamic-fields').toggle(isEnabled);
                $repeaterItem.find('[name*="[widget_type]"]').closest('.form-group').toggle(isEnabled);
                $repeaterItem.find('[name*="[widget_position]"]').closest('.form-group').toggle(isEnabled);
            });
        },
        
        renderFields: function($container, widgetType) {
            console.log('Rendering fields for widget:', widgetType);
            
            // Bestehende dynamische Felder entfernen
            $container.find('.widget-dynamic-fields').remove();
            
            if (!widgetType || !window.ContentBuilderWidgets[widgetType]) {
                console.log('No widget type or widget not found');
                return;
            }
            
            const widgetInfo = window.ContentBuilderWidgets[widgetType];
            const fields = widgetInfo.fields;
            
            console.log('Widget info:', widgetInfo);
            
            // Container für dynamische Felder
            const $fieldsContainer = $('<div class="widget-dynamic-fields" style="background:#f9f9f9;padding:15px;margin:10px 0;border:1px solid #ddd;border-radius:4px;"></div>');
            $fieldsContainer.append('<h4 style="margin-top:0;"><i class="fa ' + widgetInfo.icon + '"></i> ' + widgetInfo.label + ' - Einstellungen</h4>');
            
            if (widgetInfo.description) {
                $fieldsContainer.append('<p class="help-block">' + widgetInfo.description + '</p>');
            }
            
            // Felder generieren
            Object.keys(fields).forEach(function(fieldName) {
                const fieldConfig = fields[fieldName];
                const $field = WidgetConfig.createField(fieldName, fieldConfig, widgetType);
                $fieldsContainer.append($field);
            });
            
            // Nach widget_type einfügen
            const $typeField = $container.find('[name*="[widget_type]"]').closest('.form-group');
            $typeField.after($fieldsContainer);
            
            console.log('Fields rendered, container added after:', $typeField);
        },
        
        createField: function(fieldName, config, widgetType) {
            const fieldId = 'widget_field_' + widgetType + '_' + fieldName;
            const $formGroup = $('<div class="form-group"></div>');
            const label = config.label || fieldName;
            
            // Label
            $formGroup.append('<label for="' + fieldId + '">' + label + '</label>');
            
            // Feldtyp bestimmen
            let $input;
            
            switch (config.type) {
                case 'checkbox':
                    $input = $('<input type="checkbox" id="' + fieldId + '" data-widget-field="' + fieldName + '" value="1">');
                    if (config.label) {
                        $input = $('<div class="checkbox"><label><input type="checkbox" id="' + fieldId + '" data-widget-field="' + fieldName + '" value="1"> ' + config.label + '</label></div>');
                        $formGroup.empty(); // Label entfernen bei Checkbox
                    }
                    break;
                    
                case 'choice':
                case 'select':
                    $input = $('<select class="form-control" id="' + fieldId + '" data-widget-field="' + fieldName + '"></select>');
                    
                    // Optionen hinzufügen
                    if (config.choices) {
                        Object.keys(config.choices).forEach(function(value) {
                            const optionLabel = config.choices[value];
                            $input.append('<option value="' + value + '">' + optionLabel + '</option>');
                        });
                    }
                    
                    // Default-Wert
                    if (config.default) {
                        $input.val(config.default);
                    }
                    break;
                    
                case 'textarea':
                    $input = $('<textarea class="form-control" id="' + fieldId + '" data-widget-field="' + fieldName + '" rows="3"></textarea>');
                    break;
                    
                case 'repeater':
                    // Vereinfachter Repeater (TODO: komplexer Repeater)
                    $input = $('<div class="widget-repeater" id="' + fieldId + '" data-widget-field="' + fieldName + '"></div>');
                    $input.append('<button type="button" class="btn btn-sm btn-default widget-repeater-add">Eintrag hinzufügen</button>');
                    $input.append('<div class="widget-repeater-items"></div>');
                    break;
                    
                case 'text':
                default:
                    $input = $('<input type="text" class="form-control" id="' + fieldId + '" data-widget-field="' + fieldName + '">');
                    
                    if (config.default) {
                        $input.val(config.default);
                    }
                    break;
            }
            
            $formGroup.append($input);
            
            // Notice
            if (config.notice) {
                $formGroup.append('<span class="help-block">' + config.notice + '</span>');
            }
            
            return $formGroup;
        },
        
        loadExistingConfig: function($container) {
            // Widget-Config JSON-Feld finden (falls vorhanden)
            const $configField = $container.find('[name*="[widget_config]"]');
            
            console.log('Loading existing config from:', $configField, 'value:', $configField.val());
            
            if ($configField.length && $configField.val()) {
                try {
                    const config = JSON.parse($configField.val());
                    
                    console.log('Parsed config:', config);
                    
                    // Werte in dynamische Felder laden
                    Object.keys(config).forEach(function(fieldName) {
                        const value = config[fieldName];
                        const $field = $container.find('[data-widget-field="' + fieldName + '"]');
                        
                        if ($field.length) {
                            if ($field.is(':checkbox')) {
                                $field.prop('checked', !!value);
                            } else {
                                $field.val(value);
                            }
                        }
                    });
                } catch (e) {
                    console.error('Fehler beim Laden der Widget-Config:', e);
                }
            }
        },
        
        serializeAllWidgets: function($form) {
            console.log('Serializing all widgets in form...');
            
            // Alle Widget-Container finden
            $form.find('.widget-dynamic-fields').each(function() {
                const $fieldsContainer = $(this);
                const $repeaterItem = $fieldsContainer.parent();
                const config = {};
                
                console.log('Serializing widget container:', $fieldsContainer);
                
                // Alle Widget-Felder sammeln
                $fieldsContainer.find('[data-widget-field]').each(function() {
                    const $field = $(this);
                    const fieldName = $field.data('widget-field');
                    let value;
                    
                    if ($field.is(':checkbox')) {
                        value = $field.is(':checked');
                    } else if ($field.hasClass('widget-repeater')) {
                        // Repeater-Werte sammeln (TODO)
                        value = [];
                    } else {
                        value = $field.val();
                    }
                    
                    config[fieldName] = value;
                });
                
                console.log('Serialized config:', config);
                
                // JSON in widget_config schreiben
                const $configField = $repeaterItem.find('[name*="[widget_config]"]');
                if ($configField.length) {
                    const jsonString = JSON.stringify(config);
                    $configField.val(jsonString);
                    console.log('Saved to config field:', $configField, 'value:', jsonString);
                }
            });
        }
    };
    
    // Init bei Ready und bei REDAXO-Events
    $(document).ready(function() {
        setTimeout(function() {
            WidgetConfig.init();
        }, 100);
    });
    
    $(document).on('rex:ready', function() {
        console.log('rex:ready - initializing widgets');
        setTimeout(function() {
            WidgetConfig.init();
        }, 100);
    });
    
    // Bei Repeater-Item hinzugefügt
    $(document).on('content-builder:repeater-added', function(e, $item) {
        console.log('Repeater item added, initializing widgets in item:', $item);
        setTimeout(function() {
            WidgetConfig.initializeExisting();
        }, 100);
    });
    
})(jQuery);
