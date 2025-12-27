<?php

namespace App\Services\FieldTypes;

use App\Contracts\FieldTypeInterface;

class RelationshipField implements FieldTypeInterface
{
    public static function getTypeName(): string
    {
        return 'relationship';
    }

    public function render(array $config, mixed $value = null): string
    {
        $name = $config['name'] ?? '';
        $label = $config['label'] ?? $name;
        $required = $config['required'] ?? false;
        $help = $config['help'] ?? '';
        $postType = $config['post_type'] ?? 'product'; // product, post, page
        $multiple = $config['multiple'] ?? true;
        $min = $config['min'] ?? 0;
        $max = $config['max'] ?? 0;
        
        $selectedIds = is_array($value) ? $value : ($value ? [$value] : []);
        $selectedJson = json_encode($selectedIds);
        
        $requiredAttr = $required ? 'required' : '';
        $multipleAttr = $multiple ? 'true' : 'false';
        
        // Determine API base URL based on context
        $currentProject = session('current_project');
        $projectCode = is_array($currentProject) ? ($currentProject['code'] ?? null) : ($currentProject->code ?? null);
        $apiBase = $projectCode ? "/{$projectCode}/api" : '/api';
        
        // Pre-render helper outputs
        $requiredBadge = $this->renderRequiredBadge($required);
        $helpText = $this->renderHelp($help);

        return <<<HTML
        <div class="mb-4" x-data="relationshipField_{$name}()" x-init="init()">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                {$label} {$requiredBadge}
            </label>
            
            <div class="border rounded-lg overflow-hidden">
                <!-- Search -->
                <div class="p-3 bg-gray-50 border-b">
                    <input type="text" 
                           x-model="search" 
                           @input.debounce.300ms="searchItems()"
                           placeholder="Tìm kiếm {$postType}..."
                           class="w-full px-3 py-2 border rounded-lg text-sm">
                </div>
                
                <!-- Available Items -->
                <div class="max-h-48 overflow-y-auto p-2 border-b">
                    <template x-if="loading">
                        <div class="text-center py-4 text-gray-500">Đang tải...</div>
                    </template>
                    <template x-if="!loading && items.length === 0">
                        <div class="text-center py-4 text-gray-500">Không tìm thấy kết quả</div>
                    </template>
                    <template x-for="item in items" :key="item.id">
                        <div @click="addItem(item)" 
                             class="flex items-center gap-2 p-2 hover:bg-blue-50 rounded cursor-pointer"
                             :class="{ 'opacity-50': isSelected(item.id) }">
                            <template x-if="item.image">
                                <img :src="item.image" class="w-10 h-10 object-cover rounded">
                            </template>
                            <template x-if="!item.image">
                                <div class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            </template>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium truncate" x-text="item.title"></p>
                                <p class="text-xs text-gray-500" x-text="item.type"></p>
                            </div>
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                    </template>
                </div>
                
                <!-- Selected Items -->
                <div class="p-3 bg-white min-h-[60px]">
                    <p class="text-xs text-gray-500 mb-2">Đã chọn (<span x-text="selected.length"></span>)</p>
                    <div class="space-y-2">
                        <template x-for="(item, index) in selected" :key="item.id">
                            <div class="flex items-center gap-2 p-2 bg-blue-50 rounded">
                                <template x-if="item.image">
                                    <img :src="item.image" class="w-8 h-8 object-cover rounded">
                                </template>
                                <span class="flex-1 text-sm truncate" x-text="item.title"></span>
                                <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            
            <!-- Hidden input for form submission -->
            <template x-for="item in selected" :key="'input-' + item.id">
                <input type="hidden" name="{$name}[]" :value="item.id">
            </template>
            
            {$helpText}
            
            <script>
                function relationshipField_{$name}() {
                    return {
                        search: '',
                        items: [],
                        selected: [],
                        loading: false,
                        postType: '{$postType}',
                        multiple: {$multipleAttr},
                        initialIds: {$selectedJson},
                        apiBase: '{$apiBase}',
                        
                        init() {
                            this.loadInitialItems();
                            this.searchItems();
                        },
                        
                        async loadInitialItems() {
                            if (this.initialIds.length === 0) return;
                            
                            try {
                                const response = await fetch(this.apiBase + '/relationship-field/items?type=' + this.postType + '&ids=' + this.initialIds.join(','));
                                const data = await response.json();
                                this.selected = data.items || [];
                            } catch (e) {
                                console.error('Error loading initial items:', e);
                            }
                        },
                        
                        async searchItems() {
                            this.loading = true;
                            try {
                                const response = await fetch(this.apiBase + '/relationship-field/search?type=' + this.postType + '&q=' + encodeURIComponent(this.search));
                                const data = await response.json();
                                this.items = data.items || [];
                            } catch (e) {
                                console.error('Error searching:', e);
                                this.items = [];
                            }
                            this.loading = false;
                        },
                        
                        isSelected(id) {
                            return this.selected.some(item => item.id === id);
                        },
                        
                        addItem(item) {
                            if (this.isSelected(item.id)) return;
                            if (!this.multiple) {
                                this.selected = [item];
                            } else {
                                this.selected.push(item);
                            }
                        },
                        
                        removeItem(index) {
                            this.selected.splice(index, 1);
                        }
                    }
                }
            </script>
        </div>
        HTML;
    }

    protected function renderRequiredBadge(bool $required): string
    {
        return $required ? '<span class="text-red-500">*</span>' : '';
    }

    protected function renderHelp(string $help): string
    {
        return $help ? "<p class=\"mt-1 text-sm text-gray-500\">{$help}</p>" : '';
    }

    public function validate(mixed $value, array $rules = []): bool
    {
        if (empty($value)) {
            return !in_array('required', $rules);
        }
        return is_array($value) || is_numeric($value);
    }

    public function transform(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map('intval', $value);
        }
        return $value ? [(int)$value] : [];
    }
}
