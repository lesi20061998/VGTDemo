<!doctype html>
<html lang="en" x-data x-cloak>
<head>

    <meta charset="utf-8" />
    <title>Home Default | Kalles - Clean, Versatile, Responsive Bootstrap 5 Theme </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta content="Clean, Versatile, Responsive Bootstrap 5 Theme" name="description" />
    <meta content="SRBThemes" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="/theme/images/k_favicon_32x.png">
    <link href="/theme/libs/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/theme/libs/flickity/flickity.min.css">
    <link rel="stylesheet" href="/theme/libs/jarallax/jarallax.min.css">
    <link href="https://fonts.googleapis.com/css?family=Libre+Baskerville:300,300i,400,400i,500,500i&display=swap" rel="stylesheet">
    <!-- Bootstrap Css -->
    <link href="/theme/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="/theme/icons/font-icon.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="/theme/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
</head>

<body class="" x-data="{ showMenuScroll : false }">
<!--head banner-->
<div x-data="{ isOpen: true }" class="">
    <div class="t_header fs-13 d-flex align-items-center" x-bind:class="{ 'd-none': !isOpen }">
        <div class="container-fluid">
            <div class="d-flex gap-2">
                <div class="col text-center text-white">
                    Today deal sale off <strong>70% </strong>. End in
                    <strong class="js_kl__countdown"></strong>. <a href="#!" class="text-white">Hurry Up <i class="las la-arrow-right"></i></a>
                </div>
                <div class="col-auto mt-2 mt-md-0">
                    <a href="#" class="h_banner_close text-white" x-on:click.prevent="isOpen = false">close</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!--end head banner-->
