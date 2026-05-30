<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Crear Usuario</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50">
    <div class="min-h-screen flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-md bg-white rounded-xl shadow p-7">
            <h1 class="text-2xl font-bold text-slate-900">Crear Usuario</h1>
            <p class="text-slate-600 mt-1 text-sm">Registro de usuario para acceso a la SPA.</p>

            <form id="create-user-form" class="mt-6 space-y-4" method="POST" action="#">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Nombre</label>
                    <input
                        type="text"
                        name="name"
                        required
                        class="mt-1 w-full rounded-lg border-slate-300 bg-white text-slate-900 focus:border-slate-500 focus:ring-slate-500"
                        placeholder="Ej: Ana Pérez"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Email</label>
                    <input
                        type="email"
                        name="email"
                        required
                        class="mt-1 w-full rounded-lg border-slate-300 bg-white text-slate-900 focus:border-slate-500 focus:ring-slate-500"
                        placeholder="correo@dominio.com"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Rol</label>
                    <select
                        name="role"
                        class="mt-1 w-full rounded-lg border-slate-300 bg-white text-slate-900 focus:border-slate-500 focus:ring-slate-500"
                    >
                        <option value="user">user</option>
                        <option value="admin">admin</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Contraseña</label>
                    <input
                        type="password"
                        name="password"
                        required
                        class="mt-1 w-full rounded-lg border-slate-300 bg-white text-slate-900 focus:border-slate-500 focus:ring-slate-500"
                        placeholder="Mínimo 6 caracteres"
                    />
                </div>

                <div id="create-user-error" class="hidden rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700"></div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-slate-900 text-white rounded-lg px-4 py-2.5 font-medium hover:bg-slate-800 transition-colors">
                        Crear Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const form = document.getElementById('create-user-form');
        const errorBox = document.getElementById('create-user-error');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            errorBox.classList.add('hidden');
            errorBox.textContent = '';

            const payload = {
                name: form.name.value,
                email: form.email.value,
                role: form.role.value,
                password: form.password.value,
            };

            try {
                const token = localStorage.getItem('spa_token');
                if (!token) {
                    errorBox.textContent = 'No hay token activo. Inicia sesión nuevamente.';
                    errorBox.classList.remove('hidden');
                    return;
                }

                const response = await fetch('/api/users', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    errorBox.textContent = data.message || 'Error al crear usuario.';
                    errorBox.classList.remove('hidden');
                    return;
                }

                window.location.href = '/login';
            } catch (err) {
                errorBox.textContent = 'Error al crear usuario.';
                errorBox.classList.remove('hidden');
            }
        });
    </script>
</body>
</html>

