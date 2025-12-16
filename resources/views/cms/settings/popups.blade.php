@extends('cms.layouts.app')

@section('title', 'Cấu hình Popup')
@section('page-title', 'Popup Quảng cáo')

@section('content')
<div class="mb-6">
    <a href="{{ route('project.admin.settings.index', $currentProject->code) }}" class="text-sm text-gray-600 hover:text-gray-900">← Quay lại</a>
</div>

<div class="bg-white rounded-lg shadow-sm p-6">
    <form action="{{ route('project.admin.settings.save', $currentProject->code) }}" method="POST">
        @csrf
        
        <div class="space-y-6">
            @php
                $popup = json_decode(setting('popup', '{}'), true);
                $enabled = $popup['enabled'] ?? false;
                $delay = $popup['delay'] ?? 3;
                $frequency = $popup['frequency'] ?? 'once';
            @endphp

            <div class="border-b pb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="popup[enabled]" value="1" {{ $enabled ? 'checked' : '' }} class="mr-2 rounded">
                    <span class="font-medium text-lg">Bật Popup</span>
                </label>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Độ trễ hiển thị (giây)</label>
                    <input type="number" name="popup[delay]" value="{{ $delay }}" min="0" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Tần suất hiển thị</label>
                    <select name="popup[frequency]" class="w-full px-4 py-2 border rounded-lg">
                        <option value="once" {{ $frequency == 'once' ? 'selected' : '' }}>Chỉ 1 lần</option>
                        <option value="daily" {{ $frequency == 'daily' ? 'selected' : '' }}>Mỗi ngày</option>
                        <option value="always" {{ $frequency == 'always' ? 'selected' : '' }}>Mỗi lần truy cập</option>
                    </select>
                </div>
            </div>

            <div x-data="{ mode: 'builder' }">
                <div class="flex items-center gap-4 mb-4">
                    <button type="button" @click="mode = 'builder'" :class="mode === 'builder' ? 'bg-blue-600 text-white' : 'bg-gray-200'" class="px-4 py-2 rounded-lg">Form Builder</button>
                    <button type="button" @click="mode = 'code'" :class="mode === 'code' ? 'bg-blue-600 text-white' : 'bg-gray-200'" class="px-4 py-2 rounded-lg">Custom Code</button>
                </div>
                
                <div x-show="mode === 'builder'" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Chọn Form</label>
                        <select name="popup[form_id]" class="w-full px-4 py-2 border rounded-lg">
                            <option value="">-- Chọn form --</option>
                            @foreach(json_decode(setting('forms', '[]'), true) as $index => $form)
                            <option value="{{ $index }}" {{ ($popup['form_id'] ?? '') == $index ? 'selected' : '' }}>{{ $form['name'] }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Quản lý form tại <a href="{{ route('project.admin.settings.forms', $currentProject->code) }}" class="text-blue-600">Settings > Form</a></p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Tiêu đề Popup</label>
                            <input type="text" name="popup[title]" value="{{ $popup['title'] ?? '' }}" class="w-full px-4 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Màu nền</label>
                            <input type="color" name="popup[bg_color]" value="{{ $popup['bg_color'] ?? '#ffffff' }}" class="w-full h-10 border rounded-lg">
                        </div>
                    </div>

                </div>
                
                <div x-show="mode === 'code'">
                    <label class="block text-sm font-medium mb-2">Nội dung Popup (HTML/CSS/JS)</label>
                    <div id="popupEditor" style="height: 400px; border: 1px solid #d1d5db; border-radius: 0.5rem;"></div>
                    <textarea name="popup[content]" id="popupContent" class="hidden">{{ old('popup.content', $popup['content'] ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Lưu cấu hình</button>
        </div>
    </form>
</div>

<!-- Preview -->
<div class="bg-white rounded-lg shadow-sm p-6 mt-6">
    <h3 class="font-semibold mb-4">Preview</h3>
    <div class="flex items-center justify-center p-8 bg-gray-100 rounded-lg">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 relative">
            <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-600">×</button>
            <img src="https://via.placeholder.com/400x200" class="w-full rounded-lg mb-4">
            <h3 class="text-2xl font-bold mb-2">Khuyến mãi đặc biệt!</h3>
            <p class="text-gray-600 mb-4">Giảm giá lên đến 50% cho tất cả sản phẩm!</p>
            <button class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700">Mua ngay</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.45.0/min/vs/editor/editor.main.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.45.0/min/vs/loader.min.js"></script>
<script>
require.config({ paths: { vs: 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.45.0/min/vs' }});
require(['vs/editor/editor.main'], function() {
    const editor = monaco.editor.create(document.getElementById('popupEditor'), {
        value: document.getElementById('popupContent').value,
        language: 'html',
        theme: 'vs-dark',
        minimap: { enabled: false },
        automaticLayout: true
    });
    
    document.querySelector('form').addEventListener('submit', function() {
        document.getElementById('popupContent').value = editor.getValue();
    });
});
</script>
@endsection
