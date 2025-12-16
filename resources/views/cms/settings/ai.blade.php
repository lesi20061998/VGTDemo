@extends('cms.layouts.app')

@section('title', 'Cấu hình AI')
@section('page-title', 'AI Content Generator')

@section('content')
<div class="mb-6">
    <a href="{{ route('cms.settings.index') }}" class="text-sm text-gray-600 hover:text-gray-900">← Quay lại</a>
</div>

<div class="bg-white rounded-lg shadow-sm p-6" x-data="aiConfig()">
    <form action="{{ route('cms.settings.save') }}" method="POST">
        @csrf
        
        <div class="space-y-6">
            @php
                $ai = json_decode(setting('ai', '{}'), true);
                $enabled = $ai['enabled'] ?? false;
                $openaiKey = $ai['openai_key'] ?? '';
                $geminiKey = $ai['gemini_key'] ?? '';
            @endphp

            <!-- Enable AI -->
            <div class="border-b pb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="ai[enabled]" value="1" {{ $enabled ? 'checked' : '' }} class="mr-2 rounded">
                    <span class="font-medium text-lg">Bật AI Content Generator</span>
                </label>
                <p class="text-sm text-gray-500 mt-1 ml-6">Tự động tạo nội dung bằng AI</p>
            </div>

            <!-- OpenAI -->
            <div class="border rounded-lg p-4">
                <h3 class="font-semibold mb-3 flex items-center gap-2">
                    <img src="https://openai.com/favicon.ico" class="w-6 h-6">
                    OpenAI (ChatGPT)
                </h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium mb-1">API Key</label>
                        <div class="flex gap-2">
                            <input type="password" name="ai[openai_key]" x-model="openaiKey" value="{{ $openaiKey }}" 
                                   class="flex-1 px-3 py-2 text-sm border rounded-lg font-mono" placeholder="sk-...">
                            <button type="button" @click="testConnection('openai')" 
                                    class="px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                                <span x-show="!testing">Test</span>
                                <span x-show="testing">...</span>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Lấy tại: <a href="https://platform.openai.com/api-keys" target="_blank" class="text-blue-600">platform.openai.com</a></p>
                    </div>
                    <input type="hidden" name="ai[openai_model]" value="gpt-3.5-turbo">
                </div>
            </div>

            <!-- Gemini -->
            <div class="border rounded-lg p-4">
                <h3 class="font-semibold mb-3 flex items-center gap-2">
                    <img src="https://www.gstatic.com/lamda/images/favicon_v1_150160cddff7f294ce30.svg" class="w-6 h-6">
                    Google Gemini
                </h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium mb-1">API Key</label>
                        <div class="flex gap-2">
                            <input type="password" name="ai[gemini_key]" x-model="geminiKey" value="{{ $geminiKey }}" 
                                   class="flex-1 px-3 py-2 text-sm border rounded-lg font-mono" placeholder="AIza...">
                            <button type="button" @click="testConnection('gemini')" 
                                    class="px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                                <span x-show="!testing">Test</span>
                                <span x-show="testing">...</span>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Lấy tại: <a href="https://makersuite.google.com/app/apikey" target="_blank" class="text-blue-600">makersuite.google.com</a></p>
                    </div>
                    <input type="hidden" name="ai[gemini_model]" value="gemini-pro">
                </div>
            </div>

            <div x-show="testResult" class="p-3 rounded-lg" :class="testSuccess ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800'">
                <p class="text-sm" x-text="testMessage"></p>
            </div>

            <input type="hidden" name="ai[temperature]" value="0.7">
            <input type="hidden" name="ai[max_tokens]" value="2000">

            <!-- Usage Stats -->
            <div class="border-t pt-4">
                <h3 class="font-medium mb-3">Thống kê sử dụng</h3>
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600">Tổng requests</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $ai['stats']['total_requests'] ?? 0 }}</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600">Tokens đã dùng</p>
                        <p class="text-2xl font-bold text-green-600">{{ number_format($ai['stats']['total_tokens'] ?? 0) }}</p>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600">Chi phí ước tính</p>
                        <p class="text-2xl font-bold text-purple-600">${{ number_format(($ai['stats']['estimated_cost'] ?? 0), 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Lưu cấu hình</button>
        </div>
    </form>
</div>

<!-- Test Content Generation -->
<div class="bg-white rounded-lg shadow-sm p-6 mt-6" x-data="{ generating: false, result: '' }">
    <h3 class="font-semibold mb-4">Test tạo nội dung</h3>
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium mb-2">Prompt</label>
            <textarea x-ref="prompt" rows="3" class="w-full px-4 py-2 border rounded-lg" placeholder="Viết mô tả sản phẩm iPhone 15 Pro Max..."></textarea>
        </div>
        <button type="button" @click="generateContent()" :disabled="generating"
                class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 disabled:opacity-50 flex items-center gap-2">
            <svg x-show="!generating" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            <svg x-show="generating" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span x-text="generating ? 'Đang tạo...' : 'Tạo nội dung'"></span>
        </button>
        <div x-show="result" class="bg-gray-50 rounded-lg p-4 border">
            <p class="text-sm whitespace-pre-wrap" x-text="result"></p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
function aiConfig() {
    return {
        openaiKey: '{{ $openaiKey }}',
        geminiKey: '{{ $geminiKey }}',
        testing: false,
        testResult: false,
        testSuccess: false,
        testMessage: '',
        
        async testConnection(provider) {
            const apiKey = provider === 'openai' ? this.openaiKey : this.geminiKey;
            const model = provider === 'openai' ? 'gpt-3.5-turbo' : 'gemini-pro';
            
            if (!apiKey) {
                this.showTestResult(false, 'Vui lòng nhập API Key');
                return;
            }
            
            this.testing = true;
            this.testResult = false;
            
            try {
                const response = await fetch('/admin/ai/test', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ provider, api_key: apiKey, model })
                });
                
                const data = await response.json();
                this.showTestResult(data.success, data.message);
            } catch (error) {
                this.showTestResult(false, 'Lỗi: ' + error.message);
            } finally {
                this.testing = false;
            }
        },
        
        showTestResult(success, message) {
            this.testSuccess = success;
            this.testMessage = message;
            this.testResult = true;
            setTimeout(() => this.testResult = false, 5000);
        }
    }
}

async function generateContent() {
    const prompt = document.querySelector('[x-ref="prompt"]').value;
    if (!prompt) return;
    
    this.generating = true;
    this.result = '';
    
    try {
        const response = await fetch('/admin/ai/generate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ prompt })
        });
        
        const data = await response.json();
        this.result = data.content || data.error;
    } catch (error) {
        this.result = 'Lỗi: ' + error.message;
    } finally {
        this.generating = false;
    }
}
</script>
@endsection
