<!doctype html>
<html lang="en" x-data x-cloak>
<head>
    <meta charset="utf-8" />
    <title>Widget Preview</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <!-- Bootstrap Css -->
    <link href="/theme/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="/theme/icons/font-icon.css" rel="stylesheet" type="text/css" />
    <!-- Swiper/Jarallax/Flickity Css -->
    <link href="/theme/libs/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/theme/libs/flickity/flickity.min.css">
    <link rel="stylesheet" href="/theme/libs/jarallax/jarallax.min.css">
    <link href="https://fonts.googleapis.com/css?family=Libre+Baskerville:300,300i,400,400i,500,500i&display=swap" rel="stylesheet">
    <!-- App Css-->
    <link href="/theme/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
    <style>
        body { padding: 20px; background: #fff; }
    </style>
</head>
<body class="" x-data="{ showMenuScroll : false }">

    @yield('content')

    <!-- JAVASCRIPT -->
    <script src="/theme/libs/jquery/jquery.min.js"></script>
    <script src="/theme/js/store.js"></script>
    <script src="/theme/libs/jarallax/jarallax.min.js"></script>
    <script src="/theme/libs/swiper/swiper-bundle.min.js"></script>
    <script src="/theme/libs/alpinejs/cdn.min.js"></script>
    <script src="/theme/libs/jquery-countdown/jquery.countdown.min.js"></script>
    <script src="/theme/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/theme/js/product-slider.init.js"></script>
    <script src="/theme/libs/flickity/flickity.pkgd.min.js"></script>
    <script src="/theme/js/main.js"></script>
    <script src="/theme/js/app.js"></script>
</body>
</html>
