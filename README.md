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

- backend/ → API REST
- frontend/ → SPA Vue

---

## Funcionalidades actuales

- Dashboard con métricas
- Gestión de clientes
- Gestión de modelos
- Gestión de piezas
- Gestión de moldes
- Gestión de programas de necesidades
- Gestión de planificación semanal (ProgramaDetalle)
- Gestión de pedidos
- Sistema de roles (admin / usuario)
- Búsqueda avanzada en todas las vistas
- Interfaz responsive

---

## Lógica productiva implementada

El sistema incluye lógica basada en un entorno industrial real:

- Clasificación automática de piezas:
  - lado (izquierda/derecha)
  - mercado
  - categoría funcional
- Relación entre piezas y moldes de producción
- Programación semanal por pieza (programas de cliente)
- Agrupación de producción por molde
- Cálculo de ciclos necesarios según cavidades del molde

---

## Planificación de producción

El sistema permite transformar los programas del cliente en planificación industrial real:

- Agrupación de necesidades por molde y semana
- Cálculo de carga de producción
- Visualización de ciclos necesarios
- Identificación de cuellos de botella

---

## Control de producción (nivel avanzado)

Se ha implementado un sistema de control basado en:

- Programa del cliente
- Fabricación real
- Entregas al cliente
- Stock disponible

### Cálculos realizados

- Consumo diario estimado
- Stock de seguridad (3 días)
- Producción disponible
- Pendiente de entrega

### Sistema de semáforo

El estado de cada pieza se representa mediante:

- 🟢 Correcto → stock suficiente
- 🟡 Medio → riesgo de rotura
- 🔴 Crítico → stock por debajo de seguridad

---

## Instalación y ejecución

### Backend

cd backend  
cp .env.example .env  
composer install  
php artisan key:generate  
./vendor/bin/sail up -d  
./vendor/bin/sail artisan migrate:fresh --seed

### Frontend

cd frontend  
npm install  
npm run dev

---

## API

Base URL:

http://localhost:8080/api

### Endpoints principales

- /api/clientes
- /api/modelos
- /api/piezas
- /api/moldes
- /api/programas
- /api/programa-detalles
- /api/pedidos

### Endpoints avanzados

- /api/produccion/resumen → planificación por molde
- /api/situacion → estado real de producción (semáforo)

---

## Estado del proyecto

El sistema se encuentra en fase funcional avanzada, incluyendo:

- Gestión completa de datos maestros
- Planificación semanal
- Simulación de producción industrial
- Base preparada para integración con Excel de cliente

---

## Futuras mejoras

- Importación automática de programas desde Excel
- Control de stock en tiempo real
- Registro detallado de fabricación
- Registro de entregas
- Alertas automáticas de producción
- Exportación de datos (Excel / PDF)
- Dashboard avanzado con gráficos

---

## Autor

Proyecto desarrollado como trabajo final de DAW, enfocado a la simulación de un entorno real de producción industrial.