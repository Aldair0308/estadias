<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detalles de la Plantilla</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1>Detalles de la Plantilla</h1>
                <h5 class="text-muted">{{ $template->original_name }}</h5>
            </div>
            <div>
                <a href="{{ route('templates.index') }}" class="btn btn-secondary">Volver a Plantillas</a>
                <a href="{{ route('templates.history', $template->id) }}" class="btn btn-info"><i class="bi bi-clock-history"></i> Historial de Versiones</a>
                @can('edit templates')
                <a href="{{ route('templates.edit', $template->id) }}" class="btn btn-warning">Editar Plantilla</a>
                @endcan
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Información de la Plantilla</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Tipo de Archivo:</strong> {{ $template->mime_type }}</p>
                        <p><strong>Tamaño:</strong> {{ number_format($template->size / 1024, 2) }} KB</p>
                        <p><strong>Versión:</strong> {{ $template->version }}</p>
                        <p><strong>Subido:</strong> {{ $template->created_at->format('Y-m-d H:i') }}</p>
                        <p><strong>Última Actualización:</strong> {{ $template->updated_at->format('Y-m-d H:i') }}</p>
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
                                <div class="mt-3">
                                    <a href="{{ route('templates.write', $template->id) }}" class="btn btn-success">
                                        <i class="bi bi-pencil-square"></i> Editar Documento
                                    </a>
                                </div>
                            @elseif(session('error'))
                                <div class="mt-4">
                                    <div class="alert alert-danger">
                                        {{ session('error') }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if($template->versions->count() > 0)
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Versiones Anteriores</h5>
                    <a href="{{ route('templates.history', $template->id) }}" class="btn btn-sm btn-outline-primary">
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
                                @foreach($template->versions->take(5) as $version)
                                    <tr>
                                        <td>{{ $version->version }}</td>
                                        <td>{{ $version->original_name }}</td>
                                        <td>{{ number_format($version->size / 1024, 2) }} KB</td>
                                        <td>{{ $version->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('templates.versions.show', $version->id) }}" class="btn btn-sm btn-info">
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
                    @if($template->versions->count() > 5)
                        <div class="text-center mt-3">
                            <a href="{{ route('templates.history', $template->id) }}" class="btn btn-link">Ver Todas las Versiones</a>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>