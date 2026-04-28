<?php
/**
 * UIkit Template für Akkordeon/Tabs Element - Erweiterte Version
 * @var array $elementData
 */

$displayType = $elementData['display_type'] ?? 'accordion';
$style = $elementData['style'] ?? 'default';
$items = $elementData['items'] ?? [];
$uniqueId = uniqid('acc_');

// Accordion-Optionen
$collapsible = !empty($elementData['accordion_collapsible']);
$multiple = !empty($elementData['accordion_multiple']);
$animation = ($elementData['accordion_animation'] ?? 'true') === 'true';
$firstOpen = $elementData['first_open'] ?? true;

// Tab-Optionen
$tabStyle = $elementData['tab_style'] ?? 'default';
$tabAlignment = $elementData['tab_alignment'] ?? 'left';

// Sektion-Einstellungen
$sectionBg = $elementData['section_bg'] ?? '';
$sectionPadding = $elementData['section_padding'] ?? '';
$containerWidth = $elementData['container_width'] ?? 'uk-container';

if (empty($items)) {
    return;
}

// Style-Mapping für UIkit
$styleMap = [
    'default' => 'uk-card-default',
    'primary' => 'uk-card-primary',
    'secondary' => 'uk-card-secondary',
    'muted' => 'uk-card-muted',
];
$styleClass = $styleMap[$style] ?? '';

// Tab-Style Klassen
$tabStyleClasses = ['uk-subnav'];
switch ($tabStyle) {
    case 'pill':
        $tabStyleClasses[] = 'uk-subnav-pill';
        break;
    case 'divider':
        $tabStyleClasses[] = 'uk-subnav-divider';
        break;
    default:
        $tabStyleClasses[] = 'uk-subnav-default';
        break;
}

// Tab-Alignment Klassen
switch ($tabAlignment) {
    case 'center':
        $tabStyleClasses[] = 'uk-flex-center';
        break;
    case 'right':
        $tabStyleClasses[] = 'uk-flex-right';
        break;
    case 'expand':
        $tabStyleClasses = ['uk-tab', 'uk-child-width-expand'];
        break;
}

// Accordion uk-accordion Attribute
$accordionAttrs = [];
if ($collapsible) {
    $accordionAttrs[] = 'collapsible: true';
}
if ($multiple) {
    $accordionAttrs[] = 'multiple: true';
}
if (!$animation) {
    $accordionAttrs[] = 'animation: false';
}
$accordionAttrStr = empty($accordionAttrs) ? '' : implode('; ', $accordionAttrs);

// Sektion-Klassen
$sectionClasses = ['uk-section'];
if ($sectionBg) {
    $sectionClasses[] = $sectionBg;
}
if ($sectionPadding) {
    $sectionClasses[] = $sectionPadding;
}

// Icon Rendering Helper
$renderIcon = function($icon) {
    if (empty($icon)) {
        return '';
    }
    // Font Awesome
    if (strpos($icon, 'fa-') !== false) {
        return '<i class="fa ' . rex_escape($icon) . ' uk-margin-small-right"></i>';
    }
    // UIkit Icon
    $iconName = preg_replace('/^(uk-icon-|icon-)/', '', $icon);
    return '<span uk-icon="icon: ' . rex_escape($iconName) . '" class="uk-margin-small-right"></span>';
};
?>

<?php if ($sectionBg || $sectionPadding): ?>
<section class="<?= implode(' ', $sectionClasses) ?>">
<?php endif; ?>

<?php if ($containerWidth): ?>
<div class="<?= $containerWidth ?>">
<?php endif; ?>

