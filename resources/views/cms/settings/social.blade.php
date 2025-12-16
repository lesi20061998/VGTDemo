@extends('cms.settings.template', ['title' => 'Mạng xã hội'])

@section('form-content')
<div class="space-y-6">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Facebook</label>
        <input type="url" name="social_facebook" value="{{ old('social_facebook', setting('social_facebook')) }}" placeholder="https://facebook.com/..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Twitter / X</label>
        <input type="url" name="social_twitter" value="{{ old('social_twitter', setting('social_twitter')) }}" placeholder="https://twitter.com/..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Instagram</label>
        <input type="url" name="social_instagram" value="{{ old('social_instagram', setting('social_instagram')) }}" placeholder="https://instagram.com/..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">TikTok</label>
        <input type="url" name="social_tiktok" value="{{ old('social_tiktok', setting('social_tiktok')) }}" placeholder="https://tiktok.com/@..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
    </div>
</div>
@endsection
