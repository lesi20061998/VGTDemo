@extends('cms.layouts.app')

@section('title', 'Quản lý Menu')
@section('page-title', 'Quản lý Menu')

@section('content')
<!-- Alert Container -->
<div id="alert-container" class="fixed top-4 right-4 z-50 space-y-2 max-w-md"></div>

<div class="grid grid-cols-12 gap-4">
    <!-- Cột trái: Danh sách menu -->
    <div class="col-span-3 bg-white rounded-lg shadow-sm p-4">
        <button onclick="openCreateMenuModal()" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 mb-4 flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Thêm menu
        </button>
        
        <div class="space-y-2">
            @foreach($menus as $menu)
            <div class="flex items-center gap-2">
                <a href="{{ url(request()->segment(1) . '/admin/menus/' . $menu->id) }}" class="flex-1 p-3 rounded-lg hover:bg-gray-50 {{ $selectedMenu && $selectedMenu->id == $menu->id ? 'bg-blue-50 border-l-4 border-blue-600' : 'border-l-4 border-transparent' }}">
                    <div class="font-medium">{{ $menu->name }}</div>
                    <div class="text-xs text-gray-500">
                        {{ $menu->allItems->count() }} mục
                    </div>
                </a>
                <button onclick="deleteMenu({{ $menu->id }})" class="p-2 text-red-600 hover:bg-red-50 rounded" title="Xóa menu">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </div>
            @endforeach
        </div>
    </div>
    
    <!-- Cột giữa: Chọn lựa -->
    <div class="col-span-4 bg-white rounded-lg shadow-sm p-4">
        <h3 class="font-semibold text-lg mb-4">Chọn lựa</h3>
        
        @if($selectedMenu)
        <div class="space-y-4">
            <!-- Trang Nội Dung -->
            <div class="border rounded-lg">
                <button onclick="toggleSection('pages')" class="w-full p-3 flex justify-between items-center hover:bg-gray-50">
                    <span class="font-medium">Trang Nội Dung</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div id="pages-section" class="p-3 border-t max-h-60 overflow-y-auto">
                    @foreach($pages ?? [] as $page)
                    <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
                        <input type="checkbox" class="mr-2" data-type="page" data-id="{{ $page->id }}" data-title="{{ $page->title }}">
                        <span class="text-sm">{{ $page->title }}</span>
                    </label>
                    @endforeach
                    <button onclick="addSelectedItems('page')" class="mt-2 w-full px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">Thêm vào menu</button>
                </div>
            </div>
            
            <!-- Danh mục sản phẩm -->
            <div class="border rounded-lg">
                <button onclick="toggleSection('product-categories')" class="w-full p-3 flex justify-between items-center hover:bg-gray-50">
                    <span class="font-medium">Danh mục sản phẩm</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div id="product-categories-section" class="hidden p-3 border-t max-h-60 overflow-y-auto">
                    @if(isset($productCategories) && $productCategories->count() > 0)
                    @foreach($productCategories as $category)
                    <div class="mb-2">
                        <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer font-medium">
                            <input type="checkbox" class="mr-2" data-type="productcategory" data-id="{{ $category->id }}" data-title="{{ $category->name }}">
                            <span class="text-sm">{{ $category->name }}</span>
                        </label>
                        @if($category->children && $category->children->count() > 0)
                        <div class="ml-6 space-y-1">
                            @foreach($category->children as $child)
                            <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
                                <input type="checkbox" class="mr-2" data-type="productcategory" data-id="{{ $child->id }}" data-title="{{ $child->name }}">
                                <span class="text-sm text-gray-600">↳ {{ $child->name }}</span>
                            </label>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @endforeach
                    <button onclick="addSelectedItems('productcategory')" class="mt-2 w-full px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">Thêm vào menu</button>
                    @else
                    <p class="text-gray-500 text-sm text-center py-4">Chưa có danh mục sản phẩm</p>
                    @endif
                </div>
            </div>
            
            <!-- Liên kết -->
            <div class="border rounded-lg">
                <button onclick="toggleSection('link')" class="w-full p-3 flex justify-between items-center hover:bg-gray-50">
                    <span class="font-medium">Liên kết</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div id="link-section" class="hidden p-3 border-t">
                    <form onsubmit="addCustomLink(event)">
                        <input type="text" id="link-title" placeholder="Tiêu đề" class="w-full px-3 py-2 border rounded mb-2" required>
                        <input type="text" id="link-slug" placeholder="Slug (vd: about, contact)" class="w-full px-3 py-2 border rounded mb-2" required>
                        <button type="submit" class="w-full px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">Thêm liên kết</button>
                    </form>
                </div>
            </div>
        </div>
        @else
        <p class="text-gray-500 text-center py-8">Chọn hoặc tạo menu để bắt đầu</p>
        @endif
    </div>
    
    <!-- Cột phải: Cấu trúc menu -->
    <div class="col-span-5 bg-white rounded-lg shadow-sm p-4">
        @if($selectedMenu)
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-semibold text-lg">{{ $selectedMenu->name }}</h3>
            <div class="flex gap-2">
                <button onclick="saveMenuOrder()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-medium">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    Lưu thay đổi
                </button>
                <button onclick="deleteMenu({{ $selectedMenu->id }})" class="px-3 py-1 text-red-600 hover:bg-red-50 rounded">Xóa menu</button>
            </div>
        </div>
        
        <div id="menu-structure" class="min-h-[400px] sortable-menu">
            @if($selectedMenu->allItems && $selectedMenu->allItems->count() > 0)
                @foreach($selectedMenu->allItems->sortBy('order') as $item)
                    @php
                        $depth = 0;
                        $current = $item;
                        while($current->parent_id) {
                            $depth++;
                            $current = $selectedMenu->allItems->where('id', $current->parent_id)->first();
                            if (!$current) break;
                        }
                    @endphp
                    @include('cms.menus.partials.menu-item', ['item' => $item, 'depth' => $depth])
                @endforeach
            @else
                <p class="text-gray-400 text-center py-8">Chưa có mục nào</p>
            @endif
        </div>
        @else
        <p class="text-gray-500 text-center py-8">Chọn menu để xem cấu trúc</p>
        @endif
    </div>
