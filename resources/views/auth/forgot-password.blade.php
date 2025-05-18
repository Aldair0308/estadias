<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Recuperar Contraseña - UTVstay</title>
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

        .success-message {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            backdrop-filter: blur(10px);
        }
    </style>
</head>

<body class="bg-pattern bg-cover bg-center bg-fixed min-h-screen flex flex-col justify-center items-center px-4 py-2 text-white">
    <button onclick="toggleTheme()" class="fixed top-4 right-4 z-50 p-2 rounded-full bg-white dark:bg-gray-800 text-gray-800 dark:text-white shadow-md hover:shadow-lg transform hover:rotate-12 transition-all duration-300 w-10 h-10 flex items-center justify-center">
        <svg data-icon="moon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>
        <svg data-icon="sun" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
    </button>

    <div class="w-full max-w-md mx-auto relative">
        <div class="glass rounded-3xl overflow-hidden p-8 md:p-10 transform transition-all duration-500 hover:shadow-2xl">
            <div class="text-center mb-8">
                <a href="{{ route('welcome') }}">
                    <h1 class="text-5xl font-bold mb-2 animated-gradient bg-gradient-to-r from-green-400 via-green-500 to-emerald-600 bg-clip-text text-transparent">UTVstay</h1>
                </a>
                <div class="h-1 w-16 bg-gradient-to-r from-green-400 to-emerald-600 mx-auto rounded-full"></div>
            </div>

            <div class="text-center mb-8">
                <h2 class="text-xl font-medium text-white mb-2">{{ __('¿Olvidaste tu contraseña?') }}</h2>
                <p class="text-green-200 text-sm opacity-90 leading-relaxed">
                    {{ __('No te preocupes. Introduce tu dirección de email y te enviaremos un enlace para restablecer tu contraseña.') }}
                </p>
            </div>

            @if (session('status'))
                <div class="mb-6 p-4 rounded-xl success-message">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm text-green-300">{{ session('status') }}</p>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
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
                            class="pl-10 pr-3 py-3 w-full rounded-xl bg-white/10 border border-white/20 text-white placeholder-gray-300 focus-ring"
                            placeholder="nombre@ejemplo.com" />
                    </div>
                    @error('email')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="w-full py-3.5 px-4 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl font-medium text-white shadow-lg transform transition duration-300 hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:ring-offset-gray-800">
                    {{ __('Enviar enlace de restablecimiento') }}
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-white/10 text-center">
                <p class="text-green-200 text-sm">
                    {{ __('¿Recordaste tu contraseña?') }}
                    <a href="{{ route('login') }}" class="font-medium text-green-300 hover:text-white transition-colors duration-300 ml-1">
                        {{ __('Iniciar sesión') }}
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>

</html>
