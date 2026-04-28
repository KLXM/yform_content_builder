<?php
/**
 * Forcal-Termine – Plain (Bootstrap-/CSS-frameworkneutral) Template
 *
 * @var array<string,mixed> $elementData
 */

if (!class_exists('ForcalListRenderer')) {
    return;
}

$result = ForcalListRenderer::fetch($elementData);

$headline = (string) ($elementData['headline'] ?? '');
$description = (string) ($elementData['description'] ?? '');
$showLinks = !isset($elementData['show_links']) || !empty($elementData['show_links']);
$groupBy = (string) ($elementData['group_by'] ?? '');
$headlineTag = (string) ($elementData['headline_tag'] ?? 'h2');
if (!in_array($headlineTag, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div'], true)) {
    $headlineTag = 'h2';
}
$groupTag = (string) ($elementData['group_heading_tag'] ?? 'h3');
if (!in_array($groupTag, ['h2', 'h3', 'h4', 'h5', 'h6', 'div'], true)) {
    $groupTag = 'h3';
}
$subTagOf = static function (string $tag): string {
    if (preg_match('/^h([1-6])$/', $tag, $m)) {
        return 'h' . min(6, (int) $m[1] + 1);
    }
    return $tag;
};
$subGroupTag = $subTagOf($groupTag);

$layout = $result['layout'];
$items = $result['items'];
$error = $result['error'];

/**
 * @param list<array<string,mixed>> $items
 * @return list<array{outer:string,label:string,items:list<array<string,mixed>>}>
 */
$groupItems = static function (array $items, string $mode): array {
    $modes = ['month', 'year', 'year_month'];
    if (!in_array($mode, $modes, true)) {
        return [['outer' => '', 'label' => '', 'items' => $items]];
    }
    $monthsDe = [
        1 => 'Januar', 2 => 'Februar', 3 => 'März', 4 => 'April',
        5 => 'Mai', 6 => 'Juni', 7 => 'Juli', 8 => 'August',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Dezember',
    ];
    /** @var array<string, array{outer:string,label:string,items:list<array<string,mixed>>}> $blocks */
    $blocks = [];
    foreach ($items as $it) {
        $start = $it['start'] ?? null;
        if (!$start instanceof DateTimeInterface) {
            $outer = '';
            $label = '';
        } else {
            $year = $start->format('Y');
            $monthLabel = $monthsDe[(int) $start->format('n')] . ' ' . $year;
            if ('year' === $mode) {
                $outer = '';
                $label = $year;
            } elseif ('month' === $mode) {
                $outer = '';
                $label = $monthLabel;
            } else {
                $outer = $year;
                $label = $monthsDe[(int) $start->format('n')];
            }
        }
        $key = $outer . '|' . $label;
        if (!isset($blocks[$key])) {
            $blocks[$key] = ['outer' => $outer, 'label' => $label, 'items' => []];
        }
        $blocks[$key]['items'][] = $it;
    }
    return array_values($blocks);
};

echo '<section class="rex-forcal-list-section"';
$plainBgImage = (string) ($elementData['section_bg_image'] ?? '');
if ('' !== $plainBgImage) {
    $plainExt = strtolower(pathinfo($plainBgImage, PATHINFO_EXTENSION));
    if (!in_array($plainExt, ['mp4', 'webm'], true)) {
        echo ' style="background-image:url(\'' . rex_escape(rex_url::media($plainBgImage), 'html_attr') . '\');background-size:cover;background-position:center;"';
    }
}
echo '>';

if ('' !== $headline) {
    echo '<' . $headlineTag . '>' . rex_escape($headline) . '</' . $headlineTag . '>';
}
if ('' !== $description) {
    echo '<p class="lead">' . nl2br(rex_escape($description)) . '</p>';
}

if (null !== $error) {
    echo '<div class="alert alert-warning">' . rex_escape($error) . '</div>';
} elseif ([] === $items) {
    echo '<p>Keine kommenden Termine.</p>';
} else {
    $grouped = $groupItems($items, $groupBy);

    if ('cards' === $layout) {
        $lastOuter = null;
        foreach ($grouped as $block) {
            if ('' !== $block['outer'] && $block['outer'] !== $lastOuter) {
                echo '<' . $groupTag . ' class="rex-forcal-group">' . rex_escape($block['outer']) . '</' . $groupTag . '>';
                $lastOuter = $block['outer'];
            }
            if ('' !== $block['label']) {
                $tag = '' !== $block['outer'] ? $subGroupTag : $groupTag;
                echo '<' . $tag . ' class="rex-forcal-group">' . rex_escape($block['label']) . '</' . $tag . '>';
            }
            echo '<div class="row rex-forcal-cards">';
            foreach ($block['items'] as $it) {
                $title = rex_escape((string) $it['title']);
                $teaser = rex_escape((string) $it['teaser']);
                $href = $showLinks ? (string) $it['href'] : '';
                $color = (string) $it['category_color'];
                $dateStr = ForcalListRenderer::formatDate($it);

                $titleHtml = '' !== $href
                    ? '<a href="' . rex_escape($href) . '">' . $title . '</a>'
                    : $title;

                echo '<div class="col-md-4">'
                    . '<div class="card rex-forcal-card"' . ('' !== $color ? ' style="border-top:4px solid ' . rex_escape($color) . ';"' : '') . '>'
                    . ('' !== (string) ($it['image_url'] ?? '')
                        ? '<img src="' . rex_escape((string) $it['image_url']) . '" class="card-img-top" alt="' . $title . '" loading="lazy">'
                        : '')
                    . '<div class="card-body">'
                    . '<small class="text-muted text-uppercase">' . $dateStr . '</small>'
                    . '<h3 class="card-title">' . $titleHtml . '</h3>'
                    . ('' !== $teaser ? '<p>' . $teaser . '</p>' : '')
                    . ('' !== $it['venue'] ? '<small class="text-muted">📍 ' . rex_escape((string) $it['venue']) . '</small>' : '')
                    . '</div>'
                    . '</div>'
                    . '</div>';
            }
            echo '</div>';
        }
    } elseif ('list' === $layout) {
        echo '<ul class="list-unstyled rex-forcal-list">';
        $lastOuter = null;
        foreach ($grouped as $block) {
            if ('' !== $block['outer'] && $block['outer'] !== $lastOuter) {
                echo '<li class="rex-forcal-group-header"><strong>' . rex_escape($block['outer']) . '</strong></li>';
                $lastOuter = $block['outer'];
            }
            if ('' !== $block['label']) {
                echo '<li class="rex-forcal-group-header"><strong>' . rex_escape($block['label']) . '</strong></li>';
            }
            foreach ($block['items'] as $it) {
                $title = rex_escape((string) $it['title']);
                $teaser = rex_escape((string) $it['teaser']);
                $href = $showLinks ? (string) $it['href'] : '';
                $color = (string) $it['category_color'];
                $dateStr = ForcalListRenderer::formatDate($it);

                $titleHtml = '' !== $href
                    ? '<a href="' . rex_escape($href) . '">' . $title . '</a>'
                    : $title;

                echo '<li class="rex-forcal-list-item"' . ('' !== $color ? ' style="border-left:4px solid ' . rex_escape($color) . ';padding-left:12px;"' : '') . '>'
                    . '<small class="text-muted">' . $dateStr . ('' !== $it['venue'] ? ' · ' . rex_escape((string) $it['venue']) : '') . '</small>'
                    . '<h4>' . $titleHtml . '</h4>'
                    . ('' !== $teaser ? '<p>' . $teaser . '</p>' : '')
                    . '</li>';
            }
        }
        echo '</ul>';
    } else {
        echo '<ul class="list-unstyled rex-forcal-compact">';
        $lastOuter = null;
        foreach ($grouped as $block) {
            if ('' !== $block['outer'] && $block['outer'] !== $lastOuter) {
                echo '<li class="rex-forcal-group-header"><strong>' . rex_escape($block['outer']) . '</strong></li>';
                $lastOuter = $block['outer'];
            }
            if ('' !== $block['label']) {
                echo '<li class="rex-forcal-group-header"><strong>' . rex_escape($block['label']) . '</strong></li>';
            }
            foreach ($block['items'] as $it) {
                $title = rex_escape((string) $it['title']);
                $href = $showLinks ? (string) $it['href'] : '';
                $dateStr = ForcalListRenderer::formatDate($it);

                $titleHtml = '' !== $href
                    ? '<a href="' . rex_escape($href) . '">' . $title . '</a>'
                    : $title;

                echo '<li><small class="text-muted">' . $dateStr . '</small> · ' . $titleHtml . '</li>';
            }
        }
        echo '</ul>';
    }
}

echo '</section>';
