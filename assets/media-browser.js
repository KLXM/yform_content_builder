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
            
            // Overlay erstellen wenn nicht vorhanden
            if ($('#media-browser-overlay').length === 0) {
                this.createOverlay();
            }
            
            // Media laden
            this.loadMedia();
            
            // Overlay anzeigen
            $('#media-browser-overlay').fadeIn(200);
            $('body').css('overflow', 'hidden');
        },

        close: function() {
            $('#media-browser-overlay').fadeOut(200);
            $('body').css('overflow', '');
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
            $grid.empty();
            
            if (mediaList.length === 0) {
                $grid.html('<div class="media-browser-empty">Keine Medien gefunden</div>');
                return;
            }
            
            mediaList.forEach(function(media) {
                var isImage = /\.(jpg|jpeg|png|gif|svg|webp)$/i.test(media.filename);
                var thumb = isImage 
                    ? '<img src="/media/' + media.filename + '" alt="' + media.filename + '">'
                    : '<i class="fa fa-file-o fa-3x"></i>';
                
                var html = `
                    <div class="media-browser-item" data-filename="${media.filename}">
                        <div class="media-browser-thumb">${thumb}</div>
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
            var $preview = $input.closest('.input-group').next('.media-preview');
            
            if ($preview.length === 0) {
                $preview = $('<div class="media-preview" style="margin-top: 10px;"></div>');
                $input.closest('.input-group').after($preview);
            }
            
            if (value) {
                var isImage = /\.(jpg|jpeg|png|gif|svg|webp)$/i.test(value);
                if (isImage) {
                    $preview.html('<img src="/media/' + value + '" alt="' + value + '" style="max-width: 100%; max-height: 150px; border: 1px solid #ddd; border-radius: 3px; padding: 5px;">');
                } else {
                    $preview.html('<i class="fa fa-file-o"></i> ' + value);
                }
            } else {
                $preview.empty();
            }
        }
    };

    $(document).ready(function() {
        MediaBrowser.init();
    });

    window.ContentBuilderMediaBrowser = MediaBrowser;

})(jQuery);
