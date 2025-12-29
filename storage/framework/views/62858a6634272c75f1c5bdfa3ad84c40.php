<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/svg+xml" href="<?php echo e(asset('themes/victorious/favicon.svg')); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Victorious Cruise'); ?></title>
    <meta name="description" content="<?php echo $__env->yieldContent('description', 'Victorious Cruise - Luxury Cruise Experience'); ?>">
    
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Petrona:wght@100..900&display=swap" rel="stylesheet">
    
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css">
    
    
    <link rel="stylesheet" href="<?php echo e(asset('themes/victorious/css/style.css')); ?>">
    
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <?php echo $__env->make('frontend.themes.victorious.partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <main class="p-top">
        <?php echo $__env->yieldContent('content'); ?>
        
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($widgetArea)): ?>
            <?php echo render_widgets($widgetArea); ?>

        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </main>
    
    <?php echo $__env->make('frontend.themes.victorious.partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="<?php echo e(asset('themes/victorious/js/main.js')); ?>"></script>
    <script src="<?php echo e(asset('themes/victorious/js/slider.js')); ?>"></script>
    
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\core_laravel\Core_system\resources\views/frontend/themes/victorious/layout.blade.php ENDPATH**/ ?>