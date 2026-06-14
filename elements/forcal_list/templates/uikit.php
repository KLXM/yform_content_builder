<?php
/**
 * Forcal-Termine - UIkit Template
 *
 * @var array<string,mixed> $elementData
 */

use KLXM\YFormContentBuilder\ForcalRenderer;

if (!class_exists(ForcalRenderer::class)) {
    return;
}

$result = ForcalRenderer::fetch($elementData);

$headline = (string) ($elementData['headline'] ?? '');
$description = (string) ($elementData['description'] ?? '');
$showLinks = !isset($elementData['show_links']) || !empty($elementData['show_links']);
$layout = $result['layout'];
$items = $result['items'];
$error = $result['error'];

$sectionBg = $elementData['section_bg'] ?? '';
$sectionBgImage = (string) ($elementData['section_bg_image'] ?? '');
$sectionPadding = $elementData['section_padding'] ?? '';
$containerWidth = $elementData['container_width'] ?? 'uk-container';
$sectionLight = !empty($elementData['section_light']);
$enableSection = !isset($elementData['enable_section']) || !empty($elementData['enable_section']);
$enableContainer = !isset($elementData['enable_container']) || !empty($elementData['enable_container']);

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

$columns = (string) ($elementData['columns'] ?? '3');
$columnsTablet = (string) ($elementData['columns_tablet'] ?? '2');
$columnsMobile = (string) ($elementData['columns_mobile'] ?? '1');

echo $wrapper->parse('ycb_elements/wrapper.php');

if ($headline !== '') {
    echo '<h2 class="uk-heading-line uk-margin-medium-bottom"><span>' . rex_escape($headline) . '</span></h2>';
}
if ($description !== '') {
    echo '<div class="uk-margin-medium-bottom uk-text-lead">' . nl2br(rex_escape($description)) . '</div>';
}

if ($error !== null) {
    echo '<div class="uk-alert uk-alert-warning" uk-alert><p>' . rex_escape($error) . '</p></div>';
} elseif ($items === []) {
    echo '<div class="uk-alert uk-alert-default" uk-alert><p>Keine kommenden Termine.</p></div>';
} elseif ($layout === 'cards') {
    $colClass = 'uk-child-width-1-' . rex_escape($columnsMobile)
        . ' uk-child-width-1-' . rex_escape($columnsTablet) . '@s'
        . ' uk-child-width-1-' . rex_escape($columns) . '@m';

    echo '<div class="' . $colClass . '" uk-grid uk-height-match="target: > div > .uk-card">';
    foreach ($items as $it) {
        $title = rex_escape((string) $it['title']);
        $teaser = rex_escape((string) $it['teaser']);
        $href = $showLinks ? (string) $it['href'] : '';
        $dateStr = ForcalRenderer::formatDate($it);
        $imageUrl = (string) ($it['image_url'] ?? '');

        $titleHtml = $href !== '' ? '<a href="' . rex_escape($href) . '" class="uk-link-reset">' . $title . '</a>' : $title;
        $imgHtml = $imageUrl !== '' ? '<div class="uk-card-media-top"><img src="' . rex_escape($imageUrl) . '" alt="" loading="lazy"></div>' : '';

        echo '<div><div class="uk-card uk-card-default">' . $imgHtml . '<div class="uk-card-body">'
            . '<div class="uk-text-meta uk-text-uppercase">' . $dateStr . '</div>'
            . '<h3 class="uk-card-title uk-margin-small-top uk-margin-remove-bottom">' . $titleHtml . '</h3>'
            . ($teaser !== '' ? '<p class="uk-margin-small-top">' . $teaser . '</p>' : '')
            . '</div></div></div>';
    }
    echo '</div>';
} elseif ($layout === 'list') {
    echo '<ul class="uk-list uk-list-divider">';
    foreach ($items as $it) {
        $title = rex_escape((string) $it['title']);
        $teaser = rex_escape((string) $it['teaser']);
        $href = $showLinks ? (string) $it['href'] : '';
        $dateStr = ForcalRenderer::formatDate($it);
        $titleHtml = $href !== '' ? '<a href="' . rex_escape($href) . '">' . $title . '</a>' : $title;
        echo '<li><div class="uk-text-meta">' . $dateStr . '</div><h4 class="uk-margin-remove">' . $titleHtml . '</h4>'
            . ($teaser !== '' ? '<p class="uk-margin-remove-top">' . $teaser . '</p>' : '')
            . '</li>';
    }
    echo '</ul>';
} else {
    echo '<ul class="uk-list">';
    foreach ($items as $it) {
        $title = rex_escape((string) $it['title']);
        $href = $showLinks ? (string) $it['href'] : '';
        $dateStr = ForcalRenderer::formatDate($it);
        $titleHtml = $href !== '' ? '<a href="' . rex_escape($href) . '">' . $title . '</a>' : $title;
        echo '<li><span class="uk-text-meta uk-margin-small-right">' . $dateStr . '</span>' . $titleHtml . '</li>';
    }
    echo '</ul>';
}

echo $wrapperClose->parse('ycb_elements/wrapper.php');
