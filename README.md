# Sistema de Gestión de Licitaciones (MVP) - Consultoría y Soluciones Caballero

Este proyecto es un Producto Mínimo Viable (MVP) para la gestión y seguimiento de licitaciones comerciales. Permite centralizar clientes, productos, licitaciones y presupuesto en un entorno Laravel con contenedores Docker.

## 🛠️ Stack Tecnológico y Entorno
* **Backend:** Laravel 13
* **PHP:** ^8.3
* **Base de Datos:** PostgreSQL 17 (Alpine)
* **Autenticación:** Laravel Sanctum
* **Contenerización:** Docker + Docker Compose
* **Frontend:** Vite + Node.js 20

---

## 📁 Estructura del Proyecto
* `app/` - Modelos, controladores, políticas y middleware.
* `routes/` - Rutas web y API.
* `database/` - Migraciones, seeders y factories.
* `config/` - Configuración de Laravel y Sanctum.
* `public/` - Punto de entrada público y activos compilados.

---

## 📌 Modelo de Datos
El proyecto soporta la gestión de estos recursos:
* `User` - Usuarios internos del sistema.
* `Client` - Clientes u organizaciones relacionadas con licitaciones.
* `Product` - Productos disponibles para oferta en licitaciones.
* `Tender` - Licitaciones que agrupan productos y estados comerciales.
* `TenderProduct` - Tabla pivote que vincula productos con licitaciones.

Se utiliza una trazabilidad básica con campos de auditoría en las entidades clave.

---

## � Autenticación y rutas API
El proyecto usa Laravel Sanctum para proteger las rutas API y mantener sesiones seguras.

* Rutas públicas:
  * `POST /api/auth/login` - iniciar sesión.
* Rutas protegidas por `auth:sanctum`:
  * `POST /api/auth/logout` - cerrar sesión.
  * `GET /api/user` - obtener datos del usuario autenticado.
  * `GET|POST|PUT|PATCH|DELETE /api/clients` - CRUD de clientes.
  * `GET|POST|PUT|PATCH|DELETE /api/products` - CRUD de productos.
  * `GET|POST|PUT|PATCH|DELETE /api/tenders` - CRUD de licitaciones.
  * `POST /api/tenders/{id}/attach-product` - vincular producto a licitación.
  * `POST /api/tenders/{id}/detach-product` - desvincular producto de licitación.

La configuración de Sanctum permite dominios stateful locales como `localhost`, `127.0.0.1:8000` y `::1`, y utiliza el guard `web` para la autenticación de la SPA.

---

## �🚀 Cómo levantar el entorno
### 1. Iniciar Docker Compose
```bash
docker-compose up --build -d
```

### 2. Ejecutar migraciones y seeders
```bash
docker exec -it $(docker ps --filter "name=laravel.test" --format "{{.ID}}") php artisan migrate --seed
```

> Si el contenedor se llama diferente, reemplaza el nombre del contenedor en el comando anterior.

### 3. Acceder a la aplicación
* Backend Laravel: `http://localhost:8000`
* Frontend Vite: `http://localhost:5173`

---

## 🧪 Pruebas
Ejecuta los tests de PHPUnit desde el contenedor o localmente si ya tienes PHP configurado:
```bash
docker exec -it $(docker ps --filter "name=laravel.test" --format "{{.ID}}") php artisan test
```

---

## ⚠️ Nota
El proyecto se mantiene configurado para ejecutarse sobre Docker Compose con `postgres:17-alpine` y Node.js 20 para el frontend.
