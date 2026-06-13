<?php
/**
 * Shared Backend Edit Template for Columns Layout
 * 
 * @var array $columnsData
 * @var string $rowClass
 * @var string $rowAttrs
 * @var array $classes
 * @var array $styles
 * @var int $numCols
 * @var string $framework
 */

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
