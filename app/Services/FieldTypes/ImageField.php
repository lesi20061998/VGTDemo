<?php

namespace App\Services\FieldTypes;

class ImageField extends BaseFieldType
{
    public function render(array $config, mixed $value = null): string
    {
        $fieldId = $this->getFieldId($config);
        $name = $config['name'] ?? '';
        
        $fieldHtml = "<div class=\"space-y-3\">";
        
        // Show current image if exists
        if ($value) {
            $fieldHtml .= "<div class=\"relative inline-block\">";
            $fieldHtml .= "<img src=\"" . htmlspecialchars($value) . "\" alt=\"Current image\" class=\"max-w-xs max-h-40 object-cover rounded-lg border border-gray-200 shadow-sm\">";
            $fieldHtml .= "<input type=\"hidden\" name=\"{$name}_current\" value=\"" . htmlspecialchars($value) . "\">";
            $fieldHtml .= "</div>";
        }
        
        // File input with custom styling
        $fieldHtml .= "<div class=\"flex items-center justify-center w-full\">";
        $fieldHtml .= "<label for=\"{$fieldId}\" class=\"flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors\">";
        $fieldHtml .= "<div class=\"flex flex-col items-center justify-center pt-5 pb-6\">";
        $fieldHtml .= "<svg class=\"w-8 h-8 mb-3 text-gray-400\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">";
        $fieldHtml .= "<path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12\"></path>";
        $fieldHtml .= "</svg>";
        $fieldHtml .= "<p class=\"mb-2 text-sm text-gray-500\"><span class=\"font-semibold\">Click to upload</span></p>";
        $fieldHtml .= "<p class=\"text-xs text-gray-400\">PNG, JPG, GIF (MAX. 2MB)</p>";
        $fieldHtml .= "</div>";
        $fieldHtml .= "<input id=\"{$fieldId}\" name=\"{$name}\" type=\"file\" accept=\"image/*\" class=\"hidden\">";
        $fieldHtml .= "</label>";
        $fieldHtml .= "</div>";
        
        $fieldHtml .= "</div>";
        
        return $this->renderFieldWrapper($config, $fieldHtml);
    }

    public function validate(mixed $value, array $rules): bool
    {
        $defaultRules = \is_string($value) ? ['string'] : ['image', 'max:2048'];
        return parent::validate($value, [...$defaultRules, ...$rules]);
    }

    public function transform(mixed $value): mixed
    {
        return $value;
    }

    public static function getTypeName(): string
    {
        return 'image';
    }
}