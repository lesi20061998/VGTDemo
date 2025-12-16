// Đảm bảo DOM đã load xong trước khi khởi tạo Swiper

/* global Swiper */
document.addEventListener("DOMContentLoaded", function () {
    const swiperElement = document.querySelector(".swiper")
    let productSwiper = null

    function setupSwiper() {
        if (!swiperElement || typeof Swiper === "undefined") return

        const width = window.innerWidth

        // Product Swiper
        if (width <= 1024) {
            const slides = width <= 480 ? 1 : 2
            const space = width <= 480 ? 10 : 16

            if (!productSwiper) {
                productSwiper = new Swiper(".swiper-product-card-list", {
                    slidesPerView: slides,
                    spaceBetween: space,
                    loop: true,
                    pagination: {
                        el: ".swiper-pagination-product-card-list",
                        clickable: true,
                    },
                    navigation: {
                        nextEl: ".swiper-product-card-list .swiper-button-next",
                        prevEl: ".swiper-product-card-list .swiper-button-prev",
                    },
                })
            } else {
                // Update params when resizing between mobile and tablet
                productSwiper.params.slidesPerView = slides
                productSwiper.params.spaceBetween = space
                productSwiper.params.loop = true
                productSwiper.update()
            }
        } else {
            // Destroy on desktop (>1024px)
            if (productSwiper) {
                productSwiper.destroy(true, true)
                productSwiper = null
            }
        }
        // ------------------ end product swiper ----------------------\\


        // slide about partners ---------------------\\
        const aboutPartnerSwiper = new Swiper(".about-partners__slider", {
            slidesPerView: 4,
            spaceBetween: 60,
            loop: true,
            freeMode: true,
            speed: 6000,
            autoplay: {
                delay: 0,
                disableOnInteraction: false,
                reverseDirection: true,
            },
            breakpoints: {
                0: {
                    slidesPerView: 2,
                    spaceBetween: 20,
                },
                768: {
                    slidesPerView: 3,
                    spaceBetween: 28,
                },
                1024: {
                    slidesPerView: 5,
                    spaceBetween: 36,
                },
            },
        })
        // ------------------ end slide about partners ----------------------\\

        // Hover: dừng autoplay
        const sliderEl = document.querySelector(".about-partners__slider")
        if (sliderEl) {
            sliderEl.addEventListener("mouseenter", () => {
                aboutPartnerSwiper.autoplay.stop()
            })
            sliderEl.addEventListener("mouseleave", () => {
                aboutPartnerSwiper.autoplay.start()
            })
        }
        // end slide about partners ----------------------\\
    }

    setupSwiper()
    window.addEventListener("resize", setupSwiper)
})

// Sidebar Slider
document.addEventListener("DOMContentLoaded", function () {
    let sidebarSwiper = null

    function setupSidebarSwiper() {
        const width = window.innerWidth

        // Sidebar: toggle static layout on tablet/desktop, Swiper on mobile
        const sidebarContainer = document.querySelector(".swiper-news__sidebar-list")
        const sidebarWrapper = sidebarContainer ? sidebarContainer.querySelector(".swiper-wrapper") : null

        if (sidebarContainer && sidebarWrapper) {
            if (width <= 480) {
                // Chỉ Mobile: dùng Swiper
                sidebarWrapper.classList.remove("news-article__sidebar-list")
                const slidesSidebar = 1
                const spaceSidebar = 10

                if (!sidebarSwiper) {
                    sidebarSwiper = new Swiper(".swiper-news__sidebar-list", {
                        slidesPerView: slidesSidebar,
                        spaceBetween: spaceSidebar,
                        loop: true,
                        navigation: {
                            nextEl: ".news-article__sidebar .swiper-button-next",
                            prevEl: ".news-article__sidebar .swiper-button-prev",
                        },
                    })
                } else {
                    sidebarSwiper.params.slidesPerView = slidesSidebar
                    sidebarSwiper.params.spaceBetween = spaceSidebar
                    sidebarSwiper.params.loop = true
                    sidebarSwiper.update()
                }
            } else {
                // Tablet/Desktop: destroy Swiper và dùng layout static
                if (sidebarSwiper) {
                    sidebarSwiper.destroy(true, true)
                    sidebarSwiper = null
                }
                sidebarWrapper.classList.add("news-article__sidebar-list")
            }
        }
    }

    setupSidebarSwiper()
    window.addEventListener("resize", setupSidebarSwiper)
})

