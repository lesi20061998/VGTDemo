    <!-- BEST SELLER -->
    <section>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="text-center">
                        <div class="mb-2">
                            <h3 class="section-title position-relative flex text-uppercase">
                                <span>Best Seller</span>
                            </h3>
                        </div>
                        <span class="section-subtitle sub-title font-secondary fst-italic fs-14 text-muted">Top sale in
                            this week</span>
                    </div>
                </div><!--end col-->
            </div><!--end row-->
            <div class="row g-lg-4 g-3 mt-4">
                <div class="col-md-3 col-6">
                    <div x-data="{ imageUrl: '/theme/images/products/pr-29.jpg', isHovered: false }" class="topbar-product-card pb-3" x-on:mouseenter="isHovered = true" x-on:mouseleave="isHovered = false">
                        <div class="position-relative overflow-hidden">
                            <img :src="isHovered ? 'theme/images/products/pr-30.jpg' : imageUrl" alt="" class="img-fluid object-fit-cover w-100">
                            <a href="#" class="d-lg-none position-absolute " style="z-index: 1; top:10px; left:10px;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o text-white"></i></a>
                            <a href="#" class="wishlistadd d-none d-lg-flex position-absolute" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o text-white"></i></a>
    
                            <div class="product-button d-none d-lg-flex flex-column gap-2">
                                <a href="#exampleModal" data-bs-toggle="modal" class="btn rounded-pill fs-14"><span>Quick View</span> <i class="iccl iccl-eye"></i></a>
                                <button type="button" class="btn rounded-pill fs-14" data-bs-toggle="modal" data-bs-target="#cardModal" class="btn rounded-pill fs-14"><span>Quick Shop</span>
                                    <i class="iccl iccl-cart"></i></button>
                            </div>
                            <div class="position-absolute d-lg-none bottom-0 end-0 d-flex flex-column bg-white rounded-pill m-2" style="z-index: 1;">
                                <a href="#exampleModal" data-bs-toggle="modal" class="btn responsive-cart rounded-pill fs-14 p-2" style="width:36px; height: 36px;"><i class="iccl iccl-eye fw-semibold"></i></a>
                                <button type="button" class="btn responsive-cart rounded-pill fs-14 p-2" style="width:36px; height: 36px;" data-bs-toggle="modal" data-bs-target="#cardModal" class="btn rounded-pill fs-14">
                                    <i class="iccl iccl-cart fw-semibold"></i></button>
                            </div>
                        </div>
                        <a href="product-detail-layout-01.html" class="mt-3 d-block">
                            <h6 class="mb-1"> City Backpack Black</h6>
                            <p class="mb-0 fs-14 text-muted">
                                <span>$55.00</span>
                            </p>
                        </a>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div x-data="{ imageUrl: '/theme/images/home-metro/pr-q10.jpg', isHovered: false }" class="topbar-product-card pb-3" x-on:mouseenter="isHovered = true" x-on:mouseleave="isHovered = false">
                        <div class="position-relative overflow-hidden">
                            <img :src="isHovered ? 'theme/images/home-metro/pr-q11.jpg' : imageUrl" alt="" class="img-fluid object-fit-cover w-100">
                            <a href="#" class="d-lg-none position-absolute " style="z-index: 1; top:10px; left:10px;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o text-white"></i></a>
                            <a href="#" class="wishlistadd d-none d-lg-flex position-absolute" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o text-white"></i></a>
    
                            <div class="product-button d-none d-lg-flex flex-column gap-2">
                                <a href="#exampleModal" data-bs-toggle="modal" class="btn rounded-pill fs-14"><span>Quick View</span> <i class="iccl iccl-eye"></i></a>
                                <button type="button" class="btn rounded-pill fs-14" data-bs-toggle="modal" data-bs-target="#cardModal" class="btn rounded-pill fs-14"><span>Quick Shop</span>
                                    <i class="iccl iccl-cart"></i></button>
                            </div>
                            <div class="position-absolute d-lg-none bottom-0 end-0 d-flex flex-column bg-white rounded-pill m-2" style="z-index: 1;">
                                <a href="#exampleModal" data-bs-toggle="modal" class="btn responsive-cart rounded-pill fs-14 p-2" style="width:36px; height: 36px;"><i class="iccl iccl-eye fw-semibold"></i></a>
                                <button type="button" class="btn responsive-cart rounded-pill fs-14 p-2" style="width:36px; height: 36px;" data-bs-toggle="modal" data-bs-target="#cardModal" class="btn rounded-pill fs-14">
                                    <i class="iccl iccl-cart fw-semibold"></i></button>
                            </div>
                        </div>
                        <a href="product-detail-layout-01.html" class="mt-3 d-block">
                            <h6 class="mb-1"> Women Black Pants </h6>
                            <p class="mb-0 fs-14 text-muted">
                                <span>$100.00</span>
                            </p>
                        </a>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div x-data="{ imageUrl: '/theme/images/products/pr-15.jpg', isHovered: false }" class="topbar-product-card pb-3" x-on:mouseenter="isHovered = true" x-on:mouseleave="isHovered = false">
                        <div class="position-relative overflow-hidden">
                            <img :src="isHovered ? 'theme/images/products/pr-14.jpg' : imageUrl" alt="" class="img-fluid w-100 object-fit-cover" src="/theme/images/products/pr-15.jpg">
                            <a href="#" class="d-lg-none position-absolute " style="z-index: 1; top:10px; left:10px;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o text-white"></i></a>
                            <a href="#" class="wishlistadd d-none d-lg-flex position-absolute" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o text-white"></i></a>
    
                            <div class="bg-overlay"></div>
                            <div class="product-button d-none d-lg-flex flex-column gap-2">
                                <a href="#exampleModal" data-bs-toggle="modal" class="btn rounded-pill fs-14"><span>Quick View</span> <i class="iccl iccl-eye"></i></a>
                                <button type="button" class="btn rounded-pill fs-14" data-bs-toggle="modal" data-bs-target="#cardModal" class="btn rounded-pill fs-14"><span>Quick Shop</span>
                                    <i class="iccl iccl-cart"></i></button>
                            </div>
                            <div class="position-absolute d-lg-none bottom-0 end-0 d-flex flex-column bg-white rounded-pill m-2" style="z-index: 1;">
                                <a href="#exampleModal" data-bs-toggle="modal" class="btn responsive-cart rounded-pill fs-14 p-2" style="width:36px; height: 36px;"><i class="iccl iccl-eye fw-semibold"></i></a>
                                <button type="button" class="btn responsive-cart rounded-pill fs-14 p-2" style="width:36px; height: 36px;" data-bs-toggle="modal" data-bs-target="#cardModal" class="btn rounded-pill fs-14">
                                    <i class="iccl iccl-cart fw-semibold"></i></button>
                            </div>
                        </div>
                        <div class="mt-3">
                            <h6 class="mb-1"><a href="#!" class="product-title">Mercury Tee</a></h6>
                            <p class="mb-0 fs-14 text-muted">
                                <span class="text-muted">$68.00</span>
                            </p>
                            <div class="product-color-list mt-2 gap-2 d-flex align-items-center">
                                <a href="#!" x-on:click.prevent="imageUrl = 'theme/images/home-metro/pr-q1.jpg'; isHovered = false" style="background: url('/theme/images/home-metro/pr-q1.jpg');background-size: cover;" class="d-inline-block bg-body-tertiary rounded-circle"></a>
                                <a href="#!" x-on:click.prevent="imageUrl = 'theme/images/home-metro/pr-q2.jpg'; isHovered = false" style="background: url('/theme/images/home-metro/pr-q2.jpg');background-size: cover;" class="d-inline-block bg_color_pink rounded-circle"></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div x-data="{ imageUrl: '/theme/images/products/pr-12.jpg' }" class="topbar-product-card pb-3">
                        <div class="position-relative overflow-hidden">
                            <img :src="imageUrl" alt="" class="img-fluid object-fit-cover w-100">
                            <a href="#" class="d-lg-none position-absolute " style="z-index: 1; top:10px; left:10px;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o text-white"></i></a>
                            <a href="#" class="wishlistadd d-none d-lg-flex position-absolute" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o text-white"></i></a>
    
                            <div class="product-button d-none d-lg-flex flex-column gap-2">
                                <a href="#exampleModal" data-bs-toggle="modal" class="btn rounded-pill fs-14"><span>Quick View</span> <i class="iccl iccl-eye"></i></a>
                                <button type="button" class="btn rounded-pill fs-14" data-bs-toggle="modal" data-bs-target="#cardModal" class="btn rounded-pill fs-14"><span>Quick Shop</span>
                                    <i class="iccl iccl-cart"></i></button>
                            </div>
                            <div class="position-absolute d-lg-none bottom-0 end-0 d-flex flex-column bg-white rounded-pill m-2" style="z-index: 1;">
                                <a href="#exampleModal" data-bs-toggle="modal" class="btn responsive-cart rounded-pill fs-14 p-2" style="width:36px; height: 36px;"><i class="iccl iccl-eye fw-semibold"></i></a>
                                <button type="button" class="btn responsive-cart rounded-pill fs-14 p-2" style="width:36px; height: 36px;" data-bs-toggle="modal" data-bs-target="#cardModal" class="btn rounded-pill fs-14">
                                    <i class="iccl iccl-cart fw-semibold"></i></button>
                            </div>
                        </div>
                        <div class="mt-3">
                            <h6 class="mb-1"><a href="product-detail-layout-01.html" class="product-title">Men pants</a>
                            </h6>
                            <p class="mb-0 fs-14 text-muted">
                                <span>$49.00 – $56.00</span>
                            </p>
                            <div class="product-color-list mt-2 gap-2 d-flex align-items-center">
                                <a href="#!" x-on:mouseover="imageUrl = 'theme/images/products/pr-12.jpg'" x-on:click.prevent="imageUrl = 'theme/images/products/pr-12.jpg'" class="d-inline-block bg_color_blue rounded-circle"></a>
                                <a href="#!" x-on:mouseover="imageUrl = 'theme/images/products/pr-34.jpg'" x-on:click.prevent="imageUrl = 'theme/images/products/pr-34.jpg'" class="d-inline-block bg-dark rounded-circle"></a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- second row -->
                <div class="col-md-3 col-6">
                    <div x-data="{ imageUrl: '/theme/images/products/pr-21.jpg' }" class="topbar-product-card pb-3">
                        <div class="position-relative overflow-hidden">
                            <span class="new-label bg-danger text-white rounded-circle"> -34% </span>
                            <img :src="imageUrl" alt="" class="img-fluid object-fit-cover w-100">
                            <a href="#" class="d-lg-none position-absolute " style="z-index: 1; top:10px; left:10px;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o text-white"></i></a>
                            <a href="#" class="wishlistadd d-none d-lg-flex position-absolute" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o text-white"></i></a>
    
                            <div class="product-button d-none d-lg-flex flex-column gap-2">
                                <a href="#exampleModal" data-bs-toggle="modal" class="btn rounded-pill fs-14"><span>Quick View</span> <i class="iccl iccl-eye"></i></a>
                                <button type="button" class="btn rounded-pill fs-14" data-bs-toggle="modal" data-bs-target="#cardModal" class="btn rounded-pill fs-14"><span>Quick Shop</span>
                                    <i class="iccl iccl-cart"></i></button>
                            </div>
                            <div class="position-absolute d-lg-none bottom-0 end-0 d-flex flex-column bg-white rounded-pill m-2" style="z-index: 1;">
                                <a href="#exampleModal" data-bs-toggle="modal" class="btn responsive-cart rounded-pill fs-14 p-2" style="width:36px; height: 36px;"><i class="iccl iccl-eye fw-semibold"></i></a>
                                <button type="button" class="btn responsive-cart rounded-pill fs-14 p-2" style="width:36px; height: 36px;" data-bs-toggle="modal" data-bs-target="#cardModal" class="btn rounded-pill fs-14">
                                    <i class="iccl iccl-cart fw-semibold"></i></button>
                            </div>
                            <p class="product-size mb-0 text-center text-white fw-medium"> S, M, L</p>
                        </div>
                        <div class="mt-3">
                            <h6 class="mb-1"><a href="product-detail-layout-01.html" class="product-title">Short Sleeved
                                    Hoodie</a></h6>
                            <p class="mb-0 fs-14 text-muted">
                                <del>$45.00</del>
                                <span class="text-danger">$30.00</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div x-data="{ imageUrl: '/theme/images/products/pr-33.jpg', isHovered: false }" class="topbar-product-card pb-3" x-on:mouseenter="isHovered = true" x-on:mouseleave="isHovered = false">
                        <div class="position-relative overflow-hidden">
                            <img :src="isHovered ? 'theme/images/products/pr-34.jpg' : imageUrl" alt="" class="img-fluid w-100 object-fit-cover" src="/theme/images/products/pr-33.jpg">
                            <a href="#" class="d-lg-none position-absolute " style="z-index: 1; top:10px; left:10px;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o text-white"></i></a>
                            <a href="#" class="wishlistadd d-none d-lg-flex position-absolute" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o text-white"></i></a>
    
                            <div class="product-button d-none d-lg-flex flex-column gap-2">
                                <a href="#exampleModal" data-bs-toggle="modal" class="btn rounded-pill fs-14"><span>Quick View</span> <i class="iccl iccl-eye"></i></a>
                                <button type="button" class="btn rounded-pill fs-14" data-bs-toggle="modal" data-bs-target="#cardModal" class="btn rounded-pill fs-14"><span>Quick Shop</span>
                                    <i class="iccl iccl-cart"></i></button>
                            </div>
                            <div class="position-absolute d-lg-none bottom-0 end-0 d-flex flex-column bg-white rounded-pill m-2" style="z-index: 1;">
                                <a href="#exampleModal" data-bs-toggle="modal" class="btn responsive-cart rounded-pill fs-14 p-2" style="width:36px; height: 36px;"><i class="iccl iccl-eye fw-semibold"></i></a>
                                <button type="button" class="btn responsive-cart rounded-pill fs-14 p-2" style="width:36px; height: 36px;" data-bs-toggle="modal" data-bs-target="#cardModal" class="btn rounded-pill fs-14">
                                    <i class="iccl iccl-cart fw-semibold"></i></button>
                            </div>
                            <p class="product-size mb-0 text-center text-white fw-medium">S, M, L</p>
                        </div>
                        <div class="mt-3 d-block">
                            <h6 class="mb-1"> Sweatshirt In Geometric Print</h6>
                            <p class="mb-0 fs-14 text-muted">
                                <span>$36.00</span>
                            </p>
                            <div class="product-color-list mt-2 gap-2 d-flex align-items-center">
                                <a href="#!" x-on:mouseover="imageUrl = 'theme/images/products/pr-33.jpg'" x-on:click.prevent="imageUrl = 'theme/images/products/pr-33.jpg'" class="d-inline-block bg-dark rounded-circle"></a>
                                <a href="#!" x-on:mouseover="imageUrl = 'theme/images/products/pr-34.jpg'" x-on:click.prevent="imageUrl = 'theme/images/products/pr-34.jpg'" class="d-inline-block bg_color_blue rounded-circle"></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div x-data="{ imageUrl: '/theme/images/products/pr-23.jpg' }" class="topbar-product-card pb-3">
                        <div class="position-relative overflow-hidden">
                            <img :src="imageUrl" alt="" class="img-fluid object-fit-cover w-100">
                            <a href="#" class="d-lg-none position-absolute " style="z-index: 1; top:10px; left:10px;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o text-white"></i></a>
                            <a href="#" class="wishlistadd d-none d-lg-flex position-absolute" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o text-white"></i></a>
    
                            <div class="product-button d-none d-lg-flex flex-column gap-2">
                                <a href="#exampleModal" data-bs-toggle="modal" class="btn rounded-pill fs-14"><span>Quick View</span> <i class="iccl iccl-eye"></i></a>
                                <button type="button" class="btn rounded-pill fs-14" data-bs-toggle="modal" data-bs-target="#cardModal" class="btn rounded-pill fs-14"><span>Quick Shop</span>
                                    <i class="iccl iccl-cart"></i></button>
                            </div>
                            <div class="position-absolute d-lg-none bottom-0 end-0 d-flex flex-column bg-white rounded-pill m-2" style="z-index: 1;">
                                <a href="#exampleModal" data-bs-toggle="modal" class="btn responsive-cart rounded-pill fs-14 p-2" style="width:36px; height: 36px;"><i class="iccl iccl-eye fw-semibold"></i></a>
                                <button type="button" class="btn responsive-cart rounded-pill fs-14 p-2" style="width:36px; height: 36px;" data-bs-toggle="modal" data-bs-target="#cardModal" class="btn rounded-pill fs-14">
                                    <i class="iccl iccl-cart fw-semibold"></i></button>
                            </div>
                        </div>
                        <div class="mt-3">
                            <h6 class="mb-1"><a href="product-detail-layout-01.html" class="product-title">Dusk Pom
                                    Beanie</a></h6>
                            <p class="mb-0 fs-14 text-muted">
                                <span>$25.00</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div x-data="{ imageUrl: '/theme/images/home-metro/pr-q4.jpg', isHovered: false }" class="topbar-product-card pb-3" x-on:mouseenter="isHovered = true" x-on:mouseleave="isHovered = false">
                        <div class="position-relative overflow-hidden">
                            <img :src="isHovered ? 'theme/images/home-metro/pr-q5.jpg' : imageUrl" alt="" class="img-fluid object-fit-cover w-100">
                            <a href="#" class="d-lg-none position-absolute " style="z-index: 1; top:10px; left:10px;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o text-white"></i></a>
                            <a href="#" class="wishlistadd d-none d-lg-flex position-absolute" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o text-white"></i></a>
    
                            <div class="product-button d-none d-lg-flex flex-column gap-2">
                                <a href="#exampleModal" data-bs-toggle="modal" class="btn rounded-pill fs-14"><span>Quick View</span> <i class="iccl iccl-eye"></i></a>
                                <button type="button" class="btn rounded-pill fs-14" data-bs-toggle="modal" data-bs-target="#cardModal" class="btn rounded-pill fs-14"><span>Quick Shop</span>
                                    <i class="iccl iccl-cart"></i></button>
                            </div>
                            <div class="position-absolute d-lg-none bottom-0 end-0 d-flex flex-column bg-white rounded-pill m-2" style="z-index: 1;">
                                <a href="#exampleModal" data-bs-toggle="modal" class="btn responsive-cart rounded-pill fs-14 p-2" style="width:36px; height: 36px;"><i class="iccl iccl-eye fw-semibold"></i></a>
                                <button type="button" class="btn responsive-cart rounded-pill fs-14 p-2" style="width:36px; height: 36px;" data-bs-toggle="modal" data-bs-target="#cardModal" class="btn rounded-pill fs-14">
                                    <i class="iccl iccl-cart fw-semibold"></i></button>
                            </div>
                        </div>
                        <a href="product-detail-layout-01.html" class="mt-3 d-block">
                            <h6 class="mb-1"> Circle Snapback Cap </h6>
                            <p class="mb-0 fs-14 text-muted">
                                <span>$25.00</span>
                            </p>
                        </a>
                    </div>
                </div>
            </div><!--end row-->
        </div>
    </section>
    <!-- blog -->
