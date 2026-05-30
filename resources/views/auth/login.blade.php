<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50">
    <div class="min-h-screen flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-md bg-white rounded-xl shadow p-7">
            <h1 class="text-2xl font-bold text-slate-900">Iniciar Sesión</h1>
            <p class="text-slate-600 mt-1 text-sm">Accede a tu panel.</p>

            <form id="login-form" class="mt-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Email</label>
                    <input
                        type="email"
                        name="email"
                        required
                        autocomplete="email"
                        class="mt-1 w-full rounded-lg border-slate-300 bg-white text-slate-900 focus:border-slate-500 focus:ring-slate-500"
                        placeholder="correo@dominio.com"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Contraseña</label>
                    <input
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        class="mt-1 w-full rounded-lg border-slate-300 bg-white text-slate-900 focus:border-slate-500 focus:ring-slate-500"
                        placeholder="••••••••"
                    />
                </div>

                <div id="login-error" class="hidden rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700"></div>

                <button type="submit" class="w-full bg-slate-900 text-white rounded-lg px-4 py-2.5 font-medium hover:bg-slate-800 transition-colors">
                    Entrar
                </button>
            </form>
        </div>
    </div>

    <script>
        const form = document.getElementById('login-form');
        const errorBox = document.getElementById('login-error');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            errorBox.classList.add('hidden');
            errorBox.textContent = '';

            const email = form.email.value;
            const password = form.password.value;

            try {
                const response = await fetch('/auth/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ email, password })
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    errorBox.textContent = data.message || 'Error al iniciar sesión.';
                    errorBox.classList.remove('hidden');
                    return;
                }

                const token = data.data.token;
                const user = data.data.name;
                const role = data.data.role;

                localStorage.setItem('spa_token', token);
                localStorage.setItem('spa_user', user);
                localStorage.setItem('spa_role', role);

                window.location.href = '/';
            } catch (err) {
                errorBox.textContent = 'Error de red. Intenta nuevamente.';
                errorBox.classList.remove('hidden');
            }
        });
    </script>
</body>
</html>

