@extends('cms.layouts.app')

@section('title', $title ?? 'Cấu hình')
@section('page-title', $title ?? 'Cấu hình')

@section('content')
@php
$projectCode = request()->route('projectCode') ?? ($currentProject->code ?? request()->segment(1));
$backRoute = $projectCode ? route('project.admin.settings.index', ['projectCode' => $projectCode]) : url('/admin/settings');
$saveRoute = $projectCode ? route('project.admin.settings.save', ['projectCode' => $projectCode]) : url('/admin/settings/save');
@endphp
<div class="mb-6">
    <a href="{{ $backRoute }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Quay lại
    </a>
</div>

<form method="POST" action="{{ $action ?? $saveRoute }}" class="space-y-6">
    @csrf
    @if(isset($method)) @method($method) @endif

    <div class="bg-white rounded-lg shadow-sm p-6">
        @yield('form-content')
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ $backRoute }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
            Hủy
        </a>
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            Lưu cấu hình
        </button>
    </div>
</form>
@endsection
