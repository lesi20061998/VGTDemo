<div class="about-services" <?php if($backgroundImage): ?> style="background-image:url(<?php echo e($backgroundImage); ?>)" <?php endif; ?>>
    <section class="about">
        <div class="about__wrapper l-container l-pd-0-135px">
            <h2 class="about__title"><?php echo e($sectionTitle); ?></h2>
            <div class="about__content">
                <div class="about__image">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($decorImage): ?>
                        <img src="<?php echo e($decorImage); ?>" alt="" class="about__info-decor">
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="about__info">
                   
                    <h3 class="about__info-title"><?php echo e($title); ?></h3>
                    <div class="about__info-text"><?php echo $content; ?></div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php /**PATH C:\xampp\htdocs\core_laravel\Core_system\resources\views/widgets/victorious/about.blade.php ENDPATH**/ ?>