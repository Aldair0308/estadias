<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Archivo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Editar Archivo: {{ $file->original_name }}</h1>
            <a href="{{ route('files.index') }}" class="btn btn-secondary">Volver a Archivos</a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form action="{{ route('files.update', $file->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="file" class="form-label">Subir Nueva Versión (PDF, Excel o Word)</label>
                        <input type="file" class="form-control" id="file" name="file">
                        <div class="form-text">Tamaño máximo de archivo: 10MB</div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción (Opcional)</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $file->description) }}</textarea>
                    </div>
                    @can('edit files')
                    <div class="mb-3">
                        <label for="observations" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observations" name="observations" rows="4">{{ old('observations', $file->observations) }}</textarea>
                        <div class="form-text">Agregue observaciones o comentarios sobre este archivo.</div>
                    </div>
                    @endcan
                    <button type="submit" class="btn btn-primary">Actualizar Archivo</button>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Versiones del Archivo</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Versión</th>
                            <th>Tamaño</th>
                            <th>Última Actualización</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $file->version }}</td>
                            <td>{{ number_format($file->size / 1024, 2) }} KB</td>
                            <td>{{ $file->updated_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <a href="{{ route('files.show', $file->id) }}" class="btn btn-sm btn-info">Ver</a>
                            </td>
                        </tr>
                        @foreach($file->versions()->orderBy('version', 'desc')->get() as $version)
                            <tr>
                                <td>{{ $version->version }}</td>
                                <td>{{ number_format($version->size / 1024, 2) }} KB</td>
                                <td>{{ $version->updated_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('files.show', $version->id) }}" class="btn btn-sm btn-info">Ver</a>
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