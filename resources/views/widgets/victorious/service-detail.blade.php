<div class="service-details">
    <div class="service-details__content">
        <div class="service-details__left {{ $layout === 'image-right' ? 'order-2' : '' }}">
            @if($image)
            <img src="{{ $image }}" alt="{{ $title }}">
            @endif
        </div>
        <div class="service-details__right {{ $layout === 'image-right' ? 'order-1' : '' }}">
            @if($decorImage)
            <img src="{{ $decorImage }}" alt="" class="service-details__right-decor-img">
            @endif
            <h3 class="service-details__right-title">{{ $title }}</h3>
            <div class="service-details__right-text">{!! $content !!}</div>
            @if($buttonText)
            <a href="{{ $buttonLink }}" class="c-btn c-btn--book-room">
                <div class="btn-inner">
                    <span>{{ $buttonText }}</span>
                    <span class="c-btn__arrow">
                        <img src="{{ asset('themes/victorious/img/icon/next-2.svg') }}" alt="">
                    </span>
                </div>
                <div class="btn-hover">
                    <span>{{ $buttonText }}</span>
                    <span class="c-btn__arrow">
                        <img src="{{ asset('themes/victorious/img/icon/next.svg') }}" alt="">
                    </span>
                </div>
            </a>
            @endif
        </div>
    </div>
</div>
