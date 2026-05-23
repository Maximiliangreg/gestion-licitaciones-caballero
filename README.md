# Sistema de Gestión de Licitaciones (MVP) - Consultoría y Soluciones Caballero

Este proyecto es un Producto Mínimo Viable (MVP) diseñado para la gestión y seguimiento de licitaciones comerciales, centralizando información de oportunidades de negocio, control de presupuestos y catálogo de productos ofertados. Desarrollado como prueba técnica con un enfoque arquitectónico escalable y de alta trazabilidad.

## 🛠️ Stack Tecnológico y Entorno
* **Backend:** Laravel 13 (PHP 8.2+)
* **Base de Datos:** PostgreSQL 17 (Alpine)
* **Contenerización:** Docker & Docker Compose (Laravel Sail / Entorno Aislado)

---

## 🗄️ Arquitectura de Base de Datos y Modelos (Laravel 11)

El modelo de datos ha sido diseñado de forma híbrida para satisfacer estrictamente los requerimientos obligatorios del MVP, incorporando mejoras críticas de auditoría y control operativo (Sección 3.3 y 2).

### Entidades y Relaciones Eloquent

1. **User (`users`)**
   * Gestiona a los usuarios internos del sistema.
   * **Roles nativos (Sección 3.4):** `admin` y `user` mediante atributos `#[Fillable]` de Laravel 11.
   * **Relaciones:** Tiene muchas licitaciones (`hasMany(Tender)`) y clientes creados.

2. **Client (`clients`)**
   * Almacena las entidades externas u oportunidades de mercado.
   * **Relaciones:** Pertenece a un creador (`belongsTo(User)`) y posee múltiples procesos (`hasMany(Tender)`).

3. **Product (`products`)**
   * Catálogo maestro de bienes disponibles para ofertas comerciales.
   * **Mejora operativa:** Inclusión de control de inventario (`stock`).
   * **Relaciones:** Relación de muchos a muchos con licitaciones (`belongsToMany(Tender)`).

4. **Tender (`tenders`)**
   * Control central de las propuestas comerciales y sus estados operativos (`activa`, `por_cobrar`, `perdida`, `finalizada`).
   * **Reglas de Negocio:** Validación de presupuesto máximo (`max_budget > 0`) y cálculo acumulado (`total_amount`).
   * **Mejora operativa:** Campo `delivery_deadline` para control visual de alertas fatales antes de que expire el acto.

5. **TenderProduct (`tender_products`) - Tabla Pivote**
   * Registra los productos indexados en cada propuesta comercial con su precio histórico de venta.
   * **Restricción Crítica (RN-09):** Índice compuesto `UNIQUE(['tender_id', 'product_id'])` para mitigar la duplicidad de productos en un mismo acto.

### 📑 Auditoría Estricta (Punto 5)
Para garantizar la trazabilidad de modificaciones exigida, todas las tablas contienen las claves foráneas estructurales:
* `created_by` / `added_by`: Identifica al usuario que originó el registro.
* `updated_by`: Identifica de forma precisa a la última persona que alteró el registro.

---

## 🚀 Comandos de Inicialización y Despliegue en Docker

Para levantar el entorno local de base de datos y sincronizar la estructura relacional, ejecuta los siguientes comandos desde tu terminal de **Git Bash**:

```bash
# 1. Ejecutar las migraciones desde cero limpiando residuos previos
docker exec -it laravel_test php artisan migrate:fresh

# 2. Sembrar los datos obligatorios e iniciales del sistema
docker exec -it laravel_test php artisan db:seed