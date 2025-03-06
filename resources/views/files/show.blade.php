<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>File Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>File Details</h1>
            <div>
                <a href="{{ route('files.index') }}" class="btn btn-secondary">Back to Files</a>
                <a href="{{ route('files.history', $file->id) }}" class="btn btn-info"><i class="bi bi-clock-history"></i> Version History</a>
                @can('edit files')
                <a href="{{ route('files.edit', $file->id) }}" class="btn btn-warning">Edit File</a>
                @endcan
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">{{ $file->original_name }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>File Type:</strong> {{ $file->mime_type }}</p>
                        <p><strong>Size:</strong> {{ number_format($file->size / 1024, 2) }} KB</p>
                        <p><strong>Version:</strong> {{ $file->version }}</p>
                        <p><strong>Uploaded:</strong> {{ $file->created_at->format('Y-m-d H:i') }}</p>
                        <p><strong>Last Updated:</strong> {{ $file->updated_at->format('Y-m-d H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Description:</strong></p>
                        <p>{{ $file->description ?? 'No description provided.' }}</p>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ Storage::url($file->path) }}" class="btn btn-primary" target="_blank">Download File</a>
                    
                    @if($file->isPdf())
                        <div class="mt-3">
                            <h5>PDF Preview:</h5>
                            <div class="ratio ratio-16x9" style="max-height: 800px;">
                                <embed src="{{ Storage::url($file->path) }}" type="application/pdf" width="100%" height="800px" />
                            </div>
                        </div>
                    @elseif($file->isExcel())
                        <div class="mt-3">
                            <h5>Excel File Preview:</h5>
                            <div class="alert alert-info">
                                <p class="mb-0">This is an Excel file. You can:</p>
                                <ul class="mb-0">
                                    <li>Download and open it with Microsoft Excel or compatible software</li>
                                    <li>Use the preview button below to view it in Microsoft Office Online (if available)</li>
                                </ul>
                            </div>
                            <a href="https://view.officeapps.live.com/op/view.aspx?src={{ urlencode(url(Storage::url($file->path))) }}" class="btn btn-success mt-2" target="_blank">
                                Preview in Microsoft Office Online
                            </a>
                            
                            @if($excelPreview)
                                <div class="mt-4">
                                    <h5>Excel Content Preview:</h5>
                                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                                        <table class="table table-bordered table-striped table-hover">
                                            <tbody>
                                                @foreach($excelPreview as $row)
                                                    <tr>
                                                        @foreach($row as $cell)
                                                            <td>{{ $cell }}</td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @elseif($file->isWord())
                        <div class="mt-3">
                            <h5>Word Document Preview:</h5>
                            <div class="alert alert-info">
                                <p class="mb-0">This is a Word document. You can:</p>
                                <ul class="mb-0">
                                    <li>Download and open it with Microsoft Word or compatible software</li>
                                    <li>Use the preview button below to view it in Microsoft Office Online (if available)</li>
                                </ul>
                            </div>
                            <a href="https://view.officeapps.live.com/op/view.aspx?src={{ urlencode(url(Storage::url($file->path))) }}" class="btn btn-success mt-2" target="_blank">
                                Preview in Microsoft Office Online
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if($file->versions->count() > 0)
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Previous Versions</h5>
                    <a href="{{ route('files.history', $file->id) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-clock-history"></i> View Full History
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Version</th>
                                    <th>File Name</th>
                                    <th>Size</th>
                                    <th>Uploaded</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($file->versions->take(5) as $version)
                                    <tr>
                                        <td>{{ $version->version }}</td>
                                        <td>{{ $version->original_name }}</td>
                                        <td>{{ number_format($version->size / 1024, 2) }} KB</td>
                                        <td>{{ $version->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('files.versions.show', $version->id) }}" class="btn btn-sm btn-info">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ Storage::url($version->path) }}" class="btn btn-sm btn-primary" target="_blank">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($file->versions->count() > 5)
                        <div class="text-center mt-3">
                            <a href="{{ route('files.history', $file->id) }}" class="btn btn-link">View All Versions</a>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>