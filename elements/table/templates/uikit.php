<?php
/**
 * Table Element - UIkit Template
 * Barrierefreie, responsive Tabelle
 */

$tableData = $elementData['table_data'] ?? '';
$tableCaption = '';
$tableStyle = $elementData['table_style'] ?? 'default';
$tableSize = $elementData['table_size'] ?? 'default';
$tableHover = !empty($elementData['table_hover']);
$tableStriped = !empty($elementData['table_striped']);
$tableDivider = !empty($elementData['table_divider']);
$tableResponsive = $elementData['table_responsive'] ?? '';
$tableAlign = $elementData['table_align'] ?? '';

$sectionBg = $elementData['section_bg'] ?? '';
$sectionBgImage = $elementData['section_bg_image'] ?? '';
$sectionPadding = $elementData['section_padding'] ?? '';
$containerWidth = $elementData['container_width'] ?? '';
$sectionLight = !empty($elementData['section_light']);
$enableSection = !isset($elementData['enable_section']) || !empty($elementData['enable_section']);
$enableContainer = !isset($elementData['enable_container']) || !empty($elementData['enable_container']);

$tableRows = [];
$tableHeadRows = [];
$tableCols = [];
$hasHeaderCol = false;

try {
    $data = json_decode((string) $tableData, true);
    if (is_array($data) && isset($data['rows'])) {
        $rows = is_array($data['rows']) ? $data['rows'] : [];
        $hasHeaderRow = (bool) ($data['has_header_row'] ?? true);
        $hasHeaderCol = (bool) ($data['has_header_col'] ?? false);
        $tableCols = is_array($data['cols'] ?? null) ? $data['cols'] : [];

        if (is_string($data['caption'] ?? null)) {
            $tableCaption = $data['caption'];
        }

        $normalizedRows = [];
        foreach ($rows as $row) {
            if (is_array($row)) {
                $normalizedRows[] = array_values($row);
            }
        }

        if ($hasHeaderRow && $normalizedRows !== []) {
            $tableHeadRows[] = array_shift($normalizedRows);
        }
        $tableRows = $normalizedRows;
    }
} catch (Exception $e) {
}

if (empty($tableHeadRows) && empty($tableRows)) {
    return;
}

$tableClasses = ['uk-table'];
if ($tableStyle !== 'default') {
    $tableClasses = array_merge($tableClasses, explode(' ', $tableStyle));
}
if ($tableSize !== 'default') {
    $tableClasses[] = $tableSize;
}
if ($tableHover) {
    $tableClasses[] = 'uk-table-hover';
}
if ($tableStriped) {
    $tableClasses[] = 'uk-table-striped';
}
if ($tableDivider) {
    $tableClasses[] = 'uk-table-divider';
}
if ($tableResponsive) {
    $tableClasses[] = $tableResponsive;
}
if ($tableAlign) {
    $tableClasses[] = $tableAlign;
}

$tableClassStr = implode(' ', array_values(array_unique($tableClasses)));

$alignStyle = static function (string $type): string {
    if ($type === 'number') {
        return ' style="text-align:right;"';
    }
    if ($type === 'center') {
        return ' style="text-align:center;"';
    }
    return '';
};

$wrapper = new rex_fragment();
$wrapper->setVar('enable_section', $enableSection, false);
$wrapper->setVar('enable_container', $enableContainer, false);
$wrapper->setVar('section_bg', $sectionBg, false);
$wrapper->setVar('section_bg_image', $sectionBgImage, false);
$wrapper->setVar('section_padding', $sectionPadding, false);
$wrapper->setVar('container_width', $containerWidth, false);
$wrapper->setVar('section_light', $sectionLight, false);

$wrapperClose = new rex_fragment();
$wrapperClose->setVar('mode', 'close', false);
$wrapperClose->setVar('enable_section', $enableSection, false);
$wrapperClose->setVar('enable_container', $enableContainer, false);
$wrapperClose->setVar('section_bg_image', $sectionBgImage, false);
$wrapperClose->setVar('container_width', $containerWidth, false);
?>
<?= $wrapper->parse('ycb_elements/wrapper.php') ?>

<?php if ($tableResponsive === ''): ?>
<div class="uk-overflow-auto">
<?php endif; ?>

<table class="<?= rex_escape($tableClassStr) ?>">
    <?php if ($tableCaption): ?>
    <caption><?= rex_escape($tableCaption) ?></caption>
    <?php endif; ?>

    <?php if (!empty($tableHeadRows)): ?>
    <thead>
        <?php foreach ($tableHeadRows as $row): ?>
        <tr>
            <?php foreach ($row as $cellIndex => $cell): ?>
            <?php
            $colDef = is_array($tableCols[$cellIndex] ?? null) ? $tableCols[$cellIndex] : [];
            $headerType = (string) ($colDef['header_type'] ?? ($colDef['type'] ?? 'text'));
            ?>
            <th scope="col"<?= $alignStyle($headerType) ?>><?= rex_escape((string) $cell) ?></th>
            <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
    </thead>
    <?php endif; ?>

    <?php if (!empty($tableRows)): ?>
    <tbody>
        <?php foreach ($tableRows as $row): ?>
        <tr>
            <?php foreach ($row as $cellIndex => $cell): ?>
            <?php
            $colDef = is_array($tableCols[$cellIndex] ?? null) ? $tableCols[$cellIndex] : [];
            $bodyType = (string) ($colDef['type'] ?? 'text');
            ?>
            <?php if ($hasHeaderCol && $cellIndex === 0): ?>
            <th scope="row"<?= $alignStyle($bodyType) ?>><?= rex_escape((string) $cell) ?></th>
            <?php else: ?>
            <td<?= $alignStyle($bodyType) ?>><?= rex_escape((string) $cell) ?></td>
            <?php endif; ?>
            <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <?php endif; ?>
</table>

<?php if ($tableResponsive === ''): ?>
</div>
<?php endif; ?>

<?= $wrapperClose->parse('ycb_elements/wrapper.php') ?>
