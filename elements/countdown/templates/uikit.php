<?php
/**
 * Countdown Element - UIkit Template
 * Verschiedene Modi: simple, separator, labels, compact
 */

// Daten auslesen
$countdownDate = $elementData['countdown_date'] ?? '';
$countdownTime = $elementData['countdown_time'] ?? '00:00:00';
$countdownLanguage = $elementData['countdown_language'] ?? 'de';
$countdownMode = $elementData['countdown_mode'] ?? 'separator';
$countdownSize = $elementData['countdown_size'] ?? 'uk-h2';
$countdownAlign = $elementData['countdown_align'] ?? 'uk-text-center';
$countdownSeparator = $elementData['countdown_separator'] ?? ':';
$countdownLabels = !empty($elementData['countdown_labels']);
$countdownReload = !empty($elementData['countdown_reload']);
$countdownTextColor = $elementData['countdown_text_color'] ?? '';

// Section-Einstellungen
$sectionBg = $elementData['section_bg'] ?? '';
$sectionBgImage = $elementData['section_bg_image'] ?? '';
$sectionPadding = $elementData['section_padding'] ?? '';
$containerWidth = $elementData['container_width'] ?? '';
$lightText = !empty($elementData['light_text']);

if (empty($countdownDate)) {
    return; // Keine Daten, nichts rendern
}

// Datum + Zeit zusammenführen zu ISO 8601 Format
// Format: 2026-01-20T10:30:00+00:00
try {
    $dateTime = $countdownDate . 'T' . $countdownTime . '+00:00';
    $isoDate = $dateTime;
} catch (Exception $e) {
    return; // Fehler beim Formatieren
}

// Section-Klassen
$sectionClasses = ['uk-section'];
if ($sectionBg) $sectionClasses[] = $sectionBg;
if ($sectionPadding) $sectionClasses[] = $sectionPadding;
if ($lightText) $sectionClasses[] = 'uk-light';

// Section Background
$sectionStyle = '';
if (!empty($sectionBgImage)) {
    $bgMediaExt = strtolower(pathinfo($sectionBgImage, PATHINFO_EXTENSION));
    $videoExtensions = ['mp4', 'webm', 'ogg'];
    
    if (!in_array($bgMediaExt, $videoExtensions)) {
        $bgImageUrl = rex_media_manager::getUrl('content_slideshow', $sectionBgImage);
        $sectionStyle = ' style="background-image: url(\'' . $bgImageUrl . '\'); background-size: cover; background-position: center;"';
    }
}

$hasSection = $sectionBg || $sectionPadding || !empty($sectionBgImage);

// Container-Klassen
$containerClasses = [$countdownSize, $countdownAlign];
if ($countdownTextColor) {
    $containerClasses[] = $countdownTextColor;
}
$containerClassStr = implode(' ', $containerClasses);

// Sprach-Labels
$labels = [
    'de' => [
        'days' => 'Tage',
        'hours' => 'Stunden',
        'minutes' => 'Minuten',
        'seconds' => 'Sekunden'
    ],
    'en' => [
        'days' => 'Days',
        'hours' => 'Hours',
        'minutes' => 'Minutes',
        'seconds' => 'Seconds'
    ]
];
$currentLabels = $labels[$countdownLanguage] ?? $labels['de'];

// Countdown-Konfiguration
$reloadOption = $countdownReload ? '; reload: true' : '';
$countdownAttr = 'uk-countdown="date: ' . $isoDate . $reloadOption . '"';

?>

<?php if ($hasSection): ?>
<section class="<?= implode(' ', $sectionClasses) ?>"<?= $sectionStyle ?>>
<?php endif; ?>

<?php if ($containerWidth): ?>
<div class="<?= $containerWidth ?>">
<?php endif; ?>

<div class="<?= $containerClassStr ?>" <?= $countdownAttr ?>>
    
    <?php if ($countdownMode === 'simple'): ?>
        <!-- Nur Zahlen: 05 10 34 30 -->
        <span class="uk-countdown-number uk-countdown-days"></span>
        <span class="uk-margin-small-left uk-margin-small-right"></span>
        <span class="uk-countdown-number uk-countdown-hours"></span>
        <span class="uk-margin-small-left uk-margin-small-right"></span>
        <span class="uk-countdown-number uk-countdown-minutes"></span>
        <span class="uk-margin-small-left uk-margin-small-right"></span>
        <span class="uk-countdown-number uk-countdown-seconds"></span>
    
    <?php elseif ($countdownMode === 'separator'): ?>
        <!-- Mit Trennzeichen: 05:10:34:30 -->
        <span class="uk-countdown-number uk-countdown-days"></span>
        <span class="uk-countdown-separator"><?= rex_escape($countdownSeparator) ?></span>
        <span class="uk-countdown-number uk-countdown-hours"></span>
        <span class="uk-countdown-separator"><?= rex_escape($countdownSeparator) ?></span>
        <span class="uk-countdown-number uk-countdown-minutes"></span>
        <span class="uk-countdown-separator"><?= rex_escape($countdownSeparator) ?></span>
        <span class="uk-countdown-number uk-countdown-seconds"></span>
    
    <?php elseif ($countdownMode === 'labels'): ?>
        <!-- Mit Labels: 05 Days : 10 Hours : 34 Minutes : 30 Seconds -->
        <div class="uk-grid-small uk-flex uk-flex-center" uk-grid>
            <div>
                <span class="uk-countdown-number uk-countdown-days"></span>
                <div class="uk-countdown-label uk-text-small uk-margin-small-top"><?= $currentLabels['days'] ?></div>
            </div>
            <div>
                <span class="uk-countdown-separator uk-countdown-label"><?= rex_escape($countdownSeparator) ?></span>
            </div>
            <div>
                <span class="uk-countdown-number uk-countdown-hours"></span>
                <div class="uk-countdown-label uk-text-small uk-margin-small-top"><?= $currentLabels['hours'] ?></div>
            </div>
            <div>
                <span class="uk-countdown-separator uk-countdown-label"><?= rex_escape($countdownSeparator) ?></span>
            </div>
            <div>
                <span class="uk-countdown-number uk-countdown-minutes"></span>
                <div class="uk-countdown-label uk-text-small uk-margin-small-top"><?= $currentLabels['minutes'] ?></div>
            </div>
            <div>
                <span class="uk-countdown-separator uk-countdown-label"><?= rex_escape($countdownSeparator) ?></span>
            </div>
            <div>
                <span class="uk-countdown-number uk-countdown-seconds"></span>
                <div class="uk-countdown-label uk-text-small uk-margin-small-top"><?= $currentLabels['seconds'] ?></div>
            </div>
        </div>
    
    <?php elseif ($countdownMode === 'compact'): ?>
        <!-- Kompakt: 5d 10h 34m 30s -->
        <span class="uk-countdown-number uk-countdown-days"></span><?php echo substr($currentLabels['days'], 0, 1); ?><span class="uk-margin-left uk-countdown-number uk-countdown-hours"></span><?php echo substr($currentLabels['hours'], 0, 1); ?><span class="uk-margin-left uk-countdown-number uk-countdown-minutes"></span><?php echo substr($currentLabels['minutes'], 0, 1); ?><span class="uk-margin-left uk-countdown-number uk-countdown-seconds"></span><?php echo substr($currentLabels['seconds'], 0, 1); ?>
    
    <?php endif; ?>
    
</div>

<?php if ($containerWidth): ?>
</div>
<?php endif; ?>

<?php if ($hasSection): ?>
</section>
<?php endif; ?>
