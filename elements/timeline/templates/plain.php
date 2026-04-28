<?php

/**
 * Timeline Element - Plain Template
 *
 * @var array $elementData
 */

$heading = $elementData['heading'] ?? '';
$tag     = $elementData['tag'] ?? 'h2';
$intro   = $elementData['intro'] ?? '';
$items   = $elementData['items'] ?? [];

if (empty($items)) {
    return;
}

?>
<div class="cb-timeline-plain">
    <?php if ($heading): ?>
        <<?= $tag ?>><?= rex_escape($heading) ?></<?= $tag ?>>
    <?php endif; ?>
    <?php if ($intro): ?>
        <p><?= nl2br(rex_escape($intro)) ?></p>
    <?php endif; ?>
    <ol class="cb-timeline-plain__list">
        <?php foreach ($items as $item):
            $date  = $item['date'] ?? '';
            $title = $item['title'] ?? '';
            $text  = $item['text'] ?? '';
            $badge = $item['badge'] ?? '';

            if (empty($title)) {
                continue;
            }
        ?>
        <li class="cb-timeline-plain__item">
            <?php if ($date): ?><time><?= rex_escape($date) ?></time><?php endif; ?>
            <?php if ($badge): ?><span class="cb-badge"><?= rex_escape($badge) ?></span><?php endif; ?>
            <strong><?= rex_escape($title) ?></strong>
            <?php if ($text): ?><p><?= nl2br(rex_escape($text)) ?></p><?php endif; ?>
        </li>
        <?php endforeach; ?>
    </ol>
</div>
