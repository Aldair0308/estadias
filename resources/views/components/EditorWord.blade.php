<div class="word-editor-container">
    @props(['documentContent' => '', 'documentId' => null])
    
    <link href="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.css" rel="stylesheet">
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

    <div class="d-flex justify-content-end mb-3">
        <button type="button" class="btn btn-primary me-2" id="fullscreenBtn">
            <i class="bi bi-arrows-fullscreen"></i> Pantalla Completa
        </button>
        <button type="button" class="btn btn-success" id="saveDocBtn">
            <i class="bi bi-save"></i> Guardar Cambios
        </button>
    </div>

    <div id="editor-container" class="border rounded">
        <textarea id="wordEditor">{{ $documentContent }}</textarea>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            tinymce.init({
                selector: '#wordEditor',
                height: 500,
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'help', 'wordcount'
                ],
                toolbar: 'undo redo | blocks | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
                content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
            });

            // Fullscreen functionality
            const editorContainer = document.getElementById('editor-container');
            const fullscreenBtn = document.getElementById('fullscreenBtn');

            fullscreenBtn.addEventListener('click', () => {
                if (!document.fullscreenElement) {
                    editorContainer.requestFullscreen();
                    fullscreenBtn.innerHTML = '<i class="bi bi-fullscreen-exit"></i> Salir Pantalla Completa';
                } else {
                    document.exitFullscreen();
                    fullscreenBtn.innerHTML = '<i class="bi bi-arrows-fullscreen"></i> Pantalla Completa';
                }
            });

            // Save functionality
            const saveDocBtn = document.getElementById('saveDocBtn');
            saveDocBtn.addEventListener('click', () => {
                const content = tinymce.get('wordEditor').getContent();
                fetch(`/files/${@json($documentId)}/update-content`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ content })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Documento guardado exitosamente');
                    } else {
                        alert('Error al guardar el documento');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al guardar el documento');
                });
            });
        });
    </script>

    <style>
        .word-editor-container {
            margin-top: 2rem;
        }
        #editor-container {
            background: white;
            transition: all 0.3s ease;
        }
        #editor-container:fullscreen {
            padding: 1rem;
            background: white;
        }
        .tox-tinymce {
            border: none !important;
        }
    </style>
</div>