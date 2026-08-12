@extends('cms.settings.template', ['title' => 'Cấu hình SEO'])

@section('form-content')
<div class="space-y-6">
  <div>
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Meta Tags mặc định</h3>
    <div class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
        <input type="text" name="seo_meta_title" value="{{ old('seo_meta_title', setting_string('seo_meta_title')) }}" maxlength="60" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        <p class="text-xs text-gray-500 mt-1">Tối đa 60 ký tự</p>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
        <textarea name="seo_meta_description" rows="3" maxlength="160" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('seo_meta_description', setting_string('seo_meta_description')) }}</textarea>
        <p class="text-xs text-gray-500 mt-1">Tối đa 160 ký tự</p>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Meta Keywords</label>
        <input type="text" name="seo_meta_keywords" value="{{ old('seo_meta_keywords', setting_string('seo_meta_keywords')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        <p class="text-xs text-gray-500 mt-1">Phân cách bằng dấu phẩy</p>
      </div>
    </div>
  </div>

  <div>
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Google Analytics</h3>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Google Analytics ID</label>
      <input type="text" name="google_analytics_id" value="{{ old('google_analytics_id', setting_string('google_analytics_id')) }}" placeholder="G-XXXXXXXXXX" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
    </div>
  </div>

  <div>
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Google Search Console</h3>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Verification Code</label>
      <input type="text" name="google_site_verification" value="{{ old('google_site_verification', setting_string('google_site_verification')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
    </div>
  </div>

  <div>
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Bing Webmaster Tools</h3>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Verification Code</label>
      <input type="text" name="bing_site_verification" value="{{ old('bing_site_verification', setting_string('bing_site_verification')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
    </div>
  </div>

  <div>
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Robots.txt</h3>
    <div>
      <textarea name="robots_txt" rows="10" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono text-sm">{{ old('robots_txt', setting_string('robots_txt', "User-agent: *\nDisallow: /admin/\nSitemap: " . url('/sitemap.xml'))) }}</textarea>
    </div>
  </div>
  
  <div class="border-t pt-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Custom Code</h3>
    <p class="text-sm text-gray-600 mb-6">Thêm code tùy chỉnh vào website của bạn (Google Analytics, Facebook Pixel, Live Chat, v.v.)</p>
    
    <div class="space-y-6" x-data="{ 
      showHeaderPreview: false, 
      showBodyPreview: false, 
      showFooterPreview: false 
    }">
      <div>
        <div class="flex items-center justify-between mb-2">
          <label class="block text-sm font-medium text-gray-700">
            Header Code
            <span class="text-xs text-gray-500 font-normal ml-2">(Thêm vào &lt;head&gt;)</span>
          </label>
          <button type="button" @click="showHeaderPreview = !showHeaderPreview" class="text-xs text-blue-600 hover:text-blue-700">
            <span x-show="!showHeaderPreview"> Xem preview</span>
            <span x-show="showHeaderPreview"> Ẩn preview</span>
          </button>
        </div>
        <textarea 
          name="custom_header_code" 
          rows="8" 
          placeholder="<!-- Google Analytics, Meta Pixel, v.v. -->
<script>
 // Your code here
</script>"
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono text-sm bg-gray-50"
          spellcheck="false">{{ old('custom_header_code', setting_string('custom_header_code')) }}</textarea>
        
        <div x-show="showHeaderPreview" x-transition class="mt-2 p-4 bg-gray-800 text-green-400 rounded-lg font-mono text-xs overflow-x-auto">
          <pre x-text="$el.previousElementSibling.value || 'Chưa có code'"></pre>
        </div>
      </div>
      
      <div>
        <div class="flex items-center justify-between mb-2">
          <label class="block text-sm font-medium text-gray-700">
            Body Code
            <span class="text-xs text-gray-500 font-normal ml-2">(Thêm sau &lt;body&gt;)</span>
          </label>
          <button type="button" @click="showBodyPreview = !showBodyPreview" class="text-xs text-blue-600 hover:text-blue-700">
            <span x-show="!showBodyPreview"> Xem preview</span>
            <span x-show="showBodyPreview"> Ẩn preview</span>
          </button>
        </div>
        <textarea 
          name="custom_body_code" 
          rows="8" 
          placeholder="<!-- Google Tag Manager (noscript), v.v. -->
<noscript>
 <!-- Your code here -->
</noscript>"
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono text-sm bg-gray-50"
          spellcheck="false">{{ old('custom_body_code', setting_string('custom_body_code')) }}</textarea>
        
        <div x-show="showBodyPreview" x-transition class="mt-2 p-4 bg-gray-800 text-green-400 rounded-lg font-mono text-xs overflow-x-auto">
          <pre x-text="$el.previousElementSibling.value || 'Chưa có code'"></pre>
        </div>
      </div>
      
      <div>
        <div class="flex items-center justify-between mb-2">
          <label class="block text-sm font-medium text-gray-700">
            Footer Code
            <span class="text-xs text-gray-500 font-normal ml-2">(Thêm trước &lt;/body&gt;)</span>
          </label>
          <button type="button" @click="showFooterPreview = !showFooterPreview" class="text-xs text-blue-600 hover:text-blue-700">
            <span x-show="!showFooterPreview"> Xem preview</span>
            <span x-show="showFooterPreview"> Ẩn preview</span>
          </button>
        </div>
        <textarea 
          name="custom_footer_code" 
          rows="8" 
          placeholder="<!-- Live Chat, Popup scripts, v.v. -->
<script>
 // Your code here
</script>"
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono text-sm bg-gray-50"
          spellcheck="false">{{ old('custom_footer_code', setting_string('custom_footer_code')) }}</textarea>
        
        <div x-show="showFooterPreview" x-transition class="mt-2 p-4 bg-gray-800 text-green-400 rounded-lg font-mono text-xs overflow-x-auto">
          <pre x-text="$el.previousElementSibling.value || 'Chưa có code'"></pre>
        </div>
      </div>
      
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start gap-3">
          <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <div class="text-sm text-blue-800">
            <p class="font-medium mb-1">Lưu ý quan trọng:</p>
            <ul class="list-disc list-inside space-y-1 text-xs">
              <li>Code sẽ được chạy trên mọi trang của website</li>
              <li>Hãy chắc chắn code của bạn đã được kiểm tra kỹ</li>
              <li>Code lỗi có thể làm website không hoạt động</li>
              <li>Nên sao lưu trước khi thêm code mới</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
