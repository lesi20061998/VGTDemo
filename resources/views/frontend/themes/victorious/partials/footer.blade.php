@php
    $logoFooter = setting('site_logo_footer', asset('themes/victorious/img/common/logo-footer.svg'));
    $hqAddress = setting('hq_address', '5th Floor, VRP Bank Building, 23 Hang Voi street - Hoan Kiem ward - Ha Noi');
    $hqPhone = setting('hq_phone', '(+84) 24 3939 3555');
    $hqHotline = setting('hq_hotline', '(+84) 983 086 355');
    $hqEmail = setting('hq_email', 'info@victoriouscruise.com');
    $hlAddress = setting('hl_address', 'No. 26 Tuan Chau Marina, Halong - Quang Ninh');
    $hlPhone = setting('hl_phone', '(+84) 983 730 882');
    $hlHotline = setting('hl_hotline', '(+84) 376 169 787');
@endphp

<footer class="footer">
    <div class="footer__inner l-container l-pd-0-135px" style="padding-top: 40px; padding-bottom: 40px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
            {!! render_widgets('footer') !!}
        </div>
    </div>
    <div class="footer__copyright l-container l-pd-0-135px">
        <p>COPYRIGHT © {{ date('Y') }}. VICTORIOUS. ALL RIGHTS RESERVED.</p>
    </div>
</footer>