// Popular News Slider
document.addEventListener("DOMContentLoaded", function () {
    let popularNewsSwiper = null

    function setupPopularNewsSwiper() {
        const width = window.innerWidth

        // Popular news: toggle static layout on desktop, Swiper on <=1024
        const popularContainer = document.querySelector(".swiper-news__popular-list")
        const popularWrapper = popularContainer ? popularContainer.querySelector(".swiper-wrapper") : null

        if (popularContainer && popularWrapper) {
            if (width <= 1024) {
                // Use Swiper on mobile/tablet
                popularWrapper.classList.remove("news__popular-list")
                // const containerWidth = popularContainer.offsetWidth
                // const slides = containerWidth <= 480 ? 1 : 2

                const slides = width <= 480 ? 1 : 2
                const space = width <= 480 ? 10 : 16

                if (!popularNewsSwiper) {
                    popularNewsSwiper = new Swiper(".swiper-news__popular-list", {
                        slidesPerView: slides,
                        spaceBetween: space,
                        loop: true,
                        navigation: {
                            nextEl: ".news__popular .swiper-button-next",
                            prevEl: ".news__popular .swiper-button-prev",
                        },
                    })
                } else {
                    popularNewsSwiper.params.slidesPerView = slides
                    popularNewsSwiper.params.spaceBetween = space
                    popularNewsSwiper.params.loop = true
                    popularNewsSwiper.update()
                }
            } else {
                // Desktop: destroy Swiper and apply stacked layout class
                if (popularNewsSwiper) {
                    popularNewsSwiper.destroy(true, true)
                    popularNewsSwiper = null
                }
                popularWrapper.classList.add("news__popular-list")
            }
        }
        // ------------------ End Popular news ----------------------\\
    }

    setupPopularNewsSwiper()
    window.addEventListener("resize", setupPopularNewsSwiper)
})

// News Articles Slider
document.addEventListener("DOMContentLoaded", function () {
    let newsArticleSwiper = null

    function setupNewsArticleSwiper() {
        const width = window.innerWidth

        // News articles: toggle static layout on desktop, Swiper on <=1024
        const articleContainer = document.querySelector("#swiper-news-article-list")
        const articleWrapper = articleContainer ? articleContainer.querySelector(".swiper-wrapper") : null

        if (articleContainer && articleWrapper) {
            if (width <= 1024) {
                articleWrapper.classList.remove("news-article-list")
                const slidesNews = 1
                const spaceNews = width <= 480 ? 10 : 16

                if (!newsArticleSwiper) {
                    newsArticleSwiper = new Swiper("#swiper-news-article-list", {
                        slidesPerView: slidesNews,
                        spaceBetween: spaceNews,
                        loop: true,
                        watchOverflow: true,
                        observer: true,
                        observeParents: true,
                        updateOnWindowResize: true,
                        navigation: {
                            nextEl: ".news-article__main .swiper-button-next",
                            prevEl: ".news-article__main .swiper-button-prev",
                        },
                    })
                    newsArticleSwiper.slideTo(0, 0)
                } else {
                    newsArticleSwiper.params.slidesPerView = slidesNews
                    newsArticleSwiper.params.spaceBetween = spaceNews
                    newsArticleSwiper.params.loop = true
                    newsArticleSwiper.update()
                    newsArticleSwiper.slideTo(0, 0)
                }
            } else {
                if (newsArticleSwiper) {
                    newsArticleSwiper.destroy(true, true)
                    newsArticleSwiper = null
                }
                articleWrapper.classList.add("news-article-list")
            }
        }
        // ------------------ End News articles ----------------------\\
    }

    setupNewsArticleSwiper()
    window.addEventListener("resize", setupNewsArticleSwiper)
})

// ABOUT

