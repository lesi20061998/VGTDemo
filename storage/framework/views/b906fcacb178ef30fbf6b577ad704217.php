<section class="events l-container l-pd-0-135px">
    <div class="events__header">
        <h2 class="events__title"><?php echo e($title); ?></h2>
    </div>
    <div class="events__list" style="display: grid; grid-template-columns: repeat(<?php echo e($columns); ?>, 1fr); gap: 30px;">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(url('post/' . $post->slug)); ?>" class="event-card">
            <div class="event-card__image-wrapper">
                <img src="<?php echo e($post->thumbnail ?? ''); ?>" alt="<?php echo e($post->title); ?>">
            </div>
            <div class="event-card__content">
                <h3 class="event-card__title ug-line-break-2"><?php echo e(strtoupper($post->title)); ?></h3>
                <p class="event-card__desc ug-line-break-3"><?php echo e(Str::limit(strip_tags($post->excerpt ?? $post->content), 120)); ?></p>
                <p class="event-card__link">
                    VIEW MORE <img src="<?php echo e(asset('themes/victorious/img/icon/next-2.svg')); ?>" alt="">
                </p>
            </div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</section>
<?php /**PATH C:\xampp\htdocs\core_laravel\Core_system\resources\views/widgets/victorious/events.blade.php ENDPATH**/ ?>