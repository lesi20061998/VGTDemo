@extends('cms.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6" x-data="contactButtons()">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold mb-6">Cài đặt Contact Buttons</h2>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Settings Form -->
            <div class="lg:col-span-2">
                <form action="{{ route('cms.settings.save') }}" method="POST">
                    @csrf
                    <input type="hidden" name="page" value="contact-buttons">

                    <!-- Hiển thị Desktop -->
                    <div class="mb-6 p-4 border rounded-lg">
                        <label class="flex items-center mb-4">
                            <input type="checkbox" name="desktop_enabled" value="1" 
                                   x-model="desktopEnabled"
                                   {{ setting('contact_buttons_desktop_enabled', true) ? 'checked' : '' }}
                                   class="rounded border-gray-300">
                            <span class="ml-2 font-semibold text-lg">Hiển thị trên Desktop</span>
                        </label>

                        <div x-show="desktopEnabled" class="space-y-4">
                            <div>
                                <label class="block font-medium mb-2">Vị trí</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <template x-for="pos in positions" :key="pos.value">
                                        <label class="border rounded p-2 cursor-pointer hover:border-blue-500 transition text-sm"
                                               :class="desktopPosition === pos.value ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                                            <input type="radio" name="desktop_position" 
                                                   :value="pos.value" 
                                                   x-model="desktopPosition"
                                                   class="mr-2">
                                            <span x-text="pos.label"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium mb-1">Canh lề dọc (px)</label>
                                    <input type="number" name="desktop_margin_vertical" 
                                           x-model="desktopMarginV"
                                           value="{{ setting('contact_buttons_desktop_margin_v', 20) }}"
                                           class="w-full border rounded px-3 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Canh lề ngang (px)</label>
                                    <input type="number" name="desktop_margin_horizontal" 
                                           x-model="desktopMarginH"
                                           value="{{ setting('contact_buttons_desktop_margin_h', 20) }}"
                                           class="w-full border rounded px-3 py-2 text-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hiển thị Mobile -->
                    <div class="mb-6 p-4 border rounded-lg">
                        <label class="flex items-center mb-4">
                            <input type="checkbox" name="mobile_enabled" value="1" 
                                   x-model="mobileEnabled"
                                   {{ setting('contact_buttons_mobile_enabled', true) ? 'checked' : '' }}
                                   class="rounded border-gray-300">
                            <span class="ml-2 font-semibold text-lg">Hiển thị trên Mobile</span>
                        </label>

                        <div x-show="mobileEnabled" class="space-y-4">
                            <div>
                                <label class="block font-medium mb-2">Vị trí</label>
                                <select name="mobile_position" x-model="mobilePosition" class="w-full border rounded px-3 py-2 text-sm">
                                    <template x-for="pos in positions" :key="pos.value">
                                        <option :value="pos.value" x-text="pos.label"></option>
                                    </template>
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium mb-1">Canh lề dọc (px)</label>
                                    <input type="number" name="mobile_margin_vertical" 
                                           x-model="mobileMarginV"
                                           value="{{ setting('contact_buttons_mobile_margin_v', 20) }}"
                                           class="w-full border rounded px-3 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Canh lề ngang (px)</label>
                                    <input type="number" name="mobile_margin_horizontal" 
                                           x-model="mobileMarginH"
                                           value="{{ setting('contact_buttons_mobile_margin_h', 20) }}"
                                           class="w-full border rounded px-3 py-2 text-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kiểu hiển thị -->
                    <div class="mb-6 p-4 border rounded-lg">
                        <h3 class="font-semibold text-lg mb-3">Kiểu hiển thị</h3>
                        <div class="grid grid-cols-3 gap-3">
                            <template x-for="style in styles" :key="style.value">
                                <label class="border rounded-lg p-3 cursor-pointer hover:border-blue-500 transition"
                                       :class="selectedStyle === style.value ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                                    <input type="radio" name="style" 
                                           :value="style.value" 
                                           x-model="selectedStyle"
                                           class="hidden">
                                    <div class="text-center">
                                        <div class="mb-2 flex justify-center" x-html="style.preview"></div>
                                        <div class="text-sm font-medium" x-text="style.label"></div>
                                    </div>
                                </label>
                            </template>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="mb-6 p-4 border rounded-lg">
                        <h3 class="font-semibold text-lg mb-3">Danh sách Buttons</h3>
                        <div class="space-y-2">
                            <template x-for="(btn, index) in buttons" :key="index">
                                <div class="flex items-center gap-2 p-2 border rounded bg-gray-50">
                                    <input type="checkbox" 
                                           :name="'buttons[' + index + '][enabled]'" 
                                           value="1"
                                           x-model="btn.enabled"
                                           class="rounded">
                                    <div class="w-8 h-8 rounded flex items-center justify-center text-white text-sm"
                                         :style="'background-color: ' + btn.color"
                                         x-text="btn.icon"></div>
                                    <input type="hidden" :name="'buttons[' + index + '][type]'" :value="btn.type">
                                    <span class="font-medium text-sm w-32" x-text="btn.label"></span>
                                    <input type="text" 
                                           :name="'buttons[' + index + '][value]'" 
                                           x-model="btn.value"
                                           :placeholder="btn.placeholder"
                                           class="flex-1 border rounded px-2 py-1 text-sm">
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-3">
                        <button type="submit" class="flex items-center gap-2 px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                            </svg>
                            Lưu cài đặt
                        </button>
                        <button type="button" @click="exportCode()" 
                                class="flex items-center gap-2 px-6 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Export Code
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right: Live Preview -->
            <div class="lg:col-span-1">
                <div class="sticky top-6">
                    <h3 class="font-semibold text-lg mb-3">Xem trước trực tiếp</h3>
                    
                    <!-- Desktop Preview -->
                    <div class="mb-4">
                        <div class="text-sm font-medium mb-2 text-gray-600">Desktop</div>
                        <div class="border rounded-lg bg-gradient-to-br from-purple-100 to-blue-100 relative overflow-hidden" style="height: 300px;">
                            <div class="absolute inset-0 flex items-center justify-center text-gray-400 text-xs">
                                <div class="text-center">
                                    <svg class="w-12 h-12 mx-auto mb-1 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="text-xs">Desktop View</p>
                                </div>
                            </div>
                            <div x-show="desktopEnabled" :class="getPreviewClass('desktop')" :style="getPreviewStyle('desktop')" x-html="renderPreview()"></div>
                        </div>
                    </div>

                    <!-- Mobile Preview -->
                    <div>
                        <div class="text-sm font-medium mb-2 text-gray-600">Mobile</div>
                        <div class="border rounded-lg bg-gradient-to-br from-pink-100 to-orange-100 relative overflow-hidden mx-auto" style="height: 400px; width: 200px;">
                            <div class="absolute inset-0 flex items-center justify-center text-gray-400 text-xs">
                                <div class="text-center">
                                    <svg class="w-8 h-8 mx-auto mb-1 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="text-xs">Mobile View</p>
                                </div>
                            </div>
                            <div x-show="mobileEnabled" :class="getPreviewClass('mobile')" :style="getPreviewStyle('mobile')" x-html="renderPreview('mobile')"></div>
                        </div>
                    </div>

                    <!-- Style Info -->
                    <div class="mt-4 p-3 bg-blue-50 rounded text-xs text-gray-700">
                        <div class="font-medium mb-1">Thông tin:</div>
                        <div>Kiểu: <span class="font-semibold" x-text="selectedStyle"></span></div>
                        <div>Vị trí Desktop: <span class="font-semibold" x-text="desktopPosition"></span></div>
                        <div>Vị trí Mobile: <span class="font-semibold" x-text="mobilePosition"></span></div>
                        <div>Buttons: <span class="font-semibold" x-text="buttons.filter(b => b.enabled && b.value).length"></span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Code Modal -->
    <div x-show="showCode" x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         @click.self="showCode = false">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
            <div class="p-4 border-b flex justify-between items-center">
                <h3 class="text-lg font-bold">HTML/CSS Code</h3>
                <button @click="showCode = false" class="text-gray-500 hover:text-gray-700 text-2xl">×</button>
            </div>
            <div class="p-4 overflow-auto" style="max-height: calc(90vh - 80px);">
                <div class="mb-4">
                    <label class="block font-medium mb-2">HTML</label>
                    <pre class="bg-gray-900 text-green-400 p-4 rounded overflow-x-auto text-sm"><code x-text="generatedHTML"></code></pre>
                </div>
                <div>
                    <label class="block font-medium mb-2">CSS</label>
                    <pre class="bg-gray-900 text-blue-400 p-4 rounded overflow-x-auto text-sm"><code x-text="generatedCSS"></code></pre>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function contactButtons() {
    return {
        desktopEnabled: {{ setting('contact_buttons_desktop_enabled', true) ? 'true' : 'false' }},
        desktopPosition: '{{ setting('contact_buttons_desktop_position', 'bottom-right') }}',
        desktopMarginV: {{ setting('contact_buttons_desktop_margin_v', 20) }},
        desktopMarginH: {{ setting('contact_buttons_desktop_margin_h', 20) }},
        mobileEnabled: {{ setting('contact_buttons_mobile_enabled', true) ? 'true' : 'false' }},
        mobilePosition: '{{ setting('contact_buttons_mobile_position', 'bottom-right') }}',
        mobileMarginV: {{ setting('contact_buttons_mobile_margin_v', 20) }},
        mobileMarginH: {{ setting('contact_buttons_mobile_margin_h', 20) }},
        selectedStyle: '{{ setting('contact_buttons_style', 'circle') }}',
        showCode: false,
        generatedHTML: '',
        generatedCSS: '',
        
        positions: [
            { value: 'top-left', label: 'Trên - Trái' },
            { value: 'top-right', label: 'Trên - Phải' },
            { value: 'bottom-left', label: 'Dưới - Trái' },
            { value: 'bottom-right', label: 'Dưới - Phải' }
        ],

        styles: [
            { value: 'circle', label: 'Circle', preview: '<div class="flex gap-1"><div class="w-6 h-6 rounded-full bg-blue-500"></div><div class="w-6 h-6 rounded-full bg-green-500"></div></div>' },
            { value: 'square', label: 'Square', preview: '<div class="flex gap-1"><div class="w-6 h-6 bg-blue-500"></div><div class="w-6 h-6 bg-green-500"></div></div>' },
            { value: 'rounded', label: 'Rounded', preview: '<div class="flex gap-1"><div class="w-6 h-6 rounded-lg bg-blue-500"></div><div class="w-6 h-6 rounded-lg bg-green-500"></div></div>' },
            { value: 'pill', label: 'Pill', preview: '<div class="flex gap-1"><div class="w-10 h-6 rounded-full bg-blue-500"></div><div class="w-10 h-6 rounded-full bg-green-500"></div></div>' },
            { value: 'minimal', label: 'Minimal', preview: '<div class="flex gap-1"><div class="w-6 h-6 rounded-full border-2 border-blue-500"></div><div class="w-6 h-6 rounded-full border-2 border-green-500"></div></div>' },
            { value: 'shadow', label: 'Shadow', preview: '<div class="flex gap-1"><div class="w-6 h-6 rounded-lg bg-blue-500 shadow-lg"></div><div class="w-6 h-6 rounded-lg bg-green-500 shadow-lg"></div></div>' }
        ],

        buttons: {!! json_encode(setting('contact_buttons', [
            ['type' => 'messenger', 'label' => 'Facebook', 'value' => '', 'enabled' => true, 'icon' => 'FB', 'color' => '#0084ff', 'placeholder' => 'facebook.page.id'],
            ['type' => 'zalo', 'label' => 'Zalo', 'value' => '', 'enabled' => true, 'icon' => 'Z', 'color' => '#0068ff', 'placeholder' => '0123456789'],
            ['type' => 'phone', 'label' => 'Điện thoại', 'value' => '', 'enabled' => true, 'icon' => '☎', 'color' => '#10b981', 'placeholder' => '0123456789'],
            ['type' => 'sms', 'label' => 'SMS', 'value' => '', 'enabled' => false, 'icon' => 'SMS', 'color' => '#f59e0b', 'placeholder' => '0123456789']
        ])) !!},

        getPreviewClass(device) {
            const pos = device === 'mobile' ? this.mobilePosition : this.desktopPosition;
            const base = 'absolute flex gap-1';
            if (pos === 'top-left') return base + ' flex-col';
            if (pos === 'top-right') return base + ' flex-col';
            if (pos === 'bottom-left') return base + ' flex-col';
            return base + ' flex-col';
        },

        getPreviewStyle(device) {
            const pos = device === 'mobile' ? this.mobilePosition : this.desktopPosition;
            const marginV = device === 'mobile' ? Math.min(this.mobileMarginV, 50) : Math.min(this.desktopMarginV, 50);
            const marginH = device === 'mobile' ? Math.min(this.mobileMarginH, 30) : Math.min(this.desktopMarginH, 30);
            
            if (pos === 'top-left') return `top: ${marginV}px; left: ${marginH}px;`;
            if (pos === 'top-right') return `top: ${marginV}px; right: ${marginH}px;`;
            if (pos === 'bottom-left') return `bottom: ${marginV}px; left: ${marginH}px;`;
            return `bottom: ${marginV}px; right: ${marginH}px;`;
        },

        renderPreview(device) {
            const enabled = this.buttons.filter(b => b.enabled && b.value);
            if (!enabled.length) return '';

            const size = device === 'mobile' ? 'w-8 h-8' : 'w-10 h-10';
            const shapes = {
                circle: 'rounded-full',
                square: '',
                rounded: 'rounded-lg',
                pill: 'rounded-full px-3',
                minimal: 'rounded-full border-2 bg-transparent',
                shadow: 'rounded-lg shadow-xl'
            };

            return enabled.map(btn => {
                const isMinimal = this.selectedStyle === 'minimal';
                const bgStyle = isMinimal ? `border-color: ${btn.color}; color: ${btn.color}` : `background-color: ${btn.color}; color: white`;
                
                return `
                    <div class="${size} ${shapes[this.selectedStyle]} flex items-center justify-center cursor-pointer hover:scale-110 transition text-sm"
                         style="${bgStyle}"
                         title="${btn.label}">
                        ${btn.icon}
                    </div>
                `;
            }).join('');
        },

        exportCode() {
            const enabled = this.buttons.filter(b => b.enabled && b.value);
            
            const htmlButtons = enabled.map(btn => {
                let href = '';
                if (btn.type === 'phone') href = `tel:${btn.value}`;
                if (btn.type === 'sms') href = `sms:${btn.value}`;
                if (btn.type === 'zalo') href = `https://zalo.me/${btn.value}`;
                if (btn.type === 'messenger') href = `https://m.me/${btn.value}`;
                
                return `  <a href="${href}" class="contact-btn" title="${btn.label}">${btn.icon}</a>`;
            }).join('\n');

            this.generatedHTML = `<div class="contact-buttons">\n${htmlButtons}\n</div>`;

            const posCSS = {
                'top-left': `top: ${this.desktopMarginV}px; left: ${this.desktopMarginH}px;`,
                'top-right': `top: ${this.desktopMarginV}px; right: ${this.desktopMarginH}px;`,
                'bottom-left': `bottom: ${this.desktopMarginV}px; left: ${this.desktopMarginH}px;`,
                'bottom-right': `bottom: ${this.desktopMarginV}px; right: ${this.desktopMarginH}px;`
            };

            const shapeCSS = {
                circle: 'border-radius: 50%;',
                square: 'border-radius: 0;',
                rounded: 'border-radius: 12px;',
                pill: 'border-radius: 25px; padding: 0 20px;',
                minimal: 'border-radius: 50%; border: 2px solid; background: transparent;',
                shadow: 'border-radius: 10px; box-shadow: 0 8px 20px rgba(0,0,0,0.2);'
            };

            this.generatedCSS = `.contact-buttons {
  position: fixed;
  ${posCSS[this.desktopPosition]}
  display: flex;
  flex-direction: column;
  gap: 10px;
  z-index: 9999;
}

.contact-btn {
  width: 50px;
  height: 50px;
  ${shapeCSS[this.selectedStyle]}
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  transition: transform 0.2s;
  font-size: 24px;
  text-decoration: none;
}

.contact-btn:hover {
  transform: scale(1.1);
}

@media (max-width: 768px) {
  .contact-buttons {
    ${posCSS[this.mobilePosition].replace(this.desktopMarginV, this.mobileMarginV).replace(this.desktopMarginH, this.mobileMarginH)}
  }
}`;

            this.showCode = true;
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>
@endsection
