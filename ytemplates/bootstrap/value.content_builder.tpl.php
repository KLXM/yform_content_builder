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
 */

$fieldClass = 'yform-content-builder';
if ($required) {
    $fieldClass .= ' required';
}
?>

<div class="form-group yform-element yform-content-builder" 
     data-framework="<?= $framework ?>"
     data-available-elements='<?= rex_escape(json_encode($available_elements, JSON_UNESCAPED_UNICODE)) ?>'>
    
    <?php if ($label): ?>
        <label class="control-label" for="<?= $field_id ?>"><?= $label ?></label>
    <?php endif; ?>
    
    <?php if ($description): ?>
        <p class="help-block"><?= $description ?></p>
    <?php endif; ?>
    
    <div class="content-builder-slices">
        <?php if (!empty($value)): ?>
            <?php foreach ($value as $index => $slice): ?>
                <?php
                $sliceId = $slice['id'] ?? 'slice_' . uniqid();
                $sliceType = $slice['type'];
                $elementData = $slice['data'] ?? [];
                
                // Section-Element?
                $isSection = ($sliceType === 'section');
                
                $addon = rex_addon::get('yform_content_builder');
                $elementPath = $addon->getPath('elements/' . $sliceType);
                $templateFile = $elementPath . '/templates/' . $framework . '.php';
                
                if (!file_exists($templateFile)) {
                    $templateFile = $elementPath . '/templates/plain.php';
                }
                ?>
                
                <div class="content-builder-slice <?= $isSection ? 'is-section' : '' ?>" 
                     data-slice-id="<?= rex_escape($sliceId) ?>"
                     data-slice-type="<?= rex_escape($sliceType) ?>"
                     data-slice-index="<?= $index ?>"
                     data-slice-data='<?= rex_escape(json_encode($elementData, JSON_UNESCAPED_UNICODE)) ?>'>
                    
                    <div class="slice-toolbar">
                        <div class="btn-group btn-group-insert">
                            <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" title="<?= rex_i18n::msg('yform_content_builder_element_add') ?>">
                                <i class="fa fa-plus"></i>
                            </button>
                            <ul class="dropdown-menu pull-right">
                                <?php foreach ($available_elements as $elementType => $config): ?>
                                    <li>
                                        <a href="#" class="btn-insert-slice" 
                                           data-element-type="<?= rex_escape($elementType) ?>"
                                           data-element-label="<?= rex_escape($config['label'] ?? $elementType) ?>"
                                           data-insert-after="<?= $index ?>">
                                            <i class="fa <?= rex_escape($config['icon'] ?? 'fa-cube') ?>"></i>
                                            <?= rex_escape($config['label'] ?? $elementType) ?>
                                        </a>
                                    </li>
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
                        <button type="button" class="btn btn-xs btn-danger btn-slice-delete" title="<?= rex_i18n::msg('yform_content_builder_element_delete') ?>">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                    
                    <div class="slice-rendered">
                        <?php if ($isSection): ?>
                            <!-- Section-Label im Backend anzeigen -->
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
    
    <div class="content-builder-add">
        <div class="btn-group btn-block">
            <button type="button" class="btn btn-default btn-block dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-plus"></i> <?= rex_i18n::msg('yform_content_builder_element_add') ?>
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <?php foreach ($available_elements as $elementType => $config): ?>
                    <li>
                        <a href="#" class="btn-add-slice" 
                           data-element-type="<?= rex_escape($elementType) ?>"
                           data-element-label="<?= rex_escape($config['label'] ?? $elementType) ?>">
                            <i class="fa <?= rex_escape($config['icon'] ?? 'fa-cube') ?>"></i>
                            <?= rex_escape($config['label'] ?? $elementType) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    
    <input type="hidden" 
           name="FORM[<?= $this->params['form_name'] ?>][<?= $this->getId() ?>]" 
           class="content-builder-data" 
           value='<?= rex_escape(json_encode($value, JSON_UNESCAPED_UNICODE)) ?>'>
    
    <?php if ($notice): ?>
        <p class="help-block"><?= $notice ?></p>
    <?php endif; ?>
    
</div>

