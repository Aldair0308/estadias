<div class="word-editor-container">
    <link href="https://cdn.ckeditor.com/4.16.2/full/ckeditor.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .word-editor-container {
            position: relative;
            margin: 20px 0;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }
        .word-editor-toolbar {
            padding: 10px;
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .word-editor-content {
            min-height: 500px;
            padding: 20px;
            background: white;
            overflow-y: auto;
        }
        .word-editor-fullscreen {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 9999;
            background: white;
        }
        .word-editor-fullscreen .word-editor-content {
            height: calc(100vh - 60px);
        }
        .document-content {
            font-family: 'Calibri', sans-serif;
            line-height: 1.5;
            color: #333;
        }
    </style>

    <div id="wordEditorWrapper" class="word-editor-wrapper">
        <div class="word-editor-toolbar">
            <h5 class="mb-0">Editor de Documento</h5>
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="toggleFullscreen">
                    <i class="bi bi-arrows-fullscreen"></i> Pantalla Completa
                </button>
                <button type="button" class="btn btn-sm btn-outline-primary" id="saveDocument" data-update-url="{{ route('files.content.update', ['file' => $documentId]) }}">
                    <i class="bi bi-save"></i> Guardar Cambios
                </button>
            </div>
        </div>
        <div class="word-editor-content">
            <div id="wordEditor" class="document-content">{!! $documentContent !!}</div>
        </div>
    </div>

    <script src="https://cdn.ckeditor.com/4.16.2/full/ckeditor.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let editor = null;

            function initializeEditor() {
                // Disable version check and auto inline
                CKEDITOR.disableAutoInline = true;
                CKEDITOR.config.versionCheck = false;

                try {
                    // Remove any existing instance
                    if (CKEDITOR.instances.wordEditor) {
                        CKEDITOR.instances.wordEditor.destroy();
                    }

                    editor = CKEDITOR.replace('wordEditor', {
                        readOnly: false,
                        allowedContent: true,
                        height: '400px',
                        removePlugins: 'elementspath,resize',
                        toolbarGroups: [
                            { name: 'document', groups: ['mode', 'document', 'doctools'] },
                            { name: 'clipboard', groups: ['clipboard', 'undo'] },
                            { name: 'editing', groups: ['find', 'selection', 'spellchecker', 'editing'] },
                            { name: 'forms', groups: ['forms'] },
                            '/',
                            { name: 'basicstyles', groups: ['basicstyles', 'cleanup'] },
                            { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi', 'paragraph'] },
                            { name: 'links', groups: ['links'] },
                            { name: 'insert', groups: ['insert'] },
                            '/',
                            { name: 'styles', groups: ['styles'] },
                            { name: 'colors', groups: ['colors'] },
                            { name: 'tools', groups: ['tools'] },
                            { name: 'others', groups: ['others'] },
                        ]
                    });

                    // Set initial content after editor is ready
                    editor.on('instanceReady', function() {
                        try {
                            @if(!empty($documentContent))
                                editor.setData({!! json_encode($documentContent) !!});
                            @else
                                editor.setData('<p>No hay vista previa disponible para este documento. Puede descargar el archivo para verlo en Microsoft Word.</p>');
                            @endif
                        } catch (error) {
                            console.error('Error loading Word content:', error);
                            editor.setData('<p>Error al cargar el contenido del documento. Puede descargar el archivo para verlo en Microsoft Word.</p>');
                        }
                    });

                    return editor;
                } catch (error) {
                    console.error('Error initializing CKEditor:', error);
                    return null;
                }
            }

            // Initialize the editor
            editor = initializeEditor();

            // Fullscreen toggle functionality
            const wrapper = document.getElementById('wordEditorWrapper');
            const toggleBtn = document.getElementById('toggleFullscreen');

            toggleBtn.addEventListener('click', function() {
                if (!editor) {
                    console.error('Editor not initialized');
                    return;
                }

                wrapper.classList.toggle('word-editor-fullscreen');
                if (wrapper.classList.contains('word-editor-fullscreen')) {
                    editor.resize('100%', wrapper.offsetHeight - 120);
                } else {
                    editor.resize('100%', '400');
                }
            });

            // Save functionality implementation
            const saveBtn = document.getElementById('saveDocument');
            saveBtn.addEventListener('click', async function() {
                if (!editor || !editor.getData) {
                    alert('Error: Editor no está inicializado correctamente. Por favor, recargue la página.');
                    return;
                }

                try {
                    const content = editor.getData();
                    const updateUrl = document.getElementById('saveDocument').dataset.updateUrl;
                    const response = await fetch(updateUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ content: content })
                    });

                    if (!response.ok) {
                        const errorData = await response.json().catch(() => null);
                        throw new Error(errorData?.error || 'Error al guardar el documento');
                    }

                    const data = await response.json();
                    alert('Documento guardado exitosamente');
                    window.location.reload();
                } catch (error) {
                    console.error('Error saving document:', error);
                    alert(error.message || 'Error al guardar el documento');
                }
            });
        });
    </script>
</div>