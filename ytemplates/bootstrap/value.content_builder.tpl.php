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
 * @var string $legacy_html
 * @var string $legacy_editor
 * @var string $legacy_profile
 */

$fieldClass = 'yform-content-builder';
if ($required) {
    $fieldClass .= ' required';
}

$legacyHtml = isset($legacy_html) ? (string) $legacy_html : '';
$legacyEditor = isset($legacy_editor) ? (string) $legacy_editor : 'none';
$legacyProfile = isset($legacy_profile) ? (string) $legacy_profile : 'default';
$hasLegacyHtml = ($legacyHtml !== '' && $legacyEditor !== 'none');
if ($hasLegacyHtml) {
    $fieldClass .= ' has-legacy-html';
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
?>

<div class="form-group yform-element <?= $fieldClass ?>" 
     data-framework="<?= $framework ?>"
     data-online-toggle="<?= $enableOnlineToggle ? '1' : '0' ?>"
     data-legacy-editor="<?= rex_escape($legacyEditor) ?>"
     data-legacy-profile="<?= rex_escape($legacyProfile) ?>"
     data-available-elements='<?= rex_escape(json_encode($available_elements, JSON_UNESCAPED_UNICODE)) ?>'>
    
    <?php if ($label): ?>
        <label class="control-label" for="<?= $field_id ?>"><?= $label ?></label>
    <?php endif; ?>
    
    <?php if ($description): ?>
        <p class="help-block"><?= $description ?></p>
    <?php endif; ?>

    <?php if ($hasLegacyHtml): ?>
        <?php
        $legacyEditorClass = $legacyEditor === 'cke5' ? 'cke5-editor' : 'tiny-editor';
        $legacyEditorId = 'cb_legacy_' . uniqid();
        $legacyInputName = 'FORM[' . $this->params['form_name'] . '][' . $this->getId() . ']';
        ?>
        <div class="content-builder-legacy" data-legacy-editor="<?= rex_escape($legacyEditor) ?>" data-legacy-profile="<?= rex_escape($legacyProfile) ?>">
            <div class="alert alert-warning content-builder-legacy-notice">
                <i class="fa fa-exclamation-triangle"></i>
                <span class="content-builder-legacy-notice-text"><?= rex_i18n::msg('yform_content_builder_legacy_notice') ?></span>
                <button type="button" class="btn btn-primary btn-sm content-builder-legacy-migrate" data-confirm="<?= rex_escape(rex_i18n::msg('yform_content_builder_legacy_migrate_confirm')) ?>">
                    <i class="fa fa-magic"></i> <?= rex_i18n::msg('yform_content_builder_legacy_migrate') ?>
                </button>
            </div>
            <textarea id="<?= rex_escape($legacyEditorId) ?>"
                      name="<?= rex_escape($legacyInputName) ?>"
                      class="form-control content-builder-legacy-textarea <?= $legacyEditorClass ?>"
                      data-profile="<?= rex_escape($legacyProfile) ?>"
                      rows="15"><?= rex_escape($legacyHtml) ?></textarea>
        </div>
    <?php endif; ?>

    <div class="content-builder-slices"<?= $hasLegacyHtml ? ' style="display:none;"' : '' ?>>
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
                $elementPath = $addon->getPath('elements/' . $sliceType);
                $templateFile = $elementPath . '/templates/' . $framework . '.php';
                
                if (!file_exists($templateFile)) {
                    $templateFile = $elementPath . '/templates/plain.php';
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
                                <?php $categoryIndex = 0; ?>
                                <?php foreach ($groupedAvailableElements as $category => $elementsInCategory): ?>
                                    <?php if ($categoryIndex > 0): ?>
                                        <li role="separator" class="divider"></li>
                                    <?php endif; ?>
                                    <li class="dropdown-header"><?= rex_escape(ucfirst(str_replace('_', ' ', (string) $category))) ?></li>
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
                                $bgColor = $elementData['background_color'] ?? 'light';
                                $bgImage = $elementData['background_image'] ?? '';
                                $bgThumbnailClass = 'bg-' . ($bgColor ?: 'light');
                                $bgThumbnailStyle = '';
                                
                                if ($bgImage) {
                                    $bgImage = is_numeric($bgImage) ? rex_media::get($bgImage)->getFullPath() : $bgImage;
                                    $bgThumbnailStyle = ' style="background-image: url(' . rex_url::media($bgImage) . ');"';
                                }
                                ?>
                                <span class="section-bg-thumbnail <?= $bgThumbnailClass ?>"<?= $bgThumbnailStyle ?>></span>
                            </div>
                        <?php else: ?>
                            <?php if (file_exists($templateFile)): ?>
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
    
    <div class="content-builder-add"<?= $hasLegacyHtml ? ' style="display:none;"' : '' ?>>
        <div class="btn-group btn-block">
            <button type="button" class="btn btn-default btn-block dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-plus"></i> <?= rex_i18n::msg('yform_content_builder_element_add') ?>
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <?php $categoryIndex = 0; ?>
                <?php foreach ($groupedAvailableElements as $category => $elementsInCategory): ?>
                    <?php if ($categoryIndex > 0): ?>
                        <li role="separator" class="divider"></li>
                    <?php endif; ?>
                    <li class="dropdown-header"><?= rex_escape(ucfirst(str_replace('_', ' ', (string) $category))) ?></li>
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
    
    <?php if (!$hasLegacyHtml): ?>
    <input type="hidden" 
           name="FORM[<?= $this->params['form_name'] ?>][<?= $this->getId() ?>]" 
           class="content-builder-data" 
           value='<?= rex_escape(json_encode($value, JSON_UNESCAPED_UNICODE)) ?>'>
    <?php endif; ?>
    
    <?php if ($notice): ?>
        <p class="help-block"><?= $notice ?></p>
    <?php endif; ?>
    
</div>

