<?php

namespace KLXM\YFormContentBuilder\Fields;

use KLXM\YFormContentBuilder\ListProfiles;
use KLXM\YFormContentBuilder\SmartLink;
use rex_escape;
use rex_media;
use rex_url;

/**
 * Kombiniertes Link-Feld fuer URL, intern, Media, Mail, Tel und YForm.
 */
class SmartLinkField extends FieldAbstract
{
    public static function getType(): string
    {
        return 'smart_link';
    }

    /**
     * @param array<string, mixed> $fieldConfig
     * @param array<string, mixed> $sliceData
     */
    public function render(string $fieldName, array $fieldConfig, mixed $value, array $sliceData = []): void
    {
        if (!$this->hasPermission($fieldConfig)) {
            return;
        }

        $label = $fieldConfig['label'] ?? $fieldName;
        $notice = $fieldConfig['notice'] ?? null;
        $multiple = (bool) ($fieldConfig['multiple'] ?? false);
        $allowedTypes = $fieldConfig['types'] ?? ['auto', 'url', 'intern', 'media', 'tel', 'mail', 'yform'];

        if (is_string($allowedTypes)) {
            $allowedTypes = array_values(array_filter(array_map('trim', explode(',', $allowedTypes))));
        }

        if ($allowedTypes === []) {
            $allowedTypes = ['auto', 'url', 'intern', 'media', 'tel', 'mail', 'yform'];
        }

        $items = SmartLink::normalize($value, $multiple);
        if ($items === []) {
            $items[] = [
                'type' => 'auto',
                'value' => '',
                'label' => '',
                'pdfjs' => false,
            ];
        }

        $yformChoices = [];
        if (class_exists(ListProfiles::class)) {
            $yformChoices = ListProfiles::getContactPickerChoices(300);
        }

        $id = 'cb_smart_link_' . uniqid();

        $this->openFormGroup();
        $this->renderLabel($label);

        echo '<div class="cb-smart-link-widget" id="' . rex_escape($id) . '" data-multiple="' . ($multiple ? '1' : '0') . '">';
        echo '<input type="hidden" class="cb-smart-link-value" name="' . rex_escape($fieldName) . '" value="' . rex_escape((string) json_encode(['multiple' => $multiple, 'items' => $items], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) . '">';
        echo '<div class="cb-smart-link-rows">';

        foreach ($items as $idx => $item) {
            $this->renderRow($id, $idx, $item, $yformChoices, $allowedTypes);
        }

        echo '</div>';

        if ($multiple) {
            echo '<button type="button" class="btn btn-default btn-sm cb-smart-link-add"><i class="fa fa-plus"></i> Link hinzufügen</button>';
        }

        echo '</div>';

        $this->closeFormGroup($notice);
    }

    /**
     * @param array{type:string,value:string,label:string,pdfjs:bool} $item
     * @param array<string,string> $yformChoices
     * @param array<string> $allowedTypes
     */
    private function renderRow(string $baseId, int $idx, array $item, array $yformChoices, array $allowedTypes): void
    {
        $rowId = $baseId . '_row_' . $idx;
        $widgetId = 'cbsl_' . self::getNextMediaCounter();
        $targetFieldName = 'cb_smart_link_target_' . $widgetId;
        $type = (string) $item['type'];
        $value = (string) $item['value'];
        $label = (string) $item['label'];
        $pdfjs = (bool) $item['pdfjs'];

        $preview = '';
        if (SmartLink::detectType($value) === 'media' && $value !== '') {
            $media = rex_media::get($value);
            if ($media !== null) {
                $preview = '<a href="' . rex_escape(rex_url::media($value)) . '" target="_blank" rel="noopener">' . rex_escape($value) . '</a>';
            }
        }

        $widgetHtml = $this->renderTargetWidget($widgetId, $targetFieldName, $value, $allowedTypes);

        echo '<div class="cb-smart-link-row panel panel-default" data-row-id="' . rex_escape($rowId) . '">';
        echo '<div class="panel-body">';

        echo '<div class="row">';
        echo '<div class="col-sm-2">';
        echo '<label>Typ</label>';
        echo '<select class="form-control cb-smart-link-type">';

        $allTypes = [
            'auto' => 'Auto',
            'url' => 'URL',
            'intern' => 'Intern',
            'media' => 'Media',
            'tel' => 'Telefon',
            'mail' => 'E-Mail',
            'yform' => 'YForm',
        ];

        foreach ($allTypes as $optVal => $optLabel) {
            if (!in_array($optVal, $allowedTypes, true) && $optVal !== 'auto') {
                continue;
            }
            $sel = $type === $optVal ? ' selected' : '';
            echo '<option value="' . rex_escape($optVal) . '"' . $sel . '>' . rex_escape($optLabel) . '</option>';
        }

        echo '</select>';
        echo '</div>';

        echo '<div class="col-sm-6">';
        echo '<label>Ziel</label>';
        echo $widgetHtml;
        echo '</div>';

        echo '<div class="col-sm-4">';
        echo '<label>Label (optional)</label>';
        echo '<input type="text" class="form-control cb-smart-link-label" value="' . rex_escape($label) . '">';
        echo '</div>';
        echo '</div>';

        echo '<div class="row" style="margin-top:8px;">';
        echo '<div class="col-sm-8">';

        if (in_array('yform', $allowedTypes, true) || in_array('auto', $allowedTypes, true)) {
            echo '<div class="cb-smart-link-yform-wrap" style="display:none;">';
            echo '<label>YForm-Eintrag</label>';
            echo '<select class="form-control cb-smart-link-yform">';
            echo '<option value="">-- auswählen --</option>';
            foreach ($yformChoices as $choiceValue => $choiceLabel) {
                $sel = $value === (string) $choiceValue ? ' selected' : '';
                echo '<option value="' . rex_escape((string) $choiceValue) . '"' . $sel . '>' . rex_escape((string) $choiceLabel) . '</option>';
            }
            echo '</select>';
            echo '</div>';
        }

        echo '<div class="cb-smart-link-preview text-muted small" style="padding-top:8px;">' . $preview . '</div>';
        echo '</div>';

        echo '<div class="col-sm-4 text-right" style="padding-top:22px;">';
        echo '<label style="margin-right:8px;"><input type="checkbox" class="cb-smart-link-pdfjs" value="1"' . ($pdfjs ? ' checked' : '') . '> PDF.js Lightbox</label> ';
        echo '<button type="button" class="btn btn-default btn-sm cb-smart-link-detect" title="Typ automatisch erkennen"><i class="fa fa-magic"></i></button> ';
        echo '<button type="button" class="btn btn-danger btn-sm cb-smart-link-remove" title="Link entfernen"><i class="fa fa-trash"></i></button>';
        echo '</div>';
        echo '</div>';

        echo '</div>';
        echo '</div>';
    }

    /**
     * @param array<string> $allowedTypes
     */
    private function renderTargetWidget(string $widgetId, string $fieldName, string $value, array $allowedTypes): string
    {
        $display = $value;
        if (ctype_digit($value) && class_exists('rex_article')) {
            $article = \rex_article::get((int) $value);
            if ($article instanceof \rex_article) {
                $display = trim($article->getName() . ' [' . $article->getId() . ']');
            }
        }

        $clang = (string) \rex_clang::getCurrentId();

        $html = '<div class="input-group custom-link cb-smart-link-target-wrap" data-link-widget-id="' . rex_escape($widgetId) . '" data-clang="' . rex_escape($clang) . '" data-extern-link-prefix="https://">';
        $html .= '<input class="form-control cb-smart-link-target-display" type="text" id="REX_LINK_' . rex_escape($widgetId) . '_NAME" value="' . rex_escape($display) . '" readonly="readonly">';
        $html .= '<input type="hidden" class="cb-smart-link-target" name="' . rex_escape($fieldName) . '" id="REX_LINK_' . rex_escape($widgetId) . '" value="' . rex_escape($value) . '">';
        $html .= '<span class="input-group-btn">';

        if (in_array('intern', $allowedTypes, true) || in_array('auto', $allowedTypes, true)) {
            $html .= '<a href="#" class="btn btn-popup intern_link" title="' . rex_escape(\rex_i18n::msg('var_link_open')) . '"><i class="rex-icon rex-icon-open-linkmap"></i></a>';
        }
        if (in_array('url', $allowedTypes, true) || in_array('auto', $allowedTypes, true)) {
            $html .= '<a href="#" class="btn btn-popup external_link" title="' . rex_escape(\rex_i18n::msg('var_extern_link')) . '"><i class="rex-icon fa-external-link"></i></a>';
        }
        if (in_array('media', $allowedTypes, true) || in_array('auto', $allowedTypes, true)) {
            $html .= '<a href="#" class="btn btn-popup media_link" title="' . rex_escape(\rex_i18n::msg('var_media_open')) . '"><i class="rex-icon fa-file-o"></i></a>';
        }
        if (in_array('mail', $allowedTypes, true) || in_array('auto', $allowedTypes, true)) {
            $html .= '<a href="#" class="btn btn-popup email_link" title="' . rex_escape(\rex_i18n::msg('var_mailto_link')) . '"><i class="rex-icon fa-envelope-o"></i></a>';
        }
        if (in_array('tel', $allowedTypes, true) || in_array('auto', $allowedTypes, true)) {
            $html .= '<a href="#" class="btn btn-popup phone_link" title="' . rex_escape(\rex_i18n::msg('var_phone_link')) . '"><i class="rex-icon fa-phone"></i></a>';
        }

        $html .= '<a href="#" class="btn btn-popup delete_link" title="' . rex_escape(\rex_i18n::msg('var_link_delete')) . '"><i class="rex-icon rex-icon-delete-link"></i></a>';
        $html .= '</span>';
        $html .= '</div>';

        return $html;
    }
}
