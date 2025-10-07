<?php
/**
 * UIkit Template für Akkordeon Element
 * @var array $elementData
 */

$items = $elementData['items'] ?? [];
?>

<div class="accordion-element">
    <ul class="uk-accordion" data-uk-accordion>
        <?php foreach ($items as $item): ?>
            <?php 
            $title = $item['title'] ?? '';
            $content = $item['content'] ?? '';
            ?>
            <li>
                <a class="uk-accordion-title" href="#"><?= rex_escape($title) ?></a>
                <div class="uk-accordion-content">
                    <?= $content ?>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
