<?php
/**
 * Kontakt-Picker – UIkit Template
 *
 * Reicht die Picks an YformListRenderer::fetchPicked weiter und nutzt
 * danach dieselbe Render-Logik wie das yform_list-UIkit-Template.
 *
 * @var array<string,mixed> $elementData
 */

if (!class_exists(\KLXM\YFormContentBuilder\ListRenderer::class)) {
    return;
}

$rawItems = $elementData['items'] ?? [];
if (is_string($rawItems)) {
    $decoded = json_decode($rawItems, true);
    $rawItems = is_array($decoded) ? $decoded : [];
}
$picks = [];
foreach ((array) $rawItems as $item) {
    if (!is_array($item)) {
        continue;
    }
    $c = (string) ($item['contact'] ?? '');
    if ('' !== trim($c)) {
        $picks[] = trim($c);
    }
}

if ([] === $picks) {
    echo '<!-- contact_picker: keine Kontakte gewählt -->';
    return;
}

// Element-Layout durchreichen
$elementData['layout'] = (string) ($elementData['layout'] ?? 'contact_compact');
$result = \KLXM\YFormContentBuilder\ListRenderer::fetchPicked($picks, $elementData);

$headline = (string) ($elementData['headline'] ?? '');
$description = (string) ($elementData['description'] ?? '');
$showLinks = !empty($elementData['show_links']);

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

$columns = (string) ($elementData['columns'] ?? '2');
$columnsTablet = (string) ($elementData['columns_tablet'] ?? '2');
$columnsMobile = (string) ($elementData['columns_mobile'] ?? '1');
$gap = (string) ($elementData['gap'] ?? 'medium');

echo $wrapper->parse('ycb_elements/wrapper.php');

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
    // Wir delegieren an dieselbe Branch-Logik wie yform_list/uikit.php, indem wir
    // dieses Template inline einbinden – sauberer Fallback ohne Code-Duplizierung.
    // Kontext-Variablen sind bereits gesetzt. yform_list/uikit.php würde aber
    // erneut fetch() rufen – deshalb rendern wir hier inline kompakt.
    if ('contact_compact' === $layout) {
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
            $img = \KLXM\YFormContentBuilder\ListRenderer::imgTag($it, 'uk-border-circle uk-preserve-width');
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
    } elseif ('contact' === $layout) {
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
            $img = \KLXM\YFormContentBuilder\ListRenderer::imgTag($it, 'rex-yfl-contact-avatar uk-border-circle');
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
    } else { // list (kontakt-orientiert, mehrspaltige Tabelle)
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
            $img = \KLXM\YFormContentBuilder\ListRenderer::imgTag($it, 'uk-border-circle uk-preserve-width');
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
    }
}

echo $wrapperClose->parse('ycb_elements/wrapper.php');