<div id="kalles-section-header_top" class="">
    <div class="h__top d-flex align-items-center">
        <div class="container-fluid">
            <div class="row align-items-center justify-content-center py-3 py-xl-0">
                <div class="col-md-5 col-lg-4 col-12 d-none d-md-block">
                    <div class="d-flex align-items-xl-center justify-content-center justify-content-md-start gap-3">
                        <a href="tel:+01 23456789" class="mb-0 text-muted"><i class="pegk pe-7s-call fs-14 me-1 align-middle"></i>
                            +01 23456789</a>
                        <a href="mailto:Kalles@domain.com" class="mb-0 text-muted"><i class="pe-7s-mail pegk fs-14 me-1 align-middle"></i> Kalles@domain.com</a>
                    </div>
                </div>
                <div class="col-md-5 col-lg-4 col-12">
                    <div class="header-text text-center fs-12 py-1 py-lg-0">
                        Summer sale discount off <span class="cr">50%</span>! <a href="shop.html" class="text-reset">Shop Now</a>
                    </div>
                </div>
                <div class="col-md-2 col-lg-4 col-12">
                    <div class="dropdown text-md-end text-center position-relative">
                        <a href="#!" class="fs-12 text-reset currency-button" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="/theme/images/svg/usd.svg" alt="" height="12" class="me-1"> USD <i class="facl facl-angle-down ms-1"></i>
                        </a>
                        <ul class="dropdown-menu p-3 dropdown-currency">
                            <li><a href="#!"><img src="/theme/images/svg/aud.svg" alt="" height="12" class="me-1">
                                    AUD</a></li>
                            <li><a href="#!"><img src="/theme/images/svg/cad.svg" alt="" height="12" class="me-1">
                                    CAD</a></li>
                            <li><a href="#!"><img src="/theme/images/svg/dkk.svg" alt="" height="12" class="me-1">
                                    DKK</a></li>
                            <li><a href="#!"><img src="/theme/images/svg/eur.svg" alt="" height="12" class="me-1">
                                    EUR</a></li>
                            <li><a href="#!"><img src="/theme/images/svg/gbp.svg" alt="" height="12" class="me-1">
                                    GBP</a></li>
                            <li><a href="#!"><img src="/theme/images/svg/hkd.svg" alt="" height="12" class="me-1">
                                    HKD</a></li>
                            <li><a href="#!"><img src="/theme/images/svg/jpy.svg" alt="" height="12" class="me-1">
                                    JPY</a></li>
                            <li><a href="#!"><img src="/theme/images/svg/nzd.svg" alt="" height="12" class="me-1">
                                    NZD</a></li>
                            <li><a href="#!"><img src="/theme/images/svg/sgd.svg" alt="" height="12" class="me-1">
                                    SGD</a></li>
                            <li><a href="#!"><img src="/theme/images/svg/usd.svg" alt="" height="12" class="me-1">
                                    USD</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <nav class="navbar navbar-expand-lg navbar-custom py-0 d-flex align-items-center">
        <div class="container-fluid">
            <a class="d-lg-none" data-bs-toggle="offcanvas" href="#offcanvasExample" role="button" aria-controls="offcanvasExample">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="16" viewBox="0 0 30 16">
                    <rect width="30" height="1.5"></rect>
                    <rect y="7" width="20" height="1.5"></rect>
                    <rect y="14" width="30" height="1.5"></rect>
                </svg>
            </a>
            <a class="navbar-brand" href="index.html"><img src="/theme/images/svg/kalles.svg" alt="" width="95"></a>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <div class="d-none d-lg-block mx-auto">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                        <li class="nav-item dropdown dropdown-mega-xxl">
                            <a class="nav-link" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                Demo
                            </a>
                            <div class="dropdown-menu">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="dropdown-sub-column-item">
                                            <p class="dropdown-menu-title">Home Pages</p>
                                            <ul class="sub-column-menu">
                                                <li>
                                                    <a class="text-muted position-relative d-inline-flex" href="index.html">Home Default
                                                        <span class="badge-tag badge bg-danger">Hot</span>
                                                    </a>
                                                </li>
                                                <li><a class="text-muted position-relative d-inline-flex" href="home-classic.html">Home Classic <span class="badge-tag badge bg-danger">Hot</span></a>
                                                </li>
                                                <li><a class="text-muted" href="home-video-banner.html">Home Video Banner</a></li>
                                                <li><a class="text-muted" href="home-categories-links.html">Home Categories
                                                        Links</a>
                                                </li>
                                                <li><a class="text-muted" href="home-static-image.html">Home Static Image</a></li>
                                                <li><a class="text-muted" href="home-metro.html">Home Metro</a></li>
                                                <li><a class="text-muted" href="home-lookbook.html">Home Lookbook</a></li>
                                                <li><a class="text-muted" href="home-parallax.html">Home Parallax</a></li>
                                                <li><a class="text-muted" href="home-instagram-shop.html">Home Instgram Shop</a>
                                                </li>
                                                <li><a class="text-muted position-relative d-inline-flex" href="home-medical.html">Home Medical <span class="badge-tag badge bg-danger">Hot</span></a>
                                                </li>

                                                <li><a class="text-muted" href="home-flower.html">Home Flower</a></li>
                                                <li><a class="text-muted position-relative d-inline-flex" href="home-furniture.html">Home Furniture<span class="badge-tag badge bg-danger">Hot</span></a></li>
                                                <li><a class="text-muted" href="home-bag.html">Home Bag</a></li>

                                                <li><a class="text-muted" href="home-lingeries.html">Home Lingeries</a></li>
                                                <li><a class="text-muted position-relative d-inline-flex" href="home-cosmetics.html">Home Cosmetics <span class="badge-tag badge bg-teal">new</span></a></li>
                                                <li><a class="text-muted position-relative d-inline-flex" href="home-glasses.html">Home Glasses <span class="badge-tag badge bg-teal">new</span></a></li>
                                                <li><a class="text-muted position-relative d-inline-flex" href="home-shoes.html">Home Shoes <span class="badge-tag badge bg-danger">Hot</span></a></li>

                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="dropdown-sub-column-item">
                                            <a href="index.html" class="dropdown-menu-title">Home
                                                Pages</a>
                                            <ul class="sub-column-menu">
                                                <li>
                                                    <a class="text-muted" href="home-fashion9.html">Home Fashion 9</a>
                                                </li>
                                                <li><a class="text-muted" href="home-lookbook-collection.html">Home Lookbook
                                                        Collection</a></li>
                                                <li><a class="text-muted" href="home-fashion-simple.html">Home Fashion Simple</a>
                                                </li>
                                                <li><a class="text-muted" href="home-fashion10.html">Home Fashion 10</a></li>
                                                <li><a class="text-muted" href="home-decor.html">Home Decor</a></li>
                                                <li><a class="text-muted" href="home-decor2.html">Home Decor 2</a></li>
                                                <li><a class="text-muted" href="home-fashion-vertical.html">Home Fashion
                                                        Vertical</a>
                                                </li>
                                                <li><a class="text-muted" href="home-electric.html">Home Electric</a></li>
                                                <li><a class="text-muted" href="home-electric-vertical.html">Home Electric
                                                        Vertical</a>
                                                </li>
                                                <li><a class="text-muted" href="home-digital.html">Home Digital</a></li>
                                                <li><a class="text-muted position-relative d-inline-flex" href="home-one-product-store.html">One Product Store <span class="badge-tag badge bg-danger">Hot</span></a>
                                                <li><a class="text-muted" href="home-handmade.html">Home Handmade</a></li>
                                                <li><a class="text-muted" href="home-fashion-trend.html">Home Fashion Trend</a>
                                                </li>
                                                <li><a class="text-muted" href="home-kids.html">Home Kids</a></li>
                                                <li><a class="text-muted position-relative d-inline-flex" href="home-sport.html">Home Sport <span class="badge-tag badge bg-teal">new</span></a></li></a>
                                                <li><a class="text-muted position-relative d-inline-flex" href="home-jewelry.html">Home Jewelry <span class="badge-tag badge bg-teal">new</span></a>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="dropdown-sub-column-item">
                                            <a href="index.html" class="dropdown-menu-title">Header
                                                Layouts</a>
                                            <ul class="sub-column-menu">
                                                <li>
                                                    <a class="text-muted" href="home-header-01.html">Header Layout 1</a>
                                                </li>
                                                <li><a class="text-muted" href="home-header-02.html">Header Layout 2</a></li>
                                                <li><a class="text-muted" href="index.html">Header Layout 3</a></li>
                                                <li><a class="text-muted" href="home-header-04.html">Header Layout 4</a></li>
                                                <li><a class="text-muted" href="home-electric.html">Header Layout 5</a></li>
                                                <li><a class="text-muted" href="home-header-06.html">Header Layout 6</a></li>
                                                <li><a class="text-muted" href="home-fashion-vertical.html">Header Layout 7</a>
                                                </li>
                                                <li><a class="text-muted" href="home-electric-vertical.html">Header Layout 8</a>
                                                </li>
                                                <li><a class="text-muted" href="home-decor.html">Header Transparent</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="dropdown-sub-column-item">
                                            <a href="#!" class="dropdown-menu-title">FEATURES</a>
                                            <ul class="sub-column-menu">
                                                <li><a class="text-muted position-relative d-inline-flex" href="shop-filter-sidebar.html">Filter Options <span class="badge-tag badge bg-danger">Hot</span></a></li>
                                                <li><a class="text-muted" href="index.html">Catalog mode</a></li>
                                                <li><a class="text-muted" href="shop.html">Cookies law info</a></li>
                                                <li><a class="text-muted" href="home-age-verified.html">Age verification</a></li>
                                                <li><a class="text-muted" href="index.html">Mega menu</a></li>
                                                <li><a class="text-muted" href="home-parallax.html">Footer sticky</a></li>
                                                <li><a class="text-muted" href="shop-right-sidebar.html">Right Sidebar</a></li>
                                                <li><a class="text-muted" href="shop-hidden-sidebar.html">Hidden sidebar</a></li>
                                                <li><a class="text-muted" href="checkout.html">Checkout</a></li>
                                                <li><a class="text-muted" href="product-detail-frequently-bought-together.html">Frequently
                                                        Bought Together</a></li>
                                                <li><a class="text-muted" href="product-detail-variant-images-grouped.html">Variant
                                                        Images Grouped</a></li>
                                                <li><a class="text-muted position-relative d-inline-flex" href="home-rtl.html">Demo RTL <span class="badge-tag badge bg-danger">Hot</span></a></li>
                                                <li><a class="text-muted position-relative d-inline-flex" href="shop-grid-list-switcher.html">Grid/List
                                                        switcher <span class="badge-tag badge bg-danger">Hot</span></a></li>
                                                <li><a class="text-muted position-relative d-inline-flex" href="home-shoes.html">Compare <span class="badge-tag badge bg-teal">new</span></a></li>
                                                <li><a class="text-muted position-relative d-inline-flex" href="product-detail-pickup-availability.html">Pickup
                                                        Availability <span class="position-absolute badge bg-teal rounded-pill fw-normal text-white" style="left:  103%; top: 10px;">Selling feature</span></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item dropdown dropdown-mega-xxl">
                            <a class="nav-link position-relative" href="shop-filter-sidebar.html" data-bs-toggle="dropdown" aria-expanded="false">
                                Shop <span class="badge bg-teal fw-normal">New</span>
                            </a>
                            <div class="dropdown-menu p-3">
                                <div class="row g-0">
                                    <div class="col-lg-5">
                                        <div class="row g-0">
                                            <div class="col-lg-6">
                                                <div class="dropdown-sub-column-item">
                                                    <a href="shop-filter-sidebar.html" class="dropdown-menu-title">SHOP PAGES</a>
                                                    <ul class="sub-column-menu">
                                                        <li>
                                                            <a class="text-muted" href="shop.html">Grid Layout</a>
                                                        </li>
                                                        <li>
                                                            <a class="text-muted" href="shop-packery-layout.html">Packery Layout</a>
                                                        </li>
                                                        <li>
                                                            <a class="text-muted" href="shop-masonry-layout.html">Masonry Layout</a>
                                                        </li>
                                                        <li>
                                                            <a class="text-muted" href="shop-full-width-layout.html">Full Width Layout</a>
                                                        </li>
                                                        <li>
                                                            <a class="text-muted" href="shop-1600px-layout.html">1600px Layout</a>
                                                        </li>
                                                        <li>
                                                            <a class="text-muted" href="shop-left-sidebar.html">Left Sidebar</a>
                                                        </li>
                                                        <li>
                                                            <a class="text-muted" href="shop-right-sidebar.html">Right Sidebar</a>
                                                        </li>
                                                        <li>
                                                            <a class="text-muted" href="shop-hidden-sidebar.html">Hidden sidebar</a>
                                                        </li>
                                                        <li>
                                                            <a class="text-muted" href="shopping-cart.html">Shopping cart</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="dropdown-sub-column-item">

                                                    <a href="shop-filter-sidebar.html" class="dropdown-menu-title">FEATURES</a>
                                                    <ul class="sub-column-menu">
                                                        <li><a class="text-muted position-relative d-inline-flex" href="shop-filter-sidebar.html">Filter
                                                                options <span class="badge-tag badge bg-danger">Hot</span></a></li>
                                                        <li>
                                                            <a class="text-muted" href="shop.html">Filters area</a>
                                                        </li>
                                                        <li>
                                                            <a class="text-muted" href="shop-filter-sidebar.html">Filters sidebar</a>
                                                        </li>
                                                        <li>
                                                            <a class="text-muted" href="shop-load-more.html">Load more button</a>
                                                        <li>
                                                            <a class="text-muted" href="shop-filter-sidebar.html">Infinite scrolling</a>
                                                        </li>
                                                        <li>
                                                            <a class="text-muted" href="shop-collection.html">Collections list</a>
                                                        </li>
                                                        <li>
                                                            <a class="text-muted" href="index.html">Hidden Search</a>
                                                        </li>
                                                        <li>
                                                            <a class="text-muted" href="home-categories-links.html">Search Fullscreen</a>
                                                        </li>
                                                        <li><a class="text-muted" href="checkout.html">Checkout</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        <div class="row p-4">
                                            <div class="col-lg-6 cat-section p-0">
                                                <a href="shop-left-sidebar.html" class="d-block position-relative cat_grid_item overflow-hidden " style="height: 350px;">
                                                    <div class="h-100 w-100 cat-grid-img" style="background-image: url('/theme/images/home-classic/mega-banner-01.jpg');"></div>
                                                    <div class="cat-grid-button text-body">
                                                        <div class="cat_grid_item__title">Women</div>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col-lg-6 cat-section ps-4 p-0">
                                                <a href="shop-left-sidebar.html" class="d-block position-relative cat_grid_item overflow-hidden " style="height: 350px;">
                                                    <div class="h-100 w-100 cat-grid-img" style="background-image: url('/theme/images/megamenu/bn-02.jpg');"></div>
                                                    <div class="cat-grid-button text-body">
                                                        <div class="cat_grid_item__title">Men</div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item dropdown dropdown-mega-xxl">
                            <a class="nav-link" href="product-detail-layout-01.html" data-bs-toggle="dropdown" aria-expanded="false">
                                Product
                            </a>
                            <div class="dropdown-menu">
                                <div class="row me-4">
                                    <div class="col-lg-3">
                                        <div class="dropdown-sub-column-item">
                                            <a href="product-detail-layout-01.html" class="dropdown-menu-title">PRODUCT LAYOUT</a>
                                            <ul class="sub-column-menu">
                                                <li>
                                                    <a class="text-muted" href="product-detail-layout-01.html">Product Detail
                                                        Layout
                                                        1</a>
                                                </li>
                                                <li><a class="text-muted" href="product-detail-layout-02.html">Product Detail
                                                        Layout
                                                        2</a></li>
                                                <li><a class="text-muted" href="product-detail-layout-03.html">Product Detail
                                                        Layout
                                                        3</a></li>
                                                <li><a class="text-muted" href="product-detail-thumb-bottom.html">Product thumb at
                                                        bottom</a></li>
                                                <li><a class="text-muted" href="product-detail-thumb-right.html">Product thumb on
                                                        right</a></li>
                                                <li><a class="text-muted" href="product-detail-without-thumbnail.html">Product
                                                        without
                                                        thumbnail</a></li>
                                                <li><a class="text-muted" href="product-detail-left-sidebar.html">Left Sidebar</a>
                                                </li>
                                                <li><a class="text-muted" href="product-detail-right-sidebar.html">Right
                                                        sidebar</a>
                                                </li>
                                                <li><a class="text-muted" href="product-detail-sidebar-full-height.html">Sidebar
                                                        Full
                                                        Height</a></li>
                                                <li><a class="text-muted" href="product-detail-tab-accordion.html">Product Tab
                                                        Accordions</a></li>
                                                <li><a class="text-muted" href="product-detail-full-width-atc.html">Product Full
                                                        Width
                                                        ATC</a></li>
                                                <li><a class="text-muted" href="product-detail-full-width.html">Product full width
                                                        layout</a></li>
                                                <li><a class="text-muted position-relative d-inline-flex" href="product-detail-advance-product-type.html">
                                                        Advance Product Type <span class="badge-tag badge bg-danger">Hot</span></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="dropdown-sub-column-item">
                                            <a href="product-detail-layout-01.html" class="dropdown-menu-title">PRODUCT DETAIL</a>
                                            <ul class="sub-column-menu">
                                                <li>
                                                    <a class="text-muted" href="product-detail-external-affiliate.html">External/Affiliate
                                                        Product</a>
                                                </li>
                                                <li><a class="text-muted" href="product-detail-simple-product.html">Simple
                                                        product</a>
                                                </li>
                                                <li><a class="text-muted" href="product-detail-layout-01.html">Variable product</a>
                                                </li>
                                                <li><a class="text-muted position-relative d-inline-flex" href="product-detail-grouped-product.html">Grouped
                                                        Product <span class="badge-tag badge bg-danger">Hot</span></a></li>

                                                <li><a class="text-muted" href="product-detail-layout-02.html">Inner Zoom #1</a>
                                                </li>
                                                <li><a class="text-muted" href="product-detail-layout-01.html">External Zoom</a>
                                                </li>
                                                <li><a class="text-muted" href="product-detail-layout-03.html">Inner Zoom #2</a>
                                                </li>
                                                <li><a class="text-muted" href="product-detail-layout-01.html">PhotoSwipe Popup</a>
                                                </li>
                                                <li><a class="text-muted" href="product-detail-description-with-product.html">Description
                                                        with product</a></li>
                                                <li><a class="text-muted" href="product-detail-description-with-instagram-shop.html">Description
                                                        with instagram shop</a></li>
                                                <li><a class="text-muted position-relative d-inline-flex" href="product-detail-product-video.html">Product video <span class="badge-tag badge bg-danger">Hot</span></a></li>
                                                <li><a class="text-muted position-relative d-inline-flex" href="product-detail-3d-ar-models.html">Product 3D, AR
                                                        models<span class="badge-tag badge bg-danger">Hot</span></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="dropdown-sub-column-item">
                                            <a href="product-detail-layout-01.html" class="dropdown-menu-title">PRODUCT SWATCH</a>
                                            <ul class="sub-column-menu">
                                                <li>
                                                    <a class="text-muted" href="product-detail-layout-01.html">Product Color
                                                        Swatch</a>
                                                </li>
                                                <li><a class="text-muted" href="product-detail-swatch-color.html">Product Gallery
                                                        Swatch</a></li>
                                                <li><a class="text-muted" href="product-detail-swatch-color.html">Product Images
                                                        Swatch</a></li>
                                                <li><a class="text-muted" href="product-detail-swatch-color.html">Swatch Color</a>
                                                </li>
                                                <li><a class="text-muted" href="product-detail-layout-01.html">Swatch Color
                                                        Circle</a>
                                                </li>
                                                <li><a class="text-muted" href="product-detail-swatch-radio.html">Swatch Radio</a>
                                                </li>
                                                <li><a class="text-muted" href="product-detail-swatch-radio-color.html">Swatch
                                                        Radio
                                                        Color</a></li>
                                                <li><a class="text-muted" href="product-detail-swatch-rectangle.html">Swatch
                                                        Rectangle</a></li>
                                                <li><a class="text-muted" href="product-detail-swatch-rectangle-color.html">Swatch
                                                        Rectangle Color</a></li>
                                                <li><a class="text-muted" href="product-detail-swatch-simple.html">Swatch
                                                        Simple</a>
                                                </li>
                                                <li><a class="text-muted" href="product-detail-swatch-simple-color.html">Swatch
                                                        Simple
                                                        Color</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="dropdown-sub-column-item">
                                            <a href="product-detail-layout-01.html" class="dropdown-menu-title">PRODUCT FEATURES</a>
                                            <ul class="sub-column-menu">
                                                <li>
                                                    <a class="text-muted position-relative d-inline-flex" href="product-detail-frequently-bought-together.html" style="white-space: nowrap;">Frequently
                                                        Bought Together <span class="badge-tag badge bg-teal">new</span></a></a>
                                                </li>
                                                <li><a class="text-muted" href="product-detail-pre-orders.html">Product
                                                        pre-orders</a>
                                                </li>
                                                <li>
                                                    <a class="text-muted position-relative d-inline-flex" href="product-detail-tab-accordion.html">Product Upsell<span class="badge-tag badge bg-danger">Hot</span></a>
                                                </li>
                                                <li>
                                                    <a class="text-muted position-relative d-inline-flex" href="product-detail-description-with-lookbook.html" style="white-space: nowrap;">Description
                                                        with Lookbook<span class="badge-tag badge bg-danger">Hot</span></a>
                                                </li>
                                                <li><a class="text-muted" href="product-detail-back-in-stock-notification.html">Back
                                                        in
                                                        stock notification</a></li>
                                                <li>
                                                    <a class="text-muted position-relative d-inline-flex" href="product-detail-variant-images-grouped.html">Variant
                                                        Images Grouped<span class="badge-tag badge bg-danger">Hot</span></a>
                                                </li>
                                                <li><a class="text-muted" href="product-detail-layout-01.html">Size Guide HTML</a>
                                                </li>
                                                <li><a class="text-muted" href="product-detail-layout-01.html">Delivery &
                                                        Return</a>
                                                </li>
                                                <li><a class="text-muted" href="product-detail-layout-01.html">Ask a Question</a>
                                                </li>
                                                <li><a class="text-muted" href="product-detail-product-sticky.html">Product
                                                        sticky</a>
                                                </li>
                                                <li><a class="text-muted" href="product-detail-360-viewer.html">360� product
                                                        viewer</a>
                                                </li>
                                                <li>
                                                    <a class="text-muted position-relative d-inline-flex" href="product-detail-swatch-radio.html" style="white-space: nowrap;">Dynamic checkout
                                                        buttons <span class="badge-tag badge bg-danger">Hot</span></a>
                                                </li>
                                                <li>
                                                    <a class="text-muted position-relative d-inline-flex" href="product-detail-layout-01.html">Sticky add to
                                                        cart <span class="badge-tag badge bg-danger">Hot</span></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item dropdown dropdown-mega-3xl">
                            <a class="nav-link position-relative text-danger" href="shop-filter-sidebar.html" data-bs-toggle="dropdown" aria-expanded="false">
                                Sale <span class="badge bg-warning fw-normal">Sale</span>
                            </a>
                            <div class="dropdown-menu">
                                <div class="row">
                                    <div class="col-lg-2">
                                        <div class="dropdown-sub-column-item">
                                            <a href="shop-full-width-layout.html" class="dropdown-menu-title">Accessories</a>
                                            <a href="shop-1600px-layout.html" class="dropdown-menu-title">Footwear</a>
                                            <a href="shop-filter-sidebar.html" class="dropdown-menu-title">Women</a>
                                            <a href="shop-left-sidebar.html" class="dropdown-menu-title">T-Shirt</a>
                                            <a href="shop-right-sidebar.html" class="dropdown-menu-title">Shoes</a>
                                            <a href="shop-masonry-layout.html" class="dropdown-menu-title">Denim</a>
                                            <a href="shop-1600px-layout.html" class="dropdown-menu-title">Dress</a>
                                            <a href="shop-filter-sidebar.html" class="dropdown-menu-title">Men</a>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <!-- Swiper -->
                                        <div class="swiper mySwiper">
                                            <div class="swiper-wrapper">
                                                <div class="swiper-slide">
                                                    <div class="topbar-product-card pb-3">
                                                        <div class="position-relative">
                                                            <span class="new-label bg-success text-white rounded-circle">
                                                                New </span>
                                                            <img src="/theme/images/megamenu/pr-01.jpg" alt="" class="img-fluid">
                                                            <a href="#" class="wishlistadd position-absolute" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o"></i></a>

                                                            <div class="product-button d-flex flex-column gap-2">
                                                                <a href="#!" class="btn rounded-pill fs-14"><span>Quick
                                                                        View</span> <i class="iccl iccl-eye"></i></a>
                                                                <a href="#!" class="btn rounded-pill fs-14"><span>Quick
                                                                        Shop</span> <i class="iccl iccl-cart"></i></a>
                                                            </div>
                                                            <p class="product-size mb-0 text-center text-white fw-medium">
                                                                XS, S, M, L, XL</p>
                                                        </div>
                                                        <div class="mt-3">
                                                            <h6 class="mb-1"><a href="#!" class="product-title">Analogue
                                                                    Resin Strap</a></h6>
                                                            <p class="mb-0 fs-14 text-muted">$30.00</p>
                                                        </div>
                                                    </div>
                                                </div><!--end slide-->
                                                <div class="swiper-slide">
                                                    <div class="topbar-product-card pb-3">
                                                        <div class="position-relative">
                                                            <img src="/theme/images/megamenu/pr-03.jpg" alt="" class="img-fluid">
                                                            <a href="#" class="wishlistadd position-absolute" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o"></i></a>

                                                            <div class="product-button d-flex flex-column gap-2">
                                                                <a href="#!" class="btn rounded-pill fs-14"><span>Quick
                                                                        View</span> <i class="iccl iccl-eye"></i></a>
                                                                <a href="#!" class="btn rounded-pill fs-14"><span>Quick
                                                                        Shop</span> <i class="iccl iccl-cart"></i></a>
                                                            </div>
                                                            <p class="product-size mb-0 text-center text-white fw-medium">
                                                                XS, S, M, L, XL</p>
                                                        </div>
                                                        <div class="mt-3">
                                                            <h6 class="mb-1"><a href="product-detail-layout-01.html" class="product-title">Ridley High Waist</a></h6>
                                                            <p class="mb-0 fs-14 text-muted">$36.00</p>
                                                        </div>
                                                    </div>
                                                </div><!--end slide-->
                                                <div class="swiper-slide">
                                                    <div x-data="{ imageUrl: '/theme/images/megamenu/pr-05.jpg' }" class="topbar-product-card pb-3">
                                                        <div class="position-relative">
                                                            <img :src="imageUrl" alt="" class="img-fluid">
                                                            <a href="#" class="wishlistadd position-absolute" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o"></i></a>

                                                            <div class="product-button d-flex flex-column gap-2">
                                                                <a href="#!" class="btn rounded-pill fs-14"><span>Quick
                                                                        View</span> <i class="iccl iccl-eye"></i></a>
                                                                <a href="#!" class="btn rounded-pill fs-14"><span>Quick
                                                                        Shop</span> <i class="iccl iccl-cart"></i></a>
                                                            </div>
                                                            <p class="product-size mb-0 text-center text-white fw-medium">
                                                                S,
                                                                M, L</p>
                                                        </div>
                                                        <div class="mt-3">
                                                            <h6 class="mb-1"><a href="#!" class="product-title">Blush
                                                                    Beanie</a></h6>
                                                            <p class="mb-0 fs-14 text-muted">$15.00</p>
                                                            <div class="product-color-list mt-2 gap-2 d-flex align-items-center">
                                                                <a href="#!" x-on:mouseover="imageUrl = 'theme/images/megamenu/pr-05.jpg'" x-on:click.prevent="imageUrl = 'theme/images/megamenu/pr-05.jpg'" class="d-inline-block bg-body-tertiary rounded-circle"></a>
                                                                <a href="#!" x-on:mouseover="imageUrl = 'theme/images/products/pr-31.jpg'" x-on:click.prevent="imageUrl = 'theme/images/products/pr-31.jpg'" class="d-inline-block bg_color_pink rounded-circle"></a>
                                                                <a href="#!" x-on:mouseover="imageUrl = 'theme/images/products/pr-32.jpg'" x-on:click.prevent="imageUrl = 'theme/images/products/pr-32.jpg'" class="d-inline-block bg-dark rounded-circle"></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div><!--end slide-->
                                                <div class="swiper-slide">
                                                    <div x-data="{ imageUrl: '/theme/images/megamenu/pr-07.jpg' }" class="topbar-product-card pb-3">
                                                        <div class="position-relative">
                                                            <span class="new-label bg-danger text-white rounded-circle">
                                                                -25% </span>
                                                            <img :src="imageUrl" alt="" class="img-fluid">
                                                            <a href="#" class="wishlistadd position-absolute" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o"></i></a>

                                                            <div class="product-button d-flex flex-column gap-2">
                                                                <a href="#!" class="btn rounded-pill fs-14"><span>Quick
                                                                        View</span> <i class="iccl iccl-eye"></i></a>
                                                                <a href="#!" class="btn rounded-pill fs-14"><span>Quick
                                                                        Shop</span> <i class="iccl iccl-cart"></i></a>
                                                            </div>
                                                            <p class="product-size mb-0 text-center text-white fw-medium">
                                                                XS, S, M</p>
                                                        </div>
                                                        <div class="mt-3">
                                                            <h6 class="mb-1"><a href="#!" class="product-title">Cluse La
                                                                    Boheme Rose Gold</a></h6>
                                                            <p class="mb-0 fs-14 text-muted">
                                                                <del>$60.00</del>
                                                                <span class="text-danger">$45.00</span>
                                                            </p>
                                                            <div class="product-color-list mt-2 gap-2 d-flex align-items-center">
                                                                <a href="#!" x-on:mouseover="imageUrl = 'theme/images/megamenu/pr-07.jpg'" x-on:click.prevent="imageUrl = 'theme/images/megamenu/pr-05.jpg'" class="d-inline-block bg_color_green rounded-circle"></a>
                                                                <a href="#!" x-on:mouseover="imageUrl = 'theme/images/products/pr-06.jpg'" x-on:click.prevent="imageUrl = 'theme/images/products/pr-31.jpg'" class="d-inline-block bg-body-secondary rounded-circle"></a>
                                                                <a href="#!" x-on:mouseover="imageUrl = 'theme/images/products/pr-08.jpg'" x-on:click.prevent="imageUrl = 'theme/images/products/pr-32.jpg'" class="d-inline-block bg_color_blue rounded-circle"></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div><!--end slide-->
                                                <div class="swiper-slide">
                                                    <div x-data="{ imageUrl: '/theme/images/megamenu/pr-09.jpg' }" class="topbar-product-card pb-3">
                                                        <div class="position-relative">
                                                            <img :src="imageUrl" alt="" class="img-fluid">
                                                            <a href="#" class="wishlistadd position-absolute" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o"></i></a>

                                                            <div class="product-button d-flex flex-column gap-2">
                                                                <a href="#!" class="btn rounded-pill fs-14"><span>Quick
                                                                        View</span> <i class="iccl iccl-eye"></i></a>
                                                                <a href="#!" class="btn rounded-pill fs-14"><span>Quick
                                                                        Shop</span> <i class="iccl iccl-cart"></i></a>
                                                            </div>
                                                            <p class="product-size mb-0 text-center text-white fw-medium">
                                                                S,
                                                                M</p>
                                                        </div>
                                                        <div class="mt-3">
                                                            <h6 class="mb-1"><a href="#!" class="product-title">Mercury
                                                                    Tee</a></h6>
                                                            <p class="mb-0 fs-14 text-muted">
                                                                <span>$68.00</span>
                                                            </p>
                                                            <div class="product-color-list mt-2 gap-2 d-flex align-items-center">
                                                                <a href="#!" x-on:mouseover="imageUrl = 'theme/images/megamenu/pr-09.jpg'" x-on:click.prevent="imageUrl = 'theme/images/megamenu/pr-09.jpg'" class="d-inline-block rounded-circle" style="background: url('/theme/images/megamenu/pr-09.jpg');background-size: cover;"></a>
                                                                <a href="#!" x-on:mouseover="imageUrl = 'theme/images/products/pr-14.jpg'" x-on:click.prevent="imageUrl = 'theme/images/products/pr-14.jpg'" class="d-inline-block rounded-circle" style="background: url('/theme/images/products/pr-14.jpg');background-size: cover;"></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div><!--end slide-->
                                                <div class="swiper-slide">
                                                    <div x-data="{ imageUrl: '/theme/images/megamenu/pr-11.jpg' }" class="topbar-product-card pb-3">
                                                        <div class="position-relative">
                                                            <span class="new-label bg-danger text-white rounded-circle">
                                                                -34% </span>
                                                            <img :src="imageUrl" alt="" class="img-fluid">
                                                            <a href="#" class="wishlistadd position-absolute" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o"></i></a>

                                                            <div class="product-button d-flex flex-column gap-2">
                                                                <a href="#!" class="btn rounded-pill fs-14"><span>Quick
                                                                        View</span> <i class="iccl iccl-eye"></i></a>
                                                                <a href="#!" class="btn rounded-pill fs-14"><span>Quick
                                                                        Shop</span> <i class="iccl iccl-cart"></i></a>
                                                            </div>
                                                        </div>
                                                        <div class="mt-3">
                                                            <h6 class="mb-1"><a href="#!" class="product-title">La
                                                                    Boh�me
                                                                    Rose Gold</a></h6>
                                                            <p class="mb-0 fs-14 text-muted">
                                                                <del>$60.00</del>
                                                                <span class="text-danger">$40.00</span>
                                                            </p>
                                                            <div class="product-color-list mt-2 gap-2 d-flex align-items-center">
                                                                <a href="#!" x-on:mouseover="imageUrl = 'theme/images/megamenu/pr-11.jpg'" x-on:click.prevent="imageUrl = 'theme/images/megamenu/pr-11.jpg'" class="d-inline-block bg_color_pink rounded-circle"></a>
                                                                <a href="#!" x-on:mouseover="imageUrl = 'theme/images/products/pr-35.jpg'" x-on:click.prevent="imageUrl = 'theme/images/products/pr-35.jpg'" class="d-inline-block bg-dark rounded-circle"></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div><!--end slide-->
                                            </div>
                                            <div class="swiper-button-next"></div>
                                            <div class="swiper-button-prev"></div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item dropdown dropdown-mega-lg">
                            <a class="nav-link" href="portfolio-3-columns.html" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Portfolio
                            </a>
                            <ul class="dropdown-menu dropdown-sub-column">
                                <li><a class="text-muted" href="portfolio.html">Portfolio 2 Columns</a></li>
                                <li><a class="text-muted" href="portfolio-3-columns.html">Portfolio 3 Columns</a></li>
                                <li><a class="text-muted" href="portfolio-4-columns.html">Portfolio 4 Columns</a></li>
                                <li><a class="text-muted" href="single-portfolio-with-shop.html">Single Portfolio With Shop</a></li>
                                <li><a class="text-muted" href="single-portfolio-with-lookbook.html">Single Portfolio With Lookbook</a>
                                </li>
                                <li><a class="text-muted" href="single-portfolio-with-instagram-shop.html">Portfolio With Instagram
                                        Shop</a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown dropdown-mega-lg">
                            <a class="nav-link" href="#!" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Pages
                            </a>
                            <ul class="dropdown-menu dropdown-sub-column">
                                <li>
                                    <a class="text-muted" href="contact.html">Contact us</a>
                                </li>
                                <li>
                                    <a class="text-muted" href="about-us.html">About us</a>
                                </li>
                                <li>
                                    <a class="text-muted" href="store-locator.html">Store locator</a>
                                </li>
                                <li>
                                    <a class="text-muted" href="faqs.html">FAQs</a>
                                </li>
                                <li>
                                    <a class="text-muted" href="faqs-2.html">FAQs 2</a>
                                </li>
                                <li>
                                    <a class="text-muted" href="brands.html">Brands</a>
                                </li>
                                <li>
                                    <a class="text-muted" href="404.html">404</a>
                                </li>
                                <li>
                                    <a class="text-muted" href="timeline.html">Timeline</a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown dropdown-mega-lg">
                            <a class="nav-link" href="portfolio-3-columns.html" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Lookbook
                            </a>
                            <ul class="dropdown-menu dropdown-sub-column">
                                <li><a class="text-muted" href="home-lookbook.html">Lookbook Slider</a></li>
                                <li><a class="text-muted" href="home-lookbook-collection.html">Lookbook Section</a></li>
                                <li><a class="text-muted" href="index.html">Lookbook instagram</a></li>
                                <li><a class="text-muted" href="product-detail-description-with-lookbook.html">Lookbook in product</a></li>
                                <li><a class="text-muted" href="blog-post-with-lookbook.html">Lookbook in blog post</a></li>
                                <li><a class="text-muted" href="single-portfolio-with-lookbook.html">Lookbook in portfolio post</a></li>
                                <li><a class="text-muted" href="lookbook-in-page.html">Lookbook in page</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown dropdown-mega-lg">
                            <a class="nav-link" href="#!" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Blog
                            </a>
                            <ul class="dropdown-menu dropdown-sub-column">
                                <li><a class="text-muted" href="blog-grid.html">Grid Layout</a></li>
                                <li><a class="text-muted" href="blog-masonry.html">Masonry Layout</a></li>
                                <li><a class="text-muted" href="blog-left-sidebar.html">Left Sidebar</a></li>
                                <li><a class="text-muted" href="blog-right-sidebar.html">Right Sidebar</a></li>
                                <li><a class="text-muted" href="blog-post-with-product-listing.html">Single Post with Product Listing</a>
                                </li>
                                <li><a class="text-muted" href="blog-post-with-instagram-shop.html">Single Post with Instagram Shop</a>
                                </li>
                                <li><a class="text-muted" href="blog-post-with-instagram-shop.html">Single Post with Categories</a></li>
                                <li><a class="text-muted" href="blog-post-with-lookbook.html">Single Post with lookbook</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="navbar-nav header-offcanvas d-lg-none" tabindex="-1">
                    <!-- close icon -->
                    <a href="#!" class="btn offcanvas-close text-reset" data-bs-dismiss="offcanvas">
                        <i class="las la-times"></i>
                    </a>
                    <div class="offcanvas-body p-0">
                        <ul class="nav nav-pills" id="pills-tab" role="tablist">
                            <li class="nav-item " role="presentation">
                                <button class="nav-link active text-uppercase" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Menu</button>
                            </li>
                            <li class="nav-item col-6 p-0" role="presentation">
                                <button class="nav-link text-uppercase" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">categories</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
                                <div class="accordion" id="accordionPanelsStayOpenExample">
                                    <div class="accordion-item rounded-0">
                                        <h2 class="accordion-header" id="panelsStayOpen-headingOne">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse-05" aria-expanded="false" aria-controls="panelsStayOpen-collapse-05">
                                                Demo
                                            </button>
                                        </h2>
                                        <div id="panelsStayOpen-collapse-05" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-heading-05">
                                            <div class="accordion" id="accordi-05xample">
                                                <div class="accordion-item rounded-0">
                                                    <h2 class="accordion-header" id="heading-05">
                                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-05" aria-expanded="true" aria-controls="collapse-05">
                                                            Home Page
                                                        </button>
                                                    </h2>
                                                    <div id="collapse-05" class="accordion-collapse collapse" aria-labelledby="heading-05" data-bs-parent="#accordi-05xample">
                                                        <!-- Updated data-bs-parent -->
                                                        <ul class="accordion-nav-list list-unstyled mb-0">
                                                            <li>
                                                                <a href="index.html">Home Default</a>
                                                            </li>
                                                            <li>
                                                                <a href="home-classic.html">Home Classic</a>
                                                            </li>
                                                            <li>
                                                                <a href="home-video-banner.html">Home Video Banner</a>
                                                            </li>
                                                            <li>
                                                                <a href="home-categories-links.html">Home Categories
                                                                    Links</a>
                                                            </li>
                                                            <li>
                                                                <a href="home-static-image.html">Home Static Image</a>
                                                            </li>
                                                            <li>
                                                                <a href="home-metro.html">Home Metro</a>
                                                            </li>
                                                            <li>
                                                                <a href="home-lookbook.html">Home Lookbook</a>
                                                            </li>
                                                            <li>
                                                                <a href="home-parallax.html">Home Parallax</a>
                                                            </li>
                                                            <li>
                                                                <a href="home-instagram-shop.html">Home Instagram
                                                                    Shop</a>
                                                            </li>
                                                            <li>
                                                                <a href="home-medical.html">Home Medical
                                                                    <span class="lbc_nav lb_menu_hot ml__5">Hot</span>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="home-fashion9.html">Home Fashion 9</a>
                                                            </li>
                                                            <li>
                                                                <a href="home-lookbook-collection.html">Home Lookbook
                                                                    Collection</a>
                                                            </li>
                                                            <li>
                                                                <a href="home-fashion-simple.html">Home Fashion
                                                                    Simple</a>
                                                            </li>
                                                            <li>
                                                                <a href="home-fashion10.html">Home Fashion 10</a>
                                                            </li>
                                                            <li>
                                                                <a href="home-decor.html">Home Decor</a>
                                                            </li>
                                                            <li>
                                                                <a href="home-decor2.html">Home Decor 2</a>
                                                            </li>
                                                            <li>
                                                                <a href="home-fashion-vertical.html">Home Fashion
                                                                    Vertical</a>
                                                            </li>
                                                            <li>
                                                                <a href="home-electric.html">Home Electric</a>
                                                            </li>
                                                            <li>
                                                                <a href="home-electric-vertical.html">Home Electric
                                                                    Vertical</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="accordion-item border-0 rounded-0">
                                                    <h2 class="accordion-header" id="heading-06">
                                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-06" aria-expanded="true" aria-controls="collapse-06">
                                                            Home Layouts
                                                        </button>
                                                    </h2>
                                                    <div id="collapse-06" class="accordion-collapse collapse" aria-labelledby="heading-06" data-bs-parent="#accordi-05xample">
                                                        <!-- Updated data-bs-parent -->
                                                        <ul class="accordion-nav-list list-unstyled mb-0">
                                                            <li>
                                                                <a href="home-header-01.html">Header Layout 1</a>
                                                            </li>
                                                            <li>
                                                                <a href="home-header-02.html">Header Layout 2</a>
                                                            </li>
                                                            <li>
                                                                <a href="index.html">Header Layout 3</a>
                                                            </li>
                                                            <li>
                                                                <a href="home-header-04.html">Header Layout 4</a>
                                                            </li>
                                                            <li>
                                                                <a href="home-electric.html">Header Layout 5</a>
                                                            </li>
                                                            <li>
                                                                <a href="home-header-06.html">Header Layout 6</a>
                                                            </li>
                                                            <li>
                                                                <a href="home-fashion-vertical.html">Header Layout 7</a>
                                                            </li>
                                                            <li>
                                                                <a href="home-electric-vertical.html">Header Layout
                                                                    8</a>
                                                            </li>
                                                            <li>
                                                                <a href="home-decor.html">Header Transparent</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="panelsStayOpen-headingTwo">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">
                                                Shop
                                            </button>
                                        </h2>
                                        <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingTwo">
                                            <ul class="accordion-nav-list list-unstyled mb-0">
                                                <li>
                                                    <a href="shop.html">Grid Layout</a>
                                                </li>
                                                <li>
                                                    <a href="shop-packery-layout.html">Packery Layout</a>
                                                </li>
                                                <li>
                                                    <a href="shop-masonry-layout.html">Masonry Layout</a>
                                                </li>
                                                <li>
                                                    <a href="shop-full-width-layout.html">Full Width Layout</a>
                                                </li>
                                                <li>
                                                    <a href="shop-1600px-layout.html">1600px Layout</a>
                                                </li>
                                                <li>
                                                    <a href="shop-left-sidebar.html">Left Sidebar</a>
                                                </li>
                                                <li>
                                                    <a href="shop-right-sidebar.html">Right Sidebar</a>
                                                </li>
                                                <li>
                                                    <a href="shop-hidden-sidebar.html">Hidden sidebar</a>
                                                </li>
                                                <li>
                                                    <a href="shop.html">Filters area</a>
                                                </li>
                                                <li>
                                                    <a href="shop-filter-sidebar.html">Filters sidebar</a>
                                                </li>
                                                <li>
                                                    <a href="shopping-cart.html">Shopping cart</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="panelsStayOpen-headingThree">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                                                Product
                                            </button>
                                        </h2>
                                        <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingThree">
                                            <ul class="accordion-nav-list list-unstyled mb-0">
                                                <li>
                                                    <a href="product-detail-external-affiliate.html">External/Affiliate
                                                        Product</a>
                                                </li>
                                                <li>
                                                    <a href="product-detail-simple-product.html">Simple product</a>
                                                </li>
                                                <li>
                                                    <a href="product-detail-layout-01.html">Variable product</a>
                                                </li>
                                                <li>
                                                    <a href="product-detail-grouped-product.html">Group Product
                                                        <span class="badge bg-teal fw-normal rounded-pill">hot</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="product-detail-layout-02.html">Inner Zoom #1</a>
                                                </li>
                                                <li>
                                                    <a href="product-detail-layout-01.html">External Zoom</a>
                                                </li>
                                                <li>
                                                    <a href="product-detail-layout-03.html">Inner Zoom #2</a>
                                                </li>
                                                <li>
                                                    <a href="product-detail-layout-01.html">PhotoSwipe Popup</a>
                                                </li>
                                                <li>
                                                    <a href="product-detail-description-with-product.html">Description
                                                        with product</a>
                                                </li>
                                                <li>
                                                    <a href="product-detail-description-with-instagram-shop.html">Description
                                                        with
                                                        instagram shop</a>
                                                </li>
                                                <li>
                                                    <a href="product-detail-product-video.html">Product video
                                                        <span class="badge bg-teal fw-normal rounded-pill">hot</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="product-detail-3d-ar-models.html">Product 3D, AR models
                                                        <span class="badge bg-teal fw-normal rounded-pill">hot</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <a href="shop-filter-sidebar.html" class="pill-item col-6 p-0" role="presentation">
                                        <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Sale</button>
                                    </a>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="panelsStayOpen-headingFour">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseFour" aria-expanded="false" aria-controls="panelsStayOpen-collapseFour">
                                                Portfolio
                                            </button>
                                        </h2>
                                        <div id="panelsStayOpen-collapseFour" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingFour">
                                            <ul class="accordion-nav-list list-unstyled mb-0">
                                                <li>
                                                    <a href="portfolio.html">Portfolio 2 Columns</a>
                                                </li>
                                                <li>
                                                    <a href="portfolio-3-columns.html">Portfolio 3 Columns</a>
                                                </li>
                                                <li>
                                                    <a href="portfolio-4-columns.html">Portfolio 4 Columns</a>
                                                </li>
                                                <li>
                                                    <a href="single-portfolio-with-shop.html">Single Portfolio With
                                                        Shop</a>
                                                </li>
                                                <li>
                                                    <a href="single-portfolio-with-lookbook.html">Single Portfolio With
                                                        Lookbook</a>
                                                </li>
                                                <li>
                                                    <a href="single-portfolio-with-lookbook.html">Single Portfolio With
                                                        Lookbook</a>

                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="panelsStayOpen-headingFive">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseFive" aria-expanded="false" aria-controls="panelsStayOpen-collapseFive">
                                                Lookbook
                                            </button>
                                        </h2>
                                        <div id="panelsStayOpen-collapseFive" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingFive">
                                            <ul class="accordion-nav-list list-unstyled mb-0">
                                                <li>
                                                    <a href="home-lookbook.html">Lookbook Slider</a>
                                                </li>
                                                <li>
                                                    <a href="portfolio-3-columns.html">Portfolio 3 Columns</a>
                                                </li>
                                                <li>
                                                    <a href="home-lookbook-collection.html">Lookbook Section</a>
                                                </li>
                                                <li>
                                                    <a href="index.html">Lookbook instagram</a>
                                                </li>
                                                <li>
                                                    <a href="product-detail-description-with-lookbook.html">Lookbook in
                                                        product</a>
                                                </li>
                                                <li>
                                                    <a href="single-portfolio-with-lookbook.html">Lookbook in portfolio
                                                        post</a>
                                                </li>
                                                <li>
                                                    <a href="lookbook-in-page.html">Lookbook in page</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="panelsStayOpen-headingsix">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapsesix" aria-expanded="false" aria-controls="panelsStayOpen-collapsesix">
                                                Pages
                                            </button>
                                        </h2>
                                        <div id="panelsStayOpen-collapsesix" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingsix">
                                            <ul class="accordion-nav-list list-unstyled mb-0">
                                                <li>
                                                    <a href="contact.html">Contact us</a>
                                                </li>
                                                <li>
                                                    <a href="about-us.html">About us</a>
                                                </li>
                                                <li>
                                                    <a href="store-locator.html">Store locator</a>
                                                </li>
                                                <li>
                                                    <a href="faqs.html">FAQs</a>
                                                </li>
                                                <li>
                                                    <a href="faqs-2.html">FAQs 2</a>
                                                </li>
                                                <li>
                                                    <a href="brands.html">Brands</a>
                                                </li>
                                                <li>
                                                    <a href="404.html">404</a>
                                                </li>
                                                <li>
                                                    <a href="timeline.html">Timeline</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="panelsStayOpen-headingseven">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseseven" aria-expanded="false" aria-controls="panelsStayOpen-collapseseven">
                                                Blog
                                            </button>
                                        </h2>
                                        <div id="panelsStayOpen-collapseseven" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingseven">
                                            <ul class="accordion-nav-list list-unstyled mb-0">
                                                <li><a href="blog-grid.html">Grid Layout</a></li>
                                                <li><a href="blog-masonry.html">Masonry Layout</a></li>
                                                <li><a href="blog-left-sidebar.html">Left Sidebar</a></li>
                                                <li><a href="blog-right-sidebar.html">Right Sidebar</a></li>
                                                <li><a href="blog-post-with-product-listing.html">Single Post with Product Listing</a>
                                                </li>
                                                <li><a href="blog-post-with-instagram-shop.html">Single Post with Instagram Shop</a>
                                                </li>
                                                <li><a href="blog-post-with-instagram-shop.html">Single Post with Categories</a></li>
                                                <li><a href="blog-post-with-lookbook.html">Single Post with lookbook</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <a href="wishlist.html" class="pill-item col-6 p-0" role="presentation">
                                        <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false"> <i class="iccl iccl-heart fw-medium me-1"></i> Wishlist</button>
                                    </a>
                                    <a href="#!" class="pill-item col-6 p-0" role="presentation" data-bs-toggle="modal" data-bs-target="#searchModal">
                                        <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false"> <i class="iccl iccl-search fw-medium me-1"></i> Search</button>
                                    </a>
                                    <a href="#accountOffcanvas" data-bs-toggle="offcanvas" class="pill-item col-6 p-0" role="presentation">
                                        <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false"> <i class="iccl iccl-user fw-medium me-1"></i> Login /
                                            Register</button>
                                            
                                    </a>

                                    <div class="pill-item col-6 p-0 w-100" role="presentation">
                                        <button class="nav-link border-0" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Need Help?</button>
                                        <ul class="list-unstyled need-help mb-0">
                                            <li>
                                                <a href="#!" class="d-flex align-items-center">
                                                    <i class="pegk pe-7s-call me-1"></i>
                                                    <p class="mb-0">+01 23456789</p>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="mailto:claue@domain.com" class="d-flex mt-2 align-items-center">
                                                    <i class="pegk pe-7s-mail fwb me-2"></i>
                                                    <p class="mb-0">claue@domain.com</p>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="panelsStayOpen-headingSeven">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseSeven" aria-expanded="false" aria-controls="panelsStayOpen-collapseSeven">
                                                USD
                                            </button>
                                        </h2>
                                        <div id="panelsStayOpen-collapseSeven" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingSeven">
                                            <ul class="accordion-nav-list list-unstyled mb-0">
                                                <li>
                                                    <a href="#!">
                                                        <img src="/theme/images/svg/usd.svg" class="map-img" alt="">AUD
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#!">
                                                        <img src="/theme/images/svg/usd.svg" class="map-img" alt="">CAD
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#!">
                                                        <img src="/theme/images/svg/usd.svg" class="map-img" alt="">DKK
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#!">
                                                        <img src="/theme/images/svg/usd.svg" class="map-img" alt="">DKK
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#!">
                                                        <img src="/theme/images/svg/usd.svg" class="map-img" alt="">EUR
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#!">
                                                        <img src="/theme/images/svg/usd.svg" class="map-img" alt="">GBP
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#!">
                                                        <img src="/theme/images/svg/usd.svg" class="map-img" alt="">USD
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
                                <ul class="accordion-nav-list accordion-navs-list list-unstyled mb-0">
                                    <li>
                                        <a href="#"><i class="las la-female me-2"></i>Women�s Clothing</a>
                                    </li>
                                    <li>
                                        <a href="#"><i class="las la-male me-2"></i>Men�s Clothing</a>
                                    </li>
                                    <li>
                                        <a href="#"><i class="las la-clock me-2"></i>Watches</a>
                                    </li>
                                    <li>
                                        <a href="#"><i class="las la-glasses me-2"></i>Accessories</a>
                                    </li>
                                    <li>
                                        <a href="#"><i class="las la-camera-retro me-2"></i>Electric</a>
                                    </li>
                                    <li>
                                        <a href="#"><i class="las la-shoe-prints me-2"></i>Shoes</a>
                                    </li>
                                    <li>
                                        <a href="#"><i class="las la-gem me-2"></i>Jewellery</a>
                                    </li>
                                    <li>
                                        <a href="#"><i class="las la-tshirt me-2"></i>T-Shirt</a>
                                    </li>
                                    <li>
                                        <a href="#"><i class="las la-child me-2"></i>Toys, Kids &amp; Baby</a>
                                    </li>
                                    <li>
                                        <a href="#"><i class="las la-chair me-2"></i>Decor</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="topbar-toolbar d-flex align-items-center gap-3">
                <a data-bs-toggle="offcanvas" href="#searchOffcanvas" aria-controls="searchOffcanvas"><i class="iccl iccl-search"></i></a>
                <a class="d-md-block d-none" data-bs-toggle="offcanvas" href="#accountOffcanvas" aria-controls="accountOffcanvas"><i class="iccl iccl-user"></i></a>
                <a class="d-md-block d-none" href="wishlist.html"><i class="iccl iccl-heart"></i><span class="tcount bg-dark text-white rounded-circle d-flex align-items-center justify-content-center">3</span></a>
                <a data-bs-toggle="offcanvas" href="#shoppingCartOffcanvas" aria-controls="shoppingCartOffcanvas"><i class="iccl iccl-cart"></i><span class="tcount bg-dark text-white rounded-circle d-flex align-items-center justify-content-center">5</span></a>
            </div>
    </nav>
