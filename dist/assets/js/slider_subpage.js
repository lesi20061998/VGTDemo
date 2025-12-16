// Sản phẩm liên quan
$(document).ready(function () {
    // Swiper: Slider
    new Swiper(".c-related-products__slider", {
        loop: true,
        navigation: {
            nextEl: ".c-related-products .swiper-button-next",
            prevEl: ".c-related-products .swiper-button-prev",
        },
        pagination: {
            clickable: true,
        },
        slidesPerView: 2,
        paginationClickable: true,
        spaceBetween: 20,
        breakpoints: {
            1920: {
                slidesPerView: 6,
                spaceBetween: 36,
            },
            1028: {
                slidesPerView: 6,
                spaceBetween: 36,
            },
            480: {
                slidesPerView: 3,
                spaceBetween: 10,
            },
        },
    })
})
// ------------------ end Sản phẩm liên quan  ----------------------\\

// article-list slider
$(document).ready(function () {
    // Swiper: Slider
    new Swiper(".js-article-list__slider", {
        loop: true,
        navigation: {
            nextEl: ".c-article-list__wrapper .swiper-button-next",
            prevEl: ".c-article-list__wrapper .swiper-button-prev",
        },
        pagination: {
            clickable: true,
        },
        // slidesPerView: 3,
        paginationClickable: true,
        spaceBetween: 20,
        breakpoints: {
            1028: {
                slidesPerView: 3,
                spaceBetween: 36,
            },
            767: {
                slidesPerView: 2,
                spaceBetween: 20,
            },
            480: {
                slidesPerView: 1,
                spaceBetween: 10,
            },
        },
    })
})
// ------------------ end  ----------------------\\
