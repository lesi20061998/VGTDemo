@extends('cms.layouts.app')

@section('title', 'Widget Builder')
@section('page-title', 'Widget Builder - Drag & Drop')

@push('head-scripts')
<script>
// Declare functions early so onclick handlers work
let draggedElement = null;
let existingWidgetsGrouped = [];
let widgets = [];
let draggedWidgetItem = null;
let currentArea = 'homepage-main';
const usedWidgets = new Set();

function switchArea(area) {
    currentArea = area;
    
    // Update tabs
    document.querySelectorAll('.area-tab').forEach(tab => {
        tab.classList.remove('bg-blue-500', 'text-white');
        tab.classList.add('bg-gray-200', 'text-gray-700');
        tab.querySelector('span:last-child')?.classList.remove('bg-white/20');
        tab.querySelector('span:last-child')?.classList.add('bg-gray-300');
    });
    
    const activeTab = document.getElementById('tab-' + area);
    if (activeTab) {
        activeTab.classList.remove('bg-gray-200', 'text-gray-700');
        activeTab.classList.add('bg-blue-500', 'text-white');
        activeTab.querySelector('span:last-child')?.classList.remove('bg-gray-300');
        activeTab.querySelector('span:last-child')?.classList.add('bg-white/20');
    }
    
    // Update zones
    document.querySelectorAll('.drop-zone-container').forEach(zone => {
        zone.classList.remove('active');
        zone.classList.add('hidden');
    });
    
    const activeZone = document.getElementById('zone-' + area);
    if (activeZone) {
        activeZone.classList.remove('hidden');
        activeZone.classList.add('active');
    }
}

function toggleCategory(category) {
    const content = document.getElementById('category-' + category);
    const arrow = document.getElementById('arrow-' + category);
    
    if (content && arrow) {
        content.classList.toggle('collapsed');
        arrow.classList.toggle('collapsed');
    }
}

function filterWidgets(query) {
    const templates = document.querySelectorAll('.widget-template');
    const categories = document.querySelectorAll('.widget-category');
    query = query.toLowerCase();
    
    templates.forEach(template => {
        const name = template.dataset.name?.toLowerCase() || '';
        const type = template.dataset.type?.toLowerCase() || '';
        const matches = name.includes(query) || type.includes(query);
        template.style.display = matches ? '' : 'none';
    });
    
    categories.forEach(category => {
        const visibleWidgets = category.querySelectorAll('.widget-template:not([style*="display: none"])');
        category.style.display = visibleWidgets.length > 0 ? '' : 'none';
        
        if (visibleWidgets.length > 0) {
            const content = category.querySelector('.category-content');
            const arrow = category.querySelector('.category-arrow');
            if (content) content.classList.remove('collapsed');
            if (arrow) arrow.classList.remove('collapsed');
        }
    });
}
</script>
@endpush

@section('content')
{{-- Media Picker Modal --}}
<x-media-picker-modal />

