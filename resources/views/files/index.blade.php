<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Archivos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
            --preview-bg: #1E1E1E;
            --preview-text: #FFFFFF;
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
            border: none;
            background-color: var(--card-bg);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s ease-in-out;
        }
        
        .table {
            background: var(--card-bg);
            color: var(--text-color);
            width: 100%;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .table thead th {
            background-color: var(--card-bg);
            border-bottom: 2px solid var(--border-color);
            color: var(--text-color);
        }
        
        .table td, .table th {
            color: var(--text-color);
            border-color: rgba(73, 80, 87, 0.5);
            white-space: nowrap;
            padding: 0.75rem;
            vertical-align: middle;
            background-color: var(--card-bg);
        }

        .table tbody tr {
            background-color: var(--card-bg);
            color: var(--text-color);
        }

        .table tbody tr:hover {
            background-color: var(--border-color);
        }

        @media (max-width: 768px) {
            .btn-group {
                display: flex;
                flex-direction: column;
                gap: 0.25rem;
            }

            .btn-group .btn {
                width: 100%;
            }
        }
        
        .btn-group .btn {
            border-radius: 4px;
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

<body>
    <div class="page-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="mb-0">Gestión de Archivos</h1>
                <div class="btn-group">
                    <button onclick="toggleTheme()" class="btn btn-light me-2">
                        <i class="bi bi-moon-stars"></i>
                    </button>
                    @role('tutor')
                    <a href="{{ route('files.review') }}" class="btn btn-light me-2">Revisar Archivos</a>
                    @endrole
                    <a href="{{ route('files.create') }}" class="btn btn-light">Subir Nuevo Archivo</a>
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

        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nombre del Archivo</th>
                            <th style="width: 100px; max-width: 100px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Tipo</th>
                            <th>Tamaño</th>
                            <th>Versiones</th>
                            <th>Última Actualización</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($files as $file)
                            <tr>
                                <td>{{ $file->original_name }}</td>
                                <td style="width: 100px; max-width: 100px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $file->mime_type }}">{{ $file->mime_type }}</td>
                                <td>{{ number_format($file->size / 1024, 2) }} KB</td>
                                <td>{{ $file->versions->count() + 1 }}</td>
                                <td>{{ $file->updated_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('files.show', $file->id) }}" class="btn btn-sm btn-info">Ver</a>
                                        @can('edit files')
                                        <a href="{{ route('files.edit', $file->id) }}"
                                            class="btn btn-sm btn-warning">Editar</a>
                                        @endcan
                                        @can('delete files')
                                        <form action="{{ route('files.destroy', $file->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('¿Está seguro que desea eliminar este archivo?')">Eliminar</button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>