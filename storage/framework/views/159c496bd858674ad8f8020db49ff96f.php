<?php
    $phone = setting('site_phone', '0909 999 999');
    $email = setting('site_email', 'info@victorious.com');
    $logo = setting('site_logo', asset('themes/victorious/img/common/logo-header.svg'));
    $menuItems = [];
?>

<header class="c-header js-header">
    <div class="c-header__container">
        <div class="c-header__top">
            <div class="details l-container">
                <div class="c-header__mail">
                    <a href="tel:<?php echo e(preg_replace('/\s+/', '', $phone)); ?>">
                        <img src="<?php echo e(asset('themes/victorious/img/icon/phone-ic.svg')); ?>" alt="" class="c-header__mailic"> <?php echo e($phone); ?>

                    </a>
                    <a href="mailto:<?php echo e($email); ?>">
                        <img src="<?php echo e(asset('themes/victorious/img/icon/email-ic.svg')); ?>" alt="" class="c-header__mailic"> <?php echo e($email); ?>

                    </a>
                </div>
                <div class="c-header__social">
                    <p class="c-header__social-title">Contact us</p>
                    <ul class="c-header__social--list">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fb = setting('social_facebook')): ?>
                        <li class="c-header__social--item">
                            <a href="<?php echo e($fb); ?>"><img src="<?php echo e(asset('themes/victorious/img/icon/fb-ic.svg')); ?>" alt="Facebook"></a>
                        </li>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ig = setting('social_instagram')): ?>
                        <li class="c-header__social--item">
                            <a href="<?php echo e($ig); ?>"><img src="<?php echo e(asset('themes/victorious/img/icon/ins-ic.svg')); ?>" alt="Instagram"></a>
                        </li>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($yt = setting('social_youtube')): ?>
                        <li class="c-header__social--item">
                            <a href="<?php echo e($yt); ?>"><img src="<?php echo e(asset('themes/victorious/img/icon/ytb-ic.svg')); ?>" alt="Youtube"></a>
                        </li>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="c-header__main">
            <div class="details l-container">
                <ul class="c-header__main--left">
                    <li><a href="/">Home page</a></li>
                    <li class="flex-ic room-categories__cabin-dropmenu">
                        <a href="#">about victorious</a><img src="<?php echo e(asset('themes/victorious/img/icon/down-ic.svg')); ?>" alt="">
                        <ul class="cabin-menu">
                            <li class="cabin-item"><a href="#">About us</a></li>
                            <li class="cabin-item"><a href="#">News & Events</a></li>
                        </ul>
                    </li>
                    <li class="flex-ic room-categories__cabin-dropmenu">
                        <a href="/rooms">room categories</a><img src="<?php echo e(asset('themes/victorious/img/icon/down-ic.svg')); ?>" alt="">
                        <ul class="cabin-menu">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = \App\Models\Product::where('status', 'published')->limit(8)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="cabin-item"><a href="<?php echo e(url('room/' . $room->slug)); ?>"><?php echo e($room->name); ?></a></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </ul>
                    </li>
                </ul>
                <a href="/" class="c-header__logo">
                    <img src="<?php echo e($logo); ?>" alt="VICTORIOUS">
                </a>
                <ul class="c-header__main--right">
                    <li class="flex-ic room-categories__cabin-dropmenu">
                        <a href="/itineraries">itineraries</a><img src="<?php echo e(asset('themes/victorious/img/icon/down-ic.svg')); ?>" alt="">
                        <ul class="cabin-menu">
                            <li class="cabin-item"><a href="#">2 days 1 night</a></li>
                            <li class="cabin-item"><a href="#">3 days 2 night</a></li>
                        </ul>
                    </li>
                    <li><a href="/offers">special offers</a></li>
                    <li><a href="/contact">contact</a></li>
                </ul>
                <div class="c-header__hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
        
        
        <div class="main-header__mobile-menu" id="mobileMenu">
            <div class="main-header__mobile-menu-overlay"></div>
            <div class="main-header__mobile-menu-content">
                <button class="main-header__mobile-close" id="mobileMenuClose">
                    <img src="<?php echo e(asset('themes/victorious/img/icon/close-ic.svg')); ?>" alt="">
                </button>
                <ul class="main-header__mobile-nav">
                    <li class="mobile-nav__item">
                        <div class="mobile-nav__link-wrap">
                            <a href="/" class="mobile-nav__link">Home page</a>
                        </div>
                    </li>
                    <li class="mobile-nav__item mobile-nav__item--has-sub">
                        <div class="mobile-nav__link-wrap">
                            <a href="#" class="mobile-nav__link">About victorious</a>
                            <button class="mobile-nav__toggle-btn">
                                <img src="<?php echo e(asset('themes/victorious/img/icon/arrow-right-ic.svg')); ?>" alt="">
                            </button>
                        </div>
                        <ul class="mobile-nav__sub-list">
                            <li class="mobile-nav__sub-item"><a href="#">About us</a></li>
                            <li class="mobile-nav__sub-item"><a href="#">News & Events</a></li>
                        </ul>
                    </li>
                    <li class="mobile-nav__item mobile-nav__item--has-sub">
                        <div class="mobile-nav__link-wrap">
                            <a href="#" class="mobile-nav__link">Room categories</a>
                            <button class="mobile-nav__toggle-btn">
                                <img src="<?php echo e(asset('themes/victorious/img/icon/arrow-right-ic.svg')); ?>" alt="">
                            </button>
                        </div>
                        <ul class="mobile-nav__sub-list">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = \App\Models\Product::where('status', 'published')->limit(8)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="mobile-nav__sub-item"><a href="<?php echo e(url('room/' . $room->slug)); ?>"><?php echo e($room->name); ?></a></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </ul>
                    </li>
                    <li class="mobile-nav__item">
                        <div class="mobile-nav__link-wrap">
                            <a href="/offers" class="mobile-nav__link">Special offers</a>
                        </div>
                    </li>
                    <li class="mobile-nav__item">
                        <div class="mobile-nav__link-wrap">
                            <a href="/contact" class="mobile-nav__link">Contact</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
<?php /**PATH C:\xampp\htdocs\core_laravel\Core_system\resources\views/frontend/themes/victorious/partials/header.blade.php ENDPATH**/ ?>