<div class="flex gap-4 h-[calc(100vh-180px)]">
    <!-- Widget Templates Sidebar - Fixed Left -->
    <div class="w-80 flex-shrink-0 bg-white rounded-lg shadow-sm flex flex-col overflow-hidden">
        <div class="p-4 border-b bg-gray-50">
            <h3 class="font-bold text-lg flex items-center justify-between">
                <span>Widget Templates</span>
                <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full">{{ array_sum(array_map('count', $availableWidgets)) }}</span>
            </h3>
            <input type="text" id="widgetSearch" placeholder="Tìm widget..." 
                   class="mt-3 w-full px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-blue-500"
                   onkeyup="filterWidgets(this.value)">
        </div>
        
        <div class="flex-1 overflow-y-auto p-3 space-y-3" id="widgetTemplatesList">
            @foreach($availableWidgets as $category => $widgets)
            <div class="widget-category" data-category="{{ $category }}">
                <button type="button" onclick="toggleCategory('{{ $category }}')" 
                        class="w-full flex items-center justify-between p-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                    <span class="font-medium text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 transition-transform category-arrow" id="arrow-{{ $category }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                        {{ ucfirst(str_replace('_', ' ', $category)) }}
                    </span>
                    <span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full">{{ count($widgets) }}</span>
                </button>
                
                <div class="category-content mt-2 space-y-2" id="category-{{ $category }}">
                    @foreach($widgets as $widget)
                    <div class="widget-template border border-gray-200 rounded-lg p-3 cursor-move hover:border-blue-400 hover:bg-blue-50 hover:shadow-sm transition-all" 
                         draggable="true" 
                         data-type="{{ $widget['type'] }}"
                         data-name="{{ $widget['metadata']['name'] ?? $widget['name'] }}"
                         data-category="{{ $category }}"
                         title="{{ $widget['metadata']['description'] ?? $widget['description'] ?? 'No description' }}">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    {!! $widget['icon'] ?? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>' !!}
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-sm truncate">{{ $widget['metadata']['name'] ?? $widget['name'] }}</h4>
                                <p class="text-xs text-gray-500 truncate">{{ $widget['type'] }}</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                            </svg>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Drop Zones - Right Side with Tabs -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Header with Save Button -->
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <!-- Area Tabs -->
                <button onclick="switchArea('homepage-main')" id="tab-homepage-main" 
                        class="area-tab px-4 py-2 rounded-lg font-medium transition-all bg-blue-500 text-white">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Homepage
                        <span class="bg-white/20 text-xs px-1.5 py-0.5 rounded" id="count-homepage-main">0</span>
                    </span>
                </button>
                <button onclick="switchArea('sidebar')" id="tab-sidebar" 
                        class="area-tab px-4 py-2 rounded-lg font-medium transition-all bg-gray-200 text-gray-700 ">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        Sidebar
                        <span class="bg-gray-300 text-xs px-1.5 py-0.5 rounded" id="count-sidebar">0</span>
                    </span>
                </button>
                <button onclick="switchArea('footer')" id="tab-footer" 
                        class="area-tab px-4 py-2 rounded-lg font-medium transition-all bg-gray-200 text-gray-700 ">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        Footer
                        <span class="bg-gray-300 text-xs px-1.5 py-0.5 rounded" id="count-footer">0</span>
                    </span>
                </button>
            </div>
            
            <div class="flex items-center gap-2">
                <button onclick="clearCurrentArea()" class="px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg transition flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Xóa tất cả
                </button>
                <button onclick="saveWidgets(event)" class="px-4 py-2 bg-[#98191F] text-white rounded-lg hover:bg-[#7a1419] transition flex items-center gap-2 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    Lưu tất cả
                </button>
            </div>
        </div>

        <!-- Drop Zone Container -->
        <div class="flex-1 bg-white rounded-lg shadow-sm overflow-hidden flex flex-col min-h-0">
            <!-- Homepage Main Zone -->
            <div id="zone-homepage-main" class="drop-zone-container flex-1 flex flex-col min-h-0" data-area="homepage-main">
                <div class="p-4 border-b bg-gray-50 flex items-center justify-between flex-shrink-0">
                    <h3 class="font-semibold text-gray-700">Homepage Main Content</h3>
                    <span class="text-sm text-gray-500">Kéo widget vào đây</span>
                </div>
                <div class="flex-1 overflow-y-auto p-4 min-h-0">
                    <div id="dropZone" class="min-h-[200px] border-2 border-dashed border-gray-200 rounded-lg transition-colors p-4" data-area="homepage-main">
                        <p class="empty-message text-gray-400 text-center py-16">
                            <svg class="w-16 h-16 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Kéo widget từ bên trái vào đây<br>
                            <small class="text-xs">Có thể sắp xếp lại bằng cách kéo thả</small>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Sidebar Zone (Hidden by default) -->
            <div id="zone-sidebar" class="drop-zone-container flex-1 flex-col hidden min-h-0" data-area="sidebar">
                <div class="p-4 border-b bg-gray-50 flex items-center justify-between flex-shrink-0">
                    <h3 class="font-semibold text-gray-700">Sidebar Widgets</h3>
                    <span class="text-sm text-gray-500">Kéo widget vào đây</span>
                </div>
                <div class="flex-1 overflow-y-auto p-4 min-h-0">
                    <div id="sidebarZone" class="min-h-[200px] border-2 border-dashed border-gray-200 rounded-lg transition-colors p-4" data-area="sidebar">
                        <p class="empty-message text-gray-400 text-center py-16">
                            <svg class="w-16 h-16 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Kéo widget cho Sidebar vào đây
                        </p>
                    </div>
                </div>
            </div>

            <!-- Footer Zone (Hidden by default) -->
            <div id="zone-footer" class="drop-zone-container flex-1 flex-col hidden min-h-0" data-area="footer">
                <div class="p-4 border-b bg-gray-50 flex items-center justify-between flex-shrink-0">
                    <h3 class="font-semibold text-gray-700">Footer Widgets</h3>
                    <span class="text-sm text-gray-500">Kéo widget vào đây</span>
                </div>
                <div class="flex-1 overflow-y-auto p-4 min-h-0">
                    <div id="footerZone" class="min-h-[200px] border-2 border-dashed border-gray-200 rounded-lg transition-colors p-4" data-area="footer">
                        <p class="empty-message text-gray-400 text-center py-16">
                            <svg class="w-16 h-16 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Kéo widget cho Footer vào đây
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Config Modal -->
<div id="configModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Configure Widget</h3>
            <button onclick="closeConfig()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="configForm"></form>
        <div class="flex justify-end gap-3 mt-6">
            <button type="button" onclick="closeConfig()" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Cancel</button>
            <button type="button" onclick="previewWidget()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">Preview</button>
            <button type="button" onclick="saveConfig()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save</button>
        </div>
    </div>
</div>

<style>
.widget-dragging { 
    opacity: 0.5; 
    transform: scale(0.95); 
}

.widget-template {
    transition: all 0.2s ease;
}

.widget-template:hover {
    transform: translateX(4px);
}

.widget-item {
    transition: all 0.2s ease;
}

.widget-item:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.widget-item.opacity-50 {
    opacity: 0.5;
    transform: rotate(2deg);
}

.drag-handle {
    transition: all 0.2s ease;
}

.drag-handle:hover {
    transform: scale(1.1);
}

.drop-zone-active {
    background-color: #dbeafe !important;
    border-color: #3b82f6 !important;
    border-style: solid !important;
}

.widget-item[draggable="true"] {
    cursor: grab;
}

.widget-item[draggable="true"]:active {
    cursor: grabbing;
}

.category-arrow {
    transition: transform 0.2s ease;
}

.category-arrow.collapsed {
    transform: rotate(-90deg);
}

.category-content.collapsed {
    display: none;
}

.area-tab.active {
    background-color: #3b82f6;
    color: white;
}

.drop-zone-container {
    display: none;
}

.drop-zone-container.active {
    display: flex;
}
</style>

<script>
// Initialize data from server
existingWidgetsGrouped = @json($existingWidgets ?? []);

// Flatten grouped widgets
Object.values(existingWidgetsGrouped).forEach(group => {
    widgets = widgets.concat(group);
});

// Clear current area widgets
function clearCurrentArea() {
    if (!confirm('Xóa tất cả widget trong khu vực này?')) return;
    
    widgets = widgets.filter(w => w.area !== currentArea);
    renderWidgets();
    updateWidgetCounts();
}

// Update widget counts in tabs
function updateWidgetCounts() {
    const counts = {
        'homepage-main': widgets.filter(w => w.area === 'homepage-main').length,
        'sidebar': widgets.filter(w => w.area === 'sidebar').length,
        'footer': widgets.filter(w => w.area === 'footer').length
    };
    
    Object.entries(counts).forEach(([area, count]) => {
        const countEl = document.getElementById('count-' + area);
        if (countEl) countEl.textContent = count;
    });
}

document.querySelectorAll('.widget-template').forEach(template => {
    template.addEventListener('dragstart', (e) => {
        draggedElement = e.target.closest('.widget-template');
        e.target.classList.add('widget-dragging');
    });
    
    template.addEventListener('dragend', (e) => {
        e.target.classList.remove('widget-dragging');
    });
});

const dropZone = document.getElementById('dropZone');
const sidebarZone = document.getElementById('sidebarZone');
const footerZone = document.getElementById('footerZone');

[dropZone, sidebarZone, footerZone].forEach(zone => {
    if (!zone) return;
    
    zone.addEventListener('dragover', (e) => {
        e.preventDefault();
        zone.classList.add('bg-blue-50', 'border-blue-500');
    });

    zone.addEventListener('dragleave', () => {
        zone.classList.remove('bg-blue-50', 'border-blue-500');
    });

    zone.addEventListener('drop', (e) => {
        e.preventDefault();
        zone.classList.remove('bg-blue-50', 'border-blue-500');
        
        if (draggedElement) {
            const type = draggedElement.dataset.type;
            const area = zone.dataset.area;
            addWidget(type, area);
            draggedElement = null;
            updateWidgetCounts();
        }
    });
});

function addWidget(type, area = 'homepage-main') {
    // Switch to the target area tab
    switchArea(area);
    
    const widget = {
        type: type,
        name: type.charAt(0).toUpperCase() + type.slice(1) + ' Widget',
        area: area,
        sort_order: widgets.filter(w => w.area === area).length,
        is_active: true,
        settings: getDefaultSettings(type)
    };
    
    widgets.push(widget);
    renderWidgets();
}

function getDefaultSettings(type) {
    const defaults = {
        hero: {
            title: 'Chào mừng đến với Doanh nghiệp của chúng tôi',
            subtitle: 'Giải pháp công nghệ hàng đầu cho doanh nghiệp hiện đại',
            button_text: 'Khám phá ngay',
            button_link: '/products'
        },
        features: {
            title: 'Tại sao chọn chúng tôi',
            features: [
                {icon: 'rocket', title: 'Tốc độ nhanh', desc: 'Hiệu suất vượt trội với công nghệ tiên tiến'},
                {icon: 'shield', title: 'Bảo mật cao', desc: 'Hệ thống bảo mật đa lớp đảm bảo an toàn'},
                {icon: 'star', title: 'Hỗ trợ 24/7', desc: 'Đội ngũ chuyên gia sẵn sàng hỗ trợ mọi lúc'}
            ]
        },
        cta: {
            title: 'Sẵn sàng bắt đầu?',
            subtitle: 'Hơn 1000+ khách hàng đã tin tưởng sử dụng dịch vụ',
            button_text: 'Liên hệ ngay',
            button_link: '/contact'
        },
        post_list: {
            title: 'Tin tức & Cập nhật',
            limit: 6,
            layout: 'grid'
        },
        post_slider: {
            title: 'Câu chuyện thành công',
            limit: 5
        },
        newsletter: {
            title: 'Đăng ký nhận tin',
            subtitle: 'Nhận thông tin mới nhất về sản phẩm và dịch vụ',
            placeholder: 'Nhập email của bạn',
            button_text: 'Đăng ký'
        },
        testimonial: {
            title: 'Khách hàng nói gì về chúng tôi',
            testimonials: [
                {name: 'Nguyễn Văn A', role: 'Giám đốc Công ty ABC', content: 'Dịch vụ tuyệt vời, đội ngũ chuyên nghiệp. Chúng tôi rất hài lòng!'},
                {name: 'Trần Thị B', role: 'Trưởng phòng Marketing', content: 'Giải pháp hiệu quả, tiết kiệm thời gian và chi phí đáng kể.'},
                {name: 'Lê Minh C', role: 'CTO Startup XYZ', content: 'Công nghệ tiên tiến, hỗ trợ tận tình. Đáng đầu tư!'}
            ]
        },
        analytics: {
            title: 'Thống kê truy cập',
            show_title: true,
            style: 'default',
            columns: '2'
        }
    };
    return defaults[type] || {};
}

function renderWidgets() {
    const homepageWidgets = widgets.filter(w => w.area === 'homepage-main');
    const sidebarWidgets = widgets.filter(w => w.area === 'sidebar');
    const footerWidgets = widgets.filter(w => w.area === 'footer');
    
    const emptyHtml = (text) => `
        <p class="empty-message text-gray-400 text-center py-16">
            <svg class="w-16 h-16 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            ${text}
        </p>
    `;
    
    // Render homepage widgets with sortable functionality
    if (homepageWidgets.length === 0) {
        dropZone.innerHTML = emptyHtml('Kéo widget từ bên trái vào đây<br><small class="text-xs">Có thể sắp xếp lại bằng cách kéo thả</small>');
    } else {
        dropZone.innerHTML = homepageWidgets.map((widget) => {
            const globalIndex = widgets.indexOf(widget);
            return renderWidgetItem(widget, globalIndex, true);
        }).join('');
        makeSortable(dropZone, 'homepage-main');
    }
    
    // Render sidebar widgets with sortable functionality
    if (sidebarWidgets.length === 0) {
        sidebarZone.innerHTML = emptyHtml('Kéo widget cho Sidebar vào đây');
    } else {
        sidebarZone.innerHTML = sidebarWidgets.map((widget) => {
            const globalIndex = widgets.indexOf(widget);
            return renderWidgetItem(widget, globalIndex, true);
        }).join('');
        makeSortable(sidebarZone, 'sidebar');
    }
  
    // Render footer widgets with sortable functionality
    if (footerWidgets.length === 0) {
        footerZone.innerHTML = emptyHtml('Kéo widget cho Footer vào đây');
    } else {
        footerZone.innerHTML = footerWidgets.map((widget) => {
            const globalIndex = widgets.indexOf(widget);
            return renderWidgetItem(widget, globalIndex, true);
        }).join('');
        makeSortable(footerZone, 'footer');
    }
    
    // Update counts
    updateWidgetCounts();
}

function renderWidgetItem(widget, globalIndex, sortable = false) {
    const dragHandle = sortable ? `
        <div class="drag-handle cursor-move text-gray-400 hover:text-gray-600 mr-2" title="Kéo để sắp xếp">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
            </svg>
        </div>
    ` : '';
    
    return `
        <div class="widget-item mb-3 border border-gray-200 rounded-lg p-4 bg-white hover:shadow-md transition-shadow" data-widget-index="${globalIndex}" ${sortable ? 'draggable="true"' : ''}>
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-2">
                    ${dragHandle}
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </div>
                    <div>
                        <span class="font-medium text-gray-800">${widget.name}</span>
                        <p class="text-xs text-gray-500">${widget.type}</p>
                    </div>
                </div>
                <div class="flex items-center gap-1">
                    <button onclick="editWidget(${globalIndex})" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Chỉnh sửa">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    <button onclick="removeWidget(${globalIndex})" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Xóa">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    `;
}

// Add sortable functionality to widget areas
function makeSortable(container, area) {
    const widgetItems = container.querySelectorAll('.widget-item');
    
    widgetItems.forEach(item => {
        item.addEventListener('dragstart', (e) => {
            e.dataTransfer.setData('text/plain', '');
            item.classList.add('opacity-50');
            draggedWidgetItem = item;
        });
        
        item.addEventListener('dragend', (e) => {
            item.classList.remove('opacity-50');
            draggedWidgetItem = null;
        });
        
        item.addEventListener('dragover', (e) => {
            e.preventDefault();
            const afterElement = getDragAfterElement(container, e.clientY);
            if (afterElement == null) {
                container.appendChild(draggedWidgetItem);
            } else {
                container.insertBefore(draggedWidgetItem, afterElement);
            }
        });
    });
    
    // Update sort order when drag ends
    container.addEventListener('drop', (e) => {
        e.preventDefault();
        updateSortOrder(area);
    });
}

function getDragAfterElement(container, y) {
    const draggableElements = [...container.querySelectorAll('.widget-item:not(.opacity-50)')];
    
    return draggableElements.reduce((closest, child) => {
        const box = child.getBoundingClientRect();
        const offset = y - box.top - box.height / 2;
        
        if (offset < 0 && offset > closest.offset) {
            return { offset: offset, element: child };
        } else {
            return closest;
        }
    }, { offset: Number.NEGATIVE_INFINITY }).element;
}

function updateSortOrder(area) {
    const areaWidgets = widgets.filter(w => w.area === area);
    const container = area === 'homepage-main' ? dropZone : 
                     area === 'sidebar' ? sidebarZone : footerZone;
    
    const widgetItems = container.querySelectorAll('.widget-item');
    
    widgetItems.forEach((item, index) => {
        const widgetIndex = parseInt(item.dataset.widgetIndex);
        const widget = widgets[widgetIndex];
        if (widget) {
            widget.sort_order = index;
        }
    });
    
    // Re-render to reflect new order
    renderWidgets();
}

function removeWidget(index) {
    if (!confirm('Xóa widget này?')) return;
    widgets.splice(index, 1);
    renderWidgets();
}

let currentEditIndex = null;

function editWidget(index) {
    currentEditIndex = index;
    const widget = widgets[index];
    const modal = document.getElementById('configModal');
    const form = document.getElementById('configForm');
    
    // Show loading state
    form.innerHTML = '<div class="text-center py-4">Loading widget configuration...</div>';
    modal.classList.remove('hidden');
    
    renderConfigForm(widget);
}

function previewWidget() {
    if (currentEditIndex === null) return;
    
    const widget = widgets[currentEditIndex];
    const form = document.getElementById('configForm');
    const formData = new FormData(form);
    
    // Collect current form data
    const settings = {};
    for (let [key, value] of formData.entries()) {
        settings[key] = value;
    }
    
    // Get variant
    const variantSelect = document.getElementById('cfg_variant');
    const variant = variantSelect ? variantSelect.value : 'default';
    
    // Get CSRF token from meta tag (more reliable than inline token)
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
    
    // Request preview from server
    fetch('{{ route("cms.widgets.preview") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            type: widget.type,
            settings: settings,
            variant: variant
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showPreviewModal(data.preview);
        } else {
            alert('Preview failed: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Preview error:', error);
        alert('Preview failed: Network error');
    });
}

function showPreviewModal(previewHtml) {
    // Create preview modal
    const previewModal = document.createElement('div');
    previewModal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    previewModal.innerHTML = `
        <div class="bg-white rounded-lg p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Widget Preview</h3>
                <button onclick="closePreviewModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="border rounded-lg p-4 bg-gray-50">
                ${previewHtml}
            </div>
        </div>
    `;
    
    document.body.appendChild(previewModal);
    window.currentPreviewModal = previewModal;
}

function closePreviewModal() {
    if (window.currentPreviewModal) {
        document.body.removeChild(window.currentPreviewModal);
        window.currentPreviewModal = null;
    }
}

function renderConfigForm(widget) {
    const widgetType = widget.type;
    const form = document.getElementById('configForm');
    const currentSettings = widget.settings || {};
    
    // Use POST to send settings (avoid URL length limits and encoding issues)
    const fieldsUrl = '{{ isset($currentProject) ? route("project.admin.widgets.fields", $currentProject->code) : route("cms.widgets.fields") }}';
    
    console.log('Loading fields from:', fieldsUrl, 'for type:', widgetType);
    
    fetch(fieldsUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            type: widgetType,
            settings: currentSettings
        })
    })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Fields response:', data);
            
            if (data.success) {
                let formHtml = data.form_html;
                
                // Populate form with HTML from server (already has values)
                form.innerHTML = formHtml;
                
                // Initialize Alpine.js on dynamically added content
                if (window.Alpine) {
                    Alpine.initTree(form);
                }
                
                // Set values for standard form fields (backup)
                const fields = data.fields || [];
                fields.forEach(field => {
                    const fieldName = field.name;
                    const fieldValue = currentSettings[fieldName];
                    const fieldElement = form.querySelector(`[name="${fieldName}"]`);
                    
                    if (fieldElement && fieldValue !== undefined) {
                        if (fieldElement.type === 'checkbox') {
                            fieldElement.checked = fieldValue;
                        } else if (fieldElement.tagName === 'SELECT') {
                            fieldElement.value = fieldValue;
                        } else if (typeof fieldValue === 'string') {
                            fieldElement.value = fieldValue;
                        }
                    }
                });
                
                // Add variant selector if available
                if (data.variants && Object.keys(data.variants).length > 1) {
                    const variantHtml = `
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Widget Variant</label>
                            <select id="cfg_variant" name="variant" class="w-full px-3 py-2 border rounded-lg">
                                ${Object.entries(data.variants).map(([key, label]) => 
                                    `<option value="${key}" ${widget.variant === key ? 'selected' : ''}>${label}</option>`
                                ).join('')}
                            </select>
                        </div>
                    `;
                    form.insertAdjacentHTML('afterbegin', variantHtml);
                }
            } else {
                console.error('Fields API error:', data.message);
                // Fallback to legacy form rendering
                renderLegacyConfigForm(widget);
            }
        })
        .catch(error => {
            console.error('Error loading widget fields:', error);
            renderLegacyConfigForm(widget);
        });
}

