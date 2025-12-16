@extends('cms.layouts.app')

@section('title', 'Quản lý Form')
@section('page-title', 'Form Builder')

@section('content')
<div class="mb-6">
    <a href="{{ route('cms.settings.index') }}" class="text-sm text-gray-600 hover:text-gray-900">← Quay lại</a>
</div>

<div class="bg-white rounded-lg shadow-sm p-6" x-data="formManager()">
    <div class="flex items-center justify-between mb-6">
        <h3 class="font-semibold">Danh sách Form</h3>
        <button @click="showEditor = true; editIndex = null; resetForm()" class="px-4 py-2 bg-blue-600 text-white rounded-lg">+ Tạo Form</button>
    </div>

    <!-- Form Editor Modal -->
    <div x-show="showEditor" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <h3 class="font-semibold mb-4" x-text="editIndex !== null ? 'Sửa Form' : 'Tạo Form Mới'"></h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Tên Form</label>
                    <input type="text" x-model="currentForm.name" class="w-full px-4 py-2 border rounded-lg">
                </div>
                
                <div class="flex items-center justify-between">
                    <h4 class="font-medium">Các trường</h4>
                    <button type="button" @click="showFieldSelector = true" class="px-3 py-1 bg-green-600 text-white rounded text-sm">+ Thêm trường</button>
                </div>
                
                <!-- Field Selector -->
                <div x-show="showFieldSelector" class="border rounded-lg p-4 bg-gray-50">
                    <div class="grid grid-cols-3 gap-3 mb-3">
                        <button type="button" @click="addField('text')" class="p-3 border rounded hover:border-blue-500">Text</button>
                        <button type="button" @click="addField('email')" class="p-3 border rounded hover:border-blue-500">Email</button>
                        <button type="button" @click="addField('phone')" class="p-3 border rounded hover:border-blue-500">Phone</button>
                        <button type="button" @click="addField('textarea')" class="p-3 border rounded hover:border-blue-500">Textarea</button>
                        <button type="button" @click="addField('select')" class="p-3 border rounded hover:border-blue-500">Select</button>
                        <button type="button" @click="addField('checkbox')" class="p-3 border rounded hover:border-blue-500">Checkbox</button>
                    </div>
                    <button type="button" @click="showFieldSelector = false" class="text-sm text-gray-600">Đóng</button>
                </div>
                
                <!-- Fields List -->
                <div class="space-y-2">
                    <template x-for="(field, index) in currentForm.fields" :key="index">
                        <div class="border rounded-lg p-3 bg-gray-50">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-medium text-sm" x-text="field.label || 'Trường ' + (index + 1)"></span>
                                <button type="button" @click="removeField(index)" class="text-red-600 text-sm">× Xóa</button>
                            </div>
                            <div class="grid grid-cols-3 gap-2">
                                <input type="text" x-model="field.label" placeholder="Nhãn" class="px-3 py-2 text-sm border rounded">
                                <input type="text" x-model="field.placeholder" placeholder="Placeholder" class="px-3 py-2 text-sm border rounded">
                                <label class="flex items-center text-sm">
                                    <input type="checkbox" x-model="field.required" class="mr-2">Bắt buộc
                                </label>
                            </div>
                            <template x-if="field.type === 'select'">
                                <textarea x-model="field.options" placeholder="Tùy chọn (mỗi dòng 1)" rows="2" class="w-full px-3 py-2 text-sm border rounded mt-2"></textarea>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
            
            <div class="flex gap-2 mt-6">
                <button type="button" @click="saveForm()" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                    <span x-text="editIndex !== null ? 'Cập nhật' : 'Tạo'"></span>
                </button>
                <button type="button" @click="showEditor = false" class="px-4 py-2 border rounded-lg">Hủy</button>
            </div>
        </div>
    </div>

    <!-- Forms List -->
    <div class="grid grid-cols-3 gap-4">
        <template x-for="(form, index) in forms" :key="index">
            <div class="border rounded-lg p-4 hover:border-blue-500">
                <h4 class="font-medium mb-2" x-text="form.name"></h4>
                <p class="text-sm text-gray-500 mb-3"><span x-text="form.fields.length"></span> trường</p>
                <div class="flex gap-2">
                    <button @click="editForm(index)" class="text-sm text-blue-600">Sửa</button>
                    <button @click="deleteForm(index)" class="text-sm text-red-600">Xóa</button>
                </div>
            </div>
        </template>
    </div>

    <form method="POST" action="{{ route('cms.settings.save') }}" class="mt-6">
        @csrf
        <input type="hidden" name="forms" :value="JSON.stringify(forms)">
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg">Lưu tất cả</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
function formManager() {
    return {
        forms: @json(json_decode(setting('forms', '[]'), true)),
        showEditor: false,
        showFieldSelector: false,
        editIndex: null,
        currentForm: { name: '', fields: [] },
        
        resetForm() {
            this.currentForm = { name: '', fields: [] };
            this.showFieldSelector = false;
        },
        
        addField(type) {
            this.currentForm.fields.push({
                type: type,
                label: '',
                placeholder: '',
                required: false,
                options: type === 'select' ? 'Tùy chọn 1\nTùy chọn 2' : ''
            });
            this.showFieldSelector = false;
        },
        
        removeField(index) {
            this.currentForm.fields.splice(index, 1);
        },
        
        saveForm() {
            if (!this.currentForm.name) {
                alert('Vui lòng nhập tên form');
                return;
            }
            if (this.editIndex !== null) {
                this.forms[this.editIndex] = {...this.currentForm};
            } else {
                this.forms.push({...this.currentForm});
            }
            this.showEditor = false;
            this.resetForm();
        },
        
        editForm(index) {
            this.editIndex = index;
            this.currentForm = JSON.parse(JSON.stringify(this.forms[index]));
            this.showEditor = true;
        },
        
        deleteForm(index) {
            if (confirm('Xóa form này?')) {
                this.forms.splice(index, 1);
            }
        }
    }
}
</script>
@endsection
