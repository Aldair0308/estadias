<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Subir Nuevo Archivo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .file-type-info {
            margin-top: 20px;
            padding: 15px;
            border-radius: 5px;
        }
        .pdf-info {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
        }
        .excel-info {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
        }
        .word-info {
            background-color: #cce5ff;
            border: 1px solid #b8daff;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Subir Nuevo Archivo</h1>
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
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Formulario de Carga de Archivos</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('files.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="file" class="form-label">Seleccionar Archivo (PDF, Excel o Word)</label>
                        <input type="file" class="form-control" id="file" name="file" accept=".pdf,.xls,.xlsx,.doc,.docx,application/pdf,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" required>
                        <div class="form-text">Tamaño máximo del archivo: 10MB</div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción (Opcional)</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Ingrese una descripción para este archivo">{{ old('description') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Subir Archivo</button>
                </form>

                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="file-type-info pdf-info">
                            <h5><i class="bi bi-file-earmark-pdf"></i> Archivos PDF</h5>
                            <p>Los archivos PDF se almacenarán de forma segura y se pueden previsualizar directamente en el navegador.</p>
                            <p>Recomendado para documentos que necesitan mantener el formato.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="file-type-info excel-info">
                            <h5><i class="bi bi-file-earmark-excel"></i> Archivos Excel</h5>
                            <p>Los archivos Excel (.xls, .xlsx) se almacenarán de forma segura y se pueden previsualizar usando Microsoft Office Online.</p>
                            <p>Recomendado para datos que necesitan ser analizados o manipulados.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="file-type-info word-info">
                            <h5><i class="bi bi-file-earmark-word"></i> Archivos Word</h5>
                            <p>Los archivos Word (.doc, .docx) se almacenarán de forma segura y se pueden editar usando Microsoft Office Online.</p>
                            <p>Recomendado para documentos de texto que requieren edición.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validación simple del lado del cliente
        document.querySelector('form').addEventListener('submit', function(e) {
            const fileInput = document.getElementById('file');
            const file = fileInput.files[0];
            
            if (file) {
                const fileType = file.type;
                const validTypes = [
                    'application/pdf', 
                    'application/vnd.ms-excel', 
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                ];
                
                if (!validTypes.includes(fileType)) {
                    e.preventDefault();
                    alert('Por favor, seleccione solo archivos PDF, Excel o Word.');
                }
                
                if (file.size > 10 * 1024 * 1024) { // 10MB en bytes
                    e.preventDefault();
                    alert('El tamaño del archivo debe ser menor a 10MB.');
                }
            }
        });
    </script>
</body>
</html>