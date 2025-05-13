<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" x-bind:class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>UTVstay - Sistema de Estadías</title>
    @vite('resources/css/app.css')
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen flex flex-col bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-white transition-colors duration-300">
    <button
        @click="darkMode = !darkMode"
        class="fixed top-4 right-4 z-50 p-2 rounded-full bg-white dark:bg-gray-800 text-gray-800 dark:text-white shadow-md hover:shadow-lg transform hover:rotate-12 transition-all duration-300 w-10 h-10 flex items-center justify-center">
        <svg x-show="!darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>
        <svg x-show="darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
    </button>

    <section class="relative bg-gradient-to-br from-green-500 to-green-900 dark:from-green-700  text-white py-24 shadow-lg overflow-hidden">
        <div class="absolute inset-0 opacity-10 pattern-dots-md"></div>
        <div class="container mx-auto px-4 text-center relative z-10">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">Bienvenido a UTVstay</h1>
            <p class="text-xl md:text-2xl mb-10 max-w-3xl mx-auto">Sistema de Gestión de Archivos para Estadías de la Universidad Tecnológica del Valle de Toluca</p>
            <div class="space-x-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center bg-white text-green-600 px-6 py-3 rounded-full font-medium shadow-md hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Dashboard
                        </a>
                        <a href="{{ url('/files') }}" class="inline-flex items-center bg-white text-green-600 px-6 py-3 rounded-full font-medium shadow-md hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                            </svg>
                            Archivos
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center bg-white text-green-600 px-6 py-3 rounded-full font-medium shadow-md hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            Iniciar Sesión
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center bg-transparent border-2 border-white text-white px-6 py-3 rounded-full font-medium hover:bg-white hover:text-green-600 transform hover:-translate-y-1 transition-all duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                                Registrarse
                            </a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </section>

    <section class="py-20">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-16 text-gray-800 dark:text-white">Características Principales</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg hover:shadow-xl transform hover:-translate-y-2 transition-all duration-300 relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-br from-green-500 to-green-600 opacity-0 group-hover:opacity-5 transition-opacity duration-300"></div>
                    <div class="text-center relative z-10">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300 mb-6 transform group-hover:scale-110 transition-transform duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-800 dark:text-white">Gestión de Documentos</h3>
                        <p class="text-gray-600 dark:text-gray-300">Administra y organiza todos los documentos relacionados con tu estadía de manera eficiente.</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg hover:shadow-xl transform hover:-translate-y-2 transition-all duration-300 relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-br from-green-500 to-green-600 opacity-0 group-hover:opacity-5 transition-opacity duration-300"></div>
                    <div class="text-center relative z-10">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300 mb-6 transform group-hover:scale-110 transition-transform duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-800 dark:text-white">Seguimiento de Estudiantes</h3>
                        <p class="text-gray-600 dark:text-gray-300">Mantén un registro detallado del progreso de los estudiantes durante su período de estadía.</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg hover:shadow-xl transform hover:-translate-y-2 transition-all duration-300 relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-br from-green-500 to-green-600 opacity-0 group-hover:opacity-5 transition-opacity duration-300"></div>
                    <div class="text-center relative z-10">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300 mb-6 transform group-hover:scale-110 transition-transform duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-800 dark:text-white">Control de Versiones</h3>
                        <p class="text-gray-600 dark:text-gray-300">Mantén un historial completo de las versiones de tus documentos y realiza un seguimiento de los cambios.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="mt-auto py-6 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
        <div class="container mx-auto px-4 text-center">
            <p class="text-gray-600 dark:text-gray-400">© {{ date('Y') }} UTVstay - Universidad Tecnológica del Valle de Toluca</p>
        </div>
    </footer>

    <style>
    .pattern-dots-md {
        background-image: radial-gradient(currentColor 1px, transparent 1px);
        background-size: 5px 5px;
    }
    </style>
</body>
</html>
