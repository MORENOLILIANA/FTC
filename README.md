# NutriCasa — Backend API

API REST desarrollada en **Laravel 10** que da soporte a la aplicación móvil NutriCasa. Gestiona la autenticación de usuarios, despensas, listas de la compra y un catálogo de productos con información nutricional.

---

## Descripción del proyecto

NutriCasa es una aplicación para gestionar el inventario del hogar. Desde el móvil el usuario puede:

- Registrar los productos que tiene en casa (despensa)
- Crear listas de la compra y marcar artículos como comprados
- Escanear el código de barras de un producto para obtener su información nutricional automáticamente
- Compartir despensas y listas con otros miembros del hogar
- Recibir alertas cuando un producto está próximo a caducar o con stock bajo

---

## Tecnologías utilizadas

| Capa | Tecnología |
|---|---|
| Framework | Laravel 10 |
| Autenticación | Laravel Sanctum (tokens de API) |
| Base de datos | MySQL 8 |
| Servidor web | Nginx |
| Contenedores | Docker + Docker Compose |
| SSL | Let's Encrypt (Certbot, renovación automática) |
| Email | Gmail SMTP |
| APIs externas | Open Food Facts, UPC Item DB |

---

## Arquitectura

El código sigue una arquitectura limpia en tres capas:

```
app/
├── Http/
│   ├── Controllers/       # Reciben la petición HTTP, validan y responden
│   │   ├── Auth/
│   │   │   └── AuthController.php
│   │   ├── Api/V1/
│   │   │   ├── ProfileController.php
│   │   │   └── NotificationController.php
│   │   ├── PantryController.php
│   │   ├── ProductController.php
│   │   └── ShoppingListController.php
│   ├── Middleware/        # Middleware personalizado
│   └── Requests/          # Validación de formularios
├── Services/              # Lógica de negocio
│   ├── PantryService.php
│   ├── ProductService.php
│   ├── ShoppingListService.php
│   ├── UserService.php
│   ├── OpenFoodFactsService.php
│   └── UpcItemDbService.php
├── Models/                # Modelos Eloquent
│   ├── User.php
│   ├── Product.php
│   ├── Pantry.php
│   ├── PantryItem.php
│   ├── ShoppingList.php
│   └── ShoppingListItem.php
└── Mail/
    └── ResetPasswordMail.php
```

---

## Endpoints de la API

Base URL: `https://nutricasa.duckdns.org/api/v1`

### Autenticación (pública)

| Método | Ruta | Descripción |
|---|---|---|
| POST | `/auth/register` | Registro de nuevo usuario |
| POST | `/auth/login` | Inicio de sesión, devuelve token |
| POST | `/auth/forgot-password` | Solicitar email de recuperación |
| POST | `/auth/reset-password` | Restablecer contraseña con token |

### Autenticación (requiere token)

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/auth/me` | Datos del usuario autenticado |
| POST | `/auth/logout` | Cerrar sesión |

### Perfil

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/profile` | Ver perfil |
| PUT | `/profile` | Actualizar nombre o email |
| POST | `/profile/change-password` | Cambiar contraseña |

### Productos

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/products` | Listar productos |
| POST | `/products` | Crear producto manualmente |
| GET | `/products/search` | Buscar por nombre |
| GET | `/products/barcode/{barcode}` | Buscar por código de barras (Open Food Facts + UPC Item DB) |
| GET | `/products/barcode/{barcode}/nutritional` | Información nutricional |
| GET | `/products/{id}` | Ver producto |
| PUT | `/products/{id}` | Editar producto |
| DELETE | `/products/{id}` | Eliminar producto |

### Despensas

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/pantries` | Listar despensas del usuario |
| POST | `/pantries` | Crear despensa |
| GET | `/pantries/{id}` | Ver despensa con sus items |
| PUT | `/pantries/{id}` | Editar despensa |
| DELETE | `/pantries/{id}` | Eliminar despensa |
| POST | `/pantries/{id}/items` | Añadir producto a la despensa |
| PUT | `/pantries/{id}/items/{itemId}` | Editar cantidad, unidad, caducidad... |
| DELETE | `/pantries/{id}/items/{itemId}` | Eliminar producto de la despensa |
| GET | `/pantries/{id}/notifications` | Alertas de caducidad y stock bajo |
| GET | `/pantries/{id}/members` | Miembros con acceso |
| POST | `/pantries/{id}/share` | Generar enlace de invitación |
| POST | `/pantries/shared/{token}` | Unirse a una despensa compartida |

