<?php

/**
 * Timeline Element - Bootstrap Template
 *
 * @var array $elementData
 */

$heading   = $elementData['heading'] ?? '';
$tag       = $elementData['tag'] ?? 'h2';
$intro     = $elementData['intro'] ?? '';
$items     = $elementData['items'] ?? [];
$style     = $elementData['style'] ?? 'default';
$isCard    = $style === 'card';
$container = $elementData['container_width'] ?? 'container';
$sectionPad = $elementData['section_padding'] ?? '';

if (empty($items)) {
    return;
}

?>
<div class="<?= rex_escape($container ?: 'container') ?><?= $sectionPad ? ' ' . rex_escape($sectionPad) : '' ?>">

    <?php if ($heading || $intro): ?>
        <div class="mb-4">
            <?php if ($heading): ?>
                <<?= $tag ?> class="mb-2"><?= rex_escape($heading) ?></<?= $tag ?>>
            <?php endif; ?>
            <?php if ($intro): ?>
                <p class="text-muted"><?= nl2br(rex_escape($intro)) ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="cb-timeline-bs">
        <?php foreach ($items as $item):
            $date      = $item['date'] ?? '';
            $title     = $item['title'] ?? '';
            $text      = $item['text'] ?? '';
            $badge     = $item['badge'] ?? '';
            $highlight = !empty($item['highlight']);

            if (empty($title)) {
                continue;
            }
        ?>
        <div class="cb-timeline-bs__item d-flex gap-3 pb-4<?= $highlight ? ' fw-bold' : '' ?>">
            <div class="cb-timeline-bs__marker d-flex flex-column align-items-center">
                <div class="cb-timeline-bs__dot rounded-circle bg-primary d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:32px;height:32px;"></div>
                <div class="cb-timeline-bs__line flex-fill border-start border-primary border-opacity-25 ms-0" style="width:1px;"></div>
            </div>
            <div class="<?= $isCard ? 'card mb-2 p-3 flex-fill' : 'flex-fill' ?>">
                <?php if ($date || $badge): ?>
                    <div class="text-muted small mb-1">
                        <?= rex_escape($date) ?>
                        <?php if ($badge): ?>
                            <span class="badge bg-primary ms-2"><?= rex_escape($badge) ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <h6 class="mb-1"><?= rex_escape($title) ?></h6>
                <?php if ($text): ?>
                    <p class="mb-0 text-muted"><?= nl2br(rex_escape($text)) ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<style>
.cb-timeline-bs__item:last-child .cb-timeline-bs__line { display: none !important; }
</style>
