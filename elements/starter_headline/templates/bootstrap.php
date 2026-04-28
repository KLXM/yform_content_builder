<?php
$text = trim((string) ($elementData['text'] ?? ''));
$subline = trim((string) ($elementData['subline'] ?? ''));
$tag = (string) ($elementData['tag'] ?? 'h2');

$allowedTags = ['h1', 'h2', 'h3', 'h4'];
if (!in_array($tag, $allowedTags, true)) {
    $tag = 'h2';
}

if ($text === '') {
    return;
}
?>
<<?= $tag ?>><?= rex_escape($text) ?></<?= $tag ?>>
<?php if ($subline !== ''): ?>
<p class="text-muted"><?= rex_escape($subline) ?></p>
<?php endif; ?>
