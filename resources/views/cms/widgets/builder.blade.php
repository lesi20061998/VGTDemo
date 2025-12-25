@extends('cms.layouts.app')

@section('title', 'Widget Builder')
@section('page-title', 'Widget Builder - Drag & Drop')

@section('content')
<div class="flex gap-6">
    <!-- Widget Templates Sidebar -->
    <div class="w-2/3 bg-white rounded-lg shadow-sm p-6">
        <h3 class="font-bold text-2xl mb-6">Widget Templates ({{ array_sum(array_map('count', $availableWidgets)) }} widgets)</h3>
        
        @foreach($availableWidgets as $category => $widgets)
        <div class="mb-6">
            <h4 class="font-semibold text-lg mb-3 capitalize text-gray-700 flex items-center">
                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs mr-2">{{ count($widgets) }}</span>
                {{ ucfirst(str_replace('_', ' ', $category)) }}
            </h4>
            <div class="grid grid-cols-2 gap-4">
                @foreach($widgets as $widget)
                <div class="widget-template border-2 border-dashed border-gray-300 rounded-lg p-4 cursor-move hover:border-blue-500 hover:bg-blue-50 transition" 
                     draggable="true" 
                     data-type="{{ $widget['type'] }}"
                     data-category="{{ $category }}"
                     title="{{ $widget['metadata']['description'] ?? $widget['description'] ?? 'No description available' }}">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            @if(isset($widget['metadata']['icon']) && str_contains($widget['metadata']['icon'], 'heroicon'))
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    {!! $widget['icon'] ?? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>' !!}
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    {!! $widget['icon'] ?? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>' !!}
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-sm">{{ $widget['metadata']['name'] ?? $widget['name'] }}</h4>
                            <p class="text-xs text-gray-500 line-clamp-2">{{ Str::limit($widget['metadata']['description'] ?? $widget['description'] ?? $widget['type'], 50) }}</p>
                            @if(isset($widget['metadata']['version']))
                                <span class="inline-block bg-gray-100 text-gray-600 text-xs px-1 rounded mt-1">v{{ $widget['metadata']['version'] }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    <!-- Drop Zone -->
    <div class="w-1/3">
        <!-- Homepage Main -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-4">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-lg">Homepage Main</h3>
                <button onclick="saveWidgets()" class="px-4 py-2 bg-[#98191F] text-white rounded-lg hover:bg-[#7a1419]">Save All</button>
            </div>

            <div id="dropZone" class="min-h-[300px] border-2 border-dashed border-gray-300 rounded-lg p-4" data-area="homepage-main">
                <p class="text-gray-400 text-center py-20">
                    <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Drag widgets here to build your page<br>
                    <small class="text-xs">You can reorder widgets by dragging them</small>
                </p>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-4">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-lg">Sidebar</h3>
            </div>

            <div id="sidebarZone" class="min-h-[200px] border-2 border-dashed border-gray-300 rounded-lg p-4" data-area="sidebar">
                <p class="text-gray-400 text-center py-12">Drag widgets here for sidebar</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-lg">Footer</h3>
            </div>

            <div id="footerZone" class="min-h-[200px] border-2 border-dashed border-gray-300 rounded-lg p-4" data-area="footer">
                <p class="text-gray-400 text-center py-12">Drag widgets here for footer</p>
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
        <div id="configForm"></div>
        <div class="flex justify-end gap-3 mt-6">
            <button onclick="closeConfig()" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Cancel</button>
            <button onclick="previewWidget()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">Preview</button>
            <button onclick="saveConfig()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save</button>
        </div>
    </div>
</div>

<style>
.widget-dragging { 
    opacity: 0.5; 
    transform: scale(0.95); 
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
</style>

<script>
let draggedElement = null;
let existingWidgetsGrouped = @json($existingWidgets ?? []);
let widgets = [];
let draggedWidgetItem = null; // For reordering widgets within areas

// Flatten grouped widgets
Object.values(existingWidgetsGrouped).forEach(group => {
    widgets = widgets.concat(group);
});

const usedWidgets = new Set();

document.querySelectorAll('.widget-template').forEach(template => {
    template.addEventListener('dragstart', (e) => {
        draggedElement = e.target;
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
        }
    });
});

function addWidget(type, area = 'homepage-main') {
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
    
    // Render homepage widgets with sortable functionality
    if (homepageWidgets.length === 0) {
        dropZone.innerHTML = '<p class="text-gray-400 text-center py-20">Drag widgets here to build your page</p>';
    } else {
        dropZone.innerHTML = homepageWidgets.map((widget) => {
            const globalIndex = widgets.indexOf(widget);
            return renderWidgetItem(widget, globalIndex, true); // true for sortable
        }).join('');
        
        // Make homepage widgets sortable
        makeSortable(dropZone, 'homepage-main');
    }
    
    // Render sidebar widgets with sortable functionality
    if (sidebarWidgets.length === 0) {
        sidebarZone.innerHTML = '<p class="text-gray-400 text-center py-12">Drag widgets here for sidebar</p>';
    } else {
        sidebarZone.innerHTML = sidebarWidgets.map((widget) => {
            const globalIndex = widgets.indexOf(widget);
            return renderWidgetItem(widget, globalIndex, true); // true for sortable
        }).join('');
        
        // Make sidebar widgets sortable
        makeSortable(sidebarZone, 'sidebar');
    }
  
    // Render footer widgets with sortable functionality
    if (footerWidgets.length === 0) {
        footerZone.innerHTML = '<p class="text-gray-400 text-center py-12">Drag widgets here for footer</p>';
    } else {
        footerZone.innerHTML = footerWidgets.map((widget) => {
            const globalIndex = widgets.indexOf(widget);
            return renderWidgetItem(widget, globalIndex, true); // true for sortable
        }).join('');
        
        // Make footer widgets sortable
        makeSortable(footerZone, 'footer');
    }
}

function renderWidgetItem(widget, globalIndex, sortable = false) {
    const dragHandle = sortable ? `
        <div class="drag-handle cursor-move text-gray-400 hover:text-gray-600 mr-2" title="Drag to reorder">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
            </svg>
        </div>
    ` : '';
    
    return `
        <div class="widget-item mb-4 border rounded-lg p-4 bg-blue-50" data-widget-index="${globalIndex}" ${sortable ? 'draggable="true"' : ''}>
            <div class="flex justify-between items-center mb-2">
                <div class="flex items-center">
                    ${dragHandle}
                    <span class="font-semibold">${widget.name}</span>
                </div>
                <div class="flex gap-2">
                    <button onclick="editWidget(${globalIndex})" class="text-blue-600 hover:text-blue-800 text-sm px-2 py-1 rounded">Edit</button>
                    <button onclick="removeWidget(${globalIndex})" class="text-red-600 hover:text-red-800 text-sm px-2 py-1 rounded">Remove</button>
                </div>
            </div>
            <div class="text-sm text-gray-600">Type: ${widget.type} | Area: ${widget.area} | Order: ${widget.sort_order}</div>
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
    
    // Request preview from server
    fetch('{{ route("cms.widgets.preview") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
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
    
    // Use AJAX to get form fields from server
    fetch(`{{ route('cms.widgets.fields') }}?type=${widgetType}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let formHtml = data.form_html;
                
                // Populate form with current values
                const form = document.getElementById('configForm');
                form.innerHTML = formHtml;
                
                // Set current values
                const fields = data.fields || [];
                fields.forEach(field => {
                    const fieldName = field.name;
                    const fieldValue = widget.settings[fieldName];
                    const fieldElement = form.querySelector(`[name="${fieldName}"]`);
                    
                    if (fieldElement && fieldValue !== undefined) {
                        if (fieldElement.type === 'checkbox') {
                            fieldElement.checked = fieldValue;
                        } else if (fieldElement.tagName === 'SELECT') {
                            fieldElement.value = fieldValue;
                        } else {
                            fieldElement.value = fieldValue;
                        }
                    }
                });
                
                // Add variant selector if available
                if (data.variants && Object.keys(data.variants).length > 1) {
                    const variantHtml = `
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Widget Variant</label>
                            <select id="cfg_variant" class="w-full px-3 py-2 border rounded-lg">
                                ${Object.entries(data.variants).map(([key, label]) => 
                                    `<option value="${key}" ${widget.variant === key ? 'selected' : ''}>${label}</option>`
                                ).join('')}
                            </select>
                        </div>
                    `;
                    form.insertAdjacentHTML('afterbegin', variantHtml);
                }
            } else {
                // Fallback to legacy form rendering
                return renderLegacyConfigForm(widget);
            }
        })
        .catch(error => {
            console.error('Error loading widget fields:', error);
            return renderLegacyConfigForm(widget);
        });
}

