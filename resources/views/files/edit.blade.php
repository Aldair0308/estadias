<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit File</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Edit File: {{ $file->original_name }}</h1>
            <a href="{{ route('files.index') }}" class="btn btn-secondary">Back to Files</a>
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
            <div class="card-body">
                <form action="{{ route('files.update', $file->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="file" class="form-label">Upload New Version (PDF or Excel only)</label>
                        <input type="file" class="form-control" id="file" name="file" required>
                        <div class="form-text">Maximum file size: 10MB</div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $file->description) }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update File</button>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">File Versions</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Version</th>
                            <th>Size</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $file->version }}</td>
                            <td>{{ number_format($file->size / 1024, 2) }} KB</td>
                            <td>{{ $file->updated_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <a href="{{ route('files.show', $file->id) }}" class="btn btn-sm btn-info">View</a>
                            </td>
                        </tr>
                        @foreach($file->versions()->orderBy('version', 'desc')->get() as $version)
                            <tr>
                                <td>{{ $version->version }}</td>
                                <td>{{ number_format($version->size / 1024, 2) }} KB</td>
                                <td>{{ $version->updated_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('files.show', $version->id) }}" class="btn btn-sm btn-info">View</a>
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