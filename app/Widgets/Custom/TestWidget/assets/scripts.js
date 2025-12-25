// JavaScript for TestWidget Widget
document.addEventListener('DOMContentLoaded', function() {
    console.log('TestWidget Widget loaded');
    
    // Add your custom JavaScript here
    const widgets = document.querySelectorAll('.test_widget-widget');
    
    widgets.forEach(widget => {
        // Initialize widget functionality
        console.log('Initializing TestWidget widget:', widget);
    });
});
