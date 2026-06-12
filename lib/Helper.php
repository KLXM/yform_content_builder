<?php

namespace KLXM\YFormContentBuilder;

use rex_addon;
use rex_escape;
use Throwable;

/**
 * YForm Content Builder Helper
 * Einfache Frontend-Ausgabe von Slices
 */
class Helper
{
    /** @var array<string, mixed> Daten der aktuell offenen Section (für Grid-Close) */
    protected static array $activeSectionData = [];

    /** @var array<string, bool> Bereits eingebundene Element-Sprachverzeichnisse */
    protected static array $loadedElementLangDirs = [];

    /**
     * Bindet optional vorhandene Sprachdateien eines Elements ein.
     */
    public static function loadElementI18n(string $elementPath): void
    {
        $langDir = rtrim($elementPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'lang';
        if (!is_dir($langDir) || isset(self::$loadedElementLangDirs[$langDir])) {
            return;
        }

        \rex_i18n::addDirectory($langDir);
        self::$loadedElementLangDirs[$langDir] = true;
    }

    /**
     * Liefert eine lokalisierte Nachricht mit Fallback.
     */
    public static function t(string $key, string $fallback = ''): string
    {
        $msg = \rex_i18n::rawMsg($key);

        // REDAXO returns missing keys as "[translate:key]".
        // Treat both key and placeholder as untranslated so fallback is used.
        if ($msg !== $key && $msg !== '[translate:' . $key . ']') {
            return $msg;
        }

        return $fallback !== '' ? $fallback : $key;
    }

    /**
     * Einheitliches i18n-Muster fuer Element-Configs.
     *
     * @return \Closure(string, string): string
     */
    public static function elementTranslator(string $prefix): \Closure
    {
        return static function (string $suffix, string $fallback = '') use ($prefix): string {
            $keyPrefix = $prefix . '_';
            $key = str_starts_with($suffix, $keyPrefix) ? $suffix : $keyPrefix . $suffix;

            return self::t($key, $fallback);
        };
    }

    /**
     * Rendert Content Builder Slices im Frontend
     * Mit Auto-Close-Unterstützung für Section-Elemente
     * Mit optionalem uk-grid / uk-child-width Grid-Layout pro Section
     *
     * @param string $jsonContent JSON-String mit Slices
     * @param string $framework Framework für Templates (bootstrap|uikit|plain)
     * @return string HTML-Ausgabe
     */
    public static function render(string $jsonContent, string $framework = 'bootstrap'): string
    {
        $slices = json_decode($jsonContent, true);
        
        if (!is_array($slices) || empty($slices)) {
            return '';
        }
        
        // Offline geschaltete Slices herausfiltern (Standard: online)
        $slices = array_values(array_filter($slices, static function ($slice) {
            return !is_array($slice) || !array_key_exists('online', $slice) || $slice['online'] !== false;
        }));
        
        if (empty($slices)) {
            return '';
        }
        
        $output = '';
        $openSection = false;
        self::$activeSectionData = [];
        $sectionCount = count(array_filter($slices, function($s) { return ($s['type'] ?? '') === 'section'; }));
        
        foreach ($slices as $index => $slice) {
            $sliceType = $slice['type'] ?? '';
            
            // Ist das aktuelle Element eine Section?
            $isSection = ($sliceType === 'section');
            
            // Prüfen ob das nächste Element auch eine Section ist
            $nextIsSection = false;
            if (isset($slices[$index + 1])) {
                $nextIsSection = ($slices[$index + 1]['type'] ?? '') === 'section';
            }
            
            // Ist das letzte Element?
            $isLast = ($index === count($slices) - 1);
            
            if ($isSection) {
                // Vorherige Section schließen, wenn offen
                if ($openSection) {
                    $output .= self::renderSectionClose($framework, self::$activeSectionData);
                }
                
                // Neue Section öffnen – Daten merken für Close und Grid-Wrapping
                self::$activeSectionData = $slice['data'] ?? [];
                $output .= self::renderSlice($slice, $framework, 'open');
                $openSection = true;
                
            } else {
                // Normales Element – in Grid-Item wrappen wenn aktive Section Grid hat
                $gridEnabled = !empty(self::$activeSectionData['grid_enabled']);
                $elementHtml = self::renderSlice($slice, $framework);
                
                if ($openSection && $gridEnabled && trim($elementHtml) !== '') {
                    $output .= '<div>' . $elementHtml . '</div>' . "\n";
                } else {
                    $output .= $elementHtml;
                }
                
                // Section schließen wenn:
                // - Nächstes Element ist Section ODER
                // - Dies ist das letzte Element und eine Section ist offen
                if ($openSection && ($nextIsSection || $isLast)) {
                    $output .= self::renderSectionClose($framework, self::$activeSectionData);
                    $openSection = false;
                    self::$activeSectionData = [];
                }
            }
        }
        
        // Sicherheit: Offene Section am Ende schließen
        if ($openSection) {
            $output .= self::renderSectionClose($framework, self::$activeSectionData);
            self::$activeSectionData = [];
        }
        
        return $output;
    }

    /**
     * Schließt eine offene Section
     *
    * @param string $framework Framework
    * @param array<string, mixed> $elementData Daten der zu schließenden Section (für Grid-Wrapper)
     * @return string HTML-Ausgabe
     */
    protected static function renderSectionClose(string $framework, array $elementData = []): string
    {
        $elementPath = self::resolveElementPath('section');
        if ($elementPath === null) {
            return '</section>';
        }

        self::loadElementI18n($elementPath);

        $templateCandidates = [$framework, 'plain', 'uikit', 'bootstrap'];
        $templateFile = '';
        foreach ($templateCandidates as $templateName) {
            $candidate = $elementPath . '/templates/' . $templateName . '.php';
            if (file_exists($candidate)) {
                $templateFile = $candidate;
                break;
            }
        }
        
        if ($templateFile === '') {
            return '</section>'; // Fallback
        }
        
        $closeType = 'close';

        $obLevel = ob_get_level();
        ob_start();

        try {
            include $templateFile;
            $output = ob_get_clean();

            return is_string($output) ? $output : '';
        } catch (Throwable $e) {
            while (ob_get_level() > $obLevel) {
                ob_end_clean();
            }

            \rex_logger::logException($e);

            return '<!-- Section close render error -->';
        }
    }

    /**
     * Rendert ein einzelnes Slice
     *
    * @param array<string, mixed> $slice Slice-Daten
     * @param string $framework Framework
     * @param string|null $closeType Optional: 'open' oder 'close' für Section-Elemente
     * @return string HTML-Ausgabe
     */
    protected static function renderSlice(array $slice, string $framework, ?string $closeType = null): string
    {
        $sliceType = $slice['type'] ?? '';
        $elementData = $slice['data'] ?? [];
        
        if (empty($sliceType)) {
            return '';
        }
        
        $elementPath = self::resolveElementPath((string) $sliceType);
        if ($elementPath === null) {
            return '<!-- Element template not found: ' . rex_escape((string) $sliceType) . ' -->';
        }

        self::loadElementI18n($elementPath);

        $templateCandidates = [$framework, 'plain', 'uikit', 'bootstrap'];
        $templateFile = '';
        foreach ($templateCandidates as $templateName) {
            $candidate = $elementPath . '/templates/' . $templateName . '.php';
            if (file_exists($candidate)) {
                $templateFile = $candidate;
                break;
            }
        }
        
        if ($templateFile === '') {
            return '<!-- Element template not found: ' . rex_escape($sliceType) . ' -->';
        }

        $obLevel = ob_get_level();
        ob_start();

        try {
            include $templateFile;
            $output = ob_get_clean();

            return is_string($output) ? $output : '';
        } catch (Throwable $e) {
            while (ob_get_level() > $obLevel) {
                ob_end_clean();
            }

            \rex_logger::logException($e);

            return '<!-- Element render error: ' . rex_escape((string) $sliceType) . ' -->';
        }
    }

    protected static function resolveElementPath(string $elementType): ?string
    {
        $customPaths = \rex_extension::registerPoint(new \rex_extension_point(
            'YFORM_CONTENT_BUILDER_ELEMENT_PATHS',
            ['']
        ));

        foreach ($customPaths as $customPath) {
            if ($customPath === '') {
                continue;
            }

            $elementPath = rtrim($customPath, '/\\') . '/' . $elementType;
            if (is_dir($elementPath)) {
                return $elementPath;
            }
        }

        if (rex_addon::exists('project') && rex_addon::get('project')->isAvailable()) {
            $projectPath = rex_addon::get('project')->getPath('elements/' . $elementType);
            if (is_dir($projectPath)) {
                return $projectPath;
            }
        }

        $dataPath = rex_addon::get('yform_content_builder')->getDataPath('elements/' . $elementType);
        if (is_dir($dataPath)) {
            return $dataPath;
        }

        $addonPath = rex_addon::get('yform_content_builder')->getPath('elements/' . $elementType);
        return is_dir($addonPath) ? $addonPath : null;
    }

    /**
     * Rendert direkt aus dem rohen JSON-String.
     */
    public static function outputRaw(string $jsonContent, string $framework = 'bootstrap'): string
    {
        $normalizedContent = self::normalizeContentBuilderJson($jsonContent);
        if ($normalizedContent === null) {
            return $jsonContent;
        }

        return self::render($normalizedContent, $framework);
    }

    /**
     * Rendert direkt aus einem YORM/YForm-Datensatz.
     */
    public static function outputDataset(object $dataset, string $fieldName = 'content_builder', string $framework = 'bootstrap'): string
    {
        if (!method_exists($dataset, 'getValue')) {
            return '';
        }

        $content = $dataset->getValue($fieldName);
        if (!is_string($content)) {
            return '';
        }

        $normalizedContent = self::normalizeContentBuilderJson($content);
        if ($normalizedContent === null) {
            return $content;
        }

        return self::render($normalizedContent, $framework);
    }

    /**
     * Normalisiert Content-Builder-JSON und liefert es als JSON-Array von Slices.
     * Gibt null zurueck, wenn kein Content-Builder-JSON vorliegt.
     */
    protected static function normalizeContentBuilderJson(string $content): ?string
    {
        $trimmed = trim($content);
        if ($trimmed === '') {
            return null;
        }

        $candidates = [$trimmed];

        // Manche Felder enthalten HTML-entity-kodiertes JSON.
        $decodedEntities = html_entity_decode($trimmed, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if ($decodedEntities !== $trimmed) {
            $candidates[] = $decodedEntities;
        }

        foreach ($candidates as $candidate) {
            $decoded = json_decode($candidate, true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
                continue;
            }

            // Standard: Liste von Slices.
            if (array_is_list($decoded)) {
                if ($decoded === []) {
                    return '[]';
                }

                foreach ($decoded as $slice) {
                    if (is_array($slice) && isset($slice['type']) && is_string($slice['type'])) {
                        $normalized = json_encode($decoded);
                        return is_string($normalized) ? $normalized : null;
                    }
                }
            }

            // Sonderfall: einzelnes Slice-Objekt.
            if (isset($decoded['type']) && is_string($decoded['type'])) {
                $normalized = json_encode([$decoded]);
                return is_string($normalized) ? $normalized : null;
            }

            // Sonderfall: Wrapper mit slices-Array.
            if (isset($decoded['slices']) && is_array($decoded['slices']) && array_is_list($decoded['slices'])) {
                $normalized = json_encode($decoded['slices']);
                return is_string($normalized) ? $normalized : null;
            }
        }

        return null;
    }

    /**
     * Rendert direkt ueber YForm-Tabelle + Datensatz-ID.
     */
    public static function outputDatasetById(string $tableName, int $id, string $fieldName = 'content_builder', string $framework = 'bootstrap'): string
    {
        $tableName = trim($tableName);
        if ($tableName === '' || $id < 1) {
            return '';
        }

        if (!class_exists('rex_yform_manager_dataset')) {
            return '';
        }

        try {
            $dataset = \rex_yform_manager_dataset::get($id, $tableName);
        } catch (Throwable) {
            return '';
        }

        if (!is_object($dataset)) {
            return '';
        }

        return self::outputDataset($dataset, $fieldName, $framework);
    }

    /**
     * Wrapper für einfache Verwendung im Template
     *
     * @param mixed $dataset YOrm Dataset oder JSON-String
     * @param string $fieldName Feldname (optional, wenn Dataset übergeben wird)
     * @param string $framework Framework
     * @return string HTML-Ausgabe
     */
    public static function output(mixed $dataset, string $fieldName = 'content_builder', string $framework = 'bootstrap'): string
    {
        if (is_string($dataset)) {
            return self::outputRaw($dataset, $framework);
        }
        
        if (is_object($dataset) && method_exists($dataset, 'getValue')) {
            return self::outputDataset($dataset, $fieldName, $framework);
        }
        
        return '';
    }

    /**
     * Extrahiert alle Bilder aus dem Content für z.B. OG-Tags
     *
     * @param string $jsonContent JSON-String mit Slices
    * @return array<int, string> Array mit Bild-Dateinamen
     */
    public static function extractImages(string $jsonContent): array
    {
        $slices = json_decode($jsonContent, true);
        $images = [];
        
        if (!is_array($slices)) {
            return $images;
        }
        
        foreach ($slices as $slice) {
            // Offline geschaltete Slices überspringen
            if (is_array($slice) && array_key_exists('online', $slice) && $slice['online'] === false) {
                continue;
            }
            $data = $slice['data'] ?? [];
            
            // Durchsuche alle Felder nach Bildern
            foreach ($data as $key => $value) {
                if (is_string($value) && (
                    str_contains($key, 'image') || 
                    str_contains($key, 'media') || 
                    str_contains($key, 'bild')
                )) {
                    if (!empty($value)) {
                        $images[] = $value;
                    }
                }
                
                // Repeater-Felder durchsuchen
                if (is_array($value)) {
                    foreach ($value as $item) {
                        if (is_array($item)) {
                            foreach ($item as $subKey => $subValue) {
                                if (is_string($subValue) && (
                                    str_contains($subKey, 'image') || 
                                    str_contains($subKey, 'media') || 
                                    str_contains($subKey, 'bild')
                                )) {
                                    if (!empty($subValue)) {
                                        $images[] = $subValue;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        
        return array_unique($images);
    }

    /**
     * Extrahiert ersten Text für z.B. Meta-Description
     *
     * @param string $jsonContent JSON-String mit Slices
     * @param int $maxLength Maximale Länge
     * @return string Text ohne HTML
     */
    public static function extractFirstText(string $jsonContent, int $maxLength = 160): string
    {
        $slices = json_decode($jsonContent, true);
        
        if (!is_array($slices)) {
            return '';
        }
        
        foreach ($slices as $slice) {
            // Offline geschaltete Slices überspringen
            if (is_array($slice) && array_key_exists('online', $slice) && $slice['online'] === false) {
                continue;
            }
            $data = $slice['data'] ?? [];
            
            foreach ($data as $key => $value) {
                if (is_string($value) && (
                    str_contains($key, 'text') || 
                    str_contains($key, 'content') || 
                    str_contains($key, 'beschreibung')
                )) {
                    $text = strip_tags($value);
                    $text = trim($text);
                    
                    if (!empty($text)) {
                        if (mb_strlen($text) > $maxLength) {
                            $text = mb_substr($text, 0, $maxLength - 3) . '...';
                        }
                        return $text;
                    }
                }
            }
        }
        
        return '';
    }

    /**
     * Prüft ob eine Datei ein Bild ist
     *
     * @param string $filename Dateiname
     * @return bool
     */
    public static function isImage(string $filename): bool
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
    }

    /**
     * Prüft ob eine Datei ein Video ist
     *
     * @param string $filename Dateiname
     * @return bool
     */
    public static function isVideo(string $filename): bool
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($ext, ['mp4', 'webm', 'mov', 'avi', 'mkv', 'ogg']);
    }

    /**
     * Ermittelt MIME-Type für Media-Dateien
     *
     * @param string $filename Dateiname
     * @return string MIME-Type
     */
    public static function getMimeType(string $filename): string
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'mov' => 'video/quicktime',
            'avi' => 'video/x-msvideo',
            'mkv' => 'video/x-matroska',
            'ogg' => 'video/ogg'
        ];
        
        return $mimeTypes[$ext] ?? 'application/octet-stream';
    }
}