</div>

<!-- Modal tạo menu -->
<div id="createMenuModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-96">
        <h3 class="text-lg font-semibold mb-4">Tạo menu mới</h3>
        <form onsubmit="createMenu(event)">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Tên menu</label>
                <input type="text" id="menu-name" name="name" class="w-full px-3 py-2 border rounded-lg" required>
            </div>

            <div class="flex gap-2">
                <button type="button" onclick="closeCreateMenuModal()" class="flex-1 px-4 py-2 border rounded-lg hover:bg-gray-50">Hủy</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Tạo</button>
            </div>
        </form>
    </div>
</div>

<script>
// Alert System
function showAlert(message, type = 'success') {
    const container = document.getElementById('alert-container');
    const alertId = 'alert-' + Date.now();
    
    const icons = {
        success: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
        error: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
        warning: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>',
        info: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>'
    };
    
    const colors = {
        success: 'bg-green-50 border-green-200 text-green-800',
        error: 'bg-red-50 border-red-200 text-red-800',
        warning: 'bg-yellow-50 border-yellow-200 text-yellow-800',
        info: 'bg-blue-50 border-blue-200 text-blue-800'
    };
    
    const iconColors = {
        success: 'text-green-400',
        error: 'text-red-400',
        warning: 'text-yellow-400',
        info: 'text-blue-400'
    };
    
    const alert = document.createElement('div');
    alert.id = alertId;
    alert.className = `${colors[type]} border-l-4 p-4 rounded-lg shadow-lg transform transition-all duration-300 ease-out translate-x-full opacity-0`;
    alert.innerHTML = `
        <div class="flex items-start">
            <div class="flex-shrink-0 ${iconColors[type]}">
                ${icons[type]}
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium">${message}</p>
            </div>
            <button onclick="closeAlert('${alertId}')" class="ml-4 flex-shrink-0 inline-flex text-gray-400 hover:text-gray-600 focus:outline-none">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
            </button>
        </div>
    `;
    
    container.appendChild(alert);
    
    // Animate in
    setTimeout(() => {
        alert.classList.remove('translate-x-full', 'opacity-0');
    }, 10);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        closeAlert(alertId);
    }, 5000);
}

function closeAlert(alertId) {
    const alert = document.getElementById(alertId);
    if (alert) {
        alert.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => {
            alert.remove();
        }, 300);
    }
}

const isProjectRoute = window.location.pathname.match(/^\/[A-Z0-9]+\/admin/);
const routePrefix = isProjectRoute ? 'project.admin' : 'cms';

function toggleSection(section) {
    const el = document.getElementById(section + '-section');
    el.classList.toggle('hidden');
}

function openCreateMenuModal() {
    document.getElementById('createMenuModal').classList.remove('hidden');
}

