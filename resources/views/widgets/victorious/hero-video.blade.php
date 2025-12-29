<section class="hero-home">
    <div class="hero-home__video-wrapper">
        @if($videoUrl)
        <video class="hero-home__video" autoplay loop muted playsinline @if($poster) poster="{{ $poster }}" @endif>
            <source src="{{ $videoUrl }}" type="video/mp4">
            Trình duyệt của bạn không hỗ trợ thẻ video.
        </video>
        @endif
    </div>
</section>
