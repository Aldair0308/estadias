<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar Sesión - UTVstay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary-color: #35B550;
            --secondary-color: #2CA14D;
            --success-color: #35B550;
            --info-color: #2CA14D;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --light-color: #f8f9fa;
            --dark-color: #121212;
            --bg-color: #FFFFFF;
            --text-color: #2CA14D;
            --border-color: #e9ecef;
            --card-bg: #FFFFFF;
            --header-gradient-start: #35B550;
            --header-gradient-end: #2CA14D;
        }

        [data-theme="dark"] {
            --bg-color: #121212;
            --text-color: #FFFFFF;
            --border-color: #2CA14D;
            --card-bg: #1E1E1E;
            --header-gradient-start: #35B550;
            --header-gradient-end: #2CA14D;
        }
        
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?q=80&w=1920&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: var(--text-color);
            transition: background-color 0.3s ease, color 0.3s ease;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        [data-theme="dark"] body {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?q=80&w=1920&auto=format&fit=crop');
        }
        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 2rem;
            margin: 1rem;
            background: var(--card-bg);
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        @media (min-width: 576px) {
            .login-container {
                padding: 2.5rem;
                margin: 2rem auto;
            }
        }

        @media (max-width: 575.98px) {
            body {
                padding: 1rem;
            }
            
            .app-logo {
                font-size: 3rem;
                margin-bottom: 1.5rem;
            }
            
            .btn-theme-toggle {
                top: 0.5rem;
                right: 0.5rem;
            }
        }

        .login-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        [data-theme="dark"] .app-logo {
            color: white;
        }
        .app-logo {
            background: linear-gradient(135deg, var(--header-gradient-start), var(--header-gradient-end));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            font-size: 4rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: 2rem;
            transition: all 0.3s ease;
        }

        .form-control {
            background-color: var(--bg-color);
            border: 1px solid var(--border-color);
            color: var(--text-color);
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background-color: var(--bg-color);
            border-color: var(--primary-color);
            color: var(--text-color);
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .btn-theme-toggle {
            position: fixed;
            top: 1rem;
            right: 1rem;
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            color: var(--text-color);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .btn-theme-toggle:hover {
            transform: rotate(30deg);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--header-gradient-start), var(--header-gradient-end));
            border: none;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.4);
        }

        .text-muted {
            color: var(--text-color) !important;
            opacity: 0.7;
        }

        a {
            color: var(--primary-color);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        a:hover {
            color: var(--header-gradient-end);
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

    <div class="login-container">
        <div class="app-logo">UTVstay</div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <div class="text-center mb-4">
            <h2 class="h4 mb-2">{{ __('Bienvenido de nuevo') }}</h2>
            <p class="text-muted">{{ __('Inicia sesión en tu cuenta') }}</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div class="mb-3">
                <label for="email" class="form-label">{{ __('Email') }}</label>
                <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" />
                @error('email')
                    <div class="text-danger mt-1 small">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label">{{ __('Contraseña') }}</label>
                <input id="password" class="form-control" type="password" name="password" required autocomplete="current-password" />
                @error('password')
                    <div class="text-danger mt-1 small">{{ $message }}</div>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="mb-3">
                <div class="form-check">
                    <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                    <label class="form-check-label" for="remember_me">{{ __('Recuérdame') }}</label>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    {{ __('Iniciar sesión') }}
                </button>

                @if (Route::has('password.request'))
                    <a class="text-center" href="{{ route('password.request') }}">
                        {{ __('¿Olvidaste tu contraseña?') }}
                    </a>
                @endif
            </div>
        </form>

        <div class="text-center mt-4 pt-3 border-top">
            <p class="text-muted">
                {{ __('¿No tienes una cuenta?') }}
                <a href="{{ route('register') }}">{{ __('Regístrate') }}</a>
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