</div>

<div class="backdrop-shadow d-none"></div><div>
    <!-- main slide -->
@yield('content')
    <footer class="footer bg-light">
        <div class="container">
            <div class="row accordion" id="footer-accordion">
                <div class="col-md-4 col-xl-3 mb-2 footer-accordion-item accordion-item">
                    <button class="accordion-button footer-accordion-button collapsed px-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                        <h5>Get in touch</h5>
                    </button>
                    <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                        <a href="#!">
                            <img src="/theme/images/svg/kalles.svg" alt="" height="29">
                        </a>
    
                        <div class="mt-4 pt-2">
                            <p class="d-flex align-items-start text-muted gap-2">
                                <i class="pegk pe-7s-map-marker fs-24"></i>
                                <span>184 Main Rd E, St Albans <br> <span class="pl__30">VIC 3021, Australia</span></span>
                            </p>
                            <p class="d-flex align-items-start text-muted gap-2">
                                <i class="pegk pe-7s-mail fs-24"></i>
                                <a href="mailto:contact@company.com" class="text-reset">contact@company.com</a>
                            </p>
                            <p class="d-flex align-items-start text-muted gap-2">
                                <i class="pegk pe-7s-call fs-24"></i>
                                <span>+001 2233 456 </span>
                            </p>
                            <div class="footer-social d-flex align-items-center gap-4 mt-4">
                                <a href="https://www.facebook.com" class="d-inline-block">
                                    <i class="facl facl-facebook"></i>
                                </a>
                                <a href="https://twitter.com" class="d-inline-block">
                                    <i class="facl facl-twitter"></i>
                                </a>
                                <a href="https://www.instagram.com" class="d-inline-block">
                                    <i class="facl facl-instagram"></i>
                                </a>
                                <a href="https://www.linkedin.com" class="d-inline-block">
                                    <i class="facl facl-linkedin"></i>
                                </a>
                                <a href="https://www.pinterest.com" class="d-inline-block">
                                    <i class="facl facl-pinterest"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-xl-2 mb-2 accordion-item footer-accordion-item">
                    <button class="accordion-button footer-accordion-button px-0 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        <h5>Categories</h5>
                    </button>
                    <h5 class="fw-medium d-none d-md-block">Categories</h5>
                    <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                        <div class="mt-md-4 pt-md-2">
                            <ul class="menu list-unstyled">
                                <li class="menu-item">
                                    <a href="shop-filter-sidebar.html" class="text-muted">Men</a>
                                </li>
                                <li class="menu-item">
                                    <a href="shop-filter-sidebar.html" class="text-muted">Women</a>
                                </li>
                                <li class="menu-item">
                                    <a href="shop-1600px-layout.html" class="text-muted">Accessories</a>
                                </li>
                                <li class="menu-item">
                                    <a href="shop-1600px-layout.html" class="text-muted">Shoes</a>
                                </li>
                                <li class="menu-item">
                                    <a href="shop-1600px-layout.html" class="text-muted">Denim</a>
                                </li>
                                <li class="menu-item">
                                    <a href="shop-1600px-layout.html" class="text-muted">Dress</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div><!--end col-->
                <div class="col-md-3 col-xl-2 mb-2 accordion-item footer-accordion-item">
                    <button class="accordion-button footer-accordion-button px-0 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        <h5>Infomation</h5>
                    </button>
                    <h5 class="fw-medium d-none d-md-block">Infomation</h5>
                    <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                        <div class="mt-md-4 pt-md-2">
                            <ul class="menu list-unstyled">
                                <li class="menu-item">
                                    <a href="#!" class="text-muted">About Us</a>
                                </li>
                                <li class="menu-item">
                                    <a href="#!" class="text-muted">Contact Us</a>
                                </li>
                                <li class="menu-item">
                                    <a href="#!" class="text-muted">Terms &amp; Conditions</a>
                                </li>
                                <li class="menu-item">
                                    <a href="#!" class="text-muted">Returns &amp; Exchanges</a>
                                </li>
                                <li class="menu-item">
                                    <a href="#!" class="text-muted">Shipping &amp; Delivery</a>
                                </li>
                                <li class="menu-item">
                                    <a href="#!" class="text-muted">Privacy Policy</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div><!--end col-->
                <div class="col-md-3 col-xl-2 mb-2 accordion-item footer-accordion-item">
                    <button class="accordion-button footer-accordion-button px-0 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                        <h5>Useful links</h5>
                    </button>
                    <h5 class="fw-medium d-none d-md-block">Useful links</h5>
                    <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                        <div class="mt-md-4 pt-md-2">
                            <ul class="menu list-unstyled">
                                <li class="menu-item">
                                    <a href="#!" class="text-muted">Store Location</a>
                                </li>
                                <li class="menu-item">
                                    <a href="#!" class="text-muted">Latest News</a>
                                </li>
                                <li class="menu-item">
                                    <a href="#!" class="text-muted">My Account</a>
                                </li>
                                <li class="menu-item">
                                    <a href="#!" class="text-muted">Size Guide</a>
                                </li>
                                <li class="menu-item">
                                    <a href="#!" class="text-muted">FAQs 2</a>
                                </li>
                                <li class="menu-item">
                                    <a href="#!" class="text-muted">FAQs</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div><!--end col-->
                <div class="col-md-10 col-xl-3 col-lg-5 mb-2 accordion-item footer-accordion-item">
                    <button class="accordion-button footer-accordion-button px-0 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                        <h5>Newsletter Signup</h5>
                    </button>
                    <h5 class="fw-medium d-none d-md-block">Newsletter Signup</h5>
                    <div id="collapseFive" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                        <div class="mt-md-4 pt-md-2">
                            <p class="text-muted">Subscribe to our newsletter and get 10% off your first purchase</p>
                            <form id="contact_form" class="d-block">
                                <div class="footer-subscribe position-relative">
                                    <input type="email" name="email" placeholder="Your email address" value="" class="border-dark input-text form-control w-100 rounded-pill" required="required">
                                    <button type="submit" class="btn btn-dark position-absolute rounded-pill">
                                        <span>Subscribe</span>
                                    </button>
                                </div>
                            </form>
                            <div class="mt-3">
                                <img src="/theme/images/payment2.png" alt="">
                            </div>
                        </div>
                    </div>
                </div><!--end col-->
            </div><!--end row-->
        </div><!--end container-->
    </footer>
    
    <div class="footer-alt mb-5 mb-lg-0 py-4">
        <div class="container">
            <div class="row justify-between text-center text-lg-start">
                <div class="col-lg-6 text-muted">
                    Copyright �
                    <script>document.write(new Date().getFullYear())</script> <a href="#!" class="link-info">Kalles</a> all
                    rights reserved. Powered by <span class="text-dark">SRBThemes</span>
                </div>
                <div class="col-lg-6">
                    <ul id="footer-menu" class="mt-2 mt-lg-0 list-unstyled d-flex align-items-center mb-0 justify-content-lg-end justify-content-center flex-wrap">
                        <li class="menu-item ">
                            <a href="shop-filter-sidebar.html" class="text-muted mx-2">Shop</a>
                        </li>
                        <li class="menu-item ">
                            <a href="about-us.html" class="text-muted mx-2">About Us</a>
                        </li>
                        <li class="menu-item ">
                            <a href="contact.html" class="text-muted mx-2">Contact</a>
                        </li>
                        <li class="menu-item ">
                            <a href="blog-grid.html" class="text-muted mx-2">Blog</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="responsive-footer d-lg-none position-fixed bottom-0 start-0 end-0 d-flex align-items-center justify-content-around gap-3 py-2 z-2">
        <a href="shop-filter-sidebar.html">
            <div class="toolbar_icon text-center"></div>
            <h6 class="fw-medium fs-12 pt-2 mb-0">Shop</h6>
        </a>
        <a href="wishlist.html">
            <div class="toolbar_icon text-center icon3 position-relative">
                <span class="position-absolute top-0 tag">3</span>
            </div>
            <h6 class="fw-medium fs-12 pt-2 mb-0">Wishlist</h6>
        </a>
        <a data-bs-toggle="offcanvas" href="#shoppingCartOffcanvas" aria-controls="shoppingCartOffcanvas">
            <div class="toolbar_icon text-center icon4">
                <span class="position-absolute top-0 tag">5</span>
            </div>
            <h6 class="fw-medium fs-12 pt-2 ms-2 mb-0">Cart</h6>
        </a>
        <a data-bs-toggle="offcanvas" href="#accountOffcanvas">
            <div class="toolbar_icon text-center icon5"></div>
            <h6 class="fw-medium fs-12 pt-2 mb-0">Account</h6>
        </a>
        <a data-bs-toggle="offcanvas" href="#searchOffcanvas" aria-controls="searchOffcanvas">
            <div class="toolbar_icon text-center icon6"></div>
            <h6 class="fw-medium fs-12 pt-2 mb-0">Search</h6>
        </a>
    </div>
    
    <a href="#" x-on:click.prevent="
          window.scrollTo({
             top: 0,
             behavior: 'smooth'
          });
       " class="position-fixed bg-white border rounded d-flex align-items-center justify-content-center shadow" id="nt_backtop">
        <i class="pr pegk pe-7s-angle-up"></i>
    </a>
    
     
    
    <div class="backdrop-shadow d-none"></div>
    <div id="pop-main" class="d-none d-lg-block">
        <div class="pop-up d-none">
            <div class="pop-card position-relative d-flex align-items-center">
                <div class="popup-img">
                    <a href="#!">
                        <img src="/theme/images/popup/pr-1.jpg" alt="">
                    </a>
                </div>
                <div class="text-start popup-text">
                    <p class="mb-0">Sara (Montana) <span class="text-muted">purchased</span></p>
                    <a href="#!" class="text-uppercase fs-14">CROP TOP T-SHIRT</a>
                    <div class="fs-12">
                        <span class="text-muted">40 minutes ago</span>
                        <svg xmlns="http://www.w3.org/2000/svg" height="13" class="text-success ms-2 me-1" viewBox="0 0 512 512" fill="currentColor"><path d="M466.5 83.71l-192-80c-5.875-2.5-12.16-3.703-18.44-3.703S243.5 1.203 237.6 3.703L45.61 83.71C27.73 91.08 16 108.6 16 127.1C16 385.4 205.4 512 255.9 512C305.2 512 496 387.3 496 127.1C496 108.6 484.3 91.08 466.5 83.71zM463.9 128.3c0 225.3-166.2 351.7-207.8 351.7C213.3 479.1 48 352.2 48 128c0-6.5 3.875-12.25 9.75-14.75l192-80c1.973-.8275 4.109-1.266 6.258-1.266c2.071 0 4.154 .4072 6.117 1.266l192 80C463.3 117.1 463.9 125.8 463.9 128.3zM336 181.3c-4.094 0-8.188 1.562-11.31 4.688L229.3 281.4L187.3 239.4C184.2 236.2 180.1 234.7 176 234.7c-9.139 0-16 7.473-16 16c0 4.094 1.562 8.188 4.688 11.31l53.34 53.33C221.2 318.4 225.3 320 229.3 320s8.188-1.562 11.31-4.688l106.7-106.7C350.4 205.5 352 201.4 352 197.3C352 188.8 345.1 181.3 336 181.3z"></path></svg>
                        <span>Verified</span>
                    </div>
                </div>
                <a href="#!" class="close-btn">
                    <i class="iccl iccl-x2"></i>
                </a>
                <a href="#!" class="view-btn">
                    <i class="iccl iccl-eye2"></i>
                </a>
            </div>
        </div>
        <div class="pop-up d-none">
            <div class="pop-card position-relative d-flex align-items-center">
                <div class="popup-img">
                    <a href="#!">
                        <img src="/theme/images/popup/pr-2.jpg" alt="">
                    </a>
                </div>
                <div class="text-start popup-text">
                    <p class="mb-0">Kate (Georgia) <span class="text-muted">purchased</span></p>
                    <a href="#!" class="text-uppercase fs-14">Grey Beanie</a>
                    <div class="fs-12">
                        <span class="text-muted">25 minutes ago</span>
                        <svg xmlns="http://www.w3.org/2000/svg" height="13" class="text-success ms-2 me-1" viewBox="0 0 512 512" fill="currentColor"><path d="M466.5 83.71l-192-80c-5.875-2.5-12.16-3.703-18.44-3.703S243.5 1.203 237.6 3.703L45.61 83.71C27.73 91.08 16 108.6 16 127.1C16 385.4 205.4 512 255.9 512C305.2 512 496 387.3 496 127.1C496 108.6 484.3 91.08 466.5 83.71zM463.9 128.3c0 225.3-166.2 351.7-207.8 351.7C213.3 479.1 48 352.2 48 128c0-6.5 3.875-12.25 9.75-14.75l192-80c1.973-.8275 4.109-1.266 6.258-1.266c2.071 0 4.154 .4072 6.117 1.266l192 80C463.3 117.1 463.9 125.8 463.9 128.3zM336 181.3c-4.094 0-8.188 1.562-11.31 4.688L229.3 281.4L187.3 239.4C184.2 236.2 180.1 234.7 176 234.7c-9.139 0-16 7.473-16 16c0 4.094 1.562 8.188 4.688 11.31l53.34 53.33C221.2 318.4 225.3 320 229.3 320s8.188-1.562 11.31-4.688l106.7-106.7C350.4 205.5 352 201.4 352 197.3C352 188.8 345.1 181.3 336 181.3z"></path></svg>
                        <span>Verified</span>
                    </div>
                </div>
                <a href="#!" class="close-btn">
                    <i class="iccl iccl-x2"></i>
                </a>
                <a href="#!" class="view-btn">
                    <i class="iccl iccl-eye2"></i>
                </a>
            </div>
        </div>
        <div class="pop-up d-none">
            <div class="pop-card position-relative d-flex align-items-center">
                <div class="popup-img">
                    <a href="#!">
                        <img src="/theme/images/popup/pr-3.jpg" alt="">
                    </a>
                </div>
                <div class="text-start popup-text">
                    <p class="mb-0">Hau (California) <span class="text-muted">purchased</span></p>
                    <a href="#!" class="text-uppercase fs-14">Grey Beanie</a>
                    <div class="fs-12">
                        <span class="text-muted">2 hours ago</span>
                        <svg xmlns="http://www.w3.org/2000/svg" height="13" class="text-success ms-2 me-1" viewBox="0 0 512 512" fill="currentColor"><path d="M466.5 83.71l-192-80c-5.875-2.5-12.16-3.703-18.44-3.703S243.5 1.203 237.6 3.703L45.61 83.71C27.73 91.08 16 108.6 16 127.1C16 385.4 205.4 512 255.9 512C305.2 512 496 387.3 496 127.1C496 108.6 484.3 91.08 466.5 83.71zM463.9 128.3c0 225.3-166.2 351.7-207.8 351.7C213.3 479.1 48 352.2 48 128c0-6.5 3.875-12.25 9.75-14.75l192-80c1.973-.8275 4.109-1.266 6.258-1.266c2.071 0 4.154 .4072 6.117 1.266l192 80C463.3 117.1 463.9 125.8 463.9 128.3zM336 181.3c-4.094 0-8.188 1.562-11.31 4.688L229.3 281.4L187.3 239.4C184.2 236.2 180.1 234.7 176 234.7c-9.139 0-16 7.473-16 16c0 4.094 1.562 8.188 4.688 11.31l53.34 53.33C221.2 318.4 225.3 320 229.3 320s8.188-1.562 11.31-4.688l106.7-106.7C350.4 205.5 352 201.4 352 197.3C352 188.8 345.1 181.3 336 181.3z"></path></svg>
                        <span>Verified</span>
                    </div>
                </div>
                <a href="#!" class="close-btn">
                    <i class="iccl iccl-x2"></i>
                </a>
                <a href="#!" class="view-btn">
                    <i class="iccl iccl-eye2"></i>
                </a>
            </div>
        </div>
        <div class="pop-up d-none">
            <div class="pop-card position-relative d-flex align-items-center">
                <div class="popup-img">
                    <a href="#!">
                        <img src="/theme/images/about-us/mem-02.jpeg" alt="">
                    </a>
                </div>
                <div class="text-start popup-text">
                    <p class="mb-0">Hau (California) <span class="text-muted">purchased</span></p>
                    <a href="#!" class="text-uppercase">Grey Beanie</a>
                    <div class="fs-13">
                        <span class="text-muted">2 hours ago</span>
                        <i class="iccl iccl-verifyd text-success"></i>
                        <span>Verified</span>
                    </div>
                </div>
                <a href="#!" class="close-btn">
                    <i class="iccl iccl-x2"></i>
                </a>
                <a href="#!" class="view-btn">
                    <i class="iccl iccl-eye2"></i>
                </a>
            </div>
        </div>
    </div>
    <button type="button" class="btn btn-primary d-none" data-bs-toggle="modal" data-bs-target="#CODE15OFF">
        Launch demo modal
    </button>

