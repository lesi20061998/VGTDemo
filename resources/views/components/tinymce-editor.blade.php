@props(['name', 'value' => '', 'height' => 400])

<textarea name="{{ $name }}" id="{{ $name }}" class="tinymce-editor">{{ $value }}</textarea>

@once
@push('scripts')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    tinymce.init({
        selector: '.tinymce-editor',
        height: {{ $height }},
        menubar: true,
        language: 'vi',
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount', 'emoticons'
        ],
        toolbar: 'undo redo | blocks | bold italic underline strikethrough | forecolor backcolor | ' +
                 'alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | ' +
                 'removeformat | table link image media | code fullscreen preview | help',
        toolbar_mode: 'sliding',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; line-height: 1.6; }',
        block_formats: 'Paragraph=p; Heading 1=h1; Heading 2=h2; Heading 3=h3; Heading 4=h4; Heading 5=h5; Heading 6=h6; Preformatted=pre',
        font_size_formats: '8pt 10pt 12pt 14pt 16pt 18pt 24pt 36pt 48pt',
        image_advtab: true,
        image_caption: true,
        quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote',
        contextmenu: 'link image table',
        promotion: false,
        branding: false,
        resize: true,
        elementpath: false,
        statusbar: true,
        paste_data_images: true,
        automatic_uploads: true,
        file_picker_types: 'image',
        images_upload_handler: function (blobInfo, success, failure) {
            const formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            fetch('/admin/media/upload', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.url) {
                    success(result.url);
                } else {
                    failure('Upload failed');
                }
            })
            .catch(() => failure('Upload failed'));
        }
    });
});
</script>
@endpush
@endonce
