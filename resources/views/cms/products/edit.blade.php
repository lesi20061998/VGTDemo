@extends('cms.layouts.app')

@section('title', 'Sửa sản phẩm')
@section('page-title', 'Chỉnh sửa sản phẩm')

@section('content')
{{-- Hiển thị tất cả validation errors --}}
@if($errors->any())
<div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
    <div class="flex items-center gap-2 mb-2">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
        </svg>
        <span class="font-semibold">Có lỗi xảy ra:</span>
    </div>
    <ul class="list-disc list-inside text-sm space-y-1">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ isset($currentProject) && $currentProject ? route('project.admin.products.update', [$currentProject->code, $product]) : route('project.admin.products.update', [request()->route('projectCode'), $product]) }}" enctype="multipart/form-data" x-data="productForm()" @media-selected.window="handleMediaSelected($event)">
    @csrf @method('PUT')
    
    <!-- Header với nút Lưu/Hủy -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6 sticky top-0 z-10">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Chỉnh sửa sản phẩm</h1>
                <p class="text-sm text-gray-500">{{ $product->name }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ isset($currentProject) && $currentProject ? route('project.admin.products.index', $currentProject->code) : route('project.admin.products.index', request()->route('projectCode')) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">Hủy</a>
                <button type="submit" class="inline-flex items-center px-6 py-2 bg-[#98191F] text-white rounded-lg hover:bg-[#7a1419] transition">Cập nhật</button>
            </div>
        </div>
    </div>


    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Cột trái: Form chính -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Thông tin cơ bản -->
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
                <h2 class="font-semibold text-gray-900 mb-4">Thông tin cơ bản</h2>
                
                <!-- Tên sản phẩm -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tên sản phẩm *
                    </label>
                    <input type="text" name="name" id="product-name" value="{{ old('name', $product->name) }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#98191F] @error('name') border-red-500 @enderror"
                           oninput="generateSlug()">
                    @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">SKU *</label>
                        <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#98191F] @error('sku') border-red-500 @enderror">
                        @error('sku')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                        <input type="text" name="slug" id="product-slug" value="{{ old('slug', $product->slug) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#98191F]"
                               placeholder="Tự động tạo từ tên sản phẩm">
                    </div>
                </div>
            </div>

            <!-- Nội dung sản phẩm -->
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-6">
                <h2 class="font-semibold text-gray-900 mb-4">Nội dung sản phẩm</h2>

                <!-- Mô tả ngắn -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả ngắn</label>
                    <textarea name="short_description" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#98191F]">{{ old('short_description', $product->short_description) }}</textarea>
                </div>

                <!-- Mô tả đầy đủ -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Mô tả đầy đủ
                    </label>
                    <textarea name="description" class="tinymce-editor">{{ old('description', $product->description) }}</textarea>
                </div>
            </div>

            <!-- Dữ liệu sản phẩm - Tabs như WooCommerce -->
            <div class="bg-white rounded-lg shadow-sm">
                <!-- Tab Headers -->
                <div class="border-b border-gray-200">
                    <div class="flex items-center justify-between px-6 py-3">
                        <nav class="flex -mb-px">
                            <button type="button" @click="activeTab = 'general'" 
                                    :class="activeTab === 'general' ? 'border-[#98191F] text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="px-6 py-3 border-b-2 font-medium text-sm">
                                Chung
                            </button>
                            <button type="button" @click="activeTab = 'inventory'" 
                                    :class="activeTab === 'inventory' ? 'border-[#98191F] text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="px-6 py-3 border-b-2 font-medium text-sm">
                                Kho hàng
                            </button>
                            <button type="button" @click="activeTab = 'variations'" x-show="productType === 'variable'"
                                    :class="activeTab === 'variations' ? 'border-[#98191F] text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="px-6 py-3 border-b-2 font-medium text-sm">
                                Biến thể
                            </button>
                        </nav>
                        <div class="flex items-center gap-2">
                            <label class="text-sm text-gray-600">Loại:</label>
                            <select name="product_type" x-model="productType" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#98191F]">
                                <option value="simple">Đơn giản</option>
                                <option value="variable">Biến thể</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    <!-- Tab Chung -->
                    <div x-show="activeTab === 'general'" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Giá gốc (₫)</label>
                                <input type="text" name="price" value="{{ old('price', $product->price) }}" x-model="basePrice" @input="validateSalePrice()"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#98191F] input-number-format">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Giá khuyến mãi (₫)</label>
                                <input type="text" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" x-model="salePrice" @input="validateSalePrice()"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#98191F] input-number-format">
                                <p x-show="salePriceError" class="text-red-600 text-sm mt-1" x-text="salePriceError"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Kho hàng -->
                    <div x-show="activeTab === 'inventory'" class="space-y-4">
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="manage_stock" value="1" {{ old('manage_stock', $product->manage_stock) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
                                <span class="ml-2 text-sm text-gray-700">Quản lý tồn kho</span>
                            </label>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Số lượng</label>
                            <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#98191F]">
                        </div>
                    </div>

                    <!-- Tab Biến thể -->
                    <div x-show="activeTab === 'variations' && productType === 'variable'" class="space-y-4">
                        <!-- Chọn thuộc tính -->
                        <div class="border rounded-lg p-4 bg-gray-50">
                            <h3 class="font-medium mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                Chọn thuộc tính biến thể
                            </h3>
                            <div class="max-h-64 overflow-y-auto space-y-3">
                                @foreach($attributes ?? [] as $attr)
                                <div class="bg-white p-3 rounded-lg border">
                                    <label class="flex items-center mb-2 cursor-pointer">
                                        <input type="checkbox" @change="toggleAttribute({{ $attr->id }})" class="rounded border-gray-300 text-blue-600">
                                        <span class="ml-2 font-medium text-gray-900">{{ $attr->name }}</span>
                                    </label>
                                    <div class="ml-6 flex flex-wrap gap-2">
                                        @foreach($attr->values as $value)
                                        <label class="inline-flex items-center px-3 py-1.5 border rounded-lg hover:bg-red-50 hover:border-red-300 cursor-pointer transition">
                                            <input type="checkbox" name="attributes[{{ $attr->id }}][]" value="{{ $value->id }}" 
                                                   @change="updateSelectedAttributesCount()"
                                                   class="rounded border-gray-300 text-blue-600 mr-2">
                                            <span class="text-sm">{{ $value->value }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div x-show="selectedAttributes === 0" class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <p class="text-sm text-yellow-800 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    Vui lòng chọn thuộc tính để tạo biến thể
                                </p>
                            </div>
                            <button type="button" @click="generateVariations()" 
                                    x-show="selectedAttributes > 0"
                                    class="mt-4 w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tạo biến thể tự động (<span x-text="selectedAttributes"></span> thuộc tính)
                            </button>
                        </div>

                        <!-- Danh sách biến thể -->
                        <div x-show="variations.length > 0">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="font-medium flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                    </svg>
                                    Danh sách biến thể (<span x-text="variations.length"></span>)
                                </h3>
                                <button type="button" @click="variations = []" class="inline-flex items-center text-sm text-red-600 hover:text-red-800 transition">
                                    Xóa tất cả
                                </button>
                            </div>
                            <div class="space-y-2">
                                <template x-for="(variation, index) in variations" :key="index">
                                    <div class="border rounded-lg bg-white" x-data="{ open: false }">
                                        <!-- Accordion Header -->
                                        <div @click="open = !open" class="flex items-center justify-between p-4 cursor-pointer hover:bg-gray-50 transition">
                                            <div class="flex items-center gap-3 flex-1">
                                                <div class="w-12 h-12 rounded-lg border-2 overflow-hidden bg-gray-100 flex-shrink-0">
                                                    <template x-if="variation.image">
                                                        <img :src="variation.image" class="w-full h-full object-cover">
                                                    </template>
                                                    <template x-if="!variation.image">
                                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                            </svg>
                                                        </div>
                                                    </template>
                                                </div>
                                                <div class="flex-1">
                                                    <h4 class="font-medium text-sm text-gray-900" x-text="variation.name"></h4>
                                                    <div class="flex flex-wrap gap-1 mt-1">
                                                        <template x-for="(attr, key) in variation.attributeDetails" :key="key">
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-blue-100 text-blue-800">
                                                                <span class="font-medium" x-text="attr.name"></span>:
                                                                <span class="ml-1" x-text="attr.value"></span>
                                                            </span>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <button type="button" @click.stop="variations.splice(index, 1)" 
                                                        class="p-2 text-red-600 hover:bg-red-50 rounded transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                                <svg class="w-5 h-5 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        
                                        <!-- Accordion Content -->
                                        <div x-show="open" x-collapse class="border-t">
                                            <div class="p-4 space-y-4">
                                                <div class="grid grid-cols-4 gap-3">
                                                    <div>
                                                        <label class="text-xs font-medium text-gray-700 block mb-1">SKU</label>
                                                        <input type="text" :name="'variations['+index+'][sku]'" x-model="variation.sku" 
                                                               class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-[#98191F]">
                                                    </div>
                                                    <div>
                                                        <label class="text-xs font-medium text-gray-700 block mb-1">Giá (₫)</label>
                                                        <input type="number" :name="'variations['+index+'][price]'" x-model="variation.price" 
                                                               @input="validateVariationPrice(index)"
                                                               class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-[#98191F]">
                                                    </div>
                                                    <div>
                                                        <label class="text-xs font-medium text-gray-700 block mb-1">Giá KM (₫)</label>
                                                        <input type="number" :name="'variations['+index+'][sale_price]'" x-model="variation.sale_price" 
                                                               @input="validateVariationPrice(index)" :max="variation.price"
                                                               class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-[#98191F]">
                                                        <p x-show="variation.priceError" class="text-red-600 text-xs mt-1" x-text="variation.priceError"></p>
                                                    </div>
                                                    <div>
                                                        <label class="text-xs font-medium text-gray-700 block mb-1">Số lượng</label>
                                                        <input type="number" :name="'variations['+index+'][stock_quantity]'" x-model="variation.stock_quantity" 
                                                               class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-[#98191F]">
                                                    </div>
                                                </div>
                                                
                                                <div class="grid grid-cols-2 gap-4">
                                                    <div x-data="{ 
                                                        openMedia() { 
                                                            selectVariationImage(index);
                                                            $refs.mediaBtn.click();
                                                        }
                                                    }">
                                                        <label class="text-xs font-medium text-gray-700 block mb-2">Ảnh đại diện</label>
                                                        <div class="border-2 border-dashed rounded-lg overflow-hidden bg-gray-50 h-32 flex items-center justify-center mb-2">
                                                            <template x-if="variation.image">
                                                                <img :src="variation.image" class="w-full h-full object-cover">
                                                            </template>
                                                            <template x-if="!variation.image">
                                                                <div class="text-center text-gray-400">
                                                                    <svg class="w-8 h-8 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                                    </svg>
                                                                    <p class="text-xs">Chưa có ảnh</p>
                                                                </div>
                                                            </template>
                                                        </div>
                                                        <div @click="selectVariationImage(index)">
                                                            @include('cms.components.media-manager', ['slot' => 'Chọn ảnh'])
                                                        </div>
                                                        <input type="hidden" :name="'variations['+index+'][image]'" x-model="variation.image">
                                                    </div>
                                                    <div x-data="{ 
                                                        openMedia() { 
                                                            selectVariationGallery(index);
                                                            $refs.mediaBtn.click();
                                                        }
                                                    }">
                                                        <label class="text-xs font-medium text-gray-700 block mb-2">Gallery (<span x-text="(variation.gallery || []).length"></span>)</label>
                                                        <div class="border-2 border-dashed rounded-lg p-2 bg-gray-50 h-32 overflow-y-auto mb-2">
                                                            <div class="grid grid-cols-4 gap-1" x-show="variation.gallery && variation.gallery.length > 0">
                                                                <template x-for="(img, imgIndex) in variation.gallery" :key="imgIndex">
                                                                    <div class="relative aspect-square border rounded overflow-hidden group">
                                                                        <img :src="img" class="w-full h-full object-cover">
                                                                        <button type="button" @click.stop="removeVariationGalleryImage(index, imgIndex)" 
                                                                                class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                            </svg>
                                                                        </button>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                            <div x-show="!variation.gallery || variation.gallery.length === 0" class="h-full flex items-center justify-center text-gray-400">
                                                                <div class="text-center">
                                                                    <svg class="w-6 h-6 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                                    </svg>
                                                                    <p class="text-xs">Chưa có ảnh</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div @click="selectVariationGallery(index)">
                                                            @include('cms.components.media-manager', ['slot' => '+ Thêm gallery'])
                                                        </div>
                                                        <input type="hidden" :name="'variations['+index+'][gallery]'" :value="JSON.stringify(variation.gallery || [])">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <input type="hidden" :name="'variations['+index+'][attributes]'" :value="JSON.stringify(variation.attributes)">
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Phân tích SEO & Cài đặt SEO -->
            @include('cms.components.seo-analyzer', ['model' => $product, 'contentType' => 'sản phẩm'])
        </div>

        <!-- Cột phải: Sidebar -->
        <div class="space-y-6">
            <!-- Xuất bản -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Xuất bản</h2>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                        <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#98191F]">
                            <option value="draft" {{ old('status', $product->status) == 'draft' ? 'selected' : '' }}>Nháp</option>
                            <option value="published" {{ old('status', $product->status) == 'published' ? 'selected' : '' }}>Xuất bản</option>
                            <option value="archived" {{ old('status', $product->status) == 'archived' ? 'selected' : '' }}>Lưu trữ</option>
                        </select>
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
                            <span class="ml-2 text-sm text-gray-700">Sản phẩm nổi bật</span>
                        </label>
                    </div>
                    <div class="pt-3 border-t text-xs text-gray-500 space-y-1">
                        <div>Tạo: {{ $product->created_at->format('d/m/Y H:i') }}</div>
                        <div>Cập nhật: {{ $product->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>

            <!-- Ảnh đại diện -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Ảnh đại diện</h2>
                <div class="space-y-3">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg overflow-hidden bg-gray-50 aspect-square flex items-center justify-center">
                        <template x-if="featuredImage && featuredImage.length > 0">
                            <img :src="featuredImage" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!featuredImage || featuredImage.length === 0">
                            <div class="text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-sm">Chưa có ảnh</p>
                            </div>
                        </template>
                    </div>
                    <input type="hidden" name="featured_image" x-model="featuredImage">
                    <div @click="currentGalleryMode = false">
                        @include('cms.components.media-manager', ['slot' => 'Chọn ảnh đại diện'])
                    </div>
                </div>
            </div>

            <!-- Gallery sản phẩm -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Gallery sản phẩm</h2>
                <div class="space-y-3">
                    <div class="grid grid-cols-3 gap-2" x-show="gallery.length > 0">
                        <template x-for="(img, index) in gallery" :key="index">
                            <div class="relative aspect-square border rounded-lg overflow-hidden group">
                                <img :src="img" class="w-full h-full object-cover">
                                <input type="hidden" name="gallery[]" :value="img">
                                <button type="button" @click="removeGalleryImage(index)" 
                                        class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>
                    <div @click="currentGalleryMode = true">
                        @include('cms.components.media-manager', ['slot' => '+ Thêm ảnh gallery'])
                    </div>
                </div>
            </div>

            <!-- Danh mục -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-gray-900">Danh mục sản phẩm</h2>
                    <a href="{{ isset($currentProject) && $currentProject ? route('project.admin.categories.create', $currentProject->code) : route('cms.categories.create') }}" 
                       target="_blank"
                       class="text-xs text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        + Thêm danh mục mới
                    </a>
                </div>
                <div class="max-h-64 overflow-y-auto border rounded-lg p-3 space-y-2">
                    @php
                        $selectedCategories = old('categories', $product->categories->pluck('id')->toArray());
                    @endphp
                    @forelse($categories ?? [] as $cat)
                    <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
                        <input type="checkbox" name="categories[]" value="{{ $cat->id }}" 
                               {{ in_array($cat->id, $selectedCategories) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 mr-2">
                        <span class="text-sm">{{ $cat->name }}</span>
                    </label>
                    @empty
                    <div class="text-center py-4 text-gray-500 text-xs">
                        Chưa có danh mục nào. <a href="{{ isset($currentProject) && $currentProject ? route('project.admin.categories.create', $currentProject->code) : route('cms.categories.create') }}" target="_blank" class="text-blue-600 hover:underline">Thêm mới</a>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Thương hiệu -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Thương hiệu</h2>
                <div class="max-h-64 overflow-y-auto border rounded-lg p-3 space-y-2">
                    @php
                        $selectedBrands = old('brands', $product->brands->pluck('id')->toArray());
                    @endphp
                    @foreach($brands ?? [] as $brand)
                    <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
                        <input type="checkbox" name="brands[]" value="{{ $brand->id }}"
                               {{ in_array($brand->id, $selectedBrands) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 mr-2">
                        <span class="text-sm">{{ $brand->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function generateSlug() {
    const name = document.getElementById('product-name').value;
    const slugField = document.getElementById('product-slug');
    
    // Chỉ tự động tạo slug nếu slug field đang trống hoặc chưa được chỉnh sửa thủ công
    if (slugField && !slugField.dataset.manualEdit) {
        let slug = name.toLowerCase();
        
        // Bỏ dấu tiếng Việt
        slug = slug.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        
        // Thay thế đ/Đ
        slug = slug.replace(/đ/g, 'd').replace(/Đ/g, 'd');
        
        // Thay khoảng trắng bằng dấu gạch ngang
        slug = slug.replace(/\s+/g, '-');
        
        // Xóa các ký tự đặc biệt
        slug = slug.replace(/[^a-z0-9-]/g, '');
        
        // Xóa các dấu gạch ngang liên tiếp
        slug = slug.replace(/-+/g, '-');
        
        // Xóa dấu gạch ngang ở đầu và cuối
        slug = slug.replace(/^-|-$/g, '');
        
        slugField.value = slug;
    }
}

// Đánh dấu khi user chỉnh sửa slug thủ công
document.addEventListener('DOMContentLoaded', function() {
    const slugField = document.getElementById('product-slug');
    if (slugField) {
        slugField.addEventListener('input', function() {
            this.dataset.manualEdit = 'true';
        });
    }
});

function productForm() {
    return {
        activeTab: 'general',
        productType: '{{ old('product_type', $product->product_type ?? 'simple') }}',
        basePrice: {{ old('price', $product->price ?? 0) ?: 0 }},
        salePrice: {{ old('sale_price', $product->sale_price ?? 0) ?: 0 }},
        salePriceError: '',
        featuredImage: @json($product->featured_image ?? ''),
        gallery: @json($product->gallery ?? []),
        currentGalleryMode: false,
        
        // Variations handling
        variations: @json($variationsData ?? []),
        currentVariationIndex: null,
        currentVariationGalleryIndex: null,
        
        // Attributes handling
        selectedAttributes: 0,
        selectedAttributeValues: @json($product->attributeMappings->groupBy('product_attribute_id')->map(function($mappings) {
            return $mappings->pluck('product_attribute_value_id')->toArray();
        })->toArray() ?? []),
        
        init() {
            window.addEventListener('media-selected', (e) => {
                this.handleMediaSelected(e);
            });
            this.updateSelectedAttributesCount();
            this.preselectAttributes();
        },
        
        preselectAttributes() {
            // Pre-select checkboxes for already selected attributes
            Object.keys(this.selectedAttributeValues).forEach(attributeId => {
                const valueIds = this.selectedAttributeValues[attributeId];
                valueIds.forEach(valueId => {
                    const checkbox = document.querySelector(`input[name="attributes[${attributeId}][]"][value="${valueId}"]`);
                    if (checkbox) {
                        checkbox.checked = true;
                    }
                });
            });
        },
        
        updateSelectedAttributesCount() {
            const checkedBoxes = document.querySelectorAll('input[name^="attributes["]:checked');
            this.selectedAttributes = checkedBoxes.length;
        },
        
        toggleAttribute(attributeId) {
            // This function can be used to toggle all values of an attribute
            const checkboxes = document.querySelectorAll(`input[name="attributes[${attributeId}][]"]`);
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            checkboxes.forEach(cb => {
                cb.checked = !allChecked;
            });
            this.updateSelectedAttributesCount();
        },
        
        handleMediaSelected(e) {
            const items = e.detail.files || e.detail.items || [];
            
            if (this.currentGalleryMode) {
                items.forEach(item => {
                    if (!this.gallery.includes(item.url)) {
                        this.gallery.push(item.url);
                    }
                });
                this.currentGalleryMode = false;
            } else if (items.length > 0) {
                this.featuredImage = items[0].url;
            }
        },
        
        removeGalleryImage(index) {
            this.gallery.splice(index, 1);
        },
        
        validateSalePrice() {
            const maxPrice = 9999999999999.99; // Giới hạn tối đa cho decimal(15,2)
            
            // Kiểm tra giá trị tối đa
            if (this.salePrice && parseFloat(this.salePrice) > maxPrice) {
                this.salePriceError = 'Giá khuyến mãi không được vượt quá 9,999,999,999,999.99 VNĐ';
                return;
            }
            
            if (this.basePrice && parseFloat(this.basePrice) > maxPrice) {
                // Cũng kiểm tra giá gốc nếu cần
                this.salePriceError = 'Giá gốc không được vượt quá 9,999,999,999,999.99 VNĐ';
                return;
            }
            
            // Kiểm tra giá khuyến mãi phải nhỏ hơn giá gốc
            if (this.salePrice && this.basePrice && parseFloat(this.salePrice) >= parseFloat(this.basePrice)) {
                this.salePriceError = 'Giá khuyến mãi phải nhỏ hơn giá gốc';
            } else {
                this.salePriceError = '';
            }
        }
    }
}
</script>
@endsection
