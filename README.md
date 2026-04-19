# Sistema Web de Gestión de Producción Industrial Bajo Pedido

Proyecto DAW orientado a la gestión de producción industrial en el sector automovilístico, basado en arquitectura cliente-servidor.

---

## Tecnologías utilizadas

### Backend

- Laravel (API REST)
- MySQL
- Docker (Laravel Sail)

### Frontend

- Vue 3
- Vue Router
- Pinia
- Tailwind CSS
- Vite

---

## Estructura del proyecto

- `backend/` → API REST
- `frontend/` → SPA Vue

---

## Funcionalidades actuales

- Dashboard con métricas
- Gestión de clientes
- Gestión de modelos
- Gestión de piezas
- Gestión de moldes
- Gestión de programas
- Gestión de planificación semanal (ProgramaDetalle)
- Gestión de pedidos
- Sistema de roles
- Búsqueda avanzada
- Interfaz responsive

---

## Lógica productiva implementada

El sistema incluye lógica basada en entorno industrial real:

- Clasificación automática de piezas (lado, mercado, categoría)
- Relación piezas-moldes
- Programación semanal de producción
- Estructura preparada para planificación real

---

## Instalación y ejecución

### Backend

```bash
cd backend
cp .env.example .env
composer install
php artisan key:generate
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate:fresh --seed
```
## Endpoints principales

- `/api/clientes`
- `/api/modelos`
- `/api/piezas`
- `/api/moldes`
- `/api/programas`
- `/api/programa-detalles`
- `/api/pedidos`