function renderLegacyConfigForm(widget) {
    const form = document.getElementById('configForm');
    let html = '';
    
    if (widget.type === 'hero') {
        html = `
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Title</label>
                    <input type="text" name="title" value="${widget.settings.title || ''}" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Subtitle</label>
                    <input type="text" name="subtitle" value="${widget.settings.subtitle || ''}" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Button Text</label>
                    <input type="text" name="button_text" value="${widget.settings.button_text || ''}" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Button Link</label>
                    <input type="text" name="button_link" value="${widget.settings.button_link || ''}" class="w-full px-3 py-2 border rounded-lg">
                </div>
            </div>
        `;
    } else if (widget.type === 'features') {
        html = `
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Title</label>
                    <input type="text" name="title" value="${widget.settings.title || ''}" class="w-full px-3 py-2 border rounded-lg">
                </div>
                ${(widget.settings.features || []).map((f, i) => `
                    <div class="border-t pt-3">
                        <h4 class="font-medium mb-2">Feature ${i + 1}</h4>
                        <input type="text" name="features[${i}][title]" value="${f.title || ''}" placeholder="Title" class="w-full px-3 py-2 border rounded-lg mb-2">
                        <input type="text" name="features[${i}][desc]" value="${f.desc || ''}" placeholder="Description" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                `).join('')}
            </div>
        `;
    } else if (widget.type === 'cta') {
        html = `
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Title</label>
                    <input type="text" name="title" value="${widget.settings.title || ''}" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Subtitle</label>
                    <input type="text" name="subtitle" value="${widget.settings.subtitle || ''}" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Button Text</label>
                    <input type="text" name="button_text" value="${widget.settings.button_text || ''}" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Button Link</label>
                    <input type="text" name="button_link" value="${widget.settings.button_link || ''}" class="w-full px-3 py-2 border rounded-lg">
                </div>
            </div>
        `;
    } else if (widget.type === 'analytics') {
        html = `
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Title</label>
                    <input type="text" name="title" value="${widget.settings.title || 'Thống kê truy cập'}" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Show Title</label>
                    <input type="checkbox" name="show_title" ${widget.settings.show_title !== false ? 'checked' : ''} class="rounded">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Style</label>
                    <select name="style" class="w-full px-3 py-2 border rounded-lg">
                        <option value="default" ${widget.settings.style === 'default' ? 'selected' : ''}>Mặc định</option>
                        <option value="cards" ${widget.settings.style === 'cards' ? 'selected' : ''}>Thẻ card</option>
                        <option value="compact" ${widget.settings.style === 'compact' ? 'selected' : ''}>Gọn gàng</option>
                        <option value="modern" ${widget.settings.style === 'modern' ? 'selected' : ''}>Hiện đại</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Columns</label>
                    <select name="columns" class="w-full px-3 py-2 border rounded-lg">
                        <option value="1" ${widget.settings.columns === '1' ? 'selected' : ''}>1 cột</option>
                        <option value="2" ${widget.settings.columns === '2' ? 'selected' : ''}>2 cột</option>
                        <option value="3" ${widget.settings.columns === '3' ? 'selected' : ''}>3 cột</option>
                        <option value="4" ${widget.settings.columns === '4' ? 'selected' : ''}>4 cột</option>
                    </select>
                </div>
            </div>
        `;
    } else {
        html = `<div class="text-gray-500 text-center py-4">No configuration available for this widget type.</div>`;
    }
    
    form.innerHTML = html;
}