function closeCreateMenuModal() {
    document.getElementById('createMenuModal').classList.add('hidden');
}

function generateSlug(name) {
    return name.toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/đ/g, 'd')
        .replace(/Đ/g, 'D')
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim();
}

function createMenu(e) {
    e.preventDefault();
    const name = document.getElementById('menu-name').value.trim();
    
    if (!name) {
        alert('Vui lòng nhập tên menu');
        return;
    }
    
    const slug = generateSlug(name);
    const submitBtn = e.target.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Đang tạo...';
    
    const payload = { name, slug };
    
    const currentPath = window.location.pathname;
    const storeUrl = currentPath.replace(/\/menus.*/, '/menus');
    
    fetch(storeUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(response => response.json().then(data => ({ status: response.status, data })))
    .then(({ status, data }) => {
        if (data.success) {
            showAlert('Menu đã được tạo thành công!', 'success');
            closeCreateMenuModal();
            document.getElementById('menu-name').value = '';
            
            // Reload trang để hiển thị menu mới
            setTimeout(() => {
                window.location.href = `${storeUrl}/${data.menu.id}`;
            }, 500);
        } else {
            showAlert(data.message || 'Không thể tạo menu', 'error');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Tạo';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Lỗi kết nối. Vui lòng thử lại.', 'error');
        submitBtn.disabled = false;
        submitBtn.textContent = 'Tạo';
    });
}

function addMenuToList(menu) {
    const menuList = document.querySelector('.space-y-2');
    const menuItem = document.createElement('div');
    menuItem.className = 'flex items-center gap-2';
    const currentPath = window.location.pathname;
    const baseUrl = currentPath.replace(/\/menus.*/, '/menus');
    menuItem.innerHTML = `
        <a href="${baseUrl}" class="flex-1 p-3 rounded-lg hover:bg-gray-50 border-l-4 border-transparent">
            <div class="font-medium">${menu.name}</div>
            <div class="text-xs text-gray-500">0 mục</div>
        </a>
        <button onclick="deleteMenu(${menu.id})" class="p-2 text-red-600 hover:bg-red-50 rounded" title="Xóa menu">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
        </button>
    `;
    menuList.appendChild(menuItem);
}

function addSelectedItems(type) {
    const checkboxes = document.querySelectorAll(`input[data-type="${type}"]:checked`);
    checkboxes.forEach(cb => {
        let modelName = type.charAt(0).toUpperCase() + type.slice(1);
        if(type === 'productcategory') {
            modelName = 'ProductCategory';
        } else if(type === 'page') {
            modelName = 'Post';
        }
        addMenuItem({
            title: cb.dataset.title,
            linkable_type: 'App\\Models\\' + modelName,
            linkable_id: cb.dataset.id,
            target: '_self'
        });
        cb.checked = false;
    });
}

function addCustomLink(e) {
    e.preventDefault();
    const title = document.getElementById('link-title').value;
    const slug = document.getElementById('link-slug').value.trim();
    
    // Tạo URL từ slug
    const url = '/' + slug.replace(/^\/+/, '');
    
    addMenuItem({
        title: title,
        url: url,
        target: '_self'
    });
    
    e.target.reset();
}

function addMenuItem(data) {
    @if($selectedMenu)
    const currentPath = window.location.pathname;
    const baseUrl = currentPath.replace(/\/menus.*/, '/menus');
    fetch(`${baseUrl}/{{ $selectedMenu->id }}/items`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showAlert('Đã thêm mục menu thành công!', 'success');
            // Thêm menu item mới vào cấu trúc menu
            addMenuItemToStructure(result.item);
            // Cập nhật số lượng menu items
            updateMenuItemCount();
            // Mark as changed
            markAsChanged();
        } else {
            showAlert(result.message || 'Không thể thêm mục menu', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Không thể thêm mục menu', 'error');
    });
    @else
    alert('Vui lòng chọn menu trước');
    @endif
}

function addMenuItemToStructure(item) {
    const menuStructure = document.getElementById('menu-structure');
    const emptyMessage = menuStructure.querySelector('p');
    
    // Xóa thông báo trống nếu có
    if (emptyMessage) {
        emptyMessage.remove();
    }
    
    // Tạo menu item mới
    const menuItemDiv = document.createElement('div');
    menuItemDiv.className = 'menu-item border rounded-lg p-3 bg-gray-50';
    menuItemDiv.setAttribute('data-id', item.id);
    
    let linkInfo = '';
    if (item.linkable_type) {
        const modelName = item.linkable_type.split('\\').pop();
        linkInfo = modelName;
    } else if (item.url) {
        linkInfo = `Link → ${item.url}`;
    }
    
    menuItemDiv.innerHTML = `
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 flex-1">
                <div class="drag-handle w-5 h-5 flex flex-col justify-center items-center text-gray-400 cursor-grab hover:text-gray-600" title="Kéo thả để sắp xếp">
                    <div class="w-1 h-1 bg-current rounded-full mb-0.5"></div>
                    <div class="w-1 h-1 bg-current rounded-full mb-0.5"></div>
                    <div class="w-1 h-1 bg-current rounded-full mb-0.5"></div>
                    <div class="w-1 h-1 bg-current rounded-full mb-0.5"></div>
                    <div class="w-1 h-1 bg-current rounded-full"></div>
                </div>
                <div class="flex-1">
                    <div class="font-medium text-sm">${item.title}</div>
                    <div class="text-xs text-gray-500">${linkInfo}</div>
                </div>
            </div>
            <div class="flex gap-1">
                <button onclick="moveUp(${item.id})" class="p-1 hover:bg-blue-100 text-blue-600 rounded" title="Di chuyển lên">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                </button>
                <button onclick="moveDown(${item.id})" class="p-1 hover:bg-blue-100 text-blue-600 rounded" title="Di chuyển xuống">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <button onclick="indentRight(${item.id})" class="p-1 hover:bg-green-100 text-green-600 rounded" title="Tạo menu con">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
                <button onclick="deleteItem(${item.id})" class="p-1 hover:bg-red-100 text-red-600 rounded" title="Xóa">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </div>
        </div>
    `;
    
    menuStructure.appendChild(menuItemDiv);
    
    // Khởi tạo lại sortable cho menu item mới
    if (window.sortableInstance) {
        window.sortableInstance.destroy();
        initSortable();
    }
}

function updateMenuItemCount() {
    const menuItems = document.querySelectorAll('#menu-structure .menu-item');
    const countElement = document.querySelector('.bg-blue-50 .text-xs');
    if (countElement) {
        countElement.textContent = `${menuItems.length} mục`;
    }
}

function deleteMenu(id) {
    if(confirm('Xóa menu này? Tất cả mục menu bên trong cũng sẽ bị xóa!')) {
        const menuElement = event.target.closest('.flex.items-center.gap-2');
        const deleteBtn = event.target.closest('button');
        deleteBtn.disabled = true;
        deleteBtn.style.opacity = '0.5';
        
        const currentPath = window.location.pathname;
        const baseUrl = currentPath.replace(/\/menus.*/, '/menus');
        fetch(`${baseUrl}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Menu đã được xóa thành công!', 'success');
                menuElement.remove();
                
                const remainingMenus = document.querySelectorAll('.space-y-2 > .flex');
                if (remainingMenus.length === 0) {
                    setTimeout(() => location.reload(), 500);
                } else if (window.location.pathname.includes(`/menus/${id}`)) {
                    const basePath = window.location.pathname.replace(/\/menus.*/, '/menus');
                    setTimeout(() => window.location.href = basePath, 500);
                }
            } else {
                showAlert(data.message || 'Không thể xóa menu', 'error');
                deleteBtn.disabled = false;
                deleteBtn.style.opacity = '1';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Không thể xóa menu', 'error');
            deleteBtn.disabled = false;
            deleteBtn.style.opacity = '1';
        });
    }
}

function deleteItem(id) {
    if(confirm('Xóa mục này?')) {
        const currentPath = window.location.pathname;
        const baseUrl = currentPath.replace(/\/menus.*/, '/menus');
        fetch(`${baseUrl}/items/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Đã xóa mục menu thành công!', 'success');
                // Xóa menu item khỏi giao diện
                const menuItem = document.querySelector(`[data-id="${id}"]`);
                if (menuItem) {
                    menuItem.remove();
                    updateMenuItemCount();
                    
                    // Hiển thị thông báo trống nếu không còn item nào
                    const remainingItems = document.querySelectorAll('#menu-structure .menu-item');
                    if (remainingItems.length === 0) {
                        const menuStructure = document.getElementById('menu-structure');
                        menuStructure.innerHTML = '<p class="text-gray-400 text-center py-8">Chưa có mục nào</p>';
                    }
                }
            } else {
                showAlert('Không thể xóa mục menu', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Không thể xóa mục menu', 'error');
        });
    }
}

function moveUp(id) {
    const item = document.querySelector(`[data-id="${id}"]`);
    const prev = item.previousElementSibling;
    if (prev && prev.classList.contains('menu-item')) {
        item.parentNode.insertBefore(item, prev);
        markAsChanged();
    }
}

function moveDown(id) {
    const item = document.querySelector(`[data-id="${id}"]`);
    const next = item.nextElementSibling;
    if (next && next.classList.contains('menu-item')) {
        item.parentNode.insertBefore(next, item);
        markAsChanged();
    }
}

function indentRight(id) {
    const item = document.querySelector(`[data-id="${id}"]`);
    const currentDepth = parseInt(item.dataset.depth || 0);
    if (currentDepth < 3) {
        const newDepth = currentDepth + 1;
        updateItemDepth(item, newDepth);
        markAsChanged();
    }
}

function indentLeft(id) {
    const item = document.querySelector(`[data-id="${id}"]`);
    const currentDepth = parseInt(item.dataset.depth || 0);
    if (currentDepth > 0) {
        const newDepth = currentDepth - 1;
        updateItemDepth(item, newDepth);
        markAsChanged();
    }
}

function updateItemDepth(item, depth) {
    // Remove old depth class
    item.classList.remove(...Array.from(item.classList).filter(cls => cls.startsWith('depth-')));
    
    // Add new depth class
    item.classList.add(`depth-${depth}`);
    item.dataset.depth = depth;
    
    // Update buttons visibility
    updateItemButtons(item, depth);
    
    // Visual feedback for depth change
    if (depth > 0) {
        item.style.animation = 'indentSuccess 0.5s ease';
        setTimeout(() => {
            item.style.animation = '';
        }, 500);
    }
}

function updateItemButtons(item, depth) {
    const buttons = item.querySelector('.flex.gap-1');
    if (!buttons) return;
    
    // Remove existing indent buttons
    const existingIndentBtns = buttons.querySelectorAll('[title="Tạo menu con"], [title="Hủy phân cấp"]');
    existingIndentBtns.forEach(btn => btn.remove());
    
    const deleteBtn = buttons.querySelector('[title="Xóa"]');
    
    // Add indent right button if depth < 3
    if (depth < 3) {
        const btn = document.createElement('button');
        btn.onclick = () => indentRight(item.dataset.id);
        btn.className = 'p-1 hover:bg-green-100 text-green-600 rounded';
        btn.title = 'Tạo menu con';
        btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>';
        buttons.insertBefore(btn, deleteBtn);
    }
    
    // Add indent left button if depth > 0
    if (depth > 0) {
        const btn = document.createElement('button');
        btn.onclick = () => indentLeft(item.dataset.id);
        btn.className = 'p-1 hover:bg-orange-100 text-orange-600 rounded';
        btn.title = 'Hủy phân cấp';
        btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>';
        buttons.insertBefore(btn, deleteBtn);
    }
}

// Drag and Drop functionality
document.addEventListener('DOMContentLoaded', function() {
    @if($selectedMenu)
    initSortable();
    @endif
});

function initSortable() {
    const menuStructure = document.getElementById('menu-structure');
    if (!menuStructure) return;
    
    window.sortableInstance = new Sortable(menuStructure, {
        group: 'menu-items',
        animation: 150,
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        dragClass: 'sortable-drag',
        swapThreshold: 0.65,
        direction: 'vertical',
        forceFallback: false,
        fallbackTolerance: 3,
        
        // Allow dropping on drop zones
        onMove: function(evt) {
            return evt.related.className.indexOf('drop-zone-submenu') !== -1 || 
                   evt.related.className.indexOf('menu-item') !== -1;
        },
        
        onStart: function(evt) {
            evt.item.classList.add('dragging');
        },
        

        
        onAdd: function(evt) {
            const item = evt.item;
            const target = evt.to;
            
            // Check if dropped on a drop zone
            if (target && target.classList && target.classList.contains('drop-zone-submenu')) {
                const parentId = target.dataset.parentId;
                const parentItem = document.querySelector(`[data-id="${parentId}"]`);
                
                if (parentItem) {
                    const parentDepth = parseInt(parentItem.dataset.depth || 0);
                    const newDepth = Math.min(parentDepth + 1, 3);
                    
                    // Apply indentRight effect
                    updateItemDepth(item, newDepth);
                    
                    // Move item to correct position (after parent)
                    const menuStructure = document.getElementById('menu-structure');
                    target.remove(); // Remove drop zone
                    
                    // Find correct insertion point
                    let insertAfter = parentItem;
                    let nextSibling = parentItem.nextElementSibling;
                    
                    // Find last child of this parent
                    while (nextSibling && nextSibling.classList.contains('menu-item')) {
                        const siblingDepth = parseInt(nextSibling.dataset.depth || 0);
                        if (siblingDepth <= parentDepth) break;
                        insertAfter = nextSibling;
                        nextSibling = nextSibling.nextElementSibling;
                    }
                    
                    insertAfter.insertAdjacentElement('afterend', item);
                }
            }
        },
        
        onEnd: function(evt) {
            if (!evt.item) return;
            
            evt.item.classList.remove('dragging');
            // Chỉ đánh dấu thay đổi thứ tự, không thay đổi cấp độ
            markAsChanged();
        },
        

    });
    

}



function markAsChanged() {
    const saveBtn = document.querySelector('button[onclick="saveMenuOrder()"]');
    if (saveBtn) {
        saveBtn.classList.add('bg-orange-600', 'hover:bg-orange-700');
        saveBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
        saveBtn.innerHTML = `
            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Có thay đổi - Bấm để lưu
        `;
    }
}

function saveMenuOrder() {
    const saveBtn = document.querySelector('button[onclick="saveMenuOrder()"]');
    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.innerHTML = `
            <svg class="w-4 h-4 inline mr-1 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Đang lưu...
        `;
    }
    
    updateMenuOrder();
}

