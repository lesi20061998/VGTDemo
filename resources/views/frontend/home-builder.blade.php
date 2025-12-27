@extends('frontend.layouts.app')

@section('title', $homepage->meta_title ?? 'Trang chủ')
@section('meta_description', $homepage->meta_description ?? '')

@php
    $grapesData = json_decode($homepage->grapes_data, true) ?? [];
    $customCss = $grapesData['css'] ?? $homepage->custom_css ?? '';
    $content = $grapesData['html'] ?? $homepage->content ?? '';
@endphp

@push('styles')
@if($customCss)
<style>
    {!! $customCss !!}
</style>
@endif
@endpush

@section('content')
{!! $content !!}
@endsection
