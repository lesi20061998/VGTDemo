<section class="hero-home">
    <div class="hero-home__video-wrapper">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($videoUrl): ?>
        <video class="hero-home__video" autoplay loop muted playsinline <?php if($poster): ?> poster="<?php echo e($poster); ?>" <?php endif; ?>>
            <source src="<?php echo e($videoUrl); ?>" type="video/mp4">
            Trình duyệt của bạn không hỗ trợ thẻ video.
        </video>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</section>
<?php /**PATH C:\xampp\htdocs\core_laravel\Core_system\resources\views/widgets/victorious/hero-video.blade.php ENDPATH**/ ?>