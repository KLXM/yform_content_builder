<?php

/**
 * Timeline Element - UIkit Template
 *
 * @var array $elementData
 */

// --- Inhalt ---
$heading = $elementData['heading'] ?? '';
$tag     = $elementData['tag'] ?? 'h2';
$intro   = $elementData['intro'] ?? '';
$items   = $elementData['items'] ?? [];

// --- Design ---
$style       = $elementData['style'] ?? 'default';
$iconDefault = $elementData['icon_default'] ?? 'circle';
$iconColor   = $elementData['icon_color'] ?? 'primary';
$lineColor   = $elementData['line_color'] ?? 'solid';

// --- Sektion ---
$sectionBg    = $elementData['section_bg'] ?? '';
$sectionBgImg = $elementData['section_bg_image'] ?? '';
$sectionPad   = $elementData['section_padding'] ?? '';
$container    = $elementData['container_width'] ?? 'uk-container';
$sectionLight = !empty($elementData['section_light']);
$enableSection = !isset($elementData['enable_section']) || !empty($elementData['enable_section']);
$enableContainer = !isset($elementData['enable_container']) || !empty($elementData['enable_container']);

if (empty($items)) {
    return;
}

$wrapper = new rex_fragment();
$wrapper->setVar('enable_section', $enableSection, false);
$wrapper->setVar('enable_container', $enableContainer, false);
$wrapper->setVar('section_bg', $sectionBg, false);
$wrapper->setVar('section_bg_image', $sectionBgImg, false);
$wrapper->setVar('section_padding', $sectionPad, false);
$wrapper->setVar('container_width', $container, false);
$wrapper->setVar('section_light', $sectionLight, false);

$wrapperClose = new rex_fragment();
$wrapperClose->setVar('mode', 'close', false);
$wrapperClose->setVar('enable_section', $enableSection, false);
$wrapperClose->setVar('enable_container', $enableContainer, false);
$wrapperClose->setVar('section_bg_image', $sectionBgImg, false);
$wrapperClose->setVar('container_width', $container, false);

// CSS-Variablen für Icon-Farbe
$colorMap = [
    'primary'   => 'var(--uk-color-primary, #1e87f0)',
    'secondary' => 'var(--uk-color-secondary, #222)',
    'success'   => '#32d296',
    'warning'   => '#faa05a',
    'danger'    => '#f0506e',
    'muted'     => '#999',
];
$dotColor = $colorMap[$iconColor] ?? $colorMap['primary'];

$borderStyle = match ($lineColor) {
    'dashed' => 'dashed',
    'dotted' => 'dotted',
    default  => 'solid',
};

$isAlternating = $style === 'alternating';
$isCard        = $style === 'card';

?>
<?= $wrapper->parse('ycb_elements/wrapper.php') ?>

    <?php if ($heading || $intro): ?>
        <div class="uk-margin-medium-bottom<?= $isAlternating ? ' uk-text-center' : '' ?>">
            <?php if ($heading): ?>
                <<?= $tag ?> class="uk-margin-small-bottom"><?= rex_escape($heading) ?></<?= $tag ?>>
            <?php endif; ?>
            <?php if ($intro): ?>
                <p class="uk-text-muted"><?= nl2br(rex_escape($intro)) ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="cb-timeline cb-timeline--<?= rex_escape($style) ?>" role="list">

        <?php foreach ($items as $i => $item):
            $date      = $item['date'] ?? '';
            $title     = $item['title'] ?? '';
            $text      = $item['text'] ?? '';
            $icon      = $item['icon'] ?? '';
            $badge     = $item['badge'] ?? '';
            $highlight = !empty($item['highlight']);

            if (empty($title)) {
                continue;
            }

            $isRight = $isAlternating && ($i % 2 !== 0);
        ?>
        <div class="cb-timeline__item<?= $highlight ? ' cb-timeline__item--highlight' : '' ?><?= $isRight ? ' cb-timeline__item--right' : '' ?>" role="listitem">

            <!-- Punkt + Linie -->
            <div class="cb-timeline__marker" aria-hidden="true">
                <div class="cb-timeline__dot">
                    <?php if ($icon): ?>
                        <span uk-icon="icon: <?= rex_escape($icon) ?>; ratio: 0.7"></span>
                    <?php elseif ($iconDefault !== 'none'): ?>
                        <?php if ($iconDefault !== 'circle'): ?>
                            <span uk-icon="icon: <?= rex_escape($iconDefault) ?>; ratio: 0.7"></span>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div class="cb-timeline__line"></div>
            </div>

            <!-- Inhalt -->
            <div class="cb-timeline__content<?= $isCard ? ' uk-card uk-card-default uk-card-body uk-card-small' : '' ?>">

                <?php if ($date || $badge): ?>
                    <div class="cb-timeline__meta uk-margin-small-bottom">
                        <?php if ($date): ?>
                            <span class="cb-timeline__date uk-text-muted uk-text-small"><?= rex_escape($date) ?></span>
                        <?php endif; ?>
                        <?php if ($badge): ?>
                            <span class="uk-badge uk-margin-small-left"><?= rex_escape($badge) ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <<?= rex_escape(in_array($tag, ['h2','h3','h4','p'], true) ? ($tag === 'h2' ? 'h3' : ($tag === 'h3' ? 'h4' : 'h5')) : 'h3') ?> class="cb-timeline__title uk-margin-remove-top uk-margin-small-bottom<?= $highlight ? ' uk-text-bold' : '' ?>">
                    <?= rex_escape($title) ?>
                </<?= rex_escape(in_array($tag, ['h2','h3','h4','p'], true) ? ($tag === 'h2' ? 'h3' : ($tag === 'h3' ? 'h4' : 'h5')) : 'h3') ?>>

                <?php if ($text): ?>
                    <p class="uk-margin-remove uk-text-muted"><?= nl2br(rex_escape($text)) ?></p>
                <?php endif; ?>

            </div>
        </div>
        <?php endforeach; ?>

    </div>
