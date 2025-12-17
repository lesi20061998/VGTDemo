@extends('cms.layouts.app')

@section('title', 'Sửa sản phẩm')
@section('page-title', 'Chỉnh sửa sản phẩm')

@section('content')
<form method="POST" action="{{ isset($currentProject) && $currentProject ? route('project.admin.products.update', [$currentProject->code, $product]) : route('cms.products.update', $product) }}" enctype="multipart/form-data" x-data="productForm()">
    @csrf @method('PUT')
    
    <!-- Header với nút Lưu/Hủy -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6 sticky top-0 z-10">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Chỉnh sửa sản phẩm</h1>
                <p class="text-sm text-gray-500">{{ $product->name }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ isset($currentProject) && $currentProject ? route('project.admin.products.index', $currentProject->code) : route('cms.products.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Hủy
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Cập nhật
                </button>
            </div>
        </div>
    </div>

    <!-- Language Switcher -->
    @if(setting('multilingual_enabled', false))
        @include('cms.components.language-switcher', ['model' => $allLanguageVersions ?? collect()])
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Cột trái: Form chính -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Thông tin cơ bản -->
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">SKU *</label>
                        <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('sku') border-red-500 @enderror">
                        @error('sku')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                        <input type="text" name="slug" x-model="slug" value="{{ old('slug', $product->slug) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Nội dung sản phẩm -->
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-6">
                <h2 class="font-semibold text-gray-900 mb-4">Nội dung sản phẩm</h2>
                
                @php
                    $editProduct = isset($languageProduct) ? $languageProduct : $product;
                @endphp
                
                <!-- Tên sản phẩm -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tên sản phẩm *
                    </label>
                    <input type="text" name="name" value="{{ old('name', $editProduct->name) }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Mô tả ngắn -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả ngắn</label>
                    <textarea name="short_description" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('short_description', $editProduct->short_description) }}</textarea>
                </div>

                <!-- Mô tả đầy đủ -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Mô tả đầy đủ *
                    </label>
                    <div class="summernote-container">
                        <textarea name="description" class="summernote" required>{{ old('description', $editProduct->description) }}</textarea>
                    </div>
                    @error('description')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>


            </div>
                @include('cms.components.multilingual-tabs', ['model' => $product])
            </div>

            <!-- SEO Analyzer -->
            @include('cms.components.seo-analyzer', ['contentType' => 'sản phẩm'])

            <!-- Giá & Danh mục -->
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
                <h2 class="font-semibold text-gray-900">Giá & Phân loại</h2>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Giá gốc (₫)</label>
                        <input type="number" name="price" value="{{ old('price', $product->price) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Giá khuyến mãi (₫)</label>
                        <input type="number" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>


            </div>
        </div>

        <!-- Cột phải: Sidebar -->
        <div class="space-y-6">
            <!-- Đặt lịch đăng -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Xuất bản</h2>
                
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                        <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="draft" {{ old('status', $product->status) == 'draft' ? 'selected' : '' }}>Nháp</option>
                            <option value="published" {{ old('status', $product->status) == 'published' ? 'selected' : '' }}>Xuất bản</option>
                            <option value="archived" {{ old('status', $product->status) == 'archived' ? 'selected' : '' }}>Lưu trữ</option>
                        </select>
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Sản phẩm nổi bật</span>
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Đặt lịch đăng</label>
                        <input type="datetime-local" name="published_at" value="{{ old('published_at', $product->published_at?->format('Y-m-d\TH:i')) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#98191F] focus:border-[#98191F]">
                        <p class="text-xs text-gray-500 mt-1">Để trống để đăng ngay</p>
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
                @include('cms.components.media-manager')
            </div>
                </div>
            </div>

            <!-- Danh mục -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Danh mục sản phẩm</h2>
                <div class="max-h-64 overflow-y-auto border rounded-lg p-3 space-y-2">
                    @foreach($categories ?? [] as $cat)
                    <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
                        <input type="checkbox" name="categories[]" value="{{ $cat->id }}" 
                               {{ old('product_category_id', $product->product_category_id) == $cat->id ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 mr-2">
                        <span class="text-sm">{{ $cat->name }}</span>
                    </label>
                    @endforeach
                </div>
                <input type="hidden" name="product_category_id" id="mainCategory" value="{{ $product->product_category_id }}">
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
                        <input type="checkbox" name="brands[]" value="{{ $brand->id }}"
                               {{ old('brand_id', $product->brand_id) == $brand->id ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 mr-2">
                        <span class="text-sm">{{ $brand->name }}</span>
                    </label>
                    @endforeach
                </div>
                <input type="hidden" name="brand_id" id="mainBrand" value="{{ $product->brand_id }}">
            </div>

            <!-- Gallery sản phẩm -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Gallery sản phẩm</h2>
                
                <div class="space-y-3">
                    <div class="grid grid-cols-3 gap-2" x-show="gallery.length > 0">
                        <template x-for="(img, index) in gallery" :key="index">
                            <div class="relative aspect-square border rounded-lg overflow-hidden group">
                                <img :src="img.url" class="w-full h-full object-cover">
                                <input type="hidden" name="gallery[]" :value="img.url">
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
        </div>
    </div>
</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
function productForm() {
    return {
        slug: @json($product->slug ?? ''),
        featuredImage: @json($product->featured_image ?? ''),
        gallery: @json(collect($product->gallery ?? [])->map(fn($url) => ['url' => $url])->toArray()),
        currentGalleryMode: false,
        metaTitle: '{{ old('meta_title', $product->meta_title ?? '') }}',
        metaDesc: '{{ old('meta_description', $product->meta_description ?? '') }}',
        keyword: '{{ old('focus_keyword', $product->focus_keyword ?? '') }}',
        schemaType: '{{ old('schema_type', $product->schema_type ?? '') }}',
        canonicalUrl: '{{ old('canonical_url', $product->canonical_url ?? '') }}',
        noindex: {{ old('noindex', $product->noindex ?? false) ? 'true' : 'false' }},
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
                const items = e.detail.items;
                
                if (this.currentGalleryMode) {
                    items.forEach(item => {
                        if (!this.gallery.some(g => g.url === item.url)) {
                            this.gallery.push({ id: item.id, url: item.url });
                        }
                    });
                    this.currentGalleryMode = false;
                } else {
                    this.featuredImage = items[0].url;
                }
            });
            
            window.addEventListener('open-media-manager', (e) => {
                if (e.detail && e.detail.mode === 'gallery') {
                    this.currentGalleryMode = true;
                }
            });
            
            this.analyzeSeo();
        },
        
        generateSlug(name) {
            if (!this.slug) {
                this.slug = name.toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/[^a-z0-9\s-]/g, '')
                    .trim()
                    .replace(/\s+/g, '-');
            }
        },
        
        removeGalleryImage(index) {
            this.gallery.splice(index, 1);
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
