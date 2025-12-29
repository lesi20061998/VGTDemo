<section class="room-categories l-container l-pd-0-135px">
    <h2 class="room-categories__title"><?php echo e($title); ?></h2>
    <div class="room-categories__slider">
        <div class="swiper room-categories__swiper">
            <div class="swiper-wrapper">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <article class="swiper-slide">
                    <a href="<?php echo e(url('room/' . $room->slug)); ?>" class="room-card">
                        <div class="room-card__image-wrapper">
                            <img src="<?php echo e($room->featured_image ?? ($room->gallery[0] ?? '')); ?>" alt="<?php echo e($room->name); ?>">
                        </div>
                        <div class="room-card__content">
                            <h3 class="room-card__name"><?php echo e(strtoupper($room->name)); ?></h3>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showFeatures && !empty($room->meta['features'])): ?>
                            <ul class="room-card__features">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $room->meta['features'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="room-card__feature-item">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($feature['icon'])): ?>
                                    <img src="<?php echo e($feature['icon']); ?>" alt="">
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <p class="ug-line-break-2"><?php echo e($feature['text'] ?? ''); ?></p>
                                </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </ul>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <div class="room-card__actions">
                                <div class="c-btn c-btn--view-more">
                                    <div class="btn-inner">
                                        <span><?php echo e($viewMoreText); ?></span>
                                        <span class="c-btn__arrow">
                                            <img src="<?php echo e(asset('themes/victorious/img/icon/next.svg')); ?>" alt="">
                                        </span>
                                    </div>
                                    <div class="btn-hover">
                                        <span><?php echo e($viewMoreText); ?></span>
                                        <span class="c-btn__arrow">
                                            <img src="<?php echo e(asset('themes/victorious/img/icon/next-2.svg')); ?>" alt="">
                                        </span>
                                    </div>
                                </div>
                                <div class="c-btn c-btn--book-room">
                                    <div class="btn-inner">
                                        <span><?php echo e($bookText); ?></span>
                                        <span class="c-btn__arrow">
                                            <img src="<?php echo e(asset('themes/victorious/img/icon/next-2.svg')); ?>" alt="">
                                        </span>
                                    </div>
                                    <div class="btn-hover">
                                        <span><?php echo e($bookText); ?></span>
                                        <span class="c-btn__arrow">
                                            <img src="<?php echo e(asset('themes/victorious/img/icon/next.svg')); ?>" alt="">
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
    </div>
</section>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    new Swiper('.room-categories__swiper', {
        slidesPerView: 1,
        spaceBetween: 20,
        navigation: {
            nextEl: '.room-categories__swiper .swiper-button-next',
            prevEl: '.room-categories__swiper .swiper-button-prev',
        },
        breakpoints: {
            768: { slidesPerView: 2 },
            1024: { slidesPerView: 3 },
            1280: { slidesPerView: 4 }
        }
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\xampp\htdocs\core_laravel\Core_system\resources\views/widgets/victorious/room-categories.blade.php ENDPATH**/ ?>