</div>
<?= $wrapperClose->parse('ycb_elements/wrapper.php') ?>

<style>
.cb-timeline {
    position: relative;
    padding: 0;
    list-style: none;
}
.cb-timeline__item {
    display: grid;
    grid-template-columns: 32px 1fr;
    gap: 0 20px;
    position: relative;
    padding-bottom: 32px;
}
.cb-timeline__item:last-child {
    padding-bottom: 0;
}
.cb-timeline__item:last-child .cb-timeline__line {
    display: none;
}
.cb-timeline__marker {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex-shrink: 0;
}
.cb-timeline__dot {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: <?= rex_escape($dotColor) ?>;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    flex-shrink: 0;
    z-index: 1;
    transition: transform 0.2s ease;
}
.cb-timeline__item--highlight .cb-timeline__dot {
    width: 38px;
    height: 38px;
    box-shadow: 0 0 0 4px color-mix(in srgb, <?= rex_escape($dotColor) ?> 25%, transparent);
}
.cb-timeline__line {
    flex: 1;
    width: 2px;
    background: color-mix(in srgb, <?= rex_escape($dotColor) ?> 30%, transparent);
    border-left: 2px <?= rex_escape($borderStyle) ?> color-mix(in srgb, <?= rex_escape($dotColor) ?> 30%, transparent);
    margin-top: 4px;
    min-height: 24px;
}
.cb-timeline__content {
    padding-bottom: 8px;
}
.cb-timeline__meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

/* Alternating */
@media (min-width: 960px) {
    .cb-timeline--alternating {
        max-width: 800px;
        margin-inline: auto;
    }
    .cb-timeline--alternating .cb-timeline__item {
        grid-template-columns: 1fr 32px 1fr;
        text-align: left;
    }
    .cb-timeline--alternating .cb-timeline__marker {
        order: 2;
    }
    .cb-timeline--alternating .cb-timeline__content {
        order: 1;
        text-align: right;
    }
    .cb-timeline--alternating .cb-timeline__item--right .cb-timeline__content {
        order: 3;
        text-align: left;
    }
    .cb-timeline--alternating .cb-timeline__line {
        display: none;
    }
    .cb-timeline--alternating::before {
        content: '';
        position: absolute;
        left: 50%;
        top: 0;
        bottom: 0;
        width: 2px;
        transform: translateX(-50%);
        background: color-mix(in srgb, <?= rex_escape($dotColor) ?> 30%, transparent);
        border-left: 2px <?= rex_escape($borderStyle) ?> color-mix(in srgb, <?= rex_escape($dotColor) ?> 30%, transparent);
    }
}

/* Dark Mode */
body.rex-theme-dark .cb-timeline__dot {
    box-shadow: none;
}
@media (prefers-color-scheme: dark) {
    body.rex-has-theme:not(.rex-theme-light) .cb-timeline__dot {
        box-shadow: none;
    }
}
</style>
