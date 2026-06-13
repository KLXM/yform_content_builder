<?php
/**
 * UIkit Template for Columns Layout
 * 
 * @var array $elementData
 * @var string $framework
 */

$layout = $elementData['col_layout'] ?? '50_50';
$columnsData = $elementData['columns'] ?? [];

$rowClass = 'uk-grid uk-grid-margin';
$rowAttrs = ' data-uk-grid';
$colClasses = [
    '50_50' => ['uk-width-1-2@m', 'uk-width-1-2@m'],
    '33_33_33' => ['uk-width-1-3@m', 'uk-width-1-3@m', 'uk-width-1-3@m'],
    '25_75' => ['uk-width-1-4@m', 'uk-width-3-4@m'],
    '75_25' => ['uk-width-3-4@m', 'uk-width-1-4@m'],
];
$classes = $colClasses[$layout] ?? ['uk-width-1-2@m', 'uk-width-1-2@m'];
$styles = [];
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
        <div class="<?= $classes[$i] ?>">
            <?= \KLXM\YFormContentBuilder\Helper::renderNestedSlices($columnsData[$i] ?? [], $framework) ?>
        </div>
    <?php endfor; ?>
</div>
