@extends('cms.layouts.app')

@section('title', 'Cấu hình Website')
@section('page-title', 'Cấu hình Website')

@section('content')
<div class="flex gap-6">
    <!-- Sidebar Tabs -->
    <div class="w-64 flex-shrink-0">
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <!-- Bảng 1: Cấu hình giao diện -->
            <div class="p-4 bg-gray-50 border-b">
                <h3 class="font-semibold text-gray-700">Cấu hình giao diện</h3>
            </div>
            <nav class="p-2">
                @foreach(['general', 'topbar', 'header', 'header_mobile', 'navigation', 'map', 'footer', 'branches'] as $key)
                    @if(isset($sections[$key]))
                    <a href="?tab={{ $key }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-lg mb-1 transition-colors {{ $activeTab === $key ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        <i data-lucide="{{ $sections[$key]['icon'] }}" class="w-5 h-5"></i>
                        <span>{{ $sections[$key]['label'] }}</span>
                    </a>
                    @endif
                @endforeach
            </nav>

            <!-- Bảng 2: Cấu hình nội dung -->
            <div class="p-4 bg-gray-50 border-b border-t mt-4">
                <h3 class="font-semibold text-gray-700">Cấu hình nội dung</h3>
            </div>
            <nav class="p-2">
                @foreach(['posts', 'products', 'floating_cart', 'contact_form'] as $key)
                    @if(isset($sections[$key]))
                    <a href="?tab={{ $key }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-lg mb-1 transition-colors {{ $activeTab === $key ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        <i data-lucide="{{ $sections[$key]['icon'] }}" class="w-5 h-5"></i>
                        <span>{{ $sections[$key]['label'] }}</span>
                    </a>
                    @endif
                @endforeach
            </nav>
        </div>
    </div>

    <!-- Content Area -->
    <div class="flex-1">
        <div class="bg-white rounded-lg shadow-sm p-6">
            @if(session('alert'))
            <div class="mb-4 p-4 rounded-lg {{ session('alert.type') === 'success' ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800' }}">
                {{ session('alert.message') }}
            </div>
            @endif

            <form action="{{ route('project.admin.website-config.save', ['projectCode' => request()->segment(1)]) }}?tab={{ $activeTab }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                @if(isset($sections[$activeTab]))
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ $sections[$activeTab]['label'] }}</h2>
                        <p class="text-gray-600">Cấu hình {{ strtolower($sections[$activeTab]['label']) }} cho website</p>
                    </div>

                    <div class="space-y-6">
                        @foreach($sections[$activeTab]['fields'] as $fieldKey => $field)
                            @php
                                $bgClass = '';
                                if ($fieldKey === 'bg_color') $bgClass = 'bg-field bg-color';
                                elseif (in_array($fieldKey, ['bg_gradient_start', 'bg_gradient_end', 'bg_gradient_direction'])) $bgClass = 'bg-field bg-gradient';
                                elseif (in_array($fieldKey, ['bg_image', 'bg_image_size', 'bg_image_position', 'bg_image_repeat'])) $bgClass = 'bg-field bg-image';
                            @endphp
                            <div class="form-group {{ $bgClass }}" style="{{ $bgClass ? 'display: none;' : '' }}">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ $field['label'] }}
                                </label>

                                @php
                                    $value = $settings[$fieldKey] ?? '';
                                    if (is_array($value)) {
                                        $value = $value['value'] ?? '';
                                    }
                                @endphp

                                @if($field['type'] === 'text')
                                    <input type="text" 
                                           name="{{ $fieldKey }}" 
                                           value="{{ old($fieldKey, $value) }}"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                
                                @elseif($field['type'] === 'textarea')
                                    <textarea name="{{ $fieldKey }}" 
                                              rows="4"
                                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old($fieldKey, $value) }}</textarea>
                                
                                @elseif($field['type'] === 'number')
                                    <input type="number" 
                                           name="{{ $fieldKey }}" 
                                           value="{{ old($fieldKey, $value ?: ($field['default'] ?? '')) }}"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                
                                @elseif($field['type'] === 'color')
                                    @php
                                        $colorValue = old($fieldKey, $value ?: '#ffffff');
                                        if (!str_starts_with($colorValue, '#')) {
                                            $colorValue = '#' . $colorValue;
                                        }
                                        $colorValue = strtoupper($colorValue);
                                    @endphp
                                    <div class="flex gap-2 items-center">
                                        <input type="color" 
                                               id="picker_{{ $fieldKey }}"
                                               value="{{ $colorValue }}"
                                               class="h-12 w-16 border-2 border-gray-300 rounded cursor-pointer"
                                               onchange="document.getElementById('hex_{{ $fieldKey }}').value = this.value.toUpperCase(); document.getElementById('hex_{{ $fieldKey }}').dispatchEvent(new Event('input'));">
                                        <input type="text" 
                                               id="hex_{{ $fieldKey }}"
                                               name="{{ $fieldKey }}"
                                               value="{{ $colorValue }}"
                                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg font-mono text-sm uppercase"
                                               maxlength="7"
                                               placeholder="#FFFFFF"
                                               oninput="this.value = this.value.toUpperCase(); if(/^#[0-9A-F]{6}$/.test(this.value)) document.getElementById('picker_{{ $fieldKey }}').value = this.value;">
                                        <button type="button" 
                                                onclick="navigator.clipboard.writeText(document.getElementById('hex_{{ $fieldKey }}').value); alert('Đã copy: ' + document.getElementById('hex_{{ $fieldKey }}').value);"
                                                class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="text-xs">Copy</span>
                                        </button>
                                    </div>
                                
                                @elseif($field['type'] === 'checkbox')
                                    <input type="hidden" name="{{ $fieldKey }}" value="0">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" 
                                               name="{{ $fieldKey }}" 
                                               value="1"
                                               {{ old($fieldKey, $value) == 1 ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        <span class="ml-3 text-sm font-medium text-gray-700">Bật tính năng này</span>
                                    </label>
                                
                                @elseif($field['type'] === 'select')
                                    <select name="{{ $fieldKey }}" 
                                            id="select_{{ $fieldKey }}"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        @foreach($field['options'] as $optKey => $optLabel)
                                            <option value="{{ $optKey }}" {{ old($fieldKey, $value) == $optKey ? 'selected' : '' }}>
                                                {{ $optLabel }}
                                            </option>
                                        @endforeach
                                    </select>
                                
                                @elseif($field['type'] === 'menu_select')
                                    <select name="{{ $fieldKey }}" 
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">-- Chọn menu --</option>
                                        @if(isset($menus) && $menus->count() > 0)
                                            @foreach($menus as $menu)
                                                <option value="{{ $menu->id }}" {{ old($fieldKey, $value) == $menu->id ? 'selected' : '' }}>
                                                    {{ $menu->name }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="" disabled>Chưa có menu nào</option>
                                        @endif
                                    </select>
                                    @if(!isset($menus) || $menus->count() == 0)
                                        @php $projectCode = request()->segment(1); $isProject = $projectCode && $projectCode !== 'cms'; @endphp
                                        <p class="text-sm text-gray-500 mt-1">Vui lòng <a href="{{ $isProject ? route('project.admin.menus.index', $projectCode) : route('cms.menus.index') }}" class="text-blue-600 hover:underline">tạo menu</a> trước</p>
                                    @endif
                                
                                @elseif($field['type'] === 'image')
                                    <div class="space-y-3" x-data="{ fieldKey: '{{ $fieldKey }}', imageUrl: '{{ $value }}' }">
                                        <div x-show="imageUrl" class="mb-3">
                                            <img :src="imageUrl" alt="{{ $field['label'] }}" class="h-20 object-contain rounded border">
                                        </div>
                                        <div class="flex gap-2">
                                            <input type="text" 
                                                   :name="fieldKey" 
                                                   x-model="imageUrl"
                                                   placeholder="Nhập URL hoặc chọn từ thư viện"
                                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                            <div @click="window.dispatchEvent(new CustomEvent('open-media-for-field', { detail: { fieldKey: fieldKey } }))">
                                                <x-media-manager>
                                                    Chọn ảnh
                                                </x-media-manager>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8 pt-6 border-t flex gap-3">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
                            Lưu cấu hình
                        </button>
                        @php $projectCode = request()->segment(1); $isProject = $projectCode && $projectCode !== 'cms'; @endphp
                        <a href="{{ $isProject ? route('project.admin.website-config.preview', $projectCode) : route('cms.website-config.preview') }}" target="_blank" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <i data-lucide="eye" class="w-4 h-4 inline mr-2"></i>
                            Xem trước
                        </a>
                        <a href="{{ $isProject ? route('project.admin.dashboard', $projectCode) : route('cms.dashboard') }}" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            Hủy
                        </a>
                    </div>
                @else
                    <div class="text-center py-12">
                        <p class="text-gray-500">Chọn một mục từ menu bên trái để bắt đầu cấu hình</p>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>

<!-- Media Manager Component -->
<div id="mediaManagerModal"></div>

@push('scripts')
<script>
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    function syncColor(fieldKey, colorValue) {
        const hexInput = document.getElementById('hex_' + fieldKey);
        hexInput.value = colorValue.toUpperCase();
    }

    function syncHex(fieldKey, hexValue) {
        hexValue = hexValue.toUpperCase();
        if (!hexValue.startsWith('#')) {
            hexValue = '#' + hexValue;
        }
        
        const hexInput = document.getElementById('hex_' + fieldKey);
        hexInput.value = hexValue;
        
        if (/^#[0-9A-F]{6}$/.test(hexValue)) {
            const colorInput = document.getElementById('color_' + fieldKey);
            colorInput.value = hexValue;
        }
    }

    function copyColor(fieldKey) {
        const hexInput = document.getElementById('hex_' + fieldKey);
        const hexValue = hexInput.value;
        
        navigator.clipboard.writeText(hexValue).then(() => {
            showAlert('Đã copy: ' + hexValue, 'success');
        }).catch(() => {
            showAlert('Không thể copy', 'error');
        });
    }

    function toggleBgFields(type) {
        console.log('Toggle BG Type:', type);
        
        // Hide all background fields
        const allFields = document.querySelectorAll('.bg-field');
        console.log('Total bg-field elements:', allFields.length);
        allFields.forEach(el => {
            el.style.display = 'none';
        });
        
        // Show fields based on selected type
        if (type === 'color') {
            const colorFields = document.querySelectorAll('.bg-color');
            console.log('Color fields:', colorFields.length);
            colorFields.forEach(el => {
                el.style.display = 'block';
            });
        } else if (type === 'gradient') {
            const gradientFields = document.querySelectorAll('.bg-gradient');
            console.log('Gradient fields:', gradientFields.length);
            gradientFields.forEach(el => {
                el.style.display = 'block';
            });
        } else if (type === 'image') {
            const imageFields = document.querySelectorAll('.bg-image');
            console.log('Image fields:', imageFields.length);
            imageFields.forEach(el => {
                el.style.display = 'block';
            });
        }
    }

    let currentMediaField = null;

    function openMediaManager(fieldKey) {
        currentMediaField = fieldKey;
        
        // Get project code from URL
        const pathParts = window.location.pathname.split('/');
        const projectCode = pathParts[1]; // HD005
        const mediaUrl = `/${projectCode}/admin/media/list`;
        
        // Create media manager modal
        const modal = document.getElementById('mediaManagerModal');
        modal.innerHTML = `
            <div class="fixed inset-0 z-50 overflow-y-auto" id="mediaModal">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div onclick="closeMediaManager()" class="fixed inset-0 bg-black bg-opacity-50"></div>
                    <div class="relative bg-white rounded-lg shadow-xl max-w-7xl w-full" style="max-height: 90vh; overflow: hidden;">
                        <iframe src="${mediaUrl}" class="w-full" style="height: 85vh; border: none;"></iframe>
                    </div>
                </div>
            </div>
        `;
    }

    function closeMediaManager() {
        document.getElementById('mediaManagerModal').innerHTML = '';
    }

    // Listen for media selection
    window.addEventListener('media-selected', function(e) {
        const files = e.detail.files || [];
        if (files.length > 0) {
            const selectedImage = files[0];
            
            // Dispatch event to update Alpine.js component
            window.dispatchEvent(new CustomEvent('media-url-selected', {
                detail: { url: selectedImage.url }
            }));
        }
    });
    
    // Listen for Alpine.js field selection
    let currentAlpineField = null;
    window.addEventListener('open-media-for-field', function(e) {
        currentAlpineField = e.detail.fieldKey;
    });
    
    window.addEventListener('media-url-selected', function(e) {
        if (currentAlpineField) {
            // Find Alpine component and update
            const elements = document.querySelectorAll('[x-data]');
            elements.forEach(el => {
                const data = Alpine.$data(el);
                if (data && data.fieldKey === currentAlpineField) {
                    data.imageUrl = e.detail.url;
                }
            });
            currentAlpineField = null;
        }
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM Loaded');
        const bgTypeSelect = document.querySelector('select[name="bg_type"]');
        console.log('BG Type Select:', bgTypeSelect);
        
        if (bgTypeSelect) {
            console.log('Initial value:', bgTypeSelect.value);
            
            // Add event listener
            bgTypeSelect.addEventListener('change', function() {
                console.log('Select changed to:', this.value);
                toggleBgFields(this.value);
            });
            
            // Initialize with current value
            toggleBgFields(bgTypeSelect.value);
        }
    });
</script>
@endpush
@endsection
