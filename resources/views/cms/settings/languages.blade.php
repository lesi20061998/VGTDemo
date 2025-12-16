@extends('cms.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6" x-data="languageManager()">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Quản lý Ngôn ngữ</h2>
            <button @click="showAddModal = true" class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Thêm ngôn ngữ
            </button>
        </div>

        <!-- Languages List -->
        <div class="space-y-3">
            <template x-for="(lang, index) in languages" :key="index">
                <div class="flex items-center gap-4 p-4 border rounded-lg hover:bg-gray-50">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg" x-text="lang.code.toUpperCase()"></div>
                    <div class="flex-1">
                        <div class="font-semibold text-lg" x-text="lang.name"></div>
                        <div class="text-sm text-gray-500">
                            Code: <span class="font-mono" x-text="lang.code"></span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   :checked="lang.is_default"
                                   @change="setDefault(index)"
                                   class="rounded border-gray-300">
                            <span class="ml-2 text-sm">Mặc định</span>
                        </label>
                        <button @click="editLanguage(index)" class="p-2 text-blue-600 hover:bg-blue-50 rounded">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                        <button @click="deleteLanguage(index)" 
                                x-show="!lang.is_default"
                                class="p-2 text-red-600 hover:bg-red-50 rounded">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </template>

            <div x-show="languages.length === 0" class="text-center py-12 text-gray-400">
                <p class="text-lg">Chưa có ngôn ngữ nào</p>
                <p class="text-sm">Nhấn "Thêm ngôn ngữ" để bắt đầu</p>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <div x-show="showAddModal" 
             x-cloak
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
             @click.self="closeModal()"
             style="display: none;">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6" @click.stop>
                <h3 class="text-xl font-bold mb-4" x-text="editIndex !== null ? 'Sửa ngôn ngữ' : 'Thêm ngôn ngữ'"></h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block font-medium mb-2">Tên ngôn ngữ</label>
                        <input type="text" x-model="form.name" 
                               placeholder="Tiếng Việt"
                               class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block font-medium mb-2">Mã ngôn ngữ (code)</label>
                        <input type="text" x-model="form.code" 
                               placeholder="vi"
                               class="w-full border rounded px-3 py-2 font-mono">
                        <p class="text-xs text-gray-500 mt-1">VD: vi, en, zh, ja, ko...</p>
                    </div>


                </div>

                <div class="flex gap-3 mt-6">
                    <button @click="saveLanguage()" class="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        Lưu
                    </button>
                    <button @click="closeModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                        Hủy
                    </button>
                </div>
            </div>
        </div>

        <!-- Save Form -->
        <form action="{{ route('cms.settings.save') }}" method="POST" x-ref="saveForm">
            @csrf
            <input type="hidden" name="page" value="languages">
            <input type="hidden" name="languages" :value="JSON.stringify(languages)">
            

        </form>
    </div>

    <!-- String Translations -->
    <div class="mt-6 bg-white rounded-lg shadow p-6" x-data="translationManager()">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Quản lý Chuỗi dịch</h2>
            <button @click="scanTranslations()" class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Quét tự động
            </button>
        </div>

        <!-- Translations List -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Key</th>
                        <template x-for="lang in languages" :key="lang.code">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                <span x-text="lang.name"></span>
                            </th>
                        </template>

                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="(trans, index) in translations" :key="index">
                        <tr class="cursor-pointer hover:bg-gray-50" @click="editTranslation(index)">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono" x-text="trans.key"></td>
                            <template x-for="lang in languages" :key="lang.code">
                                <td class="px-6 py-4 text-sm">
                                    <span x-text="trans.values[lang.code] || '-'" :class="!trans.values[lang.code] ? 'text-gray-400 italic' : ''"></span>
                                </td>
                            </template>
                        </tr>
                    </template>
                    <tr x-show="translations.length === 0">
                        <td colspan="100" class="px-6 py-12 text-center text-gray-400">
                            Chưa có chuỗi dịch nào
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Add/Edit Translation Modal -->
        <div x-show="showAddTranslation" 
             x-cloak
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
             @click.self="closeTranslationModal()"
             style="display: none;">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl p-6" @click.stop>
                <h3 class="text-xl font-bold mb-4">Dịch chuỗi</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block font-medium mb-2">Key</label>
                        <div class="w-full border rounded px-3 py-2 font-mono bg-gray-50" x-text="transForm.key"></div>
                    </div>

                    <div class="space-y-3">
                        <label class="block font-medium mb-2">Nội dung dịch</label>
                        <template x-for="lang in languages" :key="lang.code">
                            <div>
                                <label class="block text-sm text-gray-600 mb-1" x-text="lang.name"></label>
                                <input type="text" 
                                       x-model="transForm.values[lang.code]"
                                       :placeholder="'Nhập nội dung ' + lang.name"
                                       class="w-full border rounded px-3 py-2">
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button @click="saveTranslation()" class="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        Lưu
                    </button>
                    <button @click="closeTranslationModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                        Hủy
                    </button>
                </div>
            </div>
        </div>

        <!-- Save Form -->
        <form action="{{ route('cms.settings.save') }}" method="POST" x-ref="transForm">
            @csrf
            <input type="hidden" name="page" value="translations">
            <input type="hidden" name="translations" :value="JSON.stringify(translations)">
        </form>
    </div>

    <!-- Info Box -->
    <div class="mt-6 p-4 bg-blue-50 rounded-lg">
        <div class="flex items-start gap-2 mb-2">
            <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="font-semibold">Hướng dẫn sử dụng:</h3>
        </div>
        <ul class="text-sm space-y-1 text-gray-700">
            <li>• Thêm các ngôn ngữ bạn muốn hỗ trợ</li>
            <li>• Chọn 1 ngôn ngữ làm mặc định</li>
            <li>• Thêm chuỗi dịch cho các từ trong code PHP</li>
            <li>• Sử dụng trong code: <code class="bg-white px-2 py-1 rounded">__('home.welcome')</code></li>
            <li>• Khi tạo Post/Product/Page sẽ có tab để nhập nội dung cho từng ngôn ngữ</li>
        </ul>
    </div>
</div>

<script>
function languageManager() {
    let savedLanguages = {!! json_encode(setting('languages', [])) !!};
    if (!Array.isArray(savedLanguages) || savedLanguages.length === 0) {
        savedLanguages = [
            {name: 'Tiếng Việt', code: 'vi', is_default: true},
            {name: 'English', code: 'en', is_default: false}
        ];
    }

    return {
        languages: savedLanguages,
        showAddModal: false,
        editIndex: null,
        form: {
            name: '',
            code: '',
            is_default: false
        },

        setDefault(index) {
            this.languages.forEach((lang, i) => {
                lang.is_default = i === index;
            });
            showAlert('Đã đặt ngôn ngữ mặc định', 'info');
            
            // Auto save to database
            this.$nextTick(() => {
                this.$refs.saveForm.submit();
            });
        },

        editLanguage(index) {
            this.editIndex = index;
            const lang = this.languages[index];
            this.form = {
                name: lang.name,
                code: lang.code,
                is_default: lang.is_default
            };
            this.showAddModal = true;
        },

        deleteLanguage(index) {
            if (this.languages[index].is_default) {
                showAlert('Không thể xóa ngôn ngữ mặc định', 'warning');
                return;
            }
            if (confirm('Xóa ngôn ngữ này?')) {
                this.languages.splice(index, 1);
                showAlert('Xóa ngôn ngữ thành công!', 'success');
                
                // Auto save to database
                this.$nextTick(() => {
                    this.$refs.saveForm.submit();
                });
            }
        },

        saveLanguage() {
            if (!this.form.name || !this.form.code) {
                showAlert('Vui lòng điền đầy đủ thông tin', 'error');
                return;
            }

            if (this.editIndex !== null) {
                // Edit
                this.languages[this.editIndex].name = this.form.name;
                this.languages[this.editIndex].code = this.form.code;
                showAlert('Cập nhật ngôn ngữ thành công!', 'success');
            } else {
                // Add new
                if (!Array.isArray(this.languages)) {
                    this.languages = [];
                }
                this.languages.push({
                    name: this.form.name,
                    code: this.form.code,
                    is_default: this.languages.length === 0
                });
                showAlert('Thêm ngôn ngữ thành công!', 'success');
            }

            this.closeModal();
            
            // Auto save to database
            this.$nextTick(() => {
                this.$refs.saveForm.submit();
            });
        },

        closeModal() {
            this.showAddModal = false;
            this.editIndex = null;
            this.form = { name: '', code: '', is_default: false };
        }
    }
}

function translationManager() {
    let rawData = {!! json_encode(setting('translations', '[]')) !!};
    let savedTranslations = typeof rawData === 'string' ? JSON.parse(rawData) : rawData;
    if (!Array.isArray(savedTranslations)) savedTranslations = [];

    return {
        languages: @json(setting('languages', [])),
        translations: savedTranslations,
        showAddTranslation: false,
        editTransIndex: null,
        transForm: {
            key: '',
            values: {}
        },

        editTranslation(index) {
            this.editTransIndex = index;
            const trans = this.translations[index];
            this.transForm.key = trans.key;
            
            // Parse values if it's a string
            let values = trans.values;
            if (typeof values === 'string') {
                try {
                    values = JSON.parse(values);
                } catch (e) {
                    values = {};
                }
            }
            
            // Initialize values for all languages
            this.transForm.values = {};
            this.languages.forEach(lang => {
                this.transForm.values[lang.code] = values && values[lang.code] ? values[lang.code] : '';
            });
            
            this.showAddTranslation = true;
        },



        saveTranslation() {
            if (!this.transForm.key) {
                showAlert('Vui lòng nhập key', 'error');
                return;
            }

            // Clean values object - ensure all are plain strings
            const cleanValues = {};
            Object.keys(this.transForm.values).forEach(langCode => {
                cleanValues[langCode] = String(this.transForm.values[langCode] || '');
            });

            const newTrans = {
                key: this.transForm.key,
                values: cleanValues
            };

            if (this.editTransIndex !== null) {
                this.translations[this.editTransIndex] = newTrans;
                showAlert('Cập nhật chuỗi dịch thành công!', 'success');
            } else {
                this.translations.push(newTrans);
                showAlert('Thêm chuỗi dịch thành công!', 'success');
            }

            this.closeTranslationModal();
            this.$nextTick(() => {
                this.$refs.transForm.submit();
            });
        },

        closeTranslationModal() {
            this.showAddTranslation = false;
            this.editTransIndex = null;
            this.transForm.key = '';
            this.transForm.values = {};
            // Re-init values for all languages
            this.languages.forEach(lang => {
                this.transForm.values[lang.code] = '';
            });
        },

        async scanTranslations() {
            try {
                const response = await fetch('{{ route('project.admin.settings.scan-translations', $currentProject->code) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const data = await response.json();
                
                if (data.success) {
                    // Add new keys that don't exist
                    let addedCount = 0;
                    data.keys.forEach(key => {
                        const exists = this.translations.find(t => t.key === key);
                        if (!exists) {
                            const values = {};
                            this.languages.forEach(lang => {
                                values[lang.code] = '';
                            });
                            this.translations.push({ key, values });
                            addedCount++;
                        }
                    });
                    
                    if (addedCount > 0) {
                        showAlert(`Đã tìm thấy ${addedCount} chuỗi mới!`, 'success');
                        this.$nextTick(() => {
                            this.$refs.transForm.submit();
                        });
                    } else {
                        showAlert('Không có chuỗi mới', 'info');
                    }
                } else {
                    showAlert('Lỗi quét: ' + data.message, 'error');
                }
            } catch (error) {
                showAlert('Lỗi: ' + error.message, 'error');
            }
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>
@endsection
