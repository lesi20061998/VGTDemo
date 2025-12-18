@php
$metaTitle = $title ?? setting_string('seo_meta_title', config('app.name', ''));
$metaDescription = $description ?? setting_string('seo_meta_description');
$metaKeywords = $keywords ?? setting_string('seo_meta_keywords');
$metaImage = $image ?? asset('images/default-og.jpg');
$metaUrl = $url ?? url()->current();
$siteName = config('app.name', '');
$locale = app()->getLocale();
$gaId = setting_string('google_analytics_id');
@endphp
<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
@if($metaKeywords)
<meta name="keywords" content="{{ $metaKeywords }}">
@endif
<meta name="author" content="{{ $siteName }}">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<link rel="canonical" href="{{ $metaUrl }}">
<meta property="og:locale" content="{{ $locale == 'vi' ? 'vi_VN' : 'en_US' }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ $metaUrl }}">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:image" content="{{ $metaImage }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
<meta name="twitter:image" content="{{ $metaImage }}">
@if(setting_string('google_site_verification'))
<meta name="google-site-verification" content="{{ setting_string('google_site_verification') }}">
@endif
@if(setting_string('bing_site_verification'))
<meta name="msvalidate.01" content="{{ setting_string('bing_site_verification') }}">
@endif
@if(setting_string('custom_header_code'))
{!! setting_string('custom_header_code') !!}
@endif
@if($gaId)
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{{ $gaId }}');</script>
@endif
