{{-- MODIFIED: 2025-01-21 --}}
@extends('admin.layouts.app')

@section('title', 'Quản lý sản phẩm')
@section('page-title', 'Sản phẩm')

@section('content')
<!-- Multi-site Breadcrumb -->

<!-- Multi-site Project Header -->
<div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg shadow-sm p-4 mb-6 text-white">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="bg-white/20 rounded-lg p-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold">Dự án: {{ strtoupper($currentProject->code ?? request()->route('projectCode')) }}</h1>
                <p class="text-blue-100 text-sm">Quản lý sản phẩm multi-site</p>
            </div>
        </div>
        <div class="flex items-center space-x-4 text-sm">
            <div class="bg-white/20 rounded-lg px-3 py-2">
                <span class="font-medium">Database:</span> 
                <span class="font-mono">project_{{ strtolower($currentProject->code ?? request()->route('projectCode')) }}</span>
            </div>
            <div class="bg-white/20 rounded-lg px-3 py-2">
                <span class="font-medium">Ngôn ngữ:</span> 
                <span class="font-bold">
                    @if(($languageId ?? 1) == 1) Tiếng Việt
                    @elseif(($languageId ?? 1) == 2) English  
                    @elseif(($languageId ?? 1) == 3) 中文
                    @else ID: {{ $languageId ?? 1 }}
                    @endif
                </span>
            </div>
            <div class="bg-white/20 rounded-lg px-3 py-2">
                <span class="font-medium">Sản phẩm:</span> 
                <span class="font-bold">{{ $products->total() }}</span>
            </div>
        </div>
    </div>
</div>

<div class="flex justify-between items-center mb-6">
    <div class="flex space-x-4">
        <form method="GET" class="flex space-x-2">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Tìm kiếm sản phẩm..." 
                   class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            
            <select name="language_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="1" {{ ($languageId ?? 1) == 1 ? 'selected' : '' }}>Tiếng Việt (Mặc định)</option>
                <option value="2" {{ ($languageId ?? 1) == 2 ? 'selected' : '' }}>English</option>
                <option value="3" {{ ($languageId ?? 1) == 3 ? 'selected' : '' }}>中文</option>
            </select>
            
            <select name="category" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Tất cả danh mục</option>
                @foreach($parentCategories as $parent)
                    <optgroup label="{{ $parent->name }}">
                        <option value="{{ $parent->id }}" {{ request('category') == $parent->id ? 'selected' : '' }}>-- {{ $parent->name }} (Tất cả)</option>
                        @foreach($parent->children as $child)
                            <option value="{{ $child->id }}" {{ request('category') == $child->id ? 'selected' : '' }}>
                                {{ $child->name }}
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
            
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Tất cả trạng thái</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Nháp</option>
                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Đã xuất bản</option>
                <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Lưu trữ</option>
            </select>
            
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Lọc
            </button>
        </form>
    </div>
    
    <div class="flex space-x-2">
        <button id="bulkEditBtn" onclick="openBulkEdit()" class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 disabled:opacity-50 transition-colors" disabled>
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Sửa nhanh (<span id="selectedCount">0</span>)
        </button>
        <a href="{{ route('project.admin.categories.index', request()->route('projectCode')) }}" 
           class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            Danh mục
        </a>
        <a href="{{ route('project.admin.products.create', request()->route('projectCode')) }}" 
           class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Thêm sản phẩm
        </a>
    </div>
</div>

