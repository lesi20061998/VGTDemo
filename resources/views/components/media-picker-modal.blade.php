@props([
    'id' => 'media-picker-modal',
    'multiple' => false,
])

@php
    $currentProject = session('current_project');
    $projectCode = is_array($currentProject) ? ($currentProject['code'] ?? null) : ($currentProject->code ?? null);
    
    // Build media URLs based on context
    if ($projectCode) {
        $mediaListUrl = url("/{$projectCode}/admin/media/list");
        $mediaUploadUrl = url("/{$projectCode}/admin/media/upload");
    } else {
        $mediaListUrl = url('/admin/media/list');
        $mediaUploadUrl = url('/admin/media/upload');
    }
@endphp

<div x-data="mediaPickerModal('{{ $id }}', {{ $multiple ? 'true' : 'false' }}, '{{ $mediaListUrl }}', '{{ $mediaUploadUrl }}')"
     x-show="isOpen"
     x-cloak
     @open-media-picker.window="openPicker($event.detail)"
     @keydown.escape.window="closePicker()"
     class="fixed inset-0 z-[9999] overflow-y-auto"
     style="display: none;">
    
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-black/60 transition-opacity" @click="closePicker()"></div>
        
        {{-- Modal --}}
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-5xl max-h-[85vh] overflow-hidden z-10"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             @click.stop>
            
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50">
                <div class="flex items-center gap-4">
                    <h3 class="text-lg font-semibold text-gray-900">Thư viện Media</h3>
                    <div class="flex rounded-lg border border-gray-300 overflow-hidden">
                        <button @click="viewMode = 'grid'" 
                                :class="viewMode === 'grid' ? 'bg-blue-500 text-white' : 'bg-white text-gray-600 hover:bg-gray-100'"
                                class="px-3 py-1.5 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                        </button>
                        <button @click="viewMode = 'list'" 
                                :class="viewMode === 'list' ? 'bg-blue-500 text-white' : 'bg-white text-gray-600 hover:bg-gray-100'"
                                class="px-3 py-1.5 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <button @click="closePicker()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            {{-- Toolbar --}}
            <div class="flex items-center justify-between px-6 py-3 border-b bg-white">
                <div class="flex items-center gap-3">
                    {{-- Breadcrumb --}}
                    <nav class="flex items-center gap-1 text-sm">
                        <button @click="navigateTo('')" class="text-blue-600 hover:underline">Media</button>
                        <template x-for="(folder, index) in currentPath.split('/').filter(f => f)" :key="index">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                <button @click="navigateTo(currentPath.split('/').slice(0, index + 1).join('/'))" 
                                        class="text-blue-600 hover:underline" x-text="folder"></button>
                            </span>
                        </template>
                    </nav>
                </div>
                
                <div class="flex items-center gap-2">
                    {{-- Search --}}
                    <div class="relative">
                        <input type="text" x-model="searchQuery" @input.debounce.300ms="filterFiles()"
                               placeholder="Tìm kiếm..."
                               class="w-48 pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    
                    {{-- Upload Button --}}
                    <label class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 cursor-pointer transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Upload
                        <input type="file" @change="uploadFiles($event)" multiple accept="image/*,video/*" class="hidden">
                    </label>
                </div>
            </div>
            
            {{-- Content --}}
            <div class="p-6 overflow-y-auto" style="max-height: calc(85vh - 200px);">
                {{-- Loading --}}
                <div x-show="loading" class="flex items-center justify-center py-12">
                    <svg class="w-8 h-8 animate-spin text-blue-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                
                {{-- Folders --}}
                <div x-show="!loading && folders.length > 0" class="mb-6">
                    <h4 class="text-sm font-medium text-gray-500 mb-3">Thư mục</h4>
                    <div class="grid grid-cols-6 gap-3">
                        <template x-for="folder in folders" :key="folder.path">
                            <button @click="navigateTo(folder.path)" 
                                    class="flex flex-col items-center p-3 rounded-lg border border-gray-200 hover:border-blue-400 hover:bg-blue-50 transition group">
                                <svg class="w-10 h-10 text-yellow-500 group-hover:text-yellow-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/>
                                </svg>
                                <span class="text-xs text-gray-600 mt-1 truncate w-full text-center" x-text="folder.name"></span>
                            </button>
                        </template>
                    </div>
                </div>
                
                {{-- Files Grid View --}}
                <div x-show="!loading && viewMode === 'grid'">
                    <h4 x-show="folders.length > 0" class="text-sm font-medium text-gray-500 mb-3">Files</h4>
                    <div class="grid grid-cols-5 gap-4">
                        <template x-for="file in filteredFiles" :key="file.id">
                            <div @click="toggleSelect(file)"
                                 :class="isSelected(file) ? 'ring-2 ring-blue-500 border-blue-500' : 'border-gray-200 hover:border-gray-300'"
                                 class="relative group cursor-pointer rounded-lg border overflow-hidden bg-gray-50 transition">
                                <div class="aspect-square">
                                    <img :src="file.url" :alt="file.name" class="w-full h-full object-cover">
                                </div>
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition"></div>
                                <div x-show="isSelected(file)" class="absolute top-2 right-2 w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div class="absolute bottom-0 left-0 right-0 p-2 bg-gradient-to-t from-black/60 to-transparent">
                                    <p class="text-xs text-white truncate" x-text="file.name"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                
                {{-- Files List View --}}
                <div x-show="!loading && viewMode === 'list'">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Preview</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tên file</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Chọn</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <template x-for="file in filteredFiles" :key="file.id">
                                <tr @click="toggleSelect(file)" 
                                    :class="isSelected(file) ? 'bg-blue-50' : 'hover:bg-gray-50'"
                                    class="cursor-pointer transition">
                                    <td class="px-4 py-2">
                                        <img :src="file.url" class="w-12 h-12 object-cover rounded">
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-700" x-text="file.name"></td>
                                    <td class="px-4 py-2 text-right">
                                        <div x-show="isSelected(file)" class="inline-flex w-6 h-6 bg-blue-500 rounded-full items-center justify-center">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                
                {{-- Empty State --}}
                <div x-show="!loading && filteredFiles.length === 0 && folders.length === 0" class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-gray-500">Chưa có file nào</p>
                    <p class="text-sm text-gray-400 mt-1">Upload file để bắt đầu</p>
                </div>
            </div>
            
            {{-- Footer --}}
            <div class="flex items-center justify-between px-6 py-4 border-t bg-gray-50">
                <div class="text-sm text-gray-500">
                    <span x-text="selectedFiles.length"></span> file đã chọn
                </div>
                <div class="flex items-center gap-3">
                    <button @click="closePicker()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                        Hủy
                    </button>
                    <button @click="confirmSelection()" 
                            :disabled="selectedFiles.length === 0"
                            :class="selectedFiles.length === 0 ? 'bg-gray-300 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'"
                            class="px-6 py-2 text-white rounded-lg transition">
                        Chọn
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function mediaPickerModal(id, multiple, listUrl, uploadUrl) {
    return {
        id: id,
        isOpen: false,
        multiple: multiple,
        listUrl: listUrl,
        uploadUrl: uploadUrl,
        loading: false,
        viewMode: 'grid',
        currentPath: '',
        searchQuery: '',
        folders: [],
        files: [],
        filteredFiles: [],
        selectedFiles: [],
        targetField: null,
        targetComponent: null,
        
        openPicker(detail) {
            this.targetField = detail.field || null;
            this.targetComponent = detail.component || null;
            this.multiple = detail.multiple || this.multiple;
            this.selectedFiles = [];
            this.isOpen = true;
            this.loadMedia();
        },
        
        closePicker() {
            this.isOpen = false;
            this.selectedFiles = [];
        },
        
        async loadMedia() {
            this.loading = true;
            try {
                const response = await fetch(this.listUrl + '?path=' + encodeURIComponent(this.currentPath));
                const data = await response.json();
                this.folders = data.folders || [];
                this.files = data.files || [];
                this.filterFiles();
            } catch (e) {
                console.error('Error loading media:', e);
            }
            this.loading = false;
        },
        
        navigateTo(path) {
            this.currentPath = path;
            this.loadMedia();
        },
        
        filterFiles() {
            if (!this.searchQuery) {
                this.filteredFiles = this.files;
            } else {
                const query = this.searchQuery.toLowerCase();
                this.filteredFiles = this.files.filter(f => f.name.toLowerCase().includes(query));
            }
        },
        
        toggleSelect(file) {
            const index = this.selectedFiles.findIndex(f => f.id === file.id);
            if (index > -1) {
                this.selectedFiles.splice(index, 1);
            } else {
                if (this.multiple) {
                    this.selectedFiles.push(file);
                } else {
                    this.selectedFiles = [file];
                }
            }
        },
        
        isSelected(file) {
            return this.selectedFiles.some(f => f.id === file.id);
        },
        
        async uploadFiles(event) {
            const files = event.target.files;
            if (!files.length) return;
            
            this.loading = true;
            const formData = new FormData();
            for (let i = 0; i < files.length; i++) {
                formData.append('files[]', files[i]);
            }
            formData.append('path', this.currentPath);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            
            try {
                const response = await fetch(this.uploadUrl, {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                if (data.success) {
                    this.loadMedia();
                }
            } catch (e) {
                console.error('Upload error:', e);
            }
            this.loading = false;
            event.target.value = '';
        },
        
        confirmSelection() {
            if (this.selectedFiles.length === 0) return;
            
            const urls = this.selectedFiles.map(f => f.url);
            
            // Dispatch event with selected files - this works for both Livewire and non-Livewire pages
            if (this.targetField) {
                window.dispatchEvent(new CustomEvent('media-selected', {
                    detail: {
                        field: this.targetField,
                        urls: this.multiple ? urls : urls[0],
                        files: this.selectedFiles
                    }
                }));
                
                // If Livewire component was passed directly, use it
                if (this.targetComponent) {
                    try {
                        this.targetComponent.set('settings.' + this.targetField, this.multiple ? urls : urls[0]);
                    } catch (e) {
                        console.log('Could not set Livewire value directly:', e);
                    }
                } else if (window.Livewire && typeof Livewire.first === 'function') {
                    // Try to find Livewire component on page (only if Livewire exists)
                    try {
                        const component = Livewire.first();
                        if (component && component.$wire) {
                            if (this.multiple) {
                                // For gallery, append to existing
                                const current = component.get('settings.' + this.targetField) || [];
                                component.set('settings.' + this.targetField, [...current, ...urls]);
                            } else {
                                component.set('settings.' + this.targetField, urls[0]);
                            }
                        }
                    } catch (e) {
                        // Livewire component not found or error - that's OK, event was dispatched
                        console.log('No Livewire component found, using event dispatch only');
                    }
                }
            }
            
            this.closePicker();
        }
    }
}
</script>
