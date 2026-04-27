<?php
/**
 * Kontakt-Picker – Plain Template
 *
 * @var array<string,mixed> $elementData
 */

if (!class_exists('YformListRenderer')) {
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
    return;
}

$elementData['layout'] = (string) ($elementData['layout'] ?? 'contact_compact');
$result = YformListRenderer::fetchPicked($picks, $elementData);

$headline = (string) ($elementData['headline'] ?? '');
$description = (string) ($elementData['description'] ?? '');
$showLinks = !empty($elementData['show_links']);

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
} elseif ('list' === $layout) {
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
}

echo '</section>';
