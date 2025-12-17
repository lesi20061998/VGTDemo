@extends('cms.layouts.app')

@section('title', 'Tính năng Frontend')
@section('page-title', 'Cấu hình tính năng Frontend')

@section('content')
@include('cms.settings.partials.back-link')

@php
    $projectCode = request()->route('projectCode') ?? request()->segment(1);
    $settingsSaveUrl = $projectCode ? route('project.admin.settings.save', ['projectCode' => $projectCode]) : url('/admin/settings/save');
@endphp

<div class="max-w-7xl mx-auto" x-data="frontendFeaturesManager()" x-init="init()">
    <!-- Header -->
    <div class="mb-8 bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold mb-2">Tính năng Frontend</h1>
                <p class="text-purple-100">Cấu hình các tính năng hiển thị trên website</p>
            </div>
            <div class="hidden md:block">
                <svg class="w-16 h-16 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>
    </div>

    <form action="{{ $settingsSaveUrl }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Đa ngôn ngữ -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900">Đa ngôn ngữ</h2>
                                <p class="text-gray-600 text-sm">Hiển thị nút chuyển đổi ngôn ngữ</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="multilang[enabled]" value="1" 
                                   x-model="features.multilang.enabled" 
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
                <div class="p-6" x-show="features.multilang.enabled">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Vị trí hiển thị</label>
                            <select name="multilang[position]" x-model="features.multilang.position" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="header">Header</option>
                                <option value="footer">Footer</option>
                                <option value="sidebar">Sidebar</option>
                                <option value="floating">Nút nổi</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kiểu hiển thị</label>
                            <select name="multilang[style]" x-model="features.multilang.style" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="dropdown">Dropdown</option>
                                <option value="flags">Cờ quốc gia</option>
                                <option value="text">Chỉ text</option>
                                <option value="both">Cờ + Text</option>
                            </select>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="multilang[auto_detect]" value="1" 
                                   x-model="features.multilang.auto_detect" 
                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                            <label class="ml-2 text-sm text-gray-700">Tự động phát hiện ngôn ngữ trình duyệt</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Popup Quảng cáo -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900">Popup Quảng cáo</h2>
                                <p class="text-gray-600 text-sm">Banner, thời gian hiển thị</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="popup[enabled]" value="1" 
                                   x-model="features.popup.enabled" 
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-pink-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pink-600"></div>
                        </label>
                    </div>
                </div>
                <div class="p-6" x-show="features.popup.enabled">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Thời gian hiển thị (giây)</label>
                            <input type="number" name="popup[delay]" x-model="features.popup.delay" min="0" max="60"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tần suất hiển thị</label>
                            <select name="popup[frequency]" x-model="features.popup.frequency" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                                <option value="always">Luôn hiển thị</option>
                                <option value="once_per_session">1 lần/phiên</option>
                                <option value="once_per_day">1 lần/ngày</option>
                                <option value="once_per_week">1 lần/tuần</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nội dung popup</label>
                            <textarea name="popup[content]" x-model="features.popup.content" rows="3"
                                      placeholder="Nhập HTML hoặc text cho popup"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thông báo ảo -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900">Thông báo ảo</h2>
                                <p class="text-gray-600 text-sm">Fake notification mua hàng</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="fake_notification[enabled]" value="1" 
                                   x-model="features.fake_notification.enabled" 
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-600"></div>
                        </label>
                    </div>
                </div>
                <div class="p-6" x-show="features.fake_notification.enabled">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Khoảng thời gian hiển thị (giây)</label>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <input type="number" name="fake_notification[min_interval]" x-model="features.fake_notification.min_interval" 
                                           placeholder="Tối thiểu" min="5" max="300"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                                </div>
                                <div>
                                    <input type="number" name="fake_notification[max_interval]" x-model="features.fake_notification.max_interval" 
                                           placeholder="Tối đa" min="10" max="600"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Danh sách thông báo (mỗi dòng 1 thông báo)</label>
                            <textarea name="fake_notification[messages]" x-model="features.fake_notification.messages" rows="4"
                                      placeholder="Nguyễn Văn A vừa mua sản phẩm X&#10;Trần Thị B vừa đặt hàng thành công&#10;..."
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Vị trí hiển thị</label>
                            <select name="fake_notification[position]" x-model="features.fake_notification.position" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                                <option value="bottom-left">Dưới trái</option>
                                <option value="bottom-right">Dưới phải</option>
                                <option value="top-left">Trên trái</option>
                                <option value="top-right">Trên phải</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Button liên hệ -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900">Button liên hệ</h2>
                                <p class="text-gray-600 text-sm">Nút gọi nổi, Zalo, Messenger</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="contact_buttons[enabled]" value="1" 
                                   x-model="features.contact_buttons.enabled" 
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                    </div>
                </div>
                <div class="p-6" x-show="features.contact_buttons.enabled">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Vị trí hiển thị</label>
                            <select name="contact_buttons[position]" x-model="features.contact_buttons.position" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                <option value="right">Bên phải</option>
                                <option value="left">Bên trái</option>
                                <option value="bottom">Dưới cùng</option>
                            </select>
                        </div>
                        
                        <!-- Phone -->
                        <div class="border rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <label class="font-medium text-gray-700">Hotline</label>
                                <input type="checkbox" name="contact_buttons[phone][enabled]" value="1" 
                                       x-model="features.contact_buttons.phone.enabled" 
                                       class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500">
                            </div>
                            <div x-show="features.contact_buttons.phone.enabled">
                                <input type="text" name="contact_buttons[phone][number]" x-model="features.contact_buttons.phone.number" 
                                       placeholder="0123456789"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            </div>
                        </div>

                        <!-- Zalo -->
                        <div class="border rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <label class="font-medium text-gray-700">Zalo</label>
                                <input type="checkbox" name="contact_buttons[zalo][enabled]" value="1" 
                                       x-model="features.contact_buttons.zalo.enabled" 
                                       class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500">
                            </div>
                            <div x-show="features.contact_buttons.zalo.enabled">
                                <input type="text" name="contact_buttons[zalo][number]" x-model="features.contact_buttons.zalo.number" 
                                       placeholder="0123456789"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            </div>
                        </div>

                        <!-- Messenger -->
                        <div class="border rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <label class="font-medium text-gray-700">Messenger</label>
                                <input type="checkbox" name="contact_buttons[messenger][enabled]" value="1" 
                                       x-model="features.contact_buttons.messenger.enabled" 
                                       class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500">
                            </div>
                            <div x-show="features.contact_buttons.messenger.enabled">
                                <input type="text" name="contact_buttons[messenger][page_id]" x-model="features.contact_buttons.messenger.page_id" 
                                       placeholder="Facebook Page ID"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thống kê truy cập -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900">Thống kê truy cập</h2>
                                <p class="text-gray-600 text-sm">Phân tích traffic, nguồn truy cập</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="analytics[enabled]" value="1" 
                                   x-model="features.analytics.enabled" 
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>
                </div>
                <div class="p-6" x-show="features.analytics.enabled">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Google Analytics ID</label>
                            <input type="text" name="analytics[google_id]" x-model="features.analytics.google_id" 
                                   placeholder="G-XXXXXXXXXX hoặc UA-XXXXXXXX-X"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Facebook Pixel ID</label>
                            <input type="text" name="analytics[facebook_pixel]" x-model="features.analytics.facebook_pixel" 
                                   placeholder="123456789012345"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="analytics[track_ecommerce]" value="1" 
                                   x-model="features.analytics.track_ecommerce" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500">
                            <label class="ml-2 text-sm text-gray-700">Theo dõi E-commerce (mua hàng, giỏ hàng)</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="analytics[show_counter]" value="1" 
                                   x-model="features.analytics.show_counter" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500">
                            <label class="ml-2 text-sm text-gray-700">Hiển thị số lượt truy cập trên website</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Watermark -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900">Watermark</h2>
                                <p class="text-gray-600 text-sm">Đóng dấu ảnh tự động</p>
                            </div>
                        </div>
                        <a href="{{ $projectCode ? route('project.admin.settings.watermark', $projectCode) : route('cms.settings.watermark') }}" 
                           class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm">
                            Cấu hình chi tiết
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        </svg>
                        <p class="text-sm">Nhấn "Cấu hình chi tiết" để thiết lập watermark</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="mt-8 flex justify-end">
            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-pink-700 focus:ring-4 focus:ring-purple-300 transition-all duration-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Lưu tất cả cấu hình
            </button>
        </div>
    </form>
