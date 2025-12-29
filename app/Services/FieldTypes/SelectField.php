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
            // Support both key-value format and array format
            if (\is_array($optionLabel)) {
                $optionValue = $optionLabel['value'] ?? $optionValue;
                $optionLabel = $optionLabel['label'] ?? $optionValue;
            }
            $selected = ($optionValue == $selectedValue) ? ' selected' : '';
            $optionsHtml .= "<option value=\"" . htmlspecialchars((string) $optionValue) . "\"{$selected}>" . htmlspecialchars((string) $optionLabel) . "</option>";
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
        // If value is empty and not required, it's valid
        if (empty($value)) {
            return true;
        }
        
        // Get options from config if available
        $options = $this->config['options'] ?? [];
        
        // Support both key-value format ['100vh' => 'Full Screen'] and array format [['value' => '100vh', 'label' => 'Full Screen']]
        $validValues = [];
        foreach ($options as $key => $option) {
            if (\is_array($option)) {
                $validValues[] = $option['value'] ?? $key;
            } else {
                $validValues[] = $key;
            }
        }
        
        // If no options defined, skip validation
        if (empty($validValues)) {
            return true;
        }
        
        return \in_array($value, $validValues, false);
    }

    /**
     * Set config for validation
     */
    public function setConfig(array $config): self
    {
        $this->config = $config;
        return $this;
    }

    public static function getTypeName(): string
    {
        return 'select';
    }
}