<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Editor de Documento Word</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #35B550;
            --secondary-color: #2CA14D;
            --success-color: #35B550;
            --info-color: #2CA14D;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --light-color: #f8f9fa;
            --dark-color: #121212;
            --bg-color: #FFFFFF;
            --text-color: #2CA14D;
            --border-color: #e9ecef;
            --card-bg: #FFFFFF;
            --header-gradient-start: #35B550;
            --header-gradient-end: #2CA14D;
            --preview-bg: #FFFFFF;
            --preview-text: #2CA14D;
            --table-text: #2CA14D;
            --table-bg: #FFFFFF;
        }

        [data-theme="dark"] {
            --bg-color: #121212;
            --text-color: #FFFFFF;
            --border-color: #2CA14D;
            --card-bg: #1E1E1E;
            --header-gradient-start: #35B550;
            --header-gradient-end: #2CA14D;
            --table-text: #FFFFFF;
            --table-bg: #1E1E1E;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
        }

        .card {
            background-color: var(--card-bg);
            border-color: var(--border-color);
        }

        .card-header {
            background: linear-gradient(to right, var(--header-gradient-start), var(--header-gradient-end));
            color: #FFFFFF;
        }

        .btn-secondary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .btn-info {
            background-color: var(--info-color);
            border-color: var(--info-color);
            color: #FFFFFF;
        }

        .alert-info {
            background-color: var(--info-color);
            border-color: var(--info-color);
            color: #FFFFFF;
        }

        .alert-warning {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Editor de Documento Word</h1>
            <div>
                <a href="{{ route('files.show', $file->id) }}" class="btn btn-secondary">Volver a Detalles</a>
                <a href="{{ route('files.history', $file->id) }}" class="btn btn-info"><i class="bi bi-clock-history"></i> Historial de Versiones</a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">{{ $file->original_name }}</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p><strong>Tipo de Archivo:</strong> {{ $file->mime_type }}</p>
                        <p><strong>Tamaño:</strong> {{ number_format($file->size / 1024, 2) }} KB</p>
                        <p><strong>Versión:</strong> {{ $file->version }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Descripción:</strong></p>
                        <p>{{ $file->description ?? 'No se proporcionó descripción.' }}</p>
                    </div>
                </div>

                @if($wordContent)
                    <div class="alert alert-info">
                        <p class="mb-0"><i class="bi bi-info-circle"></i> Los cambios que realice en este documento crearán una nueva versión automáticamente.</p>
                    </div>
                    
                    <div class="mt-4">
                        <x-editor-word :document-content="$wordContent" :document-id="$file->id" />
                    </div>
                @else
                    <div class="alert alert-warning">
                        <p class="mb-0">No se pudo cargar el contenido del documento. Por favor, intente nuevamente o contacte al administrador.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>