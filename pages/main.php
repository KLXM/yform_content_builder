<?php

/**
 * YForm Content Builder - Übersicht
 */

$addon = rex_addon::get('yform_content_builder');

// Kurze Übersicht/Intro anzeigen
$content = '<p>' . $addon->i18n('intro') . '</p>';

$fragment = new rex_fragment();
$fragment->setVar('title', $addon->i18n('title'), false);
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');
