<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Archivos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Gestión de Archivos</h1>
            <a href="{{ route('files.create') }}" class="btn btn-primary">Subir Nuevo Archivo</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nombre del Archivo</th>
                            <th>Tipo</th>
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
                                <td>{{ $file->mime_type }}</td>
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