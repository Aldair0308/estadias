<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            :root {
                --primary-bg: #ffffff;
                --secondary-bg: #f3f4f6;
                --primary-text: #111827;
                --accent-color: #22c55e;
                --button-bg: #f9fafb;
                --button-hover: #f3f4f6;
                --button-text: #374151;
                --button-border: #d1d5db;
            }
            .dark {
                --primary-bg: #1a1a1a;
                --secondary-bg: #2d2d2d;
                --primary-text: #ffffff;
                --accent-color: #22c55e;
                --button-bg: #374151;
                --button-hover: #4b5563;
                --button-text: #ffffff;
                --button-border: #4b5563;
            }
            body {
                background-color: var(--primary-bg);
                color: var(--primary-text);
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

        <div class="min-h-screen">

            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-[var(--primary-bg)] shadow transition-colors duration-200">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
