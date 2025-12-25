{{-- Default view for TestWidget Widget --}}
<section class="test_widget-widget py-8">
    <div class="container mx-auto px-4">
        <h2 class="text-2xl font-bold mb-4">{{ $title ?? 'Default Title' }}</h2>
        <p class="text-gray-600">{{ $description ?? 'Default description' }}</p>
        
        {{-- Add your custom HTML here --}}
        <div class="mt-6">
            <p class="text-sm text-gray-500">This is the default view for TestWidget widget.</p>
        </div>
    </div>
</section>