</div>

<!-- modal -->
<!-- card model -->
<div class="modal fade modal-overl mx-auto" id="cardModal" tabindex="-1" role="dialog" aria-labelledby="cardLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-sm-down modal-dialog-centered h-auto" role="document">
        <div class="modal-content position-relative p-1 mx-auto" style="max-width: 340px;">
            <div class="modal-body">
                <a href="#!" data-bs-dismiss="modal" class="fs-35 close position-absolute top-0 end-0" aria-label="Close">
                    <i class="pe-7s-close pegk"></i>
                </a>

                <div class="row">
                    <div class="col-4">
                        <img src="/theme/images/quick_shop/p_qs_01.jpg" class="img-fluid" alt="">
                    </div>
                    <div class="col-8">
                        <h6><a class="cd chp" href="product-detail-layout-01.html">Cluse La Boheme Rose Gold</a></h6>
                        <div class="d-flex mb-2 align-items-center">
                            <div class="fs-16  me-1">
                                <del class="text-muted">$60.00</del>
                                <span class="text-danger">$45.00</span>
                            </div>
                            <span class="bg-danger text-white p-1">-25%</span>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <!-- color -->
                        <div x-data="{ color: 'Grey' }">
                            <h6 class="text-uppercase fw-bold mb-3">Color: <span x-text="color"></span></h6>
                            <div class="product-color-list mt-2 gap-2 d-flex align-items-center justify-content-center">
                                <a href="#!" class="d-inline-block bg_color_pink rounded-circle square-xs" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Pink" x-on:click.prevent="color = 'Pink'; $event.target.classList.add('active'); $event.target.nextElementSibling.classList.remove('active');"></a>
                                <a href="#!" class="d-inline-block bg-secondary bg-opacity-50 rounded-circle active square-xs" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Grey" x-on:click.prevent="color = 'Grey'; $event.target.classList.add('active'); $event.target.nextElementSibling.classList.remove('active');"></a>
                                <a href="#!" class="d-inline-block bg-dark rounded-circle square-xs" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Black" x-on:click.prevent="color = 'Black'; $event.target.classList.add('active'); $event.target.previousElementSibling.classList.remove('active');"></a>
                            </div>
                        </div>
                        <!-- size -->
                        <div x-data="{ size: 'M', color: '#fff' }" class="mb-4 pt-2">
                            <h6 class="text-uppercase fw-bold mt-3">Size: <span x-text="size"></span></h6>
                            <div class="product-color-list size mt-2 gap-2 d-flex align-items-center justify-content-center">
                                <a href="#!" class="d-inline-block rounded-circle square-xs d-flex align-items-center justify-content-center" :class="{ 'active': size === 'S' }" x-on:click.prevent="size = 'S';">S</a>
                                <a href="#!" class="d-inline-block rounded-circle square-xs d-flex align-items-center justify-content-center" :class="{ 'active': size === 'M' }" x-on:click.prevent="size = 'M';">M</a>
                                <a href="#!" class="d-inline-block rounded-circle square-xs d-flex align-items-center justify-content-center" :class="{ 'active': size === 'L' }" x-on:click.prevent="size = 'L';">L</a>
                            </div>
                        </div>
                        <!-- - + -->
                        <div class="input-step border border-dark rounded-pill">
                            <button type="button" class="minus material-shadow text-dark fw-bold">�</button>
                            <input type="number" class="product-quantity fw-bold fs-6" value="1" min="0" max="100">
                            <button type="button" class="plus material-shadow text-dark fw-bold">+</button>
                        </div>
                        <div class="my-3">
                            <button type="submit" class="btn w-100 btn-teal rounded-pill text-uppercase px-4 fw-semibold">Add to
                                cart</button>
                        </div>
                        <a href="product-detail-layout-01.html" class="btn fs-16 fw-semibold detail_link">View full details<i class="facl facl-right ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- instahram sec model -->
