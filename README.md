# Sistema Web de Gestión de Producción Industrial Bajo Pedido

Proyecto DAW desarrollado con arquitectura cliente-servidor para la gestión de producción industrial bajo pedido en empresas del sector automovilístico.

## Tecnologías utilizadas

### Backend
- Laravel
- MySQL
- Docker

### Frontend
- Vue 3
- Vue Router
- Pinia
- Tailwind CSS
- Vite

## Estructura del proyecto

- `backend/` → API REST desarrollada en Laravel
- `frontend/` → aplicación SPA desarrollada en Vue

## Funcionalidades actuales

- Dashboard con métricas
- Gestión de clientes
- Gestión de modelos
- Visualización de piezas
- Visualización de programas
- Visualización de pedidos
- Notificaciones de acciones
- Diseño responsive

## Instalación y ejecución

### Backend

~~~bash
cd backend
cp .env.example .env
composer install
php artisan key:generate
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate:fresh --seed
~~~

Este comando crea la base de datos del proyecto y carga automáticamente los datos iniciales mediante seeders.

### Frontend

Instalar dependencias:

~~~bash
cd frontend
npm install
~~~

Arrancar servidor de desarrollo:

~~~bash
npm run dev
~~~

La aplicación estará disponible en:

~~~text
http://localhost:5173
~~~

## API

La API REST está disponible en:

~~~text
http://localhost:8080/api
~~~

Endpoints principales:

- `/api/clientes`
- `/api/modelos`
- `/api/piezas`
- `/api/programas`
- `/api/pedidos`
