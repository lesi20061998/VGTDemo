<section class="service">
    <div class="category-service">
        <h2 class="category-service__title">{{ $title }}</h2>
        <div class="category-service__content">
            @foreach($services as $service)
            <div class="category-service__item">
                @if(!empty($service['icon']))
                <img src="{{ $service['icon'] }}" alt="" class="category-service__item-icon">
                @endif
                <div class="category-service__item-title">{{ $service['name'] ?? '' }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>
