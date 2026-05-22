const API_PATHS = {
    login: '/api/auth/login',
};

class SessionState {
    constructor() {
        this.token = localStorage.getItem('spa_token');
        this.user = this.loadJson('spa_user');
        this.role = localStorage.getItem('spa_role');
        this.subscribers = [];
    }

    loadJson(key) {
        try {
            const value = localStorage.getItem(key);
            return value ? JSON.parse(value) : null;
        } catch {
            return null;
        }
    }

    save() {
        if (this.token) {
            localStorage.setItem('spa_token', this.token);
        } else {
            localStorage.removeItem('spa_token');
        }

        if (this.role) {
            localStorage.setItem('spa_role', this.role);
        } else {
            localStorage.removeItem('spa_role');
        }

        if (this.user) {
            localStorage.setItem('spa_user', JSON.stringify(this.user));
        } else {
            localStorage.removeItem('spa_user');
        }
    }

    setSession(token, user, role) {
        this.token = token;
        this.user = user;
        this.role = role;
        this.save();
        this.notify();
    }

    clearSession() {
        this.token = null;
        this.user = null;
        this.role = null;
        this.save();
        this.notify();
    }

    subscribe(callback) {
        this.subscribers.push(callback);
    }

    notify() {
        this.subscribers.forEach((callback) => callback(this));
    }

    get isAuthenticated() {
        return Boolean(this.token);
    }
}

class ApiService {
    async login(email, password) {
        const response = await fetch(API_PATHS.login, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
            body: JSON.stringify({ email, password }),
        });

        const payload = await response.json().catch(() => null);

        if (!response.ok || !payload || payload.success === false) {
            const message = payload?.message || 'Error de autenticaci�n.';
            throw new Error(message);
        }

        return payload.data;
    }
}

class BaseView {
    constructor(root, state) {
        this.root = root;
        this.state = state;
    }

    clear() {
        this.root.innerHTML = '';
    }
}

class LoginView extends BaseView {
    constructor(root, state, onSubmit) {
        super(root, state);
        this.onSubmit = onSubmit;
        this.errorMessage = '';
    }

    render() {
        this.root.innerHTML = `
            <section class="max-w-md mx-auto bg-white dark:bg-[#111111] rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
                <h1 class="text-2xl font-semibold mb-3 text-[#1b1b18] dark:text-white">Iniciar sesi�n</h1>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-6">Ingresa tu correo y contrase�a para acceder al panel.</p>
                <form id="login-form" class="space-y-4">
                    <div>
                        <label for="login-email" class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-200">Email</label>
                        <input id="login-email" type="email" autocomplete="username" required class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#121212] text-gray-900 dark:text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#F53003]" />
                    </div>
                    <div>
                        <label for="login-password" class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-200">Contrase�a</label>
                        <input id="login-password" type="password" autocomplete="current-password" required class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#121212] text-gray-900 dark:text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#F53003]" />
                    </div>
                    <button type="submit" class="w-full rounded-md bg-[#1b1b18] text-white py-3 text-sm font-semibold hover:bg-[#343434] transition">Entrar</button>
                </form>
                <p id="login-error" class="${this.errorMessage ? 'mt-4 text-sm text-red-600' : 'hidden'}">${this.errorMessage || ''}</p>
            </section>
        `;

        this.bindEvents();
    }

    bindEvents() {
        const form = this.root.querySelector('#login-form');

        if (form) {
            form.addEventListener('submit', this.handleSubmit.bind(this));
        }
    }

    setError(message) {
        this.errorMessage = message;
        const errorEl = this.root.querySelector('#login-error');

        if (!errorEl) {
            return;
        }

        errorEl.textContent = message || '';
        errorEl.classList.toggle('hidden', !message);
        errorEl.classList.toggle('mt-4', Boolean(message));
        errorEl.classList.toggle('text-red-600', Boolean(message));
    }

    handleSubmit(event) {
        event.preventDefault();

        const email = this.root.querySelector('#login-email').value.trim();
        const password = this.root.querySelector('#login-password').value.trim();

        if (!email || !password) {
            this.setError('El email y la contrase�a son obligatorios.');
            return;
        }

        this.setError('');
        this.onSubmit({ email, password });
    }
}

class MenuView extends BaseView {
    constructor(root, state, onNavigate, onLogout) {
        super(root, state);
        this.onNavigate = onNavigate;
        this.onLogout = onLogout;
        this.activeView = 'dashboard';
    }

    render() {
        const isAuthenticated = this.state.isAuthenticated;
        const userName = this.state.user?.name ?? 'Invitado';
        const role = this.state.role ?? 'user';

        this.root.innerHTML = `
            <div class="max-w-6xl mx-auto flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-lg font-semibold text-[#1b1b18] dark:text-white">Caballero SPA</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">${isAuthenticated ? `${userName} � ${role}` : 'Por favor inicia sesi�n para ver el contenido.'}</p>
                </div>
                ${isAuthenticated ? `
                    <nav class="flex flex-wrap gap-2">
                        <button data-view="dashboard" class="menu-action rounded-md border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-[#1b1b18] dark:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition">Inicio</button>
                        <button data-view="profile" class="menu-action rounded-md border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-[#1b1b18] dark:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition">Perfil</button>
                        <button data-view="settings" class="menu-action rounded-md border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-[#1b1b18] dark:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition">Ajustes</button>
                        <button id="logout-button" class="rounded-md bg-[#F53003] text-white px-4 py-2 text-sm font-semibold hover:bg-[#d12a03] transition">Cerrar sesi�n</button>
                    </nav>
                ` : ''}
            </div>
        `;

        this.bindEvents();
    }