function saveConfig() {
    const widget = widgets[currentEditIndex];
    const form = document.getElementById('configForm');
    const formData = new FormData(form);
    
    // Reset settings to avoid stale data
    const newSettings = {};
    
    // First, collect all array field names to initialize them
    const arrayFields = new Set();
    for (let [key, value] of formData.entries()) {
        if (key.includes('[')) {
            const fieldName = key.match(/^([^\[]+)/)[1];
            arrayFields.add(fieldName);
        }
    }
    
    // Initialize array fields
    arrayFields.forEach(fieldName => {
        newSettings[fieldName] = [];
    });
    
    // Process form data
    for (let [key, value] of formData.entries()) {
        if (key === 'variant') continue; // Skip variant, handled separately
        
        if (key.includes('[')) {
            // Handle array fields
            const simpleArrayMatch = key.match(/^([^\[]+)\[(\d+)\]$/);
            const nestedArrayMatch = key.match(/^([^\[]+)\[(\d+)\]\[([^\]]+)\]$/);
            
            if (nestedArrayMatch) {
                const fieldName = nestedArrayMatch[1];
                const index = parseInt(nestedArrayMatch[2]);
                const subField = nestedArrayMatch[3];
                
                if (!newSettings[fieldName]) newSettings[fieldName] = [];
                if (!newSettings[fieldName][index]) newSettings[fieldName][index] = {};
                newSettings[fieldName][index][subField] = value;
            } else if (simpleArrayMatch) {
                const fieldName = simpleArrayMatch[1];
                const index = parseInt(simpleArrayMatch[2]);
                
                if (!newSettings[fieldName]) newSettings[fieldName] = [];
                newSettings[fieldName][index] = value;
            }
        } else {
            // Simple field
            newSettings[key] = value;
        }
    }
    
    // Clean up arrays
    for (let key in newSettings) {
        if (Array.isArray(newSettings[key])) {
            newSettings[key] = newSettings[key].filter(item => item !== undefined && item !== null);
        }
    }
    
    // Also get data from Alpine.js components
    const alpineComponents = form.querySelectorAll('[x-data]');
    alpineComponents.forEach(el => {
        if (el._x_dataStack && el._x_dataStack[0]) {
            const data = el._x_dataStack[0];
            
            if (typeof data.imageUrl !== 'undefined' && data.fieldName) {
                newSettings[data.fieldName] = data.imageUrl;
            }
            if (typeof data.images !== 'undefined' && data.fieldName) {
                newSettings[data.fieldName] = data.images;
            }
        }
    });
    
    // Get all input values directly (backup for fields not in FormData)
    const allInputs = form.querySelectorAll('input[name], select[name], textarea[name]');
    allInputs.forEach(input => {
        const name = input.name;
        if (!name || name === 'variant' || name.includes('[')) return;
        
        if (input.type === 'checkbox') {
            newSettings[name] = input.checked;
        } else if (input.type === 'radio') {
            if (input.checked) {
                newSettings[name] = input.value;
            }
        } else if (!newSettings.hasOwnProperty(name) || newSettings[name] === '') {
            // Only set if not already set or empty
            newSettings[name] = input.value;
        }
    });
    
    // Merge with existing settings
    widget.settings = { ...widget.settings, ...newSettings };
    
    // Handle checkboxes (they don't appear in FormData if unchecked)
    const checkboxes = form.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        const name = checkbox.name;
        if (name && !name.includes('[')) {
            widget.settings[name] = checkbox.checked;
        }
    });
    
    // Update variant if available
    const variantSelect = document.getElementById('cfg_variant');
    if (variantSelect) {
        widget.variant = variantSelect.value;
    }
    
    console.log('=== SAVE CONFIG DEBUG ===');
    console.log('Widget type:', widget.type);
    console.log('Final widget.settings:', JSON.stringify(widget.settings, null, 2));
    console.log('=========================');
    
    closeConfig();
    renderWidgets();
    
    // Mark as unsaved - change save button appearance
    markUnsavedChanges();
}

