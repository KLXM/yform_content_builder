<?php
/**
 * Accordion / Tabs Element - Bootstrap 5 Template
 * @var array $elementData
 */

$displayType = $elementData['display_type'] ?? 'accordion';
$style = $elementData['style'] ?? 'default';
$items = $elementData['items'] ?? [];
$uniqueId = uniqid('acc_');

// Accordion-Optionen
$collapsible = !empty($elementData['accordion_collapsible']);
$multiple = !empty($elementData['accordion_multiple']);
$firstOpen = $elementData['first_open'] ?? true;

// Tab-Optionen
$tabStyle = $elementData['tab_style'] ?? 'default';
$tabAlignment = $elementData['tab_alignment'] ?? 'left';

// Sektion-Einstellungen
$sectionBg = $elementData['section_bg'] ?? '';
$sectionPadding = $elementData['section_padding'] ?? '';
$containerWidth = $elementData['container_width'] ?? 'container';

if (empty($items)) {
    return;
}

// Style-Mapping für Bootstrap
$styleMap = [
    'default' => '',
    'primary' => 'bg-primary text-white',
    'secondary' => 'bg-secondary text-white',
    'muted' => 'bg-light',
];
$styleClass = $styleMap[$style] ?? '';

// Tab-Style Klassen
$tabStyleClasses = ['nav'];
switch ($tabStyle) {
    case 'pill':
        $tabStyleClasses[] = 'nav-pills';
        break;
    default:
        $tabStyleClasses[] = 'nav-tabs';
        break;
}

// Tab-Alignment Klassen
switch ($tabAlignment) {
    case 'center':
        $tabStyleClasses[] = 'justify-content-center';
        break;
    case 'right':
        $tabStyleClasses[] = 'justify-content-end';
        break;
    case 'expand':
        $tabStyleClasses[] = 'nav-fill';
        break;
}

// Container Mapping
$containerMap = [
    'uk-container' => 'container',
    'uk-container-small' => 'container-sm',
    'uk-container-large' => 'container-lg',
    'uk-container-xlarge' => 'container-xl',
    'uk-container-expand' => 'container-fluid',
    '' => '',
];
$containerClass = $containerMap[$containerWidth] ?? $containerWidth;

// Icon Rendering Helper
$renderIcon = function($icon) {
    if (empty($icon)) {
        return '';
    }
    // Font Awesome
    if (strpos($icon, 'fa-') !== false) {
        return '<i class="fa ' . rex_escape($icon) . ' me-2"></i>';
    }
    // Bootstrap Icons
    return '<i class="bi bi-' . rex_escape($icon) . ' me-2"></i>';
};

// Sektion-Klassen (Bootstrap Mapping von UIkit Klassen)
$sectionStyles = '';
if ($sectionBg) {
    // Einfache Konvertierung - kann erweitert werden
    if (strpos($sectionBg, 'primary') !== false) {
        $sectionStyles .= ' bg-primary text-white';
    } elseif (strpos($sectionBg, 'secondary') !== false) {
        $sectionStyles .= ' bg-secondary text-white';
    } elseif (strpos($sectionBg, 'muted') !== false) {
        $sectionStyles .= ' bg-light';
    }
}
$sectionPaddingClass = '';
if ($sectionPadding) {
    if (strpos($sectionPadding, 'large') !== false) {
        $sectionPaddingClass = 'py-5';
    } elseif (strpos($sectionPadding, 'small') !== false) {
        $sectionPaddingClass = 'py-2';
    } else {
        $sectionPaddingClass = 'py-4';
    }
}
?>

<?php if ($sectionBg || $sectionPadding): ?>
<section class="<?= trim($sectionStyles . ' ' . $sectionPaddingClass) ?>">
<?php endif; ?>

<?php if ($containerClass): ?>
<div class="<?= $containerClass ?>">
<?php endif; ?>

<?php if ($displayType === 'tabs'): ?>
    <!-- TABS (Horizontal) -->
    <div class="tabs-element">
        <ul class="<?= implode(' ', $tabStyleClasses) ?>" id="<?= $uniqueId ?>Tab" role="tablist">
            <?php foreach ($items as $index => $item): ?>
                <?php 
                $isDisabled = !empty($item['disabled']);
                $isActive = $index === 0;
                ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link<?= $isActive ? ' active' : '' ?><?= $isDisabled ? ' disabled' : '' ?>" 
                            id="<?= $uniqueId ?>-tab-<?= $index ?>"
                            data-bs-toggle="tab" 
                            data-bs-target="#<?= $uniqueId ?>-pane-<?= $index ?>" 
                            type="button" 
                            role="tab" 
                            aria-controls="<?= $uniqueId ?>-pane-<?= $index ?>" 
                            aria-selected="<?= $isActive ? 'true' : 'false' ?>"
                            <?= $isDisabled ? 'disabled' : '' ?>>
                        <?= $renderIcon($item['icon'] ?? '') ?>
                        <?= rex_escape($item['title'] ?? 'Tab ' . ($index + 1)) ?>
                    </button>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="tab-content mt-3" id="<?= $uniqueId ?>TabContent">
            <?php foreach ($items as $index => $item): ?>
                <div class="tab-pane fade<?= $index === 0 ? ' show active' : '' ?> <?= $styleClass ?>" 
                     id="<?= $uniqueId ?>-pane-<?= $index ?>" 
                     role="tabpanel" 
                     aria-labelledby="<?= $uniqueId ?>-tab-<?= $index ?>"
                     tabindex="0">
                    <?php if (!empty($item['image'])): ?>
                    <?php $resolvedImageAlt = YFormContentBuilderMediaAltResolver::resolve((string) $item['image'], '', (string) ($item['title'] ?? '')); ?>
                        <img src="<?= rex_media_manager::getUrl('content_card', $item['image']) ?>" 
                        alt="<?= rex_escape($resolvedImageAlt) ?>" 
                             class="img-fluid mb-3" loading="lazy">
                    <?php endif; ?>
                    <div class="p-3">
                        <?= $item['content'] ?? '' ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

