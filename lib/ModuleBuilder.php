<?php

namespace KLXM\YFormContentBuilder;

use rex;
use rex_addon;
use rex_escape;
use rex_exception;
use rex_extension;
use rex_i18n;
use rex_media;
use rex_path;
use rex_request;
use rex_response;
use rex_sql;
use rex_url;
use Throwable;

class ModuleBuilder
{
    /** @var array<int, array<string, mixed>> */
    protected array $slices = [];
    protected string $framework = 'bootstrap';
    protected int $valueId = 1;
    protected string $label = '';
    protected string $description = '';
    /** @var array<int, string> */
    protected array $allowedElements = [];
    protected bool $enableOnlineToggle = false;
    protected bool $legacyCke5Enabled = false;
    protected string $legacyCke5Profile = 'default';
    protected string $legacyCke5Lang = 'de';
    /** @var array<string, string> */
    protected array $legacyEditorAttributes = [];
    protected bool $legacyMigrationHint = true;
    protected string $legacyMigrationTarget = 'starter_text';
    /** Key des Feldes im Zielelement, in das das HTML übertragen wird (Standard: text) */
    protected string $legacyMigrationField = 'text';
    protected string $legacyHtml = '';
    /** @var array<string, array<string, mixed>> */
    protected array $elementDefaults = [];

    /** @param array<string, mixed> $options */
    public static function create(int $valueId = 1, mixed $rawValue = null, array $options = []): self
    {
        $instance = new self();
        $instance->valueId = $valueId > 0 ? $valueId : 1;
        $instance->framework = (string) ($options['framework'] ?? 'bootstrap');
        $instance->label = trim((string) ($options['label'] ?? ''));
        $instance->description = trim((string) ($options['description'] ?? ''));
        $instance->allowedElements = $instance->normalizeAllowedElements($options['allowed_elements'] ?? []);
        $instance->enableOnlineToggle = array_key_exists('enable_online_toggle', $options)
            ? (bool) $options['enable_online_toggle']
            : (bool) rex_addon::get('yform_content_builder')->getConfig('enable_online_toggle', false);
        $instance->legacyCke5Enabled = array_key_exists('legacy_cke5_enabled', $options)
            ? $instance->normalizeBool($options['legacy_cke5_enabled'])
            : false;
        $instance->legacyCke5Profile = trim((string) ($options['legacy_cke5_profile'] ?? 'default'));
        if ($instance->legacyCke5Profile === '') {
            $instance->legacyCke5Profile = 'default';
        }
        $instance->legacyCke5Lang = trim((string) ($options['legacy_cke5_lang'] ?? 'de'));
        if ($instance->legacyCke5Lang === '') {
            $instance->legacyCke5Lang = 'de';
        }
        $instance->legacyEditorAttributes = $instance->resolveLegacyEditorAttributes(
            $options['legacy_editor_attributes'] ?? '',
            $instance->legacyCke5Profile,
            $instance->legacyCke5Lang
        );
        $instance->legacyMigrationHint = array_key_exists('legacy_migration_hint', $options)
            ? $instance->normalizeBool($options['legacy_migration_hint'])
            : true;
        $instance->legacyMigrationTarget = trim((string) ($options['legacy_migration_target'] ?? 'starter_text'));
        if ($instance->legacyMigrationTarget === '') {
            $instance->legacyMigrationTarget = 'starter_text';
        }
        $instance->legacyMigrationField = trim((string) ($options['legacy_migration_field'] ?? 'text'));
        if ($instance->legacyMigrationField === '') {
            $instance->legacyMigrationField = 'text';
        }

        if ($rawValue === null || $rawValue === '') {
            $rawValue = self::loadRawValueFromCurrentSlice($instance->valueId);
        }

        $instance->slices = $instance->normalizeSlices($rawValue);
        if ($instance->slices === []) {
            $initialOption = $options['initial_slices'] ?? ($options['initial_values'] ?? ($options['initial_value'] ?? null));
            $instance->slices = $instance->normalizeInitialSlices($initialOption);
        }
        // global_defaults als Alias oder als '*'-Key in element_defaults
        $globalDefaults = $options['global_defaults'] ?? [];
        $elementDefaultsInput = $options['element_defaults'] ?? [];
        if (is_array($globalDefaults) && $globalDefaults !== []) {
            if (!isset($elementDefaultsInput['*'])) {
                $elementDefaultsInput['*'] = $globalDefaults;
            } else {
                $elementDefaultsInput['*'] = array_merge($globalDefaults, (array) $elementDefaultsInput['*']);
            }
        }
        $instance->elementDefaults = $instance->normalizeElementDefaults($elementDefaultsInput);
        $instance->legacyMigrationTarget = $instance->resolveMigrationTarget($instance->legacyMigrationTarget);

        return $instance;
    }

