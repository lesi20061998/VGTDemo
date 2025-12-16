@extends('cms.layouts.app')

@section('title', 'Thêm sản phẩm')
@section('page-title', 'Thêm sản phẩm mới')

@section('content')
<form method="POST" action="{{ route('cms.products.store') }}" enctype="multipart/form-data" x-data="productForm()">
    @csrf
    
    <!-- Header với nút Lưu/Hủy -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6 sticky top-0 z-10">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-gray-900">Thêm sản phẩm mới</h1>
            <div class="flex gap-3">
                <a href="{{ route('cms.products.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">Hủy</a>
                <button type="submit" class="inline-flex items-center px-6 py-2 bg-[#98191F] text-white rounded-lg hover:bg-[#7a1419] transition">Lưu sản phẩm</button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Cột trái: Form chính -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Thông tin cơ bản -->
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên sản phẩm *</label>
                    <input type="text" name="name" value="{{ old('name') }}"  
                           @input="generateSlug($event.target.value)"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#98191F] @error('name') border-red-500 @enderror">
                    @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">SKU *</label>
                        <input type="text" name="sku" value="{{ old('sku') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#98191F] @error('sku') border-red-500 @enderror">
                        @error('sku')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                        <input type="text" name="slug" x-model="slug" value="{{ old('slug') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#98191F]">
                    </div>
                </div>
            </div>

            <!-- Mô tả -->
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả ngắn</label>
                    <textarea name="short_description" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#98191F]">{{ old('short_description') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mô tả đầy đủ *</label>
                    <x-summernote name="description" :value="old('description')" :height="400" />
                    @error('description')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
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
                                <input type="number" name="price" value="{{ old('price') }}" x-model="basePrice" @input="validateSalePrice()"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#98191F]">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Giá khuyến mãi (₫)</label>
                                <input type="number" name="sale_price" value="{{ old('sale_price') }}" x-model="salePrice" @input="validateSalePrice()"
                                       :max="basePrice" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#98191F]">
                                <p x-show="salePriceError" class="text-red-600 text-sm mt-1" x-text="salePriceError"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Kho hàng -->
                    <div x-show="activeTab === 'inventory'" class="space-y-4">
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="manage_stock" value="1" class="rounded border-gray-300 text-blue-600">
                                <span class="ml-2 text-sm text-gray-700">Quản lý tồn kho</span>
                            </label>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Số lượng</label>
                            <input type="number" name="stock_quantity" value="{{ old('stock_quantity', 0) }}"
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

            <!-- Phân tích SEO -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Phân tích SEO</h2>
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Điểm SEO</span>
                        <span class="text-sm font-bold" :class="{
                            'text-red-600': seoScore < 50,
                            'text-yellow-600': seoScore >= 50 && seoScore < 80,
                            'text-green-600': seoScore >= 80
                        }" x-text="seoScore + '/100'"></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full transition-all" :class="{
                            'bg-red-600': seoScore < 50,
                            'bg-yellow-600': seoScore >= 50 && seoScore < 80,
                            'bg-green-600': seoScore >= 80
                        }" :style="'width: ' + seoScore + '%'"></div>
                    </div>
                </div>
                <div class="space-y-2">
                    <template x-for="check in seoChecks" :key="check.title">
                        <div class="flex items-start gap-2 p-2 rounded" :class="{
                            'bg-red-50': check.status === 'error',
                            'bg-yellow-50': check.status === 'warning',
                            'bg-green-50': check.status === 'success'
                        }">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" :class="{
                                'text-red-600': check.status === 'error',
                                'text-yellow-600': check.status === 'warning',
                                'text-green-600': check.status === 'success'
                            }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path x-show="check.status === 'success'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                <path x-show="check.status === 'warning'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                <path x-show="check.status === 'error'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <div class="flex-1">
                                <p class="text-xs font-medium" :class="{
                                    'text-red-800': check.status === 'error',
                                    'text-yellow-800': check.status === 'warning',
                                    'text-green-800': check.status === 'success'
                                }" x-text="check.title"></p>
                                <p class="text-xs" :class="{
                                    'text-red-600': check.status === 'error',
                                    'text-yellow-600': check.status === 'warning',
                                    'text-green-600': check.status === 'success'
                                }" x-text="check.message"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Cột phải: Sidebar -->
        <div class="space-y-6">
            <!-- Publish -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-gray-900">Xuất bản</h2>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#98191F] text-white rounded-lg hover:bg-[#7a1419] text-sm transition">
                        Xuất bản
                    </button>
                </div>
                
                <div class="space-y-3 text-sm">
                    <!-- Status -->
                    <div class="flex items-center justify-between py-2 border-b">
                        <span class="text-gray-600">Trạng thái:</span>
                        <select name="status" class="px-2 py-1 border rounded text-sm">
                            <option value="draft">Nháp</option>
                            <option value="published">Đã xuất bản</option>
                            <option value="archived">Lưu trữ</option>
                        </select>
                    </div>

                    <!-- Visibility -->
                    <div class="flex items-center justify-between py-2 border-b">
                        <span class="text-gray-600">Hiển thị:</span>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_private" class="rounded border-gray-300 text-blue-600 mr-2">
                            <span class="text-sm">Riêng tư</span>
                        </label>
                    </div>

                    <!-- Publish Date -->
                    <div class="py-2 border-b">
                        <label class="block text-gray-600 text-sm mb-2">Xuất bản:</label>
                        <input type="datetime-local" name="published_at" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#98191F] focus:border-[#98191F]">
                    </div>

                    <!-- Featured -->
                    <div class="py-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_featured" value="1" class="rounded border-gray-300 text-blue-600">
                            <span class="ml-2 text-gray-700">Sản phẩm nổi bật</span>
                        </label>
                    </div>
                </div>
            </div>



            <!-- Ảnh đại diện -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Ảnh đại diện</h2>
                <div class="space-y-3">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg overflow-hidden bg-gray-50 h-48 flex items-center justify-center">
                        <template x-if="featuredImage">
                            <img :src="featuredImage" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!featuredImage">
                            <div class="text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-sm">Chưa có ảnh</p>
                            </div>
                        </template>
                    </div>
                    <input type="hidden" name="featured_image" x-model="featuredImage">
                    @include('cms.components.media-manager')
                </div>
            </div>

            <!-- Gallery -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Gallery sản phẩm</h2>
                <div class="space-y-3">
                    <div class="grid grid-cols-3 gap-2" x-show="gallery.length > 0">
                        <template x-for="(img, index) in gallery" :key="index">
                            <div class="relative aspect-square border rounded-lg overflow-hidden group">
                                <img :src="img.url" class="w-full h-full object-cover">
                                <button type="button" @click="removeGalleryImage(index)" 
                                        class="absolute top-1 right-1 inline-flex items-center justify-center bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                                <input type="hidden" name="gallery[]" :value="img.url">
                            </div>
                        </template>
                    </div>
                    @include('cms.components.media-manager', ['slot' => '+ Thêm ảnh gallery'])
                </div>
            </div>

            <!-- Danh mục -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Danh mục sản phẩm</h2>
                <div class="max-h-64 overflow-y-auto border rounded-lg p-3 space-y-2">
                    @foreach($categories ?? [] as $cat)
                    <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
                        <input type="checkbox" name="categories[]" value="{{ $cat->id }}" class="rounded border-gray-300 text-blue-600 mr-2">
                        <span class="text-sm">{{ $cat->name }}</span>
                    </label>
                    @endforeach
                </div>
                <input type="hidden" name="product_category_id" id="mainCategory">
                @error('product_category_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Thương hiệu -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Thương hiệu</h2>
                <div class="max-h-64 overflow-y-auto border rounded-lg p-3 space-y-2">
                    @php
                        $brands = \App\Models\Brand::all();
                    @endphp
                    @foreach($brands as $brand)
                    <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
                        <input type="checkbox" name="brands[]" value="{{ $brand->id }}" class="rounded border-gray-300 text-blue-600 mr-2">
                        <span class="text-sm">{{ $brand->name }}</span>
                    </label>
                    @endforeach
                </div>
                <input type="hidden" name="brand_id" id="mainBrand">
            </div>

            <!-- SEO -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-4">SEO</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề SEO</label>
                        <input type="text" name="meta_title" x-model="metaTitle" @input="analyzeSeo()" 
                               placeholder="Để trống để dùng tiêu đề sản phẩm"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#98191F]">
                        <p class="text-xs text-gray-500 mt-1"><span x-text="metaTitle.length"></span>/60 ký tự</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả SEO</label>
                        <textarea name="meta_description" x-model="metaDesc" @input="analyzeSeo()" rows="3"
                                  placeholder="Mô tả ngắn gọn, hấp dẫn cho kết quả tìm kiếm"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#98191F]"></textarea>
                        <p class="text-xs text-gray-500 mt-1"><span x-text="metaDesc.length"></span>/160 ký tự</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Từ khóa chính</label>
                        <input type="text" name="focus_keyword" x-model="keyword" @input="analyzeSeo()" 
                               placeholder="VD: áo thun nam"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#98191F]">
                        <p class="text-xs text-gray-500 mt-1">Từ khóa bạn muốn xếp hạng trên Google</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Schema Markup</label>
                        <select name="schema_type" x-model="schemaType" @change="analyzeSeo()"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#98191F]">
                            <option value="">Không sử dụng</option>
                            <option value="Product">Product</option>
                            <option value="Article">Article</option>
                            <option value="Review">Review</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Giúp Google hiểu rõ hơn về nội dung</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Canonical URL</label>
                        <input type="url" name="canonical_url" x-model="canonicalUrl" @input="analyzeSeo()" 
                               placeholder="https://example.com/san-pham"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#98191F]">
                        <p class="text-xs text-gray-500 mt-1">URL chính thức để tránh nội dung trùng lặp</p>
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="noindex" x-model="noindex" @change="analyzeSeo()" 
                                   class="rounded border-gray-300 text-blue-600">
                            <span class="ml-2 text-sm text-gray-700">Không cho phép index (noindex)</span>
                        </label>
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
function productForm() {
    return {
        slug: '',
        featuredImage: null,
        gallery: [],
        productType: 'simple',
        activeTab: 'general',
        basePrice: 0,
        salePrice: 0,
        salePriceError: '',
        variations: [],
        selectedAttributes: 0,
        metaTitle: '',
        metaDesc: '',
        keyword: '',
        schemaType: '',
        canonicalUrl: '',
        noindex: false,
        seoScore: 0,
        seoChecks: [],
        
        init() {
            // Auto set main category and brand
            document.querySelectorAll('input[name="categories[]"]').forEach((cb) => {
                cb.addEventListener('change', () => {
                    const checked = document.querySelectorAll('input[name="categories[]"]:checked');
                    if (checked.length > 0) {
                        document.getElementById('mainCategory').value = checked[0].value;
                    } else {
                        document.getElementById('mainCategory').value = '';
                    }
                });
            });
            
            document.querySelectorAll('input[name="brands[]"]').forEach((cb) => {
                cb.addEventListener('change', () => {
                    const checked = document.querySelectorAll('input[name="brands[]"]:checked');
                    if (checked.length > 0) {
                        document.getElementById('mainBrand').value = checked[0].value;
                    } else {
                        document.getElementById('mainBrand').value = '';
                    }
                });
            });
            
            window.addEventListener('media-selected', (e) => {
                const items = e.detail.files;
                
                // Xử lý cho biến thể
                if (this.currentVariationIndex !== undefined) {
                    if (this.currentVariationMode === 'image') {
                        this.variations[this.currentVariationIndex].image = items[0].url;
                    } else if (this.currentVariationMode === 'gallery') {
                        if (!this.variations[this.currentVariationIndex].gallery) {
                            this.variations[this.currentVariationIndex].gallery = [];
                        }
                        items.forEach(item => {
                            if (!this.variations[this.currentVariationIndex].gallery.includes(item.url)) {
                                this.variations[this.currentVariationIndex].gallery.push(item.url);
                            }
                        });
                    }
                    this.currentVariationIndex = undefined;
                    this.currentVariationMode = undefined;
                } else {
                    // Xử lý cho sản phẩm chính
                    if (items.length === 1 && !this.currentGalleryMode) {
                        this.featuredImage = items[0].url;
                    } else {
                        items.forEach(item => {
                            if (!this.gallery.some(g => g.url === item.url)) {
                                this.gallery.push({ id: item.id, url: item.url });
                            }
                        });
                    }
                    this.currentGalleryMode = false;
                }
            });
            
            window.addEventListener('open-media-manager', () => {
                // Trigger mở media manager
            });
            
            this.analyzeSeo();
        },
        
        generateSlug(name) {
            if (!this.slug) {
                try {
                    const from = 'àáạảãâầấậhẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđÀÁẠẢÃÂẦẤẬẨẪĂẰẮẶẲẴÈÉẸẺẼÊỀẾỆỂỄÌÍỊỈĨÒÓỌỎÕÔỒỐỘỔỖƠỜỚỢỞỠÙÚỤỦŨƯỪỨỰỬỮỲÝỴỶỸĐ';
                    const to = 'aaaaaaaaaaaaaaaaaeeeeeeeeeeeeiiiiiooooooooooooooooouuuuuuuuuuuuyyyyydAAAAAAAAAAAAAAAAAEEEEEEEEEEEIIIIIOOOOOOOOOOOOOOOOOUUUUUUUUUUUYYYYYD';
                    let slug = name.toLowerCase().trim();
                    for (let i = 0; i < from.length; i++) {
                        slug = slug.replace(new RegExp(from[i], 'g'), to[i]);
                    }
                    this.slug = slug.replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-');
                } catch (error) {
                    console.error('Slug generation error:', error);
                    this.slug = name.toLowerCase().replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-');
                }
            }
        },
        
        toggleAttribute(attrId) {
            this.updateSelectedAttributesCount();
        },
        
        updateSelectedAttributesCount() {
            this.selectedAttributes = document.querySelectorAll('input[name^="attributes"]:checked').length;
        },
        
        generateVariations() {
            // Kiểm tra xem đã chọn thuộc tính chưa
            const checkedInputs = document.querySelectorAll('input[name^="attributes"]:checked');
            if (checkedInputs.length === 0) {
                alert('Vui lòng chọn ít nhất một thuộc tính trước khi tạo biến thể!');
                return;
            }
            
            const selected = {};
            const attrNames = {};
            
            document.querySelectorAll('input[name^="attributes"]:checked').forEach(input => {
                const match = input.name.match(/attributes\[(\d+)\]/);
                if (match) {
                    const attrId = match[1];
                    if (!selected[attrId]) {
                        selected[attrId] = [];
                        // Lấy tên thuộc tính
                        const attrLabel = input.closest('.bg-white').querySelector('label span').textContent;
                        attrNames[attrId] = attrLabel;
                    }
                    selected[attrId].push({
                        id: input.value,
                        value: input.parentElement.querySelector('span').textContent
                    });
                }
            });
            
            const attrIds = Object.keys(selected);
            const combinations = this.cartesian(Object.values(selected));
            const baseSku = document.querySelector('[name="sku"]').value;
            
            this.variations = combinations.map((combo, i) => {
                const attributeDetails = {};
                combo.forEach((item, idx) => {
                    const attrId = attrIds[idx];
                    attributeDetails[attrId] = {
                        name: attrNames[attrId],
                        value: item.value
                    };
                });
                
                return {
                    name: combo.map(c => c.value).join(' - '),
                    sku: baseSku + '-' + (i+1),
                    price: this.basePrice || 0,
                    sale_price: 0,
                    stock_quantity: 0,
                    image: null,
                    gallery: [],
                    attributes: combo.map(c => c.id),
                    attributeDetails: attributeDetails
                };
            });
        },
        
        selectVariationImage(index) {
            this.currentVariationIndex = index;
            this.currentVariationMode = 'image';
        },
        
        selectVariationGallery(index) {
            this.currentVariationIndex = index;
            this.currentVariationMode = 'gallery';
        },
        
        removeVariationGalleryImage(varIndex, imgIndex) {
            this.variations[varIndex].gallery.splice(imgIndex, 1);
        },
        
        cartesian(arrays) {
            return arrays.reduce((a, b) => 
                a.flatMap(x => b.map(y => [...(Array.isArray(x) ? x : [x]), y])), [[]]
            );
        },
        
        removeGalleryImage(index) {
            this.gallery.splice(index, 1);
        },
        
        validateSalePrice() {
            if (this.salePrice && this.basePrice && parseFloat(this.salePrice) >= parseFloat(this.basePrice)) {
                this.salePriceError = 'Giá khuyến mãi phải thấp hơn giá gốc';
            } else {
                this.salePriceError = '';
            }
        },
        
        validateVariationPrice(index) {
            const variation = this.variations[index];
            if (variation.sale_price && variation.price && parseFloat(variation.sale_price) >= parseFloat(variation.price)) {
                variation.priceError = 'Giá KM phải thấp hơn giá gốc';
            } else {
                variation.priceError = '';
            }
        },
        
        init() {
            // Auto set main category and brand
            document.querySelectorAll('input[name="categories[]"]').forEach((cb, index) => {
                cb.addEventListener('change', () => {
                    const checked = document.querySelectorAll('input[name="categories[]"]:checked');
                    if (checked.length > 0) {
                        document.getElementById('mainCategory').value = checked[0].value;
                    } else {
                        document.getElementById('mainCategory').value = '';
                    }
                });
            });
            
            document.querySelectorAll('input[name="brands[]"]').forEach((cb, index) => {
                cb.addEventListener('change', () => {
                    const checked = document.querySelectorAll('input[name="brands[]"]:checked');
                    if (checked.length > 0) {
                        document.getElementById('mainBrand').value = checked[0].value;
                    } else {
                        document.getElementById('mainBrand').value = '';
                    }
                });
            });
        },
        
        analyzeSeo() {
            this.seoChecks = [];
            let score = 0;
            
            if (this.metaTitle.length === 0) {
                this.seoChecks.push({status: 'error', title: 'Thiếu tiêu đề SEO', message: 'Nên thêm tiêu đề SEO để tối ưu kết quả tìm kiếm'});
            } else if (this.metaTitle.length < 30) {
                this.seoChecks.push({status: 'warning', title: 'Tiêu đề SEO quá ngắn', message: `Chỉ có ${this.metaTitle.length} ký tự. Nên từ 50-60 ký tự`});
                score += 10;
            } else if (this.metaTitle.length > 60) {
                this.seoChecks.push({status: 'warning', title: 'Tiêu đề SEO quá dài', message: 'Có thể bị cắt trên kết quả tìm kiếm'});
                score += 10;
            } else {
                this.seoChecks.push({status: 'success', title: 'Tiêu đề SEO tốt', message: `${this.metaTitle.length} ký tự - Độ dài lý tưởng`});
                score += 20;
            }
            
            if (this.metaDesc.length === 0) {
                this.seoChecks.push({status: 'error', title: 'Thiếu mô tả SEO', message: 'Mô tả giúp tăng tỷ lệ click từ kết quả tìm kiếm'});
            } else if (this.metaDesc.length < 70) {
                this.seoChecks.push({status: 'warning', title: 'Mô tả SEO quá ngắn', message: `Chỉ có ${this.metaDesc.length} ký tự. Nên từ 120-160 ký tự`});
                score += 10;
            } else if (this.metaDesc.length > 160) {
                this.seoChecks.push({status: 'warning', title: 'Mô tả SEO quá dài', message: 'Có thể bị cắt trên kết quả tìm kiếm'});
                score += 10;
            } else {
                this.seoChecks.push({status: 'success', title: 'Mô tả SEO tốt', message: `${this.metaDesc.length} ký tự - Độ dài lý tưởng`});
                score += 20;
            }
            
            if (this.keyword.length === 0) {
                this.seoChecks.push({status: 'warning', title: 'Chưa có từ khóa chính', message: 'Nên thêm từ khóa để tối ưu SEO'});
            } else {
                score += 15;
                const keywordInTitle = this.metaTitle.toLowerCase().includes(this.keyword.toLowerCase());
                const keywordInDesc = this.metaDesc.toLowerCase().includes(this.keyword.toLowerCase());
                
                if (keywordInTitle && keywordInDesc) {
                    this.seoChecks.push({status: 'success', title: 'Từ khóa xuất hiện tốt', message: 'Từ khóa có trong cả tiêu đề và mô tả'});
                    score += 15;
                } else if (keywordInTitle || keywordInDesc) {
                    this.seoChecks.push({status: 'warning', title: 'Từ khóa chưa tối ưu', message: 'Nên thêm từ khóa vào cả tiêu đề và mô tả'});
                    score += 10;
                } else {
                    this.seoChecks.push({status: 'error', title: 'Từ khóa không xuất hiện', message: 'Từ khóa chưa có trong tiêu đề và mô tả'});
                }
            }
            
            if (this.schemaType) {
                this.seoChecks.push({status: 'success', title: 'Có Schema Markup', message: `Đang dùng schema: ${this.schemaType}`});
                score += 15;
            } else {
                this.seoChecks.push({status: 'warning', title: 'Chưa có Schema Markup', message: 'Schema giúp Google hiểu rõ hơn về nội dung'});
            }
            
            if (this.canonicalUrl) {
                this.seoChecks.push({status: 'success', title: 'Có Canonical URL', message: 'Giúp tránh nội dung trùng lặp'});
                score += 10;
            }
            
            if (this.noindex) {
                this.seoChecks.push({status: 'warning', title: 'Đang bật Noindex', message: 'Trang này sẽ không xuất hiện trên Google'});
            } else {
                score += 5;
            }
            
            this.seoScore = Math.min(score, 100);
        }
    }
}
</script>
@endsection
