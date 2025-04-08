<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Revisión de Documentos</title>
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
            --preview-bg: #ffffff;
            --preview-text: #212529;
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
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .table {
            color: var(--table-text);
            background-color: var(--table-bg);
        }

        .nav-tabs .nav-link {
            color: var(--text-color);
        }

        .nav-tabs .nav-link.active {
            background-color: var(--card-bg);
            border-color: var(--border-color);
            color: var(--primary-color);
        }

        .modal-fullscreen {
            padding: 0 !important;
        }
        .modal-fullscreen .modal-content {
            height: 100vh;
            border: 0;
            border-radius: 0;
            background-color: var(--bg-color);
            color: var(--text-color);
        }
        .preview-content {
            height: calc(100vh - 180px);
            overflow-y: auto;
            background-color: var(--preview-bg);
            color: var(--preview-text);
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="page-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-0">Revisión de Documentos</h1>
                    <p class="text-light mb-0"><i class="bi bi-file-earmark-text me-2"></i>Sistema de Gestión de Archivos</p>
                </div>
                <div class="btn-group">
                    <button onclick="toggleTheme()" class="btn btn-light me-2">
                        <i class="bi bi-moon-stars"></i>
                    </button>
                    <a href="{{ route('files.index') }}" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i>Volver</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-4">

        <div class="card mb-4">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="true">Por Revisar</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="reviewed-tab" data-bs-toggle="tab" data-bs-target="#reviewed" type="button" role="tab" aria-controls="reviewed" aria-selected="false">Revisados</button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                        <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nombre del Archivo</th>
                                <th>Responsable</th>
                                <th>Fecha de Carga</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($files->where('checked', false) as $file)
                                <tr>
                                    <td>
                                        {{ $file->original_name }}
                                        <br>
                                        <small class="text-muted">{{ $file->version_display }}</small>
                                    </td>
                                    <td>{{ $file->responsible ? $file->responsible->name : 'Sin Responsable' }}</td>
                                    <td>{{ $file->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#previewModal{{ $file->id }}">
                                            Vista Previa
                                        </button>
                                    </td>
                                </tr>

                                <!-- Preview Modal for each file -->
                                <div class="modal fade modal-fullscreen" id="previewModal{{ $file->id }}" tabindex="-1" aria-labelledby="previewModalLabel{{ $file->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-fullscreen">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="previewModalLabel{{ $file->id }}">{{ $file->original_name }}</h5>
                                                <div class="ms-auto me-3">
                                                    <form action="{{ route('files.mark-reviewed', $file->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn {{ $file->checked ? 'btn-success' : 'btn-outline-success' }}">
                                                            {{ $file->checked ? 'Revisado' : 'Marcar como Revisado' }}
                                                        </button>
                                                    </form>
                                                </div>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-0">
                                                <div class="preview-content p-3">
                                                    @if($file->isWord())
                                                        {!! $file->getHtmlContent() !!}
                                                    @elseif($file->isPdf())
                                                        <iframe src="{{ Storage::disk('public')->url($file->path) }}" width="100%" height="100%" style="border: none;"></iframe>
                                                    @elseif($file->isExcel())
                                                        <div class="table-responsive">
                                                            <!-- Excel preview content -->
                                                        </div>
                                                    @else
                                                        <p class="text-center">Preview not available for this file type</p>
                                                    @endif
                                                </div>

                                                <!-- Observations Form -->
                                                <div class="mt-4 p-3">
                                                    <h5>Observaciones de la Revisión</h5>
                                                    <form action="{{ route('files.update-observations', $file->id) }}" method="POST" class="mt-3">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="form-group">
                                                            <textarea name="observations" class="form-control" rows="5" placeholder="Ingrese sus observaciones aquí...">{{ $file->observations ?? '' }}</textarea>
                                                        </div>
                                                        <div class="mt-3">
                                                            <button type="submit" class="btn btn-primary">Guardar Observaciones</button>
                                                        </div>
                                                    </form>

                                                    <!-- Display Current Observations -->
                                                    <div class="mt-4">
                                                        <h5>Observaciones Actuales</h5>
                                                        <div class="p-3 bg-white border rounded">
                                                            {!! $file->observations ? nl2br(e($file->observations)) : '<em>Sin observaciones</em>' !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                    </div>
                    <div class="tab-pane fade" id="reviewed" role="tabpanel" aria-labelledby="reviewed-tab">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nombre del Archivo</th>
                                        <th>Responsable</th>
                                        <th>Fecha de Carga</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($files->where('checked', true) as $file)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-file-earmark-text fs-5 me-2 text-primary"></i>
                                                    <div>
                                                        {{ $file->original_name }}
                                                        <br>
                                                        <small class="text-muted">{{ number_format($file->size / 1024, 2) }} KB</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-person me-2"></i>
                                                    {{ $file->responsible ? $file->responsible->name : 'Sin Responsable' }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-calendar me-2"></i>
                                                    {{ $file->created_at->format('d/m/Y H:i') }}
                                                </div>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#previewModal{{ $file->id }}">
                                                    <i class="bi bi-eye me-1"></i>Vista Previa
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Preview Modal for each reviewed file -->
                                        <div class="modal fade modal-fullscreen" id="previewModal{{ $file->id }}" tabindex="-1" aria-labelledby="previewModalLabel{{ $file->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-fullscreen">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="previewModalLabel{{ $file->id }}">{{ $file->original_name }}</h5>
                                                        <div class="ms-auto me-3">
                                                            <form action="{{ route('files.mark-reviewed', $file->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn {{ $file->checked ? 'btn-success' : 'btn-outline-success' }}">
                                                                    {{ $file->checked ? 'Revisado' : 'Marcar como Revisado' }}
                                                                </button>
                                                            </form>
                                                        </div>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-0">
                                                        <div class="preview-content p-3">
                                                            @if($file->isWord())
                                                                {!! $file->getHtmlContent() !!}
                                                            @elseif($file->isPdf())
                                                                <iframe src="{{ Storage::disk('public')->url($file->path) }}" width="100%" height="100%" style="border: none;"></iframe>
                                                            @elseif($file->isExcel())
                                                                <div class="table-responsive">
                                                                    <!-- Excel preview content -->
                                                                </div>
                                                            @else
                                                                <p class="text-center">Preview not available for this file type</p>
                                                            @endif
                                                        </div>

                                                        <!-- Observations Form -->
                                                        <div class="mt-4 p-3">
                                                            <h5>Observaciones de la Revisión</h5>
                                                            <form action="{{ route('files.update-observations', $file->id) }}" method="POST" class="mt-3">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="form-group">
                                                                    <textarea name="observations" class="form-control" rows="5" placeholder="Ingrese sus observaciones aquí...">{{ $file->observations ?? '' }}</textarea>
                                                                </div>
                                                                <div class="mt-3">
                                                                    <button type="submit" class="btn btn-primary">Guardar Observaciones</button>
                                                                </div>
                                                            </form>

                                                            <!-- Display Current Observations -->
                                                            <div class="mt-4">
                                                                <h5>Observaciones Actuales</h5>
                                                                <div class="p-3 bg-white border rounded">
                                                                    {!! $file->observations ? nl2br(e($file->observations)) : '<em>Sin observaciones</em>' !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
</body>
</html>