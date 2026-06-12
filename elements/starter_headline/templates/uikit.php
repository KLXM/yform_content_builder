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
$sectionBg = (string) ($elementData['section_bg'] ?? '');
$sectionBgImage = (string) ($elementData['section_bg_image'] ?? '');
$containerWidth = (string) ($elementData['container_width'] ?? 'uk-container');
$sectionPadding = (string) ($elementData['section_padding'] ?? '');
$sectionLight = !empty($elementData['section_light']);
$enableSection = !isset($elementData['enable_section']) || !empty($elementData['enable_section']);
$enableContainer = !isset($elementData['enable_container']) || !empty($elementData['enable_container']);

$allowedTags = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
if (!in_array($tag, $allowedTags, true)) {
    $tag = 'h2';
}

if ($text === '') {
    return;
}

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
<?= $wrapper->parse('ycb_elements/wrapper.php') ?>

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

<?= $wrapperClose->parse('ycb_elements/wrapper.php') ?>
