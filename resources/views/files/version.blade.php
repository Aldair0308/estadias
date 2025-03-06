<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>File Version</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .version-sidebar {
            border-right: 1px solid #dee2e6;
            height: 100%;
        }
        .version-list {
            list-style: none;
            padding-left: 0;
        }
        .version-list-item {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #eee;
            position: relative;
        }
        .version-list-item.active {
            background-color: #f8f9fa;
            border-left: 3px solid #0d6efd;
        }
        .version-list-item:hover {
            background-color: #f8f9fa;
        }
        .version-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }
        .version-current {
            background-color: #198754;
        }
        .version-other {
            background-color: #6c757d;
        }
        .version-original {
            background-color: #0d6efd;
        }
        .pdf-preview {
            width: 100%;
            height: 800px;
            border: none;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1>Version {{ $file->version }}</h1>
                <h5 class="text-muted">{{ $parentFile->original_name }}</h5>
            </div>
            <div>
                <a href="{{ route('files.history', $parentFile->id) }}" class="btn btn-secondary">Back to History</a>
                <a href="{{ route('files.show', $parentFile->id) }}" class="btn btn-outline-secondary">Current Version</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="card version-sidebar">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">All Versions</h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="version-list">
                            @foreach($versions as $version)
                                <li class="version-list-item {{ $version->id == $file->id ? 'active' : '' }}">
                                    <a href="{{ route('files.versions.show', $version->id) }}" class="text-decoration-none text-dark d-block">
                                        <div>
                                            <span class="version-indicator {{ $version->id == $parentFile->id ? 'version-original' : ($version->id == $file->id ? 'version-current' : 'version-other') }}"></span>
                                            <strong>Version {{ $version->version }}</strong>
                                            @if($version->id == $parentFile->id)
                                                <span class="badge bg-primary">Original</span>
                                            @endif
                                        </div>
                                        <small class="text-muted d-block mt-1">{{ $version->created_at->format('Y-m-d H:i') }}</small>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ Storage::url($file->path) }}" class="btn btn-primary w-100 mb-2" target="_blank">
                        <i class="bi bi-download"></i> Download This Version
                    </a>
                    @if($file->id != $parentFile->id)
                        @can('edit files')
                            <form action="{{ route('files.versions.restore', $file->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100" 
                                    onclick="return confirm('Are you sure you want to restore this version?')">
                                    <i class="bi bi-arrow-counterclockwise"></i> Restore This Version
                                </button>
                            </form>
                        @endcan
                    @endif
                </div>
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">File Preview</h5>
                            <div>
                                <span class="badge bg-secondary">{{ $file->mime_type }}</span>
                                <span class="badge bg-info">{{ number_format($file->size / 1024, 2) }} KB</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6>Description:</h6>
                            <p>{{ $file->description ?? 'No description provided.' }}</p>
                        </div>
                        
                        @if($file->isPdf())
                            <div class="mt-3">
                                <embed src="{{ Storage::url($file->path) }}" type="application/pdf" class="pdf-preview">
                            </div>
                        @elseif($file->isExcel())
                            <div class="mt-3 text-center">
                                <div class="alert alert-info">
                                    <p class="mb-0">This is an Excel file. You can:</p>
                                    <ul class="mb-0">
                                        <li>Download and open it with Microsoft Excel or compatible software</li>
                                        <li>Use the preview button below to view it in Microsoft Office Online (if available)</li>
                                    </ul>
                                </div>
                                <a href="https://view.officeapps.live.com/op/view.aspx?src={{ urlencode(url(Storage::url($file->path))) }}" 
                                   class="btn btn-success mt-2" target="_blank">
                                    <i class="bi bi-eye"></i> Preview in Microsoft Office Online
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>