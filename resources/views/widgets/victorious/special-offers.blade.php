<section class="special-offers l-container l-pd-0-135px">
    <div class="special-offers__header">
        <h2 class="special-offers__title">{{ $title }}</h2>
        <a href="{{ $viewAllLink }}" class="special-offers__view-all">
            VIEW MORE <img src="{{ asset('themes/victorious/img/icon/next.svg') }}" alt="">
        </a>
    </div>
    
    @if(count($offersLarge) > 0)
    <div class="special-offers__grid-top">
        @foreach($offersLarge as $offer)
        <a href="{{ $offer['link'] ?? '#' }}" class="offer-card offer-card--large">
            <img src="{{ $offer['image'] ?? '' }}" alt="{{ $offer['title'] ?? '' }}">
            <div class="offer-card__overlay">
                <h3 class="offer-card__title">{{ strtoupper($offer['title'] ?? '') }}</h3>
                <p class="offer-card__link">
                    VIEW MORE <img src="{{ asset('themes/victorious/img/icon/next.svg') }}" alt="">
                </p>
            </div>
        </a>
        @endforeach
    </div>
    @endif
    
    @if(count($offersSmall) > 0)
    <div class="special-offers__grid-bottom">
        @foreach($offersSmall as $offer)
        <a href="{{ $offer['link'] ?? '#' }}" class="offer-card offer-card--small">
            <img src="{{ $offer['image'] ?? '' }}" alt="{{ $offer['title'] ?? '' }}">
            <div class="offer-card__overlay">
                <h3 class="offer-card__title">{{ strtoupper($offer['title'] ?? '') }}</h3>
                <p class="offer-card__link">
                    VIEW MORE <img src="{{ asset('themes/victorious/img/icon/next.svg') }}" alt="">
                </p>
            </div>
        </a>
        @endforeach
    </div>
    @endif
</section>
