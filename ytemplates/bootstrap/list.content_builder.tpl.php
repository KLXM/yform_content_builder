<?php
/**
 * YForm Content Builder - List View Template
 * Zeigt Element-Übersicht in Datenbank-Liste
 * 
 * @var rex_yform_value_abstract $this
 * @var string $value JSON-String mit Slices
 */

if (empty($value)) {
    echo '<em>-- Leer --</em>';
    return;
}

$data = json_decode($value, true);

if (!is_array($data) || empty($data)) {
    echo '<em>-- Keine Elemente --</em>';
    return;
}

// Nur im Debug-Mode das Raw JSON zeigen
if (rex::isBackend() && rex::getConfig('debug')) {
    $summary = '<strong>Elemente:</strong> ' . count($data);
    $summary .= '<br><details style="margin-top:5px;"><summary style="cursor:pointer;">JSON anzeigen</summary>';
    $summary .= '<pre style="background:#f5f5f5; padding:10px; border-radius:3px; font-size:11px; overflow:auto; max-height:300px; margin-top:5px;">';
    $summary .= rex_escape(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    $summary .= '</pre></details>';
    echo $summary;
    return;
}

// Ohne Debug-Mode: Nur Element-Übersicht
$elements = [];
foreach ($data as $slice) {
    $type = $slice['type'] ?? 'unknown';
    $label = ucfirst(str_replace('_', ' ', $type));
    
    // Zählen wie oft dieses Element vorkommt
    if (!isset($elements[$label])) {
        $elements[$label] = 0;
    }
    $elements[$label]++;
}

// Formatierte Ausgabe mit Zähler
$output = [];
foreach ($elements as $label => $count) {
    if ($count > 1) {
        $output[] = $label . ' (' . $count . 'x)';
    } else {
        $output[] = $label;
    }
}

echo '<strong>' . count($data) . ' Element' . (count($data) !== 1 ? 'e' : '') . ':</strong> ' . implode(', ', $output);
