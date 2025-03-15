<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Document Review</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Document Review</h1>
            <div>
                <a href="{{ route('files.index') }}" class="btn btn-secondary">Back to Files</a>
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
                                        <a href="{{ route('files.show', $file->id) }}" class="btn btn-sm btn-info">View Details</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Observations Form -->
                <div class="mt-4">
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
                </div>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>