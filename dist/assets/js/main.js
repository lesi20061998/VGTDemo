$(function () {
    AOS.init()
    // Back to top
    $(".js-btt").click(function () {
        $("html, body").animate({ scrollTop: 0 }, 1000)
        return false
    })
    // ANCHOR SCROLL
    $('a[href^="#"]').click(function (e) {
        e.preventDefault()
        var target = $(this.hash)
        if (target.length) {
            $("html, body").animate({ scrollTop: target.offset().top }, 1000)
        }
    })
    // HEADER SCROLL
    const $topHeader = $("#topHeader")

    $(window).on("scroll", function () {
        // Only run scroll logic when menu is closed
        if (!$(".c-header__nav-container").hasClass("is-open")) {
            if ($(this).scrollTop() > 0) {
                $topHeader.addClass("is-fixed")
            } else {
                $topHeader.removeClass("is-fixed")
            }
        }
    })

    // MENU TOGGLE
    let scrollPosition = 0

    function disableScroll() {
        scrollPosition = window.scrollY
        $("body").css({
            position: "fixed",
            top: `-${scrollPosition}px`,
            width: "100%",
            overflow: "hidden",
        })
    }

    function enableScroll() {
        $("body").css({
            position: "",
            top: "",
            overflow: "",
            width: "",
        })
        window.scrollTo(0, scrollPosition)
    }

    function resetMenu() {
        $(".c-header__nav-container").removeClass("is-open")
        $(".c-header__hamburger").removeClass("is-open")
        enableScroll()

        // If page is at top, remove fixed class
        if ($(window).scrollTop() === 0) {
            $topHeader.removeClass("is-fixed")
        }
    }

    function openMenu() {
        $(".c-header__nav-container").addClass("is-open")
        $(".c-header__hamburger").addClass("is-open")
        $topHeader.addClass("is-fixed") // keep fixed when menu open
        disableScroll()
    }

    $(".c-header__hamburger").click(function () {
        const navContainer = $(".c-header__nav-container")
        if (navContainer.hasClass("is-open")) {
            resetMenu()
        } else {
            openMenu()
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
