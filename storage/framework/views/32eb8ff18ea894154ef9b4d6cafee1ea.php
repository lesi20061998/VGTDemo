<?php
    $logoFooter = setting('site_logo_footer', asset('themes/victorious/img/common/logo-footer.svg'));
    $hqAddress = setting('hq_address', '5th Floor, VRP Bank Building, 23 Hang Voi street - Hoan Kiem ward - Ha Noi');
    $hqPhone = setting('hq_phone', '(+84) 24 3939 3555');
    $hqHotline = setting('hq_hotline', '(+84) 983 086 355');
    $hqEmail = setting('hq_email', 'info@victoriouscruise.com');
    $hlAddress = setting('hl_address', 'No. 26 Tuan Chau Marina, Halong - Quang Ninh');
    $hlPhone = setting('hl_phone', '(+84) 983 730 882');
    $hlHotline = setting('hl_hotline', '(+84) 376 169 787');
?>

<footer class="footer">
    <div class="footer__inner l-container l-pd-0-135px">
        <div class="footer-top">
            <a href="#" class="footer-top__link">About Us</a>
            <a href="#" class="footer-top__link">Brand Policy</a>
            <a href="#" class="footer-top__link">Privacy Policy</a>
            <a href="#" class="footer-top__link">Our Itineraries</a>
            <a href="#" class="footer-top__link">Our Amenities</a>
            <a href="#" class="footer-top__link">Our Services</a>
            <a href="#" class="footer-top__link">News & Event</a>
            <a href="#" class="footer-top__link">Contact Us</a>
        </div>
        <div class="footer-mid">
            <div class="footer__brand-info">
                <div class="footer__logo">
                    <img src="<?php echo e($logoFooter); ?>" alt="Victorious Cruise Logo">
                </div>
            </div>
            <div class="footer__contact-grid">
                <div class="footer__contact-col">
                    <h3 class="footer__contact-title">HEADQUARTER</h3>
                    <p class="footer__contact-detail">Address: <?php echo e($hqAddress); ?></p>
                    <p class="footer__contact-detail">Phone: <?php echo e($hqPhone); ?></p>
                    <p class="footer__contact-detail">Hotline: <?php echo e($hqHotline); ?></p>
                    <p class="footer__contact-detail">Email: <?php echo e($hqEmail); ?></p>
                </div>
                <div class="footer__contact-col">
                    <h3 class="footer__contact-title">HALONG OFFICE</h3>
                    <p class="footer__contact-detail">Address: <?php echo e($hlAddress); ?></p>
                    <p class="footer__contact-detail">Phone: <?php echo e($hlPhone); ?></p>
                    <p class="footer__contact-detail">Hotline: <?php echo e($hlHotline); ?></p>
                </div>
            </div>
        </div>
        <div class="footer__social-media">
            <h3 class="footer__social-title">SOCIAL MEDIA</h3>
            <div class="footer__social-links">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fb = setting('social_facebook')): ?>
                <a href="<?php echo e($fb); ?>" class="footer__social-icon" aria-label="Facebook">
                    <img src="<?php echo e(asset('themes/victorious/img/icon/fb-ic.svg')); ?>" alt="">
                </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ig = setting('social_instagram')): ?>
                <a href="<?php echo e($ig); ?>" class="footer__social-icon" aria-label="Instagram">
                    <img src="<?php echo e(asset('themes/victorious/img/icon/ins-ic.svg')); ?>" alt="">
                </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tg = setting('social_telegram')): ?>
                <a href="<?php echo e($tg); ?>" class="footer__social-icon" aria-label="Telegram">
                    <img src="<?php echo e(asset('themes/victorious/img/icon/tele-ic.svg')); ?>" alt="">
                </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($yt = setting('social_youtube')): ?>
                <a href="<?php echo e($yt); ?>" class="footer__social-icon" aria-label="Youtube">
                    <img src="<?php echo e(asset('themes/victorious/img/icon/ytb-ic.svg')); ?>" alt="">
                </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tw = setting('social_twitter')): ?>
                <a href="<?php echo e($tw); ?>" class="footer__social-icon" aria-label="X (Twitter)">
                    <img src="<?php echo e(asset('themes/victorious/img/icon/x-ic.svg')); ?>" alt="">
                </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
    <div class="footer__copyright l-container l-pd-0-135px">
        <p>COPYRIGHT © <?php echo e(date('Y')); ?>. VICTORIOUS. ALL RIGHTS RESERVED.</p>
    </div>
</footer>
<?php /**PATH C:\xampp\htdocs\core_laravel\Core_system\resources\views/frontend/themes/victorious/partials/footer.blade.php ENDPATH**/ ?>