<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Document Review</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .modal-fullscreen {
            padding: 0 !important;
        }
        .modal-fullscreen .modal-content {
            height: 100vh;
            border: 0;
            border-radius: 0;
        }
        .preview-content {
            height: calc(100vh - 180px);
            overflow-y: auto;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Document Review</h1>
            <div>
                <a href="{{ route('files.index') }}" class="btn btn-secondary">Back to Files</a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Files to Review</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>File Name</th>
                                <th>Responsible</th>
                                <th>Uploaded Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($files as $file)
                                <tr>
                                    <td>{{ $file->original_name }}</td>
                                    <td>{{ $file->responsible ? $file->responsible->name : 'No Responsible' }}</td>
                                    <td>{{ $file->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#previewModal{{ $file->id }}">
                                            Preview Document
                                        </button>
                                    </td>
                                </tr>

                                <!-- Preview Modal for each file -->
                                <div class="modal fade modal-fullscreen" id="previewModal{{ $file->id }}" tabindex="-1" aria-labelledby="previewModalLabel{{ $file->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-fullscreen">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="previewModalLabel{{ $file->id }}">{{ $file->original_name }}</h5>
                                                <div class="ms-auto me-3">
                                                    <form action="{{ route('files.mark-reviewed', $file->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn {{ $file->checked ? 'btn-success' : 'btn-outline-success' }}">
                                                            {{ $file->checked ? 'Reviewed' : 'Mark as Reviewed' }}
                                                        </button>
                                                    </form>
                                                </div>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-0">
                                                <div class="preview-content p-3">
                                                    @if($file->isWord())
                                                        {!! $file->getHtmlContent() !!}
                                                    @elseif($file->isPdf())
                                                        <iframe src="{{ Storage::disk('public')->url($file->path) }}" width="100%" height="100%" style="border: none;"></iframe>
                                                    @elseif($file->isExcel())
                                                        <div class="table-responsive">
                                                            <!-- Excel preview content -->
                                                        </div>
                                                    @else
                                                        <p class="text-center">Preview not available for this file type</p>
                                                    @endif
                                                </div>

                                                <!-- Observations Form -->
                                                <div class="mt-4 p-3">
                                                    <h5>Review Observations</h5>
                                                    <form action="{{ route('files.update-observations', $file->id) }}" method="POST" class="mt-3">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="form-group">
                                                            <textarea name="observations" class="form-control" rows="5" placeholder="Enter your observations here...">{{ $file->observations ?? '' }}</textarea>
                                                        </div>
                                                        <div class="mt-3">
                                                            <button type="submit" class="btn btn-primary">Save Observations</button>
                                                        </div>
                                                    </form>

                                                    <!-- Display Current Observations -->
                                                    <div class="mt-4">
                                                        <h5>Current Observations</h5>
                                                        <div class="p-3 bg-white border rounded">
                                                            {!! $file->observations ? nl2br(e($file->observations)) : '<em>Sin observaciones</em>' !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>