<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Media Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50">
    @include('cms.components.media-manager')
    
    <script>
        document.addEventListener('alpine:init', () => {
            setTimeout(() => {
                const button = document.querySelector('[x-data] button');
                if (button) button.click();
            }, 100);
        });
    </script>
</body>
</html>
