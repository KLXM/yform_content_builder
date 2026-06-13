<?php
/**
 * @var array $elementData
 * @var string $framework
 */

$layout = $elementData['col_layout'] ?? '50_50';
$columnsData = $elementData['columns'] ?? [];

$currentFramework = $framework ?? 'bootstrap';

$frameworkConfigs = [
    'bootstrap' => [
        'row_class' => 'row',
        'row_attrs' => '',
        'col_classes' => [
            '50_50' => ['col-sm-6', 'col-sm-6'],
            '33_33_33' => ['col-sm-4', 'col-sm-4', 'col-sm-4'],
            '25_75' => ['col-sm-3', 'col-sm-9'],
            '75_25' => ['col-sm-9', 'col-sm-3'],
        ],
        'col_styles' => []
    ],
    'uikit' => [
        'row_class' => 'uk-grid uk-grid-margin',
        'row_attrs' => ' data-uk-grid',
        'col_classes' => [
            '50_50' => ['uk-width-1-2@m', 'uk-width-1-2@m'],
            '33_33_33' => ['uk-width-1-3@m', 'uk-width-1-3@m', 'uk-width-1-3@m'],
            '25_75' => ['uk-width-1-4@m', 'uk-width-3-4@m'],
            '75_25' => ['uk-width-3-4@m', 'uk-width-1-4@m'],
        ],
        'col_styles' => []
    ],
    'plain' => [
        'row_class' => 'cb-plain-row',
        'row_attrs' => ' style="display: flex; flex-wrap: wrap; margin-left: -15px; margin-right: -15px;"',
        'col_classes' => [
            '50_50' => ['cb-plain-col-50', 'cb-plain-col-50'],
            '33_33_33' => ['cb-plain-col-33', 'cb-plain-col-33', 'cb-plain-col-33'],
            '25_75' => ['cb-plain-col-25', 'cb-plain-col-75'],
            '75_25' => ['cb-plain-col-75', 'cb-plain-col-25'],
        ],
        'col_styles' => [
            '50_50' => ['flex: 0 0 50%; max-width: 50%; padding: 0 15px; box-sizing: border-box;', 'flex: 0 0 50%; max-width: 50%; padding: 0 15px; box-sizing: border-box;'],
            '33_33_33' => ['flex: 0 0 33.333%; max-width: 33.333%; padding: 0 15px; box-sizing: border-box;', 'flex: 0 0 33.333%; max-width: 33.333%; padding: 0 15px; box-sizing: border-box;', 'flex: 0 0 33.333%; max-width: 33.333%; padding: 0 15px; box-sizing: border-box;'],
            '25_75' => ['flex: 0 0 25%; max-width: 25%; padding: 0 15px; box-sizing: border-box;', 'flex: 0 0 75%; max-width: 75%; padding: 0 15px; box-sizing: border-box;'],
            '75_25' => ['flex: 0 0 75%; max-width: 75%; padding: 0 15px; box-sizing: border-box;', 'flex: 0 0 25%; max-width: 25%; padding: 0 15px; box-sizing: border-box;'],
        ]
    ]
];

$config = $frameworkConfigs[$currentFramework] ?? $frameworkConfigs['bootstrap'];
$classes = $config['col_classes'][$layout] ?? $frameworkConfigs['bootstrap']['col_classes'][$layout];
$styles = $config['col_styles'][$layout] ?? [];
$rowClass = $config['row_class'];
$rowAttrs = $config['row_attrs'];
$numCols = count($classes);

$isBackendEditMode = false;
if (rex::isBackend()) {
    foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10) as $trace) {
        $function = $trace['function'] ?? '';
        $file = $trace['file'] ?? '';
        if ($function === 'renderSliceBackend' || 
            $function === 'renderEditorSlice' || 
            $function === 'ajaxRenderSlice' || 
            strpos($file, 'value.content_builder.tpl.php') !== false) {
            $isBackendEditMode = true;
            break;
        }
    }
}

if ($isBackendEditMode) {
    // Backend Edit View Rendering
    $elementKeys = \KLXM\YFormContentBuilder\Config\ElementRegistry::getAllElements();
    $available_elements = [];
    foreach ($elementKeys as $key) {
        $config = \KLXM\YFormContentBuilder\Config\ElementRegistry::getElementConfig($key);
        if ($config !== null) {
            $config['type'] = $key;
            $config['key'] = $key;
            $available_elements[$key] = $config;
        }
    }
    $groupedAvailableElements = [];
    foreach ($available_elements as $elementType => $config) {
        $category = trim((string) ($config['category'] ?? ''));
        if ('' === $category) {
            $category = 'sonstiges';
        }
        $groupedAvailableElements[$category][$elementType] = $config;
    }
    $enableOnlineToggle = (bool) rex_addon::get('yform_content_builder')->getConfig('enable_online_toggle', false);
    ?>
    <?php
    $backendRowClass = $rowClass . ' content-builder-columns-row';
    ?>
    <div class="<?= $backendRowClass ?>"<?= $rowAttrs ?>>
        <?php for ($i = 0; $i < $numCols; $i++): ?>
            <?php
            $styleAttr = isset($styles[$i]) ? ' style="' . $styles[$i] . '"' : '';
            ?>
            <div class="<?= $classes[$i] ?> content-builder-column"<?= $styleAttr ?>>
                <div class="content-builder-column-slices" data-column-index="<?= $i ?>">
                    <?php
                    $colSlices = $columnsData[$i] ?? [];
                    foreach ($colSlices as $index => $slice) {
                        echo \KLXM\YFormContentBuilder\Helper::renderSliceBackend(
                            $slice,
                            $index,
                            $available_elements,
                            $groupedAvailableElements,
                            $framework,
                            $enableOnlineToggle
                        );
                    }
                    ?>
                </div>
                <!-- Column Add Button Dropdown -->
                <div class="column-add-slice btn-group" style="margin-top: 10px;">
                    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-plus"></i> Element hinzufügen
                    </button>
                    <ul class="dropdown-menu">
                        <?php $categoryIndex = 0; ?>
                        <?php foreach ($groupedAvailableElements as $category => $elementsInCategory): ?>
                            <?php if ($categoryIndex > 0): ?>
                                <li role="separator" class="divider"></li>
                            <?php endif; ?>
                            <li class="dropdown-header"><?= rex_escape(ucfirst(str_replace('_', ' ', (string) $category))) ?></li>
                            <?php foreach ($elementsInCategory as $elementType => $config): ?>
                                <?php if ($elementType !== 'columns'): // Avoid nested columns ?>
                                    <li>
                                        <a href="#" class="btn-add-nested-slice" 
                                           data-element-type="<?= rex_escape($elementType) ?>"
                                           data-element-label="<?= rex_escape($config['label'] ?? $elementType) ?>">
                                            <i class="fa <?= rex_escape($config['icon'] ?? 'fa-cube') ?>"></i>
                                            <?= rex_escape($config['label'] ?? $elementType) ?>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <?php ++$categoryIndex; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endfor; ?>
    </div>
    <?php
} else {
    // Frontend Output Rendering
    ?>
    <div class="<?= $rowClass ?>"<?= $rowAttrs ?>>
        <?php for ($i = 0; $i < $numCols; $i++): ?>
            <?php
            $styleAttr = isset($styles[$i]) ? ' style="' . $styles[$i] . '"' : '';
            ?>
            <div class="<?= $classes[$i] ?>"<?= $styleAttr ?>>
                <?= \KLXM\YFormContentBuilder\Helper::renderNestedSlices($columnsData[$i] ?? [], $framework) ?>
            </div>
        <?php endfor; ?>
    </div>
    <?php
}
