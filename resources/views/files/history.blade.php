<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Historial de Versiones del Archivo</title>
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
            --bg-color: #f8f9fa;
            --text-color: #212529;
            --border-color: #e9ecef;
            --card-bg: #ffffff;
            --header-gradient-start: var(--primary-color);
            --header-gradient-end: var(--secondary-color);
            --preview-bg: #ffffff;
            --preview-text: #212529;
            --table-text: #212529;
            --table-bg: #ffffff;
        }

        [data-theme="dark"] {
            --bg-color: #121212;
            --text-color: #e9ecef;
            --border-color: #2CA14D;
            --card-bg: #1E1E1E;
            --header-gradient-start: #121212;
            --header-gradient-end: #1E1E1E;
            --table-text: #e9ecef;
            --table-bg: #1E1E1E;
            --preview-bg: #ffffff;
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
            box-shadow: 0 2px 4px rgba(13, 110, 253, 0.2);
        }
        
        .btn-primary:hover {
            background: #0056b3;
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
        
        .version-timeline {
            position: relative;
            padding-left: 30px;
            color: var(--text-color);
        }
        
        .version-timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            height: 100%;
            width: 2px;
            background: var(--border-color);
        }
        
        .version-item {
            position: relative;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
            background: var(--card-bg);
            padding: 1.5rem;
            border-radius: 8px;
            transition: transform 0.2s ease-in-out;
        }
        
        .version-item:hover {
            transform: translateY(-2px);
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
            background: var(--card-bg);
            border: 2px solid var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
            color: var(--text-color);
        }
        
        .current-version .version-marker {
            background: var(--primary-color);
            color: white;
        }
        
        .version-date {
            color: var(--text-color);
            opacity: 0.8;
            font-size: 0.875rem;
        }
        
        .version-actions {
            margin-top: 10px;
            gap: 0.5rem;
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
                    <h1 class="mb-0">Historial de Versiones</h1>
                    <p class="text-light mb-0">{{ $parentFile->original_name }}</p>
                </div>
                <div class="btn-group">
                    <button onclick="toggleTheme()" class="btn btn-light me-2">
                        <i class="bi bi-moon-stars"></i>
                    </button>
                    <a href="{{ route('files.show', $parentFile->id) }}" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i>Volver</a>
                    <a href="{{ route('files.index') }}" class="btn btn-light"><i class="bi bi-house me-2"></i>Inicio</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-4">

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
                <form action="{{ route('files.compare') }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-md-5">
                        <label for="version1" class="form-label">Primera Versión</label>
                        <select name="version1" id="version1" class="form-select" required>
                            @foreach($versions as $version)
                                <option value="{{ $version->id }}" {{ $version->id == $file->id ? 'selected' : '' }}>
                                    Versión {{ $version->version }} ({{ $version->created_at->format('Y-m-d H:i') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label for="version2" class="form-label">Segunda Versión</label>
                        <select name="version2" id="version2" class="form-select" required>
                            @foreach($versions as $version)
                                <option value="{{ $version->id }}" {{ ($version->id != $file->id && $loop->first) ? 'selected' : '' }}>
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

        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Todas las Versiones</h5>
            </div>
            <div class="card-body">
                <div class="version-timeline">
                    @foreach($versions as $version)
                        <div class="version-item {{ $version->id == $file->id ? 'current-version' : '' }}">
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
                                        <a href="{{ route('files.versions.show', $version->id) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i> Ver Detalles de esta versión
                                        </a>
                                        <a href="{{ route('files.show', $version->id) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i> Ver Esta Versión
                                        </a>
                                        <a href="{{ Storage::url($version->path) }}" class="btn btn-sm btn-primary" target="_blank">
                                            <i class="bi bi-download"></i> Descargar
                                        </a>
                                        @if($version->id != $file->id && $version->id != $parentFile->id)
                                            @can('edit files')
                                                <form action="{{ route('files.versions.restore', $version->id) }}" method="POST">
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