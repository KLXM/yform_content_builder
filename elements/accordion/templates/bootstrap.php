<?php
/**
 * Accordion / Tabs Element - Bootstrap Template
 * @var array $elementData
 */

$displayType = $elementData['display_type'] ?? 'accordion';
$style = $elementData['style'] ?? 'default';
$items = $elementData['items'] ?? [];
$uniqueId = uniqid('acc_');

if (empty($items)) {
    return;
}
?>

<?php if ($displayType === 'tabs'): ?>
    <!-- TABS -->
    <div class="tabs-element">
        <ul class="nav nav-tabs" role="tablist">
            <?php foreach ($items as $index => $item): ?>
                <li role="presentation" class="<?= $index === 0 ? 'active' : '' ?>">
                    <a href="#<?= $uniqueId ?>_tab_<?= $index ?>" 
                       role="tab" 
                       data-toggle="tab">
                        <?php if (!empty($item['icon'])): ?>
                            <i class="fa <?= rex_escape($item['icon']) ?>"></i>
                        <?php endif; ?>
                        <?= rex_escape($item['title'] ?? 'Tab ' . ($index + 1)) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="tab-content">
            <?php foreach ($items as $index => $item): ?>
                <div role="tabpanel" 
                     class="tab-pane <?= $index === 0 ? 'active' : '' ?>" 
                     id="<?= $uniqueId ?>_tab_<?= $index ?>">
                    <div class="tab-pane-content">
                        <?= $item['content'] ?? '' ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

<?php else: ?>
    <!-- ACCORDION -->
    <div class="accordion-element">
        <div class="panel-group" id="<?= $uniqueId ?>" role="tablist">
            <?php foreach ($items as $index => $item): ?>
                <div class="panel panel-<?= rex_escape($style) ?>">
                    <div class="panel-heading" role="tab" id="heading_<?= $uniqueId ?>_<?= $index ?>">
                        <h4 class="panel-title">
                            <a role="button" 
                               data-toggle="collapse" 
                               data-parent="#<?= $uniqueId ?>" 
                               href="#collapse_<?= $uniqueId ?>_<?= $index ?>"
                               aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>"
                               aria-controls="collapse_<?= $uniqueId ?>_<?= $index ?>">
                                <?php if (!empty($item['icon'])): ?>
                                    <i class="fa <?= rex_escape($item['icon']) ?>"></i>
                                <?php endif; ?>
                                <?= rex_escape($item['title'] ?? 'Item ' . ($index + 1)) ?>
                            </a>
                        </h4>
                    </div>
                    <div id="collapse_<?= $uniqueId ?>_<?= $index ?>" 
                         class="panel-collapse collapse <?= $index === 0 ? 'in' : '' ?>" 
                         role="tabpanel" 
                         aria-labelledby="heading_<?= $uniqueId ?>_<?= $index ?>">
                        <div class="panel-body">
                            <?= $item['content'] ?? '' ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<style>
/* Tabs Styling */
.tabs-element .tab-content {
    border: 1px solid #ddd;
    border-top: none;
    padding: 20px;
    background: #fff;
}

.tabs-element .tab-pane-content {
    min-height: 100px;
}

.tabs-element .nav-tabs > li > a i.fa {
    margin-right: 5px;
}
</style>