<!-- Quick Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-500">Tổng sản phẩm</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $products->total() }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-500">Đã xuất bản</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $products->where('status', 'published')->count() }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-yellow-500">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-500">Nháp</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $products->where('status', 'draft')->count() }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-purple-500">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-500">Danh mục</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $parentCategories->count() }}</p>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden"></div>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600">
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <div class="flex items-center space-x-1">
                        <span>Sản phẩm</span>
                        <div class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">Multi-site</div>
                    </div>
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Danh mục & Thương hiệu</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giá</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kho</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
         
            @forelse($products as $product)
           
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <input type="checkbox" class="product-checkbox rounded border-gray-300 text-blue-600" value="{{ $product->id }}">
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                            @if($product->featured_image)
                                <img class="h-10 w-10 rounded-full object-cover" src="{{ $product->featured_image }}" alt="">
                            @else
                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                    <span class="text-gray-600 text-xs">IMG</span>
                                </div>
                            @endif
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                            <div class="text-sm text-gray-500">{{ Str::limit($product->short_description, 50) }}</div>
                            <div class="flex items-center space-x-2 mt-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h6l2 2h6a2 2 0 012 2v4a2 2 0 01-2 2H5z"></path>
                                    </svg>
                                    {{ strtoupper($currentProject->code ?? request()->route('projectCode')) }}
                                </span>

                            </div>
                            
                            <!-- Badge Toggles -->
                            <div class="flex items-center space-x-2 mt-2">
                                <span class="text-xs text-gray-500 mr-2">Badges:</span>
                                <!-- Featured Toggle -->
                                <button onclick="toggleBadge({{ $product->id }}, 'featured')" 
                                        class="inline-flex items-center justify-center w-9 h-9 rounded {{ $product->is_featured ? 'bg-yellow-200 text-yellow-800' : 'bg-gray-100 text-gray-400' }} hover:bg-yellow-300 transition-colors" 
                                        title="Nổi bật">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                </button>
                                
                                <!-- Favorite Toggle -->
                                <button onclick="toggleBadge({{ $product->id }}, 'favorite')" 
                                        class="inline-flex items-center justify-center w-9 h-9 rounded {{ $product->is_favorite ? 'bg-red-200 text-red-800' : 'bg-gray-100 text-gray-400' }} hover:bg-red-300 transition-colors" 
                                        title="Yêu thích">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                                
                                <!-- Bestseller Toggle -->
                                <button onclick="toggleBadge({{ $product->id }}, 'bestseller')" 
                                        class="inline-flex items-center justify-center w-9 h-9 rounded {{ $product->is_bestseller ? 'bg-green-200 text-green-800' : 'bg-gray-100 text-gray-400' }} hover:bg-green-300 transition-colors" 
                                        title="Bán chạy">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $product->sku }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">
                    <!-- Categories -->
                    @if($product->categories && $product->categories->count() > 0)
                        <div class="mb-2">
                            <div class="text-xs font-medium text-gray-600 mb-1">Danh mục:</div>
                            <div class="flex flex-wrap gap-1">
                                @foreach($product->categories as $category)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $category->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    <!-- Brands -->
                    @if($product->brands && $product->brands->count() > 0)
                        <div class="mb-2">
                            <div class="text-xs font-medium text-gray-600 mb-1">Thương hiệu:</div>
                            <div class="flex flex-wrap gap-1">
                                @foreach($product->brands as $brand)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                        {{ $brand->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    <!-- Summary -->
                    @if(($product->categories && $product->categories->count() > 0) || ($product->brands && $product->brands->count() > 0))
                        <div class="text-xs text-gray-400">
                            {{ $product->categories->count() ?? 0 }} danh mục, {{ $product->brands->count() ?? 0 }} thương hiệu
                        </div>
                    @else
                        <span class="text-gray-400">Chưa phân loại</span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ $product->display_price }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        {{ $product->stock_status === 'in_stock' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $product->stock_quantity }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        {{ $product->status === 'published' ? 'bg-green-100 text-green-800' : 
                           ($product->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ ucfirst($product->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex items-center space-x-1">
                        <!-- View -->
                        <a href="{{ route('project.admin.products.show', [request()->route('projectCode'), $product->id]) }}" 
                           class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-colors" 
                           title="Xem chi tiết">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </a>

                        
                        <!-- Quick Edit -->
                        <button onclick="openSingleQuickEdit({{ $product->id }})" 
                                class="inline-flex items-center justify-center w-8 h-8 bg-green-100 text-green-600 rounded-lg hover:bg-green-200 transition-colors" 
                                title="Sửa nhanh">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                            </svg>
                        </button>
                        
                        <!-- Edit -->
                        <a href="{{ route('project.admin.products.edit', [request()->route('projectCode'), $product]) }}" 
                           class="inline-flex items-center justify-center w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg hover:bg-indigo-200 transition-colors" 
                           title="Chỉnh sửa">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                        
                        <!-- Delete -->
                        <form method="POST" action="{{ route('project.admin.products.destroy', [request()->route('projectCode'), $product]) }}" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" 
                                    class="inline-flex items-center justify-center w-8 h-8 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors" 
                                    title="Xóa" 
                                    onclick="return confirm('Bạn có chắc muốn xóa?')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center justify-center space-y-4">
                        <div class="bg-gray-100 rounded-full p-4">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <div class="text-center">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Chưa có sản phẩm nào</h3>
                            <p class="text-gray-500 mb-4">Dự án <strong>{{ strtoupper($currentProject->code ?? request()->route('projectCode')) }}</strong> chưa có sản phẩm nào được tạo.</p>
                            <a href="{{ route('project.admin.products.create', request()->route('projectCode')) }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tạo sản phẩm đầu tiên
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $products->links() }}
</div>

<!-- Bulk Edit Modal -->
<div id="bulkEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-6xl w-full max-h-[90vh] overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-medium text-gray-900">Sửa nhanh sản phẩm</h3>
            </div>
            <div class="grid grid-cols-12 gap-4 p-6">
                <!-- Product Selection - 3 columns -->
                <div class="col-span-3">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Sản phẩm sẽ cập nhật:</h4>
                    <div id="productList" class="space-y-2 max-h-96 overflow-y-auto border rounded-md p-3 bg-gray-50">
                        <!-- Dynamic product list -->
                    </div>
                </div>
                
                <!-- Bulk Edit Form - 9 columns -->
                <div class="col-span-9">
                    <form id="bulkEditForm">
                        @csrf
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Danh mục</label>
                                <div id="categoryCheckboxes" class="max-h-40 overflow-y-auto border rounded-md p-3 bg-white">
                                    <!-- Category checkboxes will be populated -->
                                </div>
                                <small class="text-gray-500">Không chọn = không thay đổi</small>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Thương hiệu</label>
                                <div id="brandCheckboxes" class="max-h-40 overflow-y-auto border rounded-md p-3 bg-white">
                                    <!-- Brand checkboxes will be populated -->
                                </div>
                                <small class="text-gray-500">Không chọn = không thay đổi</small>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                                <select id="bulkStatus" name="status" class="w-full px-3 py-2 border rounded-md">
                                    <option value="">Không thay đổi</option>
                                    <option value="draft">Nháp</option>
                                    <option value="published">Đã xuất bản</option>
                                    <option value="archived">Lưu trữ</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Giá</label>
                                <input type="number" id="bulkPrice" name="price" placeholder="Không thay đổi" class="w-full px-3 py-2 border rounded-md">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Giá khuyến mãi</label>
                                <input type="number" id="bulkSalePrice" name="sale_price" placeholder="Không thay đổi" class="w-full px-3 py-2 border rounded-md">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Huy hiệu</label>
                                <div class="grid grid-cols-3 gap-4 p-3 border rounded-md bg-white">
                                    <div class="flex flex-col items-center space-y-2">
                                        <div class="flex items-center space-x-2">
                                            <input type="checkbox" id="bulkFeatured" name="badges[]" value="featured" class="rounded">
                                            <label for="bulkFeatured" class="text-sm font-medium">Nổi bật</label>
                                        </div>
                                        <div class="w-8 h-8 bg-yellow-200 text-yellow-800 rounded flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-center space-y-2">
                                        <div class="flex items-center space-x-2">
                                            <input type="checkbox" id="bulkFavorite" name="badges[]" value="favorite" class="rounded">
                                            <label for="bulkFavorite" class="text-sm font-medium">Yêu thích</label>
                                        </div>
                                        <div class="w-8 h-8 bg-red-200 text-red-800 rounded flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-center space-y-2">
                                        <div class="flex items-center space-x-2">
                                            <input type="checkbox" id="bulkBestseller" name="badges[]" value="bestseller" class="rounded">
                                            <label for="bulkBestseller" class="text-sm font-medium">Bán chạy</label>
                                        </div>
                                        <div class="w-8 h-8 bg-green-200 text-green-800 rounded flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <small class="text-gray-500 mt-1">Chọn huy hiệu để áp dụng cho tất cả sản phẩm đã chọn</small>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-2">
                <button type="button" onclick="closeBulkEdit()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Hủy</button>
                <button type="button" id="bulkUpdateBtn" onclick="saveBulkEdit()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                    <span id="bulkUpdateText">Cập nhật tất cả</span>
                    <svg id="bulkUpdateSpinner" class="animate-spin -mr-1 ml-2 h-4 w-4 text-white hidden" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let selectedProducts = [];

// Checkbox handling
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateSelectedProducts();
});

document.querySelectorAll('.product-checkbox').forEach(cb => {
    cb.addEventListener('change', updateSelectedProducts);
});

function updateSelectedProducts() {
    selectedProducts = Array.from(document.querySelectorAll('.product-checkbox:checked')).map(cb => cb.value);
    document.getElementById('selectedCount').textContent = selectedProducts.length;
    document.getElementById('bulkEditBtn').disabled = selectedProducts.length === 0;
}

function openBulkEdit() {
    if (selectedProducts.length === 0) return;
    
    console.log('Opening bulk edit for products:', selectedProducts);
    
    // Always use project route
    const bulkEditUrl = '{{ route("project.admin.products.bulk-edit", request()->route("projectCode")) }}';
    
    fetch(bulkEditUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ ids: selectedProducts })
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            renderBulkEditTable(data.products, data.categories, data.brands);
            document.getElementById('bulkEditModal').classList.remove('hidden');
        } else {
            showNotification('Lỗi: ' + (data.message || 'Không thể tải dữ liệu'), 'error');
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        showNotification('Có lỗi xảy ra khi tải dữ liệu: ' + error.message, 'error');
    });
}

