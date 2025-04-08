<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --info-color: #0dcaf0;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --bg-color: #f8f9fa;
            --text-color: #212529;
            --border-color: #e9ecef;
            --card-bg: #ffffff;
            --header-gradient-start: var(--primary-color);
            --header-gradient-end: #0056b3;
            --preview-bg: #ffffff;
            --preview-text: #212529;
            --table-text: #212529;
            --table-bg: #ffffff;
        }

        [data-theme="dark"] {
            --bg-color: #1a1e21;
            --text-color: #e9ecef;
            --border-color: #495057;
            --card-bg: #2b3035;
            --header-gradient-start: #212529;
            --header-gradient-end: #141619;
            --table-text: #e9ecef;
            --table-bg: #2b3035;
            --preview-bg: #ffffff;
            --preview-text: #212529;
        }
        
        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        
        .page-header {
            background: linear-gradient(135deg, var(--header-gradient-start), var(--header-gradient-end));
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .card {
            background-color: var(--card-bg);
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s ease-in-out;
            margin-bottom: 2rem;
        }
        
        .card:hover {
            transform: translateY(-2px);
        }
    </style>
    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        });
    </script>
</head>
<body>
    <div class="page-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="mb-0">{{ __('Dashboard') }}</h1>
                <div class="d-flex gap-2">
                    <button onclick="toggleTheme()" class="btn btn-light">
                        <i class="bi bi-moon-stars"></i>
                    </button>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-box-arrow-right"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <i class="bi bi-check-circle-fill text-success fs-4 me-2"></i>
                            <h5 class="mb-0">{{ __('You\'re logged in!') }}</h5>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('files.index') }}" class="btn btn-primary me-2">
                                <i class="bi bi-file-earmark me-2"></i>Files
                            </a>
                            @role('tutor')
                            <a href="{{ route('students.index') }}" class="btn btn-info me-2">
                                <i class="bi bi-people me-2"></i>Students
                            </a>
                            @endrole
                            @role('admin')
                            <a href="{{ route('templates.index') }}" class="btn btn-success">
                                <i class="bi bi-file-earmark-text me-2"></i>Templates
                            </a>
                            @endrole
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
