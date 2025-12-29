$(function () {
    AOS.init()
    // Back to top
    $(".js-btt").click(function () {
        $("html, body").animate({ scrollTop: 0 }, 1000)
        return false
    })
    // HEADER SCROLL
    $(window).scroll(function () {
        if ($(window).scrollTop() > 10) {
            $(".js-header").addClass("is-scroll")
        } else {
            $(".js-header").removeClass("is-scroll")
        }
    })
    // ANCHOR SCROLL
    $('a[href^="#"]').click(function (e) {
        e.preventDefault()
        var target = $(this.hash)
        if (target.length) {
            $("html, body").animate({ scrollTop: target.offset().top }, 1000)
        }
    })

    function disableScroll() {
        const gnav = $("#g-nav")
        const scrollY = window.scrollY
        $("body", gnav)
            .css({
                position: "fixed",
                top: `-${scrollY}px`,
                width: "100%",
                overflow: "hidden",
            })
            .data("scrollY", scrollY)
    }
    function enableScroll() {
        const gnav = $("#g-nav")
        const scrollY = $("body").data("scrollY")
        $("body".gnav).css({
            position: "",
            top: "",
            overflow: "",
            width: "",
        })
        window.scrollTo(0, scrollY)
    }
    // function resetMenu() {
    //     $(".c-gnav").removeClass("is-open")
    //     $(".c-gnav, .c-header__hamburger, .js-header").removeClass("is-open")
    // }

    // $(".c-header__hamburger").click(function () {
    //     const gnav = $(".c-gnav")
    //     if (gnav.hasClass("is-open")) {
    //         resetMenu()
    //     } else {
    //         gnav.addClass("is-open")
    //         $(".c-header__hamburger").addClass("is-open")
    //     }
    // })
    // CLICK FORM
    // if ($(window).width() < 1025) {
    //     $(".c-header__form").on("click.toggleForm", function (e) {
    //         e.stopPropagation() // prevent bubbling
    //         $(this).addClass("is-open")
    //         $(this).find("input").focus()
    //     })

    //     // close when clicking outside
    //     $(document).on("click.toggleForm", function (e) {
    //         if (!$(e.target).closest(".c-header__form").length) {
    //             $(".c-header__form").removeClass("is-open")
    //         }
    //     })
    // } else {
    //     // cleanup when resizing up
    //     $(".c-header__form").removeClass("is-open")
    // }

    $(".c-post__desc").each(function () {
        let text = $(this).text()
        if (text.length > 500) {
            $(this).text(text.substring(0, 500) + "...")
        }
    })
    $(".c-post__desc--sm").each(function () {
        let text = $(this).text()
        if (text.length > 100) {
            $(this).text(text.substring(0, 100) + "...")
        }
    })
    $(".c-product__desc").each(function () {
        let text = $(this).text()
        if (text.length > 50) {
            $(this).text(text.substring(0, 50) + "...")
        }
    })
})

// action menu mobile
// Lấy các phần tử cần thiết
// const hamburger = document.querySelector(".c-header__hamburger")
const mobileMenu = document.querySelector(".main-header__mobile-menu") // Menu modal
const mobileClose = document.getElementById("mobileMenuClose") // Nút đóng
const mobileOverlay = document.querySelector(".main-header__mobile-menu-overlay") // Lớp phủ

// Kiểm tra xem menu có tồn tại không

// Hàm đóng menu
const closeMenu = () => {
    mobileMenu.classList.remove("is-open")
    document.body.classList.remove("menu-open") // Xóa class cấm cuộn
}

document.addEventListener("DOMContentLoaded", () => {
    if (mobileClose) {
        mobileClose.addEventListener("click", closeMenu)
    }
    if (mobileOverlay) {
        mobileOverlay.addEventListener("click", closeMenu)
    }
})

//

// Lấy tất cả nút toggle
const toggleButtons = document.querySelectorAll(".mobile-nav__toggle-btn")

toggleButtons.forEach((btn) => {
    btn.addEventListener("click", (e) => {
        e.preventDefault()

        const parentItem = btn.closest(".mobile-nav__item--has-sub")

        // Đóng tất cả menu con khác (nếu muốn mở 1 cái mỗi lần)
        document.querySelectorAll(".mobile-nav__item--has-sub").forEach((item) => {
            if (item !== parentItem) {
                item.classList.remove("mobile-nav__item--open")
            }
        })

        // Toggle menu hiện tại
        parentItem.classList.toggle("mobile-nav__item--open")
    })
})

// Toggle mobile menu
document.addEventListener("DOMContentLoaded", () => {
    const hamburger = document.querySelector(".c-header__hamburger")
    const menu = document.querySelector(".main-header__mobile-menu")

    if (hamburger && menu) {
        hamburger.addEventListener("click", () => {
            menu.classList.toggle("is-open")
        })
    }
})
// ------------------ End toggle mobile menu ----------------------\\

