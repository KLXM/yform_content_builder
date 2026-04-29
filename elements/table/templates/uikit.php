<?php
/**
 * Table Element - UIkit Template
 * Barrierefreie, responsive Tabelle
 */

// Daten auslesen
$tableData = $elementData['table_data'] ?? '';
$tableCaption = '';
$tableStyle = $elementData['table_style'] ?? 'default';
$tableSize = $elementData['table_size'] ?? 'default';
$tableHover = !empty($elementData['table_hover']);
$tableStriped = !empty($elementData['table_striped']);
$tableDivider = !empty($elementData['table_divider']);
$tableResponsive = $elementData['table_responsive'] ?? '';
$tableAlign = $elementData['table_align'] ?? '';

// Section-Einstellungen
$sectionBg = $elementData['section_bg'] ?? '';
$sectionBgImage = $elementData['section_bg_image'] ?? '';
$sectionPadding = $elementData['section_padding'] ?? '';
$containerWidth = $elementData['container_width'] ?? '';

// Daten parsen (JSON)
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
    // Fehler bei JSON Parse
}

if (empty($tableHeadRows) && empty($tableRows)) {
    return; // Keine Daten, nichts rendern
}

// Klassen zusammenstellen
$tableClasses = ['uk-table'];

// Style-Klassen
if ($tableStyle !== 'default') {
    $styleClasses = explode(' ', $tableStyle);
    $tableClasses = array_merge($tableClasses, $styleClasses);
}

// Größe
if ($tableSize !== 'default') {
    $tableClasses[] = $tableSize;
}

// Hover
if ($tableHover) {
    $tableClasses[] = 'uk-table-hover';
}

// Striped
if ($tableStriped) {
    $tableClasses[] = 'uk-table-striped';
}

// Divider
if ($tableDivider) {
    $tableClasses[] = 'uk-table-divider';
}

// Responsive
if ($tableResponsive) {
    $tableClasses[] = $tableResponsive;
}

// Alignment
if ($tableAlign) {
    $tableClasses[] = $tableAlign;
}

$tableClasses = array_values(array_unique($tableClasses));

$tableClassStr = implode(' ', $tableClasses);

$alignStyle = static function (string $type): string {
    if ($type === 'number') {
        return ' style="text-align:right;"';
    }
    if ($type === 'center') {
        return ' style="text-align:center;"';
    }

    return '';
};

// Section-Klassen
$sectionClasses = ['uk-section'];
if ($sectionBg) $sectionClasses[] = $sectionBg;
if ($sectionPadding) $sectionClasses[] = $sectionPadding;

// Section Background
$sectionStyle = '';
if (!empty($sectionBgImage)) {
    $bgMediaExt = strtolower(pathinfo($sectionBgImage, PATHINFO_EXTENSION));
    $videoExtensions = ['mp4', 'webm', 'ogg'];
    
    if (!in_array($bgMediaExt, $videoExtensions)) {
        $bgImageUrl = rex_media_manager::getUrl('content_slideshow', $sectionBgImage);
        $sectionStyle = ' style="background-image: url(\'' . $bgImageUrl . '\'); background-size: cover; background-position: center;"';
    }
}

$hasSection = $sectionBg || $sectionPadding || !empty($sectionBgImage);

// Container-Wrapper nötig?
$needsWrapper = $tableResponsive === '' && !$hasSection; // Nur bei horizontal scroll ohne section

?>

<?php if ($hasSection): ?>
<section class="<?= implode(' ', $sectionClasses) ?>"<?= $sectionStyle ?>>
<?php endif; ?>

<?php if ($containerWidth): ?>
<div class="<?= $containerWidth ?>">
<?php endif; ?>

<?php if ($tableResponsive === ''): ?>
<!-- Horizontal Scroll wrapper -->
<div class="uk-overflow-auto">
<?php endif; ?>

<table class="<?= $tableClassStr ?>">
    
    <?php if ($tableCaption): ?>
    <caption><?= rex_escape($tableCaption) ?></caption>
    <?php endif; ?>
    
    <?php if (!empty($tableHeadRows)): ?>
    <thead>
        <?php foreach ($tableHeadRows as $rowIndex => $row): ?>
        <tr>
            <?php foreach ($row as $cellIndex => $cell): ?>
            <?php
            $colDef = is_array($tableCols[$cellIndex] ?? null) ? $tableCols[$cellIndex] : [];
            $headerType = (string) ($colDef['header_type'] ?? ($colDef['type'] ?? 'text'));
            ?>
            <th scope="col"<?= $alignStyle($headerType) ?>>
                <?= rex_escape($cell) ?>
            </th>
            <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
    </thead>
    <?php endif; ?>
    
    <?php if (!empty($tableRows)): ?>
    <tbody>
        <?php foreach ($tableRows as $rowIndex => $row): ?>
        <tr>
            <?php foreach ($row as $cellIndex => $cell): ?>
            <?php
            $colDef = is_array($tableCols[$cellIndex] ?? null) ? $tableCols[$cellIndex] : [];
            $bodyType = (string) ($colDef['type'] ?? 'text');
            ?>
            <?php if ($hasHeaderCol && $cellIndex === 0): ?>
            <th scope="row"<?= $alignStyle($bodyType) ?>><?= rex_escape($cell) ?></th>
            <?php else: ?>
            <td<?= $alignStyle($bodyType) ?>><?= rex_escape($cell) ?></td>
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

<?php if ($containerWidth): ?>
</div>
<?php endif; ?>

<?php if ($hasSection): ?>
</section>
<?php endif; ?>
