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
$containerWidth = (string) ($elementData['container_width'] ?? 'uk-container');
$sectionPadding = (string) ($elementData['section_padding'] ?? '');

$allowedTags = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
if (!in_array($tag, $allowedTags, true)) {
    $tag = 'h2';
}

if ($text === '') {
    return;
}

$sectionClass = trim((string) $sectionPadding);
$headlineSeed = $eyebrow . '|' . $text . '|' . $highlight . '|' . $subline . '|' . $tag;
$headlineId = 'headline-' . substr(md5($headlineSeed), 0, 10);
$sublineId = $headlineId . '-subline';

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
<section<?= $sectionClass !== '' ? ' class="' . rex_escape($sectionClass) . '"' : '' ?>>
    <?php if ($containerWidth !== ''): ?>
    <div class="<?= rex_escape($containerWidth) ?>">
    <?php endif; ?>

    <header>
        <?php if ($eyebrow !== ''): ?>
        <p class="uk-text-meta uk-text-uppercase uk-margin-small-bottom"><?= rex_escape($eyebrow) ?></p>
        <?php endif; ?>

        <<?= $tag ?> class="uk-margin-remove" id="<?= rex_escape($headlineId) ?>"<?= $subline !== '' ? ' aria-describedby="' . rex_escape($sublineId) . '"' : '' ?>>
            <?= $renderHeadline($text, $highlight) ?>
        </<?= $tag ?>>

        <?php if ($subline !== ''): ?>
        <p id="<?= rex_escape($sublineId) ?>" class="uk-text-lead uk-margin-xsmall-top uk-margin-remove-bottom"><?= rex_escape($subline) ?></p>
        <?php endif; ?>
    </header>

    <?php if ($containerWidth !== ''): ?>
    </div>
    <?php endif; ?>
</section>
