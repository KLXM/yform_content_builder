<?php
/**
 * YForm-Liste – UIkit Template
 *
 * @var array<string,mixed> $elementData
 */

if (!class_exists('YformListRenderer')) {
    return;
}

$result = YformListRenderer::fetch($elementData);

$headline = (string) ($elementData['headline'] ?? '');
$description = (string) ($elementData['description'] ?? '');
$showLinks = !isset($elementData['show_links']) || !empty($elementData['show_links']);

$layout = $result['layout'];
$items = $result['items'];
$error = $result['error'];

// Section-/Container-Wrapping (analog zu anderen Elementen)
$sectionBg = $elementData['section_bg'] ?? '';
$sectionPadding = $elementData['section_padding'] ?? '';
$containerWidth = $elementData['container_width'] ?? 'uk-container';

$sectionClasses = ['uk-section'];
if ('' !== $sectionPadding) { $sectionClasses[] = $sectionPadding; }
if ('' !== $sectionBg) { $sectionClasses[] = $sectionBg; }

$columns = (string) ($elementData['columns'] ?? '3');
$columnsTablet = (string) ($elementData['columns_tablet'] ?? '2');
$columnsMobile = (string) ($elementData['columns_mobile'] ?? '1');
$gap = (string) ($elementData['gap'] ?? 'medium');

echo '<section class="' . rex_escape(implode(' ', $sectionClasses)) . '">';
echo '<div class="' . rex_escape($containerWidth) . '">';

if ('' !== $headline) {
    echo '<h2 class="uk-heading-line uk-margin-medium-bottom"><span>' . rex_escape($headline) . '</span></h2>';
}
if ('' !== $description) {
    echo '<div class="uk-margin-medium-bottom uk-text-lead">' . nl2br(rex_escape($description)) . '</div>';
}

if (null !== $error) {
    echo '<div class="uk-alert uk-alert-warning" uk-alert><p>' . rex_escape($error) . '</p></div>';
} elseif ([] === $items) {
    echo '<div class="uk-alert uk-alert-default" uk-alert><p>Keine Einträge.</p></div>';
} else {
    if ('cards' === $layout) {
        $gridClass = 'uk-grid-' . rex_escape($gap);
        $colClass = 'uk-child-width-1-' . rex_escape($columnsMobile)
            . ' uk-child-width-1-' . rex_escape($columnsTablet) . '@s'
            . ' uk-child-width-1-' . rex_escape($columns) . '@m';

        echo '<div class="' . $colClass . '" uk-grid uk-height-match="target: > div > .uk-card">';
        foreach ($items as $it) {
            $title = rex_escape((string) $it['title']);
            $teaser = rex_escape((string) $it['teaser']);
            $href = $showLinks ? (string) $it['href'] : '';
            $img = YformListRenderer::imgTag($it, 'uk-card-media-top');
            $titleHtml = '' !== $href
                ? '<a href="' . rex_escape($href) . '" class="uk-link-reset">' . $title . '</a>'
                : $title;
            echo '<div>'
                . '<div class="uk-card uk-card-default">'
                . $img
                . '<div class="uk-card-body">'
                . '<h3 class="uk-card-title uk-margin-remove-bottom">' . $titleHtml . '</h3>'
                . ('' !== $teaser ? '<p class="uk-margin-small-top">' . $teaser . '</p>' : '')
                . '</div>'
                . '</div>'
                . '</div>';
        }
        echo '</div>';
    } elseif ('list' === $layout) {
        echo '<ul class="uk-list uk-list-divider rex-yfl-list">';
        foreach ($items as $it) {
            $title = rex_escape((string) $it['title']);
            $teaser = rex_escape((string) $it['teaser']);
            $href = $showLinks ? (string) $it['href'] : '';
            $img = YformListRenderer::imgTag($it, 'rex-yfl-thumb', 80);
            $titleHtml = '' !== $href
                ? '<a href="' . rex_escape($href) . '">' . $title . '</a>'
                : $title;
            echo '<li>'
                . '<div class="uk-flex uk-flex-middle" style="gap:1em;">'
                . ('' !== $img ? '<div style="flex:0 0 auto;width:80px;">' . $img . '</div>' : '')
                . '<div class="uk-flex-1">'
                . '<h4 class="uk-margin-remove">' . $titleHtml . '</h4>'
                . ('' !== $teaser ? '<p class="uk-margin-remove uk-text-meta">' . $teaser . '</p>' : '')
                . '</div>'
                . '</div>'
                . '</li>';
        }
        echo '</ul>';
    } else { // compact
        echo '<ul class="uk-list rex-yfl-compact">';
        foreach ($items as $it) {
            $title = rex_escape((string) $it['title']);
            $href = $showLinks ? (string) $it['href'] : '';
            $titleHtml = '' !== $href
                ? '<a href="' . rex_escape($href) . '">' . $title . '</a>'
                : $title;
            echo '<li>' . $titleHtml . '</li>';
        }
        echo '</ul>';
    }
}

echo '</div></section>';
