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

<div class="form-group yform-element yform-content-builder" data-framework="<?= $framework ?>">
    
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
                // Element-Template direkt einbinden (ohne renderSlice-Wrapper)
                $sliceId = $slice['id'] ?? 'slice_' . uniqid();
                $sliceType = $slice['type'];
                $elementData = $slice['data'] ?? [];
                
                $addon = rex_addon::get('yform_content_builder');
                $elementPath = $addon->getPath('elements/' . $sliceType);
                $templateFile = $elementPath . '/templates/' . $framework . '.php';
                
                if (!file_exists($templateFile)) {
                    $templateFile = $elementPath . '/templates/plain.php';
                }
                ?>
                
                <div class="content-builder-slice" 
                     data-slice-id="<?= rex_escape($sliceId) ?>"
                     data-slice-type="<?= rex_escape($sliceType) ?>"
                     data-slice-index="<?= $index ?>"
                     data-slice-data='<?= rex_escape(json_encode($elementData, JSON_UNESCAPED_UNICODE)) ?>'>
                    
                    <div class="slice-toolbar">
                        <button type="button" class="btn btn-xs btn-default btn-slice-edit" title="<?= rex_i18n::msg('yform_content_builder_element_edit') ?>">
                            <i class="fa fa-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-xs btn-default btn-slice-move" title="<?= rex_i18n::msg('yform_content_builder_element_move') ?>">
                            <i class="fa fa-arrows"></i>
                        </button>
                        <button type="button" class="btn btn-xs btn-danger btn-slice-delete" title="<?= rex_i18n::msg('yform_content_builder_element_delete') ?>">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                    
                    <div class="slice-rendered">
                        <?php if (file_exists($templateFile)): ?>
                            <?php include $templateFile; ?>
                        <?php else: ?>
                            <div class="alert alert-danger">Template not found: <?= rex_escape($sliceType) ?></div>
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

