<?php

namespace App\Services\FieldTypes;

class ColorField extends BaseFieldType
{
    public function render(array $config, mixed $value = null): string
    {
        $fieldId = $this->getFieldId($config);
        $name = $config['name'] ?? '';
        $colorValue = $value ?? $config['default'] ?? '#3B82F6';

        $fieldHtml = "<div class=\"flex items-center gap-3\">";
        $fieldHtml .= "<input type=\"color\" id=\"{$fieldId}\" name=\"{$name}\" value=\"{$colorValue}\" class=\"w-12 h-10 p-1 border border-gray-300 rounded-lg cursor-pointer\">";
        $fieldHtml .= "<input type=\"text\" value=\"{$colorValue}\" class=\"w-28 px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono\" readonly>";
        $fieldHtml .= "</div>";
        
        return $this->renderFieldWrapper($config, $fieldHtml);
    }

    public function validate(mixed $value, array $rules): bool
    {
        $defaultRules = ['regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'];
        return parent::validate($value, [...$defaultRules, ...$rules]);
    }

    public static function getTypeName(): string
    {
        return 'color';
    }
}