$(function () {
    var heroSlider = new Swiper(".js-mainvisual-post-slider", {
        lazy: true,
        loop: true,

        // Default (mobile first)
        direction: "horizontal",
        slidesPerView: 1,
        spaceBetween: 16,
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        breakpoints: {
            1025: {
                // PC and up
                direction: "vertical",
                slidesPerView: 2,
                spaceBetween: 36,
                pagination: false,
            },
        },
    })
    // DANH MUC SAN PHAM SLIDER
    var cateSlider = new Swiper(".js-cate-slider", {
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },

        // default (SP first)
        slidesPerView: 1,
        spaceBetween: 16,

        breakpoints: {
            1025: {
                slidesPerView: 3,
                spaceBetween: 36,
            },
        },
    })
    // VE THUAN AN PHAT SLIDER
    let aboutSlider = null

    function initAboutSlider() {
        const nextEl = document.querySelector(".swiper-button-next")
        const prevEl = document.querySelector(".swiper-button-prev")

        if (window.innerWidth < 767) {
            if (!aboutSlider) {
                aboutSlider = new Swiper(".js-about-slider", {
                    loop: true,
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false,
                    },
                    navigation: {
                        nextEl,
                        prevEl,
                    },
                    slidesPerView: 1,
                    spaceBetween: 16,
                })
            }

            // ✅ show arrows only on SP
            if (nextEl && prevEl) {
                nextEl.style.display = "block"
                prevEl.style.display = "block"
            }
        } else {
            // destroy swiper on desktop
            if (aboutSlider) {
                aboutSlider.destroy(true, true)
                aboutSlider = null
            }

            // ✅ hide arrows on PC
            if (nextEl && prevEl) {
                nextEl.style.display = "none"
                prevEl.style.display = "none"
            }
        }
    }

    // run on load + resize
    window.addEventListener("load", initAboutSlider)
    window.addEventListener("resize", initAboutSlider)

    // SAN PHAM SLIDER
    var productSlider = new Swiper(".js-products-slider", {
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },

        slidesPerView: 1,
        spaceBetween: 16,

        breakpoints: {
            // Tablet
            768: {
                slidesPerView: 2,
                spaceBetween: 24,
            },
            // PC
            1025: {
                slidesPerView: 4,
                spaceBetween: 36,
            },
        },

        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
    })

    // BRAND SLIDER

    // Check if user prefers reduced motion
    const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches

    // Initialize Swiper with autoplay conditionally based on prefers-reduced-motion
    const brandSlider = new Swiper(".brand-slider", {
        lazy: true,
        slidesPerView: "auto",
        spaceBetween: 36,
        loop: true,
        speed: 4000,
        allowTouchMove: false,
        autoplay: {
            delay: 1,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
        },
    })
    // Initialize Swiper with autoplay conditionally based on prefers-reduced-motion
    const produtCatSlider = new Swiper(".js-category-list", {
        lazy: true,
        slidesPerView: 3,
        spaceBetween: 36,
        loop: true,
        speed: 4000,
        allowTouchMove: false,

        autoplay: {
            delay: 1,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },

        // Breakpoints (theo min-width)
        breakpoints: {
            0: {
                slidesPerView: 1, // mobile
                spaceBetween: 16,
                allowTouchMove: true,
            },
            767: {
                slidesPerView: 2, // tablet
                spaceBetween: 24,
                allowTouchMove: true,
            },
            1025: {
                slidesPerView: 3, // desktop
                spaceBetween: 36,
                allowTouchMove: false,
            },
        },
    })

    // PRODUCT DETAILS SLIDer
    var productDetailsSwiper = new Swiper(".js-product-details", {
        spaceBetween: 10,
        freeMode: true,
        watchSlidesProgress: true,

        breakpoints: {
            // when window width is >= 1200px
            1200: {
                slidesPerView: 3,
            },
            // when window width is >= 1025px and < 1200px
            1025: {
                slidesPerView: 2,
            },
            // when window width is < 1025px
            0: {
                slidesPerView: 3,
            },
        },
    })
    var productDetailsThumb = new Swiper(".js-product-details-thumb", {
        spaceBetween: 10,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        thumbs: {
            swiper: productDetailsSwiper,
        },
    })
})

window.addEventListener("load", function () {
    const swiper = new Swiper(".room-categories__swiper", {
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        slidesPerView: 3,
        spaceBetween: 30,

        navigation: {
            nextEl: ".room-categories__slider .swiper-button-next",
            prevEl: ".room-categories__slider .swiper-button-prev",
        },

        breakpoints: {
            0: { slidesPerView: 1 },
            768: { slidesPerView: 2 },
            1024: { slidesPerView: 3 },
        },
    })
})

window.addEventListener("load", function () {
    const swiper = new Swiper(".special-offers-rd__swiper", {
        loop: true,
        speed: 800,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        slidesPerView: 2,
        spaceBetween: 30,

        navigation: {
            nextEl: ".special-offers-rd__slider .swiper-button-next",
            prevEl: ".special-offers-rd__slider .swiper-button-prev",
        },

        breakpoints: {
            0: { slidesPerView: 1 },
            768: { slidesPerView: 2 },
            1024: { slidesPerView: 2 },
        },
    })
})


window.addEventListener("load", function () {
    // lấy tất cả swiper trong danh sách
    const sliders = document.querySelectorAll(".general-swiper")

    sliders.forEach((sliderEl) => {
        const swiper = new Swiper(sliderEl, {
            loop: true,
            speed: 900,

            navigation: {
                nextEl: sliderEl.querySelector(".swiper-button-next"),
                prevEl: sliderEl.querySelector(".swiper-button-prev"),
            },

            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },

            slidesPerView: 1,

            on: {
                slideChange: function () {
                    // counter trong cùng slider
                    const leftEl = sliderEl.querySelector(".swiper-counter__left")
                    const rightEl = sliderEl.querySelector(".swiper-counter__right")

                    if (leftEl && rightEl) {
                        const current = this.realIndex + 1
                        const total = this.slides.length - this.loopedSlides * 2

                        const formatNumber = (n) => String(n).padStart(2, "0")

                        leftEl.textContent = formatNumber(current)
                        rightEl.textContent = formatNumber(total)
                    }
                },

                init: function () {
                    this.emit("slideChange")
                },
            },
        })
    })
})

window.addEventListener("load", function () {
    // lấy tất cả swiper trong danh sách
    const sliders = document.querySelectorAll(".large-swiper")

    sliders.forEach((sliderEl) => {
        const swiper = new Swiper(sliderEl, {
            loop: true,
            speed: 800,

            navigation: {
                nextEl: sliderEl.querySelector(".swiper-button-next"),
                prevEl: sliderEl.querySelector(".swiper-button-prev"),
            },

            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },

            slidesPerView: 1,

            on: {
                slideChange: function () {
                    // counter trong cùng slider
                    const leftEl = sliderEl.querySelector(".swiper-counter__left")
                    const rightEl = sliderEl.querySelector(".swiper-counter__right")

                    if (leftEl && rightEl) {
                        const current = this.realIndex + 1
                        const total = this.slides.length - this.loopedSlides * 2

                        const formatNumber = (n) => String(n).padStart(2, "0")

                        leftEl.textContent = formatNumber(current)
                        rightEl.textContent = formatNumber(total)
                    }
                },

                init: function () {
                    this.emit("slideChange")
                },
            },
        })
    })
})
