<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Historial de Versiones de Plantilla</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Historial de Versiones</h1>
            <div>
                <a href="{{ route('templates.index') }}" class="btn btn-secondary">Volver a Plantillas</a>
                <a href="{{ route('templates.show', $parentTemplate->id) }}" class="btn btn-info">Ver Detalles</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">{{ $parentTemplate->original_name }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('templates.compare') }}" method="POST" class="mb-4">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label for="version1" class="form-label">Versión 1</label>
                            <select class="form-select" id="version1" name="version1" required>
                                @foreach($versions as $v)
                                    <option value="{{ $v->id }}" {{ $v->id == $template->id ? 'selected' : '' }}>
                                        Versión {{ $v->version }} ({{ $v->created_at->format('Y-m-d H:i') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label for="version2" class="form-label">Versión 2</label>
                            <select class="form-select" id="version2" name="version2" required>
                                @foreach($versions as $v)
                                    <option value="{{ $v->id }}" {{ ($v->id != $template->id && $loop->first) ? 'selected' : '' }}>
                                        Versión {{ $v->version }} ({{ $v->created_at->format('Y-m-d H:i') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Comparar</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Versión</th>
                                <th>Nombre del Archivo</th>
                                <th>Tamaño</th>
                                <th>Subido</th>
                                <th>Observaciones</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($versions as $version)
                                <tr class="{{ $version->id == $template->id ? 'table-primary' : '' }}">
                                    <td>{{ $version->version }}</td>
                                    <td>{{ $version->original_name }}</td>
                                    <td>{{ number_format($version->size / 1024, 2) }} KB</td>
                                    <td>{{ $version->created_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ Str::limit($version->observations, 50) }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ $version->id == $parentTemplate->id ? route('templates.show', $version->id) : route('templates.versions.show', $version->id) }}" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i> Ver
                                            </a>
                                            <a href="{{ Storage::url($version->path) }}" class="btn btn-sm btn-primary" target="_blank">
                                                <i class="bi bi-download"></i> Descargar
                                            </a>
                                            @if($version->id != $parentTemplate->id && $version->id != $template->id)
                                                @can('edit templates')
                                                <form action="{{ route('templates.versions.restore', $version->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas restaurar esta versión?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        <i class="bi bi-arrow-counterclockwise"></i> Restaurar
                                                    </button>
                                                </form>
                                                @endcan
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>