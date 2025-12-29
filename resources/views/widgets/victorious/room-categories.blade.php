<section class="room-categories l-container l-pd-0-135px">
    <h2 class="room-categories__title">{{ $title }}</h2>
    <div class="room-categories__slider">
        <div class="swiper room-categories__swiper">
            <div class="swiper-wrapper">
                @foreach($rooms as $room)
                <article class="swiper-slide">
                    <a href="{{ url('room/' . $room->slug) }}" class="room-card">
                        <div class="room-card__image-wrapper">
                            <img src="{{ $room->featured_image ?? ($room->gallery[0] ?? '') }}" alt="{{ $room->name }}">
                        </div>
                        <div class="room-card__content">
                            <h3 class="room-card__name">{{ strtoupper($room->name) }}</h3>
                            @if($showFeatures && !empty($room->meta['features']))
                            <ul class="room-card__features">
                                @foreach($room->meta['features'] ?? [] as $feature)
                                <li class="room-card__feature-item">
                                    @if(!empty($feature['icon']))
                                    <img src="{{ $feature['icon'] }}" alt="">
                                    @endif
                                    <p class="ug-line-break-2">{{ $feature['text'] ?? '' }}</p>
                                </li>
                                @endforeach
                            </ul>
                            @endif
                            <div class="room-card__actions">
                                <div class="c-btn c-btn--view-more">
                                    <div class="btn-inner">
                                        <span>{{ $viewMoreText }}</span>
                                        <span class="c-btn__arrow">
                                            <img src="{{ asset('themes/victorious/img/icon/next.svg') }}" alt="">
                                        </span>
                                    </div>
                                    <div class="btn-hover">
                                        <span>{{ $viewMoreText }}</span>
                                        <span class="c-btn__arrow">
                                            <img src="{{ asset('themes/victorious/img/icon/next-2.svg') }}" alt="">
                                        </span>
                                    </div>
                                </div>
                                <div class="c-btn c-btn--book-room">
                                    <div class="btn-inner">
                                        <span>{{ $bookText }}</span>
                                        <span class="c-btn__arrow">
                                            <img src="{{ asset('themes/victorious/img/icon/next-2.svg') }}" alt="">
                                        </span>
                                    </div>
                                    <div class="btn-hover">
                                        <span>{{ $bookText }}</span>
                                        <span class="c-btn__arrow">
                                            <img src="{{ asset('themes/victorious/img/icon/next.svg') }}" alt="">
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </article>
                @endforeach
            </div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
    </div>
</section>

@push('scripts')
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
@endpush