function openSingleQuickEdit(productId) {
    console.log('Opening quick edit for product:', productId);
    
    // Always use project route
    const bulkEditUrl = '{{ route("project.admin.products.bulk-edit", request()->route("projectCode")) }}';
    
    fetch(bulkEditUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ ids: [productId] })
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            renderBulkEditTable(data.products, data.categories, data.brands);
            document.getElementById('bulkEditModal').classList.remove('hidden');
        } else {
            showNotification('Lỗi: ' + (data.message || 'Không thể tải dữ liệu sản phẩm'), 'error');
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        showNotification('Có lỗi xảy ra khi tải dữ liệu sản phẩm: ' + error.message, 'error');
    });
}

let bulkProducts = [];

function renderBulkEditTable(products, categories, brands) {
    bulkProducts = products;
    
    // Render product list
    const productList = document.getElementById('productList');
    productList.innerHTML = products.map(product => `
        <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
            <span class="text-sm">${product.name}</span>
            <button type="button" onclick="removeProduct(${product.id})" class="text-red-600 hover:text-red-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `).join('');
    
    // Get all selected categories, brands, and badges from all products
    let allSelectedCategories = [];
    let allSelectedBrands = [];
    let commonBadges = { featured: true, favorite: true, bestseller: true };
    
    products.forEach(product => {
        if (product.selected_categories) {
            allSelectedCategories = [...allSelectedCategories, ...product.selected_categories];
        }
        if (product.selected_brands) {
            allSelectedBrands = [...allSelectedBrands, ...product.selected_brands];
        }
        
        // Check common badges (only check if ALL products have the badge)
        if (!product.is_featured) commonBadges.featured = false;
        if (!product.is_favorite) commonBadges.favorite = false;
        if (!product.is_bestseller) commonBadges.bestseller = false;
    });
    
    // Remove duplicates
    allSelectedCategories = [...new Set(allSelectedCategories)];
    allSelectedBrands = [...new Set(allSelectedBrands)];
    
    // Populate category checkboxes with pre-selected (multiple categories)
    const categoryCheckboxes = `
        <div class="flex items-center mb-2 pb-2 border-b">
            <input type="checkbox" id="selectAllCategories" class="mr-2 rounded" onchange="toggleAllCheckboxes('categories[]', this.checked)">
            <label for="selectAllCategories" class="text-sm font-semibold">Chọn tất cả</label>
        </div>
    ` + categories.map(cat => `
        <div class="flex items-center mb-2">
            <input type="checkbox" id="cat_${cat.id}" name="categories[]" value="${cat.id}" class="mr-2 rounded category-checkbox" ${allSelectedCategories.includes(cat.id) ? 'checked' : ''}>
            <label for="cat_${cat.id}" class="text-sm cursor-pointer" onclick="document.getElementById('cat_${cat.id}').click()">${cat.name}</label>
        </div>
    `).join('');
    
    // Populate brand checkboxes with pre-selected (multiple brands)
    const brandCheckboxes = `
        <div class="flex items-center mb-2 pb-2 border-b">
            <input type="checkbox" id="selectAllBrands" class="mr-2 rounded" onchange="toggleAllCheckboxes('brands[]', this.checked)">
            <label for="selectAllBrands" class="text-sm font-semibold">Chọn tất cả</label>
        </div>
    ` + brands.map(brand => `
        <div class="flex items-center mb-2">
            <input type="checkbox" id="brand_${brand.id}" name="brands[]" value="${brand.id}" class="mr-2 rounded brand-checkbox" ${allSelectedBrands.includes(brand.id) ? 'checked' : ''}>
            <label for="brand_${brand.id}" class="text-sm cursor-pointer" onclick="document.getElementById('brand_${brand.id}').click()">${brand.name}</label>
        </div>
    `).join('');
    
    document.getElementById('categoryCheckboxes').innerHTML = categoryCheckboxes;
    document.getElementById('brandCheckboxes').innerHTML = brandCheckboxes;
    
    // Set badge checkboxes based on common badges
    document.getElementById('bulkFeatured').checked = commonBadges.featured;
    document.getElementById('bulkFavorite').checked = commonBadges.favorite;
    document.getElementById('bulkBestseller').checked = commonBadges.bestseller;
}

