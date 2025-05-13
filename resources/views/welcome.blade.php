<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" x-bind:class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>UTVstay - Sistema de Estadías</title>
    @vite(['resources/js/theme.js', 'resources/css/app.css'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen flex flex-col bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-white transition-colors duration-300">
    <button onclick="toggleTheme()" class="fixed top-4 right-4 z-50 p-2 rounded-full bg-white dark:bg-gray-800 text-gray-800 dark:text-white shadow-md hover:shadow-lg transform hover:rotate-12 transition-all duration-300 w-10 h-10 flex items-center justify-center">
        <svg x-show="!darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>
        <svg x-show="darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
    </button>

    <section class="relative bg-gradient-to-br from-green-500 to-green-900 dark:from-green-700 text-white py-24 shadow-lg overflow-hidden">
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

    <section class="py-20 bg-gray-50 dark:bg-gray-800">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-16 text-gray-800 dark:text-white">¿Cómo Funciona?</h2>
            <div class="flex flex-col md:flex-row justify-center items-center md:items-start space-y-10 md:space-y-0 md:space-x-8">
                <div class="flex flex-col items-center max-w-xs text-center">
                    <div class="bg-white dark:bg-gray-700 w-16 h-16 rounded-full flex items-center justify-center mb-4 text-green-600 dark:text-green-400 shadow-md">
                        <span class="text-2xl font-bold">1</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-800 dark:text-white">Registro</h3>
                    <p class="text-gray-600 dark:text-gray-300">Crea tu cuenta y perfil con la información requerida por tu coordinador de estadías.</p>
                </div>

                <div class="hidden md:block w-8 h-1 md:w-12 md:h-0.5 bg-green-500 mt-8"></div>

                <div class="flex flex-col items-center max-w-xs text-center">
                    <div class="bg-white dark:bg-gray-700 w-16 h-16 rounded-full flex items-center justify-center mb-4 text-green-600 dark:text-green-400 shadow-md">
                        <span class="text-2xl font-bold">2</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-800 dark:text-white">Subir Documentos</h3>
                    <p class="text-gray-600 dark:text-gray-300">Carga tus formatos, reportes y cualquier documento relacionado con tu estadía.</p>
                </div>

                <div class="hidden md:block w-8 h-1 md:w-12 md:h-0.5 bg-green-500 mt-8"></div>

                <div class="flex flex-col items-center max-w-xs text-center">
                    <div class="bg-white dark:bg-gray-700 w-16 h-16 rounded-full flex items-center justify-center mb-4 text-green-600 dark:text-green-400 shadow-md">
                        <span class="text-2xl font-bold">3</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-800 dark:text-white">Seguimiento</h3>
                    <p class="text-gray-600 dark:text-gray-300">Recibe retroalimentación y mantente al día con el estado de tu proceso de estadía.</p>
                </div>
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

    <section class="py-20 bg-gray-50 dark:bg-gray-800">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-16 text-gray-800 dark:text-white">Beneficios para Todos</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                <div class="bg-white dark:bg-gray-700 rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
                    <div class="h-2 bg-blue-500"></div>
                    <div class="p-6">
                        <div class="flex items-center justify-center mb-4">
                            <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-500 dark:text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path d="M12 14l9-5-9-5-9 5 9 5z" />
                                    <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-center mb-4 text-gray-800 dark:text-white">Para Estudiantes</h3>
                        <ul class="space-y-3">
                            <li class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-gray-600 dark:text-gray-300">Organiza todos tus documentos en un solo lugar</span>
                            </li>
                            <li class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-gray-600 dark:text-gray-300">Recibe retroalimentación rápida y efectiva</span>
                            </li>
                            <li class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-gray-600 dark:text-gray-300">Sigue tu progreso en tiempo real</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-700 rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
                    <div class="h-2 bg-purple-500"></div>
                    <div class="p-6">
                        <div class="flex items-center justify-center mb-4">
                            <div class="bg-purple-100 dark:bg-purple-900 p-3 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-500 dark:text-purple-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-center mb-4 text-gray-800 dark:text-white">Para Profesores</h3>
                        <ul class="space-y-3">
                            <li class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-gray-600 dark:text-gray-300">Gestiona múltiples estudiantes fácilmente</span>
                            </li>
                            <li class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-gray-600 dark:text-gray-300">Proporciona retroalimentación específica</span>
                            </li>
                            <li class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-gray-600 dark:text-gray-300">Accede a reportes y estadísticas del progreso</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-700 rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
                    <div class="h-2 bg-yellow-500"></div>
                    <div class="p-6">
                        <div class="flex items-center justify-center mb-4">
                            <div class="bg-yellow-100 dark:bg-yellow-900 p-3 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-500 dark:text-yellow-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-center mb-4 text-gray-800 dark:text-white">Para Administradores</h3>
                        <ul class="space-y-3">
                            <li class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-gray-600 dark:text-gray-300">Visualiza métricas generales del proceso</span>
                            </li>
                            <li class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-gray-600 dark:text-gray-300">Asegura cumplimiento de procedimientos</span>
                            </li>
                            <li class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-gray-600 dark:text-gray-300">Gestiona permisos y roles del sistema</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
