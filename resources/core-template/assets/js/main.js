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
    function resetMenu() {
        $(".c-gnav").removeClass("is-open")
        $(".c-gnav, .c-header__hamburger, .js-header").removeClass("is-open")
    }

    $(".c-header__hamburger").click(function () {
        const gnav = $(".c-gnav")
        if (gnav.hasClass("is-open")) {
            resetMenu()
        } else {
            gnav.addClass("is-open")
            $(".c-header__hamburger").addClass("is-open")
        }
    })
    // CLICK FORM  
    if ($(window).width() < 1025) {
        $(".c-header__form").on("click.toggleForm", function (e) {
            e.stopPropagation() // prevent bubbling
            $(this).addClass("is-open")
            $(this).find("input").focus()
        })

        // close when clicking outside
        $(document).on("click.toggleForm", function (e) {
            if (!$(e.target).closest(".c-header__form").length) {
                $(".c-header__form").removeClass("is-open")
            }
        })
    } else {
        // cleanup when resizing up
        $(".c-header__form").removeClass("is-open")
    }

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
