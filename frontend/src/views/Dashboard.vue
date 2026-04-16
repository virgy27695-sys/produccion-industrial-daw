<script setup>
import { computed, onMounted, ref } from "vue"
import { RouterLink } from "vue-router"

import { getClientes } from "../api/clientes"
import { getModelos } from "../api/modelos"
import { getPiezas } from "../api/piezas"
import { getPedidos } from "../api/pedidos"
import { getProgramas } from "../api/programas"

const clientes = ref(0)
const modelos = ref(0)
const piezas = ref(0)
const pedidos = ref(0)

const pedidosData = ref([])
const programasData = ref([])

const loading = ref(false)
const error = ref("")

const ultimosPedidos = computed(() => {
  return [...pedidosData.value].slice(0, 5)
})

const ultimosProgramas = computed(() => {
  return [...programasData.value].slice(0, 5)
})

const programasPendientes = computed(() => {
  return programasData.value.filter((programa) => programa.estado === "pendiente").length
})

const pedidosProduccion = computed(() => {
  return pedidosData.value.filter((pedido) => pedido.estado === "produccion").length
})

function totalCantidad(detalles) {
  if (!detalles || !detalles.length) return 0

  return detalles.reduce(
    (total, detalle) => total + Number(detalle.cantidad || 0),
    0
  )
}

function badgePedido(estado) {
  if (estado === "pendiente") {
    return "bg-amber-100 text-amber-700"
  }

  if (estado === "produccion") {
    return "bg-blue-100 text-blue-700"
  }

  if (estado === "enviado") {
    return "bg-violet-100 text-violet-700"
  }

  if (estado === "entregado") {
    return "bg-green-100 text-green-700"
  }

  return "bg-slate-100 text-slate-700"
}

function badgePrograma(estado) {
  if (estado === "pendiente") {
    return "bg-amber-100 text-amber-700"
  }

  if (estado === "aprobado") {
    return "bg-green-100 text-green-700"
  }

  if (estado === "rechazado") {
    return "bg-red-100 text-red-700"
  }

  return "bg-slate-100 text-slate-700"
}

async function loadDashboard() {
  loading.value = true
  error.value = ""

  try {
    const [
      clientesRes,
      modelosRes,
      piezasRes,
      pedidosRes,
      programasRes,
    ] = await Promise.all([
      getClientes(),
      getModelos(),
      getPiezas(),
      getPedidos(),
      getProgramas(),
    ])

    clientes.value = clientesRes.length
    modelos.value = modelosRes.length
    piezas.value = piezasRes.length
    pedidos.value = pedidosRes.length

    pedidosData.value = pedidosRes
    programasData.value = programasRes
  } catch (e) {
    error.value = "No se pudo cargar la información del dashboard."
    console.error(e)
  } finally {
    loading.value = false
  }
}

onMounted(loadDashboard)
</script>

