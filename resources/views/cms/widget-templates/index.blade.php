@extends('cms.layouts.app')

@section('title', 'Widget Templates')
@section('page-title', 'Quản lý Widget Templates')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <div class="p-6 border-b">
        <h2 class="text-2xl font-bold">Thư viện Widget Templates</h2>
        <p class="text-gray-600 mt-2">Tổng cộng {{ array_sum(array_map('count', $widgets)) }} widget templates đã đăng ký</p>
    </div>

    @foreach($widgets as $category => $categoryWidgets)
    <div class="p-6 border-b">
        <h3 class="text-xl font-semibold mb-4 capitalize">📁 {{ ucfirst($category) }} ({{ count($categoryWidgets) }} widgets)</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($categoryWidgets as $widget)
            <div class="border rounded-lg p-4 hover:shadow-md transition">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                {!! $widget['icon'] ?? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>' !!}
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold">{{ $widget['name'] }}</h4>
                            <p class="text-sm text-gray-500">{{ $widget['type'] }}</p>
                        </div>
                    </div>
                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded">✓ Registered</span>
                </div>
                
                <p class="text-gray-600 text-sm mb-4">{{ $widget['description'] ?? 'No description available' }}</p>
                
                <div class="text-xs text-gray-500 mb-3">
                    <strong>Class:</strong> {{ $widget['class'] }}<br>
                    <strong>Fields:</strong> {{ count($widget['fields'] ?? []) }} cấu hình
                </div>
                
                <div class="flex gap-2">
                    <button onclick="previewWidget('{{ $widget['type'] }}')" 
                            class="flex-1 text-center px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                        Preview
                    </button>
                    <button onclick="showConfig('{{ $widget['type'] }}')" 
                            class="px-3 py-2 border border-gray-300 rounded hover:bg-gray-50 text-sm">
                        Config
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>

<!-- Preview Modal -->
<div id="previewModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-4xl max-h-[90vh] overflow-hidden">
        <div class="flex justify-between items-center p-4 border-b">
            <h3 class="text-lg font-semibold">Widget Preview</h3>
            <button onclick="closePreview()" class="text-gray-500 hover:text-gray-700">✕</button>
        </div>
        <div id="previewContent" class="p-4 overflow-y-auto max-h-[70vh]">
            <div class="text-center py-8">Đang tải...</div>
        </div>
    </div>
</div>

<script>
const widgets = @json($widgets);

function previewWidget(type) {
    document.getElementById('previewModal').classList.remove('hidden');
    document.getElementById('previewContent').innerHTML = '<div class="text-center py-8">Đang tải...</div>';
    
    // Get default settings for preview
    let widget = null;
    Object.values(widgets).forEach(category => {
        category.forEach(w => {
            if (w.type === type) widget = w;
        });
    });
    
    if (!widget) return;
    
    const defaultSettings = {};
    (widget.fields || []).forEach(field => {
        defaultSettings[field.name] = field.default || '';
    });
    
    fetch(`widget-templates/${type}/preview`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(defaultSettings)
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('previewContent').innerHTML = data.html;
    })
    .catch(error => {
        document.getElementById('previewContent').innerHTML = '<div class="text-center py-8 text-red-600">Lỗi tải preview</div>';
    });
}

function showConfig(type) {
    let widget = null;
    Object.values(widgets).forEach(category => {
        category.forEach(w => {
            if (w.type === type) widget = w;
        });
    });
    
    if (!widget) return;
    
    alert(`Widget: ${widget.name}\nType: ${widget.type}\nCategory: ${widget.category}\nFields: ${(widget.fields || []).map(f => f.name).join(', ')}`);
}

function closePreview() {
    document.getElementById('previewModal').classList.add('hidden');
}
</script>
@endsection