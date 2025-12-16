@extends('frontend.layouts.page-layout')

@section('page-title', $page->title ?? 'Page')

@section('page-content')
<div class="prose max-w-none">
    {!! $page->content ?? '' !!}
</div>
@endsection

@section('sidebar')
<div class="space-y-6">
    {!! render_widgets('page-sidebar') !!}
</div>
@endsection
