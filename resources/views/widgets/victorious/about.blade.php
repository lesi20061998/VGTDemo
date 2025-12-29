<div class="about-services" @if($backgroundImage) style="background-image:url({{ $backgroundImage }})" @endif>
    <section class="about">
        <div class="about__wrapper l-container l-pd-0-135px">
            <h2 class="about__title">{{ $sectionTitle }}</h2>
            <div class="about__content">
                <div class="about__image">
                    @if($decorImage)
                        <img src="{{ $decorImage }}" alt="" class="about__info-decor">
                    @endif
                </div>
                <div class="about__info">
                   
                    <h3 class="about__info-title">{{ $title }}</h3>
                    <div class="about__info-text">{!! $content !!}</div>
                </div>
            </div>
        </div>
    </section>
</div>
