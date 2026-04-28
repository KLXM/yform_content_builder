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
} elseif ('contact' === $layout || 'contact_compact' === $layout) {
    echo '<ul class="rex-yfl-contacts">';
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
        $img = YformListRenderer::imgTag($it, 'rex-yfl-contact-avatar');
        echo '<li class="rex-yfl-contact">'
            . ('' !== $img ? '<div class="rex-yfl-contact-image">' . $img . '</div>' : '')
            . '<div class="rex-yfl-contact-body">'
            . '<div class="rex-yfl-contact-name">' . rex_escape($name) . '</div>'
            . ('' !== $freitext ? '<div class="rex-yfl-contact-freitext">' . rex_escape($freitext) . '</div>' : '')
            . ('' !== $role ? '<div class="rex-yfl-contact-role">' . rex_escape($role) . '</div>' : '')
            . '<ul class="rex-yfl-contact-meta">'
            . ('' !== $phone ? '<li class="phone">Tel.: <a href="tel:' . rex_escape(preg_replace('/[^+\d]/', '', $phone) ?? '') . '">' . rex_escape($phone) . '</a></li>' : '')
            . ('' !== $mobile ? '<li class="mobile">Mobil: <a href="tel:' . rex_escape(preg_replace('/[^+\d]/', '', $mobile) ?? '') . '">' . rex_escape($mobile) . '</a></li>' : '')
            . ('' !== $email ? '<li class="email"><a href="mailto:' . rex_escape($email) . '">' . rex_escape($email) . '</a></li>' : '')
            . '</ul>'
            . '</div>'
            . '</li>';
    }
    echo '</ul>';
} else {
    // Erkennung Kontakt-Liste -> mehrspaltige Tabelle
    $isContactList = false;
    if ('list' === $layout) {
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
    }
    if ($isContactList) {
        echo '<table class="rex-yfl-contact-table">';
        echo '<thead><tr><th></th><th>Name</th><th>Funktion</th><th>Telefon</th><th>E-Mail</th><th>Mobil</th></tr></thead><tbody>';
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
            $img = YformListRenderer::imgTag($it, 'rex-yfl-contact-avatar');
            $nameHtml = '' !== $href
                ? '<a href="' . rex_escape($href) . '">' . rex_escape($name) . '</a>'
                : rex_escape($name);
            echo '<tr>'
                . '<td>' . $img . '</td>'
                . '<td>' . $nameHtml . '</td>'
                . '<td>' . ('' !== $role ? rex_escape($role) : '') . '</td>'
                . '<td>' . ('' !== $phone ? '<a href="tel:' . rex_escape(preg_replace('/[^+\d]/', '', $phone) ?? '') . '">' . rex_escape($phone) . '</a>' : '') . '</td>'
                . '<td>' . ('' !== $email ? '<a href="mailto:' . rex_escape($email) . '">' . rex_escape($email) . '</a>' : '') . '</td>'
                . '<td>' . ('' !== $mobile ? '<a href="tel:' . rex_escape(preg_replace('/[^+\d]/', '', $mobile) ?? '') . '">' . rex_escape($mobile) . '</a>' : '') . '</td>'
                . '</tr>';
        }
        echo '</tbody></table>';
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
}

echo '</section>';
