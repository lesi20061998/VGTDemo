$(document).ready(function () {
    // Initialize thumbs
    let thumbSwiper = new Swiper(".js-product-details-thumb", {
        direction: window.innerWidth > 768 ? "vertical" : "horizontal",
        slidesPerView: window.innerWidth > 768 ? 5 : 5,
        spaceBetween: 50,
        watchSlidesProgress: true,
        breakpoints: {
            0: { direction: "horizontal" },
            769: { direction: "vertical" },
        },
    })

    // Initialize main swiper
    let mainSwiper = new Swiper(".js-product-details", {
        spaceBetween: 36,
        thumbs: {
            swiper: thumbSwiper,
        },
    })
    $(".product-sizes__item").on("click", function () {
        // Remove active from all items in this list
        $(this).addClass("active").siblings().removeClass("active")
    })
    $(".product-nums__group").each(function () {
        const $group = $(this)
        const $input = $group.find(".product-nums__input")
        const $plus = $group.find(".product-nums__btn--plus")
        const $minus = $group.find(".product-nums__btn--minus")

        // Increase
        $plus.on("click", function () {
            let currentValue = parseInt($input.val()) || 1
            $input.val(currentValue + 1)
        })

        // Decrease
        $minus.on("click", function () {
            let currentValue = parseInt($input.val()) || 1
            if (currentValue > 1) {
                $input.val(currentValue - 1)
            }
        })

        // Prevent invalid input
        $input.on("input", function () {
            let val = $(this)
                .val()
                .replace(/[^0-9]/g, "")
            if (val === "" || parseInt(val) < 1) val = 1
            $(this).val(val)
        })
    })
    $(".file-upload__trigger").on("click", function () {
        $(this).siblings(".file-upload__input").click()
    })

    $(".file-upload__input").on("change", function () {
        const file = this.files[0]
        if (file) {
            alert(`Bạn đã chọn: ${file.name}`)
        }
    })
})
