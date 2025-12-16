@extends('cms.settings.template', ['title' => 'Cấu hình Email SMTP'])

@section('form-content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-lg border border-blue-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
            Cấu hình SMTP Server
        </h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Host *</label>
                <input type="text" name="mail_host" value="{{ old('mail_host', setting('mail_host')) }}" placeholder="smtp.gmail.com" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Ví dụ: smtp.gmail.com, smtp.mailtrap.io</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Port *</label>
                <input type="number" name="mail_port" value="{{ old('mail_port', setting('mail_port', 587)) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">TLS: 587, SSL: 465</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Encryption *</label>
                <select name="mail_encryption" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="tls" {{ setting('mail_encryption', 'tls') == 'tls' ? 'selected' : '' }}>TLS (Khuyến nghị)</option>
                    <option value="ssl" {{ setting('mail_encryption') == 'ssl' ? 'selected' : '' }}>SSL</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Username *</label>
                <input type="text" name="mail_username" value="{{ old('mail_username', setting('mail_username')) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                <input type="password" name="mail_password" value="{{ old('mail_password', setting('mail_password')) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Gmail: Sử dụng App Password thay vì mật khẩu thường</p>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-6 rounded-lg border border-green-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
            </svg>
            Thông tin người gửi
        </h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email gửi *</label>
                <input type="email" name="mail_from_address" value="{{ old('mail_from_address', setting('mail_from_address')) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tên người gửi *</label>
                <input type="text" name="mail_from_name" value="{{ old('mail_from_name', setting('mail_from_name')) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
    </div>

    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex">
            <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"></path>
            </svg>
            <div class="text-sm text-yellow-800">
                <p class="font-medium">Lưu ý:</p>
                <ul class="list-disc list-inside mt-1 space-y-1">
                    <li>Gmail: Bật xác thực 2 bước và tạo App Password</li>
                    <li>Sau khi lưu, hãy gửi email thử để kiểm tra</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
