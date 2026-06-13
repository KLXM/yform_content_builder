<?php
/**
 * Plain HTML/CSS Template for Columns Layout
 * 
 * @var array $elementData
 * @var string $framework
 */

$layout = $elementData['col_layout'] ?? '50_50';
$columnsData = $elementData['columns'] ?? [];

$rowClass = 'cb-plain-row';
$rowAttrs = ' style="display: flex; flex-wrap: wrap; margin-left: -15px; margin-right: -15px;"';
$colClasses = [
    '50_50' => ['cb-plain-col-50', 'cb-plain-col-50'],
    '33_33_33' => ['cb-plain-col-33', 'cb-plain-col-33', 'cb-plain-col-33'],
    '25_75' => ['cb-plain-col-25', 'cb-plain-col-75'],
    '75_25' => ['cb-plain-col-75', 'cb-plain-col-25'],
];
$colStyles = [
    '50_50' => ['flex: 0 0 50%; max-width: 50%; padding: 0 15px; box-sizing: border-box;', 'flex: 0 0 50%; max-width: 50%; padding: 0 15px; box-sizing: border-box;'],
    '33_33_33' => ['flex: 0 0 33.333%; max-width: 33.333%; padding: 0 15px; box-sizing: border-box;', 'flex: 0 0 33.333%; max-width: 33.333%; padding: 0 15px; box-sizing: border-box;', 'flex: 0 0 33.333%; max-width: 33.333%; padding: 0 15px; box-sizing: border-box;'],
    '25_75' => ['flex: 0 0 25%; max-width: 25%; padding: 0 15px; box-sizing: border-box;', 'flex: 0 0 75%; max-width: 75%; padding: 0 15px; box-sizing: border-box;'],
    '75_25' => ['flex: 0 0 75%; max-width: 75%; padding: 0 15px; box-sizing: border-box;', 'flex: 0 0 25%; max-width: 25%; padding: 0 15px; box-sizing: border-box;'],
];
$classes = $colClasses[$layout] ?? ['cb-plain-col-50', 'cb-plain-col-50'];
$styles = $colStyles[$layout] ?? [];
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
    include __DIR__ . '/_backend.php';
    return;
}

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
