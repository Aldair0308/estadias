<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Revisión de Documentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .modal-fullscreen {
            padding: 0 !important;
        }
        .modal-fullscreen .modal-content {
            height: 100vh;
            border: 0;
            border-radius: 0;
        }
        .preview-content {
            height: calc(100vh - 180px);
            overflow-y: auto;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Revisión de Documentos</h1>
            <div>
                <a href="{{ route('files.index') }}" class="btn btn-secondary">Volver a Archivos</a>
            </div>
        </div>

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
                                            <td>{{ $file->original_name }}</td>
                                            <td>{{ $file->responsible ? $file->responsible->name : 'Sin Responsable' }}</td>
                                            <td>{{ $file->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#previewModal{{ $file->id }}">
                                                    Vista Previa
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
</body>
</html>