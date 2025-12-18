@php
    $footerBg = setting_string('footer_background_color', '#1a1a1a');
    $footerText = setting_string('footer_text_color', '#ffffff');
    $footerLayout = setting_string('footer_layout', '3-columns');
@endphp

<footer style="background-color: {{ $footerBg }}; color: {{ $footerText }};" class="py-12">
    <div class="container mx-auto px-4">
        @if($footerLayout === '3-columns')
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="font-bold text-lg mb-4">{{ setting_string('footer_col1_title') }}</h3>
                    <div>{!! setting_string('footer_col1_content') !!}</div>
                </div>
                <div>
                    <h3 class="font-bold text-lg mb-4">{{ setting_string('footer_col2_title') }}</h3>
                    <div>{!! setting_string('footer_col2_content') !!}</div>
                </div>
                <div>
                    <h3 class="font-bold text-lg mb-4">{{ setting_string('footer_col3_title') }}</h3>
                    <div>{!! setting_string('footer_col3_content') !!}</div>
                </div>
            </div>
        @elseif($footerLayout === '4-columns')
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="font-bold text-lg mb-4">{{ setting_string('footer_col1_title') }}</h3>
                    <div>{!! setting_string('footer_col1_content') !!}</div>
                </div>
                <div>
                    <h3 class="font-bold text-lg mb-4">{{ setting_string('footer_col2_title') }}</h3>
                    <div>{!! setting_string('footer_col2_content') !!}</div>
                </div>
                <div>
                    <h3 class="font-bold text-lg mb-4">{{ setting_string('footer_col3_title') }}</h3>
                    <div>{!! setting_string('footer_col3_content') !!}</div>
                </div>
                <div>
                    <h3 class="font-bold text-lg mb-4">{{ setting_string('footer_col4_title') }}</h3>
                    <div>{!! setting_string('footer_col4_content') !!}</div>
                </div>
            </div>
        @endif
        
        <!-- Footer Widgets -->
        @if(function_exists('render_widgets'))
            <div class="mt-8">
                {!! render_widgets('footer') !!}
            </div>
        @endif
        
        <div class="border-t mt-8 pt-8 text-center" style="border-color: {{ $footerText }}33;">
            <p>{{ setting_string('footer_copyright', '© 2024 All rights reserved') }}</p>
        </div>
    </div>
</footer>
