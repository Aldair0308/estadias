export function initializeTheme() {
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
        localStorage.theme = 'dark';
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.theme = 'light';
    }

    window.toggleTheme = function() {
        if (document.documentElement.classList.contains('dark')) {
            document.documentElement.classList.remove('dark');
            localStorage.theme = 'light';
        } else {
            document.documentElement.classList.add('dark');
            localStorage.theme = 'dark';
        }
        // Actualizar la visibilidad de los íconos
        updateThemeIcons();
    };

    // Función para actualizar los íconos según el tema
    function updateThemeIcons() {
        const isDark = document.documentElement.classList.contains('dark');
        const moonIcons = document.querySelectorAll('[data-icon="moon"]');
        const sunIcons = document.querySelectorAll('[data-icon="sun"]');

        moonIcons.forEach(icon => {
            icon.style.display = isDark ? 'none' : 'block';
        });
        sunIcons.forEach(icon => {
            icon.style.display = isDark ? 'block' : 'none';
        });
    }

    // Inicializar los íconos
    updateThemeIcons();
}

initializeTheme();
