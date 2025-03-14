<div class="word-preview">
    @if($error)
        <div class="alert alert-warning mt-3">
            <h6>Aviso:</h6>
            <p>{{ $error }}</p>
        </div>
    @endif

    @if($preview)
        <div class="document-preview">
            {!! $preview !!}
        </div>
        <div class="mt-3">
            <a href="{{ route('files.write', $file->id) }}" class="btn btn-success">
                <i class="bi bi-pencil-square"></i> Editar Documento
            </a>
        </div>
    @else
        <div class="alert alert-info">
            <p class="mb-0">Este es un documento Word. Puedes descargarlo y abrirlo con Microsoft Word o software compatible.</p>
        </div>
    @endif
</div>