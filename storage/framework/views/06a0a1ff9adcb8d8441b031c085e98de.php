<section class="special-offers l-container l-pd-0-135px">
    <div class="special-offers__header">
        <h2 class="special-offers__title"><?php echo e($title); ?></h2>
        <a href="<?php echo e($viewAllLink); ?>" class="special-offers__view-all">
            VIEW MORE <img src="<?php echo e(asset('themes/victorious/img/icon/next.svg')); ?>" alt="">
        </a>
    </div>
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($offersLarge) > 0): ?>
    <div class="special-offers__grid-top">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $offersLarge; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $offer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e($offer['link'] ?? '#'); ?>" class="offer-card offer-card--large">
            <img src="<?php echo e($offer['image'] ?? ''); ?>" alt="<?php echo e($offer['title'] ?? ''); ?>">
            <div class="offer-card__overlay">
                <h3 class="offer-card__title"><?php echo e(strtoupper($offer['title'] ?? '')); ?></h3>
                <p class="offer-card__link">
                    VIEW MORE <img src="<?php echo e(asset('themes/victorious/img/icon/next.svg')); ?>" alt="">
                </p>
            </div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($offersSmall) > 0): ?>
    <div class="special-offers__grid-bottom">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $offersSmall; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $offer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e($offer['link'] ?? '#'); ?>" class="offer-card offer-card--small">
            <img src="<?php echo e($offer['image'] ?? ''); ?>" alt="<?php echo e($offer['title'] ?? ''); ?>">
            <div class="offer-card__overlay">
                <h3 class="offer-card__title"><?php echo e(strtoupper($offer['title'] ?? '')); ?></h3>
                <p class="offer-card__link">
                    VIEW MORE <img src="<?php echo e(asset('themes/victorious/img/icon/next.svg')); ?>" alt="">
                </p>
            </div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</section>
<?php /**PATH C:\xampp\htdocs\core_laravel\Core_system\resources\views/widgets/victorious/special-offers.blade.php ENDPATH**/ ?>