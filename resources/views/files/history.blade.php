<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>File Version History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .version-timeline {
            position: relative;
            padding-left: 30px;
        }
        .version-timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            height: 100%;
            width: 2px;
            background: #dee2e6;
        }
        .version-item {
            position: relative;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .version-item:last-child {
            border-bottom: none;
        }
        .version-marker {
            position: absolute;
            left: -30px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #fff;
            border: 2px solid #0d6efd;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
        }
        .current-version .version-marker {
            background: #0d6efd;
            color: white;
        }
        .version-date {
            color: #6c757d;
            font-size: 0.875rem;
        }
        .version-actions {
            margin-top: 10px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1>Version History</h1>
                <h5 class="text-muted">{{ $parentFile->original_name }}</h5>
            </div>
            <div>
                <a href="{{ route('files.show', $parentFile->id) }}" class="btn btn-secondary">Back to File</a>
                <a href="{{ route('files.index') }}" class="btn btn-outline-secondary">All Files</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Compare Versions</h5>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('files.compare') }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-md-5">
                        <label for="version1" class="form-label">First Version</label>
                        <select name="version1" id="version1" class="form-select" required>
                            @foreach($versions as $version)
                                <option value="{{ $version->id }}" {{ $version->id == $file->id ? 'selected' : '' }}>
                                    Version {{ $version->version }} ({{ $version->created_at->format('Y-m-d H:i') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label for="version2" class="form-label">Second Version</label>
                        <select name="version2" id="version2" class="form-select" required>
                            @foreach($versions as $version)
                                <option value="{{ $version->id }}" {{ ($version->id != $file->id && $loop->first) ? 'selected' : '' }}>
                                    Version {{ $version->version }} ({{ $version->created_at->format('Y-m-d H:i') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Compare</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">All Versions</h5>
            </div>
            <div class="card-body">
                <div class="version-timeline">
                    @foreach($versions as $version)
                        <div class="version-item {{ $version->id == $file->id ? 'current-version' : '' }}">
                            <div class="version-marker">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <h5>
                                        Version {{ $version->version }}
                                        @if($version->id == $parentFile->id)
                                            <span class="badge bg-primary">Original</span>
                                        @endif
                                        @if($version->id == $file->id)
                                            <span class="badge bg-success">Current</span>
                                        @endif
                                    </h5>
                                    <p class="version-date">
                                        <i class="bi bi-calendar"></i> {{ $version->created_at->format('F j, Y, g:i a') }}
                                    </p>
                                    <p>
                                        <strong>Size:</strong> {{ number_format($version->size / 1024, 2) }} KB
                                        <strong class="ms-3">Type:</strong> {{ $version->mime_type }}
                                    </p>
                                    <p>{{ $version->description ?? 'No description provided.' }}</p>
                                </div>
                                <div class="col-md-4">
                                    <div class="version-actions d-flex flex-column gap-2">
                                        <a href="{{ route('files.versions.show', $version->id) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i> View This Version
                                        </a>
                                        <a href="{{ Storage::url($version->path) }}" class="btn btn-sm btn-primary" target="_blank">
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                        @if($version->id != $file->id && $version->id != $parentFile->id)
                                            @can('edit files')
                                                <form action="{{ route('files.versions.restore', $version->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-warning w-100" 
                                                        onclick="return confirm('Are you sure you want to restore this version?')">
                                                        <i class="bi bi-arrow-counterclockwise"></i> Restore This Version
                                                    </button>
                                                </form>
                                            @endcan
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>