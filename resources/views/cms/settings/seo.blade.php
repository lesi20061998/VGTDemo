@extends('cms.settings.template', ['title' => 'Cấu hình SEO'])

@section('form-content')
<div class="space-y-6">
    <div>
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Meta Tags mặc định</h3>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                <input type="text" name="seo_meta_title" value="{{ old('seo_meta_title', setting('seo_meta_title')) }}" maxlength="60" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Tối đa 60 ký tự</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                <textarea name="seo_meta_description" rows="3" maxlength="160" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('seo_meta_description', setting('seo_meta_description')) }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Tối đa 160 ký tự</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Meta Keywords</label>
                <input type="text" name="seo_meta_keywords" value="{{ old('seo_meta_keywords', setting('seo_meta_keywords')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Phân cách bằng dấu phẩy</p>
            </div>
        </div>
    </div>

    <div>
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Google Analytics</h3>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Google Analytics ID</label>
            <input type="text" name="google_analytics_id" value="{{ old('google_analytics_id', setting('google_analytics_id')) }}" placeholder="G-XXXXXXXXXX" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
    </div>

    <div>
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Google Search Console</h3>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Verification Code</label>
            <input type="text" name="google_site_verification" value="{{ old('google_site_verification', setting('google_site_verification')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
    </div>

    <div>
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Bing Webmaster Tools</h3>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Verification Code</label>
            <input type="text" name="bing_site_verification" value="{{ old('bing_site_verification', setting('bing_site_verification')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
    </div>

    <div>
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Robots.txt</h3>
        <div>
            <textarea name="robots_txt" rows="10" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono text-sm">{{ old('robots_txt', setting('robots_txt', "User-agent: *\nDisallow: /admin/\nSitemap: " . url('/sitemap.xml'))) }}</textarea>
        </div>
    </div>
    
    <div class="border-t pt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Custom Code</h3>
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Header Code</label>
                <p class="text-xs text-gray-500 mb-2">Code sẽ được thêm vào &lt;head&gt;</p>
                <div id="headerEditor" style="height: 200px; border: 1px solid #d1d5db; border-radius: 0.5rem;"></div>
                <textarea name="custom_header_code" id="headerCode" class="hidden">{{ old('custom_header_code', setting('custom_header_code')) }}</textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Body Code</label>
                <p class="text-xs text-gray-500 mb-2">Code sẽ được thêm sau thẻ &lt;body&gt;</p>
                <div id="bodyEditor" style="height: 200px; border: 1px solid #d1d5db; border-radius: 0.5rem;"></div>
                <textarea name="custom_body_code" id="bodyCode" class="hidden">{{ old('custom_body_code', setting('custom_body_code')) }}</textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Footer Code</label>
                <p class="text-xs text-gray-500 mb-2">Code sẽ được thêm trước &lt;/body&gt;</p>
                <div id="footerEditor" style="height: 200px; border: 1px solid #d1d5db; border-radius: 0.5rem;"></div>
                <textarea name="custom_footer_code" id="footerCode" class="hidden">{{ old('custom_footer_code', setting('custom_footer_code')) }}</textarea>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.45.0/min/vs/editor/editor.main.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.45.0/min/vs/loader.min.js"></script>
<script>
require.config({ paths: { vs: 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.45.0/min/vs' }});
require(['vs/editor/editor.main'], function() {
    const headerEditor = monaco.editor.create(document.getElementById('headerEditor'), {
        value: document.getElementById('headerCode').value,
        language: 'html',
        theme: 'vs-dark',
        minimap: { enabled: false },
        automaticLayout: true
    });
    
    const bodyEditor = monaco.editor.create(document.getElementById('bodyEditor'), {
        value: document.getElementById('bodyCode').value,
        language: 'html',
        theme: 'vs-dark',
        minimap: { enabled: false },
        automaticLayout: true
    });
    
    const footerEditor = monaco.editor.create(document.getElementById('footerEditor'), {
        value: document.getElementById('footerCode').value,
        language: 'html',
        theme: 'vs-dark',
        minimap: { enabled: false },
        automaticLayout: true
    });
    
    document.querySelector('form').addEventListener('submit', function() {
        document.getElementById('headerCode').value = headerEditor.getValue();
        document.getElementById('bodyCode').value = bodyEditor.getValue();
        document.getElementById('footerCode').value = footerEditor.getValue();
    });
});
</script>
@endsection