function renderLegacyConfigForm(widget) {
    if (widget.type === 'hero') {
        return `
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Title</label>
                    <input type="text" id="cfg_title" value="${widget.settings.title}" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Subtitle</label>
                    <input type="text" id="cfg_subtitle" value="${widget.settings.subtitle}" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Button Text</label>
                    <input type="text" id="cfg_button_text" value="${widget.settings.button_text}" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Button Link</label>
                    <input type="text" id="cfg_button_link" value="${widget.settings.button_link}" class="w-full px-3 py-2 border rounded-lg">
                </div>
            </div>
        `;
    } else if (widget.type === 'features') {
        return `
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Title</label>
                    <input type="text" id="cfg_title" value="${widget.settings.title}" class="w-full px-3 py-2 border rounded-lg">
                </div>
                ${widget.settings.features.map((f, i) => `
                    <div class="border-t pt-3">
                        <h4 class="font-medium mb-2">Feature ${i + 1}</h4>
                        <input type="text" id="cfg_f${i}_title" value="${f.title}" placeholder="Title" class="w-full px-3 py-2 border rounded-lg mb-2">
                        <input type="text" id="cfg_f${i}_desc" value="${f.desc}" placeholder="Description" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                `).join('')}
            </div>
        `;
    } else if (widget.type === 'cta') {
        return `
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Title</label>
                    <input type="text" id="cfg_title" value="${widget.settings.title}" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Subtitle</label>
                    <input type="text" id="cfg_subtitle" value="${widget.settings.subtitle}" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Button Text</label>
                    <input type="text" id="cfg_button_text" value="${widget.settings.button_text}" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Button Link</label>
                    <input type="text" id="cfg_button_link" value="${widget.settings.button_link}" class="w-full px-3 py-2 border rounded-lg">
                </div>
            </div>
        `;
    } else if (widget.type === 'analytics') {
        return `
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Title</label>
                    <input type="text" id="cfg_title" value="${widget.settings.title || 'Thống kê truy cập'}" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Show Title</label>
                    <input type="checkbox" id="cfg_show_title" ${widget.settings.show_title !== false ? 'checked' : ''} class="rounded">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Style</label>
                    <select id="cfg_style" class="w-full px-3 py-2 border rounded-lg">
                        <option value="default" ${widget.settings.style === 'default' ? 'selected' : ''}>Mặc định</option>
                        <option value="cards" ${widget.settings.style === 'cards' ? 'selected' : ''}>Thẻ card</option>
                        <option value="compact" ${widget.settings.style === 'compact' ? 'selected' : ''}>Gọn gàng</option>
                        <option value="modern" ${widget.settings.style === 'modern' ? 'selected' : ''}>Hiện đại</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Columns</label>
                    <select id="cfg_columns" class="w-full px-3 py-2 border rounded-lg">
                        <option value="1" ${widget.settings.columns === '1' ? 'selected' : ''}>1 cột</option>
                        <option value="2" ${widget.settings.columns === '2' ? 'selected' : ''}>2 cột</option>
                        <option value="3" ${widget.settings.columns === '3' ? 'selected' : ''}>3 cột</option>
                        <option value="4" ${widget.settings.columns === '4' ? 'selected' : ''}>4 cột</option>
                    </select>
                </div>
            </div>
        `;
    }
}

