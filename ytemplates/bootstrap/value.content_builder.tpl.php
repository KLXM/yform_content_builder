<?php
/**
 * @var rex_yform_value_abstract $this
 * @var array $value
 * @var string $field_name
 * @var string $field_id
 * @var string $label
 * @var string $notice
 * @var bool $required
 * @var string $description
 * @var string $framework
 * @var array $available_elements
 * @var bool $legacy_mode_enabled
 * @var bool $legacy_is_active
 * @var string $legacy_html
 * @var string $legacy_cke5_profile
 * @var string $legacy_cke5_lang
 * @var array $legacy_editor_attributes
 * @var bool $legacy_migration_hint
 * @var string $legacy_migration_target
 * @var string $legacy_migration_field
 */

$fieldClass = 'yform-content-builder';
if ($required) {
    $fieldClass .= ' required';
}

// Kompaktmodus aus Addon-Config laden
$addon = rex_addon::get('yform_content_builder');
if ($addon->getConfig('compact_mode')) {
    $fieldClass .= ' compact-mode';
}

// Online/Offline-Toggle pro Slice optional (Addon-Config)
$enableOnlineToggle = (bool) $addon->getConfig('enable_online_toggle', false);
if ($enableOnlineToggle) {
    $fieldClass .= ' has-online-toggle';
}

$groupedAvailableElements = [];
foreach ($available_elements as $elementType => $config) {
    $category = trim((string) ($config['category'] ?? ''));
    if ('' === $category) {
        $category = 'sonstiges';
    }

    if (!isset($groupedAvailableElements[$category])) {
        $groupedAvailableElements[$category] = [];
    }

    $groupedAvailableElements[$category][$elementType] = $config;
}

$normalizeCategorySortKey = static function (string $category): array {
    $category = trim($category);
    if ($category === '') {
        return [9999, ''];
    }

    if (preg_match('/^\s*(\d{1,4})\s*(?:[:\-_.]{1,2}|::)?\s*(.*)$/u', $category, $matches) === 1) {
        $priority = (int) $matches[1];
        $label = trim((string) ($matches[2] ?? ''));
        if ($label !== '') {
            return [$priority, $label];
        }
    }

    return [9999, $category];
};

$formatCategoryLabel = static function (string $category) use ($normalizeCategorySortKey): string {
    [, $label] = $normalizeCategorySortKey($category);
    return ucfirst(str_replace('_', ' ', $label));
};

uksort(
    $groupedAvailableElements,
    static function (string $leftCategory, string $rightCategory) use ($normalizeCategorySortKey): int {
        $leftSortKey = $normalizeCategorySortKey($leftCategory);
        $rightSortKey = $normalizeCategorySortKey($rightCategory);

        if ($leftSortKey[0] !== $rightSortKey[0]) {
            return $leftSortKey[0] <=> $rightSortKey[0];
        }

        return strcasecmp($leftSortKey[1], $rightSortKey[1]);
    }
);

foreach ($groupedAvailableElements as &$elementsInCategory) {
    uasort(
        $elementsInCategory,
        static function (array $leftConfig, array $rightConfig): int {
            $leftLabel = trim((string) ($leftConfig['label'] ?? ''));
            $rightLabel = trim((string) ($rightConfig['label'] ?? ''));
            return strcasecmp($leftLabel, $rightLabel);
        }
    );
}
unset($elementsInCategory);

$legacyEditorId = 'yform_cb_legacy_editor_' . uniqid();
$legacyMigrateButtonId = 'yform_cb_legacy_migrate_' . uniqid();
$legacyNoticeId = 'yform_cb_legacy_notice_' . uniqid();
$legacyEditorAttributeParts = [];
foreach ($legacy_editor_attributes as $attrName => $attrValue) {
    if ($attrName === '' || strtolower((string) $attrName) === 'id') {
        continue;
    }

    $escapedName = rex_escape((string) $attrName);
    $escapedValue = rex_escape((string) $attrValue);
    $legacyEditorAttributeParts[] = $escapedName . '="' . $escapedValue . '"';
}

$legacyEditorAttributeString = implode(' ', $legacyEditorAttributeParts);
?>