<?php if ($displayType === 'tabs'): ?>
    <!-- TABS (Horizontal) -->
    <div class="tabs-element" uk-scrollspy="cls: uk-animation-fade">
        <ul class="<?= implode(' ', $tabStyleClasses) ?>" uk-switcher="animation: uk-animation-fade">
            <?php foreach ($items as $index => $item): ?>
                <?php $isDisabled = !empty($item['disabled']); ?>
                <li<?= $index === 0 ? ' class="uk-active"' : '' ?><?= $isDisabled ? ' class="uk-disabled"' : '' ?>>
                    <a href="#">
                        <?= $renderIcon($item['icon'] ?? '') ?>
                        <?= rex_escape($item['title'] ?? 'Tab ' . ($index + 1)) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <ul class="uk-switcher uk-margin">
            <?php foreach ($items as $index => $item): ?>
                <li>
                    <div class="uk-card uk-card-body <?= $styleClass ?>">
                        <?php if (!empty($item['image'])): ?>
                            <?php $resolvedImageAlt = YFormContentBuilderMediaAltResolver::resolve((string) $item['image'], '', (string) ($item['title'] ?? '')); ?>
                            <img src="<?= rex_media_manager::getUrl('content_card', $item['image']) ?>" 
                                 alt="<?= rex_escape($resolvedImageAlt) ?>" 
                                 class="uk-margin-bottom" loading="lazy">
                        <?php endif; ?>
                        <?= $item['content'] ?? '' ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

<?php elseif ($displayType === 'tabs-left'): ?>
    <!-- TABS (Vertikal links) -->
    <div class="tabs-element-left" uk-scrollspy="cls: uk-animation-fade">
        <div class="uk-grid-small uk-child-width-expand@s" uk-grid>
            <div class="uk-width-auto@m">
                <ul class="uk-tab-left" uk-tab="connect: #<?= $uniqueId ?>-tab-content; animation: uk-animation-fade">
                    <?php foreach ($items as $index => $item): ?>
                        <?php $isDisabled = !empty($item['disabled']); ?>
                        <li<?= $index === 0 ? ' class="uk-active"' : '' ?><?= $isDisabled ? ' class="uk-disabled"' : '' ?>>
                            <a href="#">
                                <?= $renderIcon($item['icon'] ?? '') ?>
                                <?= rex_escape($item['title'] ?? 'Tab ' . ($index + 1)) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="uk-width-expand@m">
                <ul id="<?= $uniqueId ?>-tab-content" class="uk-switcher">
                    <?php foreach ($items as $index => $item): ?>
                        <li>
                            <div class="uk-card uk-card-body <?= $styleClass ?>">
                                <?php if (!empty($item['image'])): ?>
                                    <?php $resolvedImageAlt = YFormContentBuilderMediaAltResolver::resolve((string) $item['image'], '', (string) ($item['title'] ?? '')); ?>
                                    <img src="<?= rex_media_manager::getUrl('content_card', $item['image']) ?>" 
                                         alt="<?= rex_escape($resolvedImageAlt) ?>" 
                                         class="uk-margin-bottom" loading="lazy">
                                <?php endif; ?>
                                <?= $item['content'] ?? '' ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- ACCORDION -->
    <div class="accordion-element" uk-scrollspy="cls: uk-animation-fade">
        <ul uk-accordion<?= $accordionAttrStr ? '="' . $accordionAttrStr . '"' : '' ?>>
            <?php foreach ($items as $index => $item): ?>
                <?php 
                $title = $item['title'] ?? '';
                $content = $item['content'] ?? '';
                $isDisabled = !empty($item['disabled']);
                $openClass = ($firstOpen && $index === 0) ? ' uk-open' : '';
                $itemClasses = [$styleClass];
                if ($isDisabled) {
                    $itemClasses[] = 'uk-disabled';
                }
                ?>
                <li class="<?= implode(' ', array_filter($itemClasses)) ?><?= $openClass ?>">
                    <a class="uk-accordion-title<?= $isDisabled ? ' uk-text-muted' : '' ?>" href="#">
                        <?= $renderIcon($item['icon'] ?? '') ?>
                        <?= rex_escape($title) ?>
                    </a>
                    <div class="uk-accordion-content">
                        <?php if (!empty($item['image'])): ?>
                            <?php $resolvedImageAlt = YFormContentBuilderMediaAltResolver::resolve((string) $item['image'], '', (string) $title); ?>
                            <img src="<?= rex_media_manager::getUrl('content_card', $item['image']) ?>" 
                                 alt="<?= rex_escape($resolvedImageAlt) ?>" 
                                 class="uk-margin-bottom" loading="lazy">
                        <?php endif; ?>
                        <?= $content ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($containerWidth): ?>
</div>
<?php endif; ?>

<?php if ($sectionBg || $sectionPadding): ?>
</section>
<?php endif; ?>
