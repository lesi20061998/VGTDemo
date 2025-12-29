

<?php $__env->startSection('title', 'Home page | Victorious Cruise'); ?>

<?php $__env->startSection('content'); ?>
    
    <?php echo render_widgets('homepage-main'); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.themes.victorious.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\core_laravel\Core_system\resources\views/frontend/themes/victorious/home.blade.php ENDPATH**/ ?>