    bindEvents() {
        if (!this.state.isAuthenticated) {
            return;
        }

        const buttons = this.root.querySelectorAll('.menu-action');

        buttons.forEach((button) => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                this.activeView = event.currentTarget.dataset.view;
                this.onNavigate(this.activeView);
            });
        });

        const logoutButton = this.root.querySelector('#logout-button');

        if (logoutButton) {
            logoutButton.addEventListener('click', (event) => {
                event.preventDefault();
                this.onLogout();
            });
        }
    }
}

class ContentView extends BaseView {
    render(view) {
        if (!this.state.isAuthenticated) {
            this.clear();
            return;
        }

        let content = '';

        switch (view) {
            case 'profile':
                content = this.profileView();
                break;
            case 'settings':
                content = this.settingsView();
                break;
            default:
                content = this.dashboardView();
                break;
        }

        this.root.innerHTML = `
            <section class="max-w-6xl mx-auto bg-white dark:bg-[#111111] rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
                ${content}
            </section>
        `;
    }

    dashboardView() {
        return `
            <div>
                <h2 class="text-2xl font-semibold mb-4 text-[#1b1b18] dark:text-white">Panel principal</h2>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">Bienvenido ${this.state.user?.name ?? 'usuario'}. Aqu� puedes navegar entre las vistas del sistema sin recargar la p�gina.</p>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-[#141414]">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Rol de usuario</p>
                        <p class="mt-2 font-semibold text-[#1b1b18] dark:text-white">${this.state.role}</p>
                    </div>
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-[#141414]">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Token activo</p>
                        <p class="mt-2 text-sm text-gray-700 dark:text-gray-300 break-all">${this.state.token}</p>
                    </div>
                </div>
            </div>
        `;
    }

    profileView() {
        return `
            <div>
                <h2 class="text-2xl font-semibold mb-4 text-[#1b1b18] dark:text-white">Perfil</h2>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-6">Datos b�sicos del usuario autenticado.</p>
                <dl class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-[#141414]">
                        <dt class="text-xs uppercase text-gray-500 dark:text-gray-400">Nombre</dt>
                        <dd class="mt-2 font-medium text-[#1b1b18] dark:text-white">${this.state.user?.name ?? 'N/A'}</dd>
                    </div>
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-[#141414]">
                        <dt class="text-xs uppercase text-gray-500 dark:text-gray-400">Rol</dt>
                        <dd class="mt-2 font-medium text-[#1b1b18] dark:text-white">${this.state.role}</dd>
                    </div>
                </dl>
            </div>
        `;
    }

    settingsView() {
        return `
            <div>
                <h2 class="text-2xl font-semibold mb-4 text-[#1b1b18] dark:text-white">Ajustes</h2>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">Esta vista sirve como punto de partida para generar componentes de configuraci�n.</p>
                <ul class="space-y-3 text-sm text-gray-700 dark:text-gray-300">
                    <li class="rounded-xl p-4 bg-gray-50 dark:bg-[#141414] border border-gray-200 dark:border-gray-700">Gesti�n de perfil y permisos</li>
                    <li class="rounded-xl p-4 bg-gray-50 dark:bg-[#141414] border border-gray-200 dark:border-gray-700">Preferencias de la aplicaci�n</li>
                    <li class="rounded-xl p-4 bg-gray-50 dark:bg-[#141414] border border-gray-200 dark:border-gray-700">Notificaciones y seguridad</li>
                </ul>
            </div>
        `;
    }
}

class App {
    constructor() {
        this.state = new SessionState();
        this.api = new ApiService();
        this.menuRoot = document.getElementById('app-header');
        this.contentRoot = document.getElementById('app-content');
        this.currentView = 'dashboard';
        this.loginView = null;
        this.menuView = null;
        this.contentView = null;
    }

    init() {
        if (!this.menuRoot || !this.contentRoot) {
            return;
        }

        this.loginView = new LoginView(this.contentRoot, this.state, this.handleLogin.bind(this));
        this.menuView = new MenuView(this.menuRoot, this.state, this.handleNavigate.bind(this), this.handleLogout.bind(this));
        this.contentView = new ContentView(this.contentRoot, this.state);

        this.state.subscribe(() => this.render());
        this.render();
    }

    render() {
        this.menuView.render();

        if (!this.state.isAuthenticated) {
            this.loginView.render();
            return;
        }

        this.contentView.render(this.currentView);
    }

    async handleLogin({ email, password }) {
        try {
            const data = await this.api.login(email, password);
            const user = {
                name: data.name || '',
                role: data.role || 'user',
            };

            this.state.setSession(data.token, user, user.role);
            this.currentView = 'dashboard';
        } catch (error) {
            this.loginView.setError(error.message || 'No se pudo iniciar sesi�n.');
        }
    }

    handleNavigate(view) {
        this.currentView = view;
        this.contentView.render(view);
    }

    handleLogout() {
        this.state.clearSession();
        this.currentView = 'dashboard';
    }
}

window.addEventListener('DOMContentLoaded', () => {
    new App().init();
});
