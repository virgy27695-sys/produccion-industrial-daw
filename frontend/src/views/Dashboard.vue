<script setup>
import { computed, onMounted, ref } from "vue"

import { getClientes } from "../api/clientes"
import { getModelos } from "../api/modelos"
import { getPiezas } from "../api/piezas"
import { getPedidos } from "../api/pedidos"

const clientes = ref(0)
const modelos = ref(0)
const piezas = ref(0)
const pedidos = ref(0)
const pedidosData = ref([])
const error = ref("")

const ultimosPedidos = computed(() => {
  return [...pedidosData.value].slice(0, 5)
})

function totalCantidad(detalles) {
  if (!detalles || !detalles.length) return 0
  return detalles.reduce((total, detalle) => total + Number(detalle.cantidad || 0), 0)
}

onMounted(async () => {
  try {
    const [clientesRes, modelosRes, piezasRes, pedidosRes] = await Promise.all([
      getClientes(),
      getModelos(),
      getPiezas(),
      getPedidos(),
    ])

    clientes.value = clientesRes.length
    modelos.value = modelosRes.length
    piezas.value = piezasRes.length
    pedidos.value = pedidosRes.length
    pedidosData.value = pedidosRes
  } catch (e) {
    error.value = "No se pudo conectar con la API."
    console.error(e)
  }
})
</script>

<template>
  <section class="space-y-6">
    <div>
      <h2 class="text-2xl font-semibold text-slate-800">
        Dashboard de Producción
      </h2>

      <p v-if="error" class="mt-2 text-sm text-red-600">
        {{ error }}
      </p>
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
      <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <h3 class="text-sm text-slate-500">Clientes</h3>
        <p class="mt-2 text-3xl font-bold text-slate-800">{{ clientes }}</p>
      </div>

      <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <h3 class="text-sm text-slate-500">Modelos</h3>
        <p class="mt-2 text-3xl font-bold text-slate-800">{{ modelos }}</p>
      </div>

      <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <h3 class="text-sm text-slate-500">Piezas</h3>
        <p class="mt-2 text-3xl font-bold text-slate-800">{{ piezas }}</p>
      </div>

      <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <h3 class="text-sm text-slate-500">Pedidos</h3>
        <p class="mt-2 text-3xl font-bold text-slate-800">{{ pedidos }}</p>
      </div>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
      <div class="mb-4 flex items-center justify-between">
        <h3 class="text-lg font-semibold text-slate-800">Últimos pedidos</h3>
        <RouterLink
          to="/pedidos"
          class="text-sm font-medium text-blue-600 hover:text-blue-700"
        >
          Ver todos
        </RouterLink>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full border-separate border-spacing-y-2">
          <thead>
            <tr class="text-left text-sm text-slate-500">
              <th class="px-4 py-2">ID</th>
              <th class="px-4 py-2">Cliente</th>
              <th class="px-4 py-2">Estado</th>
              <th class="px-4 py-2">Cantidad total</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="pedido in ultimosPedidos"
              :key="pedido.id"
              class="bg-slate-50 text-slate-800"
            >
              <td class="rounded-l-xl px-4 py-3 font-medium">#{{ pedido.id }}</td>
              <td class="px-4 py-3">
                {{ pedido.programa?.cliente?.nombre || "Sin cliente" }}
              </td>
              <td class="px-4 py-3">
                {{ pedido.estado }}
              </td>
              <td class="rounded-r-xl px-4 py-3">
                {{ totalCantidad(pedido.detalles) }}
              </td>
            </tr>

            <tr v-if="ultimosPedidos.length === 0">
              <td colspan="4" class="px-4 py-6 text-center text-sm text-slate-500">
                No hay pedidos disponibles.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</template>