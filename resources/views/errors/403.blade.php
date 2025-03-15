<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Acceso Denegado</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function goBack() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = '/';
            }
        }

        // Auto redirect after 5 seconds
        setTimeout(goBack, 3000);
    </script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-xl w-full px-4">
            <div class="bg-white shadow-2xl rounded-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
                <div class="p-8 text-center">
                    <div class="animate-bounce mb-6">
                        <svg class="mx-auto h-24 w-24 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h1 class="text-4xl font-bold text-red-500 mb-4 animate-pulse">403</h1>
                    <p class="text-2xl font-semibold text-gray-800 mb-2">Acceso Denegado</p>
                    <p class="text-gray-600 mb-4">Lo sentimos, esta área está restringida solo para administradores.</p>
                    <p class="text-sm text-gray-500 mb-8">Redirigiendo en 4 segundos...</p>
                    <button onclick="goBack()" class="bg-red-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-600 transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
                        ← Volver
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>