/*
 * YForm Content Builder - Field Widgets
 * - SmartLink (combined target field)
 */
(function($) {
    'use strict';

    function detectLinkType(value) {
        var v = (value || '').trim();
        if (v === '') {
            return 'url';
        }
        if (/^mailto:/i.test(v)) {
            return 'mail';
        }
        if (/^tel:/i.test(v)) {
            return 'tel';
        }
        if (/^#/i.test(v)) {
            return 'url';
        }
        if (/^\d+$/.test(v)) {
            return 'intern';
        }
        if (/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(v)) {
            return 'mail';
        }
        if (/^\+?[0-9\s\-()\/]{5,}$/.test(v)) {
            return 'tel';
        }
        if (/^[a-z0-9_]+:\d+$/i.test(v)) {
            return 'yform';
        }
        if (/\.(jpg|jpeg|png|gif|webp|svg|pdf|mp4|webm|mov|avi)$/i.test(v)) {
            return 'media';
        }
        return 'url';
    }

    function normalizeTargetByType(type, value) {
        var v = (value || '').toString().trim();
        if (type === 'mail' && v !== '' && !/^mailto:/i.test(v)) {
            return 'mailto:' + v;
        }
        if (type === 'tel' && v !== '' && !/^tel:/i.test(v)) {
            return 'tel:' + v;
        }
        return v;
    }

    var labelCache = {};

    function displayTextForTarget(value, callback, preferredLabel) {
        var v = (value || '').toString().trim();
        var display = (preferredLabel || '').toString().trim();

        if (display === '') {
            display = v;
        }
        
        if (!callback) {
            return display;
        }

        labelCache[v] = display;
        callback(display);
    }

    function updateAutoLabel($row, label) {
        var $labelInput = $row.find('.cb-smart-link-label');
        var current = ($labelInput.val() || '').toString().trim();
        var autoLabel = ($labelInput.data('auto-label') || '').toString().trim();
        var nextLabel = (label || '').toString().trim();

        if ($labelInput.length === 0) {
            return;
        }

        if (current === '' || current === autoLabel) {
            $labelInput.val(nextLabel);
        }

        $labelInput.data('auto-label', nextLabel);
    }

    function applySelection($widget, $row, type, value, displayLabel, autoLabel) {
        var normalizedValue = (value || '').toString().trim();
        var display = displayTextForTarget(normalizedValue, null, displayLabel);

        $row.find('.cb-smart-link-target').val(normalizedValue);
        $row.find('.cb-smart-link-target-display').val(display);
        refreshSmartLinkSelect($row.find('.cb-smart-link-type'), type);

        updateAutoLabel($row, autoLabel || display);
        smartLinkToggleTypeUi($row);
        smartLinkSerialize($widget);
    }

    function renderSmartLinkPreview($row) {
        var type = $row.find('.cb-smart-link-type').val();
        var value = ($row.find('.cb-smart-link-target').val() || '').toString().trim();
        var $preview = $row.find('.cb-smart-link-preview');

        if (value === '') {
            $preview.text('');
            return;
        }

        var effectiveType = type === 'auto' ? detectLinkType(value) : type;
        if (effectiveType === 'media') {
            var safe = $('<div>').text(value).html();
            $preview.html('Media: <strong>' + safe + '</strong>');
            return;
        }

        $preview.text('Erkannt: ' + effectiveType + ' → ' + value);
    }

    function initSmartLinkSelects($scope) {
        if (!$scope || $scope.length === 0) {
            return;
        }

        $scope.find('.cb-smart-link-select').each(function() {
            $(this).removeAttr('tabindex');
        });
    }

    function refreshSmartLinkSelect($select, value) {
        if (!$select || $select.length === 0) {
            return;
        }

        $select.val(value);
    }

    function smartLinkSerialize($widget) {
        var multiple = $widget.data('multiple') == 1;
        var items = [];

        $widget.find('.cb-smart-link-row').each(function() {
            var $row = $(this);
            var type = ($row.find('.cb-smart-link-type').val() || 'auto').toString();
            var value = ($row.find('.cb-smart-link-target').val() || '').toString().trim();
            var label = ($row.find('.cb-smart-link-label').val() || '').toString().trim();
            var pdfjs = $row.find('.cb-smart-link-pdfjs').is(':checked');

            if (type === 'yform') {
                var yformVal = ($row.find('.cb-smart-link-yform').val() || '').toString().trim();
                if (yformVal !== '') {
                    value = yformVal;
                }
            }

            value = normalizeTargetByType(type, value);

            if (value === '') {
                return;
            }

            items.push({
                type: type,
                value: value,
                label: label,
                pdfjs: pdfjs
            });
        });

        if (!multiple && items.length > 1) {
            items = [items[0]];
        }

        $widget.find('.cb-smart-link-value').val(JSON.stringify({
            multiple: multiple,
            items: items
        }));
    }

    function smartLinkToggleTypeUi($row) {
        var type = $row.find('.cb-smart-link-type').val();
        var targetValue = ($row.find('.cb-smart-link-target').val() || '').toString();

        if (type === 'yform') {
            $row.find('.cb-smart-link-yform-wrap').show();
        } else {
            $row.find('.cb-smart-link-yform-wrap').hide();
        }

        if (type === 'media' || type === 'auto') {
            $row.find('.cb-smart-link-pdfjs').closest('label').show();
        } else {
            $row.find('.cb-smart-link-pdfjs').closest('label').hide();
            $row.find('.cb-smart-link-pdfjs').prop('checked', false);
        }

        // NOTE: DO NOT update display input here - let PHP render initial value
        // Only update when picker closes (via displayTextForTarget callback)

        renderSmartLinkPreview($row);
    }

    function bindCustomLinkButtons($widget) {
        $widget.on('click', '.cb-smart-link-target-wrap .intern_link', function(e) {
            e.preventDefault();
            var $row = $(this).closest('.cb-smart-link-row');
            var $wrap = $row.find('.cb-smart-link-target-wrap');
            var widgetId = ($wrap.data('link-widget-id') || '').toString();
            if (widgetId === '' || typeof openLinkMap !== 'function') {
                return;
            }

            var $group = $row.find('.cb-smart-link-target-wrap');
            var clang = ($group.data('clang') || '').toString();
            var category = ($group.data('category') || '').toString();
            var args = '';
            if (clang !== '') {
                args += '&clang=' + clang;
            }
            if (category !== '') {
                args += '&category_id=' + category;
            }

            var linkMap = openLinkMap('REX_LINK_' + widgetId, args);
            if (linkMap && typeof $ === 'function') {
                $(linkMap).off('rex:selectLink.cbSmartLink').on('rex:selectLink.cbSmartLink', function(event, link, name) {
                    event.preventDefault();
                    if (typeof linkMap.close === 'function') {
                        linkMap.close();
                    }

                    applySelection(
                        $widget,
                        $row,
                        'intern',
                        (link || '').toString().replace(/^redaxo:\/\//, ''),
                        (name || '').toString().trim(),
                        (name || '').toString().trim()
                    );
                });
            }
        });

        $widget.on('click', '.cb-smart-link-target-wrap .media_link', function(e) {
            e.preventDefault();
            var $row = $(this).closest('.cb-smart-link-row');
            var $wrap = $row.find('.cb-smart-link-target-wrap');
            var widgetId = ($wrap.data('link-widget-id') || '').toString();
            if (widgetId === '' || typeof openREXMedia !== 'function') {
                return;
            }

            var $target = $row.find('.cb-smart-link-target');
            var originalId = $target.attr('id');
            $target.attr('id', 'REX_MEDIA_' + widgetId);

            var mediaMap = openREXMedia(widgetId, '');
            if (mediaMap && typeof $ === 'function') {
                $(mediaMap).off('rex:selectMedia.cbSmartLink').on('rex:selectMedia.cbSmartLink', function(event, mediaName) {
                    event.preventDefault();
                    if (typeof mediaMap.close === 'function') {
                        mediaMap.close();
                    }

                    $target.attr('id', originalId);
                    applySelection(
                        $widget,
                        $row,
                        'media',
                        mediaName,
                        mediaName,
                        mediaName
                    );
                });
            }
        });

        $widget.on('click', '.cb-smart-link-target-wrap .external_link', function(e) {
            e.preventDefault();
            var $row = $(this).closest('.cb-smart-link-row');
            var $input = $row.find('.cb-smart-link-target');
            var current = ($input.val() || '').toString();
            if (current === '' || current.indexOf('http') !== 0) {
                current = 'https://';
            }
            var val = window.prompt('Link', current);
            if (val !== null) {
                applySelection($widget, $row, 'url', val, val, val);
            }
        });

        $widget.on('click', '.cb-smart-link-target-wrap .email_link', function(e) {
            e.preventDefault();
            var $row = $(this).closest('.cb-smart-link-row');
            var $input = $row.find('.cb-smart-link-target');
            var current = ($input.val() || '').toString();
            if (current === '' || !/^mailto:/i.test(current)) {
                current = 'mailto:';
            }
            var val = window.prompt('Mail', current);
            if (val !== null) {
                applySelection($widget, $row, 'mail', val, val, val);
            }
        });

        $widget.on('click', '.cb-smart-link-target-wrap .phone_link', function(e) {
            e.preventDefault();
            var $row = $(this).closest('.cb-smart-link-row');
            var $input = $row.find('.cb-smart-link-target');
            var current = ($input.val() || '').toString();
            if (current === '' || !/^tel:/i.test(current)) {
                current = 'tel:';
            }
            var val = window.prompt('Telefon', current);
            if (val !== null) {
                applySelection($widget, $row, 'tel', val, val, val);
            }
        });

        $widget.on('click', '.cb-smart-link-target-wrap .delete_link', function(e) {
            e.preventDefault();
            var $row = $(this).closest('.cb-smart-link-row');
            $row.find('.cb-smart-link-target').val('');
            var $nameInput = $row.find('.cb-smart-link-target-display');
            if ($nameInput.length) {
                $nameInput.val('');
            }
            updateAutoLabel($row, '');
            smartLinkToggleTypeUi($row);
            smartLinkSerialize($widget);
        });
    }

    function startSmartLinkSync($widget) {
        if (!$widget.data('smartlink-sync')) {
            $widget.data('smartlink-sync', 1);
        }
    }

    function initSmartLinkWidget($widget) {
        if ($widget.data('smartlink-init')) {
            return;
        }
        $widget.data('smartlink-init', 1);

        bindCustomLinkButtons($widget);

        $widget.on('change input', '.cb-smart-link-type, .cb-smart-link-target, .cb-smart-link-label, .cb-smart-link-pdfjs, .cb-smart-link-yform', function() {
            var $row = $(this).closest('.cb-smart-link-row');
            if ($(this).hasClass('cb-smart-link-yform')) {
                var yv = ($(this).val() || '').toString();
                if (yv !== '') {
                    applySelection($widget, $row, 'yform', yv, yv, yv);
                    return;
                }
            }
            smartLinkToggleTypeUi($row);
            smartLinkSerialize($widget);
        });

        $widget.on('click', '.cb-smart-link-detect', function(e) {
            e.preventDefault();
            var $row = $(this).closest('.cb-smart-link-row');
            var value = ($row.find('.cb-smart-link-target').val() || '').toString();
            refreshSmartLinkSelect($row.find('.cb-smart-link-type'), detectLinkType(value));
            smartLinkToggleTypeUi($row);
            smartLinkSerialize($widget);
        });

        $widget.on('click', '.cb-smart-link-remove', function(e) {
            e.preventDefault();
            var $rows = $widget.find('.cb-smart-link-row');
            var $row = $(this).closest('.cb-smart-link-row');
            if ($rows.length > 1 && $widget.data('multiple') == 1) {
                $row.remove();
            } else {
                $row.find('.cb-smart-link-target, .cb-smart-link-label').val('');
                $row.find('.cb-smart-link-target-display').val('');
                $row.find('.cb-smart-link-yform').val('');
                updateAutoLabel($row, '');
            }
            smartLinkToggleTypeUi($row);
            smartLinkSerialize($widget);
        });

        $widget.on('click', '.cb-smart-link-add', function(e) {
            e.preventDefault();
            var $first = $widget.find('.cb-smart-link-row').first();
            var $clone = $first.clone();
            $clone.find('input[type="text"]').val('');
            $clone.find('input[type="hidden"].cb-smart-link-target').val('');
            $clone.find('.cb-smart-link-type').val('auto');
            $clone.find('.cb-smart-link-yform').val('');
            $clone.find('.cb-smart-link-pdfjs').prop('checked', false);
            $clone.find('.cb-smart-link-preview').text('');

            var newWidgetId = 'cbsl_' + Math.floor(Math.random() * 900000 + 100000);
            $clone.find('.cb-smart-link-target-wrap [id]').each(function() {
                var oldId = $(this).attr('id');
                if (!oldId) {
                    return;
                }
                var replaced = oldId.replace(/REX_LINK_[^"\s]+/g, function(match) {
                    if (/_NAME$/.test(match)) {
                        return 'REX_LINK_' + newWidgetId + '_NAME';
                    }
                    return 'REX_LINK_' + newWidgetId;
                }).replace(/mform_[a-z_]+_[^"\s]+/g, function(match) {
                    var prefix = match.replace(/_[^_]+$/, '');
                    return prefix + '_' + newWidgetId;
                });
                $(this).attr('id', replaced);
            });
            $clone.find('.cb-smart-link-target-wrap [name]').each(function() {
                var oldName = $(this).attr('name');
                if (!oldName) {
                    return;
                }
                if (oldName.indexOf('cb_smart_link_target_') === 0) {
                    $(this).attr('name', 'cb_smart_link_target_' + newWidgetId);
                }
            });
            $clone.find('.cb-smart-link-target-wrap').attr('data-link-widget-id', newWidgetId);
            $clone.find('.cb-smart-link-target').data('last-val', '');

            $widget.find('.cb-smart-link-rows').append($clone);
            initSmartLinkSelects($clone);
            smartLinkToggleTypeUi($clone);
            smartLinkSerialize($widget);
        });

        initSmartLinkSelects($widget);

        $widget.find('.cb-smart-link-row').each(function() {
            var $row = $(this);
            var initialLabel = ($row.find('.cb-smart-link-label').val() || '').toString().trim();
            var initialDisplay = ($row.find('.cb-smart-link-target-display').val() || '').toString().trim();
            var initialValue = ($row.find('.cb-smart-link-target').val() || '').toString().trim();
            if (initialLabel !== '' && (initialLabel === initialDisplay || initialLabel === initialValue)) {
                $row.find('.cb-smart-link-label').data('auto-label', initialLabel);
            }
            smartLinkToggleTypeUi($row);
        });
        startSmartLinkSync($widget);
        smartLinkSerialize($widget);
    }


    function initAll(root) {
        var $root = root ? $(root) : $(document);
        var smartLinks = $root.find('.cb-smart-link-widget');

        if (smartLinks.length > 0) {
            console.log('[CB-Widgets] Initializing:', smartLinks.length, 'SmartLink(s)');
        }

        smartLinks.each(function() {
            initSmartLinkWidget($(this));
        });
    }

    function initPublic(root) {
        initAll(root || document);
        ensureAutoInitObserver();
    }

    window.YCBFieldWidgets = window.YCBFieldWidgets || {};
    window.YCBFieldWidgets.init = initPublic;

    var cbWidgetsObserverInitialized = false;
    function ensureAutoInitObserver() {
        if (cbWidgetsObserverInitialized) {
            return;
        }
        cbWidgetsObserverInitialized = true;

        if (typeof MutationObserver === 'undefined' || !document.body) {
            return;
        }

        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(m) {
                if (!m.addedNodes || m.addedNodes.length === 0) {
                    return;
                }
                for (var i = 0; i < m.addedNodes.length; i++) {
                    var node = m.addedNodes[i];
                    if (!node || node.nodeType !== 1) {
                        continue;
                    }
                    initAll(node);
                }
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    $(document).ready(function() {
        console.log('[CB-Widgets] Document ready, initializing...');
        initPublic(document);
    });

    $(document).on('rex:ready', function(e, container) {
        console.log('[CB-Widgets] rex:ready event, initializing container...');
        initAll(container || document);
    });

    $(document).on('shown.bs.modal', '.modal', function() {
        console.log('[CB-Widgets] Modal shown, initializing modal contents...');
        initAll(this);
    });

})(jQuery);