function markUnsavedChanges() {
    const saveBtn = document.querySelector('button[onclick*="saveWidgets"]');
    if (saveBtn && !saveBtn.classList.contains('ring-2')) {
        saveBtn.classList.add('ring-2', 'ring-yellow-400', 'ring-offset-2');
        saveBtn.title = 'Có thay đổi chưa lưu - Click để lưu';
    }
}

function closeConfig() {
    document.getElementById('configModal').classList.add('hidden');
    currentEditIndex = null;
}

// Load existing widgets on page load
window.addEventListener('DOMContentLoaded', () => {
    // Initialize first tab as active
    switchArea('homepage-main');
    
    // Render widgets if any exist
    if (widgets.length > 0) {
        renderWidgets();
    }
    
    // Update counts
    updateWidgetCounts();
});

let isSaving = false;

async function saveWidgets(e) {
    if (isSaving) return;
    isSaving = true;
    
    const btn = e?.target || document.querySelector('button[onclick*="saveWidgets"]');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Đang lưu...';
    }
    
    console.log('=== SAVE WIDGETS DEBUG ===');
    console.log('Widgets to save:', JSON.stringify(widgets, null, 2));
    
    const baseUrl = '{{ isset($currentProject) ? route("project.admin.widgets.save-all", $currentProject->code) : route("cms.widgets.save-all") }}';
    
    try {
        const response = await fetch(baseUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ widgets: widgets })
        });
        
        if (!response.ok) {
            const errorText = await response.text();
            let errorMessage = 'HTTP ' + response.status;
            try {
                const errorJson = JSON.parse(errorText);
                errorMessage += ': ' + (errorJson.message || errorText.substring(0, 500));
            } catch (parseErr) {
                errorMessage += ': ' + errorText.substring(0, 500);
            }
            throw new Error(errorMessage);
        }
        
        const result = await response.json();
        
        if (result.success) {
            if (btn) {
                btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Đã lưu!';
                btn.classList.remove('bg-[#98191F]', 'ring-2', 'ring-yellow-400', 'ring-offset-2');
                btn.classList.add('bg-green-600');
            }
            showAlert(result.message || 'Lưu thành công!', 'success');
        } else {
            if (btn) {
                btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg> Lỗi!';
                btn.classList.remove('bg-[#98191F]');
                btn.classList.add('bg-red-600');
            }
            showAlert(result.message || 'Lưu thất bại', 'error');
            if (result.errors) {
                alert('Lỗi:\n' + result.errors.join('\n'));
            }
        }
    } catch (error) {
        console.error('Error saving widgets:', error);
        alert('Lỗi: ' + error.message);
        if (btn) {
            btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg> Lỗi!';
            btn.classList.remove('bg-[#98191F]');
            btn.classList.add('bg-red-600');
        }
    }
    
    setTimeout(() => {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg> Lưu tất cả';
            btn.classList.remove('bg-green-600', 'bg-red-600', 'ring-2', 'ring-yellow-400', 'ring-offset-2');
            btn.classList.add('bg-[#98191F]');
        }
        isSaving = false;
    }, 2000);
}

