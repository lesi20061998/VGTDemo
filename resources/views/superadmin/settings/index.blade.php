@extends('superadmin.layouts.app')

@section('title', 'Cài đặt hệ thống')
@section('page-title', 'Cài đặt hệ thống')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form id="settingsForm" onsubmit="saveSettings(event)">
            <div class="space-y-4" id="settingsContainer"></div>
            
            <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">Lưu cấu hình</button>
            </div>
        </form>
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
const settingsConfig = [
    { key: 'system_maintenance', label: 'Chế độ bảo trì hệ thống', description: 'Tạm khóa toàn bộ hệ thống để bảo trì' },
    { key: 'allow_registration', label: 'Cho phép đăng ký mới', description: 'Cho phép tạo tài khoản mới trong hệ thống' },
    { key: 'enable_email_notifications', label: 'Thông báo qua Email', description: 'Gửi thông báo tự động qua email' },
    { key: 'enable_activity_logs', label: 'Ghi log hoạt động', description: 'Theo dõi và lưu lại các hoạt động trong hệ thống' },
    { key: 'enable_auto_backup', label: 'Sao lưu tự động', description: 'Tự động sao lưu dữ liệu định kỳ' },
    { key: 'enable_2fa', label: 'Xác thực 2 yếu tố', description: 'Bắt buộc xác thực 2 yếu tố cho superadmin' },
    { key: 'enable_api_access', label: 'Truy cập API', description: 'Cho phép truy cập qua API' },
    { key: 'enable_debug_mode', label: 'Chế độ Debug', description: 'Hiển thị thông tin debug (chỉ dùng khi phát triển)' },
];

function loadSettings() {
    fetch('/superadmin/admin/settings/data')
        .then(res => res.json())
        .then(data => {
            renderSettings(data.settings || {});
        });
}

function renderSettings(settings) {
    const container = document.getElementById('settingsContainer');
    container.innerHTML = '';
    
    settingsConfig.forEach(setting => {
        const isEnabled = settings[setting.key] === '1' || settings[setting.key] === true;
        
        container.innerHTML += `
            <div class="border rounded-lg p-4 hover:border-purple-300 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <h5 class="font-semibold text-gray-800 mb-1">${setting.label}</h5>
                        <p class="text-sm text-gray-500">${setting.description}</p>
                    </div>
                    <label class="toggle-switch ml-4">
                        <input type="checkbox" name="settings[${setting.key}]" value="1" ${isEnabled ? 'checked' : ''}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
        `;
    });
}

function saveSettings(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    
    fetch('/superadmin/admin/settings', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(res => res.json())
    .then(data => {
        alert('Cập nhật thành công!');
    });
}

loadSettings();
</script>
@endsection
