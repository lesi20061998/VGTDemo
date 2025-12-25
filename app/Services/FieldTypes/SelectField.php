<?php

namespace App\Services\FieldTypes;

class SelectField extends BaseFieldType
{
    public function render(array $config, mixed $value = null): string
    {
        $attributes = $this->getFieldAttributes($config, $value);
        $attributes['class'] .= ' appearance-none bg-white';
        $selectedValue = $value ?? $config['default'] ?? '';
        
        $options = $config['options'] ?? [];
        $optionsHtml = '';
        
        // Add empty option if not required
        if (!($config['required'] ?? false)) {
            $optionsHtml .= '<option value="">-- Chọn --</option>';
        }
        
        foreach ($options as $optionValue => $optionLabel) {
            $selected = ($optionValue == $selectedValue) ? ' selected' : '';
            $optionsHtml .= "<option value=\"" . htmlspecialchars($optionValue) . "\"{$selected}>" . htmlspecialchars($optionLabel) . "</option>";
        }

        $fieldHtml = "<div class=\"relative\">";
        $fieldHtml .= "<select" . $this->renderAttributes($attributes) . ">{$optionsHtml}</select>";
        $fieldHtml .= "<div class=\"pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500\">";
        $fieldHtml .= "<svg class=\"w-4 h-4\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M19 9l-7 7-7-7\"></path></svg>";
        $fieldHtml .= "</div>";
        $fieldHtml .= "</div>";
        
        return $this->renderFieldWrapper($config, $fieldHtml);
    }

    public function validate(mixed $value, array $rules): bool
    {
        $options = $this->config['options'] ?? [];
        $validValues = array_keys($options);
        
        $defaultRules = ['in:' . implode(',', $validValues)];
        return parent::validate($value, [...$defaultRules, ...$rules]);
    }

    public static function getTypeName(): string
    {
        return 'select';
    }
}