{{-- Widget Template --}}
{{-- Available variables: $settings (array of field values), $widget (WidgetTemplate model) --}}
{{-- Available helpers: $products($limit), $posts($limit), $categories() --}}

<div class="widget-container p-4">
    {{-- Access field values via $settings --}}
    @if(!empty($settings['title']))
        <h2 class="text-2xl font-bold mb-4">{{ $settings['title'] }}</h2>
    @endif
    
    {{-- Example: Display products --}}
    {{-- 
    @php
        $items = $products(6);
    @endphp
    
    <div class="grid grid-cols-3 gap-4">
        @foreach($items as $item)
            <div class="border rounded p-3">
                <h3>{{ $item->name }}</h3>
                <p>{{ $item->price }}</p>
            </div>
        @endforeach
    </div>
    --}}
    
    {{-- Your custom code here --}}
</div>