function removeProduct(productId) {
    bulkProducts = bulkProducts.filter(p => p.id !== productId);
    const productList = document.getElementById('productList');
    productList.innerHTML = bulkProducts.map(product => `
        <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
            <span class="text-sm">${product.name}</span>
            <button type="button" onclick="removeProduct(${product.id})" class="text-red-600 hover:text-red-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `).join('');
}

function closeBulkEdit() {
    document.getElementById('bulkEditModal').classList.add('hidden');
}

function saveBulkEdit() {
    const categoryCheckboxes = document.querySelectorAll('input[name="categories[]"]:checked');
    const brandCheckboxes = document.querySelectorAll('input[name="brands[]"]:checked');
    const badgeCheckboxes = document.querySelectorAll('input[name="badges[]"]:checked');
    
    const price = document.getElementById('bulkPrice').value;
    const salePrice = document.getElementById('bulkSalePrice').value;
    
    // Validate price and sale price
    if (price && salePrice && parseFloat(salePrice) >= parseFloat(price)) {
        showNotification('Giá khuyến mãi phải nhỏ hơn giá gốc!', 'error');
        return;
    }
    
    const formData = {
        ids: bulkProducts.map(p => p.id),
        categories: Array.from(categoryCheckboxes).map(cb => cb.value),
        brands: Array.from(brandCheckboxes).map(cb => cb.value),
        badges: Array.from(badgeCheckboxes).map(cb => cb.value),
        status: document.getElementById('bulkStatus').value,
        price: price,
        sale_price: salePrice
    };
    
    // Show loading state
    const updateBtn = document.getElementById('bulkUpdateBtn');
    const updateText = document.getElementById('bulkUpdateText');
    const updateSpinner = document.getElementById('bulkUpdateSpinner');
    
    updateBtn.disabled = true;
    updateText.textContent = 'Đang cập nhật...';
    updateSpinner.classList.remove('hidden');
    
    // Always use project route
    const bulkUpdateUrl = '{{ route("project.admin.products.bulk-update", request()->route("projectCode")) }}';
    
    fetch(bulkUpdateUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(formData)
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => Promise.reject(err));
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification('Cập nhật thành công!', 'success');
            closeBulkEdit();
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification(data.message || 'Có lỗi xảy ra khi cập nhật', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (error.message) {
            showNotification(error.message, 'error');
        } else {
            showNotification('Có lỗi xảy ra khi cập nhật', 'error');
        }
    })
    .finally(() => {
        // Reset loading state
        updateBtn.disabled = false;
        updateText.textContent = 'Cập nhật tất cả';
        updateSpinner.classList.add('hidden');
    });
}



