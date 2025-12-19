@extends('admin.layouts.app')

@section('title', 'Sửa sản phẩm')
@section('page-title', 'Chỉnh sửa sản phẩm')

@section('content')
<form method="POST" action="{{ route('project.admin.products.update', [request()->route('projectCode'), $product]) }}" enctype="multipart/form-data" x-data="productForm()" @media-selected.window="handleMediaSelected($event)">
    @csrf
    @method('PUT')
    
    <!-- Header với nút Lưu/Hủy -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6 sticky top-0 z-10">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Chỉnh sửa sản phẩm</h1>
                <p class="text-sm text-gray-500 mt-1">ID: {{ $product->id }} | SKU: {{ $product->sku }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('project.admin.products.index', request()->route('projectCode')) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">Hủy</a>
                <button type="submit" class="inline-flex items-center px-6 py-2 bg-[#98191F] text-white rounded-lg hover:bg-[#7a1419] transition">Cập nhật</button>
            </div>
        </div>
    </div>

    <!-- Language Switcher -->
    @if(file_exists(resource_path('views/admin/components/language-switcher.blade.php')))
        @include('admin.components.language-switcher')
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Cột trái: Form chính -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Thông tin cơ bản -->
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
                <!-- Tên sản phẩm -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tên sản phẩm *
                    </label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" 
                           @input="generateSlug($event.target.value)"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#98191F] @error('name') border-red-500 @enderror">
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
                        <input type="text" name="slug" x-model="slug" value="{{ old('slug', $product->slug) }}"
                               placeholder="Tự động tạo từ tên sản phẩm..."
                               @focus="$event.target.classList.remove('bg-gray-50'); $event.target.classList.add('bg-white')"
                               @blur="if(!$event.target.value) { $event.target.classList.add('bg-gray-50'); $event.target.classList.remove('bg-white'); }"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#98191F] bg-gray-50 transition-colors">
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
                        Mô tả đầy đủ *
                    </label>
                    <div class="ckeditor-container">
                        <textarea name="description" id="description" class="ckeditor">{{ old('description', $product->description) }}</textarea>
                    </div>
                    @error('description')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Dữ liệu sản phẩm - Tabs -->
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
                        </nav>
                        <div class="flex items-center gap-2">
                            <label class="text-sm text-gray-600">Loại:</label>
                            <select name="product_type" x-model="productType" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#98191F]">
                                <option value="simple" {{ $product->product_type == 'simple' ? 'selected' : '' }}>Đơn giản</option>
                                <option value="variable" {{ $product->product_type == 'variable' ? 'selected' : '' }}>Biến thể</option>
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
                                <input type="number" name="price" value="{{ old('price', $product->price) }}" x-model="basePrice" @input="validateSalePrice()"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#98191F]">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Giá khuyến mãi (₫)</label>
                                <input type="number" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" x-model="salePrice" @input="validateSalePrice()"
                                       :max="basePrice" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#98191F]">
                                <p x-show="salePriceError" class="text-red-600 text-sm mt-1" x-text="salePriceError"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Kho hàng -->
                    <div x-show="activeTab === 'inventory'" class="space-y-4">
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="manage_stock" value="1" {{ $product->manage_stock ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
                                <span class="ml-2 text-sm text-gray-700">Quản lý tồn kho</span>
                            </label>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Số lượng</label>
                            <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#98191F]">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái kho</label>
                            <select name="stock_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#98191F]">
                                <option value="in_stock" {{ $product->stock_status == 'in_stock' ? 'selected' : '' }}>Còn hàng</option>
                                <option value="out_of_stock" {{ $product->stock_status == 'out_of_stock' ? 'selected' : '' }}>Hết hàng</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột phải: Sidebar -->
        <div class="space-y-6">
            <!-- Publish -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="mb-4">
                    <h2 class="font-semibold text-gray-900">Cài đặt sản phẩm</h2>
                </div>
                
                <div class="space-y-3 text-sm">
                    <!-- Status -->
                    <div class="flex items-center justify-between py-2 border-b">
                        <span class="text-gray-600">Trạng thái:</span>
                        <select name="status" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#98191F]">
                            <option value="draft" {{ $product->status == 'draft' ? 'selected' : '' }}>Nháp</option>
                            <option value="published" {{ $product->status == 'published' ? 'selected' : '' }}>Đã xuất bản</option>
                            <option value="archived" {{ $product->status == 'archived' ? 'selected' : '' }}>Lưu trữ</option>
                        </select>
                    </div>



                    <!-- Featured -->
                    <div class="py-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_featured" value="1" {{ $product->is_featured ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-0">
                            <span class="ml-2 text-gray-700">Sản phẩm nổi bật</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Hình ảnh sản phẩm -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Hình ảnh sản phẩm</h2>
                
                <!-- Ảnh đại diện -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ảnh đại diện *</label>
                    <div class="border-2 border-dashed rounded-lg overflow-hidden bg-gray-50 h-40 flex items-center justify-center mb-3">
                        <template x-if="featuredImage">
                            <img :src="featuredImage" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!featuredImage">
                            <div class="text-center text-gray-400">
                                <svg class="w-10 h-10 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-sm">Chưa có ảnh đại diện</p>
                            </div>
                        </template>
                    </div>
                    <div @click="selectFeaturedImage()">
                        @include('admin.components.media-manager', ['slot' => 'Chọn ảnh đại diện'])
                    </div>
                    <input type="hidden" name="featured_image" x-model="featuredImage">
                    @error('featured_image')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Gallery -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Gallery (<span x-text="gallery.length"></span> ảnh)
                    </label>
                    <div class="border-2 border-dashed rounded-lg p-3 bg-gray-50 h-32 overflow-y-auto mb-3">
                        <div class="grid grid-cols-2 gap-2" x-show="gallery.length > 0">
                            <template x-for="(img, index) in gallery" :key="index">
                                <div class="relative aspect-square border rounded overflow-hidden group">
                                    <img :src="img" class="w-full h-full object-cover">
                                    <button type="button" @click.stop="removeGalleryImage(index)" 
                                            class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                        <div x-show="gallery.length === 0" class="h-full flex items-center justify-center text-gray-400">
                            <div class="text-center">
                                <svg class="w-8 h-8 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <p class="text-xs">Chưa có ảnh gallery</p>
                            </div>
                        </div>
                    </div>
                    <div @click="selectGallery()">
                        @include('admin.components.media-manager', ['slot' => '+ Thêm ảnh gallery', 'multiple' => true])
                    </div>
                    <input type="hidden" name="gallery" :value="JSON.stringify(gallery)">
                </div>
            </div>

            <!-- Danh mục -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-gray-900">Danh mục</h2>
                    <span class="text-xs text-gray-500">Có thể chọn nhiều</span>
                </div>
                
                @if($parentCategories->isEmpty())
                    <div class="text-center py-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-500 mb-2">Chưa có danh mục nào</p>
                        <a href="{{ route('project.admin.categories.create', request()->route('projectCode')) }}" class="text-sm text-blue-600 hover:text-blue-800">+ Tạo danh mục mới</a>
                    </div>
                @else
                    <div class="max-h-64 overflow-y-auto border rounded-lg p-3 space-y-1">
                        @php
                            // Get selected categories from pivot table
                            $selectedCategories = $product->categories->pluck('id')->toArray();
                            // Fallback to single category if no pivot data
                            if (empty($selectedCategories) && $product->product_category_id) {
                                $selectedCategories = [$product->product_category_id];
                            }
                        @endphp
                        
                        @foreach($parentCategories as $parent)
                            <!-- Parent Category -->
                            <div class="mb-2">
                                <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer group">
                                    <input type="checkbox" name="categories[]" value="{{ $parent->id }}" 
                                           {{ in_array($parent->id, $selectedCategories) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3">
                                    <span class="text-sm text-gray-800 group-hover:text-blue-700 flex-1 font-medium">{{ $parent->name }}</span>
                                </label>
                                
                                <!-- Child Categories -->
                                @php
                                    $children = $parent->children ?? collect();
                                @endphp
                                
                                @if($children->count() > 0)
                                    <div class="ml-6 mt-1 space-y-1">
                                        @foreach($children as $child)
                                            <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer group">
                                                <input type="checkbox" name="categories[]" value="{{ $child->id }}" 
                                                       {{ in_array($child->id, $selectedCategories) ? 'checked' : '' }}
                                                       class="rounded border-gray-300 text-green-600 focus:ring-green-500 mr-3">
                                                <span class="text-sm text-gray-700 group-hover:text-green-700 flex-1">└─ {{ $child->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
                
                @error('categories')<p class="text-red-600 text-sm mt-2">{{ $message }}</p>@enderror
            </div>

            <!-- Thương hiệu -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-gray-900">Thương hiệu</h2>
                    <span class="text-xs text-gray-500">Có thể chọn nhiều</span>
                </div>
                
                @if($brands->isEmpty())
                    <div class="text-center py-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-500 mb-2">Chưa có thương hiệu nào</p>
                        <a href="{{ route('project.admin.brands.create', request()->route('projectCode')) }}" class="text-sm text-blue-600 hover:text-blue-800">+ Tạo thương hiệu mới</a>
                    </div>
                @else
                    <div class="max-h-64 overflow-y-auto border rounded-lg p-3 space-y-1">
                        @php
                            // Get selected brands from pivot table
                            $selectedBrands = $product->brands->pluck('id')->toArray();
                            // Fallback to single brand if no pivot data
                            if (empty($selectedBrands) && $product->brand_id) {
                                $selectedBrands = [$product->brand_id];
                            }
                        @endphp
                        
                        @foreach($brands as $brand)
                            <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer group">
                                <input type="checkbox" name="brands[]" value="{{ $brand->id }}" 
                                       {{ in_array($brand->id, $selectedBrands) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3">
                                <div class="flex items-center flex-1">
                                    @if($brand->logo)
                                        <img src="{{ $brand->logo }}" alt="{{ $brand->name }}" class="w-6 h-6 rounded mr-2 object-cover">
                                    @endif
                                    <span class="text-sm text-gray-700 group-hover:text-blue-700">{{ $brand->name }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @endif
                
                @error('brands')<p class="text-red-600 text-sm mt-2">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>
</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
function productForm() {
    return {
        slug: '{{ $product->slug }}',
        activeTab: 'general',
        basePrice: {{ $product->price ?? 0 }},
        salePrice: {{ $product->sale_price ?? 0 }},
        salePriceError: '',
        productType: '{{ $product->product_type }}',
        featuredImage: '{{ $product->featured_image ?? '' }}',
        gallery: @json($product->gallery ?? []),
        mediaTarget: null,
        
        generateSlug(name) {
            if (!name) return;
            this.slug = name.toLowerCase()
                .replace(/[àáạảãâầấậẩẫăằắặẳẵ]/g, 'a')
                .replace(/[èéẹẻẽêềếệểễ]/g, 'e')
                .replace(/[ìíịỉĩ]/g, 'i')
                .replace(/[òóọỏõôồốộổỗơờớợởỡ]/g, 'o')
                .replace(/[ùúụủũưừứựửữ]/g, 'u')
                .replace(/[ỳýỵỷỹ]/g, 'y')
                .replace(/đ/g, 'd')
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '')
                .replace(/-+/g, '-')
                .trim('-');
        },
        
        validateSalePrice() {
            if (this.salePrice && this.basePrice && parseFloat(this.salePrice) > parseFloat(this.basePrice)) {
                this.salePriceError = 'Giá khuyến mãi phải nhỏ hơn giá gốc';
            } else {
                this.salePriceError = '';
            }
        },
        
        selectFeaturedImage() {
            this.mediaTarget = 'featured';
        },
        
        selectGallery() {
            this.mediaTarget = 'gallery';
        },
        
        handleMediaSelected(event) {
            const selectedMedia = event.detail;
            
            if (this.mediaTarget === 'featured') {
                this.featuredImage = selectedMedia.files[0]?.url || selectedMedia[0]?.url;
            } else if (this.mediaTarget === 'gallery') {
                const newImages = selectedMedia.files ? selectedMedia.files.map(f => f.url) : selectedMedia.map(f => f.url || f);
                this.gallery = [...this.gallery, ...newImages];
            }
            
            this.mediaTarget = null;
        },
        
        removeGalleryImage(index) {
            this.gallery.splice(index, 1);
        },
        
        init() {
            this.initCKEditor();
            // Initialize images from existing product data
            if (!this.featuredImage && '{{ $product->featured_image ?? '' }}') {
                this.featuredImage = '{{ $product->featured_image ?? '' }}';
            }
        },
        
        initCKEditor() {
            setTimeout(() => {
                const descriptionElement = document.getElementById('description');
                if (descriptionElement) {
                    ClassicEditor
                        .create(descriptionElement, {
                            toolbar: {
                                items: [
                                    'heading', '|',
                                    'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|',
                                    'blockQuote', 'insertTable', '|',
                                    'imageUpload', 'mediaEmbed', '|',
                                    'undo', 'redo'
                                ]
                            },
                            language: 'vi'
                        })
                        .then(editor => {
                            console.log('CKEditor initialized successfully');
                        })
                        .catch(error => {
                            console.error('CKEditor initialization failed:', error);
                        });
                }
            }, 100);
        }
    }
}
</script>
@endsection
