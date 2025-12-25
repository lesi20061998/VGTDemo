<?php

namespace App\Services\FieldTypes;

class TextareaField extends BaseFieldType
{
    public function render(array $config, mixed $value = null): string
    {
        $attributes = $this->getFieldAttributes($config, $value);
        $attributes['rows'] = $config['rows'] ?? 4;
        $attributes['class'] .= ' resize-y';
        
        if (isset($config['max_length'])) {
            $attributes['maxlength'] = $config['max_length'];
        }

        $content = htmlspecialchars($value ?? $config['default'] ?? '');
        $fieldHtml = "<textarea" . $this->renderAttributes($attributes) . ">{$content}</textarea>";
        
        return $this->renderFieldWrapper($config, $fieldHtml);
    }

    public function validate(mixed $value, array $rules): bool
    {
        $defaultRules = ['string'];
        return parent::validate($value, [...$defaultRules, ...$rules]);
    }

    public static function getTypeName(): string
    {
        return 'textarea';
    }
}