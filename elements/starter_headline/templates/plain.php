<?php
$headlineData = $elementData['headline'] ?? [];
if (!is_array($headlineData)) {
    $headlineData = [];
}

$eyebrow = trim((string) ($headlineData['eyebrow'] ?? ''));
$text = trim((string) ($headlineData['text'] ?? ''));
$highlight = trim((string) ($headlineData['highlight'] ?? ''));
$subline = trim((string) ($headlineData['subline'] ?? ''));
$tag = (string) ($headlineData['tag'] ?? 'h2');

$allowedTags = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
if (!in_array($tag, $allowedTags, true)) {
    $tag = 'h2';
}

if ($text === '') {
    return;
}

$renderHeadline = static function (string $headline, string $highlightPart): string {
    if ($highlightPart === '') {
        return rex_escape($headline);
    }

    $pos = mb_stripos($headline, $highlightPart);
    if ($pos === false) {
        return rex_escape($headline);
    }

    $before = mb_substr($headline, 0, $pos);
    $match = mb_substr($headline, $pos, mb_strlen($highlightPart));
    $after = mb_substr($headline, $pos + mb_strlen($highlightPart));

    return rex_escape($before)
        . '<mark>' . rex_escape($match) . '</mark>'
        . rex_escape($after);
};
?>
<header>
<?php if ($eyebrow !== ''): ?>
<p><?= rex_escape($eyebrow) ?></p>
<?php endif; ?>
<<?= $tag ?>><?= $renderHeadline($text, $highlight) ?></<?= $tag ?>>
<?php if ($subline !== ''): ?>
<p><?= rex_escape($subline) ?></p>
<?php endif; ?>
</header>
