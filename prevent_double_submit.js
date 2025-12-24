// ===================================
// PREVENT DOUBLE SUBMIT FOR SETTINGS FORMS
// Thêm vào layout hoặc settings page
// ===================================

document.addEventListener('DOMContentLoaded', function() {
    // Prevent double submit for all forms
    const forms = document.querySelectorAll('form');
    
    forms.forEach(function(form) {
        let isSubmitting = false;
        
        form.addEventListener('submit', function(e) {
            // Nếu đang submit thì prevent
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }
            
            // Đánh dấu đang submit
            isSubmitting = true;
            
            // Disable submit buttons
            const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
            submitButtons.forEach(function(btn) {
                btn.disabled = true;
                
                // Thay đổi text để user biết đang xử lý
                if (btn.tagName === 'BUTTON') {
                    btn.dataset.originalText = btn.textContent;
                    btn.textContent = 'Đang lưu...';
                }
            });
            
            // Reset sau 5 giây để tránh stuck
            setTimeout(function() {
                isSubmitting = false;
                submitButtons.forEach(function(btn) {
                    btn.disabled = false;
                    if (btn.dataset.originalText) {
                        btn.textContent = btn.dataset.originalText;
                    }
                });
            }, 5000);
        });
    });
    
    // Prevent multiple clicks on save buttons
    const saveButtons = document.querySelectorAll('[data-save-button]');
    saveButtons.forEach(function(btn) {
        let clickCount = 0;
        
        btn.addEventListener('click', function(e) {
            clickCount++;
            
            if (clickCount > 1) {
                e.preventDefault();
                return false;
            }
            
            // Reset click count sau 2 giây
            setTimeout(function() {
                clickCount = 0;
            }, 2000);
        });
    });
});

// Utility function để show loading state
function showLoadingState(button, loadingText = 'Đang xử lý...') {
    if (button) {
        button.disabled = true;
        button.dataset.originalText = button.textContent;
        button.textContent = loadingText;
    }
}

function hideLoadingState(button) {
    if (button && button.dataset.originalText) {
        button.disabled = false;
        button.textContent = button.dataset.originalText;
    }
}