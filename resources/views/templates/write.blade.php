<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Editor de Plantilla Word</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Editor de Plantilla Word</h1>
            <div>
                <a href="{{ route('templates.show', $template->id) }}" class="btn btn-secondary">Volver a Detalles</a>
                <a href="{{ route('templates.history', $template->id) }}" class="btn btn-info"><i class="bi bi-clock-history"></i> Historial de Versiones</a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">{{ $template->original_name }}</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p><strong>Tipo de Archivo:</strong> {{ $template->mime_type }}</p>
                        <p><strong>Tamaño:</strong> {{ number_format($template->size / 1024, 2) }} KB</p>
                        <p><strong>Versión:</strong> {{ $template->version }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Descripción:</strong></p>
                        <p>{{ $template->description ?? 'No se proporcionó descripción.' }}</p>
                    </div>
                </div>

                @if($wordContent)
                    <div class="alert alert-info">
                        <p class="mb-0"><i class="bi bi-info-circle"></i> Los cambios que realice en esta plantilla crearán una nueva versión automáticamente.</p>
                    </div>
                    
                    <div class="mt-4">
                        <x-editor-word :document-content="$wordContent" :document-id="$template->id" />
                    </div>
                @else
                    <div class="alert alert-warning">
                        <p class="mb-0">No se pudo cargar el contenido de la plantilla. Por favor, intente nuevamente o contacte al administrador.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>