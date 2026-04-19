<script setup>
import { ref, onMounted, computed } from "vue"
import { getPedidos } from "../api/pedidos"
import { isAdmin } from "../utils/auth"

const admin = isAdmin()

const pedidos = ref([])
const loading = ref(false)

async function loadData() {
  loading.value = true
  try {
    pedidos.value = await getPedidos()
  } catch (error) {
    console.error(error)
  } finally {
    loading.value = false
  }
}

const totalPedidos = computed(() => pedidos.value.length)

const pendientes = computed(() =>
  pedidos.value.filter(p => p.estado === "pendiente").length
)

const produccion = computed(() =>
  pedidos.value.filter(p => p.estado === "produccion").length
)

const enviados = computed(() =>
  pedidos.value.filter(p => p.estado === "enviado").length
)

const entregados = computed(() =>
  pedidos.value.filter(p => p.estado === "entregado").length
)

const ultimosPedidos = computed(() =>
  [...pedidos.value].slice(0, 5)
)

onMounted(loadData)
</script>

<template>
  <section class="space-y-6">

    <div>
      <h1 class="text-2xl font-bold text-slate-800">
        Dashboard
      </h1>
      <p class="text-slate-600">
        Resumen del sistema de producción
      </p>
    </div>

    <!-- MÉTRICAS -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">

      <div class="rounded-2xl bg-white p-4 shadow ring-1 ring-slate-200">
        <p class="text-sm text-slate-500">Total pedidos</p>
        <p class="text-2xl font-bold text-slate-800">{{ totalPedidos }}</p>
      </div>

      <div class="rounded-2xl bg-amber-50 p-4 shadow ring-1 ring-amber-200">
        <p class="text-sm text-amber-600">Pendientes</p>
        <p class="text-2xl font-bold text-amber-700">{{ pendientes }}</p>
      </div>

      <div class="rounded-2xl bg-blue-50 p-4 shadow ring-1 ring-blue-200">
        <p class="text-sm text-blue-600">Producción</p>
        <p class="text-2xl font-bold text-blue-700">{{ produccion }}</p>
      </div>

      <div class="rounded-2xl bg-violet-50 p-4 shadow ring-1 ring-violet-200">
        <p class="text-sm text-violet-600">Enviados</p>
        <p class="text-2xl font-bold text-violet-700">{{ enviados }}</p>
      </div>

      <div class="rounded-2xl bg-green-50 p-4 shadow ring-1 ring-green-200">
        <p class="text-sm text-green-600">Entregados</p>
        <p class="text-2xl font-bold text-green-700">{{ entregados }}</p>
      </div>

    </div>

    <!-- ÚLTIMOS PEDIDOS -->
    <div class="rounded-2xl bg-white p-6 shadow ring-1 ring-slate-200">
      <h2 class="mb-4 text-lg font-semibold text-slate-800">
        Últimos pedidos
      </h2>

      <p v-if="loading" class="text-sm text-slate-500">
        Cargando...
      </p>

      <div v-else class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="text-left text-slate-500 border-b">
              <th class="px-3 py-2">ID</th>
              <th class="px-3 py-2">Cliente</th>
              <th class="px-3 py-2">Estado</th>
              <th class="px-3 py-2">Fecha</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="pedido in ultimosPedidos" :key="pedido.id" class="border-b">
              <td class="px-3 py-2 font-medium">
                #{{ pedido.id }}
              </td>

              <td class="px-3 py-2">
                {{ pedido.programa?.cliente?.nombre || "—" }}
              </td>

              <td class="px-3 py-2">
                <span class="rounded-full px-2 py-1 text-xs font-medium" :class="{
                  'bg-amber-100 text-amber-700': pedido.estado === 'pendiente',
                  'bg-blue-100 text-blue-700': pedido.estado === 'produccion',
                  'bg-violet-100 text-violet-700': pedido.estado === 'enviado',
                  'bg-green-100 text-green-700': pedido.estado === 'entregado',
                }">
                  {{ pedido.estado }}
                </span>
              </td>

              <td class="px-3 py-2">
                {{ pedido.fecha_pedido || "—" }}
              </td>
            </tr>

            <tr v-if="ultimosPedidos.length === 0">
              <td colspan="4" class="px-3 py-4 text-center text-slate-500">
                No hay pedidos.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </section>
</template>