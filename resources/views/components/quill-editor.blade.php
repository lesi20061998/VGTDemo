@props(['name', 'value' => '', 'height' => '400px'])

<div x-data="quillEditor('{{ $name }}', @js($value), '{{ $height }}')" x-init="init()">
    <div :id="editorId" style="height: {{ $height }}"></div>
    <input type="hidden" :name="name" x-model="content">
</div>

@once
@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
.ql-editor{min-height:200px;font-size:16px}
.ql-toolbar.ql-snow{border-radius:8px 8px 0 0;background:#f8fafc}
.ql-container.ql-snow{border-radius:0 0 8px 8px}
</style>
@endpush

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
function quillEditor(name, initialValue, height) {
    return {
        name: name,
        content: initialValue,
        editorId: 'editor-' + name,
        quill: null,
        init() {
            this.quill = new Quill('#' + this.editorId, {
                theme: 'snow',
                placeholder: 'Nhập nội dung...',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                        [{ 'font': [] }],
                        [{ 'size': ['small', false, 'large', 'huge'] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'script': 'sub'}, { 'script': 'super' }],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'indent': '-1'}, { 'indent': '+1' }],
                        [{ 'align': [] }],
                        ['blockquote', 'code-block'],
                        ['link', 'image', 'video'],
                        ['clean']
                    ]
                }
            });
            
            if (this.content) {
                this.quill.root.innerHTML = this.content;
            }
            
            this.quill.on('text-change', () => {
                this.content = this.quill.root.innerHTML;
            });
        }
    }
}
</script>
@endpush
@endonce