function saveConfig() {
    const widget = widgets[currentEditIndex];
    const form = document.getElementById('configForm');
    const formData = new FormData(form);
    
    // Update widget settings from form
    for (let [key, value] of formData.entries()) {
        // Handle array fields (like repeatable fields)
        if (key.includes('[')) {
            // Parse array notation like "social_links[0][platform]"
            const matches = key.match(/^([^[]+)(\[.+\])$/);
            if (matches) {
                const fieldName = matches[1];
                const arrayPath = matches[2];
                
                if (!widget.settings[fieldName]) {
                    widget.settings[fieldName] = [];
                }
                
                // Simple array handling - could be improved for nested arrays
                const arrayIndex = arrayPath.match(/\[(\d+)\]/);
                if (arrayIndex) {
                    const index = parseInt(arrayIndex[1]);
                    const subField = arrayPath.match(/\[(\d+)\]\[([^\]]+)\]/);
                    
                    if (subField) {
                        if (!widget.settings[fieldName][index]) {
                            widget.settings[fieldName][index] = {};
                        }
                        widget.settings[fieldName][index][subField[2]] = value;
                    }
                }
            }
        } else {
            widget.settings[key] = value;
        }
    }
    
    // Handle checkboxes (they don't appear in FormData if unchecked)
    const checkboxes = form.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        if (!formData.has(checkbox.name)) {
            widget.settings[checkbox.name] = false;
        } else {
            widget.settings[checkbox.name] = true;
        }
    });
    
    // Update variant if available
    const variantSelect = document.getElementById('cfg_variant');
    if (variantSelect) {
        widget.variant = variantSelect.value;
    }
    
    closeConfig();
    renderWidgets();
}

