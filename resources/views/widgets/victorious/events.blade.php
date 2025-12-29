<section class="events l-container l-pd-0-135px">
    <div class="events__header">
        <h2 class="events__title">{{ $title }}</h2>
    </div>
    <div class="events__list" style="display: grid; grid-template-columns: repeat({{ $columns }}, 1fr); gap: 30px;">
        @foreach($posts as $post)
        <a href="{{ url('post/' . $post->slug) }}" class="event-card">
            <div class="event-card__image-wrapper">
                <img src="{{ $post->thumbnail ?? '' }}" alt="{{ $post->title }}">
            </div>
            <div class="event-card__content">
                <h3 class="event-card__title ug-line-break-2">{{ strtoupper($post->title) }}</h3>
                <p class="event-card__desc ug-line-break-3">{{ Str::limit(strip_tags($post->excerpt ?? $post->content), 120) }}</p>
                <p class="event-card__link">
                    VIEW MORE <img src="{{ asset('themes/victorious/img/icon/next-2.svg') }}" alt="">
                </p>
            </div>
        </a>
        @endforeach
    </div>
</section>