### Listas de la compra

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/shopping-lists` | Listar todas las listas |
| POST | `/shopping-lists` | Crear lista |
| GET | `/shopping-lists/{id}` | Ver lista con sus items |
| PUT | `/shopping-lists/{id}` | Editar lista |
| DELETE | `/shopping-lists/{id}` | Eliminar lista |
| POST | `/shopping-lists/{id}/items` | Añadir producto a la lista |
| PUT | `/shopping-lists/{id}/items/{itemId}/purchased` | Marcar como comprado |
| PUT | `/shopping-lists/{id}/items/{itemId}/unpurchased` | Desmarcar |
| DELETE | `/shopping-lists/{id}/items/{itemId}` | Eliminar item |
| POST | `/shopping-lists/{id}/complete` | Completar lista |
| POST | `/shopping-lists/{id}/move-to-pantry` | Mover items comprados a la despensa |
| GET | `/shopping-lists/{id}/suggestions` | Sugerencias de productos |
| POST | `/shopping-lists/{id}/share` | Generar enlace de invitación |
| POST | `/shopping-lists/shared/{token}` | Unirse a una lista compartida |

### Notificaciones globales

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/notifications` | Todas las alertas del usuario (caducados, por caducar, stock bajo) |

---

## Formato de respuesta

Todas las respuestas siguen el mismo formato:

**Éxito:**
```json
{
    "success": true,
    "message": "Item updated successfully",
    "data": { ... }
}
```

**Error:**
```json
{
    "success": false,
    "message": "Access denied"
}
```

---

## Despliegue con Docker

El proyecto incluye un `docker-compose.yml` con cuatro servicios:

- **app** — PHP + Laravel
- **nginx** — servidor web con HTTPS
- **db** — MySQL 8
- **certbot** — renovación automática del certificado SSL
- **adminer** — panel web de base de datos (puerto 8080)

### Levantar el proyecto

```bash
# Construir e iniciar todos los servicios
docker-compose up -d --build

# Ejecutar migraciones
docker-compose exec app php artisan migrate

# Ver logs
docker-compose logs -f app
```

### Comandos útiles de Laravel

```bash
# Limpiar caché
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear

# Regenerar caché (producción)
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
```

---

## Base de datos

Las migraciones están en `database/migrations/` y crean las siguientes tablas:

| Tabla | Descripción |
|---|---|
| `users` | Usuarios registrados |
| `products` | Catálogo de productos con datos nutricionales |
| `pantries` | Despensas de cada usuario |
| `pantry_items` | Productos dentro de cada despensa |
| `pantry_users` | Relación usuarios ↔ despensas compartidas |
| `shopping_lists` | Listas de la compra |
| `shopping_list_items` | Productos de cada lista |
| `shopping_list_users` | Relación usuarios ↔ listas compartidas |
| `personal_access_tokens` | Tokens de autenticación Sanctum |
| `password_reset_tokens` | Tokens para recuperar contraseña |

---

## Deep links

La app móvil usa el esquema `nutricasa://`. El backend incluye una ruta web que redirige los enlaces compartidos por HTTPS al esquema de la app:

```
https://nutricasa.duckdns.org/pantry/shared/{token}
→ redirige a →
nutricasa://pantry/shared/{token}
```

Esto permite que los enlaces funcionen tanto desde el navegador como desde la app.