function updateMenuOrder() {
    const menuTree = buildMenuTree();
    
    // Send update to server
    const currentPath = window.location.pathname;
    const baseUrl = currentPath.replace(/\/menus.*/, '/menus');
    fetch(`${baseUrl}/{{ $selectedMenu->id ?? 0 }}/update-tree`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ tree: menuTree })
    })
    .then(response => response.json())
    .then(data => {
        const saveBtn = document.querySelector('button[onclick="saveMenuOrder()"]');
        if (data.success) {
            showAlert('Cấu trúc menu đã được lưu thành công!', 'success');
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.classList.remove('bg-orange-600', 'hover:bg-orange-700');
                saveBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                saveBtn.innerHTML = `
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Đã lưu!
                `;
                
                setTimeout(() => {
                    saveBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                    saveBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                    saveBtn.innerHTML = `
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Lưu thay đổi
                    `;
                }, 2000);
            }
        } else {
            showAlert('Lỗi khi lưu cấu trúc menu', 'error');
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.classList.add('bg-red-600', 'hover:bg-red-700');
                saveBtn.innerHTML = `
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Lỗi - Thử lại
                `;
            }
        }
    })
    .catch(error => {
        console.error('Error updating menu order:', error);
        showAlert('Lỗi kết nối khi lưu menu', 'error');
    });
}

