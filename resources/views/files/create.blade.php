<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Subir Nuevo Archivo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --info-color: #0dcaf0;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --bg-color: #f8f9fa;
            --text-color: #212529;
            --border-color: #e9ecef;
            --card-bg: #ffffff;
            --header-gradient-start: var(--primary-color);
            --header-gradient-end: #0056b3;
            --preview-bg: #ffffff;
            --preview-text: #212529;
            --table-text: #212529;
            --table-bg: #ffffff;
        }

        [data-theme="dark"] {
            --bg-color: #1a1e21;
            --text-color: #e9ecef;
            --border-color: #495057;
            --card-bg: #2b3035;
            --header-gradient-start: #212529;
            --header-gradient-end: #141619;
            --table-text: #e9ecef;
            --table-bg: #2b3035;
            --preview-bg: #2b3035;
            --preview-text: #e9ecef;
        }
        
        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        
        .page-header {
            background: linear-gradient(135deg, var(--header-gradient-start), var(--header-gradient-end));
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .card {
            border: none;
            background-color: var(--card-bg);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s ease-in-out;
            margin-bottom: 2rem;
            color: var(--text-color);
        }
        
        .card:hover {
            transform: translateY(-2px);
        }
        
        .card-header {
            background: var(--card-bg);
            border-bottom: 2px solid var(--border-color);
            padding: 1rem 1.25rem;
            color: var(--text-color);
        }
        
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background: var(--primary-color);
            border: none;
            box-shadow: 0 2px 4px rgba(13, 110, 253, 0.2);
        }
        
        .btn-primary:hover {
            background: #0056b3;
            transform: translateY(-1px);
        }

        .file-type-info {
            margin-top: 20px;
            padding: 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            color: var(--text-color);
        }

        .file-type-info:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .form-control, .form-select {
            background-color: var(--bg-color);
            border-color: var(--border-color);
            color: var(--text-color);
        }

        .form-control:focus, .form-select:focus {
            background-color: var(--bg-color);
            border-color: var(--primary-color);
            color: var(--text-color);
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .form-label {
            color: var(--text-color);
        }

        .alert {
            border: none;
            border-radius: 8px;
        }
    </style>
    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        });
    </script>
</head>
<body>
    <div class="page-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-0">Subir Nuevo Archivo</h1>
                    <p class="text-light mb-0">Crea un nuevo archivo o usa una plantilla existente</p>
                </div>
                <div class="btn-group">
                    <button onclick="toggleTheme()" class="btn btn-light me-2">
                        <i class="bi bi-moon-stars"></i>
                    </button>
                    <a href="{{ route('files.index') }}" class="btn btn-light">
                        <i class="bi bi-arrow-left me-2"></i>Volver a Archivos
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-4">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Crear Archivo desde Plantilla</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('files.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="create_from_template" value="1">
                    <div class="mb-3">
                        <label for="template_id" class="form-label">Seleccionar Plantilla</label>
                        <select class="form-select" id="template_id" name="template_id" required>
                            <option value="">Seleccione una plantilla...</option>
                            @foreach($templates as $template)
                                @if($template->versions->count() > 0)
                                    @php
                                        $latestVersion = $template->versions->sortByDesc('created_at')->first();
                                    @endphp
                                    <option value="{{ $latestVersion->id }}">
                                        {{ $template->original_name }} (Última versión)
                                    </option>
                                @else
                                    <option value="{{ $template->id }}">
                                        {{ $template->original_name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="custom_title" class="form-label">Título Personalizado</label>
                        <input type="text" class="form-control" id="custom_title" name="custom_title" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción (Opcional)</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Ingrese una descripción para este archivo">{{ old('description') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Crear desde Plantilla
                    </button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-cloud-upload me-2"></i>Subir Nuevo Archivo</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('files.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                    @csrf
                    <div class="mb-3">
                        <label for="file" class="form-label">Seleccionar Archivo (PDF, Excel o Word)</label>
                        <input type="file" class="form-control" id="file" name="file" accept=".pdf,.xls,.xlsx,.doc,.docx,application/pdf,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" required>
                        <div class="form-text">Tamaño máximo del archivo: 10MB</div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción (Opcional)</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Ingrese una descripción para este archivo">{{ old('description') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload me-2"></i>Subir Archivo
                    </button>
                </form>

                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="file-type-info">
                            <h5><i class="bi bi-file-earmark-pdf text-danger me-2"></i>Archivos PDF</h5>
                            <p class="mb-2">Los archivos PDF se almacenarán de forma segura y se pueden previsualizar directamente en el navegador.</p>
                            <p class="mb-0">Recomendado para documentos que necesitan mantener el formato.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="file-type-info">
                            <h5><i class="bi bi-file-earmark-excel text-success me-2"></i>Archivos Excel</h5>
                            <p class="mb-2">Los archivos Excel (.xls, .xlsx) se almacenarán de forma segura y se pueden previsualizar usando Microsoft Office Online.</p>
                            <p class="mb-0">Recomendado para datos que necesitan ser analizados o manipulados.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="file-type-info">
                            <h5><i class="bi bi-file-earmark-word text-primary me-2"></i>Archivos Word</h5>
                            <p class="mb-2">Los archivos Word (.doc, .docx) se almacenarán de forma segura y se pueden editar usando Microsoft Office Online.</p>
                            <p class="mb-0">Recomendado para documentos de texto que requieren edición.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            const fileInput = document.getElementById('file');
            const file = fileInput.files[0];
            
            if (file) {
                const fileType = file.type;
                const validTypes = [
                    'application/pdf', 
                    'application/vnd.ms-excel', 
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                ];
                
                if (!validTypes.includes(fileType)) {
                    e.preventDefault();
                    alert('Por favor, seleccione solo archivos PDF, Excel o Word.');
                }
                
                if (file.size > 10 * 1024 * 1024) { // 10MB en bytes
                    e.preventDefault();
                    alert('El tamaño del archivo debe ser menor a 10MB.');
                }
            }
        });
    </script>
</body>
</html>