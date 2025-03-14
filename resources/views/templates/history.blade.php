<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Historial de Versiones de Plantilla</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .version-timeline {
            position: relative;
            padding-left: 30px;
        }
        .version-timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            height: 100%;
            width: 2px;
            background: #dee2e6;
        }
        .version-item {
            position: relative;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .version-item:last-child {
            border-bottom: none;
        }
        .version-marker {
            position: absolute;
            left: -30px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #fff;
            border: 2px solid #0d6efd;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
        }
        .current-version .version-marker {
            background: #0d6efd;
            color: white;
        }
        .version-date {
            color: #6c757d;
            font-size: 0.875rem;
        }
        .version-actions {
            margin-top: 10px;
        }
    </style>
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
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Comparar Versiones</h5>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('templates.compare') }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-md-5">
                        <label for="version1" class="form-label">Primera Versión</label>
                        <select name="version1" id="version1" class="form-select" required>
                            @foreach($versions as $version)
                                <option value="{{ $version->id }}" {{ $version->id == $template->id ? 'selected' : '' }}>
                                    Versión {{ $version->version }} ({{ $version->created_at->format('Y-m-d H:i') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label for="version2" class="form-label">Segunda Versión</label>
                        <select name="version2" id="version2" class="form-select" required>
                            @foreach($versions as $version)
                                <option value="{{ $version->id }}" {{ ($version->id != $template->id && $loop->first) ? 'selected' : '' }}>
                                    Versión {{ $version->version }} ({{ $version->created_at->format('Y-m-d H:i') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Comparar</button>
                    </div>
                </form>
            </div>
        </div>

                <div class="version-timeline">
                    @foreach($versions as $version)
                        <div class="version-item {{ $version->id == $template->id ? 'current-version' : '' }}">
                            <div class="version-marker">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <h5>
                                        Versión {{ $version->version }}
                                        @if($version->id == $versions->min('id'))
                                            <span class="badge bg-primary">Inicial</span>
                                        @endif
                                        @if($version->id == $versions->max('id'))
                                            <span class="badge bg-success">Actual</span>
                                        @endif
                                    </h5>
                                    <p class="version-date">
                                        <i class="bi bi-calendar"></i> {{ $version->created_at->format('F j, Y, g:i a') }}
                                    </p>
                                    <p>
                                        <strong>Tamaño:</strong> {{ number_format($version->size / 1024, 2) }} KB
                                        <strong class="ms-3">Tipo:</strong> {{ $version->mime_type }}
                                    </p>
                                    <p>{{ $version->description ?? 'No se proporcionó descripción.' }}</p>
                                </div>
                                <div class="col-md-4">
                                    <div class="version-actions d-flex flex-column gap-2">
                                        <a href="{{ route('templates.versions.show', $version->id) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i> Ver Detalles de esta versión
                                        </a>
                                        <a href="{{ route('templates.show', $version->id) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i> Ver Esta Versión
                                        </a>
                                        <a href="{{ Storage::url($version->path) }}" class="btn btn-sm btn-primary" target="_blank">
                                            <i class="bi bi-download"></i> Descargar
                                        </a>
                                        @if($version->id != $template->id && $version->id != $parentTemplate->id)
                                            @can('edit templates')
                                                <form action="{{ route('templates.versions.restore', $version->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-warning w-100" 
                                                        onclick="return confirm('¿Está seguro que desea restaurar esta versión?')">
                                                        <i class="bi bi-arrow-counterclockwise"></i> Restaurar Esta Versión
                                                    </button>
                                                </form>
                                            @endcan
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>