// Simple alert function
function showAlert(message, type = 'info') {
    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' :
        type === 'error' ? 'bg-red-100 text-red-800 border border-red-200' :
        'bg-blue-100 text-blue-800 border border-blue-200'
    }`;
    alertDiv.textContent = message;
    
    document.body.appendChild(alertDiv);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.parentNode.removeChild(alertDiv);
        }
    }, 3000);
}

// Global media selection handler for widget config forms
window.addEventListener('media-selected', function(e) {
    const { field, urls } = e.detail;
    if (!field) return;
    
    // Find the input field in the config form
    const form = document.getElementById('configForm');
    if (!form) return;
    
    const url = Array.isArray(urls) ? urls[0] : urls;
    
    // Try to find input by name
    let input = form.querySelector(`[name="${field}"]`);
    
    if (input) {
        input.value = url;
        // Trigger input event for Alpine.js reactivity
        input.dispatchEvent(new Event('input', { bubbles: true }));
    }
    
    // Also update Alpine data if available
    const alpineEl = form.querySelector('[x-data]');
    if (alpineEl && alpineEl._x_dataStack) {
        const data = alpineEl._x_dataStack[0];
        // Update the specific field in Alpine data
        if (data && field) {
            // Handle nested field names like 'settings.video_url'
            const fieldParts = field.split('.');
            let target = data;
            for (let i = 0; i < fieldParts.length - 1; i++) {
                if (target[fieldParts[i]] === undefined) {
                    target[fieldParts[i]] = {};
                }
                target = target[fieldParts[i]];
            }
            target[fieldParts[fieldParts.length - 1]] = url;
        }
    }
    
    console.log('Media selected for field:', field, 'URL:', url);
});
</script>
@endsection
