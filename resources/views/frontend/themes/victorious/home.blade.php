@extends('frontend.themes.victorious.layout')

@section('title', 'Home page | Victorious Cruise')

@section('content')
    {{-- Widgets will be rendered from database --}}
    {!! render_widgets('homepage-main') !!}
@endsection
