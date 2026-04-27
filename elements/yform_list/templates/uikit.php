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
        // Erkennung: Wenn Kontakt-Felder gemappt sind -> mehrspaltige Kontakt-Tabelle.
        $isContactList = false;
        foreach ($items as $it) {
            $c = (array) ($it['contact'] ?? []);
            if ('' !== trim((string) ($c['firstname'] ?? ''))
                || '' !== trim((string) ($c['phone'] ?? ''))
                || '' !== trim((string) ($c['mobile'] ?? ''))
                || '' !== trim((string) ($c['email'] ?? ''))
                || '' !== trim((string) ($c['role'] ?? ''))
            ) {
                $isContactList = true;
                break;
            }
        }
        if ($isContactList) {
            $tel = static fn(string $v): string => preg_replace('/[^+\d]/', '', $v) ?? '';
            echo '<div class="uk-overflow-auto">';
            echo '<table class="uk-table uk-table-middle uk-table-divider uk-table-responsive rex-yfl-contact-table">';
            echo '<thead><tr>'
                . '<th class="uk-table-shrink"></th>'
                . '<th>Name</th>'
                . '<th>Funktion</th>'
                . '<th>Telefon</th>'
                . '<th>E-Mail</th>'
                . '<th>Mobil</th>'
                . '</tr></thead><tbody>';
            foreach ($items as $it) {
                $contact = (array) ($it['contact'] ?? []);
                $first = trim((string) ($contact['firstname'] ?? ''));
                $last = trim((string) ($contact['lastname'] ?? $it['title'] ?? ''));
                $role = trim((string) ($contact['role'] ?? ''));
                $phone = trim((string) ($contact['phone'] ?? ''));
                $mobile = trim((string) ($contact['mobile'] ?? ''));
                $email = trim((string) ($contact['email'] ?? ''));
                $name = trim($first . ' ' . $last);
                if ('' === $name) {
                    $name = (string) $it['title'];
                }
                $href = $showLinks ? (string) $it['href'] : '';
                $img = YformListRenderer::imgTag($it, 'uk-border-circle uk-preserve-width');
                $nameHtml = '' !== $href
                    ? '<a href="' . rex_escape($href) . '" class="uk-link-reset">' . rex_escape($name) . '</a>'
                    : rex_escape($name);
                echo '<tr>'
                    . '<td class="uk-table-shrink">' . $img . '</td>'
                    . '<td><strong>' . $nameHtml . '</strong></td>'
                    . '<td>' . ('' !== $role ? rex_escape($role) : '&mdash;') . '</td>'
                    . '<td>' . ('' !== $phone ? '<a href="tel:' . rex_escape($tel($phone)) . '">' . rex_escape($phone) . '</a>' : '&mdash;') . '</td>'
                    . '<td>' . ('' !== $email ? '<a href="mailto:' . rex_escape($email) . '">' . rex_escape($email) . '</a>' : '&mdash;') . '</td>'
                    . '<td>' . ('' !== $mobile ? '<a href="tel:' . rex_escape($tel($mobile)) . '">' . rex_escape($mobile) . '</a>' : '&mdash;') . '</td>'
                    . '</tr>';
            }
            echo '</tbody></table></div>';
        } else {
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
        }
    } elseif ('contact' === $layout) {
        // Kontakt-Karten: Avatar + Name + Funktion + Kontaktdaten
        $colClass = 'uk-child-width-1-' . rex_escape($columnsMobile)
            . ' uk-child-width-1-' . rex_escape($columnsTablet) . '@s'
            . ' uk-child-width-1-' . rex_escape($columns) . '@m';
        echo '<div class="' . $colClass . '" uk-grid uk-height-match="target: > div > .rex-yfl-contact">';
        foreach ($items as $it) {
            $contact = (array) ($it['contact'] ?? []);
            $first = trim((string) ($contact['firstname'] ?? ''));
            $last = trim((string) ($contact['lastname'] ?? $it['title'] ?? ''));
            $freitext = trim((string) ($contact['freitext'] ?? ''));
            $role = trim((string) ($contact['role'] ?? ''));
            $phone = trim((string) ($contact['phone'] ?? ''));
            $mobile = trim((string) ($contact['mobile'] ?? ''));
            $email = trim((string) ($contact['email'] ?? ''));
            $name = trim($first . ' ' . $last);
            $img = YformListRenderer::imgTag($it, 'rex-yfl-contact-avatar uk-border-circle');
            $tel = static fn(string $v): string => preg_replace('/[^+\d]/', '', $v) ?? '';

            echo '<div>';
            echo '<div class="uk-card uk-card-default uk-card-body uk-text-center rex-yfl-contact">';
            if ('' !== $img) {
                echo '<div class="uk-margin-small-bottom" style="display:inline-block;">' . $img . '</div>';
            }
            echo '<h3 class="uk-card-title uk-margin-remove-bottom">' . rex_escape($name) . '</h3>';
            if ('' !== $freitext) {
                echo '<div class="uk-text-meta">' . rex_escape($freitext) . '</div>';
            }
            if ('' !== $role) {
                echo '<div class="uk-margin-small-top"><strong>' . rex_escape($role) . '</strong></div>';
            }
            $meta = [];
            if ('' !== $phone) {
                $meta[] = '<li><span uk-icon="receiver"></span> <a href="tel:' . rex_escape($tel($phone)) . '">' . rex_escape($phone) . '</a></li>';
            }
            if ('' !== $mobile) {
                $meta[] = '<li><span uk-icon="tablet"></span> <a href="tel:' . rex_escape($tel($mobile)) . '">' . rex_escape($mobile) . '</a></li>';
            }
            if ('' !== $email) {
                $meta[] = '<li><span uk-icon="mail"></span> <a href="mailto:' . rex_escape($email) . '">' . rex_escape($email) . '</a></li>';
            }
            if ([] !== $meta) {
                echo '<ul class="uk-list uk-margin-small-top rex-yfl-contact-meta">' . implode('', $meta) . '</ul>';
            }
            echo '</div></div>';
        }
        echo '</div>';
    } elseif ('contact_compact' === $layout) {
        // Kompakte Kontakt-Card mit Avatar im Header (UIkit-Beispiel-Markup).
        $colClass = 'uk-child-width-1-' . rex_escape($columnsMobile)
            . ' uk-child-width-1-' . rex_escape($columnsTablet) . '@s'
            . ' uk-child-width-1-' . rex_escape($columns) . '@m';
        echo '<div class="' . $colClass . '" uk-grid uk-height-match="target: > div > .uk-card">';
        foreach ($items as $it) {
            $contact = (array) ($it['contact'] ?? []);
            $first = trim((string) ($contact['firstname'] ?? ''));
            $last = trim((string) ($contact['lastname'] ?? $it['title'] ?? ''));
            $freitext = trim((string) ($contact['freitext'] ?? ''));
            $role = trim((string) ($contact['role'] ?? ''));
            $phone = trim((string) ($contact['phone'] ?? ''));
            $mobile = trim((string) ($contact['mobile'] ?? ''));
            $email = trim((string) ($contact['email'] ?? ''));
            $name = trim($first . ' ' . $last);
            // Bewusst ohne width/height – Cropping kommt aus dem MM-Typ.
            $img = YformListRenderer::imgTag($it, 'uk-border-circle uk-preserve-width');
            $tel = static fn(string $v): string => preg_replace('/[^+\d]/', '', $v) ?? '';
            $hrefMain = $showLinks ? (string) $it['href'] : '';
            $nameHtml = '' !== $hrefMain
                ? '<a href="' . rex_escape($hrefMain) . '" class="uk-link-reset">' . rex_escape($name) . '</a>'
                : rex_escape($name);

            echo '<div>';
            echo '<div class="uk-card uk-card-default">';
            echo '<div class="uk-card-header">';
            echo '<div class="uk-grid-small uk-flex-middle" uk-grid>';
            if ('' !== $img) {
                echo '<div class="uk-width-auto">' . $img . '</div>';
            }
            echo '<div class="uk-width-expand">';
            echo '<h3 class="uk-card-title uk-margin-remove-bottom">' . $nameHtml . '</h3>';
            if ('' !== $role) {
                echo '<p class="uk-text-meta uk-margin-remove-top">' . rex_escape($role) . '</p>';
            }
            if ('' !== $freitext) {
                echo '<p class="uk-text-meta uk-margin-remove">' . rex_escape($freitext) . '</p>';
            }
            echo '</div>';
            echo '</div>';
            echo '</div>';
            $hasMeta = '' !== $phone || '' !== $mobile || '' !== $email;
            if ($hasMeta) {
                echo '<div class="uk-card-body">';
                echo '<ul class="uk-list uk-margin-remove rex-yfl-contact-meta">';
                if ('' !== $phone) {
                    echo '<li><span uk-icon="receiver"></span> <a href="tel:' . rex_escape($tel($phone)) . '">' . rex_escape($phone) . '</a></li>';
                }
                if ('' !== $mobile) {
                    echo '<li><span uk-icon="tablet"></span> <a href="tel:' . rex_escape($tel($mobile)) . '">' . rex_escape($mobile) . '</a></li>';
                }
                if ('' !== $email) {
                    echo '<li><span uk-icon="mail"></span> <a href="mailto:' . rex_escape($email) . '">' . rex_escape($email) . '</a></li>';
                }
                echo '</ul>';
                echo '</div>';
            }
            echo '</div></div>';
        }
        echo '</div>';
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
