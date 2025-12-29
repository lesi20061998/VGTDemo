<div class="service-details">
    <div class="service-details__content">
        <div class="service-details__left <?php echo e($layout === 'image-right' ? 'order-2' : ''); ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($image): ?>
            <img src="<?php echo e($image); ?>" alt="<?php echo e($title); ?>">
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <div class="service-details__right <?php echo e($layout === 'image-right' ? 'order-1' : ''); ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($decorImage): ?>
            <img src="<?php echo e($decorImage); ?>" alt="" class="service-details__right-decor-img">
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <h3 class="service-details__right-title"><?php echo e($title); ?></h3>
            <div class="service-details__right-text"><?php echo $content; ?></div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($buttonText): ?>
            <a href="<?php echo e($buttonLink); ?>" class="c-btn c-btn--book-room">
                <div class="btn-inner">
                    <span><?php echo e($buttonText); ?></span>
                    <span class="c-btn__arrow">
                        <img src="<?php echo e(asset('themes/victorious/img/icon/next-2.svg')); ?>" alt="">
                    </span>
                </div>
                <div class="btn-hover">
                    <span><?php echo e($buttonText); ?></span>
                    <span class="c-btn__arrow">
                        <img src="<?php echo e(asset('themes/victorious/img/icon/next.svg')); ?>" alt="">
                    </span>
                </div>
            </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\core_laravel\Core_system\resources\views/widgets/victorious/service-detail.blade.php ENDPATH**/ ?>