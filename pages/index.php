<?php

/**
 * YForm Content Builder - Hauptseite
 */

$addon = rex_addon::get('yform_content_builder');

echo rex_view::title($addon->i18n('title'));

// Subpages einbinden
rex_be_controller::includeCurrentPageSubPath();
