<?php
/**
 * Plain HTML Template für Akkordeon Element
 * @var array $elementData
 */

$items = $elementData['items'] ?? [];
$accordionId = 'accordion_' . uniqid();
?>

<div class="accordion-element" id="<?= $accordionId ?>">
    <?php foreach ($items as $index => $item): ?>
        <?php 
        $itemId = $accordionId . '_item_' . $index;
        $title = $item['title'] ?? '';
        $content = $item['content'] ?? '';
        ?>
        <div class="accordion-item">
            <div class="accordion-title">
                <a href="#<?= $itemId ?>" class="accordion-toggle">
                    <?= rex_escape($title) ?>
                </a>
            </div>
            <div id="<?= $itemId ?>" class="accordion-content">
                <?= $content ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
