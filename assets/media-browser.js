/**
 * YForm Content Builder - Media Browser
 * Eigener Medienpool-Browser als Overlay
 */

(function($) {
    'use strict';

    var MediaBrowser = {
        
        currentInputId: null,
        
        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            var self = this;

            // Media-Button Click
            $(document).on('click', '.btn-select-media', function(e) {
                e.preventDefault();
                var inputId = $(this).data('input-id');
                self.open(inputId);
            });

            // Media löschen
            $(document).on('click', '.btn-delete-media', function(e) {
                e.preventDefault();
                var inputId = $(this).data('input-id');
                $('#' + inputId).val('');
                self.updatePreview(inputId);
            });

            // Media im Browser auswählen
            $(document).on('click', '.media-browser-item', function(e) {
                e.preventDefault();
                var filename = $(this).data('filename');
                self.selectMedia(filename);
            });

            // Browser schließen
            $(document).on('click', '.media-browser-close, .media-browser-overlay', function(e) {
                if (e.target === this) {
                    self.close();
                }
            });

            // Kategorie-Filter
            $(document).on('change', '.media-browser-category', function() {
                self.loadMedia($(this).val());
            });

            // Suche
            $(document).on('input', '.media-browser-search', function() {
                self.filterMedia($(this).val());
            });
        },

        open: function(inputId) {
            this.currentInputId = inputId;
            this.allowedTypes = ['image']; // Standard: nur Bilder
            
            // Overlay erstellen wenn nicht vorhanden
            if ($('#media-browser-overlay').length === 0) {
                this.createOverlay();
            }
            
            // Media laden
            this.loadMedia();
            
            // Aktuelle Scroll-Position speichern
            this.scrollTop = $(window).scrollTop();
            
            // Overlay anzeigen
            $('#media-browser-overlay').fadeIn(200);
            
            // Body overflow hidden setzen, aber Scroll-Position beibehalten
            $('body').addClass('media-browser-open').css({
                'overflow': 'hidden',
                'position': 'fixed',
                'top': -this.scrollTop + 'px',
                'width': '100%'
            });
        },
        
        openEnhanced: function(inputId, allowedTypes) {
            this.currentInputId = inputId;
            this.allowedTypes = Array.isArray(allowedTypes) ? allowedTypes : (allowedTypes ? allowedTypes.split(',') : ['image', 'video']);
            
            // Overlay erstellen wenn nicht vorhanden
            if ($('#media-browser-overlay').length === 0) {
                this.createOverlay();
            }
            
            // Media laden
            this.loadMedia();
            
            // Aktuelle Scroll-Position speichern
            this.scrollTop = $(window).scrollTop();
            
            // Overlay anzeigen
            $('#media-browser-overlay').fadeIn(200);
            
            // Body overflow hidden setzen, aber Scroll-Position beibehalten
            $('body').addClass('media-browser-open').css({
                'overflow': 'hidden',
                'position': 'fixed',
                'top': -this.scrollTop + 'px',
                'width': '100%'
            });
        },

        close: function() {
            $('#media-browser-overlay').fadeOut(200);
            
            // Body-Styles zurücksetzen und Scroll-Position wiederherstellen
            $('body').removeClass('media-browser-open').css({
                'overflow': '',
                'position': '',
                'top': '',
                'width': ''
            });
            
            // Scroll-Position wiederherstellen
            if (this.scrollTop) {
                $(window).scrollTop(this.scrollTop);
            }
            
            this.currentInputId = null;
        },

        createOverlay: function() {
            var html = `
                <div id="media-browser-overlay" class="media-browser-overlay" style="display: none;">
                    <div class="media-browser-dialog">
                        <div class="media-browser-header">
                            <h3><i class="fa fa-image"></i> Medium auswählen</h3>
                            <button type="button" class="media-browser-close">&times;</button>
                        </div>
                        <div class="media-browser-toolbar">
                            <div class="row">
                                <div class="col-sm-6">
                                    <select class="form-control media-browser-category">
                                        <option value="">Alle Kategorien</option>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control media-browser-search" placeholder="Suchen...">
                                </div>
                            </div>
                        </div>
                        <div class="media-browser-content">
                            <div class="media-browser-grid"></div>
                        </div>
                    </div>
                </div>
            `;
            
            $('body').append(html);
            
            // Kategorien laden
            this.loadCategories();
        },

        loadCategories: function() {
            $.ajax({
                url: window.location.href,
                method: 'POST',
                data: {
                    action: 'load_media_categories'
                },
                success: function(response) {
                    var data = typeof response === 'string' ? JSON.parse(response) : response;
                    if (data.categories) {
                        var $select = $('.media-browser-category');
                        data.categories.forEach(function(cat) {
                            $select.append('<option value="' + cat.id + '">' + cat.name + '</option>');
                        });
                    }
                }
            });
        },

        loadMedia: function(categoryId) {
            var self = this;
            
            $.ajax({
                url: window.location.href,
                method: 'POST',
                data: {
                    action: 'load_media_list',
                    category_id: categoryId || ''
                },
                success: function(response) {
                    var data = typeof response === 'string' ? JSON.parse(response) : response;
                    self.renderMediaGrid(data.media || []);
                }
            });
        },

        renderMediaGrid: function(mediaList) {
            var $grid = $('.media-browser-grid');
            var self = this;
            $grid.empty();
            
            if (mediaList.length === 0) {
                $grid.html('<div class="media-browser-empty">Keine Medien gefunden</div>');
                return;
            }
            
            mediaList.forEach(function(media) {
                var isImage = /\.(jpg|jpeg|png|gif|svg|webp)$/i.test(media.filename);
                var isVideo = /\.(mp4|webm|mov|avi|mkv|ogg)$/i.test(media.filename);
                
                // Typ-Filter anwenden
                var showItem = false;
                if (self.allowedTypes.includes('image') && isImage) {
                    showItem = true;
                } else if (self.allowedTypes.includes('video') && isVideo) {
                    showItem = true;
                } else if (!isImage && !isVideo && self.allowedTypes.includes('other')) {
                    showItem = true;
                }
                
                if (!showItem) {
                    return; // Skip dieses Item
                }
                
                var thumb = '';
                var typeIcon = '';
                
                if (isImage) {
                    thumb = '<img src="/media/' + media.filename + '" alt="' + media.filename + '">';
                    typeIcon = '<i class="fa fa-image media-type-icon"></i>';
                } else if (isVideo) {
                    thumb = '<div class="video-thumb"><i class="fa fa-play-circle fa-3x"></i></div>';
                    typeIcon = '<i class="fa fa-video-camera media-type-icon"></i>';
                } else {
                    thumb = '<i class="fa fa-file-o fa-3x"></i>';
                    typeIcon = '<i class="fa fa-file media-type-icon"></i>';
                }
                
                var html = `
                    <div class="media-browser-item" data-filename="${media.filename}">
                        <div class="media-browser-thumb">${thumb}${typeIcon}</div>
                        <div class="media-browser-name">${media.filename}</div>
                    </div>
                `;
                
                $grid.append(html);
            });
        },

        filterMedia: function(search) {
            search = search.toLowerCase();
            
            $('.media-browser-item').each(function() {
                var filename = $(this).data('filename').toLowerCase();
                if (filename.indexOf(search) !== -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        },

        selectMedia: function(filename) {
            if (this.currentInputId) {
                $('#' + this.currentInputId).val(filename);
                this.updatePreview(this.currentInputId);
            }
            this.close();
        },

        updatePreview: function(inputId) {
            var $input = $('#' + inputId);
            var value = $input.val();
            
            // Preview-Container finden - entweder über ID oder als nächstes Element
            var $preview = $('#preview_' + inputId);
            if ($preview.length === 0) {
                $preview = $input.closest('.input-group').next('.media-preview');
            }
            
            if ($preview.length === 0) {
                $preview = $('<div class="media-preview" id="preview_' + inputId + '" style="margin-top: 10px;"></div>');
                $input.closest('.input-group').after($preview);
            }
            
            // Prüfen ob es ein enhanced preview ist
            var isEnhanced = $preview.hasClass('media-preview-enhanced');
            
            if (value) {
                var isImage = /\.(jpg|jpeg|png|gif|svg|webp)$/i.test(value);
                var isVideo = /\.(mp4|webm|mov|avi|mkv|ogg)$/i.test(value);
                
                if (isEnhanced) {
                    // Enhanced Preview
                    if (isImage) {
                        $preview.html(
                            '<div class="media-item media-item-image">' +
                                '<img src="/media/' + value + '" alt="' + value + '" />' +
                                '<div class="media-info"><i class="fa fa-image"></i> Bild: ' + value + '</div>' +
                            '</div>'
                        );
                    } else if (isVideo) {
                        $preview.html(
                            '<div class="media-item media-item-video">' +
                                '<video preload="metadata" data-filename="' + value + '">' +
                                    '<source src="/media/' + value + '" />' +
                                '</video>' +
                                '<div class="media-overlay"><i class="fa fa-play-circle"></i></div>' +
                                '<div class="media-controls">' +
                                    '<button class="btn-video-play" title="Abspielen"><i class="fa fa-play"></i></button>' +
                                    '<button class="btn-video-mute" title="Stumm schalten"><i class="fa fa-volume-up"></i></button>' +
                                    '<button class="btn-video-fullscreen" title="Vollbild"><i class="fa fa-expand"></i></button>' +
                                '</div>' +
                                '<div class="media-info"><i class="fa fa-video-camera"></i> Video: ' + value + '</div>' +
                            '</div>'
                        );
                    } else {
                        $preview.html('<i class="fa fa-file-o"></i> ' + value);
                    }
                } else {
                    // Standard Preview
                    if (isImage) {
                        $preview.html('<img src="/media/' + value + '" alt="' + value + '" style="max-width: 100%; max-height: 150px; border: 1px solid #ddd; border-radius: 3px; padding: 5px;">');
                    } else {
                        $preview.html('<i class="fa fa-file-o"></i> ' + value);
                    }
                }
                $preview.show();
            } else {
                if (isEnhanced) {
                    // Enhanced Preview zurücksetzen
                    var allowedTypes = $input.data('allowed-types');
                    if (allowedTypes) {
                        var allowedTypesArray = allowedTypes.split(',');
                        this.resetEnhancedPreview($preview, allowedTypesArray);
                    } else {
                        $preview.empty().hide();
                    }
                } else {
                    $preview.empty().hide();
                }
            }
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
        }
    };

    $(document).ready(function() {
        MediaBrowser.init();
    });

    // Globale Referenz für Content Builder
    window.MediaBrowser = MediaBrowser;
    window.ContentBuilderMediaBrowser = MediaBrowser;

})(jQuery);
