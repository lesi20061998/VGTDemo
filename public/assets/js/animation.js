document.addEventListener('DOMContentLoaded', () => {
  // animation for news article main and sidebar
  const elements = document.querySelectorAll(
    '.news-article__main, .news-article__sidebar',
  )
  const observer = new IntersectionObserver(
    (entries, obs) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('animate')
          obs.unobserve(entry.target) // chỉ chạy 1 lần
        }
      })
    },
    { threshold: 0.2 }, // hiển thị 20% là trigger
  )

  elements.forEach((el) => observer.observe(el))
  // ------------------ end animation for news article main and sidebar ----------------------\\

  // animation for news detail heading
  const heading = document.querySelector('.news-detail__heading')
  if (heading) {
    const observer = new IntersectionObserver(
      (entries, obs) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('animate')
            obs.unobserve(entry.target)
          }
        })
      },
      { threshold: 0.2 },
    )

    observer.observe(heading)
  }
  // ------------------ end animation for news detail heading ----------------------\\

  // animation for news detail toc
  const elementsArticleSidebarTitle = document.querySelectorAll(
    '.news-article__main, .news-article__sidebar, .news-detail__heading, .news-detail__toc',
  )
  const observersArticleSidebarTitle = new IntersectionObserver(
    (entries, obs) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('animate')
          obs.unobserve(entry.target)
        }
      })
    },
    { threshold: 0.2 },
  )

  elementsArticleSidebarTitle.forEach((el) =>
    observersArticleSidebarTitle.observe(el),
  )
  // ------------------ end animation for news detail toc ----------------------\\
})

// Toggle mobile menu
document.addEventListener('DOMContentLoaded', () => {
  const hamburger = document.querySelector('.c-header__hamburger')
  const menu = document.querySelector('.c-header__main--left')

  if (hamburger && menu) {
    hamburger.addEventListener('click', () => {
      menu.classList.toggle('is-open')
    })
  }
})
// ------------------ End toggle mobile menu ----------------------\\




// ABOUT
// animation section tầm nhìn sứ mệnh
document.addEventListener('DOMContentLoaded', () => {
  const items = document.querySelectorAll('.about-vision-mission__item')

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('active')
          observer.unobserve(entry.target) // chỉ chạy 1 lần
        }
      })
    },
    { threshold: 0.3 }, // scroll tới 30% là kích hoạt
  )

  items.forEach((item) => observer.observe(item))
})
// ------------------ end animation section tầm nhìn sứ mệnh ----------------------\\

// animation văn hóa doanh nghiệp
document.addEventListener('DOMContentLoaded', () => {
  const cultureSection = document.querySelector('.about-culture')

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          cultureSection.classList.add('active')
          // Nếu chỉ muốn chạy 1 lần thì unobserve
          observer.unobserve(cultureSection)
        }
      })
    },
    { threshold: 0.3 }, // hiện 30% section thì kích hoạt
  )

  if (cultureSection) observer.observe(cultureSection)
})
// ------------------  end animation section tầm nhìn sứ mệnh ----------------------\\

// animation Lịch sử hình thành
document.addEventListener('DOMContentLoaded', () => {
  const timelineSlider = document.querySelector('.timeline__slider')
  const dots = document.querySelectorAll('.time')

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          // console.log('Timeline in view')
          timelineSlider.classList.add('active')
          observer.unobserve(timelineSlider) // chỉ chạy 1 lần
        }
      })
    },
    { threshold: 0.2 }, // chỉ cần thấy 20% là chạy
  )

  if (timelineSlider) observer.observe(timelineSlider)

  if (dots.length > 0) {
    const observerDot = new IntersectionObserver(
      (entries, obs) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('active')
            obs.unobserve(entry.target)
          }
        })
      },
      { threshold: 0.5 },
    )

    dots.forEach((dot) => observerDot.observe(dot))
  }
})

// ------------------ end section Lịch sử hình thành ----------------------\\

// animation văn hóa doanh nghiệp
document.addEventListener('DOMContentLoaded', () => {
  const img = document.querySelector('.about-culture__image-bg')

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible')
        }
      })
    },
    { threshold: 0.2 }, // 20% vào màn hình mới chạy
  )

  if (img) observer.observe(img)
})

//------------------  animation văn hóa doanh nghiệp  ----------------------\\

// END ABOUT