// Toggle all checkboxes
function toggleAllCheckboxes(name, checked) {
    document.querySelectorAll(`input[name="${name}"]`).forEach(cb => {
        cb.checked = checked;
    });
}

// Add real-time price validation
function validateBulkPrices() {
    const priceInput = document.getElementById('bulkPrice');
    const salePriceInput = document.getElementById('bulkSalePrice');
    
    if (priceInput && salePriceInput) {
        const price = parseFloat(priceInput.value);
        const salePrice = parseFloat(salePriceInput.value);
        
        if (price && salePrice && salePrice >= price) {
            salePriceInput.setCustomValidity('Giá khuyến mãi phải nhỏ hơn giá gốc');
            salePriceInput.style.borderColor = '#ef4444';
        } else {
            salePriceInput.setCustomValidity('');
            salePriceInput.style.borderColor = '';
        }
    }
}

// Add event listeners for real-time validation
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners when modal is opened
    document.addEventListener('input', function(e) {
        if (e.target.id === 'bulkPrice' || e.target.id === 'bulkSalePrice') {
            validateBulkPrices();
        }
    });
});

// Close modal when clicking outside
document.getElementById('bulkEditModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeBulkEdit();
    }
});

// Badge toggle functionality
function toggleBadge(productId, badgeType) {
    const button = event.target.closest('button');
    const originalClass = button.className;
    
    // Show loading state
    button.disabled = true;
    button.classList.add('opacity-50');
    
    // Always use project route
    const toggleUrl = '{{ route("project.admin.products.toggle-badge", request()->route("projectCode")) }}';
    
    fetch(toggleUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            product_id: productId,
            badge_type: badgeType
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => Promise.reject(err));
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Update button appearance based on new state
            const isActive = data.badge_active;
            
            // Remove all color classes
            button.classList.remove('bg-yellow-200', 'text-yellow-800', 'bg-red-200', 'text-red-800', 'bg-green-200', 'text-green-800', 'bg-gray-100', 'text-gray-400');
            
            // Add appropriate classes based on badge type and state
            if (badgeType === 'featured') {
                if (isActive) {
                    button.classList.add('bg-yellow-200', 'text-yellow-800');
                } else {
                    button.classList.add('bg-gray-100', 'text-gray-400');
                }
            } else if (badgeType === 'favorite') {
                if (isActive) {
                    button.classList.add('bg-red-200', 'text-red-800');
                } else {
                    button.classList.add('bg-gray-100', 'text-gray-400');
                }
            } else if (badgeType === 'bestseller') {
                if (isActive) {
                    button.classList.add('bg-green-200', 'text-green-800');
                } else {
                    button.classList.add('bg-gray-100', 'text-gray-400');
                }
            }
            
            // Update badge display in the product name area
            updateBadgeDisplay(productId, badgeType, isActive);
            
            // Show success notification
            showNotification(data.message, 'success', 2000);
        } else {
            showNotification(data.message || 'Có lỗi xảy ra khi cập nhật badge', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification(error.message || 'Có lỗi xảy ra khi cập nhật badge', 'error');
    })
    .finally(() => {
        // Reset loading state
        button.disabled = false;
        button.classList.remove('opacity-50');
    });
}