<template>
  <section class="space-y-6">
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
      <div>
        <h2 class="text-2xl font-semibold text-slate-800">
          Dashboard de Producción
        </h2>
        <p class="mt-1 text-sm text-slate-500">
          Resumen general del sistema industrial.
        </p>
      </div>

      <div class="flex flex-wrap gap-2">
        <RouterLink to="/clientes"
          class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">
          Ver clientes
        </RouterLink>

        <RouterLink to="/pedidos"
          class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700">
          Ver pedidos
        </RouterLink>
      </div>
    </div>

    <p v-if="error" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
      {{ error }}
    </p>

    <div v-if="loading" class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
      <p class="text-sm text-slate-500">Cargando dashboard...</p>
    </div>

    <template v-else>
      <!-- Métricas principales -->
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
          <p class="text-sm font-medium text-slate-500">Clientes</p>
          <p class="mt-3 text-3xl font-bold text-slate-800">{{ clientes }}</p>
          <p class="mt-2 text-xs text-slate-400">Empresas registradas</p>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
          <p class="text-sm font-medium text-slate-500">Modelos</p>
          <p class="mt-3 text-3xl font-bold text-slate-800">{{ modelos }}</p>
          <p class="mt-2 text-xs text-slate-400">Modelos asociados a clientes</p>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
          <p class="text-sm font-medium text-slate-500">Piezas</p>
          <p class="mt-3 text-3xl font-bold text-slate-800">{{ piezas }}</p>
          <p class="mt-2 text-xs text-slate-400">Referencias productivas</p>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
          <p class="text-sm font-medium text-slate-500">Pedidos</p>
          <p class="mt-3 text-3xl font-bold text-slate-800">{{ pedidos }}</p>
          <p class="mt-2 text-xs text-slate-400">Pedidos totales registrados</p>
        </div>
      </div>

      <!-- Resumen rápido -->
      <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
          <p class="text-sm font-medium text-slate-500">Programas pendientes</p>
          <p class="mt-3 text-2xl font-bold text-amber-600">{{ programasPendientes }}</p>
          <p class="mt-2 text-sm text-slate-500">
            Programas que requieren seguimiento o aprobación.
          </p>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
          <p class="text-sm font-medium text-slate-500">Pedidos en producción</p>
          <p class="mt-3 text-2xl font-bold text-blue-600">{{ pedidosProduccion }}</p>
          <p class="mt-2 text-sm text-slate-500">
            Pedidos activos actualmente en proceso.
          </p>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
          <p class="text-sm font-medium text-slate-500">Estado del sistema</p>
          <p class="mt-3 text-2xl font-bold text-green-600">Operativo</p>
          <p class="mt-2 text-sm text-slate-500">
            Backend y frontend conectados correctamente.
          </p>
        </div>
      </div>

      <!-- Tablas resumen -->
      <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <!-- Últimos pedidos -->
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
          <div class="mb-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-slate-800">Últimos pedidos</h3>
            <RouterLink to="/pedidos" class="text-sm font-medium text-blue-600 hover:text-blue-700">
              Ver todos
            </RouterLink>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full border-separate border-spacing-y-2">
              <thead>
                <tr class="text-left text-sm text-slate-500">
                  <th class="px-3 py-2">ID</th>
                  <th class="px-3 py-2">Cliente</th>
                  <th class="px-3 py-2">Estado</th>
                  <th class="px-3 py-2">Cantidad</th>
                </tr>
              </thead>

              <tbody>
                <tr v-for="pedido in ultimosPedidos" :key="pedido.id" class="bg-slate-50 text-slate-800">
                  <td class="rounded-l-xl px-3 py-3 font-medium">#{{ pedido.id }}</td>
                  <td class="px-3 py-3">
                    {{ pedido.programa?.cliente?.nombre || "Sin cliente" }}
                  </td>
                  <td class="px-3 py-3">
                    <span class="rounded-full px-3 py-1 text-xs font-medium" :class="badgePedido(pedido.estado)">
                      {{ pedido.estado }}
                    </span>
                  </td>
                  <td class="rounded-r-xl px-3 py-3">
                    {{ totalCantidad(pedido.detalles) }}
                  </td>
                </tr>

                <tr v-if="ultimosPedidos.length === 0">
                  <td colspan="4" class="px-3 py-6 text-center text-sm text-slate-500">
                    No hay pedidos disponibles.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Últimos programas -->
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
          <div class="mb-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-slate-800">Últimos programas</h3>
            <RouterLink to="/programas" class="text-sm font-medium text-blue-600 hover:text-blue-700">
              Ver todos
            </RouterLink>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full border-separate border-spacing-y-2">
              <thead>
                <tr class="text-left text-sm text-slate-500">
                  <th class="px-3 py-2">ID</th>
                  <th class="px-3 py-2">Cliente</th>
                  <th class="px-3 py-2">Entrega</th>
                  <th class="px-3 py-2">Estado</th>
                </tr>
              </thead>

              <tbody>
                <tr v-for="programa in ultimosProgramas" :key="programa.id" class="bg-slate-50 text-slate-800">
                  <td class="rounded-l-xl px-3 py-3 font-medium">#{{ programa.id }}</td>
                  <td class="px-3 py-3">
                    {{ programa.cliente?.nombre || "Sin cliente" }}
                  </td>
                  <td class="px-3 py-3">
                    {{ programa.fecha_entrega || "—" }}
                  </td>
                  <td class="rounded-r-xl px-3 py-3">
                    <span class="rounded-full px-3 py-1 text-xs font-medium" :class="badgePrograma(programa.estado)">
                      {{ programa.estado }}
                    </span>
                  </td>
                </tr>

                <tr v-if="ultimosProgramas.length === 0">
                  <td colspan="4" class="px-3 py-6 text-center text-sm text-slate-500">
                    No hay programas disponibles.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Accesos rápidos -->
      <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <h3 class="mb-4 text-lg font-semibold text-slate-800">Accesos rápidos</h3>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
          <RouterLink to="/clientes"
            class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm font-medium text-slate-700 transition hover:bg-slate-100">
            Gestionar clientes
          </RouterLink>

          <RouterLink to="/modelos"
            class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm font-medium text-slate-700 transition hover:bg-slate-100">
            Gestionar modelos
          </RouterLink>

          <RouterLink to="/piezas"
            class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm font-medium text-slate-700 transition hover:bg-slate-100">
            Consultar piezas
          </RouterLink>

          <RouterLink to="/pedidos"
            class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm font-medium text-slate-700 transition hover:bg-slate-100">
            Revisar pedidos
          </RouterLink>
        </div>
      </div>
    </template>
  </section>
</template>