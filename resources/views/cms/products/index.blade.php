{{-- MODIFIED: 2025-01-21 --}}
@extends('cms.layouts.app')

@section('title', 'Quản lý sản phẩm')
@section('page-title', 'Sản phẩm')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div class="flex space-x-4">
        <form method="GET" class="flex space-x-2">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Tìm kiếm sản phẩm..." 
                   class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            
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
        <button id="bulkEditBtn" onclick="openBulkEdit()" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 disabled:opacity-50" disabled>
            Sửa nhanh (<span id="selectedCount">0</span>)
        </button>
        <a href="{{ isset($currentProject) && $currentProject ? route('project.admin.products.create', $currentProject->code) : route('cms.products.create') }}" 
           class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
            Thêm sản phẩm
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600">
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sản phẩm</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Danh mục</th>
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
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $product->sku }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $product->category?->name ?? 'Chưa phân loại' }}
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
                        <a href="{{ isset($currentProject) && $currentProject ? route('project.admin.products.show', [$currentProject->code, $product->id]) : route('cms.products.show', $product->id) }}" 
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
                        <a href="{{ isset($currentProject) && $currentProject ? route('project.admin.products.edit', [$currentProject->code, $product]) : route('cms.products.edit', $product) }}" 
                           class="inline-flex items-center justify-center w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg hover:bg-indigo-200 transition-colors" 
                           title="Chỉnh sửa">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                        
                        <!-- Delete -->
                        <form method="POST" action="{{ isset($currentProject) && $currentProject ? route('project.admin.products.destroy', [$currentProject->code, $product]) : route('cms.products.destroy', $product) }}" class="inline">
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
                <td colspan="8" class="px-6 py-4 text-center text-gray-500">Không có sản phẩm nào</td>
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
                        </div>
                    </form>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-2">
                <button type="button" onclick="closeBulkEdit()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Hủy</button>
                <button type="button" onclick="saveBulkEdit()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Cập nhật tất cả</button>
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
    
    fetch('/cms/admin/products/bulk-edit', {
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
            alert('Lỗi: ' + (data.message || 'Không thể tải dữ liệu'));
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        alert('Có lỗi xảy ra khi tải dữ liệu: ' + error.message);
    });
}

function openSingleQuickEdit(productId) {
    console.log('Opening quick edit for product:', productId);
    
    fetch('/cms/admin/products/bulk-edit', {
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
            alert('Lỗi: ' + (data.message || 'Không thể tải dữ liệu sản phẩm'));
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        alert('Có lỗi xảy ra khi tải dữ liệu sản phẩm: ' + error.message);
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
    
    // Get selected category and brand from products (chỉ lấy cái đầu tiên nếu có nhiều sản phẩm)
    const selectedCategoryId = products[0]?.category?.id || null;
    const selectedBrandId = products[0]?.brand?.id || null;
    
    // Populate category checkboxes with pre-selected
    const categoryCheckboxes = `
        <div class="flex items-center mb-2 pb-2 border-b">
            <input type="checkbox" id="selectAllCategories" class="mr-2 rounded" onchange="toggleAllCheckboxes('categories[]', this.checked)">
            <label for="selectAllCategories" class="text-sm font-semibold">Chọn tất cả</label>
        </div>
    ` + categories.map(cat => `
        <div class="flex items-center mb-2">
            <input type="checkbox" id="cat_${cat.id}" name="categories[]" value="${cat.id}" class="mr-2 rounded category-checkbox" ${cat.id === selectedCategoryId ? 'checked' : ''}>
            <label for="cat_${cat.id}" class="text-sm cursor-pointer" onclick="document.getElementById('cat_${cat.id}').click()">${cat.name}</label>
        </div>
    `).join('');
    
    // Populate brand checkboxes with pre-selected
    const brandCheckboxes = `
        <div class="flex items-center mb-2 pb-2 border-b">
            <input type="checkbox" id="selectAllBrands" class="mr-2 rounded" onchange="toggleAllCheckboxes('brands[]', this.checked)">
            <label for="selectAllBrands" class="text-sm font-semibold">Chọn tất cả</label>
        </div>
    ` + brands.map(brand => `
        <div class="flex items-center mb-2">
            <input type="checkbox" id="brand_${brand.id}" name="brands[]" value="${brand.id}" class="mr-2 rounded brand-checkbox" ${brand.id === selectedBrandId ? 'checked' : ''}>
            <label for="brand_${brand.id}" class="text-sm cursor-pointer" onclick="document.getElementById('brand_${brand.id}').click()">${brand.name}</label>
        </div>
    `).join('');
    
    document.getElementById('categoryCheckboxes').innerHTML = categoryCheckboxes;
    document.getElementById('brandCheckboxes').innerHTML = brandCheckboxes;
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
    
    const formData = {
        ids: bulkProducts.map(p => p.id),
        categories: Array.from(categoryCheckboxes).map(cb => cb.value),
        brands: Array.from(brandCheckboxes).map(cb => cb.value),
        status: document.getElementById('bulkStatus').value,
        price: document.getElementById('bulkPrice').value,
        sale_price: document.getElementById('bulkSalePrice').value
    };
    
    fetch('/cms/admin/products/bulk-update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Cập nhật thành công!');
            closeBulkEdit();
            location.reload();
        } else {
            alert(data.message || 'Có lỗi xảy ra khi cập nhật');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi cập nhật');
    });
}



// Toggle all checkboxes
function toggleAllCheckboxes(name, checked) {
    document.querySelectorAll(`input[name="${name}"]`).forEach(cb => {
        cb.checked = checked;
    });
}

// Close modal when clicking outside
document.getElementById('bulkEditModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeBulkEdit();
    }
});
</script>
@endsection
