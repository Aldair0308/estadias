<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>UTVstay - Sistema de Estadías</title>
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
            --preview-bg: #2b3035;
            --preview-text: #e9ecef;
        }
        
        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: background-color 0.3s ease, color 0.3s ease;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .hero-section {
            background: linear-gradient(135deg, var(--header-gradient-start), var(--header-gradient-end));
            padding: 6rem 0;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1;
            margin-bottom: 4rem;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="20" height="20" xmlns="http://www.w3.org/2000/svg"><circle cx="2" cy="2" r="1" fill="rgba(255,255,255,0.1)"/></svg>');
            opacity: 0.3;
            z-index: -1;
        }

        .auth-buttons {
            position: relative;
            z-index: 2;
        }

        .feature-card {
            background-color: var(--card-bg);
            border: none;
            border-radius: 1rem;
            padding: 2rem;
            height: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, var(--header-gradient-start), var(--header-gradient-end));
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 0;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .feature-card:hover::before {
            opacity: 0.05;
        }

        .feature-card > * {
            position: relative;
            z-index: 1;
        }

        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            color: var(--primary-color);
            transition: transform 0.3s ease;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1);
        }

        .btn-theme-toggle {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1030;
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            padding: 0.5rem;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            color: var(--text-color);
        }

        .btn-theme-toggle:hover {
            transform: rotate(30deg);
        }

        .auth-buttons .btn {
            padding: 0.75rem 2rem;
            border-radius: 2rem;
            font-weight: 500;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .auth-buttons .btn:hover {
            transform: translateY(-2px);
        }

        footer {
            margin-top: auto;
            padding: 2rem 0;
            background-color: var(--card-bg);
            border-top: 1px solid var(--border-color);
        }

        .features-section {
            padding: 4rem 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 3rem;
            color: var(--text-color);
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
    <button onclick="toggleTheme()" class="btn btn-theme-toggle" title="Cambiar tema">
        <i class="bi bi-moon-stars"></i>
    </button>

    <section class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold mb-4">Bienvenido a UTVstay</h1>
            <p class="lead mb-5">Sistema de Gestión de Estadías de la Universidad Tecnológica de la Vega</p>
            <div class="auth-buttons">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-light btn-lg me-3 shadow-sm">
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard
                        </a>
                        <a href="{{ url('/files') }}" class="btn btn-light btn-lg shadow-sm">
                            <i class="bi bi-folder me-2"></i>Archivos
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-light btn-lg me-3 shadow-sm">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg shadow-sm">
                                <i class="bi bi-person-plus me-2"></i>Registrarse
                            </a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </section>

    <section class="features-section">
        <div class="container">
            <h2 class="section-title h1 mb-5">Características Principales</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <i class="bi bi-file-earmark-text feature-icon"></i>
                        <h3 class="h4 mb-3">Gestión de Documentos</h3>
                        <p class="text-muted mb-0">Administra y organiza todos los documentos relacionados con tu estadía de manera eficiente.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <i class="bi bi-people feature-icon"></i>
                        <h3 class="h4 mb-3">Seguimiento de Estudiantes</h3>
                        <p class="text-muted mb-0">Mantén un registro detallado del progreso de los estudiantes durante su período de estadía.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <i class="bi bi-clock-history feature-icon"></i>
                        <h3 class="h4 mb-3">Control de Versiones</h3>
                        <p class="text-muted mb-0">Mantén un historial completo de las versiones de tus documentos y realiza un seguimiento de los cambios.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="text-center">
        <div class="container">
            <p class="text-muted mb-0">© {{ date('Y') }} UTVstay - Universidad Tecnológica de la Vega</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
