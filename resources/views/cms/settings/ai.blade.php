@extends('cms.layouts.app')

@section('title', 'Cấu hình AI')
@section('page-title', 'AI Content Generator')

@section('content')
@include('cms.settings.partials.back-link')

@php
  $projectCode = request()->route('projectCode') ?? request()->segment(1);
  $settingsSaveUrl = $projectCode ? route('project.admin.settings.save', ['projectCode' => $projectCode]) : url('/admin/settings/save');
@endphp

<div class="bg-white rounded-lg shadow-sm p-6" x-data="aiConfig()"
  <form action="{{ $settingsSaveUrl }}" method="POST">
    @csrf
    
    <div class="space-y-6">
      @php
        $aiSetting = setting('ai', []);
        $ai = is_array($aiSetting) ? $aiSetting : json_decode($aiSetting, true) ?? [];
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
        
        <div class="mt-4 space-y-3">
          <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-start gap-3">
              <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
              </svg>
              <div>
                <h4 class="font-semibold text-blue-900"> Khuyến nghị</h4>
                <p class="text-sm text-blue-800 mt-1">
                  <strong>Dùng Google Gemini</strong> - Miễn phí, đủ tốt cho tạo nội dung bài viết, mô tả sản phẩm.
                  <br>Chỉ dùng OpenAI nếu cần chất lượng cao hơn và sẵn sàng trả phí.
                </p>
              </div>
            </div>
          </div>
          
          <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <div class="flex items-start gap-3">
              <svg class="w-5 h-5 text-yellow-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
              </svg>
              <div>
                <h4 class="font-semibold text-yellow-900">️ Xử lý lỗi thường gặp</h4>
                <div class="text-sm text-yellow-800 mt-1 space-y-2">
                  <div>
                    <strong> API key expired/invalid:</strong>
                    <br>• Tạo API key mới tại: <a href="https://aistudio.google.com/app/apikey" target="_blank" class="text-yellow-700 underline">aistudio.google.com</a>
                    <br>• Xóa API key cũ và tạo mới
                  </div>
                  <div>
                    <strong>️ Quota exceeded:</strong>
                    <br>• Đã dùng hết 15 requests/phút hoặc 1M tokens/ngày
                    <br>• Đợi reset quota (24h) hoặc check: <a href="https://ai.dev/usage" target="_blank" class="text-yellow-700 underline">ai.dev/usage</a>
                  </div>
                  <div>
                    <strong> Model not found:</strong>
                    <br>• Click nút "Models" để xem danh sách model có sẵn
                    <br>• Sử dụng model được suggest
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- OpenAI -->
      <div class="border rounded-lg p-4 bg-gradient-to-br from-orange-50 to-red-50"></div>
        <h3 class="font-semibold mb-3 flex items-center gap-2">
          <img src="https://openai.com/favicon.ico" class="w-6 h-6">
          OpenAI (ChatGPT)
          <span class="text-xs bg-orange-100 text-orange-700 px-2 py-1 rounded-full">TRẢ PHÍ</span>
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
            <div class="mt-2 text-xs text-orange-700 bg-orange-100 rounded p-2">
              <strong>Model:</strong> gpt-3.5-turbo (Chất lượng cao nhưng tốn phí)
              <br><strong>Chi phí:</strong> ~$0.002/1K tokens
            </div>
          </div>
          <input type="hidden" name="ai[openai_model]" value="gpt-3.5-turbo">
        </div>
      </div>

      <!-- Gemini -->
      <div class="border rounded-lg p-4 bg-gradient-to-br from-green-50 to-blue-50">
        <h3 class="font-semibold mb-3 flex items-center gap-2">
          <img src="https://www.gstatic.com/lamda/images/favicon_v1_150160cddff7f294ce30.svg" class="w-6 h-6">
          Google Gemini
          <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">MIỄN PHÍ</span>
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
              <button type="button" @click="loadAvailableModels()" 
                  class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                <span x-show="!loadingModels">Models</span>
                <span x-show="loadingModels">...</span>
              </button>
            </div>
            
            <!-- Model Selection -->
            <div class="mt-3">
              <label class="block text-xs font-medium mb-1">Chọn Model</label>
              <select name="ai[gemini_model]" x-model="selectedModel" 
                  class="w-full px-3 py-2 text-sm border rounded-lg bg-white">
                <optgroup label=" Miễn phí - Đã xác nhận hoạt động">
                  <option value="gemini-1.5-flash">gemini-1.5-flash (Khuyến nghị - Nhanh, miễn phí)</option>
                  <option value="gemini-1.5-flash-latest">gemini-1.5-flash-latest (Tự động cập nhật)</option>
                  <option value="gemini-1.5-pro">gemini-1.5-pro (Chất lượng cao, miễn phí)</option>
                  <option value="gemini-1.5-pro-latest">gemini-1.5-pro-latest (Pro tự động)</option>
                </optgroup>
                <optgroup label="️ Có thể không hoạt động hoặc trả phí" x-show="availableModels.length > 0">
                  <template x-for="model in availableModels.filter(m => !['gemini-1.5-flash', 'gemini-1.5-flash-latest', 'gemini-1.5-pro', 'gemini-1.5-pro-latest'].includes(m.name))" :key="model.name">
                    <option :value="model.name" x-text="`${model.name} - ${model.displayName} (Thử nghiệm)`"></option>
                  </template>
                </optgroup>
              </select>
              <div class="mt-1 p-2 bg-blue-50 border border-blue-200 rounded text-xs">
                <strong> Khuyến nghị:</strong> Chỉ sử dụng models trong nhóm " Miễn phí" để tránh bị tính phí.
                <br>Models khác có thể yêu cầu thanh toán hoặc không hoạt động.
              </div>
            </div>
            
            <div class="flex items-center justify-between mt-2">
              <p class="text-xs text-gray-500">
                Lấy tại: <a href="https://aistudio.google.com/app/apikey" target="_blank" class="text-blue-600">aistudio.google.com</a>
              </p>
              <a href="https://aistudio.google.com/app/apikey" target="_blank" 
                class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded hover:bg-blue-200 transition-colors">
                 Tạo API Key mới
              </a>
            </div>
            
            <div class="mt-2 text-xs text-green-700 bg-green-100 rounded p-2">
              <strong>Model hiện tại:</strong> <span x-text="selectedModel"></span>
              <br><strong>Trạng thái:</strong> 
              <span x-show="['gemini-1.5-flash', 'gemini-1.5-flash-latest', 'gemini-1.5-pro', 'gemini-1.5-pro-latest'].includes(selectedModel)" class="text-green-600"> Miễn phí, đã xác nhận</span>
              <span x-show="!['gemini-1.5-flash', 'gemini-1.5-flash-latest', 'gemini-1.5-pro', 'gemini-1.5-pro-latest'].includes(selectedModel)" class="text-orange-600">️ Có thể trả phí hoặc không hoạt động</span>
              <br><strong>Giới hạn miễn phí:</strong> 15 requests/phút, 1M tokens/ngày
            </div>
          </div>
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
          <div class="bg-blue-50 rounded-lg p-4">
            <p class="text-sm text-gray-600">Chi phí ước tính</p>
            <p class="text-2xl font-bold text-blue-600">${{ number_format(($ai['stats']['estimated_cost'] ?? 0), 2) }}</p>
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
        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2">
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
    availableModels: [],
    selectedModel: '{{ $ai['gemini_model'] ?? 'gemini-1.5-flash' }}',
    loadingModels: false,
    
    async testConnection(provider) {
      const apiKey = provider === 'openai' ? this.openaiKey : this.geminiKey;
      const model = provider === 'openai' ? 'gpt-3.5-turbo' : (document.querySelector('[name="ai[gemini_model]"]')?.value || 'gemini-1.5-flash');
      
      if (!apiKey) {
        this.showTestResult(false, 'Vui lòng nhập API Key');
        return;
      }
      
      this.testing = true;
      this.testResult = false;
      
      try {
        const baseUrl = '{{ $projectCode ? "/" . $projectCode . "/admin" : "/admin" }}';
        const response = await fetch(`${baseUrl}/ai/test`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify({ provider, api_key: apiKey, model })
        });
        
        // Check if response is HTML (error page)
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('text/html')) {
          throw new Error('Server trả về trang HTML thay vì JSON. Có thể route không tồn tại hoặc có lỗi server.');
        }
        
        if (!response.ok) {
          throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        this.showTestResult(data.success, data.message);
      } catch (error) {
        console.error('Test connection error:', error);
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
    },
    
    async listModels() {
      if (!this.geminiKey) {
        this.showTestResult(false, 'Vui lòng nhập API Key trước');
        return;
      }
      
      try {
        const baseUrl = '{{ $projectCode ? "/" . $projectCode . "/admin" : "/admin" }}';
        const response = await fetch(`${baseUrl}/ai/list-models`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify({ api_key: this.geminiKey })
        });
        
        if (!response.ok) {
          throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        if (data.success) {
          let modelList = data.models.map(m => `• ${m.name}`).join('\n');
          this.showTestResult(true, `Available models:\n${modelList}`);
        } else {
          this.showTestResult(false, data.message);
        }
      } catch (error) {
        console.error('List models error:', error);
        this.showTestResult(false, 'Lỗi: ' + error.message);
      }
    },
    
    async loadAvailableModels() {
      if (!this.geminiKey) {
        this.showTestResult(false, 'Vui lòng nhập API Key trước');
        return;
      }
      
      this.loadingModels = true;
      
      try {
        const baseUrl = '{{ $projectCode ? "/" . $projectCode . "/admin" : "/admin" }}';
        const response = await fetch(`${baseUrl}/ai/list-models`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify({ api_key: this.geminiKey })
        });
        
        if (!response.ok) {
          throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        if (data.success) {
          this.availableModels = data.models;
          this.showTestResult(true, `Đã load ${data.models.length} models. Chọn model từ dropdown bên dưới.`);
        } else {
          this.showTestResult(false, data.message);
        }
      } catch (error) {
        console.error('Load models error:', error);
        this.showTestResult(false, 'Lỗi: ' + error.message);
      } finally {
        this.loadingModels = false;
      }
    }
  }
}

async function generateContent() {
  const prompt = document.querySelector('[x-ref="prompt"]').value;
  if (!prompt) return;
  
  this.generating = true;
  this.result = '';
  
  try {
    const baseUrl = '{{ $projectCode ? "/" . $projectCode . "/admin" : "/admin" }}';
    const response = await fetch(`${baseUrl}/ai/generate`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify({ prompt })
    });
    
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }
    
    const data = await response.json();
    this.result = data.content || data.error;
  } catch (error) {
    console.error('Generate content error:', error);
    this.result = 'Lỗi: ' + error.message;
  } finally {
    this.generating = false;
  }
}
</script>
@endsection