    public function getEditor(): string
    {
        $this->applyBackendLocale();

        $availableElements = $this->getAvailableElements();
        $groupedAvailableElements = $this->groupAvailableElements($availableElements);
        $legacyActive = $this->legacyCke5Enabled && $this->legacyHtml !== '';
        $hiddenValue = $legacyActive
            ? $this->legacyHtml
            : json_encode($this->slices, JSON_UNESCAPED_UNICODE);

        if (!is_string($hiddenValue)) {
            $hiddenValue = '[]';
        }

        $legacyEditorId = 'yform_cb_module_legacy_editor_' . uniqid();
        $legacyMigrateId = 'yform_cb_module_legacy_migrate_' . uniqid();
        $legacyNoticeId = 'yform_cb_module_legacy_notice_' . uniqid();
        $legacyEditorAttributeParts = [];
        foreach ($this->legacyEditorAttributes as $attrName => $attrValue) {
            if ($attrName === '' || strtolower((string) $attrName) === 'id') {
                continue;
            }

            $legacyEditorAttributeParts[] = rex_escape((string) $attrName) . '="' . rex_escape((string) $attrValue) . '"';
        }
        $legacyEditorAttributeString = implode(' ', $legacyEditorAttributeParts);

        ob_start();
        ?>
        <div class="form-group yform-content-builder"
             data-framework="<?= rex_escape($this->framework) ?>"
             data-online-toggle="<?= $this->enableOnlineToggle ? '1' : '0' ?>"
             data-legacy-mode="<?= $legacyActive ? '1' : '0' ?>"
             data-available-elements='<?= rex_escape(json_encode($availableElements, JSON_UNESCAPED_UNICODE)) ?>'
             <?php if ($this->elementDefaults !== []): ?>data-element-defaults='<?= rex_escape(json_encode($this->elementDefaults, JSON_UNESCAPED_UNICODE)) ?>'<?php endif; ?>>
            <?php if ($this->label !== ''): ?>
                <label class="control-label"><?= rex_escape($this->label) ?></label>
            <?php endif; ?>

            <?php if ($this->description !== ''): ?>
                <p class="help-block"><?= rex_escape($this->description) ?></p>
            <?php endif; ?>

            <?php if ($legacyActive): ?>
                <div class="panel panel-default" style="margin-bottom: 12px;">
                    <div class="panel-body">
                        <?php if ($this->legacyMigrationHint): ?>
                            <?php
                            $migrateElements = $this->getAvailableElements();
                            ?>
                            <div id="<?= $legacyNoticeId ?>" class="alert alert-info" style="margin-bottom: 12px; padding: 8px 12px;">
                                <div style="margin-bottom:8px;">Legacy-HTML ist aktiv. Du kannst direkt weiter editieren oder auf den modernen Editor umstellen.</div>
                                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                                    <label style="margin:0;font-weight:normal;white-space:nowrap;">Element:</label>
                                    <select id="<?= $legacyMigrateId ?>_element" class="form-control input-sm" style="width:auto;min-width:140px;">
                                        <?php foreach ($migrateElements as $eKey => $eCfg): ?>
                                            <option value="<?= rex_escape($eKey) ?>"<?= $eKey === $this->legacyMigrationTarget ? ' selected' : '' ?>><?= rex_escape((string) ($eCfg['label'] ?? $eKey)) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="margin:0;font-weight:normal;white-space:nowrap;">Feld (Key):</label>
                                    <input id="<?= $legacyMigrateId ?>_field" type="text" class="form-control input-sm" style="width:120px;" value="<?= rex_escape($this->legacyMigrationField) ?>" placeholder="text">
                                    <button type="button" class="btn btn-default btn-xs" id="<?= $legacyMigrateId ?>">
                                        <i class="fa fa-exchange"></i> Zum modernen Editor wechseln
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="form-group" style="margin-bottom:0;">
                            <label class="control-label" for="<?= $legacyEditorId ?>">Legacy HTML (Editor)</label>
                            <textarea
                                id="<?= $legacyEditorId ?>"
                                <?= $legacyEditorAttributeString !== '' ? $legacyEditorAttributeString : '' ?>><?= rex_escape($this->legacyHtml) ?></textarea>
                        </div>
                    </div>
                </div>

                <script nonce="<?= rex_response::getNonce() ?>">
                (function() {
                    var $hidden = $('input[name="REX_INPUT_VALUE[<?= $this->valueId ?>]"]');
                    var $textarea = $('#<?= $legacyEditorId ?>');
                    var migrateButton = $('#<?= $legacyMigrateId ?>');
                    var $migrateElementSelect = $('#<?= $legacyMigrateId ?>_element');
                    var $migrateFieldInput = $('#<?= $legacyMigrateId ?>_field');

                    if ($hidden.length === 0 || $textarea.length === 0) {
                        return;
                    }

                    function syncLegacyToHidden() {
                        $hidden.val($textarea.val() || '');
                    }

                    function bindTinyMceSync() {
                        if (typeof tinymce === 'undefined') {
                            return false;
                        }

                        var editor = tinymce.get('<?= $legacyEditorId ?>');
                        if (!editor) {
                            return false;
                        }

                        var syncFromTinyMce = function() {
                            var content = editor.getContent();
                            $textarea.val(content);
                            syncLegacyToHidden();
                        };

                        editor.on('input change keyup SetContent', syncFromTinyMce);
                        syncFromTinyMce();
                        return true;
                    }

                    $textarea.on('input change', syncLegacyToHidden);

                    if ($textarea.hasClass('tiny-editor') && typeof tiny_init === 'function') {
                        try {
                            tiny_init($textarea.closest('.panel-body'));
                        } catch (e) {
                            console.warn('Module legacy TinyMCE init failed', e);
                        }

                        var tinyBindTries = 0;
                        var tinyBindTimer = setInterval(function() {
                            tinyBindTries++;
                            if (bindTinyMceSync() || tinyBindTries > 20) {
                                clearInterval(tinyBindTimer);
                            }
                        }, 150);
                    }

                    if ($textarea.hasClass('cke5-editor') && typeof cke5_init === 'function') {
                        try {
                            cke5_init($textarea);
                        } catch (e) {
                            console.warn('Module legacy CKE5 init failed', e);
                        }
                    }

                    $(window).on('rex:cke5IsInit', function(event, editor, editorId) {
                        if (editorId !== '<?= $legacyEditorId ?>') {
                            return;
                        }

                        editor.model.document.on('change:data', function() {
                            $textarea.val(editor.getData());
                            syncLegacyToHidden();
                        });
                    });

                    $textarea.closest('form').on('submit', function() {
                        if ($textarea.hasClass('tiny-editor') && typeof tinymce !== 'undefined') {
                            var tinyEditor = tinymce.get('<?= $legacyEditorId ?>');
                            if (tinyEditor) {
                                $textarea.val(tinyEditor.getContent());
                            }
                        }

                        syncLegacyToHidden();
                    });

                    migrateButton.on('click', function() {
                        var html = $textarea.val() || '';
                        var targetElement = ($migrateElementSelect.length ? $migrateElementSelect.val() : '') || '<?= rex_escape($this->legacyMigrationTarget) ?>';
                        var targetField = ($migrateFieldInput.length ? $migrateFieldInput.val().trim() : '') || '<?= rex_escape($this->legacyMigrationField) ?>';
                        var data = {};
                        data[targetField] = html;
                        var payload = [{
                            id: 'slice_' + Date.now(),
                            type: targetElement,
                            online: true,
                            data: data
                        }];

                        $hidden.val(JSON.stringify(payload));

                        var $notice = $('#<?= $legacyNoticeId ?>');
                        if ($notice.length) {
                            $notice.removeClass('alert-info').addClass('alert-success');
                            $notice.find('span').first().text('In den modernen Editor überführt. Bitte Modul speichern, um den Wechsel abzuschließen.');
                        }

                        $(this).prop('disabled', true);
                    });
                })();
                </script>
            <?php else: ?>
                <div class="content-builder-slices">
                    <?php foreach ($this->slices as $index => $slice): ?>
                        <?= $this->renderEditorSlice($slice, $index, $groupedAvailableElements) ?>
                    <?php endforeach; ?>
                </div>

                <div class="content-builder-add">
                    <div class="btn-group btn-block">
                        <button type="button" class="btn btn-default btn-block dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-plus"></i> <?= rex_i18n::msg('yform_content_builder_element_add') ?>
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <?php $categoryIndex = 0; ?>
                            <?php foreach ($groupedAvailableElements as $category => $elementsInCategory): ?>
                                <?php if ($categoryIndex > 0): ?>
                                    <li role="separator" class="divider"></li>
                                <?php endif; ?>
                                <li class="dropdown-header"><?= rex_escape($this->formatCategoryLabel($category)) ?></li>
                                <?php foreach ($elementsInCategory as $elementType => $config): ?>
                                    <?php $elementDescription = trim((string) ($config['description'] ?? '')); ?>
                                    <li>
                                        <a href="#" class="btn-add-slice"
                                           data-element-type="<?= rex_escape($elementType) ?>"
                                           data-element-label="<?= rex_escape((string) ($config['label'] ?? $elementType)) ?>"
                                           <?php if ($elementDescription !== ''): ?>
                                           data-toggle="tooltip"
                                           data-placement="right"
                                           data-container="body"
                                           data-delay='{"show":700,"hide":120}'
                                           title="<?= rex_escape($elementDescription) ?>"
                                           <?php endif; ?>>
                                            <i class="fa <?= rex_escape((string) ($config['icon'] ?? 'fa-cube')) ?>"></i>
                                            <?= rex_escape((string) ($config['label'] ?? $elementType)) ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                                <?php ++$categoryIndex; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <input type="hidden"
                   name="REX_INPUT_VALUE[<?= $this->valueId ?>]"
                   class="content-builder-data"
                   value='<?= rex_escape($hiddenValue) ?>'>
        </div>
        <?php

        return (string) ob_get_clean();
    }