// slide about certificate
document.addEventListener("DOMContentLoaded", () => {
    let achievementsSwiper = null

    function setupAchievementsSwiper() {
        const container = document.querySelector(".about-achievements__slider")
        const wrapper = container ? container.querySelector(".swiper-wrapper") : null
        const width = window.innerWidth

        if (container && wrapper) {
            if (width <= 1024) {
                wrapper.style.display = ""
                wrapper.style.gridTemplateColumns = ""
                wrapper.style.gap = ""

                if (!achievementsSwiper) {
                    achievementsSwiper = new Swiper(".about-achievements__slider", {
                        slidesPerView: width <= 480 ? 1 : 2,
                        spaceBetween: width <= 480 ? 10 : 16,
                        loop: true,
                        navigation: {
                            nextEl: ".about-achievements__slider .swiper-button-next",
                            prevEl: ".about-achievements__slider .swiper-button-prev",
                        },
                    })
                } else {
                    achievementsSwiper.params.slidesPerView = width <= 480 ? 1 : 2
                    achievementsSwiper.params.spaceBetween = width <= 480 ? 10 : 16
                    achievementsSwiper.update()
                }
            } else {
                if (achievementsSwiper) {
                    achievementsSwiper.destroy(true, true)
                    achievementsSwiper = null
                }

                wrapper.style.display = "flex"
                wrapper.style.gap = "36px"
            }
        }
    }

    setupAchievementsSwiper()
    window.addEventListener("resize", setupAchievementsSwiper)
})
// ------------------ end slide about certificate ----------------------\\

// slide category about
document.addEventListener("DOMContentLoaded", () => {
    const tabs = document.querySelectorAll(".about-products__tab")

    // Initialize Swiper cho About Products
    window.aboutProductsSwiper = new Swiper(".about-products__slider-wrapper", {
        slidesPerView: 2,
        spaceBetween: 36,
        loop: true, // nên để false khi thay slide động
        pagination: {
            el: ".about-products__pagination",
            clickable: true,
        },
        navigation: {
            nextEl: ".about-products__slider-wrapper .swiper-button-next",
            prevEl: ".about-products__slider-wrapper .swiper-button-prev",
        },
        breakpoints: {
            0: { slidesPerView: 1 },
            768: { slidesPerView: 2 },
            1024: { slidesPerView: 2 },
        },
    })

    // Dữ liệu demo
    const data = {
        0: [
            {
                img: "./assets/img/about/product.jpg",
                title: "Máy gia công kim loại A",
            },
            {
                img: "./assets/img/about/product.jpg",
                title: "Máy gia công kim loại B",
            },
        ],
        1: [
            { img: "./assets/img/about/product.jpg", title: "Máy cơ khí chế tạo A" },
            { img: "./assets/img/about/product.jpg", title: "Máy cơ khí chế tạo B" },
        ],
        2: [
            { img: "./assets/img/about/product.jpg", title: "Dụng cụ cơ khí A" },
            { img: "./assets/img/about/product.jpg", title: "Dụng cụ cơ khí B" },
        ],
    }

    tabs.forEach((tab, index) => {
        tab.addEventListener("click", () => {
            // Active tab
            tabs.forEach((t) => t.classList.remove("about-products__tab--active"))
            tab.classList.add("about-products__tab--active")

            // Xóa hết slide cũ bằng API
            window.aboutProductsSwiper.removeAllSlides()

            // Tạo slide mới
            const newSlides = data[index].map(
                (item) => `
        <div class="about-products__item swiper-slide">
          <div class="about-products__image-wrapper">
            <img src="${item.img}" alt="${item.title}" class="about-products__image">
          </div>
          <p class="about-products__name">${item.title}</p>
        </div>`,
            )

            // Append vào Swiper
            window.aboutProductsSwiper.appendSlide(newSlides)
            window.aboutProductsSwiper.update()
        })
    })
})
// ------------------ end slide category about ----------------------\\

// Lịch sử hình thành
document.addEventListener("DOMContentLoaded", () => {
    const timelineSwiper = new Swiper(".timeline__slider", {
        slidesPerView: 3,
        spaceBetween: 30,
        loop: true,
        navigation: {
            nextEl: ".timeline__slider .swiper-button-next",
            prevEl: ".timeline__slider .swiper-button-prev",
        },
        pagination: {
            el: ".swiper-pagination-timeline",
            clickable: true,
        },
        breakpoints: {
            0: { slidesPerView: 1 },
            768: { slidesPerView: 2 },
            1024: { slidesPerView: 3 },
        },
    })

    timelineSwiper.init()
})
// ------------------ end Lịch sử hình thành  ----------------------\\

// END ABOUT