<div class="modal fade modal-overl" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content overflow-hidden">
            <div class="modal-body p-0">
                <button type="button" class="btn-close position-absolute end-0 top-0 m-2" style="z-index: 99;" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="row">
                    <div class="col-md-7">
                        <div class="images">
                            <div style="--swiper-navigation-color: #fff; --swiper-pagination-color: #fff" class="swiper productJewellry">
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide topbar-product-card">
                                        <div class="position-relative overflow-hidden">
                                            <span class="new-label bg-danger text-white rounded-circle"> -34% </span>
                                            <img src="/theme/images/quick_view/pr-01.jpg" class="product-view-img w-100 object-fit-cover" />
                                        </div>
                                    </div>
                                    <div class="swiper-slide topbar-product-card">
                                        <div class="position-relative overflow-hidden">
                                            <span class="new-label bg-danger text-white rounded-circle"> -34% </span>
                                            <img src="/theme/images/quick_view/pr-02.jpg" class="product-view-img w-100 object-fit-cover" />
                                        </div>
                                    </div>
                                    <div class="swiper-slide topbar-product-card">
                                        <div class="position-relative overflow-hidden">
                                            <span class="new-label bg-danger text-white rounded-circle"> -34% </span>
                                            <img src="/theme/images/quick_view/pr-03.jpg" class="product-view-img w-100 object-fit-cover" />
                                        </div>
                                    </div>
                                    <div class="swiper-slide topbar-product-card">
                                        <div class="position-relative overflow-hidden">
                                            <span class="new-label bg-danger text-white rounded-circle"> -34% </span>
                                            <img src="/theme/images/quick_view/pr-04.jpg" class="product-view-img w-100 object-fit-cover" />
                                        </div>
                                    </div>
                                    <div class="swiper-slide topbar-product-card">
                                        <div class="position-relative overflow-hidden">
                                            <span class="new-label bg-danger text-white rounded-circle"> -34% </span>
                                            <img src="/theme/images/quick_view/pr-05.jpg" class="product-view-img w-100 object-fit-cover" />
                                        </div>
                                    </div>
                                    <div class="swiper-slide topbar-product-card">
                                        <div class="position-relative overflow-hidden">
                                            <span class="new-label bg-danger text-white rounded-circle"> -34% </span>
                                            <img src="/theme/images/quick_view/pr-06.jpg" class="product-view-img w-100 object-fit-cover" />
                                        </div>
                                    </div>
                                    <div class="swiper-slide topbar-product-card">
                                        <div class="position-relative overflow-hidden">
                                            <span class="new-label bg-danger text-white rounded-circle"> -34% </span>
                                            <img src="/theme/images/quick_view/pr-07.jpg" class="product-view-img w-100 object-fit-cover" />
                                        </div>
                                    </div>
                                    <div class="swiper-slide topbar-product-card">
                                        <div class="position-relative overflow-hidden">
                                            <span class="new-label bg-danger text-white rounded-circle"> -34% </span>
                                            <img src="/theme/images/quick_view/pr-08.jpg" class="product-view-img w-100 object-fit-cover" />
                                        </div>
                                    </div>
                                    <div class="swiper-slide topbar-product-card">
                                        <div class="position-relative overflow-hidden">
                                            <span class="new-label bg-danger text-white rounded-circle"> -34% </span>
                                            <img src="/theme/images/quick_view/pr-09.jpg" class="product-view-img w-100 object-fit-cover" />
                                        </div>
                                    </div>
                                    <div class="swiper-slide topbar-product-card">
                                        <div class="position-relative overflow-hidden">
                                            <span class="new-label bg-danger text-white rounded-circle"> -34% </span>
                                            <img src="/theme/images/quick_view/pr-10.jpg" class="product-view-img w-100 object-fit-cover" />
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-button-next"></div>
                                <div class="swiper-button-prev"></div>
                                <div class="swiper-pagination"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 overflow-y-auto overflow-x-hidden" style="height: 624px;">
                        <div>
                            <div class="pt-30 ps-4 ps-md-0 pe-4">
                                <h6 class="fs-20 mb-2"><a href="product-detail-layout-01.html" class="main_link">La Boh�me
                                        Rose Gold</a></h6>
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
                                    <p class="mb-0 fs-16 text-muted flex-grow-1">
                                        <del>$60.00</del>
                                        <span class="text-danger">$40.00</span>
                                    </p>
                                    <a href="product-detail-layout-01.html" class="text-body flex-shrink-0">
                                        <div class="kalles-rating-result">
                                            <span class="kalles-rating-result__pipe">
                                                <span class="kalles-rating-result__start"></span>
                                                <span class="kalles-rating-result__start"></span>
                                                <span class="kalles-rating-result__start"></span>
                                                <span class="kalles-rating-result__start active"></span>
                                                <span class="kalles-rating-result__start de-active"></span>
                                            </span>
                                            <span class="kalles-rating-result__number">(12 reviews)</span>
                                        </div>
                                    </a>
                                </div>
                                <p class="text-muted">Go kalles this summer with this vintage navy and white striped v-neck
                                    t-shirt from the Nike. Perfect for pairing with denim and white kicks for a stylish
                                    kalles vibe.</p>
                                <div x-data="{ color: 'Pink' }">
                                    <h6 class="text-uppercase mb-3">Color: <span x-text="color"></span></h6>
                                    <div class="product-color-list mt-2 gap-2 d-flex align-items-center">
                                        <a href="#!" class="d-inline-block bg_color_pink rounded-circle active square-xs" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Pink" x-on:click.prevent="color = 'Pink'; $event.target.classList.add('active'); $event.target.nextElementSibling.classList.remove('active');"></a>
                                        <a href="#!" class="d-inline-block bg-dark rounded-circle square-xs" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Black" x-on:click.prevent="color = 'Black'; $event.target.classList.add('active'); $event.target.previousElementSibling.classList.remove('active');"></a>
                                    </div>
                                </div>

                                <div x-data="{ size: 'M' }" class="mt-4 pt-2">
                                    <h6 class="text-uppercase mb-3">Size: <span x-text="size"></span></h6>
                                    <div class="product-color-list size mt-2 gap-2 d-flex align-items-center">
                                        <a href="#!" class="d-inline-block rounded-circle square-xs d-flex align-items-center justify-content-center" :class="{ 'active': size === 'XS' }" x-on:click.prevent="size = 'XS';">XS</a>
                                        <a href="#!" class="d-inline-block rounded-circle square-xs d-flex align-items-center justify-content-center" :class="{ 'active': size === 'S' }" x-on:click.prevent="size = 'S';">S</a>
                                        <a href="#!" class="d-inline-block rounded-circle square-xs d-flex align-items-center justify-content-center" :class="{ 'active': size === 'M' }" x-on:click.prevent="size = 'M';">M</a>
                                    </div>
                                </div>

                                <div class="mt-4 d-flex flex-wrap align-items-center pt-2 gap-2">
                                    <div x-data="{ quantity: 1 }" class="quantity fs-14 position-relative border-dark mb-0">
                                        <input x-bind:value="quantity" type="number" class="input-text text-center" readonly step="1" min="0" max="9999">
                                        <button type="button" class="minus position-absolute start-0 ps-3" x-on:click="quantity > 1 ? quantity-- : null">
                                            <i class="facl facl-minus"></i>
                                        </button>
                                        <button type="button" class="plus position-absolute end-0 pe-3" x-on:click="quantity++">
                                            <i class="facl facl-plus"></i>
                                        </button>
                                    </div>
                                    <button x-data="{ shake: false }" x-init="
            setInterval(() => { 
                shake = true; 
                setTimeout(() => { 
                    shake = false; 
                }, 2000); 
            }, 6000);
        " :class="{ 'animation-shake': shake }" class="btn btn-info text-uppercase rounded-pill min-w-150">
                                        Add to Cart
                                    </button>
                                    <a href="#" class="btn square-40 btn-wishlistadd p-0 fs-16 d-flex align-items-center rounded-pill flex-shrink-0 justify-content-center"><i class="facl facl-heart-o"></i></a>
                                </div>

                                <div class="mt-3">
                                    <img src="/theme/images/trust_img2.png" alt="" class="img-fluid">
                                </div>
                                <div class="mt-4">
                                    <p class="text-muted mb-1"><span class="text-body">SKU:</span> 4540967714955-1</p>
                                    <p class="text-muted mb-1"><span class="text-body">Categories:</span> <a href="#!" class="main_link text-muted">Accessories</a>, <a href="#!" class="main_link text-muted">All</a>, <a href="#!" class="main_link text-muted">Best seller</a>, <a href="#!" class="main_link text-muted">New
                                            Arrival</a>, <a href="#!" class="main_link text-muted">Sale</a>, <a href="#!" class="main_link text-muted">Watches</a>, <a href="#!" class="main_link text-muted">Women</a></p>
                                    <p class="text-muted mb-1"><span class="text-body">Tags:</span> <a href="#!" class="main_link text-muted">Color Black</a>, <a href="#!" class="main_link text-muted">Color
                                            Pink</a>, <a href="#!" class="main_link text-muted">Price $7-$50</a>, <a href="#!" class="main_link text-muted">Vendor Kalles</a>, <a href="#!" class="main_link text-muted">Watch</a>,
                                        <a href="#!" class="main_link text-muted">Women</a>
                                    </p>
                                </div>
                                <div>
                                    <div class="social-share mt-4 mb-3">
                                        <a href="https://www.facebook.com/">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="at-icon at-icon-facebook">
                                                <g>
                                                    <path d="M22 5.16c-.406-.054-1.806-.16-3.43-.16-3.4 0-5.733 1.825-5.733 5.17v2.882H9v3.913h3.837V27h4.604V16.965h3.823l.587-3.913h-4.41v-2.5c0-1.123.347-1.903 2.198-1.903H22V5.16z" fill-rule="evenodd"></path>
                                                </g>
                                            </svg>
                                        </a>
                                        <a href="https://twitter.com/">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="at-icon at-icon-twitter">
                                                <g>
                                                    <path d="M27.996 10.116c-.81.36-1.68.602-2.592.71a4.526 4.526 0 0 0 1.984-2.496 9.037 9.037 0 0 1-2.866 1.095 4.513 4.513 0 0 0-7.69 4.116 12.81 12.81 0 0 1-9.3-4.715 4.49 4.49 0 0 0-.612 2.27 4.51 4.51 0 0 0 2.008 3.755 4.495 4.495 0 0 1-2.044-.564v.057a4.515 4.515 0 0 0 3.62 4.425 4.52 4.52 0 0 1-2.04.077 4.517 4.517 0 0 0 4.217 3.134 9.055 9.055 0 0 1-5.604 1.93A9.18 9.18 0 0 1 6 23.85a12.773 12.773 0 0 0 6.918 2.027c8.3 0 12.84-6.876 12.84-12.84 0-.195-.005-.39-.014-.583a9.172 9.172 0 0 0 2.252-2.336" fill-rule="evenodd"></path>
                                                </g>
                                            </svg>
                                        </a>
                                        <a href="https://www.google.com/gmail/about">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="at-icon at-icon-email kalles-social-media__btn">
                                                <g>
                                                    <g fill-rule="evenodd"></g>
                                                    <path d="M27 22.757c0 1.24-.988 2.243-2.19 2.243H7.19C5.98 25 5 23.994 5 22.757V13.67c0-.556.39-.773.855-.496l8.78 5.238c.782.467 1.95.467 2.73 0l8.78-5.238c.472-.28.855-.063.855.495v9.087z">
                                                    </path>
                                                    <path d="M27 9.243C27 8.006 26.02 7 24.81 7H7.19C5.988 7 5 8.004 5 9.243v.465c0 .554.385 1.232.857 1.514l9.61 5.733c.267.16.8.16 1.067 0l9.61-5.733c.473-.283.856-.96.856-1.514v-.465z">
                                                    </path>
                                                </g>
                                            </svg>
                                        </a>
                                        <a href="https://www.pinterest.com/">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="at-icon at-icon-pinterest_share">
                                                <g>
                                                    <path d="M7 13.252c0 1.81.772 4.45 2.895 5.045.074.014.178.04.252.04.49 0 .772-1.27.772-1.63 0-.428-1.174-1.34-1.174-3.123 0-3.705 3.028-6.33 6.947-6.33 3.37 0 5.863 1.782 5.863 5.058 0 2.446-1.054 7.035-4.468 7.035-1.232 0-2.286-.83-2.286-2.018 0-1.742 1.307-3.43 1.307-5.225 0-1.092-.67-1.977-1.916-1.977-1.692 0-2.732 1.77-2.732 3.165 0 .774.104 1.63.476 2.336-.683 2.736-2.08 6.814-2.08 9.633 0 .87.135 1.728.224 2.6l.134.137.207-.07c2.494-3.178 2.405-3.8 3.533-7.96.61 1.077 2.182 1.658 3.43 1.658 5.254 0 7.614-4.77 7.614-9.067C26 7.987 21.755 5 17.094 5 12.017 5 7 8.15 7 13.252z" fill-rule="evenodd"></path>
                                                </g>
                                            </svg>
                                        </a>
                                        <a href="https://www.messenger.com">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="at-icon at-icon-messenger">
                                                <g>
                                                    <path d="M16 6C9.925 6 5 10.56 5 16.185c0 3.205 1.6 6.065 4.1 7.932V28l3.745-2.056c1 .277 2.058.426 3.155.426 6.075 0 11-4.56 11-10.185C27 10.56 22.075 6 16 6zm1.093 13.716l-2.8-2.988-5.467 2.988 6.013-6.383 2.868 2.988 5.398-2.987-6.013 6.383z" fill-rule="evenodd"></path>
                                                </g>
                                            </svg>
                                        </a>
                                    </div>
                                    <a href="product-detail-layout-01.html" class="fw-medium detail_link ">View full details<i class="facl facl-right ms-1"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- custome header -->
<div class="header-offcanvas offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
    <!-- close icon -->
    <a href="#!" class="btn offcanvas-close text-reset" data-bs-dismiss="offcanvas">
        <i class="las la-times"></i>
    </a>
    <div class="offcanvas-body p-0">
        <ul class="nav nav-pills" id="pills-tab" role="tablist">
            <li class="nav-item " role="presentation">
                <button class="nav-link active text-uppercase" id="pills-menu-tab" data-bs-toggle="pill" data-bs-target="#pills-menu" type="button" role="tab" aria-controls="pills-menu" aria-selected="true">Menu</button>
            </li>
            <li class="nav-item col-6 p-0" role="presentation">
                <button class="nav-link text-uppercase" id="pills-categories-tab" data-bs-toggle="pill" data-bs-target="#pills-categories" type="button" role="tab" aria-controls="pills-categories" aria-selected="false">categories</button>
            </li>
        </ul>
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-menu" role="tabpanel" aria-labelledby="pills-menu-tab" tabindex="0">
                <div class="accordion" id="accordionPanelsStayOpenExample">
                    <div class="accordion-item rounded-0">
                        <h2 class="accordion-header" id="panelsStayOpen-headingOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse-05" aria-expanded="false" aria-controls="panelsStayOpen-collapse-05">
                                Demo
                            </button>
                        </h2>
                        <div id="panelsStayOpen-collapse-05" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-heading-05">
                            <div class="accordion" id="accordi-05xample">
                                <div class="accordion-item rounded-0">
                                    <h2 class="accordion-header" id="heading-05">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-05" aria-expanded="true" aria-controls="collapse-05">
                                            Home Page
                                        </button>
                                    </h2>
                                    <div id="collapse-05" class="accordion-collapse collapse" aria-labelledby="heading-05" data-bs-parent="#accordi-05xample">
                                        <!-- Updated data-bs-parent -->
                                        <ul class="accordion-nav-list list-unstyled mb-0">
                                            <li>
                                                <a href="index.html">Home Default</a>
                                            </li>
                                            <li>
                                                <a href="home-classic.html">Home Classic</a>
                                            </li>
                                            <li>
                                                <a href="home-video-banner.html">Home Video Banner</a>
                                            </li>
                                            <li>
                                                <a href="home-categories-links.html">Home Categories Links</a>
                                            </li>
                                            <li>
                                                <a href="home-static-image.html">Home Static Image</a>
                                            </li>
                                            <li>
                                                <a href="home-metro.html">Home Metro</a>
                                            </li>
                                            <li>
                                                <a href="home-lookbook.html">Home Lookbook</a>
                                            </li>
                                            <li>
                                                <a href="home-parallax.html">Home Parallax</a>
                                            </li>
                                            <li>
                                                <a href="home-instagram-shop.html">Home Instagram Shop</a>
                                            </li>
                                            <li>
                                                <a href="home-medical.html">Home Medical
                                                    <span class="lbc_nav lb_menu_hot ml__5">Hot</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="home-fashion9.html">Home Fashion 9</a>
                                            </li>
                                            <li>
                                                <a href="home-lookbook-collection.html">Home Lookbook Collection</a>
                                            </li>
                                            <li>
                                                <a href="home-fashion-simple.html">Home Fashion Simple</a>
                                            </li>
                                            <li>
                                                <a href="home-fashion10.html">Home Fashion 10</a>
                                            </li>
                                            <li>
                                                <a href="home-decor.html">Home Decor</a>
                                            </li>
                                            <li>
                                                <a href="home-decor2.html">Home Decor 2</a>
                                            </li>
                                            <li>
                                                <a href="home-fashion-vertical.html">Home Fashion Vertical</a>
                                            </li>
                                            <li>
                                                <a href="home-electric.html">Home Electric</a>
                                            </li>
                                            <li>
                                                <a href="home-electric-vertical.html">Home Electric Vertical</a>
                                            </li>
                                            <li>
                                                <a href="home-digital.html">Home Digital</a>
                                            </li>
                                            <li>
                                                <a href="home-one-product-store.html">One Product Store</a>
                                            </li>
                                            <li>
                                                <a href="home-handmade.html">Home Handmade</a>
                                            </li>
                                            <li>
                                                <a href="home-fashion-trend.html">Home Fashion Trend</a>
                                            </li>
                                            <li>
                                                <a href="home-kids.html">Home Kids</a>
                                            </li>
                                            <li>
                                                <a href="home-handmade.html">Home Handmade</a>
                                            </li>
                                            <li>
                                                <a href="home-sport.html">Home Sport</a>
                                            </li>
                                            <li>
                                                <a href="home-jewelry.html">Home Jewelry</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="accordion-item border-0 rounded-0">
                                    <h2 class="accordion-header" id="heading-06">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-06" aria-expanded="true" aria-controls="collapse-06">
                                            Home Layouts
                                        </button>
                                    </h2>
                                    <div id="collapse-06" class="accordion-collapse collapse" aria-labelledby="heading-06" data-bs-parent="#accordi-05xample">
                                        <!-- Updated data-bs-parent -->
                                        <ul class="accordion-nav-list list-unstyled mb-0">
                                            <li>
                                                <a href="home-header-01.html">Header Layout 1</a>
                                            </li>
                                            <li>
                                                <a href="home-header-02.html">Header Layout 2</a>
                                            </li>
                                            <li>
                                                <a href="index.html">Header Layout 3</a>
                                            </li>
                                            <li>
                                                <a href="home-header-04.html">Header Layout 4</a>
                                            </li>
                                            <li>
                                                <a href="home-electric.html">Header Layout 5</a>
                                            </li>
                                            <li>
                                                <a href="home-header-06.html">Header Layout 6</a>
                                            </li>
                                            <li>
                                                <a href="home-fashion-vertical.html">Header Layout 7</a>
                                            </li>
                                            <li>
                                                <a href="home-electric-vertical.html">Header Layout 8</a>
                                            </li>
                                            <li>
                                                <a href="home-decor.html">Header Transparent</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="panelsStayOpen-headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">
                                Shop
                            </button>
                        </h2>
                        <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingTwo">
                            <ul class="accordion-nav-list list-unstyled mb-0">
                                <li>
                                    <a href="shop.html">Grid Layout</a>
                                </li>
                                <li>
                                    <a href="shop-packery-layout.html">Packery Layout</a>
                                </li>
                                <li>
                                    <a href="shop-masonry-layout.html">Masonry Layout</a>
                                </li>
                                <li>
                                    <a href="shop-full-width-layout.html">Full Width Layout</a>
                                </li>
                                <li>
                                    <a href="shop-1600px-layout.html">1600px Layout</a>
                                </li>
                                <li>
                                    <a href="shop-left-sidebar.html">Left Sidebar</a>
                                </li>
                                <li>
                                    <a href="shop-right-sidebar.html">Right Sidebar</a>
                                </li>
                                <li>
                                    <a href="shop-hidden-sidebar.html">Hidden sidebar</a>
                                </li>
                                <li>
                                    <a href="shop.html">Filters area</a>
                                </li>
                                <li>
                                    <a href="shop-filter-sidebar.html">Filters sidebar</a>
                                </li>
                                <li>
                                    <a href="shopping-cart.html">Shopping cart</a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="panelsStayOpen-headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                                Product
                            </button>
                        </h2>
                        <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingThree">
                            <ul class="accordion-nav-list list-unstyled mb-0">
                                <li>
                                    <a href="product-detail-external-affiliate.html">External/Affiliate Product</a>
                                </li>
                                <li>
                                    <a href="product-detail-simple-product.html">Simple product</a>
                                </li>
                                <li>
                                    <a href="product-detail-layout-01.html">Variable product</a>
                                </li>
                                <li>
                                    <a href="product-detail-grouped-product.html">Group Product
                                        <span class="badge bg-teal fw-normal rounded-pill">hot</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="product-detail-layout-02.html">Inner Zoom #1</a>
                                </li>
                                <li>
                                    <a href="product-detail-layout-01.html">External Zoom</a>
                                </li>
                                <li>
                                    <a href="product-detail-layout-03.html">Inner Zoom #2</a>
                                </li>
                                <li>
                                    <a href="product-detail-layout-01.html">PhotoSwipe Popup</a>
                                </li>
                                <li>
                                    <a href="product-detail-description-with-product.html">Description with product</a>
                                </li>
                                <li>
                                    <a href="product-detail-description-with-instagram-shop.html">Description with
                                        instagram shop</a>
                                </li>
                                <li>
                                    <a href="product-detail-product-video.html">Product video
                                        <span class="badge bg-teal fw-normal rounded-pill">hot</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="product-detail-3d-ar-models.html">Product 3D, AR models
                                        <span class="badge bg-teal fw-normal rounded-pill">hot</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <a href="shop-filter-sidebar.html" class="pill-item col-6 p-0" role="presentation">
                        <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Sale</button>
                    </a>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="panelsStayOpen-headingFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseFour" aria-expanded="false" aria-controls="panelsStayOpen-collapseFour">
                                Portfolio
                            </button>
                        </h2>
                        <div id="panelsStayOpen-collapseFour" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingFour">
                            <ul class="accordion-nav-list list-unstyled mb-0">
                                <li>
                                    <a href="portfolio.html">Portfolio 2 Columns</a>
                                </li>
                                <li>
                                    <a href="portfolio-3-columns.html">Portfolio 3 Columns</a>
                                </li>
                                <li>
                                    <a href="portfolio-4-columns.html">Portfolio 4 Columns</a>
                                </li>
                                <li>
                                    <a href="single-portfolio-with-shop.html">Single Portfolio With Shop</a>
                                </li>
                                <li>
                                    <a href="single-portfolio-with-lookbook.html">Single Portfolio With Lookbook</a>
                                </li>
                                <li>
                                    <a href="single-portfolio-with-lookbook.html">Single Portfolio With Lookbook</a>

                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="panelsStayOpen-headingFive">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseFive" aria-expanded="false" aria-controls="panelsStayOpen-collapseFive">
                                Lookbook
                            </button>
                        </h2>
                        <div id="panelsStayOpen-collapseFive" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingFive">
                            <ul class="accordion-nav-list list-unstyled mb-0">
                                <li>
                                    <a href="home-lookbook.html">Lookbook Slider</a>
                                </li>
                                <li>
                                    <a href="portfolio-3-columns.html">Portfolio 3 Columns</a>
                                </li>
                                <li>
                                    <a href="home-lookbook-collection.html">Lookbook Section</a>
                                </li>
                                <li>
                                    <a href="index.html">Lookbook instagram</a>
                                </li>
                                <li>
                                    <a href="product-detail-description-with-lookbook.html">Lookbook in product</a>
                                </li>
                                <li>
                                    <a href="single-portfolio-with-lookbook.html">Lookbook in portfolio post</a>
                                </li>
                                <li>
                                    <a href="lookbook-in-page.html">Lookbook in page</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="panelsStayOpen-headingsix">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapsesix" aria-expanded="false" aria-controls="panelsStayOpen-collapsesix">
                                Pages
                            </button>
                        </h2>
                        <div id="panelsStayOpen-collapsesix" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingsix">
                            <ul class="accordion-nav-list list-unstyled mb-0">
                                <li>
                                    <a href="contact.html">Contact us</a>
                                </li>
                                <li>
                                    <a href="about-us.html">About us</a>
                                </li>
                                <li>
                                    <a href="store-locator.html">Store locator</a>
                                </li>
                                <li>
                                    <a href="faqs.html">FAQs</a>
                                </li>
                                <li>
                                    <a href="faqs-2.html">FAQs 2</a>
                                </li>
                                <li>
                                    <a href="brands.html">Brands</a>
                                </li>
                                <li>
                                    <a href="404.html">404</a>
                                </li>
                                <li>
                                    <a href="timeline.html">Timeline</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="panelsStayOpen-headingseven">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseseven" aria-expanded="false" aria-controls="panelsStayOpen-collapseseven">
                                Blog
                            </button>
                        </h2>
                        <div id="panelsStayOpen-collapseseven" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingseven">
                            <ul class="accordion-nav-list list-unstyled mb-0">
                                <li><a href="blog-grid.html">Grid Layout</a></li>
                                <li><a href="blog-masonry.html">Masonry Layout</a></li>
                                <li><a href="blog-left-sidebar.html">Left Sidebar</a></li>
                                <li><a href="blog-right-sidebar.html">Right Sidebar</a></li>
                                <li><a href="blog-post-with-product-listing.html">Single Post with Product Listing</a>
                                </li>
                                <li><a href="blog-post-with-instagram-shop.html">Single Post with Instagram Shop</a>
                                </li>
                                <li><a href="blog-post-with-instagram-shop.html">Single Post with Categories</a></li>
                                <li><a href="blog-post-with-lookbook.html">Single Post with lookbook</a></li>
                            </ul>
                        </div>
                    </div>
                    <a href="wishlist.html" class="pill-item col-6 p-0" role="presentation">
                        <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false"> <i class="iccl iccl-heart fw-medium me-1"></i> Wishlist</button>
                    </a>
                    <a href="#!" class="pill-item col-6 p-0" role="presentation" data-bs-toggle="modal" data-bs-target="#searchModal">
                        <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false"> <i class="iccl iccl-search fw-medium me-1"></i> Search</button>
                    </a>
                    <a href="#accountOffcanvas" data-bs-toggle="offcanvas" class="pill-item col-6 p-0" role="presentation">
                        <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false"> <i class="iccl iccl-user fw-medium me-1"></i> Login /
                            Register</button>
                    </a>

                    <div class="pill-item col-6 p-0 w-100" role="presentation">
                        <button class="nav-link border-0" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Need Help?</button>
                        <ul class="list-unstyled need-help mb-0">
                            <li>
                                <a href="#!" class="d-flex align-items-center">
                                    <i class="pegk pe-7s-call me-1"></i>
                                    <p class="mb-0">+01 23456789</p>
                                </a>
                            </li>
                            <li>
                                <a href="mailto:claue@domain.com" class="d-flex mt-2 align-items-center">
                                    <i class="pegk pe-7s-mail fwb me-2"></i>
                                    <p class="mb-0">claue@domain.com</p>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="panelsStayOpen-headingSeven">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseSeven" aria-expanded="false" aria-controls="panelsStayOpen-collapseSeven">
                                USD
                            </button>
                        </h2>
                        <div id="panelsStayOpen-collapseSeven" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingSeven">
                            <ul class="accordion-nav-list list-unstyled mb-0">
                                <li>
                                    <a href="#!">
                                        <img src="/theme/images/svg/usd.svg" class="map-img" alt="">AUD
                                    </a>
                                </li>
                                <li>
                                    <a href="#!">
                                        <img src="/theme/images/svg/usd.svg" class="map-img" alt="">CAD
                                    </a>
                                </li>
                                <li>
                                    <a href="#!">
                                        <img src="/theme/images/svg/usd.svg" class="map-img" alt="">DKK
                                    </a>
                                </li>
                                <li>
                                    <a href="#!">
                                        <img src="/theme/images/svg/usd.svg" class="map-img" alt="">DKK
                                    </a>
                                </li>
                                <li>
                                    <a href="#!">
                                        <img src="/theme/images/svg/usd.svg" class="map-img" alt="">EUR
                                    </a>
                                </li>
                                <li>
                                    <a href="#!">
                                        <img src="/theme/images/svg/usd.svg" class="map-img" alt="">GBP
                                    </a>
                                </li>
                                <li>
                                    <a href="#!">
                                        <img src="/theme/images/svg/usd.svg" class="map-img" alt="">USD
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="pills-categories" role="tabpanel" aria-labelledby="pills-categories-tab" tabindex="0">
                <ul class="accordion-nav-list accordion-navs-list list-unstyled mb-0">
                    <li>
                        <a href="#"><i class="las la-female me-2"></i>Women�s Clothing</a>
                    </li>
                    <li>
                        <a href="#"><i class="las la-male me-2"></i>Men�s Clothing</a>
                    </li>
                    <li>
                        <a href="#"><i class="las la-clock me-2"></i>Watches</a>
                    </li>
                    <li>
                        <a href="#"><i class="las la-glasses me-2"></i>Accessories</a>
                    </li>
                    <li>
                        <a href="#"><i class="las la-camera-retro me-2"></i>Electric</a>
                    </li>
                    <li>
                        <a href="#"><i class="las la-shoe-prints me-2"></i>Shoes</a>
                    </li>
                    <li>
                        <a href="#"><i class="las la-gem me-2"></i>Jewellery</a>
                    </li>
                    <li>
                        <a href="#"><i class="las la-tshirt me-2"></i>T-Shirt</a>
                    </li>
                    <li>
                        <a href="#"><i class="las la-child me-2"></i>Toys, Kids &amp; Baby</a>
                    </li>
                    <li>
                        <a href="#"><i class="las la-chair me-2"></i>Decor</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!--search offcanavas-->