function createDropZones() {
    const menuItems = document.querySelectorAll('.menu-item:not(.dragging)');
    
    menuItems.forEach(item => {
        const itemDepth = parseInt(item.dataset.depth || 0);
        
        // Chỉ tạo drop zone cho items có thể làm parent (depth < 3)
        if (itemDepth < 3) {
            const dropZone = document.createElement('div');
            dropZone.className = 'drop-zone-submenu';
            dropZone.innerHTML = `<span>→ Thả vào đây để tạo menu con</span>`;
            dropZone.dataset.parentId = item.dataset.id;
            dropZone.style.marginLeft = `${(itemDepth + 1) * 30}px`;
            
            // Add drop event listener
            dropZone.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('active');
            });
            
            dropZone.addEventListener('dragleave', function(e) {
                this.classList.remove('active');
            });
            
            dropZone.addEventListener('drop', function(e) {
                e.preventDefault();
                const draggedItem = document.querySelector('.dragging');
                if (draggedItem) {
                    handleDropToSubmenu(draggedItem, this.dataset.parentId);
                }
            });
            
            // Thêm drop zone sau item
            item.insertAdjacentElement('afterend', dropZone);
            
            // Highlight item có thể làm parent
            item.classList.add('can-be-parent');
        }
    });
}

function handleDropToSubmenu(draggedItem, parentId) {
    const parentItem = document.querySelector(`[data-id="${parentId}"]`);
    if (!parentItem) return;
    
    const parentDepth = parseInt(parentItem.dataset.depth || 0);
    const newDepth = Math.min(parentDepth + 1, 3);
    
    // Apply indentRight effect exactly like the button
    updateItemDepth(draggedItem, newDepth);
    
    // Find correct position to insert
    let insertAfter = parentItem;
    let nextSibling = parentItem.nextElementSibling;
    
    // Skip drop zones and find last child
    while (nextSibling) {
        if (nextSibling.classList.contains('drop-zone-submenu')) {
            nextSibling = nextSibling.nextElementSibling;
            continue;
        }
        if (nextSibling.classList.contains('menu-item')) {
            const siblingDepth = parseInt(nextSibling.dataset.depth || 0);
            if (siblingDepth <= parentDepth) break;
            insertAfter = nextSibling;
        }
        nextSibling = nextSibling.nextElementSibling;
    }
    
    // Insert the dragged item
    insertAfter.insertAdjacentElement('afterend', draggedItem);
    
    // Mark as changed
    markAsChanged();
}

