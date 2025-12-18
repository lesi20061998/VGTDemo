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
            <h4 class="font-semibold text-lg mb-3 capitalize text-gray-700">{{ ucfirst($category) }}</h4>
            <div class="grid grid-cols-2 gap-4">
                @foreach($widgets as $widget)
                <div class="widget-template border-2 border-dashed border-gray-300 rounded-lg p-4 cursor-move hover:border-blue-500 hover:bg-blue-50 transition" draggable="true" data-type="{{ $widget['type'] }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {!! $widget['icon'] ?? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>' !!}
                        </svg>
                        <div>
                            <h4 class="font-semibold">{{ $widget['name'] }}</h4>
                            <p class="text-xs text-gray-500">{{ $widget['description'] ?? $widget['type'] }}</p>
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
                <p class="text-gray-400 text-center py-20">Drag widgets here to build your page</p>
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
            <button onclick="saveConfig()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save</button>
        </div>
    </div>
</div>

<style>
.widget-dragging { opacity: 0.5; transform: scale(0.95); }

</style>

<script>
let draggedElement = null;
let existingWidgetsGrouped = @json($existingWidgets ?? []);
let widgets = [];

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
    
    // Render homepage widgets
    if (homepageWidgets.length === 0) {
        dropZone.innerHTML = '<p class="text-gray-400 text-center py-20">Drag widgets here to build your page</p>';
    } else {
        dropZone.innerHTML = homepageWidgets.map((widget) => {
            const globalIndex = widgets.indexOf(widget);
            return renderWidgetItem(widget, globalIndex);
        }).join('');
    }
    
    // Render sidebar widgets
    if (sidebarWidgets.length === 0) {
        sidebarZone.innerHTML = '<p class="text-gray-400 text-center py-12">Drag widgets here for sidebar</p>';
    } else {
        sidebarZone.innerHTML = sidebarWidgets.map((widget) => {
            const globalIndex = widgets.indexOf(widget);
            return renderWidgetItem(widget, globalIndex);
        }).join('');
    }
  
// Render footer widgets
    if (footerWidgets.length === 0) {
        footerZone.innerHTML = '<p class="text-gray-400 text-center py-12">Drag widgets here for footer</p>';
    } else {
        footerZone.innerHTML = footerWidgets.map((widget) => {
            const globalIndex = widgets.indexOf(widget);
            return renderWidgetItem(widget, globalIndex);
        }).join('');
    }
}

function renderWidgetItem(widget, globalIndex) {
    return `
        <div class="mb-4 border rounded-lg p-4 bg-blue-50">
            <div class="flex justify-between items-center mb-2">
                <span class="font-semibold">${widget.name}</span>
                <div class="flex gap-2">
                    <button onclick="editWidget(${globalIndex})" class="text-blue-600 hover:text-blue-800">Edit</button>
                    <button onclick="removeWidget(${globalIndex})" class="text-red-600 hover:text-red-800">Remove</button>
                </div>
            </div>
            <div class="text-sm text-gray-600">Type: ${widget.type} | Area: ${widget.area}</div>
        </div>
    `;
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
    
    form.innerHTML = renderConfigForm(widget);
    modal.classList.remove('hidden');
}

function renderConfigForm(widget) {
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
    
    if (widget.type === 'hero' || widget.type === 'cta') {
        widget.settings.title = document.getElementById('cfg_title').value;
        widget.settings.subtitle = document.getElementById('cfg_subtitle').value;
        widget.settings.button_text = document.getElementById('cfg_button_text').value;
        widget.settings.button_link = document.getElementById('cfg_button_link').value;
    } else if (widget.type === 'features') {
        widget.settings.title = document.getElementById('cfg_title').value;
        widget.settings.features = widget.settings.features.map((f, i) => ({
            ...f,
            title: document.getElementById(`cfg_f${i}_title`).value,
            desc: document.getElementById(`cfg_f${i}_desc`).value
        }));
    } else if (widget.type === 'analytics') {
        widget.settings.title = document.getElementById('cfg_title').value;
        widget.settings.show_title = document.getElementById('cfg_show_title').checked;
        widget.settings.style = document.getElementById('cfg_style').value;
        widget.settings.columns = document.getElementById('cfg_columns').value;
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
    
    const baseUrl = '/cms/admin/widgets';
    
    // Save widgets
    for (const widget of widgets) {
        try {
            await fetch(baseUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(widget)
            });
        } catch (error) {
            console.error('Error saving widget:', error);
        }
    }
    
    btn.textContent = 'Saved!';
    setTimeout(() => {
        btn.disabled = false;
        btn.textContent = 'Save All';
        isSaving = false;
    }, 2000);
}
</script>
@endsection