function closeConfig() {
    document.getElementById('configModal').classList.add('hidden');
    currentEditIndex = null;
}

// Load existing widgets on page load
window.addEventListener('DOMContentLoaded', () => {
    if (widgets.length > 0) {
        renderWidgets();
    }
});

let isSaving = false;

async function saveWidgets() {
    if (isSaving) return;
    isSaving = true;
    
    const btn = event.target;
    btn.disabled = true;
    btn.textContent = 'Saving...';
    
    const baseUrl = '{{ isset($currentProject) ? route("project.admin.widgets.save-all", $currentProject->code) : route("cms.widgets.save-all") }}';
    //console.log(baseUrl);
    try {
        const response = await fetch(baseUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                widgets: widgets
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            btn.textContent = 'Saved!';
            btn.className = btn.className.replace('bg-[#98191F]', 'bg-green-600');
            
            // Show success message
            if (result.message) {
                showAlert(result.message, 'success');
            }
        } else {
            btn.textContent = 'Save Failed';
            btn.className = btn.className.replace('bg-[#98191F]', 'bg-red-600');
            
            // Show error message
            showAlert(result.message || 'Failed to save widgets', 'error');
            
            if (result.errors) {
                console.error('Widget save errors:', result.errors);
            }
        }
    } catch (error) {
        console.error('Error saving widgets:', error);
        btn.textContent = 'Save Failed';
        btn.className = btn.className.replace('bg-[#98191F]', 'bg-red-600');
        showAlert('Network error while saving widgets', 'error');
    }
    
    setTimeout(() => {
        btn.disabled = false;
        btn.textContent = 'Save All';
        btn.className = btn.className.replace('bg-green-600', 'bg-[#98191F]').replace('bg-red-600', 'bg-[#98191F]');
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
</script>
@endsection
