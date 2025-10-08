/**
 * YForm Content Builder JavaScript
 * Edit-on-Click & Drag-a                  // REX Media Browser-Buttons werden jetzt vom media-browser.js behandelt).on('click', '.btn-delete-media', function(e) {
                e.preventDefault();
                var inputId = $(this).data('input-id');
                $('#' + inputId).val('');
                $('#preview_' + inputId).hide().empty();
            });

            // Repeater hinzufügentionalität
 */

(function($) {
    'use strict';

    var ContentBuilder = {
        
        init: function() {
            this.bindEvents();
            this.initSortable();
            this.updateSectionClasses();
        },

        bindEvents: function() {
            var self = this;

            // Slice löschen - MUSS VOR edit kommen!
            $(document).on('click', '.btn-slice-delete', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $slice = $(this).closest('.content-builder-slice');
                self.deleteSlice($slice);
                return false;
            });
            
            // Move Button - stopPropagation damit Edit nicht triggert
            $(document).on('click', '.btn-slice-move', function(e) {
                e.stopPropagation();
            });

            // Slice bearbeiten - Edit on Click
            $(document).on('click', '.btn-slice-edit', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $slice = $(this).closest('.content-builder-slice');
                self.editSlice($slice);
            });

            // Neues Slice hinzufügen
            $(document).on('click', '.btn-add-slice', function(e) {
                e.preventDefault();
                var elementType = $(this).data('element-type');
                var elementLabel = $(this).data('element-label');
                var $container = $(this).closest('.yform-content-builder').find('.content-builder-slices');
                self.addSlice($container, elementType, elementLabel);
            });

            // Formular speichern
            $(document).on('click', '.btn-slice-save', function(e) {
                e.preventDefault();
                var $slice = $(this).closest('.content-builder-slice');
                self.saveSlice($slice);
            });

            // Formular abbrechen
            $(document).on('click', '.btn-slice-cancel', function(e) {
                e.preventDefault();
                var $slice = $(this).closest('.content-builder-slice');
                self.cancelEdit($slice);
            });
            
            // Media Browser Events - Custom Off-Canvas Implementation
            $(document).on('click', '.btn-select-media', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                var $btn = $(this);
                var inputId = $btn.data('input-id');
                
                // Debug: Loggen welcher Button geklickt wurde
                console.log('Media button clicked for input:', inputId);
                
                // Media Browser wird jetzt von media-browser.js behandelt
                console.log('Media button intercepted, handled by MediaBrowser');
                
                return false;
            });
            
            $(document).on('click', '.btn-delete-media', function(e) {
                e.preventDefault();
                
                var inputId = $(this).data('input-id');
                var $input = $('#' + inputId);
                var $preview = $('#preview_' + inputId);
                
                console.log('Delete media for input:', inputId, 'found input:', $input.length);
                
                $input.val('');
                $preview.hide().empty();
                
                return false;
            });

            // Repeater: Item hinzufügen
            $(document).on('click', '.btn-add-repeater', function(e) {
                e.preventDefault();
                var $container = $(this).siblings('.repeater-container');
                self.addRepeaterItem($container);
            });

            // Repeater: Item entfernen
            $(document).on('click', '.btn-remove-repeater', function(e) {
                e.preventDefault();
                $(this).closest('.repeater-item').fadeOut(200, function() {
                    $(this).remove();
                });
            });
        },

        /**
         * Aktualisiert die in-section Klassen basierend auf Section-Elementen
         */
        updateSectionClasses: function() {
            var $slices = $('.content-builder-slices');
            
            $slices.each(function() {
                var $container = $(this);
                var $allSlices = $container.find('.content-builder-slice');
                var inSection = false;
                
                // Erst alle in-section Klassen entfernen
                $allSlices.removeClass('in-section');
                
                // Dann neu zuweisen
                $allSlices.each(function() {
                    var $slice = $(this);
                    var isSection = $slice.hasClass('is-section');
                    
                    if (isSection) {
                        // Section-Element: Neue Section beginnt
                        inSection = true;
                    } else {
                        // Normales Element: Wenn wir in einer Section sind, Klasse setzen
                        if (inSection) {
                            $slice.addClass('in-section');
                        }
                    }
                });
            });
        },

        initSortable: function() {
            var self = this;
            var slicesContainer = document.querySelector('.content-builder-slices');
            
            if (slicesContainer && typeof Sortable !== 'undefined') {
                new Sortable(slicesContainer, {
                    handle: '.btn-slice-move',
                    animation: 150,
                    ghostClass: 'slice-ghost',
                    onEnd: function(evt) {
                        self.updateIndices();
                        self.updateHiddenField();
                        self.updateSectionClasses(); // Nach Sortierung aktualisieren
                    }
                });
            }
        },

        editSlice: function($slice) {
            // Gerenderte Ansicht ausblenden
            $slice.find('.slice-rendered').hide();
            $slice.find('.slice-toolbar').hide();
            
            // Edit-Form anzeigen
            var $editForm = $slice.find('.slice-edit-form');
            
            if ($editForm.children().length === 0) {
                // Formular erstmal laden
                this.loadSliceForm($slice);
            }
            
            $editForm.show();
        },

        loadSliceForm: function($slice) {
            var sliceType = $slice.data('slice-type');
            var sliceData = this.getSliceData($slice);
            var $editForm = $slice.find('.slice-edit-form');
            
            
            // YForm-Formular per AJAX laden
            $.ajax({
                url: window.location.href,
                method: 'POST',
                data: {
                    action: 'load_slice_form',
                    slice_type: sliceType,
                    slice_data: sliceData
                },
                success: function(response) {
                    $editForm.html(response);
                    
                    // REX Linkmap-Buttons mit Event-Delegation initialisieren
                    $editForm.on('click', '.rex-linkmap-btn', function(e) {
                        e.preventDefault();
                        var $btn = $(this);
                        var inputId = $btn.data('id');
                        var params = $btn.data('params') || '';
                        
                        if (typeof openLinkMap === 'function') {
                            openLinkMap(inputId, params);
                        }
                        return false;
                    });
                    
                    // REX Linkmap Delete-Buttons
                    $editForm.on('click', '.rex-linkmap-delete-btn', function(e) {
                        e.preventDefault();
                        var $btn = $(this);
                        var counter = $btn.data('counter');
                        
                        if (typeof deleteREXLink === 'function') {
                            deleteREXLink(counter);
                        }
                        return false;
                    });
                    
                    // CKEditor 5 initialisieren - EINFACHER ANSATZ
                    setTimeout(function() {
                        $editForm.find('textarea.cke5-editor').each(function() {
                            var $textarea = $(this);
                            
                            if (typeof cke5_init === 'function') {
                                try {
                                    cke5_init($textarea);
                                } catch(e) {
                                    console.error('CKE5 init error:', e);
                                }
                            }
                        });
                    }, 300);
                }
            });
        },

        saveSlice: function($slice) {
            var self = this;
            var $editForm = $slice.find('.slice-edit-form');
            var sliceData = {};
            
            
            // WICHTIG: CKE5-Instanzen in Textareas zurückschreiben
            $editForm.find('textarea.cke5-editor').each(function() {
                var $textarea = $(this);
                var textareaId = $textarea.attr('id');
                
                
                // CKE5-Instanz finden und Daten in Textarea schreiben
                if (typeof ckeditors !== 'undefined' && ckeditors[textareaId]) {
                    var editorData = ckeditors[textareaId].getData();
                    $textarea.val(editorData);
                } else {
                    console.warn('CKE5 instance not found for:', textareaId, 'Available editors:', typeof ckeditors !== 'undefined' ? Object.keys(ckeditors) : 'none');
                }
            });
            
            // Form-Daten sammeln - direkt aus allen Input-Feldern im Edit-Container
            $editForm.find('input, textarea, select').each(function() {
                var $field = $(this);
                var name = $field.attr('name');
                var value = $field.val();
                
                
                if (name && value !== undefined && value !== '') {
                    // Verschachteltes Objekt erstellen aus Bracket-Notation
                    self.setNestedValue(sliceData, name, value);
                }
            });
            
            
            // Slice-Daten als Attribut UND als jQuery data speichern
            $slice.attr('data-slice-data', JSON.stringify(sliceData));
            $slice.data('slice-data', sliceData);
            
            // Slice neu rendern
            this.renderSlice($slice, sliceData);
            
            // Zur Ansicht zurück
            this.cancelEdit($slice);
            
            // Hidden Field updaten
            this.updateHiddenField();
            
            // Section-Klassen aktualisieren (falls Section gespeichert wurde)
            this.updateSectionClasses();
        },
        
        /**
         * Set nested value from bracket notation (e.g. "items[0][title]" = "value")
         */
        setNestedValue: function(obj, path, value) {
            // items[0][title] -> ['items', '0', 'title']
            var keys = path.match(/[^\[\]]+/g);
            
            var current = obj;
            for (var i = 0; i < keys.length - 1; i++) {
                var key = keys[i];
                var nextKey = keys[i + 1];
                
                // Wenn nächster Key eine Zahl ist, Array erstellen
                if (!isNaN(nextKey)) {
                    if (!current[key]) {
                        current[key] = [];
                    }
                } else {
                    if (!current[key]) {
                        current[key] = {};
                    }
                }
                
                current = current[key];
            }
            
            current[keys[keys.length - 1]] = value;
        },

        renderSlice: function($slice, sliceData) {
            var sliceType = $slice.data('slice-type');
            var framework = $slice.closest('.yform-content-builder').data('framework') || 'bootstrap';
            
            // Section-Elemente im Backend speziell rendern
            if (sliceType === 'section') {
                var label = sliceData.label || 'Unbenannt';
                var bgColor = sliceData.background_color || '';
                var customId = sliceData.custom_id || '';
                
                var html = '<div class="section-backend-label">' +
                    '<i class="fa fa-object-group"></i>' +
                    '<strong>Section:</strong> ' + $('<div>').text(label).html() +
                    '<span class="section-info">';
                
                if (bgColor && bgColor !== 'none') {
                    html += '<span class="label label-default">' + $('<div>').text(bgColor).html() + '</span>';
                }
                
                if (customId) {
                    html += '<span class="label label-info">#' + $('<div>').text(customId).html() + '</span>';
                }
                
                html += '</span></div>';
                
                $slice.find('.slice-rendered').html(html).show();
                return;
            }
            
            // Normale Elemente: Template per AJAX laden und rendern
            $.ajax({
                url: window.location.href,
                method: 'POST',
                data: {
                    action: 'render_slice',
                    slice_type: sliceType,
                    slice_data: sliceData,
                    framework: framework
                },
                success: function(response) {
                    $slice.find('.slice-rendered').html(response).show();
                }
            });
        },

        // Media-Browser-Funktionen wurden nach media-browser.js ausgelagert





        cancelEdit: function($slice) {
            $slice.find('.slice-edit-form').hide();
            $slice.find('.slice-rendered').show();
            $slice.find('.slice-toolbar').show();
        },

        deleteSlice: function($slice) {
            var self = this;
            
            // Bootstrap Modal statt confirm()
            var modal = '<div class="modal fade" id="delete-slice-modal" tabindex="-1">' +
                '<div class="modal-dialog modal-sm">' +
                    '<div class="modal-content">' +
                        '<div class="modal-header">' +
                            '<button type="button" class="close" data-dismiss="modal">&times;</button>' +
                            '<h4 class="modal-title">Element löschen</h4>' +
                        '</div>' +
                        '<div class="modal-body">' +
                            '<p>Element wirklich löschen?</p>' +
                        '</div>' +
                        '<div class="modal-footer">' +
                            '<button type="button" class="btn btn-default" data-dismiss="modal">Abbrechen</button>' +
                            '<button type="button" class="btn btn-danger" id="confirm-delete-slice">Löschen</button>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>';
            
            // Modal ins DOM einfügen
            $('body').append(modal);
            var $modal = $('#delete-slice-modal');
            
            // Confirm-Handler
            $('#confirm-delete-slice').on('click', function() {
                $modal.modal('hide');
                
                $slice.fadeOut(300, function() {
                    $(this).remove();
                    self.updateIndices();
                    self.updateHiddenField();
                    self.updateSectionClasses(); // Nach Löschen aktualisieren
                });
            });
            
            // Modal nach Schließen aufräumen
            $modal.on('hidden.bs.modal', function() {
                $(this).remove();
            });
            
            // Modal anzeigen
            $modal.modal('show');
        },

        addSlice: function($container, elementType, elementLabel) {
            var sliceId = 'slice_' + Date.now();
            var index = $container.children('.content-builder-slice').length;
            
            // Section-Element?
            var isSectionClass = (elementType === 'section') ? ' is-section' : '';
            
            var $newSlice = $('<div class="content-builder-slice' + isSectionClass + '" data-slice-id="' + sliceId + '" data-slice-type="' + elementType + '" data-slice-index="' + index + '">' +
                '<div class="slice-toolbar">' +
                    '<button type="button" class="btn btn-xs btn-default btn-slice-edit" title="Bearbeiten"><i class="fa fa-pencil"></i></button>' +
                    '<button type="button" class="btn btn-xs btn-default btn-slice-move" title="Verschieben"><i class="fa fa-arrows"></i></button>' +
                    '<button type="button" class="btn btn-xs btn-danger btn-slice-delete" title="Löschen"><i class="fa fa-trash"></i></button>' +
                '</div>' +
                '<div class="slice-rendered"><div class="alert alert-info">Neues Element: ' + elementLabel + ' - Klicken zum Bearbeiten</div></div>' +
                '<div class="slice-edit-form" style="display: none;"></div>' +
            '</div>');
            
            $container.append($newSlice);
            
            // Direkt bearbeiten
            this.editSlice($newSlice);
            
            this.updateIndices();
            this.updateSectionClasses(); // Nach Hinzufügen aktualisieren
        },

        getSliceData: function($slice) {
            // Zuerst aus dem Attribut lesen (aktuellste Daten)
            var dataAttr = $slice.attr('data-slice-data');
            if (dataAttr) {
                try {
                    return JSON.parse(dataAttr);
                } catch(e) {
                    console.error('Error parsing slice data:', e);
                }
            }
            
            // Fallback auf jQuery data
            var dataStr = $slice.data('slice-data');
            if (dataStr && typeof dataStr === 'string') {
                try {
                    return JSON.parse(dataStr);
                } catch(e) {
                    return {};
                }
            }
            
            // Falls bereits als Objekt
            if (typeof dataStr === 'object') {
                return dataStr;
            }
            
            return {};
        },

        updateIndices: function() {
            $('.content-builder-slice').each(function(index) {
                $(this).attr('data-slice-index', index);
            });
        },

        updateHiddenField: function() {
            $('.yform-content-builder').each(function() {
                var $container = $(this);
                var slices = [];
                
                $container.find('.content-builder-slice').each(function() {
                    var $slice = $(this);
                    slices.push({
                        id: $slice.data('slice-id'),
                        type: $slice.data('slice-type'),
                        data: ContentBuilder.getSliceData($slice)
                    });
                });
                
                $container.find('.content-builder-data').val(JSON.stringify(slices));
            });
        },

        addRepeaterItem: function($container) {
            var fieldName = $container.data('field');
            var $items = $container.find('.repeater-item:not(.repeater-item-template)');
            var $templateItem = $container.find('.repeater-item-template');
            var newIndex = $items.length;
            
            
            // Prüfen ob ein verstecktes Template-Item existiert
            if ($templateItem.length > 0) {
                var $newItem = $templateItem.clone(false, false);
                $newItem.removeClass('repeater-item-template');
                $newItem.show();
                $newItem.attr('data-index', newIndex);
                
                // Neue eindeutige IDs für das Item und Modal generieren
                var newItemId = 'repeater_item_' + Math.random().toString(16).slice(2);
                $newItem.attr('id', newItemId);
                
                // Modal ID aktualisieren (falls vorhanden)
                var $modal = $newItem.find('.modal');
                if ($modal.length > 0) {
                    var newModalId = newItemId + '_modal';
                    $modal.attr('id', newModalId);
                    $newItem.find('[data-toggle="modal"]').attr('data-target', '#' + newModalId);
                }
                
                // CKE5 Elemente entfernen
                $newItem.find('.ck-editor__editable').remove();
                $newItem.find('.ck-editor__top').remove();
                $newItem.find('.ck-editor__main').remove();
                $newItem.find('.ck-editor').remove();
                $newItem.find('.ck').remove();
                
                // Textareas zurücksetzen
                $newItem.find('textarea.cke5-editor').each(function() {
                    var $ta = $(this);
                    $ta.removeAttr('id');
                    $ta.removeClass('ck-hidden');
                    $ta.removeAttr('data-cke-init');
                    if (!$ta.attr('data-profile')) {
                        $ta.attr('data-profile', 'default');
                    }
                });
                
                // Input-Namen und Werte aktualisieren
                $newItem.find('input, textarea, select').each(function() {
                    var $input = $(this);
                    var name = $input.attr('name');
                    var oldId = $input.attr('id');
                    
                    if (name) {
                        var newName = name.replace(/\[(\d+)\]/g, function(match, num) {
                            return '[' + newIndex + ']';
                        });
                        $input.attr('name', newName);
                        
                        if ($input.hasClass('cke5-editor')) {
                            var newId = 'cke5_' + Math.random().toString(16).slice(2);
                            $input.attr('id', newId);
                        }
                        
                        // Media/Link-Inputs: Neue ID generieren
                        if (oldId && (oldId.indexOf('media_') === 0 || oldId.indexOf('REX_LINK_') === 0)) {
                            var newId = oldId.replace(/_\w+$/, '_' + Date.now() + '_' + newIndex);
                            $input.attr('id', newId);
                            
                            // Zugehörige Buttons aktualisieren (data-id für Linkmap, data-input-id für Media)
                            $newItem.find('[data-id="' + oldId + '"]').attr('data-id', newId);
                            $newItem.find('[data-input-id="' + oldId + '"]').attr('data-input-id', newId);
                            
                            // NAME-Input aktualisieren (für Linkmap)
                            var $nameInput = $newItem.find('#' + oldId + '_NAME');
                            if ($nameInput.length) {
                                $nameInput.attr('id', newId + '_NAME');
                            }
                            
                            // Preview-Container aktualisieren (für Media)
                            var $preview = $newItem.find('#preview_' + oldId);
                            if ($preview.length) {
                                $preview.attr('id', 'preview_' + newId);
                            }
                        }
                        
                        if ($input.is(':checkbox') || $input.is(':radio')) {
                            $input.prop('checked', false);
                        } else {
                            $input.val('');
                        }
                    }
                });
                
            } else if ($items.length === 0) {
                // Kein Item vorhanden - Template aus data-template holen oder Standard erstellen
                var template = $container.data('template');
                if (template) {
                    var $newItem = $(template);
                    
                    // Neue IDs für CKE5-Textareas generieren (auch im Template)
                    $newItem.find('textarea.cke5-editor').each(function() {
                        var $ta = $(this);
                        if (!$ta.attr('id')) {
                            var newId = 'cke5_' + Math.random().toString(16).slice(2);
                            $ta.attr('id', newId);
                        }
                    });
                } else {
                    // Standard-Template für text/textarea Felder
                    var newId = 'cke5_' + Math.random().toString(16).slice(2);
                    var $newItem = $('<div class="repeater-item" data-index="' + newIndex + '">' +
                        '<div class="form-group">' +
                            '<label>Titel</label>' +
                            '<input type="text" class="form-control" name="' + fieldName + '[' + newIndex + '][title]" />' +
                        '</div>' +
                        '<div class="form-group">' +
                            '<label>Inhalt</label>' +
                            '<textarea id="' + newId + '" class="form-control cke5-editor" data-profile="default" name="' + fieldName + '[' + newIndex + '][content]"></textarea>' +
                        '</div>' +
                        '<button type="button" class="btn btn-sm btn-danger btn-remove-repeater"><i class="fa fa-trash"></i></button>' +
                    '</div>');
                }
            } else {
                // Item vorhanden - Clone vom letzten
                var $lastItem = $items.last();
                var $newItem = $lastItem.clone(false, false); // Shallow clone, keine Events, keine Data
                
                $newItem.attr('data-index', newIndex);
                
                // Neue eindeutige IDs für das Item und Modal generieren
                var oldItemId = $lastItem.attr('id');
                var newItemId = 'repeater_item_' + Math.random().toString(16).slice(2);
                $newItem.attr('id', newItemId);
                
                // Modal ID aktualisieren (falls vorhanden)
                var $modal = $newItem.find('.modal');
                if ($modal.length > 0) {
                    var oldModalId = $modal.attr('id');
                    var newModalId = newItemId + '_modal';
                    $modal.attr('id', newModalId);
                    
                    // Modal-Button data-target aktualisieren
                    $newItem.find('[data-toggle="modal"]').attr('data-target', '#' + newModalId);
                    
                }
                
                // WICHTIG: Alle CKE5-DOM-Elemente komplett entfernen
                $newItem.find('.ck-editor__editable').remove();
                $newItem.find('.ck-editor__top').remove();
                $newItem.find('.ck-editor__main').remove();
                $newItem.find('.ck-editor').remove();
                $newItem.find('.ck').remove();
                
                
                // Alle Textareas zurücksetzen und neue IDs geben
                $newItem.find('textarea.cke5-editor').each(function() {
                    var $ta = $(this);
                    // Alte ID entfernen
                    $ta.removeAttr('id');
                    // CKE5-Klassen/Attribute zurücksetzen
                    $ta.removeClass('ck-hidden');
                    $ta.removeAttr('data-cke-init');
                    // WICHTIG: data-profile BEHALTEN oder setzen
                    if (!$ta.attr('data-profile')) {
                        $ta.attr('data-profile', 'default');
                    }
                });
                
                // Input-Namen und Werte aktualisieren
                $newItem.find('input, textarea, select').each(function() {
                    var $input = $(this);
                    var name = $input.attr('name');
                    
                    if (name) {
                        // Index im Namen ersetzen (alle Vorkommen)
                        var newName = name.replace(/\[(\d+)\]/g, function(match, num) {
                            return '[' + newIndex + ']';
                        });
                        $input.attr('name', newName);
                        
                        // Neue eindeutige ID für CKE5-Textareas generieren
                        if ($input.hasClass('cke5-editor')) {
                            var newId = 'cke5_' + Math.random().toString(16).slice(2);
                            $input.attr('id', newId);
                            $input.removeAttr('repeater_cke'); // Damit cke5_init neue ID generiert
                        }
                        
                        // Wert leeren
                        if ($input.is(':checkbox') || $input.is(':radio')) {
                            $input.prop('checked', false);
                        } else {
                            $input.val('');
                        }
                    }
                });
            }
            
            $container.append($newItem);
            $newItem.hide().fadeIn(200);
            
            // CKE5 in neuem Item initialisieren - EINFACHER ANSATZ
            setTimeout(function() {
                $newItem.find('textarea.cke5-editor').each(function() {
                    var $textarea = $(this);
                    
                    // DEBUG: Prüfen ob bereits ein CKE5-Editor existiert
                    var nextElement = $textarea.next();
                    
                    if (typeof cke5_init === 'function') {
                        try {
                            cke5_init($textarea);
                        } catch(e) {
                            console.error('CKE5 init error:', e);
                        }
                    } else {
                        console.error('cke5_init function not found');
                    }
                });
            }, 500); // Länger warten
            
        }
    };

    $(document).ready(function() {
        ContentBuilder.init();
        
        // Media Browser initialisieren, falls vorhanden
        if (window.MediaBrowser) {
            window.MediaBrowser.init();
        }
    });

})(jQuery);
