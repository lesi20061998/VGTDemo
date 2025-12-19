@extends('superadmin.layouts.app')

@section('title', 'Cấu hình Project')
@section('page-title', 'Cấu hình chức năng - ' . $project->name)

@section('content')
<div class="mb-6">
    <a href="{{ route('superadmin.projects.index') }}" class="text-purple-600 hover:text-purple-700 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Quay lại Dự án
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-xl font-bold">{{ $project->name }}</h3>
            <p class="text-gray-600">{{ $project->code }}</p>
        </div>
        <span class="px-3 py-1 text-sm font-semibold rounded-full 
            {{ $project->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
            {{ ucfirst($project->status) }}
        </span>
    </div>
    
    @if($remoteStats)
    <div class="border-t pt-4 mt-4">
        <h4 class="font-semibold text-gray-700 mb-3">Thống kê Remote Server</h4>
        <div class="grid grid-cols-4 gap-3">
            <div class="bg-blue-50 p-3 rounded-lg">
                <p class="text-sm text-gray-600">Users</p>
                <p class="text-2xl font-bold text-blue-600">{{ $remoteStats['users'] ?? 0 }}</p>
            </div>
            <div class="bg-green-50 p-3 rounded-lg">
                <p class="text-sm text-gray-600">Products</p>
                <p class="text-2xl font-bold text-green-600">{{ $remoteStats['products'] ?? 0 }}</p>
            </div>
            <div class="bg-purple-50 p-3 rounded-lg">
                <p class="text-sm text-gray-600">Orders</p>
                <p class="text-2xl font-bold text-purple-600">{{ $remoteStats['orders'] ?? 0 }}</p>
            </div>
            <div class="bg-orange-50 p-3 rounded-lg">
                <p class="text-sm text-gray-600">Posts</p>
                <p class="text-2xl font-bold text-orange-600">{{ $remoteStats['posts'] ?? 0 }}</p>
            </div>
        </div>
    </div>
    @endif
    
    <div class="border-t pt-4 mt-4">
        <h4 class="font-semibold text-gray-700 mb-3">Thông tin Truy cập</h4>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="flex-1">
                    <p class="text-sm text-blue-800 font-medium mb-2">Hướng dẫn đăng nhập:</p>
                    <ol class="text-sm text-blue-700 space-y-1 list-decimal list-inside">
                        <li>Truy cập Login URL bên dưới</li>
                        <li>Đăng nhập với Username và Mật khẩu ở phần "Thông tin tài khoản"</li>
                        <li>Sau khi đăng nhập thành công sẽ vào Admin Panel</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 gap-3">
            @if($project->api_token)
            <div class="border rounded-lg p-3 bg-yellow-50">
                <label class="text-sm font-medium text-gray-700">API Token (Remote Control):</label>
                <div class="flex items-center gap-2 mt-1">
                    <code class="flex-1 text-xs bg-gray-900 text-green-400 p-2 rounded font-mono break-all">{{ $project->api_token }}</code>
                    <button onclick="copyToClipboard('{{ $project->api_token }}')" 
                            class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-xs">
                        Copy
                    </button>
                </div>
                <p class="text-xs text-gray-600 mt-2">Sử dụng token này để SuperAdmin control project từ xa</p>
            </div>
            @endif
            <div class="border rounded-lg p-3">
                <label class="text-sm font-medium text-gray-700">Login URL:</label>
                <div class="flex items-center gap-2 mt-1">
                    <a href="{{ route('project.login', $project->code) }}" target="_blank" 
                       class="flex-1 text-blue-600 hover:text-blue-700 font-mono text-sm break-all">
                        {{ route('project.login', $project->code) }}
                    </a>
                    <button onclick="copyToClipboard('{{ route('project.login', $project->code) }}')" 
                            class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-xs">
                        Copy
                    </button>
                </div>
            </div>
            <div class="border rounded-lg p-3 bg-gray-50">
                <label class="text-sm font-medium text-gray-700">Admin Panel (sau khi đăng nhập):</label>
                <p class="text-gray-600 font-mono text-sm mt-1 break-all">
                    {{ route('project.admin.dashboard', $project->code) }}
                </p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Cột trái: Thông tin tài khoản -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-bold mb-4">Thông tin tài khoản</h3>
        
        @if($users->isNotEmpty())
        <div class="space-y-3">
            @foreach($users as $user)
            <div class="border rounded-lg p-4 hover:border-purple-200 transition-colors">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex-1">
                        <h5 class="font-semibold text-gray-900">{{ $user->name }}</h5>
                        <p class="text-sm text-gray-600">{{ $user->email }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                        {{ ucfirst($user->role ?? 'user') }}
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <div>
                        <span class="text-gray-500">Username:</span>
                        <p class="font-mono text-gray-900">{{ $user->username }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Mật khẩu:</span>
                        @if($project->project_admin_password && $user->username == $project->code)
                            <p class="font-mono text-purple-600 font-semibold">{{ $project->project_admin_password }}</p>
                        @else
                            <p class="text-gray-400">***</p>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-gray-500 text-center py-8">Chưa có tài khoản nào</p>
        @endif
    </div>

    <!-- Cột phải: Tabs -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <!-- Tab Navigation -->
        <div class="flex border-b mb-4">
            <button class="tab-button active px-4 py-2 border-b-2 border-purple-600 text-purple-600 font-semibold flex items-center gap-2" onclick="showTab('config')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Cấu hình CMS
            </button>
            <button class="tab-button px-4 py-2 border-b-2 border-transparent text-gray-500 hover:text-gray-700 flex items-center gap-2" onclick="showTab('history')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Lịch sử chỉnh sửa
            </button>
        </div>
        
        <!-- Config Tab -->
        <div id="config-tab" class="tab-content">
            <form method="POST" action="{{ route('superadmin.projects.config', $project) }}">
                @csrf
                
                <div class="space-y-3 max-h-[600px] overflow-y-auto pr-2">
                    @foreach($systemModules as $module)
                    <div class="border rounded-lg p-4 hover:border-purple-300 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h5 class="font-semibold text-gray-800 mb-1">{{ $module['title'] }}</h5>
                                <p class="text-sm text-gray-500">{{ $module['description'] }}</p>
                            </div>
                            <label class="toggle-switch ml-4">
                                <input type="checkbox" name="settings[{{ $module['key'] }}]" value="1" 
                                    {{ isset($settings[$module['key']]) && $settings[$module['key']] == '1' ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="border-t pt-4 mt-4">
                    <label class="flex items-center gap-3 p-4 bg-blue-50 border border-blue-200 rounded-lg cursor-pointer hover:bg-blue-100 transition-colors">
                        <input type="checkbox" name="sync_data" value="1" class="w-5 h-5 text-blue-600 rounded">
                        <div class="flex-1">
                            <span class="font-semibold text-blue-900">Đồng bộ dữ liệu từ Main DB</span>
                            <p class="text-sm text-blue-700 mt-1">Copy settings, menus, widgets, posts, categories, brands từ database chính sang project database</p>
                        </div>
                    </label>
                </div>
                
                <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
                    <a href="{{ route('superadmin.projects.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Hủy</a>
                    <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">Lưu cấu hình</button>
                </div>
            </form>
        </div>
        
        <!-- History Tab -->
        <div id="history-tab" class="tab-content hidden">
            <div class="mb-4 flex gap-2">
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2" onclick="refreshHistory()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh
                </button>
                <a href="/superadmin/debug-history" target="_blank" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Debug
                </a>
                <div class="relative">
                    <button onclick="toggleExportMenu()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export
                    </button>
                    <div id="export-menu" class="hidden absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border z-10">
                        <div class="p-2">
                            <a href="{{ route('superadmin.projects.export-viewer', $project) }}" target="_blank" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded font-medium">
                                👁️ Export Viewer
                            </a>
                            <div class="border-t my-1"></div>
                            <a href="{{ route('superadmin.projects.export-config', $project) }}" target="_blank" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">
                                📄 View JSON
                            </a>
                            <a href="{{ route('superadmin.projects.export-config', $project) }}?format=download" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">
                                💾 Download JSON
                            </a>
                            <a href="{{ route('superadmin.projects.export-config', $project) }}?include_eval=1" target="_blank" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">
                                🔍 With Eval Detection
                            </a>
                            <a href="{{ route('superadmin.projects.export-config', $project) }}?include_eval=1&format=download" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">
                                🔍💾 Download with Eval
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div id="history-content">
                <div class="text-center py-8">
                    <div class="spinner-border" role="status"></div>
                    <p class="text-gray-500 mt-2">Đang tải lịch sử...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.toggle-switch {
    position: relative;
    display: inline-block;
    width: 48px;
    height: 24px;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #cbd5e1;
    transition: .3s;
    border-radius: 24px;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .3s;
    border-radius: 50%;
}

input:checked + .toggle-slider {
    background-color: #7c3aed;
}

input:checked + .toggle-slider:before {
    transform: translateX(24px);
}

.toggle-slider:hover {
    background-color: #94a3b8;
}

input:checked + .toggle-slider:hover {
    background-color: #6d28d9;
}
</style>

<script>
// Utility functions
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Đã copy link!');
    });
}

function showNotification(message, type = 'info') {
    const existing = document.querySelectorAll('.notification-toast');
    existing.forEach(n => n.remove());
    
    const colors = {
        success: 'bg-green-100 border-green-200 text-green-800',
        error: 'bg-red-100 border-red-200 text-red-800',
        warning: 'bg-yellow-100 border-yellow-200 text-yellow-800',
        info: 'bg-blue-100 border-blue-200 text-blue-800'
    };
    
    const notification = document.createElement('div');
    notification.className = `notification-toast fixed top-4 right-4 ${colors[type]} border rounded-lg p-4 shadow-lg z-50 max-w-sm`;
    notification.innerHTML = `
        <div class="flex items-start">
            <div class="flex-1 text-sm font-medium">${message}</div>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-gray-400 hover:text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

function showProcessingStatus(step, message, progress = null) {
    const statusHtml = `
        <div class="text-center py-8">
            <div class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-100 to-purple-100 text-blue-800 rounded-lg mb-4 shadow-sm">
                <svg class="animate-spin -ml-1 mr-3 h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <div class="text-left">
                    <div class="font-semibold">Bước ${step}: ${message}</div>
                    ${progress ? `<div class="text-xs mt-1 opacity-75">${progress}</div>` : ''}
                </div>
            </div>
            <div class="space-y-2 text-sm text-gray-600">
                <div class="flex items-center justify-center space-x-4">
                    <div class="flex items-center ${step >= 1 ? 'text-green-600' : 'text-gray-400'}">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Kết nối API
                    </div>
                    <div class="flex items-center ${step >= 2 ? 'text-green-600' : 'text-gray-400'}">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Nhận dữ liệu
                    </div>
                    <div class="flex items-center ${step >= 3 ? 'text-green-600' : 'text-gray-400'}">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Xử lý & hiển thị
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('history-content').innerHTML = statusHtml;
}

function getMethodColor(method) {
    const colors = {
        'GET': 'bg-blue-100 text-blue-800',
        'POST': 'bg-green-100 text-green-800',
        'PUT': 'bg-yellow-100 text-yellow-800',
        'PATCH': 'bg-orange-100 text-orange-800',
        'DELETE': 'bg-red-100 text-red-800'
    };
    return colors[method] || 'bg-gray-100 text-gray-800';
}

function getTimeAgo(date) {
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);
    
    if (diffInSeconds < 60) return 'Vừa xong';
    if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + ' phút trước';
    if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + ' giờ trước';
    if (diffInSeconds < 2592000) return Math.floor(diffInSeconds / 86400) + ' ngày trước';
    
    return date.toLocaleDateString('vi-VN');
}

// Tab management
function showTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active', 'border-purple-600', 'text-purple-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    
    document.getElementById(tabName + '-tab').classList.remove('hidden');
    
    event.target.classList.add('active', 'border-purple-600', 'text-purple-600');
    event.target.classList.remove('border-transparent', 'text-gray-500');
    
    if (tabName === 'history') {
        loadHistory();
    }
}

// History management
function loadHistory() {
    console.log('Loading history for project: {{ $project->code }}');
    
    showProcessingStatus(1, 'Khởi tạo kết nối', 'Đang quét file log: storage/logs/file-changes-{{ $project->code }}.log');
    
    fetch('/superadmin/file-monitor?project={{ $project->code }}', {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        showProcessingStatus(2, 'Nhận dữ liệu thành công', `API Status: ${response.status} - Đang parse JSON response...`);
        return response.json();
    })
    .then(data => {
        console.log('History data:', data);
        showProcessingStatus(3, 'Xử lý dữ liệu', `Tổng số logs: ${data.total || (data.logs ? data.logs.length : 0)} - Đang format hiển thị...`);
        
        const logs = data.logs || data || [];
        console.log('Processed logs:', logs);
        
        setTimeout(() => {
            if (logs && logs.length > 0) {
                displayLogs(logs);
            } else {
                showEmptyState();
            }
        }, 800);
    })
    .catch(error => {
        console.error('Error loading history:', error);
        showErrorState(error);
    });
}

function displayLogs(logs) {
    let historyHtml = '<div class="space-y-3 max-h-[500px] overflow-y-auto">';
    
    logs.forEach(log => {
        const date = new Date(log.timestamp);
        const timeAgo = getTimeAgo(date);
        
        historyHtml += `
            <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex-1">
                        <h5 class="font-semibold text-gray-900">${log.action || 'Thay đổi'}</h5>
                        <p class="text-sm text-gray-600">${log.route || log.url}</p>
                    </div>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full ${getMethodColor(log.method)}">
                        ${log.method}
                    </span>
                </div>
                <div class="flex items-center justify-between text-sm text-gray-500">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        ${log.user_name} (${log.user_email})
                    </span>
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        ${timeAgo}
                    </span>
                </div>
                ${log.data_summary && Object.keys(log.data_summary).length > 0 ? `
                    <div class="mt-3 p-2 bg-gray-100 rounded text-xs">
                        <strong>Dữ liệu:</strong> ${JSON.stringify(log.data_summary, null, 2).substring(0, 200)}...
                    </div>
                ` : ''}
            </div>
        `;
    });
    
    historyHtml += '</div>';
    
    const summaryHtml = `
        <div class="mb-6 grid grid-cols-3 gap-4">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-blue-900">Tổng số logs</p>
                        <p class="text-2xl font-bold text-blue-600">${logs.length}</p>
                    </div>
                </div>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-green-900">Log mới nhất</p>
                        <p class="text-sm font-bold text-green-600">${getTimeAgo(new Date(logs[0].timestamp))}</p>
                    </div>
                </div>
            </div>
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-purple-900">Project</p>
                        <p class="text-sm font-bold text-purple-600">{{ $project->code }}</p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('history-content').innerHTML = summaryHtml + historyHtml;
    showNotification('✅ Đã tải thành công ' + logs.length + ' log entries', 'success');
}

function showEmptyState() {
    document.getElementById('history-content').innerHTML = `
        <div class="text-center py-12">
            <div class="mb-4">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Chưa có lịch sử chỉnh sửa</h3>
            <p class="text-sm text-gray-500 mb-4">Các thay đổi sẽ được ghi lại tự động khi bạn thực hiện các hành động.</p>
            
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 max-w-md mx-auto">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-400 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="text-left">
                        <h4 class="text-sm font-medium text-yellow-800">Để tạo log mẫu:</h4>
                        <ul class="mt-2 text-xs text-yellow-700 space-y-1">
                            <li>• Thực hiện thay đổi cấu hình</li>
                            <li>• Tạo/sửa sản phẩm, bài viết</li>
                            <li>• Hoặc <a href="/superadmin/test-logging" target="_blank" class="underline">test logging</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    showNotification('ℹ️ Không tìm thấy log nào cho project này', 'info');
}

function showErrorState(error) {
    document.getElementById('history-content').innerHTML = `
        <div class="text-center py-8">
            <div class="bg-red-50 border border-red-200 rounded-lg p-6 max-w-md mx-auto">
                <div class="text-red-600 mb-4">
                    <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-red-900 mb-2">Lỗi tải lịch sử</h3>
                <p class="text-red-700 mb-4">${error.message}</p>
                
                <div class="flex gap-2">
                    <button onclick="loadHistory()" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
                        🔄 Thử lại
                    </button>
                    <a href="/superadmin/debug-history" target="_blank" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 text-sm">
                        🔧 Debug
                    </a>
                </div>
            </div>
        </div>
    `;
    
    showNotification('❌ Lỗi tải lịch sử: ' + error.message, 'error');
}

function refreshHistory() {
    showNotification('🔄 Đang refresh lịch sử...', 'info');
    loadHistory();
}

// Export menu management
function toggleExportMenu() {
    const menu = document.getElementById('export-menu');
    menu.classList.toggle('hidden');
}

// Close export menu when clicking outside
document.addEventListener('click', function(event) {
    const menu = document.getElementById('export-menu');
    const button = event.target.closest('button');
    
    if (!button || !button.onclick || button.onclick.toString().indexOf('toggleExportMenu') === -1) {
        menu.classList.add('hidden');
    }
});
</script>
@endsection