<div class="offcanvas offcanvas-end" tabindex="-1" id="searchOffcanvas" aria-labelledby="searchOffcanvasLabel">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fs-16 text-uppercase" id="searchOffcanvasLabel">Search Out Site</h5>
        <button type="button" class="btn-close btn-close-none" data-bs-dismiss="offcanvas" aria-label="Close"><i class="pe-7s-close pegk"></i></button>
    </div>
    <div class="px-3 py-4">
        <div>
            <form action="#!">
                <select class="form-select rounded-pill mb-3">
                    <option value="*">All Categories</option>
                    <option value="Accessories">Accessories</option>
                    <option value="Bag">Bag</option>
                    <option value="Camera">Camera</option>
                    <option value="Decor">Decor</option>
                    <option value="Earphones">Earphones</option>
                    <option value="Electric">Electric</option>
                    <option value="Furniture">Furniture</option>
                    <option value="Headphone">Headphone</option>
                    <option value="Men">Men</option>
                    <option value="Shoes">Shoes</option>
                    <option value="Speaker">Speaker</option>
                    <option value="Watch">Watch</option>
                    <option value="Women">Women</option>
                </select>
                <div class="search-box position-relative">
                    <input type="text" class="form-control rounded-pill" id="exampleFormControlsearch2" placeholder="Search for products">
                    <button type="submit" class="btn"><i class="iccl iccl-search"></i></button>
                </div>
            </form>
        </div>
    </div>
    <div class="p-3 border-bottom border-top shadow-2xl">
        <h6 class="mb-0 fw-medium">Need some inspiration?</h6>
    </div>
    <div class="offcanvas-body">
        <div class="row mb-3">
            <div class="col-4">
                <a href="#!"><img src="/theme/images/mini-cart/product-01.jpg" alt="" class="img-fluid"></a>
            </div>
            <div class="col-8">
                <h6 class="mb-2"><a href="product-detail-layout-01.html" class="product-title">sunlight bell solar
                        lamp</a></h6>
                <p class="mb-0 fs-14 text-muted">$30.00</p>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-4 pe-0">
                <a href="#!"><img src="/theme/images/mini-cart/product-02.jpg" alt="" class="img-fluid"></a>
            </div>
            <div class="col-8">
                <h6 class="mb-2"><a href="product-detail-layout-01.html" class="product-title">cru thermos jug</a></h6>
                <p class="mb-0 fs-14 text-muted d-flex align-items-center gap-2">
                    <del>$60.00</del>
                    <span class="text-danger">$40.00</span>
                    <span class="badge bg-danger rounded-0">-25%</span>
                </p>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-4 pe-0">
                <a href="#!"><img src="/theme/images/mini-cart/product-03.jpg" alt="" class="img-fluid"></a>
            </div>
            <div class="col-8">
                <h6 class="mb-2"><a href="product-detail-layout-01.html" class="product-title">brush set small</a></h6>
                <p class="mb-0 fs-14 text-muted">$65.00</p>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-4 pe-0">
                <a href="#!"><img src="/theme/images/mini-cart/product-04.jpg" alt="" class="img-fluid"></a>
            </div>
            <div class="col-8">
                <h6 class="mb-2"><a href="product-detail-layout-05.html" class="product-title">stripe oilcloth</a></h6>
                <p class="mb-0 fs-14 text-muted">$35.00</p>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-4 pe-0">
                <a href="#!"><img src="/theme/images/mini-cart/product-05.jpg" alt="" class="img-fluid"></a>
            </div>
            <div class="col-8">
                <h6 class="mb-2"><a href="product-detail-layout-05.html" class="product-title">picto wall clock</a></h6>
                <p class="mb-0 fs-14 text-muted">$15.00</p>
            </div>
        </div>
    </div>
    <div class="py-4 border-top mx-4">
        <a href="#" class="detail_link fs-14 fw-semibold">View All <i class="las la-arrow-right fs__18"></i></a>
    </div>
</div>
<!--account offcanavas-->
<div class="offcanvas offcanvas-end" tabindex="-1" id="accountOffcanvas" aria-labelledby="accountOffcanvasLabel">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fs-16 text-uppercase" id="accountOffcanvasLabel">LOGIN</h5>
        <button type="button" class="btn-close btn-close-none" data-bs-dismiss="offcanvas" aria-label="Close"><i class="pe-7s-close pegk"></i></button>
    </div>
    <div class="offcanvas-body">
        <div>
            <form action="#!" class="mb-4">
                <div class="mb-3">
                    <label for="emailInputOffcanvas" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="emailInputOffcanvas">
                </div>
                <div class="mb-3 pb-1">
                    <label for="current-password" class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="current-password" autocomplete="off">
                </div>
                <div>
                    <button type="submit" class="btn btn-info w-100 rounded-pill">SIGN IN</button>
                </div>
            </form>
            <p class="text-muted">New customer? <a href="#!" class="product-title">Create your account</a></p>
            <p class="text-muted">Lost password? <a href="#!" class="product-title">Recover password</a></p>
        </div>
    </div>
</div>

