<?php
/**
 * Kontaktformular Element - Konfiguration (Alias)
 * Delegiert an doform2026 - das neue Haupt-Formular-Element.
 * Bestehende Inhalte bleiben voll kompatibel.
 */
$doform2026Config = include rex_path::addon('yform_content_builder', 'elements/doform2026/config.php');

// Label und Icon für Rückwärtskompatibilität beibehalten
$doform2026Config['label'] = 'Kontaktformular';
$doform2026Config['icon'] = 'fa fa-envelope';
$doform2026Config['description'] = 'Flexibles Kontaktformular mit E-Mail-Versand';

return $doform2026Config;
