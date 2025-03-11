<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Versión de Plantilla</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Versión de Plantilla</h1>
            <div>
                <a href="{{ route('templates.index') }}" class="btn btn-secondary">Volver a Plantillas</a>
                <a href="{{ route('templates.history', $template->parent_id) }}" class="btn btn-info"><i class="bi bi-clock-history"></i> Historial de Versiones</a>
                @can('edit templates')
                <form action="{{ route('templates.versions.restore', $template->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que deseas restaurar esta versión?');">
                    @csrf
                    <button type="submit" class="btn btn-success"><i class="bi bi-arrow-counterclockwise"></i> Restaurar Versión</button>
                </form>
                @endcan
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">{{ $template->original_name }} (Versión {{ $template->version }})</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Tipo de Archivo:</strong> {{ $template->mime_type }}</p>
                        <p><strong>Tamaño:</strong> {{ number_format($template->size / 1024, 2) }} KB</p>
                        <p><strong>Versión:</strong> {{ $template->version }}</p>
                        <p><strong>Subido:</strong> {{ $template->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Descripción:</strong></p>
                        <p>{{ $template->description ?? 'No se proporcionó descripción.' }}</p>
                        <p><strong>Observaciones:</strong></p>
                        <p>{{ $template->observations ?? 'No se proporcionó observaciones.' }}</p>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ Storage::url($template->path) }}" class="btn btn-primary" target="_blank">Descargar Plantilla</a>
                    
                    @if($template->isPdf())
                        <div class="mt-3">
                            <h5>Vista Previa PDF:</h5>
                            <div class="ratio ratio-16x9" style="max-height: 800px;">
                                <embed src="{{ Storage::url($template->path) }}" type="application/pdf" width="100%" height="800px" />
                            </div>
                        </div>
                    @elseif($template->isExcel())
                        <div class="mt-3">
                            <h5>Vista Previa de Excel:</h5>
                            <div class="alert alert-info">
                                <p class="mb-0">Este es un archivo Excel. Puedes descargarlo y abrirlo con Microsoft Excel o software compatible.</p>
                            </div>
                            
                            @if($excelPreview)
                                <div class="mt-4">
                                    <h5>Vista Previa del Contenido Excel:</h5>
                                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                                        <table class="table table-bordered table-striped table-hover">
                                            <tbody>
                                                @foreach($excelPreview as $row)
                                                    <tr>
                                                        @foreach($row as $cell)
                                                            <td>{{ $cell }}</td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @elseif($template->isWord())
                        <div class="mt-3">
                            <h5>Vista Previa de Documento Word:</h5>
                            <div class="alert alert-info">
                                <p class="mb-0">Este es un documento Word. Puedes descargarlo y abrirlo con Microsoft Word o software compatible.</p>
                            </div>
                            @if($wordPreview)
                                <div class="mt-4">
                                    <h5>Vista Previa del Contenido Word:</h5>
                                    <div class="document-preview" style="max-height: 500px; overflow-y: auto; padding: 20px; background-color: white; border: 1px solid #dee2e6; border-radius: 4px;">
                                        {!! $wordPreview !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>