// Dịch vụ - Cập nhật nội dung chi tiết khi click vào item
document.addEventListener("DOMContentLoaded", () => {
    // 1. Lấy tất cả các item dịch vụ
    const serviceItems = document.querySelectorAll(".category-service__item")

    // 2. Lấy các phần tử cần cập nhật nội dung chi tiết
    const serviceDetails = document.querySelector(".service-details")
    const detailImage = serviceDetails ? serviceDetails.querySelector(".service-details__left img") : null
    const detailTitle = serviceDetails ? serviceDetails.querySelector(".service-details__right-title") : null
    const detailText = serviceDetails ? serviceDetails.querySelector(".service-details__right-text") : null

    // data
    const serviceData = {
        "Aquarius Pool": {
            image: "./assets/img/common/service-banner-1.png",
            title: "Aquarius Pool",
            description: "Thư giãn và tận hưởng làn nước mát lạnh tại Hồ bơi Aquarius sang trọng. Hồ bơi được thiết kế với không gian mở, lý tưởng cho việc tắm nắng và ngắm cảnh biển tuyệt đẹp.",
        },
        "Venus Spa": {
            image: "./assets/img/common/service-banner-2.jpg",
            title: "Venus Spa",
            description: "Venus Spa mang đến một loạt các liệu pháp trị liệu và massage để phục hồi sức khỏe và tinh thần của bạn. Tận hưởng sự thư thái tuyệt đối trong không gian yên tĩnh.",
        },
        "Capella Restaurant": {
            image: "./assets/img/common/service-banner-3.jpg",
            title: "Capella Restaurant",
            description: "Thưởng thức ẩm thực đa dạng từ Á sang Âu tại Nhà hàng Capella. Với tầm nhìn tuyệt đẹp, đây là nơi hoàn hảo cho những bữa ăn đáng nhớ.",
        },
        Sundeck: {
            image: "./assets/img/common/service-banner-4.jpg",
            title: "Sundeck",
            description: "Khu vực Sundeck lý tưởng để thư giãn, tắm nắng, hoặc ngắm hoàng hôn với đồ uống yêu thích. Tận hưởng không gian mở và gió biển mát lành.",
        },
        "Carina Bar": {
            image: "./assets/img/common/service-banner-5.jpg",
            title: "Carina Bar",
            description: "Carina Bar phục vụ các loại cocktail đặc sắc, rượu vang và đồ uống giải khát trong không gian hiện đại và sôi động. Là nơi tuyệt vời để giao lưu và thư giãn.",
        },
        "Gemini Gym": {
            image: "./assets/img/common/service-banner-6.jpg",
            title: "Gemini Gym",
            description: "Phòng tập Gym Gemini được trang bị đầy đủ các thiết bị hiện đại, giúp bạn duy trì thói quen tập luyện ngay cả khi đang trong kỳ nghỉ.",
        },
        Events: {
            image: "./assets/img/common/service-banner-7.jpg",
            title: "Events",
            description: "Không gian tổ chức sự kiện đa năng, phù hợp cho các buổi hội nghị, tiệc cưới hoặc các sự kiện cá nhân khác. Chúng tôi cung cấp dịch vụ chuyên nghiệp để đảm bảo sự kiện của bạn thành công.",
        },
    }

    // Hàm cập nhật nội dung chi tiết
    const updateServiceDetails = (data) => {
        if (detailImage) detailImage.src = data.image
        if (detailTitle) detailTitle.textContent = data.title
        if (detailText) detailText.textContent = data.description
    }

    // Gắn sự kiện click cho từng item
    serviceItems.forEach((item) => {
        item.addEventListener("click", () => {
            // Lấy tiêu đề dịch vụ từ item đã click
            const serviceTitleElement = item.querySelector(".category-service__item-title")
            if (!serviceTitleElement) return // Đảm bảo có tiêu đề

            const serviceName = serviceTitleElement.textContent.trim()
            const selectedData = serviceData[serviceName]

            // Kiểm tra xem dữ liệu có tồn tại không và cập nhật
            if (selectedData) {
                updateServiceDetails(selectedData)
            }
        })
    })

    // Tải nội dung chi tiết mặc định khi trang load (chọn item đầu tiên)
    if (serviceItems.length > 0) {
        const defaultTitle = serviceItems[0].querySelector(".category-service__item-title").textContent.trim()
        const defaultData = serviceData[defaultTitle]
        if (defaultData) {
            updateServiceDetails(defaultData)
        }
    }
})
// ------------------ End dịch vụ ----------------------\\

// accordion
const items = document.querySelectorAll(".accordion-item .accordion-header")

function toggleAccordion() {
    const isExpanded = this.getAttribute("aria-expanded") === "true"
    this.setAttribute("aria-expanded", !isExpanded)
    const accordionItem = this.closest(".accordion-item")
    if (!isExpanded) accordionItem.classList.add("accordion-active")
    else accordionItem.classList.remove("accordion-active")
}

items.forEach((item) => item.addEventListener("click", toggleAccordion))

document.addEventListener("DOMContentLoaded", () => {
    const promoCodeIcon = document.querySelector(".promo-code_img")
    const popupOffer = document.querySelector(".popup-offer")
    const closeButton = document.querySelector(".popup-offer__close")

    const openPopup = () => {
        popupOffer.classList.add("is-visible")
        popupOffer.classList.add("popup-offer--near-button")
    }

    const closePopup = () => {
        popupOffer.classList.remove("is-visible")
        popupOffer.classList.remove("popup-offer--near-button")
    }

    // click vào icon để mở popup
    if (promoCodeIcon) {
        promoCodeIcon.addEventListener("click", (e) => {
            e.stopPropagation()
            openPopup()
        })
    }

    // nút X
    if (closeButton) {
        closeButton.addEventListener("click", closePopup)
    }

    // click ra ngoài để đóng
    if (popupOffer) {
        popupOffer.addEventListener("click", (event) => {
            if (event.target === popupOffer) {
                closePopup()
            }
        })
    }
})
