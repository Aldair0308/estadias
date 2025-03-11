<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Plantillas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Gestión de Plantillas</h1>
            <a href="{{ route('templates.create') }}" class="btn btn-primary">Subir Nueva Plantilla</a>
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
                            <th>Nombre de la Plantilla</th>
                            <th>Tipo</th>
                            <th>Tamaño</th>
                            <th>Versiones</th>
                            <th>Última Actualización</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($templates as $template)
                            <tr>
                                <td>{{ $template->original_name }}</td>
                                <td>{{ $template->mime_type }}</td>
                                <td>{{ number_format($template->size / 1024, 2) }} KB</td>
                                <td>{{ $template->versions->count() + 1 }}</td>
                                <td>{{ $template->updated_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('templates.show', $template->id) }}" class="btn btn-sm btn-info">Ver</a>
                                        @can('edit templates')
                                        <a href="{{ route('templates.edit', $template->id) }}"
                                            class="btn btn-sm btn-warning">Editar</a>
                                        @endcan
                                        @can('delete templates')
                                        <form action="{{ route('templates.destroy', $template->id) }}" method="POST"
                                            onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta plantilla?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
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