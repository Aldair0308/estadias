<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Compare File Versions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .version-preview {
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            padding: 1rem;
            height: 100%;
            min-height: 500px;
        }
        .version-info {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 0.25rem;
            margin-bottom: 1rem;
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
                <h1>Compare Versions</h1>
                <h5 class="text-muted">{{ $parentFile->original_name }}</h5>
            </div>
            <div>
                <a href="{{ route('files.history', $parentFile->id) }}" class="btn btn-secondary">Back to History</a>
                <a href="{{ route('files.show', $parentFile->id) }}" class="btn btn-outline-secondary">View File</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="version-info">
                    <h4>Version {{ $version1->version }}</h4>
                    <p class="mb-1">
                        <i class="bi bi-calendar"></i> {{ $version1->created_at->format('F j, Y, g:i a') }}
                    </p>
                    <p class="mb-1">
                        <strong>Size:</strong> {{ number_format($version1->size / 1024, 2) }} KB
                        <strong class="ms-3">Type:</strong> {{ $version1->mime_type }}
                    </p>
                    <p class="mb-0">{{ $version1->description ?? 'No description provided.' }}</p>
                </div>
                <div class="version-preview">
                    @if($version1->isPdf())
                        <embed src="{{ Storage::url($version1->path) }}" type="application/pdf" class="pdf-preview">
                    @elseif($version1->isExcel())
                        <div class="text-center">
                            <p class="mb-3">Excel file preview is not available in comparison view.</p>
                            <a href="{{ Storage::url($version1->path) }}" class="btn btn-primary" target="_blank">
                                <i class="bi bi-download"></i> Download Version {{ $version1->version }}
                            </a>
                            <a href="https://view.officeapps.live.com/op/view.aspx?src={{ urlencode(url(Storage::url($version1->path))) }}" 
                               class="btn btn-success mt-2" target="_blank">
                                <i class="bi bi-eye"></i> View in Microsoft Office Online
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-md-6">
                <div class="version-info">
                    <h4>Version {{ $version2->version }}</h4>
                    <p class="mb-1">
                        <i class="bi bi-calendar"></i> {{ $version2->created_at->format('F j, Y, g:i a') }}
                    </p>
                    <p class="mb-1">
                        <strong>Size:</strong> {{ number_format($version2->size / 1024, 2) }} KB
                        <strong class="ms-3">Type:</strong> {{ $version2->mime_type }}
                    </p>
                    <p class="mb-0">{{ $version2->description ?? 'No description provided.' }}</p>
                </div>
                <div class="version-preview">
                    @if($version2->isPdf())
                        <embed src="{{ Storage::url($version2->path) }}" type="application/pdf" class="pdf-preview">
                    @elseif($version2->isExcel())
                        <div class="text-center">
                            <p class="mb-3">Excel file preview is not available in comparison view.</p>
                            <a href="{{ Storage::url($version2->path) }}" class="btn btn-primary" target="_blank">
                                <i class="bi bi-download"></i> Download Version {{ $version2->version }}
                            </a>
                            <a href="https://view.officeapps.live.com/op/view.aspx?src={{ urlencode(url(Storage::url($version2->path))) }}" 
                               class="btn btn-success mt-2" target="_blank">
                                <i class="bi bi-eye"></i> View in Microsoft Office Online
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>