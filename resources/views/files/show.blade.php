<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detalles del Archivo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary-color: #35B550;
            --secondary-color: #2CA14D;
            --success-color: #35B550;
            --info-color: #0dcaf0;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --bg-color: #FFFFFF;
            --text-color: #212529;
            --border-color: #e9ecef;
            --card-bg: #FFFFFF;
            --header-gradient-start: #35B550;
            --header-gradient-end: #2CA14D;
            --preview-bg: #FFFFFF;
            --preview-text: #212529;
            --table-text: #212529;
            --table-bg: #FFFFFF;
        }

        [data-theme="dark"] {
            --bg-color: #121212;
            --text-color: #e9ecef;
            --border-color: #495057;
            --card-bg: #1E1E1E;
            --header-gradient-start: #2CA14D;
            --header-gradient-end: #1E1E1E;
            --table-text: #e9ecef;
            --table-bg: #1E1E1E;
            --preview-bg: #FFFFFF;
            --preview-text: #212529;
        }
        
        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        
        .text-muted {
            color: rgba(16, 17, 18, 0.75) !important;
        }
        [data-theme="dark"] .text-muted {
            color: rgba(233, 236, 239, 0.75) !important;
        }
        
        .small, small {
            color: inherit;
            opacity: 0.85;
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
        
        .document-preview {
            max-height: 800px;
            overflow-y: auto;
            padding: 2rem;
            background-color: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            color: #212529;
        }
        
        .document-preview img {
            max-width: 100%;
            height: auto;
            margin: 1rem 0;
            display: block;
            border-radius: 4px;
        }
        
        .document-preview table {
            width: 100%;
            margin-bottom: 1rem;
            border-collapse: collapse;
            background-color: var(--card-bg);
        }
        
        .document-preview table td,
        .document-preview table th {
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            color: var(--preview-text);
        }
        
        .document-preview p {
            margin-bottom: 1rem;
            line-height: 1.6;
            color: var(--preview-text);
        }
        
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .btn-group .btn {
            border-radius: 4px;
        }
        
        .btn-primary {
            background: var(--primary-color);
            border: none;
            box-shadow: 0 2px 4px rgba(53, 181, 80, 0.2);
        }
        
        .btn-primary:hover {
            background: var(--secondary-color);
            transform: translateY(-1px);
        }
        
        .table {
            background: var(--table-bg);
            border-radius: 8px;
            overflow: hidden;
            color: var(--table-text);
        }
        
        .table thead th {
            background-color: var(--table-bg);
            border-bottom: 2px solid var(--border-color);
            color: var(--table-text);
            font-weight: 600;
        }
        
        .table-hover tbody tr:hover {
            background-color: var(--bg-color);
        }
        
        .file-info {
            background: var(--card-bg);
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        
        .file-info p {
            margin-bottom: 0.5rem;
            color: var(--text-color);
        }
        
        .file-info strong {
            color: var(--text-color);
            min-width: 120px;
            display: inline-block;
        }
        
        .preview-section {
            background: var(--card-bg);
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .preview-section h5 {
            color: var(--text-color);
            margin-bottom: 1rem;
            font-weight: 600;
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
<body class="bg-light">
    <div class="page-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-0">{{ $file->original_name }}</h1>
                    <p class="text-light mb-0"><i class="bi bi-file-earmark me-2"></i>{{ $file->mime_type }}</p>
                </div>
                <div class="btn-group">
                    <button onclick="toggleTheme()" class="btn btn-light me-2">
                        <i class="bi bi-moon-stars"></i>
                    </button>
                    <a href="{{ route('files.index') }}" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i>Volver</a>
                    <a href="{{ route('files.history', $file->id) }}" class="btn btn-light"><i class="bi bi-clock-history me-2"></i>Historial</a>
                    @can('edit files')
                    <a href="{{ route('files.edit', $file->id) }}" class="btn btn-warning"><i class="bi bi-pencil me-2"></i>Editar</a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid py-3 px-4" style="background-color: var(--bg-color); transition: background-color 0.3s ease;">
        <div class="row g-2">
            <div class="col-12">
                <div class="preview-section mb-3">
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-file-earmark-text fs-3 me-3 text-primary"></i>
                                            <div>
                                                <h5 class="mb-1">{{ $file->original_name }}</h5>
                                                <div class="text-muted small">
                                                    <span class="me-3"><i class="bi bi-hdd me-1"></i>{{ number_format($file->size / 1024, 2) }} KB</span>
                                                    <span class="me-3"><i class="bi bi-code-slash me-1"></i>Versión {{ $file->version }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('files.write', $file->id) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-pencil me-1"></i>Editar
                                            </a>
                                            <a href="{{ Storage::url($file->path) }}" class="btn btn-primary btn-sm" target="_blank">
                                                <i class="bi bi-download me-1"></i>Descargar
                                            </a>
                                        </div>
                                    </div>
                                    <div class="border-top pt-2">
                                        <div class="row g-2">
                                            <div class="col-auto">
                                                <div class="text-muted small">
                                                    <i class="bi bi-calendar-plus me-1"></i>Subido el {{ $file->created_at->format('d/m/Y H:i') }}
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <div class="text-muted small">
                                                    <i class="bi bi-calendar-check me-1"></i>Actualizado el {{ $file->updated_at->format('d/m/Y H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="preview-section mb-3">
                    <h5>Descripción</h5>
                    <p class="mb-0">{{ $file->description ?? 'No se proporcionó descripción.' }}</p>
                </div>

                <div class="preview-section">
                    <h5>Observaciones</h5>
                    <p class="mb-0">{{ $file->observations ?? 'No se proporcionó observaciones.' }}</p>
                </div>
                    
                <div class="preview-section mb-3">
                    <h5>Vista Previa del Documento</h5>
                    @if ($file->isWord())
                        @if ($wordPreviewError)
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>{{ $wordPreviewError }}
                            </div>
                        @endif
                        @if ($wordPreview)
                            <div class="document-preview">
                                {!! $wordPreview !!}
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('files.write', $file->id) }}" class="btn btn-primary">
                                    <i class="bi bi-pencil me-2"></i>Editar Contenido
                                </a>
                            </div>
                        @endif
                    @elseif ($file->isPdf())
                        <div class="document-preview">
                            <iframe src="{{ Storage::url($file->path) }}" width="100%" height="700px" frameborder="0" class="rounded"></iframe>
                        </div>
                    @elseif ($file->isExcel() && $excelPreview)
                        <div class="table-responsive document-preview">
                            <table class="table table-bordered table-hover mb-0">
                                @foreach ($excelPreview as $row)
                                    @if ($loop->first)
                                        <thead>
                                            <tr>
                                                @foreach ($row as $cell)
                                                    <th>{{ $cell }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                    @else
                                        <tr>
                                            @foreach ($row as $cell)
                                                <td>{{ $cell }}</td>
                                            @endforeach
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>Vista previa no disponible para este tipo de archivo
                        </div>
                    @endif
                </div>

                <footer class="mt-4 pt-3 border-top text-center text-muted">
                    <p class="mb-0"><small>Sistema de Gestión de Archivos © {{ date('Y') }}</small></p>
                </footer>
                </div>
            </div>
        </div>

        @if($file->versions->count() > 0)
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Versiones Anteriores</h5>
                    <a href="{{ route('files.history', $file->id) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-clock-history"></i> Ver Historial Completo
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Versión</th>
                                    <th>Nombre del Archivo</th>
                                    <th>Tamaño</th>
                                    <th>Subido</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($file->versions->take(5) as $version)
                                    <tr>
                                        <td>{{ $version->version }}</td>
                                        <td>{{ $version->original_name }}</td>
                                        <td>{{ number_format($version->size / 1024, 2) }} KB</td>
                                        <td>{{ $version->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('files.versions.show', $version->id) }}" class="btn btn-sm btn-info">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ Storage::url($version->path) }}" class="btn btn-sm btn-primary" target="_blank">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($file->versions->count() > 5)
                        <div class="text-center mt-3">
                            <a href="{{ route('files.history', $file->id) }}" class="btn btn-link">Ver Todas las Versiones</a>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>