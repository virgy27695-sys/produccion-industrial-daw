# ISAVEX · Sistema Web de Gestión de Producción Industrial Bajo Pedido

Aplicación web fullstack desarrollada como proyecto final de DAW orientada a la gestión de producción industrial bajo pedido en el sector automovilístico.

El sistema simula un entorno industrial real permitiendo gestionar clientes, modelos, piezas, moldes, programas de necesidades, pedidos, planificación, producción, entregas y situación de stock.

---

# Arquitectura del proyecto

El proyecto sigue una arquitectura cliente-servidor desacoplada:

```txt
frontend/ → SPA Vue 3
backend/ → API REST Laravel
```

---

# Tecnologías utilizadas

## Backend

- Laravel
- API REST
- MySQL
- Eloquent ORM
- Laravel Sail
- Docker
- Sanctum (autenticación)

## Frontend

- Vue 3
- Composition API
- Vue Router
- Tailwind CSS
- Lucide Icons
- Vite

---

# Funcionalidades implementadas

## Gestión de clientes

Permite:

- Crear clientes
- Editar clientes
- Eliminar clientes
- Consultar clientes

---

## Gestión de modelos

Permite asociar modelos de vehículo a cada cliente.

Ejemplo:

```txt
Audi → Q3
BMW → Serie 1
```

---

## Gestión de piezas

Permite registrar piezas industriales con:

- Código
- Denominación
- Cliente
- Modelo
- Molde asociado
- Mercado
- Lado
- Categoría funcional
- Stock
- Stock seguridad

---

## Gestión de moldes

Permite gestionar:

- Código de molde
- Número de cavidades
- Estado
- Máquina
- Piezas asociadas

### Lógica implementada

Un mismo molde puede fabricar simultáneamente:

```txt
90112502 → Izquierda
90112503 → Derecha
```

Ejemplo:

```txt
Molde M-AUDI-270
Referencias: 90112502 / 90112503
```

Ambas piezas se fabrican simultáneamente en cada ciclo de inyección.

---

## Gestión de programas

Permite registrar necesidades semanales enviadas por cliente.

Incluye:

- Año
- Semana
- Cantidad
- Cliente asociado

---

## Gestión de pedidos

Permite:

- Crear pedidos
- Editar pedidos
- Eliminar pedidos
- Asociar piezas
- Controlar estados

Estados disponibles:

- Pendiente
- Producción
- Enviado
- Entregado

---

## Producción y planificación

La planificación agrupa necesidades por:

- Molde
- Semana
- Año

Calcula automáticamente:

- Total de piezas
- Número de cavidades
- Ciclos necesarios

---

## Partes de producción

Permite registrar:

- Operario
- Molde
- Máquina
- Turno
- Cantidad fabricada
- Cantidad buena
- Cantidad rechazada
- Averías
- Paradas
- Motivos
- Observaciones

---

## Entregas

Permite registrar:

- Pieza
- Fecha
- Semana
- Año
- Cantidad entregada

---

## Situación y stock

Permite visualizar:

- Stock actual
- Necesidad
- Disponible
- Cobertura
- Estado productivo

Sistema visual mediante semáforo:

🟢 Correcto

🟡 Riesgo

🔴 Crítico

---

# Sistema de roles

El sistema implementa cuatro perfiles:

## Administrador

Acceso completo:

- Gestión total
- Gestión de usuarios
- Configuración

---

## Planificador

Acceso a:

- Clientes
- Modelos
- Piezas
- Moldes
- Programas
- Pedidos
- Producción
- Partes

---

## Encargado

Acceso a:

- Producción
- Planning
- Partes de producción

El encargado accede directamente al planning industrial.

---

## Almacén

Acceso a:

- Pedidos
- Entregas
- Stock
- Situación

---

# API REST

Principales endpoints:

```txt
/api/clientes
/api/modelos
/api/piezas
/api/moldes
/api/programas
/api/programa-detalles
/api/pedidos
/api/partes-produccion
/api/entregas
/api/situacion
/api/produccion/resumen
```

---

# Instalación

## Backend

```bash
cd backend

cp .env.example .env

composer install

./vendor/bin/sail up -d

./vendor/bin/sail artisan key:generate

./vendor/bin/sail artisan migrate:fresh --seed
```

## Frontend

```bash
cd frontend

npm install

npm run dev
```

---

# Estructura principal del proyecto

```txt
Proyecto/

backend/
├── app/
├── database/
├── routes/

frontend/
├── src/
│   ├── api/
│   ├── components/
│   ├── composables/
│   ├── layouts/
│   ├── router/
│   ├── utils/
│   └── views/
```

---

# Estado actual del proyecto

Versión funcional implementada:

✅ CRUD de datos maestros

✅ Producción

✅ Planning industrial

✅ Pedidos

✅ Partes de producción

✅ Entregas

✅ Stock

✅ Sistema de roles

✅ Diseño responsive

✅ Dashboard

---

# Mejoras futuras

## Producción

- Importación automática desde Excel
- Trazabilidad avanzada
- Gestión de operarios
- Registro de incidencias

## Frontend

- Dashboard con gráficos
- Sistema de notificaciones
- Modo oscuro
- PWA

## Backend

- Exportación PDF
- Exportación Excel
- Jobs y colas
- Integración ERP/MES

---

# Autor

Proyecto desarrollado por Virginia Molina como Trabajo Final de DAW.

Orientado a la simulación realista de un entorno industrial de producción bajo pedido.

---

# Licencia

Proyecto educativo.