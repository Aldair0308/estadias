@props(['file'])

<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <div class="d-flex flex-column">
                    <h5 class="modal-title" id="previewModalLabel">{{ $file->original_name }}</h5>
                    <small class="text-muted">Responsable: {{ $file->responsible->name }}</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    @if(auth()->user()->hasRole('tutor'))
                        <form action="{{ route('files.mark-reviewed', $file->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn {{ $file->checked ? 'btn-success' : 'btn-outline-success' }}">
                                <i class="bi {{ $file->checked ? 'bi-check-circle-fill' : 'bi-check-circle' }}"></i>
                                {{ $file->checked ? 'Revisado' : 'Marcar como revisado' }}
                            </button>
                        </form>
                    @endif
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
            <div class="modal-body p-4">
                @if($file->isPdf())
                    <div class="ratio ratio-16x9" style="height: calc(100vh - 100px);">
                        <embed src="{{ Storage::url($file->path) }}" type="application/pdf" width="100%" height="100%" />
                    </div>
                @elseif($file->isExcel())
                    <div class="table-responsive" style="max-height: calc(100vh - 100px); overflow-y: auto;">
                        <table class="table table-bordered table-striped table-hover">
                            <tbody>
                                @foreach($excelPreview ?? [] as $row)
                                    <tr>
                                        @foreach($row as $cell)
                                            <td>{{ $cell }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @elseif($file->isWord())
                    <div class="document-preview" style="max-height: calc(100vh - 100px); overflow-y: auto;">
                        <x-word-preview :file="$file" />
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>