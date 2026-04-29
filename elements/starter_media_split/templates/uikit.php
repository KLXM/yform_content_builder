<?php
$headline = (string) ($elementData['headline'] ?? '');
$text = (string) ($elementData['text'] ?? '');
$position = (string) ($elementData['media_position'] ?? 'left');
$mediaFile = (string) ($elementData['media_file'] ?? '');
$mediaAlt = (string) ($elementData['media_alt'] ?? '');

$sectionBg = (string) ($elementData['section_bg'] ?? '');
$sectionPadding = (string) ($elementData['section_padding'] ?? '');
$containerWidth = (string) ($elementData['container_width'] ?? 'uk-container');
$sectionLight = !empty($elementData['section_light']);

if ($headline === '' && trim(strip_tags($text)) === '' && $mediaFile === '') {
    return;
}

$ext = strtolower((string) pathinfo($mediaFile, PATHINFO_EXTENSION));
$isVideo = in_array($ext, ['mp4', 'webm', 'ogg'], true);

$mediaLabel = \KLXM\YFormContentBuilder\MediaAltResolver::resolve($mediaFile, $mediaAlt, $headline);

$sectionClasses = ['uk-section'];
if ($sectionBg !== '') {
    $sectionClasses[] = $sectionBg;
}
if ($sectionPadding !== '') {
    $sectionClasses[] = $sectionPadding;
}
if ($sectionLight) {
    $sectionClasses[] = 'uk-light';
}
?>
<section class="<?= rex_escape(implode(' ', $sectionClasses)) ?>">
    <?php if ($containerWidth !== ''): ?>
    <div class="<?= rex_escape($containerWidth) ?>">
    <?php endif; ?>
        <div class="uk-grid-large uk-flex-middle" uk-grid>
            <?php if ($position === 'left'): ?>
                <div class="uk-width-1-2@m">
                    <?php if ($mediaFile !== ''): ?>
                        <?php if ($isVideo): ?>
                            <video controls playsinline aria-label="<?= rex_escape($mediaLabel !== '' ? $mediaLabel : 'Video') ?>" class="uk-width-1-1">
                                <source src="<?= rex_url::media($mediaFile) ?>" type="video/<?= rex_escape($ext) ?>">
                            </video>
                        <?php else: ?>
                            <img src="<?= rex_url::media($mediaFile) ?>" alt="<?= rex_escape($mediaLabel) ?>" class="uk-width-1-1" loading="lazy">
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <div class="uk-width-1-2@m">
                <?php if ($headline !== ''): ?>
                    <h3><?= rex_escape($headline) ?></h3>
                <?php endif; ?>
                <?php if ($text !== ''): ?>
                    <div><?= $text ?></div>
                <?php endif; ?>
            </div>
            <?php if ($position === 'right'): ?>
                <div class="uk-width-1-2@m">
                    <?php if ($mediaFile !== ''): ?>
                        <?php if ($isVideo): ?>
                            <video controls playsinline aria-label="<?= rex_escape($mediaLabel !== '' ? $mediaLabel : 'Video') ?>" class="uk-width-1-1">
                                <source src="<?= rex_url::media($mediaFile) ?>" type="video/<?= rex_escape($ext) ?>">
                            </video>
                        <?php else: ?>
                            <img src="<?= rex_url::media($mediaFile) ?>" alt="<?= rex_escape($mediaLabel) ?>" class="uk-width-1-1" loading="lazy">
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php if ($containerWidth !== ''): ?>
    </div>
    <?php endif; ?>
</section>
