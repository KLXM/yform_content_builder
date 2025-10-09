/**
 * YForm Content Builder JavaScript
 * Edit-on-Click & Drag-a            $(document).on('click', '.btn-delete-media', function(e) {
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
            this.initMoveButtons();
            this.initGridViews();
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
            
            // Media Browser Events
            $(document).on('click', '.btn-select-media', function(e) {
                e.preventDefault();
                var inputId = $(this).data('input-id');
                if (window.MediaBrowser) {
                    window.MediaBrowser.open(inputId);
                }
            });
            
            $(document).on('click', '.btn-select-media-enhanced', function(e) {
                e.preventDefault();
                var inputId = $(this).data('input-id');
                var allowedTypes = $(this).data('allowed-types');
                if (window.MediaBrowser) {
                    window.MediaBrowser.openEnhanced(inputId, allowedTypes);
                }
            });
            
            // Enhanced Media Platzhalter klickbar machen
            $(document).on('click', '.media-preview-enhanced .media-placeholder', function(e) {
                e.preventDefault();
                var $preview = $(this).closest('.media-preview-enhanced');
                var previewId = $preview.attr('id');
                var inputId = previewId.replace('preview_', '');
                var $input = $('#' + inputId);
                var allowedTypes = $input.data('allowed-types');
                
                if (window.MediaBrowser && allowedTypes) {
                    window.MediaBrowser.openEnhanced(inputId, allowedTypes);
                }
            });
            
            // Video Controls
            $(document).on('click', '.btn-video-play', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $video = $(this).closest('.media-item-video').find('video')[0];
                var $button = $(this);
                
                if ($video.paused) {
                    $video.play();
                    $button.html('<i class="fa fa-pause"></i>');
                    $button.attr('title', 'Pausieren');
                } else {
                    $video.pause();
                    $button.html('<i class="fa fa-play"></i>');
                    $button.attr('title', 'Abspielen');
                }
            });
            
            $(document).on('click', '.btn-video-mute', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $video = $(this).closest('.media-item-video').find('video')[0];
                var $button = $(this);
                
                if ($video.muted) {
                    $video.muted = false;
                    $button.html('<i class="fa fa-volume-up"></i>');
                    $button.attr('title', 'Stumm schalten');
                } else {
                    $video.muted = true;
                    $button.html('<i class="fa fa-volume-off"></i>');
                    $button.attr('title', 'Ton an');
                }
            });
            
            $(document).on('click', '.btn-video-fullscreen', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $video = $(this).closest('.media-item-video').find('video')[0];
                
                if ($video.requestFullscreen) {
                    $video.requestFullscreen();
                } else if ($video.webkitRequestFullscreen) {
                    $video.webkitRequestFullscreen();
                } else if ($video.mozRequestFullScreen) {
                    $video.mozRequestFullScreen();
                } else if ($video.msRequestFullscreen) {
                    $video.msRequestFullscreen();
                }
            });
            
            // Video Click zum Play/Pause
            $(document).on('click', '.media-item-video video', function(e) {
                e.preventDefault();
                var $video = this;
                var $playButton = $(this).closest('.media-item-video').find('.btn-video-play');
                
                if ($video.paused) {
                    $video.play();
                    $playButton.html('<i class="fa fa-pause"></i>');
                    $playButton.attr('title', 'Pausieren');
                } else {
                    $video.pause();
                    $playButton.html('<i class="fa fa-play"></i>');
                    $playButton.attr('title', 'Abspielen');
                }
            });
            
            // Video Overlay Click
            $(document).on('click', '.media-item-video .media-overlay', function(e) {
                e.preventDefault();
                var $video = $(this).closest('.media-item-video').find('video')[0];
                var $playButton = $(this).closest('.media-item-video').find('.btn-video-play');
                
                if ($video.paused) {
                    $video.play();
                    $playButton.html('<i class="fa fa-pause"></i>');
                    $playButton.attr('title', 'Pausieren');
                } else {
                    $video.pause();
                    $playButton.html('<i class="fa fa-play"></i>');
                    $playButton.attr('title', 'Abspielen');
                }
            });
            
            // Video Loading Events
            $(document).on('loadedmetadata', '.media-item-video video', function() {
                $(this).closest('.media-item-video').find('.video-fallback').hide();
            });
            
            $(document).on('error', '.media-item-video video', function() {
                $(this).hide();
                $(this).closest('.media-item-video').find('.video-fallback').show();
            });
            
            // Video Hover Events für Controls
            $(document).on('mouseenter', '.media-item-video', function() {
                $(this).find('.media-controls').fadeIn(200);
            });
            
            $(document).on('mouseleave', '.media-item-video', function() {
                $(this).find('.media-controls').fadeOut(200);
            });
            
            $(document).on('click', '.btn-delete-media', function(e) {
                e.preventDefault();
                var inputId = $(this).data('input-id');
                $('#' + inputId).val('');
                $('#preview_' + inputId).hide().empty();
                
                // Enhanced preview zurücksetzen
                var $enhancedPreview = $('#preview_' + inputId);
                if ($enhancedPreview.hasClass('media-preview-enhanced')) {
                    var $input = $('#' + inputId);
                    var allowedTypes = $input.data('allowed-types');
                    if (allowedTypes) {
                        var allowedTypesArray = allowedTypes.split(',');
                        self.resetEnhancedPreview($enhancedPreview, allowedTypesArray);
                    }
                }
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
                                    // CKE5 initialization failed
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
            });            // Slice-Daten als Attribut UND als jQuery data speichern
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
                },
                error: function(xhr, status, error) {
                    $slice.find('.slice-rendered').html('<div class="alert alert-danger">Fehler beim Laden des Templates</div>').show();
                }
            });
        },

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
                    // Error parsing slice data
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
                
                // Alle Media-Preview Container leeren (bevor IDs geändert werden)
                $newItem.find('.media-preview').each(function() {
                    $(this).empty().hide();
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
                                // Preview sollte bereits geleert sein (siehe oben)
                            }

                            // Zusätzlich: Alle anderen Referenzen auf die alte ID aktualisieren
                            $newItem.find('[data-target]').each(function() {
                                var $btn = $(this);
                                var target = $btn.attr('data-target');
                                if (target && target === '#' + oldId) {
                                    $btn.attr('data-target', '#' + newId);
                                }
                            });
                        }
                        
                        if ($input.is(':checkbox') || $input.is(':radio')) {
                            $input.prop('checked', false);
                        } else if ($input.is('select')) {
                            // For select fields, check if there's a default selected option
                            var $defaultOption = $input.find('option[selected]');
                            if ($defaultOption.length > 0) {
                                $input.val($defaultOption.val());
                            } else {
                                // If no default, select first option
                                var firstValue = $input.find('option:first').val();
                                $input.val(firstValue);
                            }
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
                        '<div class="move-buttons">' +
                            '<button type="button" class="btn-move btn-move-up" title="Nach oben"><i class="fa fa-chevron-up"></i></button>' +
                            '<button type="button" class="btn-move btn-move-down" title="Nach unten"><i class="fa fa-chevron-down"></i></button>' +
                        '</div>' +
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
                    var oldId = $input.attr('id');
                    
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
                            
                            // Zusätzlich: Alle anderen Referenzen auf die alte ID aktualisieren
                            $newItem.find('[data-target]').each(function() {
                                var $btn = $(this);
                                var target = $btn.attr('data-target');
                                if (target && target === '#' + oldId) {
                                    $btn.attr('data-target', '#' + newId);
                                }
                            });
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
            
            // Move Button States aktualisieren
            this.updateMoveButtonStates();
            
            // CKE5 in neuem Item initialisieren - EINFACHER ANSATZ
            setTimeout(function() {
                $newItem.find('textarea.cke5-editor').each(function() {
                    var $textarea = $(this);
                    
                    if (typeof cke5_init === 'function') {
                        try {
                            cke5_init($textarea);
                        } catch(e) {
                            // CKE5 initialization failed
                        }
                    }
                });
            }, 500); // Länger warten
            
        },
        
        resetEnhancedPreview: function($preview, allowedTypes) {
            // Platzhalter je nach erlaubten Typen
            var placeholderText = '';
            var placeholderIcon = 'fa-file';
            
            if (allowedTypes.includes('image') && allowedTypes.includes('video')) {
                placeholderText = 'Bild oder Video auswählen';
                placeholderIcon = 'fa-file-image-o';
            } else if (allowedTypes.includes('image')) {
                placeholderText = 'Bild auswählen';
                placeholderIcon = 'fa-image';
            } else if (allowedTypes.includes('video')) {
                placeholderText = 'Video auswählen';
                placeholderIcon = 'fa-video-camera';
            }
            
            $preview.html(
                '<div class="media-placeholder">' +
                    '<i class="fa ' + placeholderIcon + '"></i>' +
                    '<p>' + placeholderText + '</p>' +
                '</div>'
            ).show();
        },
        
        /**
         * Initialize Grid Views
         */
        initGridViews: function() {
            $('.repeater-grid-view').each(function() {
                var $container = $(this);
                var columns = $container.data('grid-columns') || 3;
                $container.css('grid-template-columns', 'repeat(' + columns + ', 1fr)');
            });
        },
        
        /**
         * Initialize Move Buttons for all Repeaters
         */
        initMoveButtons: function() {
            var self = this;
            
            // Move Up Button
            $(document).on('click', '.btn-move-up', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                var $item = $(this).closest('.repeater-item');
                var $prevItem = $item.prev('.repeater-item:not(.repeater-item-template)');
                
                if ($prevItem.length > 0) {
                    $item.fadeOut(150, function() {
                        $item.insertBefore($prevItem);
                        $item.fadeIn(150);
                        self.updateRepeaterIndices($item.closest('.repeater-container'));
                    });
                }
            });
            
            // Move Down Button
            $(document).on('click', '.btn-move-down', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                var $item = $(this).closest('.repeater-item');
                var $nextItem = $item.next('.repeater-item:not(.repeater-item-template)');
                
                if ($nextItem.length > 0) {
                    $item.fadeOut(150, function() {
                        $item.insertAfter($nextItem);
                        $item.fadeIn(150);
                        self.updateRepeaterIndices($item.closest('.repeater-container'));
                    });
                }
            });
            
            // Update button states after initialization
            this.updateMoveButtonStates();
        },
        
        /**
         * Update repeater indices after reordering
         */
        updateRepeaterIndices: function($container) {
            $container.find('.repeater-item:not(.repeater-item-template)').each(function(index) {
                $(this).attr('data-index', index);
                
                // Input-Namen aktualisieren
                $(this).find('input, textarea, select').each(function() {
                    var $input = $(this);
                    var name = $input.attr('name');
                    if (name && name.indexOf('[') !== -1) {
                        var newName = name.replace(/\[(\d+)\]/g, '[' + index + ']');
                        $input.attr('name', newName);
                    }
                });
            });
            
            // Button states aktualisieren
            this.updateMoveButtonStates();
        },
        
        /**
         * Update move button states (disable if first/last)
         */
        updateMoveButtonStates: function() {
            $('.repeater-container').each(function() {
                var $container = $(this);
                var $items = $container.find('.repeater-item:not(.repeater-item-template)');
                
                $items.each(function(index) {
                    var $item = $(this);
                    var $upBtn = $item.find('.btn-move-up');
                    var $downBtn = $item.find('.btn-move-down');
                    
                    // Erster Item: Up-Button deaktivieren
                    $upBtn.prop('disabled', index === 0);
                    
                    // Letzter Item: Down-Button deaktivieren
                    $downBtn.prop('disabled', index === $items.length - 1);
                });
            });
        }
    };

    $(document).ready(function() {
        ContentBuilder.init();
        
        // Media Browser initialisieren, falls vorhanden
        if (window.MediaBrowser) {
            window.MediaBrowser.init();
        } else if (window.ContentBuilderMediaBrowser) {
            window.MediaBrowser = window.ContentBuilderMediaBrowser;
            window.MediaBrowser.init();
        }
    });

})(jQuery);