<?php elseif ($displayType === 'tabs-left'): ?>
    <!-- TABS (Vertikal links) -->
    <div class="tabs-element-left">
        <div class="row">
            <div class="col-md-3">
                <div class="nav flex-column nav-pills" id="<?= $uniqueId ?>Tab" role="tablist" aria-orientation="vertical">
                    <?php foreach ($items as $index => $item): ?>
                        <?php 
                        $isDisabled = !empty($item['disabled']);
                        $isActive = $index === 0;
                        ?>
                        <button class="nav-link<?= $isActive ? ' active' : '' ?><?= $isDisabled ? ' disabled' : '' ?>" 
                                id="<?= $uniqueId ?>-tab-<?= $index ?>"
                                data-bs-toggle="pill" 
                                data-bs-target="#<?= $uniqueId ?>-pane-<?= $index ?>" 
                                type="button" 
                                role="tab" 
                                aria-controls="<?= $uniqueId ?>-pane-<?= $index ?>" 
                                aria-selected="<?= $isActive ? 'true' : 'false' ?>"
                                <?= $isDisabled ? 'disabled' : '' ?>>
                            <?= $renderIcon($item['icon'] ?? '') ?>
                            <?= rex_escape($item['title'] ?? 'Tab ' . ($index + 1)) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-md-9">
                <div class="tab-content" id="<?= $uniqueId ?>TabContent">
                    <?php foreach ($items as $index => $item): ?>
                        <div class="tab-pane fade<?= $index === 0 ? ' show active' : '' ?> <?= $styleClass ?>" 
                             id="<?= $uniqueId ?>-pane-<?= $index ?>" 
                             role="tabpanel" 
                             aria-labelledby="<?= $uniqueId ?>-tab-<?= $index ?>"
                             tabindex="0">
                            <?php if (!empty($item['image'])): ?>
                                  <?php $resolvedImageAlt = YFormContentBuilderMediaAltResolver::resolve((string) $item['image'], '', (string) ($item['title'] ?? '')); ?>
                                <img src="<?= rex_media_manager::getUrl('content_card', $item['image']) ?>" 
                                      alt="<?= rex_escape($resolvedImageAlt) ?>" 
                                     class="img-fluid mb-3" loading="lazy">
                            <?php endif; ?>
                            <?= $item['content'] ?? '' ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- ACCORDION (Bootstrap 5) -->
    <div class="accordion-element">
        <div class="accordion<?= $multiple ? '' : ' accordion-flush' ?>" id="<?= $uniqueId ?>">
            <?php foreach ($items as $index => $item): ?>
                <?php 
                $title = $item['title'] ?? '';
                $content = $item['content'] ?? '';
                $isDisabled = !empty($item['disabled']);
                $isOpen = $firstOpen && $index === 0;
                ?>
                <div class="accordion-item <?= $styleClass ?>">
                    <h2 class="accordion-header" id="heading-<?= $uniqueId ?>-<?= $index ?>">
                        <button class="accordion-button<?= !$isOpen ? ' collapsed' : '' ?><?= $isDisabled ? ' disabled' : '' ?>" 
                                type="button" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#collapse-<?= $uniqueId ?>-<?= $index ?>" 
                                aria-expanded="<?= $isOpen ? 'true' : 'false' ?>" 
                                aria-controls="collapse-<?= $uniqueId ?>-<?= $index ?>"
                                <?= $isDisabled ? 'disabled' : '' ?>>
                            <?= $renderIcon($item['icon'] ?? '') ?>
                            <?= rex_escape($title) ?>
                        </button>
                    </h2>
                    <div id="collapse-<?= $uniqueId ?>-<?= $index ?>" 
                         class="accordion-collapse collapse<?= $isOpen ? ' show' : '' ?>" 
                         aria-labelledby="heading-<?= $uniqueId ?>-<?= $index ?>"
                         <?= $multiple ? '' : 'data-bs-parent="#' . $uniqueId . '"' ?>>
                        <div class="accordion-body">
                            <?php if (!empty($item['image'])): ?>
                                <?php $resolvedImageAlt = YFormContentBuilderMediaAltResolver::resolve((string) $item['image'], '', (string) $title); ?>
                                <img src="<?= rex_media_manager::getUrl('content_card', $item['image']) ?>" 
                                     alt="<?= rex_escape($resolvedImageAlt) ?>" 
                                     class="img-fluid mb-3" loading="lazy">
                            <?php endif; ?>
                            <?= $content ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?php if ($containerClass): ?>
</div>
<?php endif; ?>

<?php if ($sectionBg || $sectionPadding): ?>
</section>
<?php endif; ?>

