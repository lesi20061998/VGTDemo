<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test Media Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">Test Media Manager</h1>
        
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">Chọn hình ảnh</h2>
            
            <p class="mb-4 text-sm text-gray-600">User: {{ auth()->user()->name ?? 'Guest' }} | Tenant: {{ auth()->user()->tenant_id ?? 'N/A' }}</p>
            
            <x-media-manager>
                Chọn từ thư viện
            </x-media-manager>
            
            <div class="mt-6">
                <h3 class="font-semibold mb-2">Kết quả chọn:</h3>
                <pre id="result" class="bg-gray-100 p-4 rounded text-sm overflow-auto max-h-96">Chưa chọn ảnh nào</pre>
            </div>
            
            <div class="mt-4">
                <h3 class="font-semibold mb-2">Debug Console:</h3>
                <pre id="debug" class="bg-red-50 p-4 rounded text-sm overflow-auto max-h-96 text-red-600"></pre>
            </div>
        </div>
    </div>

    <script>
        // Debug console
        const originalConsoleError = console.error;
        console.error = function(...args) {
            document.getElementById('debug').textContent += args.join(' ') + '\n';
            originalConsoleError.apply(console, args);
        };
        
        window.addEventListener('media-selected', (event) => {
            document.getElementById('result').textContent = JSON.stringify(event.detail, null, 2);
        });
        
        // Test API
        fetch('/admin/media/list')
            .then(r => r.json())
            .then(data => console.log('API Response:', data))
            .catch(e => console.error('API Error:', e));
    </script>
</body>
</html>