</div>

<script>
function frontendFeaturesManager() {
    return {
        features: {
            multilang: {
                enabled: {{ setting('multilang.enabled', false) ? 'true' : 'false' }},
                position: '{{ setting('multilang.position', 'header') }}',
                style: '{{ setting('multilang.style', 'dropdown') }}',
                auto_detect: {{ setting('multilang.auto_detect', false) ? 'true' : 'false' }}
            },
            popup: {
                enabled: {{ setting('popup.enabled', false) ? 'true' : 'false' }},
                delay: {{ setting('popup.delay', 5) }},
                frequency: '{{ setting('popup.frequency', 'once_per_session') }}',
                content: `{{ setting('popup.content', '') }}`
            },
            fake_notification: {
                enabled: {{ setting('fake_notification.enabled', false) ? 'true' : 'false' }},
                min_interval: {{ setting('fake_notification.min_interval', 30) }},
                max_interval: {{ setting('fake_notification.max_interval', 120) }},
                messages: `{{ setting('fake_notification.messages', 'Nguyễn Văn A vừa mua sản phẩm X\nTrần Thị B vừa đặt hàng thành công') }}`,
                position: '{{ setting('fake_notification.position', 'bottom-right') }}'
            },
            contact_buttons: {
                enabled: {{ setting('contact_buttons.enabled', false) ? 'true' : 'false' }},
                position: '{{ setting('contact_buttons.position', 'right') }}',
                phone: {
                    enabled: {{ setting('contact_buttons.phone.enabled', false) ? 'true' : 'false' }},
                    number: '{{ setting('contact_buttons.phone.number', '') }}'
                },
                zalo: {
                    enabled: {{ setting('contact_buttons.zalo.enabled', false) ? 'true' : 'false' }},
                    number: '{{ setting('contact_buttons.zalo.number', '') }}'
                },
                messenger: {
                    enabled: {{ setting('contact_buttons.messenger.enabled', false) ? 'true' : 'false' }},
                    page_id: '{{ setting('contact_buttons.messenger.page_id', '') }}'
                }
            },
            analytics: {
                enabled: {{ setting('analytics.enabled', false) ? 'true' : 'false' }},
                google_id: '{{ setting('analytics.google_id', '') }}',
                facebook_pixel: '{{ setting('analytics.facebook_pixel', '') }}',
                track_ecommerce: {{ setting('analytics.track_ecommerce', false) ? 'true' : 'false' }},
                show_counter: {{ setting('analytics.show_counter', false) ? 'true' : 'false' }}
            }
        },

        init() {
            console.log('Frontend Features Manager initialized');
        }
    }
}
</script>

<style>
.peer:checked ~ .peer-checked\:after\:translate-x-full::after {
    transform: translateX(100%);
}
</style>
@endsection