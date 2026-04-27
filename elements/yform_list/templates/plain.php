<?php
/**
 * YForm-Liste – Plain Template (framework-neutral, einfaches HTML)
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

echo '<section class="rex-yfl rex-yfl-' . rex_escape($layout) . '">';

if ('' !== $headline) {
    echo '<h2>' . rex_escape($headline) . '</h2>';
}
if ('' !== $description) {
    echo '<p class="rex-yfl-intro">' . nl2br(rex_escape($description)) . '</p>';
}

if (null !== $error) {
    echo '<p class="rex-yfl-error"><em>' . rex_escape($error) . '</em></p>';
} elseif ([] === $items) {
    echo '<p class="rex-yfl-empty">Keine Einträge.</p>';
} else {
    echo '<ul class="rex-yfl-items">';
    foreach ($items as $it) {
        $title = rex_escape((string) $it['title']);
        $teaser = rex_escape((string) $it['teaser']);
        $href = $showLinks ? (string) $it['href'] : '';
        $img = YformListRenderer::imgTag($it);
        $titleHtml = '' !== $href
            ? '<a href="' . rex_escape($href) . '">' . $title . '</a>'
            : $title;
        echo '<li class="rex-yfl-item">'
            . ('' !== $img && 'compact' !== $layout ? '<div class="rex-yfl-image">' . $img . '</div>' : '')
            . '<h3 class="rex-yfl-title">' . $titleHtml . '</h3>'
            . ('' !== $teaser && 'compact' !== $layout ? '<p class="rex-yfl-teaser">' . $teaser . '</p>' : '')
            . '</li>';
    }
    echo '</ul>';
}

echo '</section>';