function removeDropZones() {
    document.querySelectorAll('.drop-zone-submenu').forEach(zone => zone.remove());
    document.querySelectorAll('.can-be-parent').forEach(item => {
        item.classList.remove('can-be-parent');
    });
}

function buildMenuTree() {
    const menuStructure = document.getElementById('menu-structure');
    const items = [];
    const parentStack = []; // Stack to track parents at each depth
    
    Array.from(menuStructure.children).forEach((item, index) => {
        if (item.classList.contains('menu-item')) {
            const depth = parseInt(item.dataset.depth || 0);
            const itemId = parseInt(item.dataset.id);
            
            // Adjust parent stack to current depth
            parentStack.length = depth;
            
            // Find parent_id
            let parentId = null;
            if (depth > 0 && parentStack.length > 0) {
                parentId = parentStack[depth - 1];
            }
            
            // Add current item to stack
            parentStack[depth] = itemId;
            
            items.push({
                id: itemId,
                order: index,
                depth: depth,
                parent_id: parentId
            });
        }
    });
    
    return items;
}






</script>

<style>
/* Smooth Drag Styles */
.sortable-ghost {
    opacity: 0.4;
    background: #f0f8ff;
    border: 2px dashed #0073aa;
    transform: rotate(2deg);
}

.sortable-chosen {
    background: #e1f5fe;
    border-color: #0073aa;
    box-shadow: 0 4px 12px rgba(0,115,170,0.3);
    transform: scale(1.02);
    z-index: 1000;
}

