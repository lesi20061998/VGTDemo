/**
 * Review Popup Helper
 * Simple helper functions to integrate review popup into any page
 */

// Global helper functions
window.ReviewPopupHelper = {
    /**
     * Open review popup for a product
     * @param {number|string} productId - Product ID
     * @param {string} productName - Product name (optional)
     * @param {string} productImage - Product image URL (optional)
     */
    openForProduct: function(productId, productName = '', productImage = '') {
        if (window.openReviewPopup) {
            window.openReviewPopup(productId, productName, productImage);
        } else {
            console.error('Review popup not initialized. Make sure to include <x-review-popup /> component.');
        }
    },

    /**
     * Add review button to product cards
     * @param {string} selector - CSS selector for product cards
     * @param {object} options - Configuration options
     */
    addReviewButtons: function(selector, options = {}) {
        const products = document.querySelectorAll(selector);
        
        products.forEach(product => {
            const productId = product.dataset.productId || options.productId;
            const productName = product.dataset.productName || options.productName || '';
            const productImage = product.dataset.productImage || options.productImage || '';
            
            if (!productId) {
                console.warn('Product ID not found for product card:', product);
                return;
            }
            
            // Create review button
            const reviewBtn = document.createElement('button');
            reviewBtn.className = options.buttonClass || 'px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors';
            reviewBtn.textContent = options.buttonText || 'Đánh giá';
            reviewBtn.onclick = () => this.openForProduct(productId, productName, productImage);
            
            // Find where to insert the button
            const targetElement = product.querySelector(options.insertSelector || '.product-actions') || product;
            
            if (options.insertPosition === 'prepend') {
                targetElement.insertBefore(reviewBtn, targetElement.firstChild);
            } else {
                targetElement.appendChild(reviewBtn);
            }
        });
    },

    /**
     * Add floating review button
     * @param {object} options - Configuration options
     */
    addFloatingButton: function(options = {}) {
        const floatingBtn = document.createElement('button');
        floatingBtn.id = 'floating-review-btn';
        floatingBtn.className = options.className || 'fixed bottom-6 right-6 w-14 h-14 bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 z-40 transition-all duration-300';
        floatingBtn.innerHTML = options.icon || `
            <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
            </svg>
        `;
        floatingBtn.title = options.title || 'Viết đánh giá';
        
        floatingBtn.onclick = () => {
            this.openForProduct(
                options.productId || document.querySelector('[data-product-id]')?.dataset.productId,
                options.productName || document.querySelector('[data-product-name]')?.dataset.productName || '',
                options.productImage || document.querySelector('[data-product-image]')?.dataset.productImage || ''
            );
        };
        
        document.body.appendChild(floatingBtn);
        
        // Auto-hide on scroll (optional)
        if (options.hideOnScroll) {
            let lastScrollTop = 0;
            window.addEventListener('scroll', () => {
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                if (scrollTop > lastScrollTop) {
                    floatingBtn.style.transform = 'translateY(100px)';
                } else {
                    floatingBtn.style.transform = 'translateY(0)';
                }
                lastScrollTop = scrollTop;
            });
        }
        
        return floatingBtn;
    },

    /**
     * Initialize review triggers based on data attributes
     */
    initAutoTriggers: function() {
        // Auto-initialize buttons with data-review-trigger attribute
        document.querySelectorAll('[data-review-trigger]').forEach(trigger => {
            const productId = trigger.dataset.productId;
            const productName = trigger.dataset.productName || '';
            const productImage = trigger.dataset.productImage || '';
            
            if (productId) {
                trigger.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.openForProduct(productId, productName, productImage);
                });
            }
        });
    },

    /**
     * Show review prompt after purchase
     * @param {object} orderData - Order information
     */
    showPostPurchasePrompt: function(orderData) {
        // Show a subtle prompt after a delay
        setTimeout(() => {
            const prompt = document.createElement('div');
            prompt.className = 'fixed bottom-4 left-4 bg-white border border-gray-200 rounded-lg shadow-lg p-4 max-w-sm z-50';
            prompt.innerHTML = `
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-sm font-medium text-gray-900">Cảm ơn bạn đã mua hàng!</h4>
                        <p class="text-sm text-gray-500 mt-1">Hãy chia sẻ trải nghiệm của bạn để giúp khách hàng khác.</p>
                        <div class="mt-3 flex space-x-2">
                            <button onclick="ReviewPopupHelper.openForProduct('${orderData.productId}', '${orderData.productName}', '${orderData.productImage}')" class="text-xs bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                                Viết đánh giá
                            </button>
                            <button onclick="this.closest('.fixed').remove()" class="text-xs text-gray-500 hover:text-gray-700">
                                Để sau
                            </button>
                        </div>
                    </div>
                    <button onclick="this.closest('.fixed').remove()" class="flex-shrink-0 text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;
            
            document.body.appendChild(prompt);
            
            // Auto-remove after 10 seconds
            setTimeout(() => {
                if (prompt.parentNode) {
                    prompt.remove();
                }
            }, 10000);
            
        }, 2000); // Show after 2 seconds
    },

    /**
     * Check if user can review a product
     * @param {number|string} productId - Product ID
     * @returns {Promise<boolean>}
     */
    canReview: async function(productId) {
        try {
            const config = await window.ReviewsConfig.getConfig();
            
            if (!config.enabled) return false;
            if (config.require_login && !this.isLoggedIn()) return false;
            
            // Additional checks can be added here (e.g., purchase verification)
            
            return true;
        } catch (error) {
            console.error('Error checking review permissions:', error);
            return false;
        }
    },

    /**
     * Check if user is logged in
     * @returns {boolean}
     */
    isLoggedIn: function() {
        // This should be set by your authentication system
        return window.isAuthenticated || false;
    }
};

// Auto-initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.ReviewPopupHelper.initAutoTriggers();
});

// Backward compatibility
window.openReviewPopup = window.ReviewPopupHelper.openForProduct;