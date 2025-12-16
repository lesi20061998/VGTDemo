@extends('cms.settings.template', ['title' => 'Thông tin liên hệ'])

@section('form-content')
<div class="space-y-6">
    <div class="bg-white p-6 rounded-lg border">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Thông tin liên hệ</h3>
        <p class="text-sm text-gray-600 mb-4">Thông tin liên hệ đến chủ website email, số điện thoại, địa chỉ</p>
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <p class="text-xs text-gray-500 mb-2">Email liên hệ dùng để nhận mail</p>
                <input type="email" name="contact_email" value="{{ old('contact_email', setting('contact_email')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Điện Thoại</label>
                <p class="text-xs text-gray-500 mb-2">Số điện thoại chăm sóc khách hàng, hotline tư vấn...</p>
                <input type="text" name="contact_phone" value="{{ old('contact_phone', setting('contact_phone')) }}" placeholder="081.606.1512" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
                <p class="text-xs text-gray-500 mb-2">Địa chỉ đầy đủ của công ty, shop</p>
                <input type="text" name="contact_address" value="{{ old('contact_address', setting('contact_address')) }}" placeholder="30 Hàm Nghị, Phường Nguyễn Thái Bình, Quận 1, TP. Hồ Chí Minh" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Số Zalo</label>
                <input type="text" name="contact_zalo" value="{{ old('contact_zalo', setting('contact_zalo')) }}" placeholder="081.606.1512" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Facebook Messenger</label>
                <p class="text-xs text-gray-500 mb-2">Link chat Facebook Messenger</p>
                <input type="url" name="contact_facebook_message_id" value="{{ old('contact_facebook_message_id', setting('contact_facebook_message_id')) }}" placeholder="https://m.me/..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
    </div>
</div>
@endsection
