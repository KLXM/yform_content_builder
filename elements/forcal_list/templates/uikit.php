<?php
/**
 * Forcal-Termine – UIkit Template
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
$groupBy = (string) ($elementData['group_by'] ?? '');
$headlineTag = (string) ($elementData['headline_tag'] ?? 'h2');
if (!in_array($headlineTag, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div'], true)) {
    $headlineTag = 'h2';
}
$headlineStyle = (string) ($elementData['headline_style'] ?? 'uk-heading-line');
if ('plain' === $headlineStyle) {
    $headlineStyle = '';
}
$groupTag = (string) ($elementData['group_heading_tag'] ?? 'h3');
if (!in_array($groupTag, ['h2', 'h3', 'h4', 'h5', 'h6', 'div'], true)) {
    $groupTag = 'h3';
}
$groupStyle = (string) ($elementData['group_heading_style'] ?? 'uk-heading-line');
if ('plain' === $groupStyle) {
    $groupStyle = '';
}

// Sub-Tag: eine Stufe kleiner als der Haupt-Tag, fuer Monats-Untertrenner im year_month-Modus.
$subTagOf = static function (string $tag): string {
    if (preg_match('/^h([1-6])$/', $tag, $m)) {
        return 'h' . min(6, (int) $m[1] + 1);
    }
    return $tag;
};
$subGroupTag = $subTagOf($groupTag);

/**
 * Render der Trenner-Überschrift abhängig vom gewählten Stil.
 */
$renderGroup = static function (string $label) use ($groupTag, $groupStyle): string {
    if ('' === $label) {
        return '';
    }
    $cls = trim($groupStyle . ' uk-margin-medium-top uk-margin-small-bottom');
    if ('uk-heading-line' === $groupStyle) {
        return '<' . $groupTag . ' class="' . rex_escape($cls) . '"><span>' . rex_escape($label) . '</span></' . $groupTag . '>';
    }
    return '<' . $groupTag . ' class="' . rex_escape($cls) . '">' . rex_escape($label) . '</' . $groupTag . '>';
};

/**
 * Render eines Sub-Trenners (z.B. Monat unter einem Jahr-Trenner) – schlanker.
 */
$renderSubGroup = static function (string $label) use ($subGroupTag): string {
    if ('' === $label) {
        return '';
    }
    return '<' . $subGroupTag . ' class="uk-margin-small-top uk-margin-small-bottom uk-text-bold">' . rex_escape($label) . '</' . $subGroupTag . '>';
};

$layout = $result['layout'];
$items = $result['items'];
$error = $result['error'];

