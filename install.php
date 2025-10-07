<?php

/**
 * Install-Script für YForm Content Builder
 */

// Prüfen ob YForm installiert ist
if (!rex_addon::get('yform')->isInstalled()) {
    throw new rex_functional_exception('Dieses Addon benötigt das "yform" Addon.');
}

$this->setProperty('install', true);
