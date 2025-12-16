const IMAGE_CACHE = 'theme-images-v1';
const cachedUrls = new Set();

if ('IntersectionObserver' in window && 'caches' in window) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                const url = img.src;
                if (!cachedUrls.has(url)) {
                    caches.open(IMAGE_CACHE).then(cache => {
                        cache.match(url).then(response => {
                            if (!response) {
                                fetch(url).then(r => cache.put(url, r));
                            }
                            cachedUrls.add(url);
                        });
                    });
                }
                observer.unobserve(img);
            }
        });
    }, {rootMargin: '50px'});
    
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('img[data-cache-img]').forEach(img => observer.observe(img));
    });
}
