@extends('cms.layouts.app')

@section('title', 'Widget Templates')
@section('page-title', 'Quản lý Widget Templates')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Widget Templates</h1>
        <p class="text-gray-600 mt-1">Quản lý các loại widget và cấu hình fields</p>
    </div>
    <div class="flex items-center gap-3">
        {{-- Import Button --}}
        <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')" 
                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            Import JSON
        </button>
        
        {{-- Export All Button --}}
        @if(!empty($dbTemplates))
        <a href="{{ isset($currentProject) ? route('project.admin.widget-templates.export-all', $currentProject->code) : route('cms.widget-templates.export-all') }}" 
           class="px-4 py-2 border border-green-300 text-green-700 rounded-lg hover:bg-green-50 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Export All
        </a>
        @endif
        
        <a href="{{ isset($currentProject) ? route('project.admin.widget-templates.create', $currentProject->code) : route('cms.widget-templates.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tạo Widget Template
        </a>
    </div>
</div>

@if (session()->has('success'))
    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
        {{ session('success') }}
    </div>
@endif

@if (session()->has('error'))
    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

{{-- Database Templates (Custom) --}}
@if(!empty($dbTemplates))
<div class="bg-white rounded-lg shadow-sm mb-6">
    <div class="p-6 border-b bg-gradient-to-r from-purple-50 to-blue-50">
        <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            Custom Widget Templates
        </h2>
        <p class="text-gray-600 mt-1">Các widget template tự tạo qua giao diện</p>
    </div>

    @foreach($dbTemplates as $category => $templates)
    <div class="p-6 {{ !$loop->last ? 'border-b' : '' }}">
        <h3 class="text-lg font-semibold mb-4 capitalize flex items-center gap-2">
            <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
            {{ ucfirst($category) }} ({{ count($templates) }})
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($templates as $template)
            <div class="border border-purple-200 rounded-lg p-4 hover:shadow-md transition bg-white">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h4 class="font-semibold text-gray-900">{{ $template['name'] }}</h4>
                        <p class="text-sm text-gray-500">{{ $template['type'] }}</p>
                    </div>
                    <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded">Custom</span>
                </div>
                
                <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $template['description'] ?? 'Không có mô tả' }}</p>
                
                <div class="text-xs text-gray-500 mb-3">
                    <strong>Fields:</strong> {{ count($template['config_schema']['fields'] ?? []) }} cấu hình
                </div>
                
                <div class="flex gap-2">
                    <a href="{{ isset($currentProject) ? route('project.admin.widget-templates.export', [$currentProject->code, $template['id']]) : route('cms.widget-templates.export', $template['id']) }}" 
                       class="px-3 py-2 border border-green-300 text-green-600 rounded hover:bg-green-50 text-sm" title="Export JSON">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </a>
                    <a href="{{ isset($currentProject) ? route('project.admin.widget-templates.edit', [$currentProject->code, $template['id']]) : route('cms.widget-templates.edit', $template['id']) }}" 
                       class="flex-1 text-center px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                        Sửa
                    </a>
                    <form action="{{ isset($currentProject) ? route('project.admin.widget-templates.destroy', [$currentProject->code, $template['id']]) : route('cms.widget-templates.destroy', $template['id']) }}" method="POST" 
                          onsubmit="return confirm('Bạn có chắc muốn xóa template này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-2 border border-red-300 text-red-600 rounded hover:bg-red-50 text-sm">
                            Xóa
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Code-based Widgets --}}
<div class="bg-white rounded-lg shadow-sm">
    <div class="p-6 border-b">
        <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
            </svg>
            Code-based Widgets
        </h2>
        <p class="text-gray-600 mt-1">Widgets được định nghĩa trong code ({{ array_sum(array_map('count', $codeWidgets)) }} widgets)</p>
    </div>

    @foreach($codeWidgets as $category => $categoryWidgets)
    <div class="p-6 {{ !$loop->last ? 'border-b' : '' }}">
        <h3 class="text-lg font-semibold mb-4 capitalize flex items-center gap-2">
            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
            {{ ucfirst($category) }} ({{ count($categoryWidgets) }})
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($categoryWidgets as $widget)
            <div class="border rounded-lg p-4 hover:shadow-md transition">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold">{{ $widget['metadata']['name'] ?? $widget['type'] }}</h4>
                            <p class="text-sm text-gray-500">{{ $widget['type'] }}</p>
                        </div>
                    </div>
                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded">Code</span>
                </div>
                
                <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $widget['metadata']['description'] ?? 'No description' }}</p>
                
                <div class="text-xs text-gray-500 mb-3">
                    <strong>Fields:</strong> {{ count($widget['metadata']['fields'] ?? []) }} cấu hình
                </div>
                
                <div class="flex gap-2">
                    <button onclick="previewWidget('{{ $widget['type'] }}')" 
                            class="flex-1 text-center px-3 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 text-sm">
                        Preview
                    </button>
                    <button onclick="showConfig('{{ $widget['type'] }}')" 
                            class="px-3 py-2 border border-gray-300 rounded hover:bg-gray-50 text-sm">
                        Info
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
const codeWidgets = @json($codeWidgets);

function previewWidget(type) {
    document.getElementById('previewModal').classList.remove('hidden');
    document.getElementById('previewContent').innerHTML = '<div class="text-center py-8">Đang tải...</div>';
    
    let widget = null;
    Object.values(codeWidgets).forEach(category => {
        category.forEach(w => {
            if (w.type === type) widget = w;
        });
    });
    
    if (!widget) return;
    
    const defaultSettings = {};
    (widget.metadata?.fields || []).forEach(field => {
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
    Object.values(codeWidgets).forEach(category => {
        category.forEach(w => {
            if (w.type === type) widget = w;
        });
    });
    
    if (!widget) return;
    
    const fields = (widget.metadata?.fields || []).map(f => f.name).join(', ') || 'Không có';
    alert(`Widget: ${widget.metadata?.name || widget.type}\nType: ${widget.type}\nCategory: ${widget.metadata?.category || 'general'}\nFields: ${fields}`);
}

function closePreview() {
    document.getElementById('previewModal').classList.add('hidden');
}
</script>

<!-- Import Modal -->
<div id="importModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-md">
        <div class="flex justify-between items-center p-4 border-b">
            <h3 class="text-lg font-semibold">Import Widget Templates</h3>
            <button onclick="document.getElementById('importModal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700">✕</button>
        </div>
        <form action="{{ isset($currentProject) ? route('project.admin.widget-templates.import', $currentProject->code) : route('cms.widget-templates.import') }}" 
              method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Chọn file JSON</label>
                <input type="file" name="json_file" accept=".json" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="text-xs text-gray-500 mt-1">Hỗ trợ file JSON được export từ hệ thống (tối đa 2MB)</p>
            </div>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                <p class="text-sm text-yellow-800">
                    <strong>Lưu ý:</strong> Các template có type trùng với template đã tồn tại sẽ bị bỏ qua.
                </p>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" 
                        class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Hủy
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Import
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
