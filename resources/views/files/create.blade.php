<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Upload New File</title>
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
            <h1>Upload New File</h1>
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
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">File Upload Form</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('files.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="file" class="form-label">Select File (PDF, Excel, or Word)</label>
                        <input type="file" class="form-control" id="file" name="file" accept=".pdf,.xls,.xlsx,.doc,.docx,application/pdf,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" required>
                        <div class="form-text">Maximum file size: 10MB</div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter a description for this file">{{ old('description') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Upload File</button>
                </form>

                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="file-type-info pdf-info">
                            <h5><i class="bi bi-file-earmark-pdf"></i> PDF Files</h5>
                            <p>PDF files will be stored securely and can be previewed directly in the browser.</p>
                            <p>Recommended for documents that need to maintain formatting.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="file-type-info excel-info">
                            <h5><i class="bi bi-file-earmark-excel"></i> Excel Files</h5>
                            <p>Excel files (.xls, .xlsx) will be stored securely and can be previewed using Microsoft Office Online.</p>
                            <p>Recommended for data that needs to be analyzed or manipulated.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="file-type-info word-info">
                            <h5><i class="bi bi-file-earmark-word"></i> Word Files</h5>
                            <p>Word files (.doc, .docx) will be stored securely and can be edited using Microsoft Office Online.</p>
                            <p>Recommended for text documents that require editing.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simple client-side validation
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
                    alert('Please select only PDF, Excel, or Word files.');
                }
                
                if (file.size > 10 * 1024 * 1024) { // 10MB in bytes
                    e.preventDefault();
                    alert('File size must be less than 10MB.');
                }
            }
        });
    </script>
</body>
</html>