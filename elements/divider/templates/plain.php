<?php
/**
 * Divider Element - Plain Template
 * @var array $elementData
 */

$style = $elementData['style'] ?? 'solid';
$text = $elementData['text'] ?? '';
$textPosition = $elementData['text_position'] ?? 'center';
$scrollAnchor = $elementData['scroll_anchor'] ?? '#';

$classes = ['cb-divider'];
$classes[] = 'cb-divider-' . $style;
$classes[] = 'cb-divider-text-' . $textPosition;

$classStr = implode(' ', $classes);
?>

<?php if ($style === 'none'): ?>
    <!-- Keine Linie - nur Abstand -->
    <div class="<?= $classStr ?>"></div>

<?php elseif ($style === 'scroll'): ?>
    <div class="<?= $classStr ?>">
        <a href="<?= rex_escape($scrollAnchor) ?>" class="cb-divider-scroll-chevron">↓</a>
    </div>
<?php elseif ($style === 'text' && !empty($text)): ?>
    <div class="<?= $classStr ?>">
        <?php if ($textPosition === 'left'): ?>
            <span class="cb-divider-text"><?= rex_escape($text) ?></span>
        <?php else: ?>
            <span class="cb-divider-text"><?= rex_escape($text) ?></span>
        <?php endif; ?>
        <hr>
    </div>
<?php else: ?>
    <hr class="<?= $classStr ?>">
<?php endif; ?>