.sortable-drag {
    background: #fff;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    transform: rotate(3deg);
    opacity: 0.9;
}

.dragging {
    cursor: grabbing;
    transition: none;
}

.drag-handle {
    cursor: grab;
    transition: all 0.2s ease;
    opacity: 0.6;
}

.drag-handle:hover {
    opacity: 1;
    color: #3b82f6;
    transform: scale(1.1);
}

.drag-handle:active {
    cursor: grabbing;
}

.menu-item {
    transition: all 0.2s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    position: relative;
    overflow: hidden;
}



.sub-menu-container {
    position: relative;
    transition: all 0.3s ease;
}



/* WordPress-style menu structure */
.menu-structure {
    list-style: none;
    margin: 0;
    padding: 0;
}

.menu-item {
    position: relative;
    margin: 0;
    padding: 0;
    list-style: none;
}

/* Depth-based indentation - WordPress style */
.depth-0 { margin-left: 0; }
.depth-1 { margin-left: 30px; }
.depth-2 { margin-left: 60px; }
.depth-3 { margin-left: 90px; }
.depth-4 { margin-left: 120px; }
.depth-5 { margin-left: 150px; }

/* Tree lines - WordPress style */
.depth-1::before {
    content: '';
    position: absolute;
    left: -15px;
    top: 0;
    width: 1px;
    height: 100%;
    background: #ddd;
    z-index: 1;
}

.depth-1::after {
    content: '';
    position: absolute;
    left: -15px;
    top: 50%;
    width: 8px;
    height: 1px;
    background: #ddd;
    z-index: 1;
}

.depth-2::before {
    content: '';
    position: absolute;
    left: -45px;
    top: 0;
    width: 1px;
    height: 100%;
    background: #ddd;
    z-index: 1;
}

.depth-2::after {
    content: '';
    position: absolute;
    left: -15px;
    top: 50%;
    width: 8px;
    height: 1px;
    background: #ddd;
    z-index: 1;
}

.depth-3::before {
    content: '';
    position: absolute;
    left: -75px;
    top: 0;
    width: 1px;
    height: 100%;
    background: #ddd;
    z-index: 1;
}

.depth-3::after {
    content: '';
    position: absolute;
    left: -15px;
    top: 50%;
    width: 8px;
    height: 1px;
    background: #ddd;
    z-index: 1;
}

/* Hide vertical line for last items */
.menu-item:last-child::before {
    height: 50%;
}

