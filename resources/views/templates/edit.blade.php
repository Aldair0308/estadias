<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Plantilla</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Editar Plantilla</h1>
            <div>
                <a href="{{ route('templates.index') }}" class="btn btn-secondary">Volver a Plantillas</a>
                <a href="{{ route('templates.show', $template->id) }}" class="btn btn-info">Ver Detalles</a>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form action="{{ route('templates.update', $template->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Nombre Original</label>
                        <input type="text" class="form-control" value="{{ $template->original_name }}" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="file" class="form-label">Reemplazar Archivo (opcional)</label>
                        <input type="file" class="form-control" id="file" name="file">
                        <div class="form-text">Formatos permitidos: PDF, Excel, Word. Tamaño máximo: 10MB.</div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ $template->description }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observations" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observations" name="observations" rows="3">{{ $template->observations }}</textarea>
                        <div class="form-text">Notas sobre los cambios realizados o información adicional.</div>
                    </div>
                    <button type="submit" class="btn btn-primary">Actualizar Plantilla</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>