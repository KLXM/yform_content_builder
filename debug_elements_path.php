<?php
/**
 * Test-Script für Elements-Path Debugging
 * 
 * Dieses Script testet die Pfad-Auflösung des Content Builders
 */

// Prüfe ob formbuilder_elements Ordner existiert
$addonPath = rex_addon::get('yform_content_builder')->getPath();
$testPath = $addonPath . 'formbuilder_elements';

echo '<h2>YForm Content Builder - Elements Path Debug</h2>';

echo '<h3>1. Addon-Pfad Test</h3>';
echo '<p><strong>AddOn-Pfad:</strong> ' . htmlspecialchars($addonPath) . '</p>';
echo '<p><strong>Test-Pfad:</strong> ' . htmlspecialchars($testPath) . '</p>';
echo '<p><strong>Ordner existiert:</strong> ' . (is_dir($testPath) ? '✓ JA' : '✗ NEIN') . '</p>';

if (is_dir($testPath)) {
    echo '<h3>2. Verfügbare Elemente in formbuilder_elements</h3>';
    $dirs = scandir($testPath);
    echo '<ul>';
    foreach ($dirs as $dir) {
        if ($dir !== '.' && $dir !== '..' && is_dir($testPath . '/' . $dir)) {
            $configFile = $testPath . '/' . $dir . '/config.php';
            $hasConfig = file_exists($configFile);
            echo '<li>' . htmlspecialchars($dir) . ' ' . ($hasConfig ? '✓' : '✗ config.php fehlt') . '</li>';
            
            if ($hasConfig) {
                $config = include $configFile;
                echo '<ul><li>Label: ' . htmlspecialchars($config['label'] ?? 'unbekannt') . '</li></ul>';
            }
        }
    }
    echo '</ul>';
}

echo '<h3>3. YForm Field Test</h3>';
// Simuliere YForm-Field für Test
try {
    $field = new rex_yform_value_content_builder();
    
    // Test der resolvePath Methode
    $reflection = new ReflectionClass($field);
    $method = $reflection->getMethod('resolvePath');
    $method->setAccessible(true);
    
    $testPaths = [
        'formbuilder_elements',
        'project_elements',
        '/data/elements',
        '/var/www/elements'
    ];
    
    echo '<table border="1" style="border-collapse: collapse; margin: 10px 0;">';
    echo '<tr><th>Input</th><th>Resolved Path</th><th>Exists</th></tr>';
    
    foreach ($testPaths as $testPath) {
        try {
            $resolved = $method->invoke($field, $testPath);
            $exists = $resolved && is_dir($resolved);
            
            echo '<tr>';
            echo '<td>' . htmlspecialchars($testPath) . '</td>';
            echo '<td>' . htmlspecialchars($resolved ?: 'NULL') . '</td>';
            echo '<td>' . ($exists ? '✓' : '✗') . '</td>';
            echo '</tr>';
        } catch (Exception $e) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($testPath) . '</td>';
            echo '<td colspan="2">ERROR: ' . htmlspecialchars($e->getMessage()) . '</td>';
            echo '</tr>';
        }
    }
    echo '</table>';
    
} catch (Exception $e) {
    echo '<p style="color: red;">Fehler beim Testen: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

echo '<h3>4. Log-Dateien</h3>';
echo '<p>Prüfe die Error-Logs für Debug-Ausgaben mit "[YFORM_CB]"</p>';
echo '<p><strong>PHP Error Log:</strong> ' . ini_get('error_log') . '</p>';
?>