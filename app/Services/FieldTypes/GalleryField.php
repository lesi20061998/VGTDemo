<?php

namespace App\Services\FieldTypes;

class GalleryField extends BaseFieldType
{
    public function render(array $config, mixed $value = null): string
    {
        $fieldId = $this->getFieldId($config);
        $images = \is_array($value) ? $value : [];
        $maxItems = $config['max_items'] ?? 10;
        
        $fieldHtml = "<div class=\"gallery-field\" data-max-items=\"{$maxItems}\">";
        
        // Image container
        $fieldHtml .= "<div id=\"{$fieldId}_container\" class=\"grid grid-cols-2 md:grid-cols-4 gap-4 mb-4\">";
        
        foreach ($images as $index => $image) {
            $fieldHtml .= $this->renderImageItem($config['name'], $index, $image);
        }
        
        $fieldHtml .= "</div>";
        
        // Add button
        $fieldHtml .= "<button type=\"button\" onclick=\"addGalleryImage('{$fieldId}', '{$config['name']}')\" class=\"inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors\">";
        $fieldHtml .= "<svg class=\"w-4 h-4\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\"></path></svg>";
        $fieldHtml .= "Thêm ảnh";
        $fieldHtml .= "</button>";
        
        $fieldHtml .= "</div>";
        
        // Add JavaScript
        $fieldHtml .= $this->renderGalleryScript();
        
        return $this->renderFieldWrapper($config, $fieldHtml);
    }

    protected function renderImageItem(string $fieldName, int $index, string $image): string
    {
        $html = "<div class=\"gallery-item relative group border border-gray-200 rounded-lg overflow-hidden shadow-sm\">";
        $html .= "<img src=\"" . htmlspecialchars($image) . "\" alt=\"Gallery image\" class=\"w-full h-24 object-cover\">";
        $html .= "<input type=\"hidden\" name=\"{$fieldName}[{$index}]\" value=\"" . htmlspecialchars($image) . "\">";
        $html .= "<button type=\"button\" onclick=\"removeGalleryImage(this)\" class=\"absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 text-sm hover:bg-red-600 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center shadow-lg\">&times;</button>";
        $html .= "</div>";
        
        return $html;
    }

    protected function renderGalleryScript(): string
    {
        return "
        <script>
        function addGalleryImage(fieldId, fieldName) {
            const container = document.getElementById(fieldId + '_container');
            const maxItems = parseInt(container.parentElement.dataset.maxItems);
            const currentItems = container.children.length;
            
            if (currentItems >= maxItems) {
                alert('Tối đa ' + maxItems + ' ảnh');
                return;
            }
            
            const imageUrl = prompt('Nhập URL ảnh:');
            if (imageUrl) {
                const index = currentItems;
                const itemHtml = `
                    <div class=\"gallery-item relative group border border-gray-200 rounded-lg overflow-hidden shadow-sm\">
                        <img src=\"\${imageUrl}\" alt=\"Gallery image\" class=\"w-full h-24 object-cover\">
                        <input type=\"hidden\" name=\"\${fieldName}[\${index}]\" value=\"\${imageUrl}\">
                        <button type=\"button\" onclick=\"removeGalleryImage(this)\" class=\"absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 text-sm hover:bg-red-600 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center shadow-lg\">&times;</button>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', itemHtml);
            }
        }
        
        function removeGalleryImage(button) {
            button.parentElement.remove();
        }
        </script>
        ";
    }

    public function validate(mixed $value, array $rules): bool
    {
        if (!\is_array($value)) {
            return false;
        }
        
        $maxItems = $this->config['max_items'] ?? 10;
        if (\count($value) > $maxItems) {
            return false;
        }
        
        foreach ($value as $image) {
            if (!\is_string($image) || empty($image)) {
                return false;
            }
        }
        
        return parent::validate($value, $rules);
    }

    public static function getTypeName(): string
    {
        return 'gallery';
    }
}