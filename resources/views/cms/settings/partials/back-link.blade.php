@php
    $projectCode = request()->route('projectCode');
    
    if ($projectCode) {
        $backUrl = route('project.admin.settings.index', ['projectCode' => $projectCode]);
    } else {
        $currentUrl = request()->url();
        $baseAdminUrl = str_contains($currentUrl, '/cms/admin') ? '/cms/admin' : '/admin';
        $backUrl = url($baseAdminUrl . '/settings');
    }
@endphp
<div class="mb-6">
    <a href="{{ $backUrl }}" class="text-sm text-gray-600 hover:text-gray-900">← Quay lại</a>
</div>
