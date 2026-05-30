/**
 * ===================================================================
 * CABALLERO SPA - ROUTER JAVASCRIPT MINIMALISTA
 * ===================================================================
 * Sistema de navegación instantánea SPA que alterna la visibilidad
 * de las vistas usando clases Tailwind CSS (hidden/visible).
 * Maneja el cambio de estado del sidebar y la gestión de rutas.
 * ===================================================================
 */

// Inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function () {
    initSpaRouter();
});

/**
 * Inicializa el router SPA
 * Configura los listeners de los botones del sidebar
 */
function initSpaRouter() {
    const navButtons = document.querySelectorAll('.nav-btn');
    
    navButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const targetViewId = this.getAttribute('data-target');
            navigateToView(targetViewId);
        });
    });
}

/**
 * Navega a una vista específica
 * @param {string} viewId - ID de la vista a mostrar (ej: 'view-inicio')
 */
function navigateToView(viewId) {
    const allViews = document.querySelectorAll('.spa-view');
    const targetView = document.getElementById(viewId);

    if (!targetView) {
        console.error(`Vista no encontrada: ${viewId}`);
        return;
    }

    allViews.forEach(view => {
        view.classList.add('hidden');
    });

    targetView.classList.remove('hidden');

    updateActiveNavButton(viewId);
}

/**
 * Actualiza el estilo del botón activo en el sidebar
 * @param {string} viewId - ID de la vista activa
 */
function updateActiveNavButton(viewId) {
    const navButtons = document.querySelectorAll('.nav-btn');
    
    navButtons.forEach(button => {
        const buttonTarget = button.getAttribute('data-target');
        
        if (buttonTarget === viewId) {
            button.classList.remove('text-slate-300');
            button.classList.add('bg-slate-800', 'text-white');
        } else {
            button.classList.remove('bg-slate-800', 'text-white');
            button.classList.add('text-slate-300');
        }
    });
}

/**
 * Cierra la sesión del usuario
 * Redirige a la página de login (cuando esté implementada)
 */
function logout() {
    if (confirm('¿Estás seguro de que deseas cerrar sesión?')) {
        localStorage.removeItem('spa_token');
        localStorage.removeItem('spa_user');
        localStorage.removeItem('spa_role');
        window.location.href = '/login';
    }
}

window.navigateToView = navigateToView;
window.logout = logout;