<!--Shopping Cart offcanavas-->
<div class="offcanvas offcanvas-end" tabindex="-1" id="shoppingCartOffcanvas" aria-labelledby="shoppingCartnvasLabel">
    <div class="offcanvas-header p-3">
        <h5 class="offcanvas-title text-uppercase" id="shoppingCartnvasLabel">Shopping Cart</h5>
        <button type="button" class="btn-close btn-close-none" data-bs-dismiss="offcanvas" aria-label="Close"><i class="pe-7s-close pegk"></i></button>
    </div>
    <div class="p-20 border-bottom border-top shadow-2xl">
        <h6 class="mb-0 fw-medium fs-13 lh-base">Almost there, add <span class="text-danger">$9.00</span> more to get
            <span class="text-danger">FREE SHIPPING!</span>
        </h6>
    </div>
    <div class="offcanvas-body p-0">
        <div class="p-20">
            <div class="row">
                <div class="col-5">
                    <img src="/theme/images/mini-cart/mini-cart-01.jpg" alt="" class="img-fluid">
                </div>
                <div class="col-7">
                    <h6 class="mb-1"><a href="#!" class="product-title">La Boh�me Rose Gold</a></h6>
                    <p class="text-muted fs-12">Pink</p>

                    <p class="fs-14 text-muted d-flex align-items-center gap-2">
                        <del>$60.00</del>
                        <span class="text-danger">$40.00</span>
                    </p>
                    <div x-data="{ quantity: 2 }" class="quantity fs-14 position-relative">
                        <input x-bind:value="quantity" type="number" class="input-text text-center" readonly step="1" min="0" max="9999">
                        <button type="button" class="minus position-absolute start-0 ps-3" x-on:click="quantity > 1 ? quantity-- : null">
                            <i class="facl facl-minus"></i>
                        </button>
                        <button type="button" class="plus position-absolute end-0 pe-3" x-on:click="quantity++">
                            <i class="facl facl-plus"></i>
                        </button>
                    </div>

                    <div class="d-flex align-items-center gap-3 mt-2">
                        <a href="#!" class="main_link" data-bs-toggle="modal" data-bs-target="#cardModal" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit this item">
                            <svg xmlns="http://www.w3.org/2000/svg" class="square-20" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </a>
                        <a href="#!" class="main_link" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Remove this item">
                            <svg xmlns="http://www.w3.org/2000/svg" class="square-20" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                </path>
                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                <line x1="14" y1="11" x2="14" y2="17"></line>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-20 border-top">
            <div class="row">
                <div class="col-lg-5">
                    <img src="/theme/images/mini-cart/mini-cart-02.jpg" alt="" class="img-fluid">
                </div>
                <div class="col-lg-7">
                    <h6 class="mb-1"><a href="#!" class="product-title">Blush Beanie</a></h6>
                    <p class="text-muted fs-12">Grey / S</p>

                    <p class="fs-14 text-muted d-flex align-items-center gap-2">
                        <span>$15.00</span>
                    </p>
                    <div x-data="{ quantity: 13 }" class="quantity fs-14 position-relative">
                        <input x-bind:value="quantity" type="number" class="input-text text-center" readonly step="1" min="0" max="9999">
                        <button type="button" class="minus position-absolute start-0 ps-3" x-on:click="quantity > 1 ? quantity-- : null">
                            <i class="facl facl-minus"></i>
                        </button>
                        <button type="button" class="plus position-absolute end-0 pe-3" x-on:click="quantity++">
                            <i class="facl facl-plus"></i>
                        </button>
                    </div>

                    <div class="d-flex align-items-center gap-3 mt-2">
                        <a href="#!" class="main_link" data-bs-toggle="modal" data-bs-target="#cardModal" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit this item">
                            <svg xmlns="http://www.w3.org/2000/svg" class="square-20" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </a>
                        <a href="#!" class="main_link" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Remove this item">
                            <svg xmlns="http://www.w3.org/2000/svg" class="square-20" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                </path>
                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                <line x1="14" y1="11" x2="14" y2="17"></line>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-20 border-top">
            <div class="row">
                <div class="col-lg-5">
                    <img src="/theme/images/mini-cart/mini-cart-03.jpg" alt="" class="img-fluid">
                </div>
                <div class="col-lg-7">
                    <h6 class="mb-1"><a href="#!" class="product-title">Ridley High Waist</a></h6>
                    <p class="text-muted fs-12">S</p>

                    <p class="fs-14 text-muted d-flex align-items-center gap-2">
                        <span>$36.00</span>
                    </p>
                    <div x-data="{ quantity: 7 }" class="quantity fs-14 position-relative">
                        <input x-bind:value="quantity" type="number" class="input-text text-center" readonly step="1" min="0" max="9999">
                        <button type="button" class="minus position-absolute start-0 ps-3" x-on:click="quantity > 1 ? quantity-- : null">
                            <i class="facl facl-minus"></i>
                        </button>
                        <button type="button" class="plus position-absolute end-0 pe-3" x-on:click="quantity++">
                            <i class="facl facl-plus"></i>
                        </button>
                    </div>

                    <div class="d-flex align-items-center gap-3 mt-2">
                        <a href="#!" class="main_link" data-bs-toggle="modal" data-bs-target="#cardModal" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit this item">
                            <svg xmlns="http://www.w3.org/2000/svg" class="square-20" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </a>
                        <a href="#!" class="main_link" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Remove this item">
                            <svg xmlns="http://www.w3.org/2000/svg" class="square-20" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                </path>
                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                <line x1="14" y1="11" x2="14" y2="17"></line>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="px-20 py-3">
            <ul class="mini_cart_tool list-unstyled d-flex gap-3 justify-content-center mb-0">
                <li>
                    <a href="#!" class="square-sm rounded-circle fs-25 d-inline-flex text-reset align-items-center justify-content-center"><i class="lar la-clipboard"></i></a>
                </li>
                <li>
                    <a href="#!" class="square-sm rounded-circle fs-25 d-inline-flex text-reset align-items-center justify-content-center"><i class="las la-gift"></i></a>
                </li>
                <li>
                    <a href="#!" class="square-sm rounded-circle fs-25 d-inline-flex text-reset align-items-center justify-content-center"><i class="las la-truck-moving"></i></a>
                </li>
                <li>
                    <a href="#!" class="square-sm rounded-circle fs-25 d-inline-flex text-reset align-items-center justify-content-center"><i class="las la-tag"></i></a>
                </li>
            </ul>
        </div>
    </div>
    <div class="offcanvas-footer p-20 border-top">
        <div class="d-flex align-items-center mb-3">
            <h5 class="mb-0 flex-grow-1 fs-18">Subtotal:</h5>
            <a href="#!" class="cart_tot_price fs-18 texrt-reset">$91.00</a>
        </div>
        <p class="text-muted fs-13 mb-2">Taxes, shipping and discounts codes calculated at checkout</p>
        <div class="form-check form-check-info">
            <input class="form-check-input" type="checkbox" value="" id="cartAgreeInput">
            <label class="form-check-label fs-13 text-muted" for="cartAgreeInput">
                I agree with the terms and conditions.
            </label>
        </div>
        <div class="mt-3 vstack gap-3">
            <a href="shopping-cart.html"><button type="button" class="btn btn-light w-100 rounded-pill text-uppercase fw-semibold" style="letter-spacing: 4px; font-size: 11px;">View cart</button></a>
            <a href="checkout.html">
                <button type="button" class="btn btn-info w-100 rounded-pill text-uppercase fw-semibold" style="letter-spacing: 4px; font-size: 11px;">Check out</button>
            </a>
        </div>
        <div class="mt-3">
            <img src="/theme/images/trust_img2.png" alt="" class="img-fluid">
        </div>
    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas" aria-labelledby="shoppingCartnvasLabel">
    <div class="offcanvas-header bg-black p-3">
        <h5 class="offcanvas-title text-white text-uppercase" id="shoppingCartnvasLabel">Filter</h5>
        <button type="button" class="btn-close text-white btn-close-none" data-bs-dismiss="offcanvas" aria-label="Close"><i class="pe-7s-close text-white pegk"></i></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="p-4 filter-box rounded-0 border-0 shadow-none">
            <div class="row g-4 g-sm-2">
                <div class="col-12">
                    <h5 class="mb-1 fw-medium"> By Vendor </h5>
                    <div class="filter-title"></div>
                    <div class="mt-3">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked1">
                            <label class="form-check-label" for="flexCheckChecked1" style="cursor: pointer;">
                                Ck
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked2">
                            <label class="form-check-label" for="flexCheckChecked2" style="cursor: pointer;">
                                H&M
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked3">
                            <label class="form-check-label" for="flexCheckChecked3" style="cursor: pointer;">
                                Kalles
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked4" style="cursor: pointer;">
                            <label class="form-check-label" for="flexCheckChecked4" style="cursor: pointer;">
                                Lavi's
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked5" style="cursor: pointer;">
                            <label class="form-check-label" for="flexCheckChecked5" style="cursor: pointer;">
                                Monki
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked6" style="cursor: pointer;">
                            <label class="form-check-label" for="flexCheckChecked6" style="cursor: pointer;">
                                Nike
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <h5 class="mb-1 fw-medium"> By Size </h5>
                    <div class="filter-title"></div>
                    <div class="mt-3">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked11">
                            <label class="form-check-label" for="flexCheckChecked11" style="cursor: pointer;">
                                S <span class="ms-1 text-muted">(9)</span>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked21">
                            <label class="form-check-label" for="flexCheckChecked21" style="cursor: pointer;">
                                M <span class="ms-1 text-muted">(12)</span>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked31">
                            <label class="form-check-label" for="flexCheckChecked31" style="cursor: pointer;">
                                L <span class="ms-1 text-muted">(6)</span>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked41" style="cursor: pointer;">
                            <label class="form-check-label" for="flexCheckChecked41" style="cursor: pointer;">
                                Xs <span class="ms-1 text-muted">(8)</span>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked51" style="cursor: pointer;">
                            <label class="form-check-label" for="flexCheckChecked51" style="cursor: pointer;">
                                Xl <span class="ms-1 text-muted">(25)</span>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked61" style="cursor: pointer;">
                            <label class="form-check-label" for="flexCheckChecked61" style="cursor: pointer;">
                                Xxl <span class="ms-1 text-muted">(16)</span>
                            </label>
                        </div>
                    </div>
                </div>
                <!-- color -->
                <div class="col-12">
                    <h5 class="mb-1 fw-medium"> By Vendor </h5>
                    <div class="filter-title"></div>
                    <div class="mt-3 filter-category">
                        <div class="round d-flex align-items-center pt-2 mb-2 gap-1">
                            <input class="form-check-input bg-black border-black p-1" type="checkbox" value="" id="colo1">
                            <label class="form-check-label ms-1" style="cursor: pointer;" for="color1">
                                Black
                            </label>
                        </div>
                        <div class="round d-flex align-items-center pt-2 mb-2 gap-1">
                            <input class="form-check-input bg-teal border-teal p-1" type="checkbox" value="" id="color2">
                            <label class="form-check-label ms-1" style="cursor: pointer;" for="color2">
                                Cyan
                            </label>
                        </div>
                        <div class="round d-flex align-items-center pt-2 mb-2 gap-1">
                            <input class="form-check-input bg-green2 p-1" type="checkbox" value="" id="color3">
                            <label class="form-check-label ms-1" style="cursor: pointer;" for="color3">
                                Green
                            </label>
                        </div>
                        <div class="round d-flex align-items-center pt-2 mb-2 gap-1">
                            <input class="form-check-input bg-cid-green border-cid-green p-1" type="checkbox" value="" id="color4">
                            <label class="form-check-label ms-1" style="cursor: pointer;" for="color4">
                                Gray
                            </label>
                        </div>
                        <div class="round d-flex align-items-center pt-2 mb-2 gap-1">
                            <input class="form-check-input bg-pink2 border-pink2 p-1" type="checkbox" value="" id="color5">
                            <label class="form-check-label ms-1" style="cursor: pointer;" for="color5">
                                Pink
                            </label>
                        </div>
                        <div class="round d-flex align-items-center pt-2 mb-2 gap-1">
                            <input class="form-check-input bg-sea border-sea p-1" type="checkbox" value="" id="color6">
                            <label class="form-check-label ms-1" style="cursor: pointer;" for="color6">
                                Sea
                            </label>
                        </div>
                        <div class="round d-flex align-items-center pt-2 mb-2 gap-1">
                            <input class="form-check-input bg-blue-dark border-blue-dark p-1" type="checkbox" value="" id="color7">
                            <label class="form-check-label ms-1" style="cursor: pointer;" for="color7">
                                Blue
                            </label>
                        </div>
                        <div class="round d-flex align-items-center pt-2 mb-2 gap-1">
                            <input class="form-check-input bg-red border-red p-1" type="checkbox" value="" id="color8">
                            <label class="form-check-label ms-1" style="cursor: pointer;" for="color8">
                                red
                            </label>
                        </div>
                        <div class="round d-flex align-items-center pt-2 mb-2 gap-1">
                            <input class="form-check-input bg-orange p-1 border-orange" type="checkbox" value="" id="color9">
                            <label class="form-check-label ms-1" style="cursor: pointer;" for="color9">
                                Orange
                            </label>
                        </div>
                    </div>
                </div>
                <!-- Category -->
                <div class="col-12 ">
                    <h5 class="mb-1 fw-medium"> By Category </h5>
                    <div class="filter-title"></div>
                    <div class="mt-3 filter-category">
                        <div class="form-check pt-2 mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="cate">
                            <label class="form-check-label" style="cursor: pointer;" for="cate">
                                Accessories
                            </label>
                        </div>
                        <div class="form-check pt-2 mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="cate22">
                            <label class="form-check-label" style="cursor: pointer;" for="cate22">
                                Men
                            </label>
                        </div>
                        <div class="form-check pt-2 mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="cate3">
                            <label class="form-check-label" style="cursor: pointer;" for=" cate3">
                                Women
                            </label>
                        </div>
                        <div class="form-check pt-2 mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="cate4">
                            <label class="form-check-label" style="cursor: pointer;" for=" cate4">
                                Shoes
                            </label>
                        </div>
                        <div class="form-check pt-2 mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="cate5">
                            <label class="form-check-label" style="cursor: pointer;" for=" cate5">
                                T-Shirt
                            </label>
                        </div>
                        <div class="form-check pt-2 mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="cate6">
                            <label class="form-check-label" style="cursor: pointer;" for=" cate6">
                                Dress
                            </label>
                        </div>
                        <div class="form-check pt-2 mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="cate7">
                            <label class="form-check-label" style="cursor: pointer;" for=" cate7">
                                Jackets
                            </label>
                        </div>
                        <div class="form-check pt-2 mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="cate8">
                            <label class="form-check-label" style="cursor: pointer;" for=" cate8">
                                Boots
                            </label>
                        </div>
                        <div class="form-check pt-2 mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="cate9">
                            <label class="form-check-label" style="cursor: pointer;" for=" cate9">
                                Jewellery
                            </label>
                        </div>
                        <div class="form-check pt-2 mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="cate10">
                            <label class="form-check-label" style="cursor: pointer;" for=" cate">
                                Tops
                            </label>
                        </div>
                        <div class="form-check pt-2 mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="cate11">
                            <label class="form-check-label" style="cursor:pointer; " for=" cate11">
                                Wallet
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <h5 class="mb-1 fw-medium"> By Price </h5>
                    <div class="filter-title"></div>
                    <form action="" class="mt-5">
                        <div class="slider-area">
                            <div>
                                <div class="slider-area">
                                    <div id="slider-snap" class="slider"></div>
                                    <div class="d-flex align-items-center mt-4 py-2">
                                        <span class="text-muted">Price: </span>
                                        <h6 class="mb-0 mx-2">
                                            <span id="slider-snap-value-lower"></span>
                                        </h6>
                                        -
                                        <h6 class="mb-0 ms-2">
                                            <span id="slider-snap-value-upper"></span>
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-custom-dark  fw-medium min-w-150 ">FILTER</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!--shop offcanavas-->
<div class="offcanvas offcanvas-end " tabindex="-1" id="shopOffcanvas" aria-labelledby="shopOffcanvasLabel">
    <div class="offcanvas-header border-bottom bg-black text-white">
        <h5 class="offcanvas-title fs-16 text-uppercase" id="shopOffcanvasLabel">SIDEBAR</h5>
        <button type="button" class="btn-close btn-close-none text-white" data-bs-dismiss="offcanvas" aria-label="Close"><i class="pe-7s-close pegk"></i></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="filter-box p-3" style="box-shadow: none;">
            <h5 class="mb-1 fw-medium"> Short by </h5>
            <div class="filter-title"></div>
            <p class="text-teal mb-2 mt-3"> Featured</p>
            <p class="mb-2">Best selling </p>
            <p class="mb-2">Alphabetically, A-Z</p>
            <p class="mb-2">Alphabetically, Z-A</p>
            <p class="mb-2">Price, low to high</p>
            <p class="mb-2">Price, high to low</p>
            <p class="mb-2">Date, old to new</p>
            <p class="mb-2">Date, new to old</p>

        </div>
        <div class="filter-box p-3 border-0" style="box-shadow: none;">
            <h5 class="mb-1 fw-medium"> By Category </h5>
            <div class="filter-title"></div>
            <div class="mt-3 filter-category">
                <p class="mb-2">
                    Accessories
                </p>
                <p class="mb-2">
                    Men
                </p>
                <p class="mb-2">
                    Women
                </p>
                <p class="mb-2">
                    Shoes
                </p>
                <p class="mb-2">
                    T-Shirt
                </p>
                <p class="mb-2">
                    Dress
                </p>
                <p class="mb-2">
                    Jackets
                </p>
                <p class="mb-2">
                    Boots
                </p>
                <p class="mb-2">
                    Jewellery
                </p>
                <p class="mb-2">
                    Tops
                </p>
                <p class="mb-2">
                    Wallet
                </p>
            </div>
        </div>
        <div class="filter-box p-3" style="box-shadow: none;">
            <h5 class="mb-1 fw-medium"> Filter by price </h5>
            <div class="filter-title"></div>
            <div class="mt-3">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        $50-$100
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        $100-$150
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        $150-$200
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        $200-$250
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        $250-$300
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        $300-$350
                    </label>
                </div>
            </div>
        </div>
        <div class="filter-box p-3 border-0" style="box-shadow: none;">
            <h5 class="mb-1 fw-medium"> Sale Products </h5>
            <div class="filter-title"></div>
            <div class="row mt-3">
                <div class="col-4">
                    <img src="/theme/images/shop/sidebar-product-01.jpg" class="img-fluid" alt="">
                </div>
                <div class="col-8 ps-0">
                    <h6>Skin Sweatpants</h6>
                    <p class="text-danger"><del class="text-muted">$75.00</del>$45.00</p>
                    <span class="bg-danger text-white px-2 py-1">-40%</span>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-4">
                    <img src="/theme/images/shop/sidebar-product-02.jpg" class="img-fluid" alt="">
                </div>
                <div class="col-8 ps-0">
                    <h6>Cluse La Boheme Rose Gold</h6>
                    <p class="text-danger"><del class="text-muted">$60.00</del>$45.00</p>
                    <span class="bg-danger text-white px-2 py-1">-25%</span>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-4">
                    <img src="/theme/images/shop/sidebar-product-03.jpg" class="img-fluid" alt="">
                </div>
                <div class="col-8 ps-0">
                    <h6>Felt Cowboy Hat</h6>
                    <p class="text-muted">$22.00</p>
                </div>
            </div>
        </div>
        <div class="filter-box p-3 border-0" style="box-shadow: none;">
            <h5 class="mb-1 fw-medium"> Instagram </h5>
            <div class="filter-title"></div>
            <div class="row row-cols-3 g-1 mt-3 ">
                <div class="insta-card position-relative">
                    <img src="/theme/images/instagram/ins1_1.jpg" alt="" class="img-fluid">
                </div>
                <div class="insta-card position-relative">
                    <img src="/theme/images/instagram/ins1_2.jpg" alt="" class="img-fluid">
                </div>
                <div class="insta-card position-relative">
                    <img src="/theme/images/instagram/ins1_5.jpg" alt="" class="img-fluid">
                </div>
                <div class="insta-card position-relative">
                    <img src="/theme/images/instagram/ins1_4.jpg" alt="" class="img-fluid">
                </div>
                <div class="insta-card position-relative">
                    <img src="/theme/images/instagram/ins1_5.jpg" alt="" class="img-fluid">
                </div>
                <div class="insta-card position-relative">
                    <img src="/theme/images/instagram/ins1_6.jpg" alt="" class="img-fluid">
                </div>
                <div class="insta-card position-relative">
                    <img src="/theme/images/instagram/ins1_7.jpg" alt="" class="img-fluid">
                </div>
                <div class="insta-card position-relative">
                    <img src="/theme/images/instagram/ins1_8.jpg" alt="" class="img-fluid">
                </div>
                <div class="insta-card position-relative">
                    <img src="/theme/images/instagram/ins1_4.jpg" alt="" class="img-fluid">
                </div>
            </div>
        </div>
        <div class="filter-box p-3 border-0" style="box-shadow: none;">
            <h5 class="mb-1 fw-medium"> Shipping & Delivery </h5>
            <div class="filter-title"></div>
            <div class="row mt-3">
                <div class="col-3">
                    <h1><i class="las la-truck"></i></h1>
                </div>
                <div class="col-8 ps-0">
                    <h6>FREE SHIPPING</h6>
                    <p class="text-muted">Free shipping for all US order</p>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-3">
                    <h1><i class="las la-headset"></i></h1>
                </div>
                <div class="col-8 ps-0">
                    <h6>SUPPORT 24/7</h6>
                    <p class="text-muted">We support 24 hours a day</p>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-3">
                    <h1><i class="las la-exchange-alt"></i></h1>
                </div>
                <div class="col-8 ps-0">
                    <h6>30 DAYS RETURN</h6>
                    <p class="text-muted">You have 30 days to return</p>
                </div>
            </div>
        </div>
    </div>
</div>
<!--filter offcanavas-->
<div class="offcanvas offcanvas-start" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">
    <div class="offcanvas-header border-bottom bg-black text-white">
        <h5 class="offcanvas-title fs-16 text-uppercase" id="filterOffcanvasLabel">FILTER</h5>
        <button type="button" class="btn-close btn-close-none text-white" data-bs-dismiss="offcanvas" aria-label="Close"><i class="pe-7s-close pegk"></i></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="filter-box p-3" style="box-shadow: none;">
            <h5 class="mb-1 fw-medium"> By Vendor </h5>
            <div class="filter-title"></div>
            <div class="mt-3">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        Ck
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        H&amp;M
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        Kalles
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        Lavi's
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        Monki
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        Nike
                    </label>
                </div>
            </div>
        </div>
        <div class="filter-box p-3 border-0" style="box-shadow: none;">
            <h5 class="mb-1 fw-medium"> By Size </h5>
            <div class="filter-title"></div>
            <div class="mt-3">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        S <span class="ms-1 text-muted">(9)</span>
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        M <span class="ms-1 text-muted">(12)</span>
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        L <span class="ms-1 text-muted">(6)</span>
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        Xs <span class="ms-1 text-muted">(8)</span>
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        Xl <span class="ms-1 text-muted">(25)</span>
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        Xxl <span class="ms-1 text-muted">(16)</span>
                    </label>
                </div>
            </div>
        </div>
        <div class="filter-box p-3" style="box-shadow: none;">
            <h5 class="mb-1 fw-medium"> By Color </h5>
            <div class="filter-title"></div>
            <div class="mt-3 filter-category">
                <div class="round d-flex align-items-center mb-2">
                    <input class="form-check-input bg-black p-1" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label ms-1" for="flexCheckChecked">
                        Black
                    </label>
                </div>
                <div class="round d-flex align-items-center mb-2">
                    <input class="form-check-input bg-teal p-1" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label ms-1" for="flexCheckChecked">
                        Cyan
                    </label>
                </div>
                <div class="round d-flex align-items-center mb-2">
                    <input class="form-check-input bg-green2 p-1" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label ms-1" for="flexCheckChecked">
                        Green
                    </label>
                </div>
                <div class="round d-flex align-items-center mb-2">
                    <input class="form-check-input bg-cid-green: p-1" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label ms-1" for="flexCheckChecked">
                        Gray
                    </label>
                </div>
                <div class="round d-flex align-items-center mb-2">
                    <input class="form-check-input bg-pink2 p-1" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label ms-1" for="flexCheckChecked">
                        Pink
                    </label>
                </div>
                <div class="round d-flex align-items-center mb-2">
                    <input class="form-check-input bg-sea p-1" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label ms-1" for="flexCheckChecked">
                        Sea
                    </label>
                </div>
                <div class="round d-flex align-items-center mb-2">
                    <input class="form-check-input bg-blue-dark p-1" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label ms-1" for="flexCheckChecked">
                        Blue
                    </label>
                </div>
                <div class="round d-flex align-items-center mb-2">
                    <input class="form-check-input bg-red p-1" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label ms-1" for="flexCheckChecked">
                        red
                    </label>
                </div>
                <div class="round d-flex align-items-center mb-2">
                    <input class="form-check-input bg-orange p-1" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label ms-1" for="flexCheckChecked">
                        Orange
                    </label>
                </div>
            </div>
        </div>
        <div class="filter-box p-3 border-0" style="box-shadow: none;">
            <h5 class="mb-1 fw-medium"> By Category </h5>
            <div class="filter-title"></div>
            <div class="mt-3 filter-category">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        Accessories
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        Men
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked" checked="">
                    <label class="form-check-label" for="flexCheckChecked">
                        Women
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        Shoes
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        T-Shirt
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        Dress
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        Jackets
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        Boots
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        Jewellery
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        Tops
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        Wallet
                    </label>
                </div>
            </div>
        </div>
        <div class="filter-box p-3 border" style="box-shadow: none;">
            <h5 class="mb-1 fw-medium"> By Title </h5>
            <div class="filter-title mb-3"></div>
            <form class="form-inline mt-3 mb-2 my-lg-0 filter-search">
                <input class="form-control mr-sm-2" type="search" placeholder="Search for product title" aria-label="Search">
                <button class="btn btn-custom-dark  fw-medium min-w-150 mt-3">FILTER</button>
            </form>
        </div>
        <div class="p-3 ">
            <h5 class="mb-1 fw-medium"> By Price </h5>
            <div class="filter-title"></div>
            <form action="" class="mt-5">
                <div class="slider">
                    <div class="progress"></div>
                </div>
                <div class="range-input">
                    <input type="range" class="range-min" min="32" max="100" value="32" step="1">
                    <input type="range" class="range-max" min="60" max="100" value="60" step="1">
                </div>
                <p class="fw-medium text-black fs-14 mt-4"><span class="text-muted fw-normal me-2">Price</span>$32.70
                    -
                    $60.19
                </p>
                <button class="btn btn-custom-dark  fw-medium min-w-150 ">FILTER</button>
            </form>
        </div>
    </div>
</div>

