/**
 * Reviews Configuration Helper for Client-side
 * Provides easy access to reviews settings from the server
 */
class ReviewsConfig {
    constructor() {
        this.config = null;
        this.loaded = false;
    }

    /**
     * Load reviews configuration from server
     * @returns {Promise<Object>} Reviews configuration
     */
    async load() {
        if (this.loaded && this.config) {
            return this.config;
        }

        try {
            const response = await fetch('/api/reviews/config');
            const result = await response.json();
            
            if (result.success) {
                this.config = result.data;
                this.loaded = true;
                return this.config;
            } else {
                throw new Error('Failed to load reviews configuration');
            }
        } catch (error) {
            console.error('Error loading reviews config:', error);
            // Return default configuration as fallback
            this.config = this.getDefaultConfig();
            return this.config;
        }
    }

    /**
     * Get default configuration as fallback
     * @returns {Object} Default reviews configuration
     */
    getDefaultConfig() {
        return {
            enabled: true,
            require_login: true,
            require_purchase: false,
            auto_approve: false,
            allow_images: true,
            max_images: 5,
            min_rating: 1,
            show_verified: true,
            reward_points: 0,
            enable_product: true,
            enable_post: false,
            align: 'left',
            display_order: 10,
            template: 'template1',
            fake_type: 'preset',
            enable_fake: false,
            default_sort: 'newest',
            per_page: 10,
            allow_helpful: true
        };
    }

    /**
     * Check if reviews are enabled
     * @returns {Promise<boolean>}
     */
    async isEnabled() {
        const config = await this.load();
        return config.enabled;
    }

    /**
     * Check if login is required for reviews
     * @returns {Promise<boolean>}
     */
    async requiresLogin() {
        const config = await this.load();
        return config.require_login;
    }

    /**
     * Check if purchase is required for reviews
     * @returns {Promise<boolean>}
     */
    async requiresPurchase() {
        const config = await this.load();
        return config.require_purchase;
    }

    /**
     * Check if images are allowed in reviews
     * @returns {Promise<boolean>}
     */
    async allowsImages() {
        const config = await this.load();
        return config.allow_images;
    }

    /**
     * Get maximum number of images allowed
     * @returns {Promise<number>}
     */
    async getMaxImages() {
        const config = await this.load();
        return config.max_images;
    }

    /**
     * Get minimum rating allowed
     * @returns {Promise<number>}
     */
    async getMinRating() {
        const config = await this.load();
        return config.min_rating;
    }

    /**
     * Check if verified badge should be shown
     * @returns {Promise<boolean>}
     */
    async showsVerified() {
        const config = await this.load();
        return config.show_verified;
    }

    /**
     * Get reward points for reviews
     * @returns {Promise<number>}
     */
    async getRewardPoints() {
        const config = await this.load();
        return config.reward_points;
    }

    /**
     * Check if reviews are enabled for products
     * @returns {Promise<boolean>}
     */
    async isEnabledForProducts() {
        const config = await this.load();
        return config.enable_product;
    }

    /**
     * Check if reviews are enabled for posts
     * @returns {Promise<boolean>}
     */
    async isEnabledForPosts() {
        const config = await this.load();
        return config.enable_post;
    }

    /**
     * Get review template
     * @returns {Promise<string>}
     */
    async getTemplate() {
        const config = await this.load();
        return config.template;
    }

    /**
     * Get default sort order
     * @returns {Promise<string>}
     */
    async getDefaultSort() {
        const config = await this.load();
        return config.default_sort;
    }

    /**
     * Get reviews per page
     * @returns {Promise<number>}
     */
    async getPerPage() {
        const config = await this.load();
        return config.per_page;
    }

    /**
     * Check if helpful votes are allowed
     * @returns {Promise<boolean>}
     */
    async allowsHelpful() {
        const config = await this.load();
        return config.allow_helpful;
    }

    /**
     * Get alignment setting
     * @returns {Promise<string>}
     */
    async getAlign() {
        const config = await this.load();
        return config.align;
    }

    /**
     * Get display order
     * @returns {Promise<number>}
     */
    async getDisplayOrder() {
        const config = await this.load();
        return config.display_order;
    }

    /**
     * Get full configuration object
     * @returns {Promise<Object>}
     */
    async getConfig() {
        return await this.load();
    }

    /**
     * Validate rating against minimum requirement
     * @param {number} rating - Rating to validate
     * @returns {Promise<boolean>}
     */
    async isValidRating(rating) {
        const minRating = await this.getMinRating();
        return rating >= minRating;
    }

    /**
     * Validate image count against maximum allowed
     * @param {number} imageCount - Number of images
     * @returns {Promise<boolean>}
     */
    async isValidImageCount(imageCount) {
        const allowsImages = await this.allowsImages();
        if (!allowsImages) return imageCount === 0;
        
        const maxImages = await this.getMaxImages();
        return imageCount <= maxImages;
    }
}

// Create global instance
window.ReviewsConfig = new ReviewsConfig();

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ReviewsConfig;
}