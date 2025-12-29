<section class="our-itineraries l-container l-pd-0-135px">
    <div class="our-itineraries__header">
        <h2 class="our-itineraries__title">{{ $title }}</h2>
    </div>
    <div class="our-itineraries__grid">
        @foreach($itineraries as $item)
        <a href="{{ $item['link'] ?? '#' }}" class="itinerary-card">
            <div class="itinerary-card__image-wrapper">
                @if(!empty($item['image']))
                <img src="{{ $item['image'] }}" alt="{{ $item['duration'] ?? '' }}">
                @endif
            </div>
            <div class="itinerary-card__info">
                <p class="itinerary-card__duration">{{ $item['duration'] ?? '' }}</p>
                <div class="c-btn c-btn--view-more">
                    <div class="btn-inner">
                        <span>{{ $buttonText }}</span>
                        <span class="c-btn__arrow">
                            <img src="{{ asset('themes/victorious/img/icon/next.svg') }}" alt="">
                        </span>
                    </div>
                    <div class="btn-hover">
                        <span>{{ $buttonText }}</span>
                        <span class="c-btn__arrow">
                            <img src="{{ asset('themes/victorious/img/icon/next-2.svg') }}" alt="">
                        </span>
                    </div>
                </div>
            </div>
        </a>
        @endforeach
    </div>
</section>
