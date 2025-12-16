// Menu functionality
document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileToggle = document.querySelector('.mobile-menu-toggle');
    const mainMenu = document.querySelector('.main-menu');
    
    if (mobileToggle && mainMenu) {
        mobileToggle.addEventListener('click', function() {
            mainMenu.classList.toggle('active');
            
            // Update aria-expanded attribute
            const isExpanded = mainMenu.classList.contains('active');
            mobileToggle.setAttribute('aria-expanded', isExpanded);
        });
    }
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        if (mainMenu && mainMenu.classList.contains('active')) {
            if (!mainMenu.contains(event.target) && !mobileToggle.contains(event.target)) {
                mainMenu.classList.remove('active');
                mobileToggle.setAttribute('aria-expanded', 'false');
            }
        }
    });
    
    // Handle keyboard navigation
    const menuItems = document.querySelectorAll('.menu-item');
    
    menuItems.forEach(item => {
        const link = item.querySelector('.menu-link');
        const subMenu = item.querySelector('.sub-menu');
        
        if (link && subMenu) {
            // Handle keyboard navigation for dropdowns
            link.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    item.classList.toggle('keyboard-focus');
                }
                
                if (e.key === 'Escape') {
                    item.classList.remove('keyboard-focus');
                    link.blur();
                }
            });
            
            // Handle focus out
            item.addEventListener('focusout', function(e) {
                // Check if focus is moving to another element within the same menu item
                setTimeout(() => {
                    if (!item.contains(document.activeElement)) {
                        item.classList.remove('keyboard-focus');
                    }
                }, 100);
            });
        }
    });
    
    // Add keyboard focus styles
    const style = document.createElement('style');
    style.textContent = `
        .menu-item.keyboard-focus .sub-menu {
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
        }
    `;
    document.head.appendChild(style);
    
    // Smooth scroll for anchor links
    const anchorLinks = document.querySelectorAll('.menu-link[href^="#"]');
    
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            if (href === '#') return;
            
            const target = document.querySelector(href);
            
            if (target) {
                e.preventDefault();
                
                // Close mobile menu if open
                if (mainMenu && mainMenu.classList.contains('active')) {
                    mainMenu.classList.remove('active');
                    mobileToggle.setAttribute('aria-expanded', 'false');
                }
                
                // Smooth scroll to target
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                
                // Update URL without jumping
                history.pushState(null, null, href);
            }
        });
    });
    
    // Add active class to current page menu item
    const currentPath = window.location.pathname;
    const menuLinks = document.querySelectorAll('.menu-link');
    
    menuLinks.forEach(link => {
        const linkPath = new URL(link.href).pathname;
        
        if (linkPath === currentPath) {
            link.classList.add('active');
            
            // Also add active class to parent menu item if this is a sub-menu item
            const parentMenuItem = link.closest('.sub-menu')?.closest('.menu-item');
            if (parentMenuItem) {
                parentMenuItem.querySelector('.menu-link').classList.add('active-parent');
            }
        }
    });
});

// Utility function to create mobile menu toggle button
function createMobileMenuToggle() {
    const toggle = document.createElement('button');
    toggle.className = 'mobile-menu-toggle';
    toggle.setAttribute('aria-label', 'Toggle menu');
    toggle.setAttribute('aria-expanded', 'false');
    toggle.innerHTML = `
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
        </svg>
    `;
    
    return toggle;
}

// Export for use in other scripts
window.MenuUtils = {
    createMobileMenuToggle
};