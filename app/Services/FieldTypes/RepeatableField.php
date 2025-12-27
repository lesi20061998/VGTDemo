<?php

namespace App\Services\FieldTypes;

class RepeatableField extends BaseFieldType
{
    public function render(array $config, mixed $value = null): string
    {
        $fieldId = $this->getFieldId($config);
        $items = \is_array($value) ? $value : [];
        $maxItems = $config['max_items'] ?? 10;
        $minItems = $config['min_items'] ?? 0;
        $subFields = $config['fields'] ?? [];

        // Generate template for JavaScript
        $templateHtml = $this->renderRepeatableItem($config['name'], '__INDEX__', [], $subFields);
        $templateHtml = str_replace(["\n", "\r", "'"], ['', '', "\\'"], $templateHtml);
        $templateEncoded = htmlspecialchars($templateHtml, ENT_QUOTES, 'UTF-8');

        $fieldHtml = "<div class=\"repeatable-field\" data-max-items=\"{$maxItems}\" data-min-items=\"{$minItems}\">";

        // Items container with template stored in data attribute
        $fieldHtml .= "<div id=\"{$fieldId}_container\" class=\"space-y-4 mb-4\" data-template=\"{$templateEncoded}\">";

        foreach ($items as $index => $item) {
            $fieldHtml .= $this->renderRepeatableItem($config['name'], $index, $item, $subFields);
        }

        // Add empty item if no items exist and min_items > 0
        if (empty($items) && $minItems > 0) {
            for ($i = 0; $i < $minItems; $i++) {
                $fieldHtml .= $this->renderRepeatableItem($config['name'], $i, [], $subFields);
            }
        }

        $fieldHtml .= "</div>";

        // Add button - uses global function defined in layout
        $fieldHtml .= "<button type=\"button\" onclick=\"addRepeatableItem('{$fieldId}', '{$config['name']}')\" class=\"inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors\">";
        $fieldHtml .= "<svg class=\"w-4 h-4\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M12 4v16m8-8H4\"></path></svg>";
        $fieldHtml .= "Thêm mục";
        $fieldHtml .= "</button>";

        $fieldHtml .= "</div>";

        return $this->renderFieldWrapper($config, $fieldHtml);
    }

    protected function renderRepeatableItem(string $fieldName, int|string $index, array $item, array $subFields): string
    {
        $displayIndex = \is_int($index) ? $index + 1 : $index;

        $html = "<div class=\"repeatable-item border border-gray-200 rounded-lg p-4 bg-white shadow-sm\">";
        $html .= "<div class=\"flex justify-between items-center mb-4 pb-3 border-b border-gray-100\">";
        $html .= "<h4 class=\"font-semibold text-gray-700\">Muc {$displayIndex}</h4>";
        $html .= "<button type=\"button\" onclick=\"removeRepeatableItem(this)\" class=\"inline-flex items-center gap-1 text-red-600 hover:text-red-800 text-sm font-medium transition-colors\">";
        $html .= "<svg class=\"w-4 h-4\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16\"></path></svg>";
        $html .= "Xoa";
        $html .= "</button>";
        $html .= "</div>";

        $html .= "<div class=\"grid grid-cols-1 md:grid-cols-2 gap-4\">";

        foreach ($subFields as $subField) {
            $subFieldName = "{$fieldName}[{$index}][{$subField['name']}]";
            $subFieldValue = $item[$subField['name']] ?? ($subField['default'] ?? '');

            $html .= "<div class=\"col-span-1\">";
            $html .= $this->renderSubField($subField, $subFieldName, $subFieldValue);
            $html .= "</div>";
        }

        $html .= "</div>";
        $html .= "</div>";

        return $html;
    }

    protected function renderSubField(array $fieldConfig, string $fieldName, mixed $value): string
    {
        $fieldType = $fieldConfig['type'] ?? 'text';
        $fieldConfig['name'] = $fieldName;

        return match ($fieldType) {
            'text' => (new TextField())->render($fieldConfig, $value),
            'textarea' => (new TextareaField())->render($fieldConfig, $value),
            'select' => (new SelectField())->render($fieldConfig, $value),
            'checkbox' => (new CheckboxField())->render($fieldConfig, $value),
            default => (new TextField())->render($fieldConfig, $value),
        };
    }

    public function validate(mixed $value, array $rules): bool
    {
        if (!\is_array($value)) {
            return false;
        }

        $maxItems = $this->config['max_items'] ?? 10;
        $minItems = $this->config['min_items'] ?? 0;

        if (\count($value) > $maxItems || \count($value) < $minItems) {
            return false;
        }

        $subFields = $this->config['fields'] ?? [];
        foreach ($value as $item) {
            if (!\is_array($item)) {
                return false;
            }

            foreach ($subFields as $subField) {
                $subFieldValue = $item[$subField['name']] ?? null;
                $subFieldRules = explode('|', $subField['validation'] ?? '');

                if (!$this->validateSubField($subFieldValue, $subFieldRules, $subField)) {
                    return false;
                }
            }
        }

        return parent::validate($value, $rules);
    }

    protected function validateSubField(mixed $value, array $rules, array $fieldConfig): bool
    {
        $fieldType = $fieldConfig['type'] ?? 'text';

        return match ($fieldType) {
            'text' => (new TextField())->validate($value, $rules),
            'textarea' => (new TextareaField())->validate($value, $rules),
            'select' => (new SelectField($fieldConfig))->validate($value, $rules),
            'checkbox' => (new CheckboxField())->validate($value, $rules),
            default => true,
        };
    }

    public static function getTypeName(): string
    {
        return 'repeatable';
    }
}