/**
 * Erzeugt eine flache Liste von Render-Bloecken; jeder Block hat optionales
 * 'outer' (Jahr) und ein 'label' (Monat oder Jahr selbst).
 *
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
$gap = (string) ($elementData['gap'] ?? 'medium');

echo $wrapper->parse('ycb_elements/wrapper.php');

if ('' !== $headline) {
    $hCls = trim($headlineStyle . ' uk-margin-medium-bottom');
    if ('uk-heading-line' === $headlineStyle) {
        echo '<' . $headlineTag . ' class="' . rex_escape($hCls) . '"><span>' . rex_escape($headline) . '</span></' . $headlineTag . '>';
    } else {
        echo '<' . $headlineTag . ' class="' . rex_escape($hCls) . '">' . rex_escape($headline) . '</' . $headlineTag . '>';
    }
}
if ('' !== $description) {
    echo '<div class="uk-margin-medium-bottom uk-text-lead">' . nl2br(rex_escape($description)) . '</div>';
}

if (null !== $error) {
    echo '<div class="uk-alert uk-alert-warning" uk-alert><p>' . rex_escape($error) . '</p></div>';
} elseif ([] === $items) {
    echo '<div class="uk-alert uk-alert-default" uk-alert><p>Keine kommenden Termine.</p></div>';
} else {
    $grouped = $groupItems($items, $groupBy);

    if ('cards' === $layout) {
        $colClass = 'uk-child-width-1-' . rex_escape($columnsMobile)
            . ' uk-child-width-1-' . rex_escape($columnsTablet) . '@s'
            . ' uk-child-width-1-' . rex_escape($columns) . '@m';

        $lastOuter = null;
        foreach ($grouped as $block) {
            if ('' !== $block['outer'] && $block['outer'] !== $lastOuter) {
                echo $renderGroup($block['outer']);
                $lastOuter = $block['outer'];
            }
            if ('' !== $block['outer']) {
                echo $renderSubGroup($block['label']);
            } else {
                echo $renderGroup($block['label']);
            }
            echo '<div class="' . $colClass . '" uk-grid uk-height-match="target: > div > .uk-card">';
            foreach ($block['items'] as $it) {
                $title = rex_escape((string) $it['title']);
                $teaser = rex_escape((string) $it['teaser']);
                $href = $showLinks ? (string) $it['href'] : '';
                $color = (string) $it['category_color'];
                $dateStr = ForcalRenderer::formatDate($it);

                $titleHtml = '' !== $href
                    ? '<a href="' . rex_escape($href) . '" class="uk-link-reset">' . $title . '</a>'
                    : $title;

                $colorBar = '' !== $color
                    ? '<div style="height:4px;background:' . rex_escape($color) . ';"></div>'
                    : '';

                $imageUrl = (string) ($it['image_url'] ?? '');
                $mediaHtml = '';
                if ('' !== $imageUrl) {
                    $imgTag = '<img src="' . rex_escape($imageUrl) . '" alt="' . $title . '" loading="lazy">';
                    $mediaHtml = '<div class="uk-card-media-top">'
                        . ('' !== $href ? '<a href="' . rex_escape($href) . '">' . $imgTag . '</a>' : $imgTag)
                        . '</div>';
                }

                echo '<div>'
                    . '<div class="uk-card uk-card-default">'
                    . $colorBar
                    . $mediaHtml
                    . '<div class="uk-card-body">'
                    . '<div class="uk-text-meta uk-text-uppercase">' . $dateStr . '</div>'
                    . '<h3 class="uk-card-title uk-margin-small-top uk-margin-remove-bottom">' . $titleHtml . '</h3>'
                    . ('' !== $teaser ? '<p class="uk-margin-small-top">' . $teaser . '</p>' : '')
                    . ('' !== $it['venue'] ? '<div class="uk-text-meta uk-margin-small-top"><span uk-icon="icon: location"></span> ' . rex_escape((string) $it['venue']) . '</div>' : '')
                    . '</div>'
                    . '</div>'
                    . '</div>';
            }
            echo '</div>';
        }
    } elseif ('list' === $layout) {
        echo '<ul class="uk-list uk-list-divider rex-forcal-list">';
        $lastOuter = null;
        foreach ($grouped as $block) {
            $headerHtml = '';
            if ('' !== $block['outer'] && $block['outer'] !== $lastOuter) {
                $headerHtml .= $renderGroup($block['outer']);
                $lastOuter = $block['outer'];
            }
            if ('' !== $block['label']) {
                $headerHtml .= '' !== $block['outer']
                    ? $renderSubGroup($block['label'])
                    : $renderGroup($block['label']);
            }
            if ('' !== $headerHtml) {
                echo '</ul>' . $headerHtml . '<ul class="uk-list uk-list-divider rex-forcal-list">';
            }
            foreach ($block['items'] as $it) {
                $title = rex_escape((string) $it['title']);
                $teaser = rex_escape((string) $it['teaser']);
                $href = $showLinks ? (string) $it['href'] : '';
                $color = (string) $it['category_color'];
                $dateStr = ForcalRenderer::formatDate($it);

                $titleHtml = '' !== $href
                    ? '<a href="' . rex_escape($href) . '" class="uk-link-heading">' . $title . '</a>'
                    : $title;

                $imageUrl = (string) ($it['image_url'] ?? '');
                $thumbHtml = '' !== $imageUrl
                    ? '<div class="uk-width-auto"><img src="' . rex_escape($imageUrl) . '" alt="" loading="lazy" style="width:96px;height:64px;object-fit:cover;"></div>'
                    : '';

                echo '<li>'
                    . '<div class="uk-grid-small uk-flex-middle" uk-grid>'
                    . ('' !== $color
                        ? '<div class="uk-width-auto"><span style="display:inline-block;width:8px;height:32px;background:' . rex_escape($color) . ';border-radius:2px;"></span></div>'
                        : '')
                    . $thumbHtml
                    . '<div class="uk-width-expand">'
                    . '<div class="uk-text-meta">' . $dateStr . ('' !== $it['venue'] ? ' &middot; ' . rex_escape((string) $it['venue']) : '') . '</div>'
                    . '<h4 class="uk-margin-remove">' . $titleHtml . '</h4>'
                    . ('' !== $teaser ? '<p class="uk-margin-remove-top">' . $teaser . '</p>' : '')
                    . '</div>'
                    . '</div>'
                    . '</li>';
            }
        }
        echo '</ul>';
    } else {
        echo '<ul class="uk-list rex-forcal-compact">';
        $lastOuter = null;
        foreach ($grouped as $block) {
            $headerHtml = '';
            if ('' !== $block['outer'] && $block['outer'] !== $lastOuter) {
                $headerHtml .= $renderGroup($block['outer']);
                $lastOuter = $block['outer'];
            }
            if ('' !== $block['label']) {
                $headerHtml .= '' !== $block['outer']
                    ? $renderSubGroup($block['label'])
                    : $renderGroup($block['label']);
            }
            if ('' !== $headerHtml) {
                echo '</ul>' . $headerHtml . '<ul class="uk-list rex-forcal-compact">';
            }
            foreach ($block['items'] as $it) {
                $title = rex_escape((string) $it['title']);
                $href = $showLinks ? (string) $it['href'] : '';
                $dateStr = ForcalRenderer::formatDate($it);

                $titleHtml = '' !== $href
                    ? '<a href="' . rex_escape($href) . '">' . $title . '</a>'
                    : $title;

                echo '<li>'
                    . '<span class="uk-text-meta uk-margin-small-right">' . $dateStr . '</span>'
                    . $titleHtml
                    . '</li>';
            }
        }
        echo '</ul>';
    }
}

echo $wrapperClose->parse('ycb_elements/wrapper.php');