    public function renderInput(): string
    {
        return $this->getEditor();
    }

    public function renderOutput(): string
    {
        $jsonContent = json_encode($this->slices, JSON_UNESCAPED_UNICODE);
        if (!is_string($jsonContent) || $jsonContent === '[]') {
            return '';
        }

        return Helper::outputRaw($jsonContent, $this->framework);
    }

    protected static function loadRawValueFromCurrentSlice(int $valueId): string
    {
        $slot = $valueId;
        if ($slot < 1 || $slot > 20) {
            $slot = 1;
        }

        $sliceId = rex_request('slice_id', 'int', 0);
        if ($sliceId <= 0) {
            return self::loadRawValueFromModuleContext($slot);
        }

        try {
            $field = 'value' . $slot;
            $sql = rex_sql::factory();
            $sql->setQuery(
                'SELECT ' . $field . ' FROM ' . rex::getTable('article_slice') . ' WHERE id = :id',
                [':id' => $sliceId]
            );

            if ($sql->getRows() !== 1) {
                return self::loadRawValueFromModuleContext($slot);
            }

            return (string) $sql->getValue($field);
        } catch (Throwable $e) {
            return self::loadRawValueFromModuleContext($slot);
        }
    }

    protected static function loadRawValueFromModuleContext(int $slot): string
    {
        if (isset($GLOBALS['REX_VALUE']) && is_array($GLOBALS['REX_VALUE'])) {
            if (array_key_exists($slot, $GLOBALS['REX_VALUE'])) {
                return (string) $GLOBALS['REX_VALUE'][$slot];
            }

            $slotKey = (string) $slot;
            if (array_key_exists($slotKey, $GLOBALS['REX_VALUE'])) {
                return (string) $GLOBALS['REX_VALUE'][$slotKey];
            }
        }

        return '';
    }