<!--search Modal -->
<div class="modal fade modal-overl " id="searchModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen header-search-model">
        <div class="modal-content">
            <div class="modal-body p-0 mb-4">
                <form class="row g-2 mx-2 mx-md-5 my-4 pb-3 pt-4" action="#!">
                    <div class="col-md-3">
                        <select class="form-select rounded-pill w-100">
                            <option value="*">All Categories</option>
                            <option value="Accessories">Accessories</option>
                            <option value="Bag">Bag</option>
                            <option value="Camera">Camera</option>
                            <option value="Decor">Decor</option>
                            <option value="Earphones">Earphones</option>
                            <option value="Electric">Electric</option>
                            <option value="Furniture">Furniture</option>
                            <option value="Headphone">Headphone</option>
                            <option value="Men">Men</option>
                            <option value="Shoes">Shoes</option>
                            <option value="Speaker">Speaker</option>
                            <option value="Watch">Watch</option>
                            <option value="Women">Women</option>
                        </select>
                    </div>
                    <div class="col-md-9">
                        <div class="search-box position-relative">
                            <input type="text" class="form-control rounded-pill" id="" placeholder="Search for products">
                            <button type="submit" class="btn"><i class="iccl iccl-search"></i></button>
                        </div>
                    </div>
                </form>
                <p class="need-sec fs-16 fw-medium p-2 text-center">Need some inspiration?</p>
                <div class="row g-3 row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 row-cols-xl-6 row-cols-xxl-auto mx-auto mt-4 justify-content-center px-lg-5">
                    <div class="col">
                        <img src="/theme/images/home-fashion-9/pr-s-01.jpg" alt="">
                        <div class="mt-4 text-center">
                            <h6 class="mb-1 text-capitalize fs-14 fw-medium"><a href="product-detail-layout-01.html" class="main_link_teal">Analogue Resin
                                    Strap</a></h6>
                            <p class="mb-0 fs-14 text-muted">
                                <del>$30.00</del>
                            </p>
                        </div>
                    </div>
                    <div class="col">
                        <img src="/theme/images/home-fashion-9/pr-s-02.jpg" alt="">
                        <div class="mt-4 text-center">
                            <h6 class="mb-1 text-capitalize fs-14 fw-medium">
                                <a href="product-detail-layout-01.html" class="main_link_teal">Ridley High
                                    Waist</a>
                            </h6>
                            <p class="mb-0 fs-14 text-muted">
                                <del>$36.00</del>
                            </p>
                        </div>
                    </div>
                    <div class="col">
                        <img src="/theme/images/home-fashion-9/pr-s-03.jpg" alt="">
                        <div class="mt-4 text-center">
                            <h6 class="mb-1 text-capitalize fs-14 fw-medium">
                                <a href="product-detail-layout-01.html" class="main_link_teal">
                                    Blush Beanie </a>
                            </h6>
                            <p class="mb-0 fs-14 text-muted">
                                <del>$15.00</del>
                            </p>
                        </div>
                    </div>
                    <div class="col">
                        <img src="/theme/images/home-fashion-9/pr-s-04.jpg" alt="">
                        <div class="mt-4 text-center">
                            <h6 class="mb-1 text-capitalize fs-14 fw-medium">
                                <a href="product-detail-layout-01.html" class="main_link_teal">
                                    Cluse La Boheme Rose <br /> Gold </a>
                            </h6>
                            <p class="mb-0 fs-14 text-muted">
                                <del>$60.00</del>
                                <span class="text-danger">$45.00</span>
                                <span class="badge bg-danger fw-normal text-white rounded-0">-25%</span>
                            </p>
                        </div>
                    </div>
                    <div class="col">
                        <img src="/theme/images/home-fashion-9/pr-s-05.jpg" alt="">
                        <div class="mt-4 text-center">
                            <h6 class="mb-1 text-capitalize fs-14 fw-medium">
                                <a href="product-detail-layout-01.html" class="main_link_teal">
                                    Mercury Tee </a>
                            </h6>
                            <p class="mb-0 fs-14 text-muted">
                                <del>$68.00</del>
                            </p>
                        </div>
                    </div>
                    <div class="col">
                        <img src="/theme/images/home-fashion-9/pr-s-06.jpg" alt="">
                        <div class="mt-4 text-center">
                            <h6 class="mb-1 text-capitalize fs-14 fw-medium">
                                <a href="product-detail-layout-01.html" class="main_link_teal">
                                    La Boh�me Rose Gold </a>
                            </h6>
                            <p class="mb-0 fs-14 text-muted">
                                <del>$60.00</del>
                                <span class="text-danger">$40.00</span>
                                <span class="badge bg-danger fw-normal text-white rounded-0">-34%</span>
                            </p>
                        </div>
                    </div>
                    <div class="col">
                        <img src="/theme/images/home-fashion-9/pr-s-07.jpg" alt="">
                        <div class="mt-4 text-center">
                            <h6 class="mb-1 text-capitalize fs-14 fw-medium">
                                <a href="product-detail-layout-01.html" class="main_link_teal">
                                    Cream women pants </a>
                            </h6>
                            <p class="mb-0 fs-14 text-muted">
                                <del>$35.00</del>
                            </p>
                        </div>
                    </div>
                    <div class="col">
                        <img src="/theme/images/home-fashion-9/pr-s-08.jpg" alt="">
                        <div class="mt-4 text-center">
                            <h6 class="mb-1 text-capitalize fs-14 fw-medium">
                                <a href="product-detail-layout-01.html" class="main_link_teal">
                                    Black mountain hat </a>
                            </h6>
                            <p class="mb-0 fs-14 text-muted">
                                <del>$50.00</del>
                            </p>
                        </div>
                    </div>
                    <div class="col">
                        <img src="/theme/images/home-fashion-9/pr-s-09.jpg" alt="">
                        <div class="mt-4 text-center">
                            <h6 class="mb-1 text-capitalize fs-14 fw-medium">
                                <a class="product-title db" href="product-detail-layout-01.html">Short Sleeved
                                    Hoodie</a>
                            </h6>
                            <p class="mb-0 fs-14 text-muted">
                                <del>$45.00</del>
                                <span class="text-danger">$30.00</span>
                                <span class="text-danger">$34.00</span>
                            </p>
                        </div>
                    </div>
                    <div class="col">
                        <img src="/theme/images/home-fashion-9/pr-s-10.jpg" alt="">
                        <div class="mt-4 text-center">
                            <h6 class="mb-1 text-capitalize fs-14 fw-medium">
                                <a href="product-detail-layout-01.html" class="main_link_teal">
                                    Black mountain hat </a>
                            </h6>
                            <p class="mb-0 fs-14 text-muted">
                                <del>$35.00</del>
                            </p>
                        </div>
                    </div>
                    <div class="col">
                        <img src="/theme/images/home-fashion-9/pr-s-11.jpg" alt="">
                        <div class="mt-4 text-center">
                            <h6 class="mb-1 text-capitalize fs-14 fw-medium">
                                <a href="product-detail-layout-01.html" class="main_link_teal">
                                    Men pants </a>
                            </h6>
                            <p class="mb-0 fs-14 text-muted">
                                <del>$49.00�$56.00</del>
                            </p>
                        </div>
                    </div>
                    <div class="col">
                        <img src="/theme/images/home-fashion-9/pr-s-12.jpg" alt="">
                        <div class="mt-4 text-center">
                            <h6 class="mb-1 text-capitalize fs-14 fw-medium">
                                <a class="product-title db" href="product-detail-layout-01.html">Skin
                                    Sweatpans</a>
                            </h6>
                            <p class="mb-0 fs-14 text-muted">
                                <del>$75.00</del>
                                <span class="text-danger">$45.00</span>
                                <span class="text-danger">$40.00</span>
                            </p>
                        </div>
                    </div>
                    <div class="col">
                        <img src="/theme/images/home-fashion-9/pr-s-13.jpg" alt="">
                        <div class="mt-4 text-center">
                            <h6 class="mb-1 text-capitalize fs-14 fw-medium">
                                <a href="product-detail-layout-01.html" class="main_link_teal">
                                    Simple Skin T-shirt </a>
                            </h6>
                            <p class="mb-0 fs-14 text-muted">
                                <del>$56.00</del>
                            </p>
                        </div>
                    </div>
                    <div class="col">
                        <img src="/theme/images/home-fashion-9/pr-s-14.jpg" alt="">
                        <div class="mt-4 text-center">
                            <h6 class="mb-1 text-capitalize fs-14 fw-medium">
                                <a href="product-detail-layout-01.html" class="main_link_teal">
                                    Sunny Life </a>
                            </h6>
                            <p class="mb-0 fs-14 text-muted">
                                <del>$68.00</del>
                            </p>
                        </div>
                    </div>
                    <div class="col">
                        <img src="/theme/images/home-fashion-9/pr-s-15.jpg" alt="">
                        <div class="mt-4 text-center">
                            <h6 class="mb-1 text-capitalize fs-14 fw-medium">
                                <a href="product-detail-layout-01.html" class="main_link_teal">
                                    Leather White Trainers </a>
                            </h6>
                            <p class="mb-0 fs-14 text-muted">
                                <del>$20.00</del>
                            </p>
                        </div>
                    </div>
                    <div class="col">
                        <img src="/theme/images/home-fashion-9/pr-s-16.jpg" alt="">
                        <div class="mt-4 text-center">
                            <h6 class="mb-1 text-capitalize fs-14 fw-medium">
                                <a href="product-detail-layout-01.html" class="main_link_teal">
                                    Stripe Long Sleeve Top </a>
                            </h6>
                            <p class="mb-0 fs-14 text-muted">
                                <del>$15.00</del>
                            </p>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn search-model-close" data-bs-dismiss="modal">
                    <i class="las la-times"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- write review model -->
<div class="modal fade modal-overl mx-auto" id="rateUsModel" tabindex="-1" role="dialog" aria-labelledby="cardLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-sm-down modal-dialog-centered h-auto" role="document">
        <div class="modal-content p-2" style="max-width: 420px;">
            <div class="modal-body p-4">
                <a href="#!" data-bs-dismiss="modal" class="fs-35 close position-absolute top-0 end-0" aria-label="Close">
                    <i class="pe-7s-close pegk"></i>
                </a>
                <h2 class="fs-22 mb-3">Rate Us</h2>
                <div class="border p-3 rounded-1">
                    <div class="d-flex align-items-center">
                        <div>
                            <img alt="" src="/theme/images/single-product/layout-02/thumb-sticky.jpg" style="max-height: 75px; max-width: 65px; Width: auto; height: auto; vertical-align: middle;">
                        </div>
                        <div class="ms-2 w-100">
                            <h6 class="mb-1 fs-14 fw-bold">Striped Long Sleeve Top</h6>
                            <div class="d-flex align-items-center gap-2">
                                <div class="kalles-rating-result">
                                    <span class="kalles-rating-result__pipe ,b-1">
                                        <span class="kalles-rating-result__start kalles-rating-result__start--big"></span>
                                        <span class="kalles-rating-result__start kalles-rating-result__start--big"></span>
                                        <span class="kalles-rating-result__start kalles-rating-result__start--big"></span>
                                        <span class="kalles-rating-result__start kalles-rating-result__start--big active"></span>
                                        <span class="text-muted kalles-rating-result__start kalles-rating-result__start--big"></span>
                                    </span>
                                </div>
                                <p class="text-muted mb-0">13 Review</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-4 mt-3">
                    <p class="text-muted mb-0 fw-bold">Quality</p>
                    <div class="kalles-rating-result">
                        <span class="kalles-rating-result__pipe ,b-1">
                            <span class="kalles-rating-result__start kalles-rating-result__start--lg kalles-rating-result__start--big"></span>
                            <span class="kalles-rating-result__start kalles-rating-result__start--lg kalles-rating-result__start--big"></span>
                            <span class="kalles-rating-result__start kalles-rating-result__start--lg kalles-rating-result__start--big"></span>
                            <span class="kalles-rating-result__start kalles-rating-result__start--lg kalles-rating-result__start--big active"></span>
                            <span class="text-muted kalles-rating-result__start kalles-rating-result__start--lg kalles-rating-result__start--big"></span>
                        </span>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="name" role="button" class="fw-medium mb-2 text-muted">Your Name*</label>
                    <input id="name" class="form-control form-control-sm py-2 rounded-0" placeholder="John Smith" type="text">
                </div>
                <div class="mb-3">
                    <label for="email" role="button" class="fw-medium mb-2 text-muted">Your Email*</label>
                    <input id="email" class="form-control form-control-sm py-2 rounded-0" placeholder="example@yourdomain.com" type="text">
                </div>
                <div class="mb-3">
                    <label for="title" role="button" class="fw-medium mb-2 text-muted">Review Title</label>
                    <input id="title" class="form-control form-control-sm py-2 rounded-0" placeholder="Look great" type="text">
                </div>
                <div class="mb-3">
                    <label for="review" role="button" class="fw-medium mb-2 text-muted">Review Content</label>
                    <textarea id="review" rows="9" class="form-control form-control-sm py-2 rounded-0" placeholder="Write something" type="text"></textarea>
                </div>
                <button type="button" data-bs-toggle="modal" data-bs-target="#rateUsModel012" class="btn btn-warning rounded-1 py-2 px-2 fw-semibold">
                    Submit Your Review
                </button>
            </div>
        </div>
    </div>
</div>

<!-- comment model -->
<div class="modal fade modal-overl mx-auto" id="commentModel" tabindex="-1" role="dialog" aria-labelledby="cardLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-sm-down modal-dialog-centered h-auto" role="document">
        <div class="modal-content p-2" style="max-width: 420px;">
            <div class="modal-body p-4">
                <a href="#!" data-bs-dismiss="modal" class="fs-35 close position-absolute top-0 end-0" aria-label="Close">
                    <i class="pe-7s-close pegk"></i>
                </a>

                <div class="rounded-pill d-inline-block align-items-center p-1 bg-light mb-2">
                    <div class="d-flex align-items-center">
                        <p class="mb-0 rounded-pill  bg-warning text-white d-inline-block text-center d-flex justify-content-center align-items-center" style="width: 30px; height: 30px;">P</p>
                        <span class="fw-bold mx-2">Peter</span>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-2 gap-2">
                    <div class="kalles-rating-result my-2">
                        <span class="kalles-rating-result__pipe ,b-1">
                            <span class="kalles-rating-result__start kalles-rating-result__start--big"></span>
                            <span class="kalles-rating-result__start kalles-rating-result__start--big"></span>
                            <span class="kalles-rating-result__start kalles-rating-result__start--big"></span>
                            <span class="kalles-rating-result__start kalles-rating-result__start--big active"></span>
                            <span class="text-muted kalles-rating-result__start kalles-rating-result__start--big"></span>
                        </span>
                    </div>
                    <p class="text-muted mb-0 opacity-75 fs-14">1 month ago</p>
                </div>
                <h6 class="pb-1">Contrary to popular belief</h6>
                <p class="text-muted mb-2">It is a long established fact that a reader will be distracted by the readable content of a page</p>
                <div class="border-bottom py-2"></div>

                <div class="d-flex gap-3 mt-3">
                    <p class="mb-0 rounded-pill  bg-danger text-white d-inline-block text-center d-flex justify-content-center align-items-center" style="min-width: 30px; width: 30px; height: 30px;">A</p>
                    <div>
                        <div class="bg-light py-2 px-3 rounded-2">
                            <span class="fw-bold">AdamStore</span>
                            <span>It is a long established fact that a reader will be distracted by the readable content of a page</span>
                        </div>
                        <p class="text-muted mb-0 text-end mt-2 opacity-75 fs-14">1 month ago</p>
                    </div>
                </div>
                <div class="d-flex gap-3 mt-3">
                    <p class="mb-0 rounded-pill  bg-primary text-white d-inline-block text-center d-flex justify-content-center align-items-center" style="min-width: 30px; width: 30px; height: 30px;">S</p>
                    <div>
                        <div class="bg-light py-2 px-3 rounded-2">
                            <span class="fw-bold">SevenAM</span>
                            <span>It is a long established fact that a reader will be distracted by the readable content of a page</span>
                        </div>
                        <p class="text-muted mb-0 text-end mt-2 opacity-75 fs-14">2 weeks ago</p>
                    </div>
                </div>

                <div class="border-bottom py-2 mb-3"></div>

                <div class="bg-light px-3 py-2 text-muted rounded-2" data-bs-toggle="modal" data-bs-target="#rateUsModel"><span class="fw-bold">Comment</span></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="CODE15OFF" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-0 position-relative">
            <div class="modal-body p-0">
                <button type="button" class="btn-close position-absolute end-0 p-0 lh-lg bg-white rounded-circle" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="row g-0">
                    <div class="col-lg-6">
                        <a href="#!" class="position-relative d-block h-100 copycode-left">
                            <img src="/theme/images/rectangle_3.jpg" alt="" class="img-fluid w-100 h-100 recommended_products">
                            <div class="p-4 position-absolute d-flex top-0 m-auto h-100 w-100 flex-column justify-content-center align-items-center text-white text-center">
                                <h3 class="fs-32">Wait! before you leave...</h3>
                                <p class="mb-30">Get 15% off for your first order</p>
                                <div class="d-flex align-items-center mb-30 copycode">
                                    <button class="btn btn-outline-light rounded-0 fs-16 fw-semibold">CODE15OFF</button>
                                    <svg data-bs-toggle="tooltip" title="Copy to clipboard" class="bg-primary" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" stroke="currentColor" viewBox="0 0 512 512">
                                        <path d="M448 352H288c-17.7 0-32-14.3-32-32V64c0-17.7 14.3-32 32-32H396.1c4.2 0 8.3 1.7 11.3 4.7l67.9 67.9c3 3 4.7 7.1 4.7 11.3V320c0 17.7-14.3 32-32 32zM497.9 81.9L430.1 14.1c-9-9-21.2-14.1-33.9-14.1H288c-35.3 0-64 28.7-64 64V320c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V115.9c0-12.7-5.1-24.9-14.1-33.9zM64 128c-35.3 0-64 28.7-64 64V448c0 35.3 28.7 64 64 64H224c35.3 0 64-28.7 64-64V416H256v32c0 17.7-14.3 32-32 32H64c-17.7 0-32-14.3-32-32V192c0-17.7 14.3-32 32-32H192V128H64z"></path>
                                    </svg>
                                </div>
                                <p class="mb-3 px-4">Use above code to get 15% 0FF for your first order when checkout</p>
                                <button class="btn btn-lg py-2 px-5 lh-lg rounded-pill fs-18 btn-primary">GRAB THE DISCOUNT</button>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-6">
                        <h3 class="p-3 pt-4 border-bottom mb-4 fs-20">Recommended Products</h3>
                        <div class="p-30 pt-0 popup-h">
                            <div class="d-flex mb-4">
                                <img class="img-fluid max-w-100" src="/theme/images/products/recommended_products_01.jpg" alt="">
                                <div class="ms-2">
                                    <h6 class="fs-16 ">Amara Reversible Jacket</h6>
                                    <p class="text-muted mt-3">$47.00</p>
                                    <div class="product-color-list mt-2 gap-2 d-flex align-items-center">
                                        <a href="#!" class="d-inline-block bg_color_violet rounded-circle" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Violet"></a>
                                        <a href="#!" class="d-inline-block bg_color_pink rounded-circle" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Pink"></a>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mb-4">
                                <img class="img-fluid max-w-100" src="/theme/images/products/recommended_products_02.jpg" alt="">
                                <div class="ms-2">
                                    <h6 class="fs-16 ">Anti Slip Exercise Yoga Mat - Cyan</h6>
                                    <p class="text-muted mt-3">$12.00 � $19.00</p>
                                    <div class="product-color-list mt-2 gap-2 d-flex align-items-center">
                                        <a href="#!" class="d-inline-block bg-black rounded-circle" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Black"></a>
                                        <a href="#!" class="d-inline-block bg_color_green rounded-circle" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Green"></a>
                                        <a href="#!" class="d-inline-block bg_color_violet rounded-circle" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Violet"></a>
                                        <a href="#!" class="d-inline-block bg-danger rounded-circle" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Red"></a>
                                        <a href="#!" class="d-inline-block bg_color_pink rounded-circle" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Pink"></a>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mb-4">
                                <img class="img-fluid max-w-100" src="/theme/images/products/recommended_products_03.jpg" alt="">
                                <div class="ms-2">
                                    <h6 class="fs-16 ">Beach Babe - Malibu Bikini</h6>
                                    <p class="text-muted mt-3">$38.00</p>
                                </div>
                            </div>
                            <div class="d-flex">
                                <img class="img-fluid max-w-100" src="/theme/images/products/recommended_products_04.jpg" alt="">
                                <div class="ms-2">
                                    <h6 class="fs-16 ">Black Sportwear</h6>
                                    <p class="text-muted mt-3">$129.00</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div><!-- JAVASCRIPT -->
<script src="/theme/libs/jquery/jquery.min.js"></script>
<script src="/theme/js/store.js"></script>
<script src="/theme/libs/jarallax/jarallax.min.js"></script>
<script src="/theme/libs/swiper/swiper-bundle.min.js"></script>
<script src="/theme/libs/alpinejs/cdn.min.js"></script>
<script src="/theme/libs/jquery-countdown/jquery.countdown.min.js"></script>
<script src="/theme/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/theme/js/product-slider.init.js"></script>
<script src="/theme/js/popup.js"></script>

<script src="/theme/libs/flickity/flickity.pkgd.min.js"></script>
<script src="/theme/js/main.js"></script>
<script src="/theme/js/app.js"></script>
</body>

</html>
