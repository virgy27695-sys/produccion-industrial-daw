# ISAVEX · Sistema Web de Gestión de Producción Industrial Bajo Pedido

Aplicación web fullstack orientada a la gestión de producción industrial en el sector automovilístico, desarrollada como proyecto final de DAW.

El sistema simula un entorno real de planificación industrial bajo pedido, incluyendo control de programas de cliente, producción, moldes, trazabilidad, pedidos y situación de stock.

---

## Arquitectura del proyecto

El sistema sigue una arquitectura cliente-servidor desacoplada:

```txt
frontend/   → SPA Vue 3
backend/    → API REST Laravel
```

---

## Tecnologías utilizadas

### Backend

- Laravel
- API REST
- MySQL
- Eloquent ORM
- Laravel Sail
- Docker

### Frontend

- Vue 3
- Composition API
- Vue Router
- Pinia
- Tailwind CSS
- Lucide Icons
- Vite

---

## Características principales

- Dashboard operativo
- Gestión de clientes
- Gestión de modelos
- Gestión de piezas
- Gestión de moldes
- Gestión de programas de necesidades
- Gestión de planificación semanal
- Gestión de pedidos
- Gestión de producción
- Situación de stock y entregas
- Sistema de roles
- Búsqueda avanzada en vistas principales
- Interfaz responsive
- Componentes reutilizables
- Diálogos de confirmación modernos

---

## Arquitectura frontend

El frontend se ha estructurado mediante componentes reutilizables y composables para reducir duplicación y mejorar el mantenimiento.

### Componentes UI reutilizables

```txt
src/components/ui/
```

Incluye:

- AppCard
- PageHeader
- StatCard
- StatusBadge
- EmptyState
- SearchInput
- BaseInput
- BaseSelect
- BaseButton
- ActionButtons
- ConfirmDialog

### Componentes de tabla

```txt
src/components/ui/table/
```

Incluye:

- DataTable
- TableLoading
- TableEmpty

### Composable CRUD reutilizable

```txt
src/composables/useCrud.js
```

Este composable centraliza:

- Estado de carga
- Estado de guardado
- Errores generales
- Errores de formulario
- Lógica async reutilizable

---

## Diseño responsive

La aplicación incluye:

- Layout adaptable
- Sidebar responsive
- Sidebar tipo drawer en móvil
- Header optimizado
- Footer reutilizable
- Cards móviles
- Tablas responsive
- Formularios adaptados
- Acciones con iconos
- Modales de confirmación

---

## Funcionalidades industriales implementadas

### Gestión de clientes

Permite crear, editar, eliminar y consultar clientes industriales.

### Gestión de modelos

Permite asociar modelos de vehículo a cada cliente.

### Gestión de piezas

Permite registrar piezas fabricadas con información técnica y productiva:

- Código
- Denominación
- Cliente
- Modelo
- Molde
- Lado de pieza
- Mercado
- Categoría funcional

### Gestión de moldes

Permite consultar moldes de producción, cavidades, estado, máquina asociada y piezas vinculadas.

### Gestión de programas

Permite gestionar necesidades semanales enviadas por clientes.

### Gestión de pedidos

Permite controlar pedidos asociados a programas productivos, piezas y estados de fabricación.

### Producción

Permite visualizar planificación industrial agrupada por molde y semana.

### Situación

Permite controlar la situación real de cada pieza en base a:

- Programa
- Fabricación
- Entregas
- Stock actual
- Stock de seguridad
- Disponible
- Pendiente

---

## Lógica productiva implementada

El sistema incluye lógica basada en un entorno industrial real:

- Clasificación automática de piezas
- Detección de lado de pieza
- Detección de mercado
- Detección de categoría funcional
- Relación entre piezas y moldes
- Programación semanal por pieza
- Agrupación de producción por molde
- Cálculo de ciclos necesarios según cavidades del molde
- Control de stock y disponibilidad
- Semáforo de situación productiva

---

## Sistema de semáforo

Cada pieza se clasifica automáticamente según su situación:

| Estado      | Significado                   |
| ----------- | ----------------------------- |
| 🟢 Correcto | Stock suficiente              |
| 🟡 Medio    | Riesgo de rotura              |
| 🔴 Crítico  | Stock por debajo de seguridad |

---

## Seguridad y roles

El sistema contempla dos tipos de usuario:

- Administrador
- Usuario estándar

El administrador puede realizar operaciones CRUD completas.

El usuario estándar puede consultar información y realizar acciones limitadas según su rol.

---

## API REST

### Base URL

```txt
http://localhost:8080/api
```

### Endpoints principales

```txt
/api/clientes
/api/modelos
/api/piezas
/api/moldes
/api/programas
/api/programa-detalles
/api/pedidos
```

### Endpoints avanzados

```txt
/api/produccion/resumen
/api/situacion
```

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

### Frontend

```bash
cd frontend
npm install
npm run dev
```

---

## Estructura principal del proyecto

```txt
Proyecto/
│
├── backend/
│   ├── app/
│   ├── database/
│   ├── routes/
│   └── ...
│
└── frontend/
    ├── src/
    │   ├── api/
    │   ├── assets/
    │   ├── components/
    │   │   ├── ui/
    │   │   └── ui/table/
    │   ├── composables/
    │   ├── layouts/
    │   ├── router/
    │   ├── stores/
    │   ├── utils/
    │   └── views/
    └── ...
```

---

## Estado actual del proyecto

El sistema se encuentra en fase funcional avanzada e incluye:

- CRUD completo de datos maestros
- Planificación semanal
- Simulación de producción industrial
- Control de pedidos
- Control de situación productiva
- Sistema de roles
- Arquitectura frontend modular
- Componentes reutilizables
- Composable CRUD
- Interfaz responsive
- Diálogos de confirmación modernos
- Base preparada para integración con Excel de cliente

---

## Futuras mejoras

### Producción

- Importación automática de programas desde Excel
- Control de fabricación en tiempo real
- Registro de incidencias
- Gestión de operarios
- Trazabilidad avanzada

### Frontend

- Dashboard con gráficos avanzados
- Sistema de notificaciones
- Modo oscuro
- PWA
- Optimización móvil avanzada

### Backend

- Exportación PDF
- Exportación Excel
- Jobs y colas
- Cache
- Logs industriales
- Integración con ERP o MES

---

## Autor

Proyecto desarrollado como Trabajo Final de DAW.

Orientado a la simulación realista de un entorno industrial de producción bajo pedido en automoción.

---

## Licencia

Proyecto educativo.
