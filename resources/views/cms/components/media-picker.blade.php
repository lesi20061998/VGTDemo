@props(['name', 'value' => '', 'label' => 'Chọn Ảnh / File'])

@php
    $inputId = 'media_picker_' . Str::random(8);
    $mediaListUrl = isset($currentProject) && $currentProject ? route('project.admin.media.list', $currentProject->code) : (Route::has('media.list') ? route('media.list') : url('media/list'));
@endphp

<div x-data="mediaPicker('{{ $inputId }}', '{{ $mediaListUrl }}', '{{ $value }}')" class="media-picker-wrapper">
    <div class="flex items-center gap-4">
        <!-- Preview Area -->
        <div class="relative w-24 h-24 border border-gray-300 rounded-lg overflow-hidden bg-gray-50 flex items-center justify-center">
            <template x-if="previewUrl">
                <img :src="previewUrl" class="w-full h-full object-cover">
            </template>
            <template x-if="!previewUrl">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </template>
            <!-- Remove Button -->
            <button type="button" x-show="previewUrl" @click.prevent="clearSelection()" class="absolute top-1 right-1 bg-white rounded-full p-1 shadow hover:bg-red-50 text-red-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="flex-1">
            <input type="hidden" name="{{ $name }}" id="{{ $inputId }}" :value="selectedValue">
            <button type="button" @click.prevent="openModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 flex items-center gap-2 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
                {{ $label }}
            </button>
            <p class="text-xs text-gray-500 mt-2" x-text="selectedValue ? selectedValue : 'Chưa chọn file nào'"></p>
        </div>
    </div>

    <!-- Media Picker Modal -->
    <div x-show="isOpen" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto overflow-x-hidden bg-black bg-opacity-50">
        <div class="relative w-full max-w-4xl max-h-full p-4" @click.away="closeModal()">
            <div class="relative bg-white rounded-lg shadow h-[80vh] flex flex-col">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Quản lý Media</h3>
                    <button type="button" @click="closeModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                        <svg class="w-3 h-3" aria-hidden="true" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 flex-1 overflow-y-auto bg-gray-50 relative">
                    <!-- Loading State -->
                    <div x-show="isLoading" class="absolute inset-0 bg-white/80 flex items-center justify-center z-10">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    </div>

                    <!-- Breadcrumb / Toolbar -->
                    <div class="flex items-center gap-2 mb-4 bg-white p-2 rounded shadow-sm">
                        <button type="button" @click="loadMedia('')" class="p-2 hover:bg-gray-100 rounded">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        </button>
                        <span class="text-gray-400">/</span>
                        <span class="text-sm text-gray-600" x-text="currentPath || 'Root'"></span>
                        
                        <div class="ml-auto">
                            <!-- Input file for upload -->
                            <input type="file" x-ref="fileInput" @change="uploadFile" class="hidden" accept="image/*,.pdf,.doc,.docx">
                            <button type="button" @click="$refs.fileInput.click()" class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                Upload
                            </button>
                        </div>
                    </div>

                    <!-- Grid -->
                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-4">
                        <!-- Folders -->
                        <template x-for="folder in folders" :key="folder.path">
                            <div @click="loadMedia(folder.path)" class="bg-white p-3 rounded-lg shadow-sm border border-transparent hover:border-blue-500 cursor-pointer text-center group transition-all">
                                <svg class="w-12 h-12 mx-auto text-yellow-400 group-hover:text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path>
                                </svg>
                                <p class="text-xs mt-2 truncate text-gray-700" x-text="folder.name"></p>
                            </div>
                        </template>

                        <!-- Files -->
                        <template x-for="file in files" :key="file.path">
                            <div @click="selectFile(file)" 
                                 :class="{'border-blue-500 ring-2 ring-blue-200': selectedFile && selectedFile.path === file.path, 'border-gray-200 hover:border-blue-400': !selectedFile || selectedFile.path !== file.path}"
                                 class="bg-white p-2 rounded-lg shadow-sm border cursor-pointer group transition-all relative">
                                
                                <div class="aspect-square bg-gray-100 rounded overflow-hidden flex items-center justify-center mb-2">
                                    <!-- Image preview -->
                                    <template x-if="isImage(file.url)">
                                        <img :src="file.url" class="w-full h-full object-cover">
                                    </template>
                                    <!-- Icon for non-images -->
                                    <template x-if="!isImage(file.url)">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                    </template>
                                </div>
                                <p class="text-xs truncate text-gray-600" x-text="file.name" :title="file.name"></p>
                                <p class="text-[10px] text-gray-400 mt-0.5" x-text="file.size"></p>

                                <!-- Checkmark for selected -->
                                <div x-show="selectedFile && selectedFile.path === file.path" class="absolute -top-2 -right-2 bg-blue-600 text-white rounded-full p-1 shadow">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                            </div>
                        </template>

                        <!-- Empty state -->
                        <div x-show="folders.length === 0 && files.length === 0 && !isLoading" class="col-span-full py-12 text-center">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-gray-500">Thư mục trống</p>
                        </div>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center justify-end p-4 border-t bg-gray-50">
                    <button type="button" @click="closeModal()" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 focus:z-10 focus:ring-4 focus:ring-gray-100">
                        Hủy
                    </button>
                    <button type="button" @click="confirmSelection()" :disabled="!selectedFile" class="ms-3 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center disabled:opacity-50 disabled:cursor-not-allowed">
                        Chọn file
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
@once
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('mediaPicker', (inputId, mediaListUrl, initialValue) => ({
            isOpen: false,
            isLoading: false,
            currentPath: '',
            folders: [],
            files: [],
            selectedValue: initialValue,
            selectedFile: null,
            tinymceCallback: null,

            get previewUrl() {
                if (!this.selectedValue) return null;
                // If it's a full URL or starts with /, return as is
                if (this.selectedValue.startsWith('http') || this.selectedValue.startsWith('/')) {
                    // Check if image
                    if (this.isImage(this.selectedValue)) return this.selectedValue;
                    return null;
                }
                // Assuming stored value might be relative to storage
                return this.isImage(this.selectedValue) ? '/storage/' + this.selectedValue : null;
            },

            init() {
                // Listen for TinyMCE media picker requests
                window.addEventListener('open-media-picker-tinymce', (e) => {
                    this.tinymceCallback = e.detail.callback;
                    this.openModal();
                });
            },

            isImage(url) {
                if (!url) return false;
                const ext = url.split('.').pop().toLowerCase();
                return ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'].includes(ext);
            },

            openModal() {
                this.isOpen = true;
                this.loadMedia(this.currentPath);
                document.body.style.overflow = 'hidden';
            },

            closeModal() {
                this.isOpen = false;
                this.selectedFile = null;
                this.tinymceCallback = null;
                document.body.style.overflow = 'auto';
            },

            async loadMedia(path = '') {
                this.isLoading = true;
                this.currentPath = path;
                this.selectedFile = null;
                
                try {
                    const url = new URL(mediaListUrl);
                    url.searchParams.append('path', path);
                    
                    const response = await fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        // Assume MediaController returns { folders: [], files: [] } or similar
                        this.folders = data.folders || [];
                        this.files = data.files || [];
                    } else {
                        console.error('Failed to load media');
                    }
                } catch (error) {
                    console.error('Error loading media:', error);
                } finally {
                    this.isLoading = false;
                }
            },

            selectFile(file) {
                this.selectedFile = file;
            },

            confirmSelection() {
                if (this.selectedFile) {
                    const url = this.selectedFile.url;
                    
                    if (this.tinymceCallback) {
                        // Return to TinyMCE
                        this.tinymceCallback(url, { title: this.selectedFile.name });
                    } else {
                        // Normal input selection. We store the relative path or full url based on logic.
                        // Let's store full URL or relative path if it's in storage
                        this.selectedValue = this.selectedFile.path; 
                        
                        // Wait, for TinyMCE we return full URL, for db we might just want the path or url.
                        // Let's just use the URL so it's simple to display.
                        this.selectedValue = url;
                    }
                    
                    // Trigger change event on the hidden input so other scripts know
                    setTimeout(() => {
                        document.getElementById(inputId).dispatchEvent(new Event('change'));
                    }, 100);
                }
                this.closeModal();
            },

            clearSelection() {
                this.selectedValue = '';
                setTimeout(() => {
                    document.getElementById(inputId).dispatchEvent(new Event('change'));
                }, 100);
            },

            async uploadFile(e) {
                const file = e.target.files[0];
                if (!file) return;

                this.isLoading = true;
                const formData = new FormData();
                formData.append('file', file);
                formData.append('path', this.currentPath);

                // Need to determine the upload URL from the list URL
                const uploadUrl = mediaListUrl.replace('/list', '/upload');

                try {
                    const response = await fetch(uploadUrl, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        // Reload current folder
                        await this.loadMedia(this.currentPath);
                        if(window.showAlert) showAlert('Upload thành công!', 'success');
                    } else {
                        const errorData = await response.json();
                        alert('Upload lỗi: ' + (errorData.message || 'Unknown error'));
                    }
                } catch (error) {
                    console.error('Upload error:', error);
                    alert('Lỗi khi upload file.');
                } finally {
                    this.isLoading = false;
                    e.target.value = ''; // Reset input
                }
            }
        }));
    });
</script>
@endonce
@endpush
