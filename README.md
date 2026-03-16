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

```bash
cd backend
cp .env.example .env
composer install
php artisan key:generate
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate:fresh --seed

## Frontend

Instalar dependencias:

```bash
cd frontend
npm install

Arrancar servidor de desarrollo:

```bash
npm run dev

La aplicación estará disponible en:

```bash
http://localhost:5173
