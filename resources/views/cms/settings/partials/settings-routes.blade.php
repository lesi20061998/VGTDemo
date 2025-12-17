@php
    $projectCode = request()->route('projectCode');
    
    // Fallback to currentProject if available
    if (!$projectCode && isset($currentProject) && $currentProject) {
        $projectCode = $currentProject->code;
    }
    
    // Last fallback - get from URL segment
    if (!$projectCode) {
        $segment = request()->segment(1);
        if ($segment && $segment !== 'admin' && $segment !== 'cms') {
            $projectCode = $segment;
        }
    }
    
    if ($projectCode) {
        // Project routes: /{projectCode}/admin/settings
        $settingsBackUrl = route('project.admin.settings.index', ['projectCode' => $projectCode]);
        $settingsSaveUrl = route('project.admin.settings.save', ['projectCode' => $projectCode]);
    } else {
        // Fallback - should not happen in multisite mode
        $settingsBackUrl = url('/admin/settings');
        $settingsSaveUrl = url('/admin/settings/save');
    }
@endphp