    /** @return array<int, array<string, mixed>> */
    protected function normalizeSlices(mixed $rawValue): array
    {
        if (is_string($rawValue)) {
            $decoded = html_entity_decode($rawValue, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $data = json_decode($decoded, true);
            if (is_array($data)) {
                return array_values($data);
            }

            if ($this->legacyCke5Enabled && $this->isLegacyHtmlString($decoded)) {
                $this->legacyHtml = $decoded;
            }

            return [];
        }

        return is_array($rawValue) ? array_values($rawValue) : [];
    }

    protected function isLegacyHtmlString(string $value): bool
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return false;
        }

        $decoded = json_decode($trimmed, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return false;
        }

        if (strpos($trimmed, '<') !== false && strpos($trimmed, '>') !== false) {
            return true;
        }

        return trim(strip_tags($trimmed)) !== '';
    }

    /**
     * @param mixed $elementDefaults
     * @return array<string, array<string, mixed>>
     */
    protected function normalizeElementDefaults(mixed $elementDefaults): array
    {
        if (!is_array($elementDefaults) || $elementDefaults === []) {
            return [];
        }

        $result = [];
        foreach ($elementDefaults as $type => $defaults) {
            // '*' ist der Wildcard-Key für alle Element-Typen
            if (!is_string($type) || $type === '' || !is_array($defaults)) {
                continue;
            }
            $result[$type] = $defaults;
        }

        return $result;
    }

    /**
     * @param mixed $initialSlices
     * @return array<int, array<string, mixed>>
     */
    protected function normalizeInitialSlices(mixed $initialSlices): array
    {
        if ($initialSlices === null || $initialSlices === '') {
            return [];
        }

        $data = null;
        if (is_string($initialSlices)) {
            $decodedString = html_entity_decode($initialSlices, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $decodedData = json_decode($decodedString, true);
            if (is_array($decodedData)) {
                $data = $decodedData;
            }
        } elseif (is_array($initialSlices)) {
            $data = $initialSlices;
        }

        if (!is_array($data) || $data === []) {
            return [];
        }

        $rawSlices = $this->isListArray($data) ? $data : [$data];
        $normalized = [];
        foreach ($rawSlices as $index => $slice) {
            if (!is_array($slice)) {
                continue;
            }

            $type = trim((string) ($slice['type'] ?? ''));
            if ($type === '') {
                continue;
            }

            $sliceData = $slice['data'] ?? [];
            if (!is_array($sliceData)) {
                $sliceData = [];
            }

            // Defaults anwenden: global ('*') < typ-spezifisch < gespeicherte Werte
            $globalBase = isset($this->elementDefaults['*']) && is_array($this->elementDefaults['*'])
                ? $this->elementDefaults['*']
                : [];
            $typeBase = isset($this->elementDefaults[$type]) && is_array($this->elementDefaults[$type])
                ? $this->elementDefaults[$type]
                : [];
            $sliceData = array_merge($globalBase, $typeBase, $sliceData);

            $normalized[] = [
                'id' => (string) ($slice['id'] ?? ('slice_' . uniqid() . '_' . $index)),
                'type' => $type,
                'online' => !array_key_exists('online', $slice) || $this->normalizeBool($slice['online']),
                'data' => $sliceData,
            ];
        }

        return $normalized;
    }

    protected function normalizeBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_int($value)) {
            return $value === 1;
        }

        if (is_string($value)) {
            return in_array(strtolower(trim($value)), ['1', 'true', 'yes', 'on'], true);
        }

        return false;
    }

    /**
     * @param array<mixed> $data
     */
    protected function isListArray(array $data): bool
    {
        if ($data === []) {
            return true;
        }

        $expectedKey = 0;
        foreach (array_keys($data) as $key) {
            if ($key !== $expectedKey) {
                return false;
            }
            ++$expectedKey;
        }

        return true;
    }

    protected function resolveMigrationTarget(string $target): string
    {
        $availableElements = $this->getAvailableElements();
        if (isset($availableElements[$target])) {
            return $target;
        }

        if (isset($availableElements['starter_text'])) {
            return 'starter_text';
        }

        foreach (array_keys($availableElements) as $elementKey) {
            return (string) $elementKey;
        }

        return 'starter_text';
    }

    /**
     * @return array<string, string>
     */
    protected function resolveLegacyEditorAttributes(mixed $rawAttributes, string $legacyProfile, string $legacyLang): array
    {
        $attributeString = trim((string) $rawAttributes);
        $attributes = $this->parseAttributeString($attributeString);

        $classTokens = [];
        if (isset($attributes['class'])) {
            $classTokens = preg_split('/\s+/', trim((string) $attributes['class'])) ?: [];
            $classTokens = array_values(array_filter($classTokens, static fn (string $token): bool => $token !== ''));
        }

        foreach (['form-control', 'yform-cb-legacy-editor'] as $requiredClass) {
            if (!in_array($requiredClass, $classTokens, true)) {
                $classTokens[] = $requiredClass;
            }
        }

        $hasEditorClass = in_array('cke5-editor', $classTokens, true) || in_array('tiny-editor', $classTokens, true);
        if (!$hasEditorClass) {
            $classTokens[] = 'cke5-editor';
        }

        $attributes['class'] = implode(' ', $classTokens);

        if (!isset($attributes['rows']) || trim((string) $attributes['rows']) === '') {
            $attributes['rows'] = '14';
        }

        if (!isset($attributes['data-profile']) || trim((string) $attributes['data-profile']) === '') {
            $attributes['data-profile'] = $legacyProfile;
        }

        if (!isset($attributes['data-lang']) || trim((string) $attributes['data-lang']) === '') {
            $attributes['data-lang'] = $legacyLang;
        }

        return $attributes;
    }

    /**
     * @return array<string, string>
     */
    protected function parseAttributeString(string $rawAttributes): array
    {
        $attributes = [];
        if ($rawAttributes === '') {
            return $attributes;
        }

        preg_match_all("/([a-zA-Z_:][-a-zA-Z0-9_:.]*)(?:\\s*=\\s*(?:\"([^\"]*)\"|'([^']*)'|([^\\s\"'=<>`]+)))?/", $rawAttributes, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $name = strtolower((string) ($match[1] ?? ''));
            if ($name === '') {
                continue;
            }

            $value = '';
            if (isset($match[2]) && $match[2] !== '') {
                $value = (string) $match[2];
            } elseif (isset($match[3]) && $match[3] !== '') {
                $value = (string) $match[3];
            } elseif (isset($match[4]) && $match[4] !== '') {
                $value = (string) $match[4];
            }

            $attributes[$name] = $value;
        }

        return $attributes;
    }

    /** @return array<int, string> */
    protected function normalizeAllowedElements(mixed $allowedElements): array
    {
        if (is_string($allowedElements)) {
            $decoded = json_decode($allowedElements, true);
            if (is_array($decoded)) {
                $allowedElements = $decoded;
            } else {
                $allowedElements = array_map('trim', explode(',', $allowedElements));
            }
        }

        if (!is_array($allowedElements)) {
            return [];
        }

        return array_values(array_filter(array_map(static fn ($value) => trim((string) $value), $allowedElements), static fn (string $value) => $value !== ''));
    }

    /** @return array<string, array<string, mixed>> */
    protected function getAvailableElements(): array
    {
        /** @var array<string, array<string, mixed>> $elements */
        $elements = [];
        /** @var array<int, string> $customPaths */
        $customPaths = rex_extension::registerPoint(new \rex_extension_point(
            'YFORM_CONTENT_BUILDER_ELEMENT_PATHS',
            []
        ));

        $elementMode = (string) rex_extension::registerPoint(new \rex_extension_point(
            'YFORM_CONTENT_BUILDER_ELEMENT_MODE',
            'replace'
        ));

        if (!in_array($elementMode, ['replace', 'merge'], true)) {
            $elementMode = 'replace';
        }

        if (count($customPaths) === 0 && rex_addon::exists('project') && rex_addon::get('project')->isAvailable()) {
            $projectPath = rex_addon::get('project')->getPath('elements/');
            if (is_dir($projectPath)) {
                $customPaths[] = $projectPath;
            }
        }

        if ($customPaths !== [] && $elementMode === 'replace') {
            foreach ($customPaths as $customPath) {
                $elements = array_replace($elements, $this->loadElementsFromBasePath((string) $customPath, 'custom'));
            }

            return $this->filterAllowedElements($elements);
        }

        $demoPath = rex_addon::get('yform_content_builder')->getPath('elements/');
        if (is_dir($demoPath)) {
            $elements = array_replace($elements, $this->loadElementsFromBasePath($demoPath, 'demo'));
        }

        if ($customPaths !== [] && $elementMode === 'merge') {
            foreach ($customPaths as $customPath) {
                $elements = array_replace($elements, $this->loadElementsFromBasePath((string) $customPath, 'custom'));
            }
        }

        return $this->filterAllowedElements($elements);
    }

    /** @return array<string, array<string, mixed>> */
    protected function loadElementsFromBasePath(string $basePath, string $source): array
    {
        if (!is_dir($basePath)) {
            return [];
        }

        $enableDemoElements = (bool) rex_addon::get('yform_content_builder')->getConfig('enable_demo_elements', true);
        $bundledDemoKeys = array_flip(Config::getBundledDemoElementKeys());

        /** @var array<string, array<string, mixed>> $elements */
        $elements = [];
        $dirs = scandir($basePath);
        if (!is_array($dirs)) {
            return [];
        }

        foreach ($dirs as $dir) {
            if ($dir === '.' || $dir === '..') {
                continue;
            }

            if ($source === 'demo' && (!$enableDemoElements || !isset($bundledDemoKeys[$dir]))) {
                continue;
            }

            $elementPath = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $dir;
            $configFile = $elementPath . '/config.php';

            if (!is_dir($elementPath) || !file_exists($configFile)) {
                continue;
            }

            Helper::loadElementI18n($elementPath);
            $config = include $configFile;
            if (!is_array($config)) {
                continue;
            }

            $config['_source'] = $source;
            $config['_path'] = $elementPath;
            $elements[$dir] = $config;
        }

        return $elements;
    }

    /**
     * @param array<string, array<string, mixed>> $elements
     * @return array<string, array<string, mixed>>
     */
    protected function filterAllowedElements(array $elements): array
    {
        if ($this->allowedElements === []) {
            return $elements;
        }

        $filtered = array_intersect_key($elements, array_flip($this->allowedElements));
        if ($filtered !== []) {
            return $filtered;
        }

        return $elements;
    }

    /**
     * @param array<string, array<string, mixed>> $availableElements
     * @return array<string, array<string, array<string, mixed>>>
     */
    protected function groupAvailableElements(array $availableElements): array
    {
        /** @var array<string, array<string, array<string, mixed>>> $groupedAvailableElements */
        $groupedAvailableElements = [];

        foreach ($availableElements as $elementType => $config) {
            $category = trim((string) ($config['category'] ?? ''));
            if ($category === '') {
                $category = Helper::t('yform_content_builder_category_other', 'other');
            }

            if (!isset($groupedAvailableElements[$category])) {
                $groupedAvailableElements[$category] = [];
            }

            $groupedAvailableElements[$category][$elementType] = $config;
        }

        return $groupedAvailableElements;
    }

    protected function formatCategoryLabel(string $category): string
    {
        return ucfirst(str_replace('_', ' ', $category));
    }

    /**
     * @param array<string, mixed> $slice
     * @param array<string, array<string, array<string, mixed>>> $groupedAvailableElements
     */
    protected function renderEditorSlice(array $slice, int $index, array $groupedAvailableElements): string
    {
        $sliceId = (string) ($slice['id'] ?? ('slice_' . uniqid()));
        $sliceType = trim((string) ($slice['type'] ?? ''));
        $elementData = is_array($slice['data'] ?? null) ? $slice['data'] : [];
        $sliceOnline = !array_key_exists('online', $slice) || $slice['online'] !== false;
        $isSection = $sliceType === 'section';
        $templateFile = $this->resolveTemplateFile($sliceType);
        $elementLabel = $sliceType;
        $elementIcon = 'fa-cube';
        foreach ($groupedAvailableElements as $elementsInCategory) {
            if (isset($elementsInCategory[$sliceType]) && is_array($elementsInCategory[$sliceType])) {
                $elementLabel = (string) ($elementsInCategory[$sliceType]['label'] ?? $sliceType);
                $elementIcon  = (string) ($elementsInCategory[$sliceType]['icon'] ?? 'fa-cube');
                break;
            }
        }

        ob_start();
        ?>
        <div class="content-builder-slice <?= $isSection ? 'is-section' : '' ?> <?= $sliceOnline ? '' : 'is-offline' ?>"
             data-slice-id="<?= rex_escape($sliceId) ?>"
             data-slice-type="<?= rex_escape($sliceType) ?>"
             data-slice-index="<?= $index ?>"
             data-slice-online="<?= $sliceOnline ? '1' : '0' ?>"
             data-slice-data='<?= rex_escape(json_encode($elementData, JSON_UNESCAPED_UNICODE)) ?>'>
            <div class="slice-toolbar" data-element-name="<?= rex_escape($elementLabel) ?>">
                <span class="slice-label"><i class="fa <?= rex_escape($elementIcon) ?>"></i><?= rex_escape($elementLabel) ?></span>
                <div class="btn-group btn-group-insert">
                    <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" title="<?= rex_i18n::msg('yform_content_builder_element_add') ?>">
                        <i class="fa fa-plus"></i>
                    </button>
                    <ul class="dropdown-menu pull-right">
                        <?php 
                        // Gesamtanzahl Elemente zählen
                        $totalElements = 0;
                        foreach ($groupedAvailableElements as $elementsInCategory) {
                            $totalElements += count($elementsInCategory);
                        }
                        // Suchfeld anzeigen wenn mehr als 5 Elemente
                        if ($totalElements > 5): 
                        ?>
                        <li class="dropdown-search-wrapper">
                            <input type="text" class="form-control dropdown-search" placeholder="<?= rex_i18n::msg('search') ?>" />
                        </li>
                        <?php endif; ?>
                        <?php $categoryIndex = 0; ?>
                        <?php foreach ($groupedAvailableElements as $category => $elementsInCategory): ?>
                            <?php if ($categoryIndex > 0): ?>
                                <li role="separator" class="divider"></li>
                            <?php endif; ?>
                            <li class="dropdown-header"><?= rex_escape($this->formatCategoryLabel($category)) ?></li>
                            <?php foreach ($elementsInCategory as $elementKey => $config): ?>
                                <?php $elementDescription = trim((string) ($config['description'] ?? '')); ?>
                                <li class="element-item" data-element-search-text="<?= rex_escape(strtolower((string) ($config['label'] ?? $elementKey) . ' ' . $category)) ?>">
                                    <a href="#" class="btn-insert-slice"
                                       data-element-type="<?= rex_escape($elementKey) ?>"
                                       data-element-label="<?= rex_escape((string) ($config['label'] ?? $elementKey)) ?>"
                                       <?php if ($elementDescription !== ''): ?>
                                       data-toggle="tooltip"
                                       data-placement="right"
                                       data-container="body"
                                       data-delay='{"show":700,"hide":120}'
                                       title="<?= rex_escape($elementDescription) ?>"
                                       <?php endif; ?>
                                       data-insert-after="<?= $index ?>">
                                        <i class="fa <?= rex_escape((string) ($config['icon'] ?? 'fa-cube')) ?>"></i>
                                        <?= rex_escape((string) ($config['label'] ?? $elementKey)) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                            <?php ++$categoryIndex; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <button type="button" class="btn btn-xs btn-default btn-slice-edit" title="<?= rex_i18n::msg('yform_content_builder_element_edit') ?>">
                    <i class="fa fa-pencil"></i>
                </button>
                <button type="button" class="btn btn-xs btn-default btn-slice-move-up" title="<?= rex_escape(Helper::t('yform_content_builder_element_move_up', 'Move up')) ?>">
                    <i class="fa fa-arrow-up"></i>
                </button>
                <button type="button" class="btn btn-xs btn-default btn-slice-move-down" title="<?= rex_escape(Helper::t('yform_content_builder_element_move_down', 'Move down')) ?>">
                    <i class="fa fa-arrow-down"></i>
                </button>
                <?php if ($this->enableOnlineToggle): ?>
                    <button type="button" class="btn btn-xs btn-default btn-slice-toggle-online" title="<?= $sliceOnline ? rex_i18n::msg('yform_content_builder_element_set_offline') : rex_i18n::msg('yform_content_builder_element_set_online') ?>">
                        <i class="fa <?= $sliceOnline ? 'fa-eye' : 'fa-eye-slash' ?>"></i>
                    </button>
                <?php endif; ?>
                <button type="button" class="btn btn-xs btn-danger btn-slice-delete" title="<?= rex_i18n::msg('yform_content_builder_element_delete') ?>">
                    <i class="fa fa-trash"></i>
                </button>
            </div>

            <div class="slice-rendered">
                <?php if ($isSection): ?>
                    <?= $this->renderSectionPreview($elementData) ?>
                <?php elseif ($templateFile !== null): ?>
                    <?php
                    $data = $elementData;
                    $config = $this->loadElementConfig($sliceType);
                    $framework = $this->framework;
                    include $templateFile;
                    ?>
                <?php else: ?>
                    <div class="alert alert-danger"><?= rex_escape(Helper::t('yform_content_builder_template_not_found', 'Template not found')) ?>: <?= rex_escape($sliceType) ?></div>
                <?php endif; ?>
            </div>

            <div class="slice-edit-form" style="display: none;"></div>
        </div>
        <?php

        return (string) ob_get_clean();
    }

    /** @param array<string, mixed> $elementData */
    protected function renderSectionPreview(array $elementData): string
    {
        $label = (string) ($elementData['label'] ?? Helper::t('yform_content_builder_section_unnamed', 'Unnamed'));
        $bgColor = (string) ($elementData['background_color'] ?? 'light');
        $bgImage = (string) ($elementData['background_image'] ?? '');
        $customId = (string) ($elementData['custom_id'] ?? '');
        $bgThumbnailClass = 'bg-' . ($bgColor !== '' ? $bgColor : 'light');
        $bgThumbnailStyle = '';

        if ($bgImage !== '') {
            $resolvedImage = $bgImage;
            if (is_numeric($bgImage)) {
                $media = rex_media::get($bgImage);
                if ($media instanceof rex_media) {
                    $resolvedImage = $media->getFileName();
                }
            }

            if ($resolvedImage !== '') {
                $bgThumbnailStyle = ' style="background-image: url(' . rex_escape(rex_url::media($resolvedImage)) . ');"';
            }
        }

        $html = '<div class="section-backend-label">';
        $html .= '<i class="fa fa-object-group"></i>';
        $html .= '<strong>' . rex_escape(Helper::t('yform_content_builder_section_label', 'Section')) . ':</strong> ' . rex_escape($label);
        $html .= '<span class="section-info">';

        if ($bgColor !== '' && $bgColor !== 'none') {
            $html .= '<span class="label label-default">' . rex_escape($bgColor) . '</span>';
        }

        if ($customId !== '') {
            $html .= '<span class="label label-info">#' . rex_escape($customId) . '</span>';
        }

        $html .= '</span>';
        $html .= '<span class="section-bg-thumbnail ' . rex_escape($bgThumbnailClass) . '"' . $bgThumbnailStyle . '></span>';
        $html .= '</div>';

        return $html;
    }

    protected function resolveTemplateFile(string $sliceType): ?string
    {
        if ($sliceType === '') {
            return null;
        }

        $elementPath = $this->getElementPath($sliceType);
        if ($elementPath === null) {
            return null;
        }

        $templateCandidates = array_values(array_unique([
            $this->framework,
            'plain',
            'uikit',
            'bootstrap',
        ]));

        foreach ($templateCandidates as $templateName) {
            $candidate = $elementPath . '/templates/' . $templateName . '.php';
            if (file_exists($candidate)) {
                return $candidate;
            }
        }

        $allTemplateFiles = glob($elementPath . '/templates/*.php');
        if (is_array($allTemplateFiles)) {
            sort($allTemplateFiles);
            foreach ($allTemplateFiles as $file) {
                $basename = basename($file);
                if ($basename !== '' && $basename[0] !== '_') {
                    return $file;
                }
            }
        }

        $legacyTemplate = $elementPath . '/element.php';
        if (file_exists($legacyTemplate)) {
            return $legacyTemplate;
        }

        return null;
    }

    /** @return array<string, mixed>|null */
    protected function loadElementConfig(string $sliceType): ?array
    {
        $elementPath = $this->getElementPath($sliceType);
        if ($elementPath === null) {
            return null;
        }

        $configFile = $elementPath . '/config.php';
        if (!file_exists($configFile)) {
            return null;
        }

        Helper::loadElementI18n($elementPath);
        $config = include $configFile;

        return is_array($config) ? $config : null;
    }

    protected function getElementPath(string $elementType): ?string
    {
        $availableElements = $this->getAvailableElements();
        if (isset($availableElements[$elementType]['_path'])) {
            return (string) $availableElements[$elementType]['_path'];
        }

        $fallbackPath = rex_path::addon('yform_content_builder', 'elements/' . $elementType);
        return is_dir($fallbackPath) ? $fallbackPath : null;
    }

    protected function applyBackendLocale(): void
    {
        $locale = $this->resolveBackendLocale();
        if ($locale === '') {
            return;
        }

        try {
            rex_i18n::setLocale($locale, false);
        } catch (rex_exception $e) {
            // Fallback: aktuelle REDAXO-Locale beibehalten.
        }
    }

    protected function resolveBackendLocale(): string
    {
        $userLanguage = trim((string) rex::getUser()?->getLanguage());
        if ($userLanguage === '') {
            return rex_i18n::getLocale();
        }

        if (preg_match('/^[a-z]{2}_[a-z]{2}$/', $userLanguage) === 1) {
            return $userLanguage;
        }

        if (preg_match('/^[a-z]{2}$/', $userLanguage) === 1) {
            foreach (rex_i18n::getLocales() as $availableLocale) {
                if (str_starts_with($availableLocale, $userLanguage . '_')) {
                    return $availableLocale;
                }
            }
        }

        return rex_i18n::getLocale();
    }
}