<script setup>
import { computed, onMounted, ref } from "vue"
import { getPedidos } from "../api/pedidos"

const pedidos = ref([])
const loading = ref(false)
const error = ref("")
const busqueda = ref("")
const abiertos = ref([])

const pedidosFiltrados = computed(() => {
  const texto = busqueda.value.trim().toLowerCase()

  if (!texto) return pedidos.value

  return pedidos.value.filter((pedido) => {
    const cliente = pedido.programa?.cliente?.nombre?.toLowerCase() || ""
    const estado = pedido.estado?.toLowerCase() || ""
    const id = String(pedido.id)

    return (
      id.includes(texto) ||
      cliente.includes(texto) ||
      estado.includes(texto)
    )
  })
})

function totalCantidad(detalles) {
  if (!detalles || !detalles.length) return 0
  return detalles.reduce((total, detalle) => total + Number(detalle.cantidad || 0), 0)
}

function estaAbierto(id) {
  return abiertos.value.includes(id)
}

function toggleDetalle(id) {
  if (estaAbierto(id)) {
    abiertos.value = abiertos.value.filter((item) => item !== id)
  } else {
    abiertos.value.push(id)
  }
}

async function loadPedidos() {
  loading.value = true
  error.value = ""

  try {
    pedidos.value = await getPedidos()
  } catch (e) {
    error.value = "No se pudieron cargar los pedidos."
    console.error(e)
  } finally {
    loading.value = false
  }
}

onMounted(loadPedidos)
</script>

<template>
  <section class="space-y-6">
    <div>
      <h2 class="mb-4 text-2xl font-semibold text-slate-800">Pedidos</h2>
      <p class="text-slate-600">
        Consulta de pedidos de producción asociados a programas y clientes.
      </p>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
      <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h3 class="text-lg font-semibold text-slate-800">Listado de pedidos</h3>

        <input
          v-model="busqueda"
          type="text"
          placeholder="Buscar por ID, cliente o estado"
          class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200 sm:max-w-sm"
        />
      </div>

      <p v-if="error" class="mb-4 text-sm text-red-600">
        {{ error }}
      </p>

      <p v-if="loading" class="text-sm text-slate-500">
        Cargando pedidos...
      </p>

      <div v-else class="space-y-4">
        <div
          v-for="pedido in pedidosFiltrados"
          :key="pedido.id"
          class="rounded-2xl border border-slate-200 bg-slate-50"
        >
          <div class="flex flex-col gap-4 p-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="grid flex-1 grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
              <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Pedido</p>
                <p class="mt-1 font-semibold text-slate-800">#{{ pedido.id }}</p>
              </div>

              <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Cliente</p>
                <p class="mt-1 text-slate-800">
                  {{ pedido.programa?.cliente?.nombre || "Sin cliente" }}
                </p>
              </div>

              <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Fecha</p>
                <p class="mt-1 text-slate-800">
                  {{ pedido.fecha_pedido || "—" }}
                </p>
              </div>

              <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Estado</p>
                <p class="mt-1">
                  <span
                    class="rounded-full px-3 py-1 text-xs font-medium"
                    :class="{
                      'bg-amber-100 text-amber-700': pedido.estado === 'pendiente',
                      'bg-blue-100 text-blue-700': pedido.estado === 'produccion',
                      'bg-violet-100 text-violet-700': pedido.estado === 'enviado',
                      'bg-green-100 text-green-700': pedido.estado === 'entregado',
                    }"
                  >
                    {{ pedido.estado }}
                  </span>
                </p>
              </div>

              <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Cantidad total</p>
                <p class="mt-1 font-semibold text-slate-800">
                  {{ totalCantidad(pedido.detalles) }}
                </p>
              </div>
            </div>

            <div>
              <button
                @click="toggleDetalle(pedido.id)"
                class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100"
              >
                {{ estaAbierto(pedido.id) ? "Ocultar detalle" : "Ver detalle" }}
              </button>
            </div>
          </div>

          <div
            v-if="estaAbierto(pedido.id)"
            class="border-t border-slate-200 bg-white p-4"
          >
            <h4 class="mb-3 text-sm font-semibold text-slate-700">Detalle del pedido</h4>

            <div class="overflow-x-auto">
              <table class="min-w-full">
                <thead>
                  <tr class="text-left text-sm text-slate-500">
                    <th class="px-3 py-2">Código</th>
                    <th class="px-3 py-2">Pieza</th>
                    <th class="px-3 py-2">Cantidad</th>
                  </tr>
                </thead>

                <tbody>
                  <tr
                    v-for="detalle in pedido.detalles"
                    :key="detalle.id"
                    class="border-t border-slate-100 text-slate-800"
                  >
                    <td class="px-3 py-3 font-medium">
                      {{ detalle.pieza?.codigo || "—" }}
                    </td>
                    <td class="px-3 py-3">
                      {{ detalle.pieza?.denominacion || "—" }}
                    </td>
                    <td class="px-3 py-3">
                      {{ detalle.cantidad }}
                    </td>
                  </tr>

                  <tr v-if="!pedido.detalles || pedido.detalles.length === 0">
                    <td colspan="3" class="px-3 py-4 text-sm text-slate-500">
                      Este pedido no tiene líneas de detalle.
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div
          v-if="pedidosFiltrados.length === 0"
          class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-6 text-center text-sm text-slate-500"
        >
          No hay pedidos registrados.
        </div>
      </div>
    </div>
  </section>
</template>