// Update badge display in product name area - No longer needed since badges are not displayed
function updateBadgeDisplay(productId, badgeType, isActive) {
    // Badge display has been removed, only toggle buttons remain
    // This function is kept for compatibility but does nothing
    console.log(`Badge ${badgeType} for product ${productId} is now ${isActive ? 'active' : 'inactive'}`);
}

// Professional notification system
function showNotification(message, type = 'info', duration = 5000) {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification-toast');
    existingNotifications.forEach(notification => notification.remove());
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'notification-toast fixed top-4 right-4 z-50 max-w-sm w-full transform transition-all duration-300 ease-in-out translate-x-full opacity-0';
    
    // Define styles based on type
    const styles = {
        success: {
            bg: 'bg-green-50',
            border: 'border-green-200',
            icon: 'text-green-400',
            text: 'text-green-800',
            iconPath: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
        },
        error: {
            bg: 'bg-red-50',
            border: 'border-red-200',
            icon: 'text-red-400',
            text: 'text-red-800',
            iconPath: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'
        },
        warning: {
            bg: 'bg-yellow-50',
            border: 'border-yellow-200',
            icon: 'text-yellow-400',
            text: 'text-yellow-800',
            iconPath: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'
        },
        info: {
            bg: 'bg-blue-50',
            border: 'border-blue-200',
            icon: 'text-blue-400',
            text: 'text-blue-800',
            iconPath: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
        }
    };
    
    const style = styles[type] || styles.info;
    
    notification.innerHTML = `
        <div class="rounded-lg shadow-lg ${style.bg} ${style.border} border p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 ${style.icon}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${style.iconPath}"/>
                    </svg>
                </div>
                <div class="ml-3 w-0 flex-1">
                    <p class="text-sm font-medium ${style.text}">
                        ${message}
                    </p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button class="inline-flex ${style.text} hover:${style.text.replace('800', '600')} focus:outline-none focus:${style.text.replace('800', '600')}" onclick="this.closest('.notification-toast').remove()">
                        <span class="sr-only">Đóng</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    // Add to document
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full', 'opacity-0');
        notification.classList.add('translate-x-0', 'opacity-100');
    }, 100);
    
    // Auto remove after duration
    if (duration > 0) {
        setTimeout(() => {
            if (notification.parentNode) {
                notification.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.remove();
                    }
                }, 300);
            }
        }, duration);
    }
}

// Add notification styles to head if not already present
if (!document.querySelector('#notification-styles')) {
    const style = document.createElement('style');
    style.id = 'notification-styles';
    style.textContent = `
        .notification-toast {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .notification-toast:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    `;
    document.head.appendChild(style);
}
</script>
@endsection