<div class="form-group yform-element <?= $fieldClass ?>" 
     data-framework="<?= $framework ?>"
     data-online-toggle="<?= $enableOnlineToggle ? '1' : '0' ?>"
     data-copy-paste="<?= $addon->getConfig('enable_copy_paste') ? '1' : '0' ?>"
     data-legacy-mode="<?= $legacy_is_active ? '1' : '0' ?>"
     data-available-elements='<?= rex_escape(json_encode($available_elements, JSON_UNESCAPED_UNICODE)) ?>'>
    
    <?php if ($label): ?>
        <label class="control-label" for="<?= $field_id ?>"><?= $label ?></label>
    <?php endif; ?>
    
    <?php if ($description): ?>
        <p class="help-block"><?= $description ?></p>
    <?php endif; ?>
    
    <?php if ($legacy_is_active): ?>
        <div class="panel panel-default" style="margin-bottom: 12px;">
            <div class="panel-body">
                <?php if ($legacy_migration_hint): ?>
                    <div id="<?= $legacyNoticeId ?>" class="alert alert-info" style="margin-bottom: 12px; padding: 8px 12px;">
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap;">
                            <span>Sie bearbeiten aktuell einen älteren Inhalt. Wechseln Sie jetzt zum modernen Content-Builder.</span>
                            <button type="button" id="<?= $legacyMigrateButtonId ?>" class="btn btn-default btn-xs">
                                <i class="fa fa-exchange"></i> Zum modernen Content-Builder wechseln
                            </button>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="control-label" for="<?= $legacyEditorId ?>">Legacy HTML (Editor)</label>
                    <textarea
                        id="<?= $legacyEditorId ?>"
                        <?= $legacyEditorAttributeString !== '' ? $legacyEditorAttributeString : '' ?>><?= rex_escape($legacy_html) ?></textarea>
                </div>
            </div>
        </div>

        <input type="hidden"
               name="FORM[<?= $this->params['form_name'] ?>][<?= $this->getId() ?>]"
               class="content-builder-data"
               value='<?= rex_escape($legacy_html) ?>'>

        <script nonce="<?= rex_response::getNonce() ?>">
        (function() {
            var editorId = '<?= $legacyEditorId ?>';
            var migrateButtonId = '<?= $legacyMigrateButtonId ?>';
            var migrateTarget = '<?= rex_escape($legacy_migration_target) ?>';
            var migrateField = '<?= rex_escape($legacy_migration_field) ?>';
            var $textarea = $('#' + editorId);
            var $root = $textarea.closest('.yform-content-builder');
            if ($root.length === 0) {
                return;
            }

            var $hidden = $root.find('.content-builder-data').first();

            if ($hidden.length === 0 || $textarea.length === 0) {
                return;
            }

            function syncLegacyHtmlToHidden() {
                $hidden.val($textarea.val() || '');
            }

            function bindTinyMceSync() {
                if (typeof tinymce === 'undefined') {
                    return false;
                }

                var editor = tinymce.get(editorId);
                if (!editor) {
                    return false;
                }

                var syncFromTinyMce = function() {
                    var content = editor.getContent();
                    $textarea.val(content);
                    syncLegacyHtmlToHidden();
                };

                editor.on('input change keyup SetContent', syncFromTinyMce);
                syncFromTinyMce();
                return true;
            }

            $textarea.on('input change', syncLegacyHtmlToHidden);

            if ($textarea.hasClass('tiny-editor') && typeof tiny_init === 'function') {
                try {
                    tiny_init($root);
                } catch (e) {
                    console.warn('Legacy TinyMCE init failed', e);
                }

                var tinyBindTries = 0;
                var tinyBindTimer = setInterval(function() {
                    tinyBindTries++;
                    if (bindTinyMceSync() || tinyBindTries > 20) {
                        clearInterval(tinyBindTimer);
                    }
                }, 150);
            }

            if ($textarea.hasClass('cke5-editor') && typeof cke5_init === 'function') {
                try {
                    cke5_init($textarea);
                } catch (e) {
                    console.warn('Legacy CKE5 init failed', e);
                }
            }

            $(window).on('rex:cke5IsInit', function(event, editor, initializedEditorId) {
                if (initializedEditorId !== editorId) {
                    return;
                }
                editor.model.document.on('change:data', function() {
                    $textarea.val(editor.getData());
                    syncLegacyHtmlToHidden();
                });
            });

            $('#' + migrateButtonId).on('click', function() {
                var html = $textarea.val() || '';
                var data = {};
                data[migrateField || 'text'] = html;
                var slice = {
                    id: 'slice_' + Date.now(),
                    type: migrateTarget || 'starter_text',
                    online: true,
                    data: data
                };

                $hidden.val(JSON.stringify([slice]));

                var notice = $('#<?= $legacyNoticeId ?>');
                if (notice.length) {
                    notice.removeClass('alert-info').addClass('alert-success');
                    notice.find('span').first().text('In den modernen Content-Builder übernommen. Die Änderungen werden jetzt gespeichert.');
                    $(this).prop('disabled', true);
                }

                // Nach Migration direkt "Übernehmen" auslösen, damit der Wechsel persistent wird.
                var $form = $root.closest('form');
                if ($form.length) {
                    var formEl = $form.get(0);
                    var applyButton = $form.find('button.btn-apply[type="submit"], input.btn-apply[type="submit"]').first().get(0) || null;

                    if (formEl && typeof formEl.requestSubmit === 'function') {
                        formEl.requestSubmit(applyButton || undefined);
                        return;
                    }

                    if (applyButton && typeof applyButton.click === 'function') {
                        applyButton.click();
                        return;
                    }

                    if (formEl && typeof formEl.submit === 'function') {
                        formEl.submit();
                    }
                }
            });

            $root.closest('form').on('submit', function() {
                if ($textarea.hasClass('tiny-editor') && typeof tinymce !== 'undefined') {
                    var tinyEditor = tinymce.get(editorId);
                    if (tinyEditor) {
                        $textarea.val(tinyEditor.getContent());
                    }
                }

                syncLegacyHtmlToHidden();
            });
        })();
        </script>
    <?php else: ?>
        <div class="content-builder-slices">
            <?php if (!empty($value)): ?>
                <?php foreach ($value as $index => $slice): ?>
                    <?php
                    $sliceId = $slice['id'] ?? 'slice_' . uniqid();
                    $sliceType = $slice['type'];
                    $elementData = $slice['data'] ?? [];
                    // Online/Offline-Status – Standard: online (true)
                    $sliceOnline = !isset($slice['online']) || $slice['online'] !== false;
                    
                    // Section-Element?
                    $isSection = ($sliceType === 'section');
                    
                    $addon = rex_addon::get('yform_content_builder');
                    $elementPath = '';

                    if (isset($available_elements[$sliceType]['_path']) && is_string($available_elements[$sliceType]['_path'])) {
                        $candidate = (string) $available_elements[$sliceType]['_path'];
                        if (is_dir($candidate)) {
                            $elementPath = $candidate;
                        }
                    }

                    if ($elementPath === '') {
                        $customPaths = rex_extension::registerPoint(new rex_extension_point(
                            'YFORM_CONTENT_BUILDER_ELEMENT_PATHS',
                            ['']
                        ));

                        foreach ($customPaths as $customPath) {
                            if ($customPath === '') {
                                continue;
                            }

                            $candidate = rtrim($customPath, '/\\') . '/' . $sliceType;
                            if (is_dir($candidate)) {
                                $elementPath = $candidate;
                                break;
                            }
                        }
                    }

                    if ($elementPath === '' && rex_addon::exists('project') && rex_addon::get('project')->isAvailable()) {
                        $candidate = rex_addon::get('project')->getPath('elements/' . $sliceType);
                        if (is_dir($candidate)) {
                            $elementPath = $candidate;
                        }
                    }

                    if ($elementPath === '') {
                        $candidate = $addon->getDataPath('elements/' . $sliceType);
                        if (is_dir($candidate)) {
                            $elementPath = $candidate;
                        }
                    }

                    if ($elementPath === '') {
                        $elementPath = $addon->getPath('elements/' . $sliceType);
                    }

                    $templateFile = '';
                    foreach ([$framework, 'plain', 'uikit', 'bootstrap'] as $templateName) {
                        $candidate = $elementPath . '/templates/' . $templateName . '.php';
                        if (file_exists($candidate)) {
                            $templateFile = $candidate;
                            break;
                        }
                    }
                    ?>
                    
                    <div class="content-builder-slice <?= $isSection ? 'is-section' : '' ?> <?= $sliceOnline ? '' : 'is-offline' ?>" 
                         data-slice-id="<?= rex_escape($sliceId) ?>"
                         data-slice-type="<?= rex_escape($sliceType) ?>"
                         data-slice-index="<?= $index ?>"
                         data-slice-online="<?= $sliceOnline ? '1' : '0' ?>"
                         data-slice-data='<?= rex_escape(json_encode($elementData, JSON_UNESCAPED_UNICODE)) ?>'>
                        
                        <div class="slice-toolbar">
                            <div class="btn-group btn-group-insert">
                                <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" title="<?= rex_i18n::msg('yform_content_builder_element_add') ?>">
                                    <i class="fa fa-plus"></i>
                                </button>
                                <ul class="dropdown-menu pull-right">
                                    <?php if ($addon->getConfig('enable_copy_paste')): ?>
                                        <li class="paste-slice-item" style="display: none;">
                                            <a href="#" class="btn-paste-slice" data-insert-after="<?= $index ?>">
                                                <i class="fa fa-clipboard"></i> <strong>Element einfügen</strong>
                                            </a>
                                        </li>
                                        <li role="separator" class="divider paste-slice-item" style="display: none;"></li>
                                    <?php endif; ?>
                                    <?php $categoryIndex = 0; ?>
                                    <?php foreach ($groupedAvailableElements as $category => $elementsInCategory): ?>
                                        <?php if ($categoryIndex > 0): ?>
                                            <li role="separator" class="divider"></li>
                                        <?php endif; ?>
                                        <li class="dropdown-header"><?= rex_escape($formatCategoryLabel((string) $category)) ?></li>
                                        <?php foreach ($elementsInCategory as $elementType => $config): ?>
                                            <li>
                                                <?php $elementDescription = (string) ($config['description'] ?? ''); ?>
                                                <a href="#" class="btn-insert-slice" 
                                                   data-element-type="<?= rex_escape($elementType) ?>"
                                                   data-element-label="<?= rex_escape($config['label'] ?? $elementType) ?>"
                                                   <?php if ($elementDescription !== ''): ?>
                                                   data-toggle="tooltip"
                                                   data-placement="right"
                                                   data-container="body"
                                                   data-delay='{"show":700,"hide":120}'
                                                   title="<?= rex_escape($elementDescription) ?>"
                                                   <?php endif; ?>
                                                   data-insert-after="<?= $index ?>">
                                                    <i class="fa <?= rex_escape($config['icon'] ?? 'fa-cube') ?>"></i>
                                                    <?= rex_escape($config['label'] ?? $elementType) ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                        <?php ++$categoryIndex; ?>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <button type="button" class="btn btn-xs btn-default btn-slice-edit" title="<?= rex_i18n::msg('yform_content_builder_element_edit') ?>">
                                <i class="fa fa-pencil"></i>
                            </button>
                            <?php if ($addon->getConfig('enable_copy_paste')): ?>
                            <button type="button" class="btn btn-xs btn-default btn-slice-copy" title="Kopieren">
                                <i class="fa fa-copy"></i>
                            </button>
                            <?php endif; ?>
                            <button type="button" class="btn btn-xs btn-default btn-slice-move-up" title="Nach oben verschieben">
                                <i class="fa fa-arrow-up"></i>
                            </button>
                            <button type="button" class="btn btn-xs btn-default btn-slice-move-down" title="Nach unten verschieben">
                                <i class="fa fa-arrow-down"></i>
                            </button>
                            <?php if ($enableOnlineToggle): ?>
                            <button type="button" class="btn btn-xs btn-default btn-slice-toggle-online" title="<?= $sliceOnline ? rex_i18n::msg('yform_content_builder_element_set_offline') : rex_i18n::msg('yform_content_builder_element_set_online') ?>">
                                <i class="fa <?= $sliceOnline ? 'fa-eye' : 'fa-eye-slash' ?>"></i>
                            </button>
                            <?php endif; ?>
                            <button type="button" class="btn btn-xs btn-danger btn-slice-delete" title="<?= rex_i18n::msg('yform_content_builder_element_delete') ?>">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                        
                        <div class="slice-rendered">
                            <?php if ($isSection): ?>
                                <!-- Section-Label mit Hintergrund-Thumbnail -->
                                <div class="section-backend-label">
                                    <i class="fa fa-object-group"></i>
                                    <strong>Section:</strong> <?= rex_escape($elementData['label'] ?? 'Unbenannt') ?>
                                    <span class="section-info">
                                        <?php if (!empty($elementData['background_color']) && $elementData['background_color'] !== 'none'): ?>
                                            <span class="label label-default"><?= rex_escape($elementData['background_color']) ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($elementData['custom_id'])): ?>
                                            <span class="label label-info">#<?= rex_escape($elementData['custom_id']) ?></span>
                                        <?php endif; ?>
                                    </span>
                                    <!-- Hintergrund-Thumbnail -->
                                    <?php
                                    $bgColor = $elementData['background_color'] ?? 'none';
                                    $bgImage = $elementData['background_image'] ?? '';
                                    $bgThumbnailClass = 'bg-' . ($bgColor ?: 'none');
                                    $bgThumbnailStyle = '';
                                    
                                    if ($bgImage) {
                                        if (is_numeric($bgImage)) {
                                            $media = rex_media::get($bgImage);
                                            if ($media instanceof rex_media) {
                                                $bgImage = $media->getFileName();
                                            }
                                        }
                                        $bgThumbnailStyle = ' style="background-image: url(' . rex_url::media($bgImage) . ');"';
                                    }
                                    ?>
                                    <span class="section-bg-thumbnail <?= $bgThumbnailClass ?>"<?= $bgThumbnailStyle ?>></span>
                                </div>
                            <?php else: ?>
                                <?php if ($templateFile !== ''): ?>
                                    <?php include $templateFile; ?>
                                <?php else: ?>
                                    <div class="alert alert-danger">Template not found: <?= rex_escape($sliceType) ?></div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        
                        <div class="slice-edit-form" style="display: none;">
                            <!-- Wird per AJAX mit YForm-Formular gefüllt -->
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="content-builder-add">
            <div class="btn-group btn-block">
                <button type="button" class="btn btn-default btn-block dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-plus"></i> <?= rex_i18n::msg('yform_content_builder_element_add') ?>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <?php if ($addon->getConfig('enable_copy_paste')): ?>
                        <li class="paste-slice-item" style="display: none;">
                            <a href="#" class="btn-paste-slice" data-insert-after="end">
                                <i class="fa fa-clipboard"></i> <strong>Element einfügen</strong>
                            </a>
                        </li>
                        <li role="separator" class="divider paste-slice-item" style="display: none;"></li>
                    <?php endif; ?>
                    <?php $categoryIndex = 0; ?>
                    <?php foreach ($groupedAvailableElements as $category => $elementsInCategory): ?>
                        <?php if ($categoryIndex > 0): ?>
                            <li role="separator" class="divider"></li>
                        <?php endif; ?>
                        <li class="dropdown-header"><?= rex_escape($formatCategoryLabel((string) $category)) ?></li>
                        <?php foreach ($elementsInCategory as $elementType => $config): ?>
                            <li>
                                <?php $elementDescription = (string) ($config['description'] ?? ''); ?>
                                <a href="#" class="btn-add-slice" 
                                   data-element-type="<?= rex_escape($elementType) ?>"
                                   data-element-label="<?= rex_escape($config['label'] ?? $elementType) ?>"
                                   <?php if ($elementDescription !== ''): ?>
                                   data-toggle="tooltip"
                                   data-placement="right"
                                   data-container="body"
                                   data-delay='{"show":700,"hide":120}'
                                   title="<?= rex_escape($elementDescription) ?>"
                                   <?php endif; ?>>
                                    <i class="fa <?= rex_escape($config['icon'] ?? 'fa-cube') ?>"></i>
                                    <?= rex_escape($config['label'] ?? $elementType) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                        <?php ++$categoryIndex; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        
        <input type="hidden" 
               name="FORM[<?= $this->params['form_name'] ?>][<?= $this->getId() ?>]" 
               class="content-builder-data" 
               value='<?= rex_escape(json_encode($value, JSON_UNESCAPED_UNICODE)) ?>'>
    <?php endif; ?>
    
    <?php if ($notice): ?>
        <p class="help-block"><?= $notice ?></p>
    <?php endif; ?>
    
</div>