/* WordPress-style drag handle */
.drag-handle {
    opacity: 0.6;
    transition: opacity 0.2s;
    pointer-events: none; /* Không cần click vào handle nữa */
}

.menu-item:hover .drag-handle {
    opacity: 1;
}

/* WordPress-style menu item */
.menu-item {
    background: #fff;
    border: 1px solid #ddd;
    margin-bottom: 1px;
    position: relative;
    min-height: 50px;
    display: flex;
    align-items: center;
    padding: 10px;
    transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: grab;
    will-change: transform;
}

.menu-item:active {
    cursor: grabbing;
}

.menu-item:not(.sortable-chosen):not(.sortable-ghost):hover {
    background: #f9f9f9;
    border-color: #999;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.menu-item:hover {
    background: #f9f9f9;
    border-color: #999;
}

.menu-item.sortable-chosen {
    background: #e1f5fe;
    border-color: #0073aa;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.menu-item.sortable-ghost {
    opacity: 0.5;
    background: #f0f0f0;
}





/* Control buttons */
.menu-item .flex.gap-1 button {
    transition: all 0.2s ease;
    opacity: 0.7;
    cursor: pointer;
    pointer-events: auto;
}

.menu-item .flex.gap-1 {
    pointer-events: auto;
}

.menu-item:hover .flex.gap-1 button {
    opacity: 1;
}

.menu-item .flex.gap-1 button:hover {
    transform: scale(1.1);
}

/* Drag feedback animations */
@keyframes indentSuccess {
    0% { transform: translateX(0); background-color: transparent; }
    50% { transform: translateX(10px); background-color: #dcfce7; }
    100% { transform: translateX(0); background-color: transparent; }
}

/* Drag visual feedback */
.menu-item.dragging {
    opacity: 0.8;
    transform: rotate(2deg);
}

/* Drop zones for submenu */
.drop-zone-submenu {
    height: 50px;
    margin: 8px 0;
    border: 2px dashed #22c55e;
    border-radius: 8px;
    background: rgba(34, 197, 94, 0.05);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 1;
    transform: scaleY(1);
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    cursor: pointer;
}

.drop-zone-submenu::before {
    content: '';
    position: absolute;
    left: -15px;
    top: 50%;
    width: 1px;
    height: 20px;
    background: #22c55e;
    transform: translateY(-50%);
}

.drop-zone-submenu::after {
    content: '';
    position: absolute;
    left: -15px;
    top: 50%;
    width: 8px;
    height: 1px;
    background: #22c55e;
    transform: translateY(-50%);
}

.drop-zone-submenu.active,
.drop-zone-submenu:hover {
    background: rgba(34, 197, 94, 0.15);
    border-color: #16a34a;
    border-style: solid;
    transform: scaleY(1.05);
    box-shadow: 0 2px 8px rgba(34, 197, 94, 0.2);
}

.drop-zone-submenu span {
    color: #16a34a;
    font-size: 13px;
    font-weight: 600;
    pointer-events: none;
    text-shadow: 0 1px 2px rgba(255,255,255,0.8);
}

/* Parent highlight */
.can-be-parent {
    position: relative;
}

.can-be-parent::after {
    content: '';
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    width: 0;
    height: 0;
    border-left: 6px solid #22c55e;
    border-top: 4px solid transparent;
    border-bottom: 4px solid transparent;
    opacity: 0.7;
}

/* Smooth show/hide drop zones */
.drop-zone-submenu {
    max-height: 0;
    overflow: hidden;
    opacity: 0;
    margin: 0;
    padding: 0;
}

.dragging ~ .drop-zone-submenu,
.menu-item:not(.dragging) + .drop-zone-submenu {
    max-height: 50px;
    opacity: 1;
    margin: 8px 0;
    padding: 0 16px;
}

/* Button tooltips */
.menu-item .flex.gap-1 button[title]:hover::after {
    content: attr(title);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background: #333;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    white-space: nowrap;
    z-index: 1000;
    margin-bottom: 4px;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .sortable-ghost,
    .sortable-chosen,
    .sortable-drag {
        transform: none;
    }
    
    .menu-item[class*="depth-"] .flex.items-center.gap-2.flex-1 {
        padding-left: calc(var(--depth-indent) * 0.5);
    }
    
    .menu-item .flex.gap-1 {
        gap: 2px;
    }
    
    .menu-item .flex.gap-1 button {
        padding: 2px;
    }
    
    .menu-item .flex.gap-1 button svg {
        width: 12px;
        height: 12px;
    }
}
</style>

@endsection
