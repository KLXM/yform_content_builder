/**
 * YForm Content Builder JavaScript
 * Edit-on-Click & Drag-Funktionalität
 */

(function($) {
    'use strict';
    
    // Flag um doppelte Initialisierung zu verhindern
    var eventsInitialized = false;
    
    // API-URL für AJAX-Requests (rex_api_function)
    var apiUrl = '/redaxo/index.php?rex-api-call=content_builder';

    var ContentBuilder = {
        
        // Getter für die API-URL (für AJAX-Requests)
        getAjaxUrl: function() {
            return apiUrl;
        },
        
        init: function() {
            // Events nur einmal binden (bei erstem init)
            if (!eventsInitialized) {
                this.bindEvents();
                eventsInitialized = true;
            }
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
            
            // Move Up Button
            $(document).on('click', '.btn-slice-move-up', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $slice = $(this).closest('.content-builder-slice');
                self.moveSliceUp($slice);
                return false;
            });
            
            // Move Down Button
            $(document).on('click', '.btn-slice-move-down', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $slice = $(this).closest('.content-builder-slice');
                self.moveSliceDown($slice);
                return false;
            });
            
            // Old Move Button (deprecated)
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

            // Neues Slice hinzufügen (am Ende)
            $(document).on('click', '.btn-add-slice', function(e) {
                e.preventDefault();
                var elementType = $(this).data('element-type');
                var elementLabel = $(this).data('element-label');
                var $container = $(this).closest('.yform-content-builder').find('.content-builder-slices');
                self.addSlice($container, elementType, elementLabel);
            });
            
            // Slice an bestimmter Position einfügen
            $(document).on('click', '.btn-insert-slice', function(e) {
                e.preventDefault();
                var elementType = $(this).data('element-type');
                var elementLabel = $(this).data('element-label');
                var insertAfter = parseInt($(this).data('insert-after'));
                var $container = $(this).closest('.yform-content-builder').find('.content-builder-slices');
                self.insertSliceAt($container, elementType, elementLabel, insertAfter + 1);
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
            
            // Radio Image Selection Events
            $(document).on('change', '.radio-image-item input[type="radio"]', function() {
                var $container = $(this).closest('.radio-image-group');
                $container.find('.radio-image-item').removeClass('active');
                $(this).closest('.radio-image-item').addClass('active');
            });
            
            $(document).on('click', '.radio-image-item label', function(e) {
                var $item = $(this).closest('.radio-image-item');
                var $radio = $item.find('input[type="radio"]');
                if (!$radio.is(':checked')) {
                    $radio.prop('checked', true).trigger('change');
                }
            });
            
            // Color Swatches Selection Events
            $(document).on('change', '.color-swatch-item input[type="radio"]', function() {
                var $container = $(this).closest('.color-swatches-group');
                $container.find('.color-swatch-item').removeClass('active');
                $(this).closest('.color-swatch-item').addClass('active');
            });
            
            $(document).on('click', '.color-swatch-item label', function(e) {
                var $item = $(this).closest('.color-swatch-item');
                var $radio = $item.find('input[type="radio"]');
                if (!$radio.is(':checked')) {
                    $radio.prop('checked', true).trigger('change');
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
            
            // REDAXO Linkmap Widget Events (global für alle Kontexte: YForm + Struktur)
            $(document).on('click', '.rex-linkmap-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                var linkId = $(this).data('id');
                var params = $(this).data('params') || '';
                
                if (typeof openLinkMap === 'function') {
                    // REDAXO's openLinkMap Funktion aufrufen
                    // Format: openLinkMap('REX_LINK_1', '&clang=1&category_id=1')
                    openLinkMap(linkId, params);
                } else {
                    console.error('openLinkMap function not found');
                }
                
                return false;
            });
            
            $(document).on('click', '.rex-linkmap-delete-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                var linkId = $(this).data('id');
                
                if (typeof deleteREXLink === 'function') {
                    // REDAXO's deleteREXLink Funktion aufrufen
                    deleteREXLink(linkId);
                } else {
                    console.error('deleteREXLink function not found');
                }
                
                return false;
            });
            
            // Enhanced Media Platzhalter und Preview klickbar machen
            $(document).on('click', '.media-preview-enhanced', function(e) {
                // Nur wenn nicht auf Delete-Button geklickt wurde
                if ($(e.target).closest('.btn-delete-preview').length > 0) {
                    return;
                }
                
                e.preventDefault();
                var $preview = $(this);
                var counter = $preview.data('counter');
                var types = $preview.data('types');
                
                if (counter) {
                    var typesParam = types ? '&types=' + types : '';
                    if (typeof openREXMedia === 'function') {
                        if (typesParam) {
                            openREXMedia(counter, typesParam);
                        } else {
                            openREXMedia(counter);
                        }
                    }
                }
            });
            
            // Delete Button für Enhanced Media Preview
            $(document).on('click', '.btn-delete-preview', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                var $preview = $(this).closest('.media-preview-enhanced');
                var inputId = $preview.data('input-id');
                var $input = $('#' + inputId);
                
                // Input leeren
                $input.val('');
                
                // Preview zurücksetzen auf Placeholder
                var types = $preview.data('types') || '';
                var allowedTypes = types ? types.split(',') : [];
                
                var placeholderText = 'Klicken Sie hier, um ein Medium auszuwählen';
                var placeholderIcon = 'fa-cloud-upload';
                
                if (allowedTypes.includes('image') && allowedTypes.includes('video')) {
                    placeholderText = 'Klicken Sie hier, um ein Bild oder Video auszuwählen';
                } else if (allowedTypes.includes('image')) {
                    placeholderText = 'Klicken Sie hier, um ein Bild auszuwählen';
                } else if (allowedTypes.includes('video')) {
                    placeholderText = 'Klicken Sie hier, um ein Video auszuwählen';
                }
                
                $preview.html(
                    '<div class="media-placeholder">' +
                        '<i class="fa ' + placeholderIcon + '"></i>' +
                        '<p>' + placeholderText + '</p>' +
                    '</div>'
                );
            });
            
            // Watch for media input changes and update enhanced preview
            $(document).on('change input', 'input[id^="REX_MEDIA_"]', function() {
                var $input = $(this);
                var inputId = $input.attr('id');
                var $preview = $('#preview_' + inputId);
                
                // Nur Enhanced Preview aktualisieren
                if ($preview.length && $preview.hasClass('media-preview-enhanced')) {
                    self.updateEnhancedMediaPreview($input, $preview);
                }
            });
            
            // Polling für Enhanced Media Widgets (da openREXMedia nicht immer change-Event feuert)
            setInterval(function() {
                $('.media-enhanced-clickable').each(function() {
                    var $container = $(this);
                    var inputId = $container.data('input-id');
                    
                    if (inputId) {
                        var $input = $('#' + inputId);
                        
                        if ($input.length) {
                            var currentValue = $input.val();
                            var lastValue = $input.data('last-value') || '';
                            
                            if (currentValue !== lastValue) {
                                $input.data('last-value', currentValue);
                                self.updateEnhancedMediaWidget($container, $input);
                            }
                        }
                    }
                });
            }, 500);
            
            // Content Builder Media - Eigene Preview-Logik
            $(document).on('change', '.content-builder-media-input', function() {
                var $input = $(this);
                var inputId = $input.attr('id');
                var $preview = $('.content-builder-media-preview[data-input-id="' + inputId + '"]');
                var mediaFile = $input.val();
                
                if (mediaFile) {
                    // Update Preview
                    self.updateContentBuilderMediaPreview($preview, mediaFile);
                } else {
                    // Clear Preview
                    $preview.empty();
                }
            });
            
            // Delete Button für Content Builder Media
            $(document).on('click', '.btn-delete-cb-media', function(e) {
                e.preventDefault();
                var inputId = $(this).data('input-id');
                var $input = $('#' + inputId);
                var $preview = $('.content-builder-media-preview[data-input-id="' + inputId + '"]');
                
                $input.val('');
                $preview.empty();
                
                // Trigger change event
                $input.trigger('change');
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
                url: ContentBuilder.getAjaxUrl(),
                method: 'POST',
                data: {
                    action: 'load_slice_form',
                    slice_type: sliceType,
                    slice_data: sliceData
                },
                dataType: 'html',
                success: function(response) {
                    // Script-Tags aus Response entfernen, um doppelte Variablen zu vermeiden
                    // (CKEditor, MBlock etc. sind bereits auf der Seite geladen)
                    var $response = $('<div>').html(response);
                    $response.find('script').remove();
                    var cleanedHtml = $response.html();
                    
                    $editForm.html(cleanedHtml);
                    
                    // Bootstrap Selectpicker initialisieren (für AJAX-geladene Inhalte)
                    // sanitize: false damit SVG/img src nicht entfernt wird
                    $editForm.find('.selectpicker').selectpicker({
                        sanitize: false
                    });
                    
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
            
            // Sammle alle Modal-IDs die zu diesem Slice gehören
            var $allInputs = $editForm.find('input, textarea, select');
            
            // Auch Inputs in zugehörigen Modals sammeln (Bootstrap verschiebt Modals nach body)
            // Sowohl Settings-Modals als auch Repeater-Item-Modals
            $editForm.find('[data-toggle="modal"]').each(function() {
                var modalId = $(this).attr('data-target');
                if (modalId) {
                    var $modal = $(modalId);
                    if ($modal.length) {
                        $allInputs = $allInputs.add($modal.find('input, textarea, select'));
                    }
                }
            });
            
            // Auch Modals in body suchen die zu diesem Slice gehören könnten
            // (Bootstrap verschiebt Modals nach body)
            $('body > .modal').each(function() {
                var $modal = $(this);
                var modalId = $modal.attr('id');
                // Prüfen ob dieses Modal zu unserem Slice gehört
                if (modalId && $editForm.find('[data-target="#' + modalId + '"]').length > 0) {
                    $allInputs = $allInputs.add($modal.find('input, textarea, select'));
                }
            });
            
            // Form-Daten sammeln - aus allen Input-Feldern
            $allInputs.each(function() {
                var $field = $(this);
                var name = $field.attr('name');
                var value = $field.val();
                
                // Radio-Buttons: Nur gecheckte übernehmen
                if ($field.is(':radio')) {
                    if ($field.is(':checked') && name) {
                        self.setNestedValue(sliceData, name, value);
                    }
                    return; // continue - nicht-gecheckte Radios überspringen
                }
                
                // Checkboxen: Nur gecheckte übernehmen
                if ($field.is(':checkbox')) {
                    if ($field.is(':checked') && name) {
                        self.setNestedValue(sliceData, name, value || '1');
                    }
                    return; // continue
                }
                
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
            
            // Zur gespeicherten Slice scrollen und Glow-Effekt
            this.scrollToSlice($slice);
            this.glowEffect($slice);
        },
        
        /**
         * Glow-Effekt für visuelles Feedback
         */
        glowEffect: function($slice) {
            $slice.css({
                'box-shadow': '0 0 0 3px rgba(40, 167, 69, 0.6), 0 0 20px rgba(40, 167, 69, 0.4)',
                'transition': 'box-shadow 0.3s ease-in-out'
            });
            
            setTimeout(function() {
                $slice.css({
                    'box-shadow': '0 0 0 6px rgba(40, 167, 69, 0.8), 0 0 30px rgba(40, 167, 69, 0.5)'
                });
            }, 150);
            
            setTimeout(function() {
                $slice.css({
                    'box-shadow': ''
                });
            }, 1200);
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
                url: ContentBuilder.getAjaxUrl(),
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
                    self.updateInsertButtons();
                });
            });
            
            // Modal nach Schließen aufräumen
            $modal.on('hidden.bs.modal', function() {
                $(this).remove();
            });
            
            // Modal anzeigen
            $modal.modal('show');
        },
        
        moveSliceUp: function($slice) {
            var $prev = $slice.prev('.content-builder-slice');
            if ($prev.length) {
                $slice.insertBefore($prev);
                this.updateIndices();
                this.updateHiddenField();
                this.updateSectionClasses();
                this.updateInsertButtons();
                
                // Kurzes Highlight
                $slice.css('background', '#d9edf7');
                setTimeout(function() {
                    $slice.css('background', '');
                }, 300);
            }
        },
        
        moveSliceDown: function($slice) {
            var $next = $slice.next('.content-builder-slice');
            if ($next.length) {
                $slice.insertAfter($next);
                this.updateIndices();
                this.updateHiddenField();
                this.updateSectionClasses();
                this.updateInsertButtons();
                
                // Kurzes Highlight
                $slice.css('background', '#d9edf7');
                setTimeout(function() {
                    $slice.css('background', '');
                }, 300);
            }
        },

        addSlice: function($container, elementType, elementLabel) {
            var sliceId = 'slice_' + Date.now();
            var index = $container.children('.content-builder-slice').length;
            
            // Section-Element?
            var isSectionClass = (elementType === 'section') ? ' is-section' : '';
            
            var $newSlice = $('<div class="content-builder-slice' + isSectionClass + '" data-slice-id="' + sliceId + '" data-slice-type="' + elementType + '" data-slice-index="' + index + '">' +
                '<div class="slice-toolbar">' +
                    '<button type="button" class="btn btn-xs btn-default btn-slice-edit" title="Bearbeiten"><i class="fa fa-pencil"></i></button>' +
                    '<button type="button" class="btn btn-xs btn-default btn-slice-move-up" title="Nach oben"><i class="fa fa-arrow-up"></i></button>' +
                    '<button type="button" class="btn btn-xs btn-default btn-slice-move-down" title="Nach unten"><i class="fa fa-arrow-down"></i></button>' +
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
            this.updateInsertButtons();
            
            this.scrollToSlice($newSlice);
        },
        
        insertSliceAt: function($container, elementType, elementLabel, position) {
            var sliceId = 'slice_' + Date.now();
            
            // Section-Element?
            var isSectionClass = (elementType === 'section') ? ' is-section' : '';
            
            var $newSlice = $('<div class="content-builder-slice' + isSectionClass + '" data-slice-id="' + sliceId + '" data-slice-type="' + elementType + '" data-slice-index="' + position + '">' +
                '<div class="slice-toolbar">' +
                    '<button type="button" class="btn btn-xs btn-default btn-slice-edit" title="Bearbeiten"><i class="fa fa-pencil"></i></button>' +
                    '<button type="button" class="btn btn-xs btn-default btn-slice-move-up" title="Nach oben"><i class="fa fa-arrow-up"></i></button>' +
                    '<button type="button" class="btn btn-xs btn-default btn-slice-move-down" title="Nach unten"><i class="fa fa-arrow-down"></i></button>' +
                    '<button type="button" class="btn btn-xs btn-danger btn-slice-delete" title="Löschen"><i class="fa fa-trash"></i></button>' +
                '</div>' +
                '<div class="slice-rendered"><div class="alert alert-info">Neues Element: ' + elementLabel + ' - Klicken zum Bearbeiten</div></div>' +
                '<div class="slice-edit-form" style="display: none;"></div>' +
            '</div>');
            
            var $slices = $container.children('.content-builder-slice');
            
            if (position >= $slices.length) {
                // Am Ende einfügen
                $container.append($newSlice);
            } else {
                // An Position einfügen
                $newSlice.insertBefore($slices.eq(position));
            }
            
            // Sofort zum Bearbeiten öffnen
            this.editSlice($newSlice);
            
            this.updateIndices();
            this.updateSectionClasses();
            this.updateInsertButtons();
            
            this.scrollToSlice($newSlice);
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
                // Update insert-after indices for insert buttons in toolbar
                $(this).find('.btn-insert-slice').attr('data-insert-after', index);
            });
        },
        
        updateInsertButtons: function() {
            var self = this;
            
            $('.yform-content-builder').each(function() {
                var $builder = $(this);
                var availableElements = $builder.data('available-elements');
                
                if (!availableElements) {
                    return;
                }
                
                // Remove old insert-between buttons (cleanup if any exist)
                $builder.find('.content-builder-insert-between').remove();
                
                $builder.find('.content-builder-slice').each(function(index) {
                    var $slice = $(this);
                    var $toolbar = $slice.find('.slice-toolbar');
                    
                    // Check if insert button group already exists
                    var $insertGroup = $toolbar.find('.btn-group-insert');
                    
                    if ($insertGroup.length === 0) {
                        // Create new button group
                        $insertGroup = self.createInsertButton(availableElements, index);
                        $toolbar.prepend($insertGroup);
                    } else {
                        // Update index
                        $insertGroup.find('.btn-insert-slice').attr('data-insert-after', index);
                    }
                });
            });
        },
        
        createInsertButton: function(availableElements, insertAfter) {
            var dropdownItems = '';
            
            for (var elementType in availableElements) {
                if (availableElements.hasOwnProperty(elementType)) {
                    var config = availableElements[elementType];
                    var label = config.label || elementType;
                    var icon = config.icon || 'fa-cube';
                    
                    dropdownItems += '<li>' +
                        '<a href="#" class="btn-insert-slice" ' +
                        'data-element-type="' + elementType + '" ' +
                        'data-element-label="' + label + '" ' +
                        'data-insert-after="' + insertAfter + '">' +
                        '<i class="fa ' + icon + '"></i> ' + label +
                        '</a>' +
                        '</li>';
                }
            }
            
            var html = '<div class="btn-group btn-group-insert">' +
                '<button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" title="Element einfügen">' +
                '<i class="fa fa-plus"></i>' +
                '</button>' +
                '<ul class="dropdown-menu pull-right">' + dropdownItems + '</ul>' +
                '</div>';
            
            return $(html);
        },

        scrollToSlice: function($slice) {
            var offset = $slice.offset().top;
            // Ein bisschen Abstand nach oben lassen (z.B. für Fixed Header)
            offset = offset - 100;
            
            $('html, body').animate({
                scrollTop: offset
            }, 500);
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
                
                // ALLE Modals aktualisieren (es können mehrere sein: media_modal, item_modal, etc.)
                $newItem.find('.modal').each(function() {
                    var $modal = $(this);
                    var oldModalId = $modal.attr('id');
                    
                    // Modalname extrahieren (z.B. "_item_modal", "_media_modal")
                    var modalSuffix = oldModalId ? oldModalId.replace(/^repeater_item_[^_]+/, '') : '_modal';
                    var newModalId = newItemId + modalSuffix;
                    
                    $modal.attr('id', newModalId);
                    
                    // Den entsprechenden Button finden und aktualisieren
                    // Button sollte im gleichen Item sein (nicht in verschachtelten Items!)
                    $newItem.children().find('[data-target="#' + oldModalId + '"]').attr('data-target', '#' + newModalId);
                });
                
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
                        if (oldId && (oldId.indexOf('media_') === 0 || oldId.indexOf('REX_MEDIA_') === 0 || oldId.indexOf('REX_LINK_') === 0)) {
                            var newId;
                            
                            // Für REX_MEDIA_ müssen wir den Counter hochzählen
                            if (oldId.indexOf('REX_MEDIA_') === 0) {
                                // Globalen Counter verwenden oder hochzählen
                                if (typeof window.rexMediaCounter === 'undefined') {
                                    // Counter aus dem höchsten vorhandenen REX_MEDIA_X ermitteln
                                    var maxCounter = 0;
                                    $('input[id^="REX_MEDIA_"]').each(function() {
                                        var match = $(this).attr('id').match(/REX_MEDIA_(\d+)/);
                                        if (match) {
                                            maxCounter = Math.max(maxCounter, parseInt(match[1]));
                                        }
                                    });
                                    window.rexMediaCounter = maxCounter;
                                }
                                window.rexMediaCounter++;
                                newId = 'REX_MEDIA_' + window.rexMediaCounter;
                                
                                // onclick-Attribute für openREXMedia aktualisieren
                                $newItem.find('a[onclick*="openREXMedia"]').each(function() {
                                    var $link = $(this);
                                    var oldOnclick = $link.attr('onclick');
                                    if (oldOnclick) {
                                        var oldCounter = oldId.replace('REX_MEDIA_', '');
                                        var newOnclick = oldOnclick.replace(
                                            'openREXMedia(' + oldCounter,
                                            'openREXMedia(' + window.rexMediaCounter
                                        );
                                        $link.attr('onclick', newOnclick);
                                    }
                                });
                                
                                // deleteREXMedia und viewREXMedia auch updaten
                                $newItem.find('a[onclick*="deleteREXMedia"],a[onclick*="viewREXMedia"]').each(function() {
                                    var $link = $(this);
                                    var oldOnclick = $link.attr('onclick');
                                    if (oldOnclick) {
                                        var oldCounter = oldId.replace('REX_MEDIA_', '');
                                        var newOnclick = oldOnclick.replace(
                                            new RegExp('(deleteREXMedia|viewREXMedia)\\(' + oldCounter),
                                            '$1(' + window.rexMediaCounter
                                        );
                                        $link.attr('onclick', newOnclick);
                                    }
                                });
                            } else {
                                // Für media_ und REX_LINK_: Timestamp + Index
                                newId = oldId.replace(/_\w+$/, '_' + Date.now() + '_' + newIndex);
                            }
                            
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
                                $preview.attr('data-input-id', newId);
                                
                                // Für be_media_enhanced: Counter auch aktualisieren
                                if ($preview.hasClass('media-preview-enhanced')) {
                                    if (oldId.indexOf('REX_MEDIA_') === 0) {
                                        var newCounter = window.rexMediaCounter;
                                        $preview.attr('data-counter', newCounter);
                                    }
                                }
                                
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
                            // Radio-Buttons: Neue eindeutige ID generieren und Label aktualisieren
                            if ($input.is(':radio') && oldId) {
                                var newRadioId = 'radio_' + Math.random().toString(16).slice(2);
                                $input.attr('id', newRadioId);
                                // Zugehöriges Label finden und for-Attribut aktualisieren
                                $newItem.find('label[for="' + oldId + '"]').attr('for', newRadioId);
                            }
                            // Default-Wert setzen (erstes Element als checked)
                            var $radioGroup = $input.closest('.radio-image-group, .color-swatches-group');
                            if ($radioGroup.length > 0) {
                                var $firstRadio = $radioGroup.find('input[type="radio"]:first');
                                if ($input.is($firstRadio)) {
                                    $input.prop('checked', true);
                                    $input.closest('.radio-image-item, .color-swatch-item').addClass('active');
                                } else {
                                    $input.prop('checked', false);
                                    $input.closest('.radio-image-item, .color-swatch-item').removeClass('active');
                                }
                            } else {
                                $input.prop('checked', false);
                            }
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
                        if (oldId && (oldId.indexOf('media_') === 0 || oldId.indexOf('REX_MEDIA_') === 0 || oldId.indexOf('REX_LINK_') === 0)) {
                            var newId;
                            
                            // Für REX_MEDIA_ müssen wir den Counter hochzählen
                            if (oldId.indexOf('REX_MEDIA_') === 0) {
                                // Globalen Counter verwenden oder hochzählen
                                if (typeof window.rexMediaCounter === 'undefined') {
                                    // Counter aus dem höchsten vorhandenen REX_MEDIA_X ermitteln
                                    var maxCounter = 0;
                                    $('input[id^="REX_MEDIA_"]').each(function() {
                                        var match = $(this).attr('id').match(/REX_MEDIA_(\d+)/);
                                        if (match) {
                                            maxCounter = Math.max(maxCounter, parseInt(match[1]));
                                        }
                                    });
                                    window.rexMediaCounter = maxCounter;
                                }
                                window.rexMediaCounter++;
                                newId = 'REX_MEDIA_' + window.rexMediaCounter;
                                
                                // onclick-Attribute für openREXMedia aktualisieren
                                $newItem.find('a[onclick*="openREXMedia"]').each(function() {
                                    var $link = $(this);
                                    var oldOnclick = $link.attr('onclick');
                                    if (oldOnclick) {
                                        var oldCounter = oldId.replace('REX_MEDIA_', '');
                                        var newOnclick = oldOnclick.replace(
                                            'openREXMedia(' + oldCounter,
                                            'openREXMedia(' + window.rexMediaCounter
                                        );
                                        $link.attr('onclick', newOnclick);
                                    }
                                });
                                
                                // deleteREXMedia und viewREXMedia auch updaten
                                $newItem.find('a[onclick*="deleteREXMedia"],a[onclick*="viewREXMedia"]').each(function() {
                                    var $link = $(this);
                                    var oldOnclick = $link.attr('onclick');
                                    if (oldOnclick) {
                                        var oldCounter = oldId.replace('REX_MEDIA_', '');
                                        var newOnclick = oldOnclick.replace(
                                            new RegExp('(deleteREXMedia|viewREXMedia)\\(' + oldCounter),
                                            '$1(' + window.rexMediaCounter
                                        );
                                        $link.attr('onclick', newOnclick);
                                    }
                                });
                            } else {
                                // Für media_ und REX_LINK_: Timestamp + Index
                                newId = oldId.replace(/_\w+$/, '_' + Date.now() + '_' + newIndex);
                            }
                            
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
            
            // Enhanced Media Previews zurücksetzen
            $newItem.find('.media-preview-enhanced').each(function() {
                var $preview = $(this);
                var types = $preview.attr('data-types') || '';
                var allowedTypes = types ? types.split(',') : ['image', 'video'];
                self.resetEnhancedPreview($preview, allowedTypes);
            });
            
            // Initial last-value für Polling setzen
            $newItem.find('input[id^="REX_MEDIA_"]').each(function() {
                $(this).data('last-value', $(this).val() || '');
            });
            
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
         * Update Enhanced Media Preview when media is selected
         */
        updateEnhancedMediaPreview: function($input, $preview) {
            var filename = $input.val();
            
            if (!filename) {
                // Kein Medium: Placeholder anzeigen
                var allowedTypes = $input.data('allowed-types') || [];
                this.resetEnhancedPreview($preview, allowedTypes);
                return;
            }
            
            // Dateityp bestimmen
            var ext = filename.split('.').pop().toLowerCase();
            var imageExts = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'bmp'];
            var videoExts = ['mp4', 'webm', 'ogv', 'mov'];
            
            var html = '';
            
            if (imageExts.indexOf(ext) !== -1) {
                // Bild-Preview
                var mediaUrl = '';
                if (typeof rex !== 'undefined' && rex.media_manager_url) {
                    mediaUrl = rex.media_manager_url + 'rex_mediapool_preview/' + encodeURIComponent(filename);
                } else {
                    mediaUrl = '../media/' + encodeURIComponent(filename);
                }
                
                html = '<div class="media-item media-item-image">' +
                       '<img src="' + mediaUrl + '" alt="' + filename + '" />' +
                       '</div>';
                       
            } else if (videoExts.indexOf(ext) !== -1) {
                // Video-Preview
                var mediaUrl = '../media/' + encodeURIComponent(filename);
                html = '<div class="media-item media-item-video" style="position: relative; padding-bottom: 56.25%; height: 0;">' +
                       '<video preload="metadata" muted style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">' +
                       '<source src="' + mediaUrl + '" type="video/' + ext + '" />' +
                       '</video>' +
                       '<div class="media-overlay"><i class="fa fa-play-circle"></i></div>' +
                       '</div>';
                       
            } else {
                // Unbekannter Dateityp
                html = '<div class="media-placeholder">' +
                       '<i class="fa fa-file"></i>' +
                       '<p>' + filename + '</p>' +
                       '</div>';
            }
            
            $preview.html(html).show();
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
        },
        
        /**
         * Update Content Builder Media Preview
         */
        updateContentBuilderMediaPreview: function($preview, mediaFile) {
            var ext = mediaFile.split('.').pop().toLowerCase();
            var isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'].indexOf(ext) !== -1;
            var isVideo = ['mp4', 'webm', 'ogg', 'mov'].indexOf(ext) !== -1;
            
            var html = '';
            
            if (isImage) {
                var mediaUrl = '/media/' + mediaFile;
                // Versuche Media Manager URL wenn verfügbar
                if (typeof rex !== 'undefined' && rex.media_manager) {
                    mediaUrl = rex.media_manager.getUrl('yform_content_builder_preview', mediaFile);
                }
                html = '<div class="cb-media-preview-item">';
                html += '<div class="cb-media-container">';
                html += '<img src="' + mediaUrl + '" alt="' + mediaFile + '" />';
                html += '</div>';
                html += '<span class="cb-media-filename">' + mediaFile + '</span>';
                html += '</div>';
            } else if (isVideo) {
                var mediaUrl = '/media/' + mediaFile;
                html = '<div class="cb-media-preview-item cb-media-video">';
                html += '<div class="cb-media-container">';
                html += '<video controls preload="metadata">';
                html += '<source src="' + mediaUrl + '" />';
                html += '</video>';
                html += '</div>';
                html += '<span class="cb-media-filename">' + mediaFile + '</span>';
                html += '</div>';
            } else {
                html = '<div class="cb-media-preview-item cb-media-file">';
                html += '<i class="fa fa-file"></i>';
                html += '<span class="cb-media-filename">' + mediaFile + '</span>';
                html += '</div>';
            }
            
            $preview.html(html);
        },
        
        /**
         * Initialisiere Enhanced Media Widgets
         */
        initEnhancedMediaWidgets: function() {
            // Setze last-value für alle enhanced media inputs
            $('.media-enhanced-clickable').each(function() {
                var $container = $(this);
                var inputId = $container.data('input-id');
                
                if (inputId) {
                    var $input = $('#' + inputId);
                    
                    if ($input.length) {
                        $input.data('last-value', $input.val() || '');
                    }
                }
            });
        },
        
        /**
         * Update Enhanced Media Widget nach Medienauswahl
         */
        updateEnhancedMediaWidget: function($container, $input) {
            var mediaValue = $input.val();
            var types = $input.data('types') || '';
            
            if (mediaValue) {
                // Medium wurde ausgewählt - Preview anzeigen
                var self = this;
                $.ajax({
                    url: ContentBuilder.getAjaxUrl(),
                    type: 'POST',
                    data: {
                        ajax_action: 'get_media_preview',
                        media_file: mediaValue,
                        types: types
                    },
                    success: function(response) {
                        if (response.success && response.html) {
                            $container.html(response.html);
                        } else {
                            // Fallback: Einfache Preview
                            self.createSimpleMediaPreview($container, mediaValue, types);
                        }
                    },
                    error: function() {
                        // Fallback: Einfache Preview
                        self.createSimpleMediaPreview($container, mediaValue, types);
                    }
                });
            } else {
                // Kein Medium - Placeholder anzeigen
                this.showMediaPlaceholder($container, types);
            }
        },
        
        /**
         * Erstelle einfache Media Preview (Fallback)
         */
        createSimpleMediaPreview: function($container, mediaFile, types) {
            var ext = mediaFile.split('.').pop().toLowerCase();
            var isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'].indexOf(ext) !== -1;
            var isVideo = ['mp4', 'webm', 'ogg', 'mov'].indexOf(ext) !== -1;
            
            var html = '<div class="media-enhanced-preview">';
            
            if (isImage) {
                var mediaUrl = '/media/' + mediaFile;
                html += '<img src="' + mediaUrl + '" alt="' + mediaFile + '" />';
            } else if (isVideo) {
                var mediaUrl = '/media/' + mediaFile;
                html += '<video controls><source src="' + mediaUrl + '" /></video>';
            } else {
                html += '<div class="media-enhanced-file">';
                html += '<i class="fa fa-file"></i>';
                html += '<span>' + mediaFile + '</span>';
                html += '</div>';
            }
            
            html += '</div>';
            $container.html(html);
        },
        
        /**
         * Zeige Media Placeholder
         */
        showMediaPlaceholder: function($container, types) {
            var placeholderIcon = 'fa-cloud-upload';
            var placeholderText = 'Medium auswählen';
            
            if (types) {
                var typeArray = types.split(',');
                if (typeArray.indexOf('image') !== -1 && typeArray.indexOf('video') !== -1) {
                    placeholderIcon = 'fa-file-image-o';
                    placeholderText = 'Bild oder Video auswählen';
                } else if (typeArray.indexOf('image') !== -1) {
                    placeholderIcon = 'fa-image';
                    placeholderText = 'Bild auswählen';
                } else if (typeArray.indexOf('video') !== -1) {
                    placeholderIcon = 'fa-video-camera';
                    placeholderText = 'Video auswählen';
                }
            }
            
            var html = '<div class="media-placeholder-box">';
            html += '<i class="fa ' + placeholderIcon + '"></i>';
            html += '<p>' + placeholderText + '</p>';
            html += '</div>';
            
            $container.html(html);
        }
    };

    // Initialisierungsfunktion
    function initContentBuilder() {
        ContentBuilder.init();
        
        // Media Browser initialisieren, falls vorhanden
        if (window.MediaBrowser) {
            window.MediaBrowser.init();
        } else if (window.ContentBuilderMediaBrowser) {
            window.MediaBrowser = window.ContentBuilderMediaBrowser;
            window.MediaBrowser.init();
        }
    }

    // Bei Document Ready
    $(document).ready(function() {
        initContentBuilder();
    });
    
    // Bei PJAX-Navigation (rex:ready wird nach PJAX-Load gefeuert)
    $(document).on('rex:ready', function(event, container) {
        // Nur initialisieren, wenn Content Builder im geladenen Container vorhanden ist
        if ($(container).find('.yform-content-builder').length > 0 || 
            $(container).is('.yform-content-builder')) {
            initContentBuilder();
        }
    });

})(jQuery);
