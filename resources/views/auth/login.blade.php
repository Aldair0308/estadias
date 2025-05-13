<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar Sesión - UTVstay</title>
    @vite(['resources/js/theme.js', 'resources/css/app.css'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }

        .bg-pattern {
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)),
                url('https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?q=80&w=1920&auto=format&fit=crop');
        }

        .dark .bg-pattern {
            background-image: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
                url('https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?q=80&w=1920&auto=format&fit=crop');
        }

        .glass {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.17);
        }

        .dark .glass {
            background: rgba(8, 8, 8, 0.25);
            backdrop-filter: blur(70px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }

        .focus-ring {
            transition: all 0.3s ease-in-out;
        }

        .focus-ring:focus {
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.5);
            outline: none;
        }
    </style>
</head>

<body class="bg-pattern bg-cover bg-center bg-fixed min-h-screen flex flex-col justify-center items-center px-4 py-2 text-white">
    <button onclick="toggleTheme()" class="fixed top-4 right-4 z-50 p-2 rounded-full bg-white dark:bg-gray-800 text-gray-800 dark:text-white shadow-md hover:shadow-lg transform hover:rotate-12 transition-all duration-300 w-10 h-10 flex items-center justify-center">
        <svg x-show="!darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>
        <svg x-show="darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
    </button>

    <div class="w-full max-w-md mx-auto relative">
        <div class="glass rounded-3xl overflow-hidden p-8 md:p-10 transform transition-all duration-500 hover:shadow-2xl">
            <div class="text-center mb-8">
                <a href="{{ url('/')}}">
                    <h1 class="text-5xl font-bold mb-2 animated-gradient bg-gradient-to-r from-green-400 via-green-500 to-emerald-600 bg-clip-text text-transparent">UTVstay</h1>
                </a>
                <div class="h-1 w-16 bg-gradient-to-r from-green-400 to-emerald-600 mx-auto rounded-full"></div>
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <div class="text-center mb-8">
                <h2 class="text-xl font-medium text-white mb-1">{{ __('Bienvenido de nuevo') }}</h2>
                <p class="text-green-200 text-sm opacity-90">{{ __('Inicia sesión en tu cuenta') }}</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-green-100 mb-1 ml-1">{{ __('Email') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </div>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            autocomplete="username"
                            class="pl-10 pr-3 py-3 w-full rounded-xl bg-white/10 border border-white/20 text-white placeholder-gray-300 focus-ring"
                            placeholder="nombre@ejemplo.com" />
                    </div>
                    @error('email')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-green-100 mb-1 ml-1">{{ __('Contraseña') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            autocomplete="current-password"
                            class="pl-10 pr-10 py-3 w-10/12 rounded-xl bg-white/10 border border-white/20 text-white placeholder-gray-300 focus-ring"
                            placeholder="••••••••" />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer" onclick="togglePasswordVisibility()">
                            <!-- Ícono para mostrar (ojo) -->
                            <svg id="show-password-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: block;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>

                            <!-- Ícono para ocultar (ojo tachado) -->
                            <svg id="hide-password-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </div>
                    </div>
                    @error('password')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <script>
                    function togglePasswordVisibility() {
                        const passwordInput = document.getElementById('password');
                        const showIcon = document.getElementById('show-password-icon');
                        const hideIcon = document.getElementById('hide-password-icon');

                        if (passwordInput.type === 'password') {
                            passwordInput.type = 'text';
                            showIcon.style.display = 'none';
                            hideIcon.style.display = 'block';
                        } else {
                            passwordInput.type = 'password';
                            showIcon.style.display = 'block';
                            hideIcon.style.display = 'none';
                        }
                    }
                </script>

                <div class="flex items-center">
                    <input
                        id="remember_me"
                        type="checkbox"
                        name="remember"
                        class="h-4 w-4 rounded bg-white/10 border-white/20 text-green-500 focus:ring-green-500">
                    <label for="remember_me" class="ml-2 block text-sm text-green-100">
                        {{ __('Recuérdame') }}
                    </label>
                </div>

                <button
                    type="submit"
                    class="w-full py-3.5 px-4 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl font-medium text-white shadow-lg transform transition duration-300 hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:ring-offset-gray-800 animate-pulse-slow">
                    {{ __('Iniciar sesión') }}
                </button>

                @if (Route::has('password.request'))
                <div class="text-center">
                    <a href="{{ route('password.request') }}" class="text-sm text-green-200 hover:text-white transition-colors duration-300">
                        {{ __('¿Olvidaste tu contraseña?') }}
                    </a>
                </div>
                @endif
            </form>

            <div class="mt-8 pt-6 border-t border-white/10 text-center">
                <p class="text-green-200 text-sm">
                    {{ __('¿No tienes una cuenta?') }}
                    <a href="{{ route('register') }}" class="font-medium text-green-300 hover:text-white transition-colors duration-300 ml-1">
                        {{ __('Regístrate ahora') }}
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>

</html>
