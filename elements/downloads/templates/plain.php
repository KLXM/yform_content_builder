<?php
/**
 * Downloads Element - Plain HTML Template
 * 
 * @var array $elementData Element-Daten aus dem Content Builder
 */

// Daten extrahieren
$headline = $elementData['headline'] ?? '';
$description = $elementData['description'] ?? '';
$layout = $elementData['layout'] ?? 'list';
$showFilesize = !empty($elementData['show_filesize']);
$showFiletype = !empty($elementData['show_filetype']);
$showIcon = !empty($elementData['show_icon']);
$openInNewTab = !empty($elementData['open_in_new_tab']);
$items = $elementData['items'] ?? [];

// Keine Items? Nichts ausgeben
if (empty($items)) {
    return;
}

// Helper-Funktion für Dateigröße
$formatFilesize = function($filename) {
    $path = rex_path::media($filename);
    if (!file_exists($path)) {
        return '';
    }
    $bytes = filesize($path);
    $units = ['B', 'KB', 'MB', 'GB'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 1) . ' ' . $units[$i];
};
?>

<div class="downloads-element">
    <?php if ($headline): ?>
        <h2><?= rex_escape($headline) ?></h2>
    <?php endif; ?>
    
    <?php if ($description): ?>
        <p><?= rex_escape($description) ?></p>
    <?php endif; ?>
    
    <ul class="downloads-list">
        <?php foreach ($items as $item): ?>
            <?php
            $file = $item['file'] ?? '';
            if (!$file) continue;
            
            $title = $item['title'] ?? $file;
            $desc = $item['description'] ?? '';
            $filesize = $showFilesize ? $formatFilesize($file) : '';
            $filetype = $showFiletype ? strtoupper(pathinfo($file, PATHINFO_EXTENSION)) : '';
            $downloadUrl = rex_url::media($file);
            $target = $openInNewTab ? '_blank' : '_self';
            ?>
            <li>
                <a href="<?= $downloadUrl ?>" target="<?= $target ?>" download>
                    <?= rex_escape($title) ?>
                </a>
                <?php if ($desc): ?>
                    <span class="download-description"><?= rex_escape($desc) ?></span>
                <?php endif; ?>
                <?php if ($filetype || $filesize): ?>
                    <span class="download-meta">
                        <?php if ($filetype): ?>(<?= $filetype ?>)<?php endif; ?>
                        <?php if ($filesize): ?><?= $filesize ?><?php endif; ?>
                    </span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
