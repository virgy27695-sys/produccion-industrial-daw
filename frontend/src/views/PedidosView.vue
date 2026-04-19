<script setup>
import { ref, onMounted, computed } from "vue"
import {
  getPedidos,
  createPedido,
  updatePedido,
  deletePedido,
} from "../api/pedidos"
import { apiGet } from "../api/http"
import { isAdmin } from "../utils/auth"

const admin = isAdmin()

const pedidos = ref([])
const programas = ref([])
const piezas = ref([])

const loading = ref(false)
const saving = ref(false)
const error = ref("")
const showForm = ref(false)
const editingId = ref(null)
const search = ref("")

const form = ref({
  programa_id: "",
  fecha_pedido: "",
  estado: "pendiente",
  detalles: [
    {
      pieza_id: "",
      cantidad: 1,
    },
  ],
})

const pedidosFiltrados = computed(() => {
  const term = search.value.toLowerCase().trim()

  if (!term) return pedidos.value

  return pedidos.value.filter((pedido) => {
    const cliente = pedido.programa?.cliente?.nombre?.toLowerCase() || ""
    const estado = pedido.estado?.toLowerCase() || ""
    const id = String(pedido.id)

    return id.includes(term) || cliente.includes(term) || estado.includes(term)
  })
})

async function loadData() {
  loading.value = true
  error.value = ""

  try {
    pedidos.value = await getPedidos()
    programas.value = await apiGet("/programas")
    piezas.value = await apiGet("/piezas")
  } catch (e) {
    error.value = "No se pudieron cargar los pedidos."
    console.error(e)
  } finally {
    loading.value = false
  }
}

function resetForm() {
  form.value = {
    programa_id: "",
    fecha_pedido: "",
    estado: "pendiente",
    detalles: [
      {
        pieza_id: "",
        cantidad: 1,
      },
    ],
  }
}

function openCreate() {
  if (!admin) return

  editingId.value = null
  resetForm()
  showForm.value = true
}

function openEdit(pedido) {
  editingId.value = pedido.id
  form.value = {
    programa_id: pedido.programa_id,
    fecha_pedido: pedido.fecha_pedido || "",
    estado: pedido.estado,
    detalles: pedido.detalles.map((detalle) => ({
      pieza_id: detalle.pieza_id,
      cantidad: detalle.cantidad,
    })),
  }
  showForm.value = true
}

function closeForm() {
  showForm.value = false
}

function addDetalle() {
  if (!admin) return

  form.value.detalles.push({
    pieza_id: "",
    cantidad: 1,
  })
}

function removeDetalle(index) {
  if (!admin) return
  if (form.value.detalles.length === 1) return

  form.value.detalles.splice(index, 1)
}

async function submitForm() {
  saving.value = true
  error.value = ""

  try {
    if (editingId.value) {
      if (admin) {
        await updatePedido(editingId.value, form.value)
      } else {
        const pedidoOriginal = pedidos.value.find((p) => p.id === editingId.value)

        await updatePedido(editingId.value, {
          programa_id: pedidoOriginal.programa_id,
          fecha_pedido: pedidoOriginal.fecha_pedido,
          estado: form.value.estado,
          detalles: pedidoOriginal.detalles.map((detalle) => ({
            pieza_id: detalle.pieza_id,
            cantidad: detalle.cantidad,
          })),
        })
      }
    } else {
      if (!admin) return
      await createPedido(form.value)
    }

    closeForm()
    resetForm()
    await loadData()
  } catch (e) {
    error.value = e.message || "No se pudo guardar el pedido."
    console.error(e)
  } finally {
    saving.value = false
  }
}

async function removePedido(id) {
  if (!admin) return
  if (!confirm("¿Seguro que quieres eliminar este pedido?")) return

  error.value = ""

  try {
    await deletePedido(id)
    await loadData()
  } catch (e) {
    error.value = e.message || "No se pudo eliminar el pedido."
    console.error(e)
  }
}

function getTotalPiezas(detalles) {
  return detalles.reduce((total, detalle) => total + detalle.cantidad, 0)
}

onMounted(loadData)
</script>

<template>
  <section class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
      <div>
        <h2 class="text-2xl font-semibold text-slate-800">Pedidos</h2>
        <p class="text-slate-600">
          Gestión de pedidos generados a partir de programas de necesidades.
        </p>
      </div>

      <div class="flex flex-col gap-3 sm:flex-row">
        <input v-model="search" type="text" placeholder="Buscar por ID, cliente o estado"
          class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200 sm:w-80" />

        <button v-if="admin" @click="openCreate"
          class="rounded-xl bg-slate-800 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700">
          Nuevo pedido
        </button>
      </div>
    </div>

    <p v-if="error" class="text-sm text-red-600">
      {{ error }}
    </p>

    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
      <p v-if="loading" class="text-sm text-slate-500">
        Cargando pedidos...
      </p>

      <div v-else class="grid gap-4">
        <div v-for="pedido in pedidosFiltrados" :key="pedido.id"
          class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
          <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-2">
              <h3 class="text-lg font-semibold text-slate-800">
                Pedido #{{ pedido.id }}
              </h3>

              <p class="text-sm text-slate-600">
                <span class="font-medium">Programa:</span>
                #{{ pedido.programa?.id || "—" }}
              </p>

              <p class="text-sm text-slate-600">
                <span class="font-medium">Cliente:</span>
                {{ pedido.programa?.cliente?.nombre || "Sin cliente" }}
              </p>

              <p class="text-sm text-slate-600">
                <span class="font-medium">Fecha pedido:</span>
                {{ pedido.fecha_pedido || "—" }}
              </p>

              <p class="text-sm text-slate-600">
                <span class="font-medium">Estado:</span>
                <span class="ml-2 rounded-full px-3 py-1 text-xs font-medium" :class="{
                  'bg-amber-100 text-amber-700': pedido.estado === 'pendiente',
                  'bg-blue-100 text-blue-700': pedido.estado === 'produccion',
                  'bg-violet-100 text-violet-700': pedido.estado === 'enviado',
                  'bg-green-100 text-green-700': pedido.estado === 'entregado',
                }">
                  {{ pedido.estado }}
                </span>
              </p>

              <p class="text-sm text-slate-600">
                <span class="font-medium">Observaciones del programa:</span>
                {{ pedido.programa?.observaciones || "Sin observaciones" }}
              </p>

              <p class="text-sm text-slate-600">
                <span class="font-medium">Líneas:</span>
                {{ pedido.detalles.length }}
              </p>

              <p class="text-sm text-slate-600">
                <span class="font-medium">Total piezas:</span>
                {{ getTotalPiezas(pedido.detalles) }}
              </p>
            </div>

            <div class="flex flex-wrap gap-2">
              <button @click="openEdit(pedido)"
                class="rounded-lg border border-amber-300 bg-amber-50 px-3 py-2 text-sm text-amber-700 transition hover:bg-amber-100">
                {{ admin ? "Editar" : "Cambiar estado" }}
              </button>

              <button v-if="admin" @click="removePedido(pedido.id)"
                class="rounded-lg border border-rose-300 bg-rose-50 px-3 py-2 text-sm text-rose-700 transition hover:bg-rose-100">
                Eliminar
              </button>
            </div>
          </div>

          <div class="mt-5 overflow-x-auto">
            <table class="min-w-full border-separate border-spacing-y-2 text-sm">
              <thead>
                <tr class="text-left text-slate-500">
                  <th class="px-4 py-2">Pieza</th>
                  <th class="px-4 py-2">Cantidad</th>
                </tr>
              </thead>

              <tbody>
                <tr v-for="detalle in pedido.detalles" :key="detalle.id" class="bg-white text-slate-800">
                  <td class="rounded-l-xl px-4 py-3">
                    {{ detalle.pieza?.codigo || "—" }} -
                    {{ detalle.pieza?.denominacion || "Sin denominación" }}
                  </td>
                  <td class="rounded-r-xl px-4 py-3">
                    {{ detalle.cantidad }}
                  </td>
                </tr>

                <tr v-if="pedido.detalles.length === 0">
                  <td colspan="2" class="px-4 py-4 text-center text-slate-500">
                    Este pedido no tiene líneas.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div v-if="pedidosFiltrados.length === 0"
          class="rounded-2xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500">
          No hay pedidos registrados.
        </div>
      </div>
    </div>

    <div v-if="showForm" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
      <div class="max-h-[90vh] w-full max-w-4xl overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl">
        <div class="mb-6 flex items-center justify-between">
          <h3 class="text-xl font-bold text-slate-800">
            {{ editingId ? "Editar pedido" : "Nuevo pedido" }}
          </h3>

          <button @click="closeForm" class="rounded-lg px-3 py-2 text-sm text-slate-500 transition hover:bg-slate-100">
            Cerrar
          </button>
        </div>

        <form class="space-y-6" @submit.prevent="submitForm">
          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="mb-1 block text-sm font-medium text-slate-700">
                Programa
              </label>

              <select v-model="form.programa_id" class="w-full rounded-xl border border-slate-300 px-4 py-2"
                :disabled="!admin" required>
                <option value="">Selecciona un programa</option>
                <option v-for="programa in programas" :key="programa.id" :value="programa.id">
                  #{{ programa.id }} - {{ programa.cliente?.nombre || "Sin cliente" }}
                </option>
              </select>
            </div>

            <div>
              <label class="mb-1 block text-sm font-medium text-slate-700">
                Fecha pedido
              </label>

              <input v-model="form.fecha_pedido" type="date" class="w-full rounded-xl border border-slate-300 px-4 py-2"
                :disabled="!admin" />
            </div>

            <div class="md:col-span-2">
              <label class="mb-1 block text-sm font-medium text-slate-700">
                Estado
              </label>

              <select v-model="form.estado" class="w-full rounded-xl border border-slate-300 px-4 py-2" required>
                <option value="pendiente">Pendiente</option>
                <option value="produccion">Producción</option>
                <option value="enviado">Enviado</option>
                <option value="entregado">Entregado</option>
              </select>
            </div>
          </div>

          <div>
            <div class="mb-3 flex items-center justify-between">
              <h4 class="text-lg font-semibold text-slate-800">
                Líneas del pedido
              </h4>

              <button v-if="admin" type="button" @click="addDetalle"
                class="rounded-lg bg-slate-100 px-3 py-2 text-sm transition hover:bg-slate-200">
                Añadir línea
              </button>
            </div>

            <div class="space-y-3">
              <div v-for="(detalle, index) in form.detalles" :key="index"
                class="grid gap-3 rounded-xl border border-slate-200 p-4 md:grid-cols-[1fr_160px_120px]">
                <div>
                  <label class="mb-1 block text-sm font-medium text-slate-700">
                    Pieza
                  </label>

                  <select v-model="detalle.pieza_id" class="w-full rounded-xl border border-slate-300 px-4 py-2"
                    :disabled="!admin" required>
                    <option value="">Selecciona una pieza</option>
                    <option v-for="pieza in piezas" :key="pieza.id" :value="pieza.id">
                      {{ pieza.codigo }} - {{ pieza.denominacion }}
                    </option>
                  </select>
                </div>

                <div>
                  <label class="mb-1 block text-sm font-medium text-slate-700">
                    Cantidad
                  </label>

                  <input v-model.number="detalle.cantidad" type="number" min="1"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2" :disabled="!admin" required />
                </div>

                <div v-if="admin" class="flex items-end">
                  <button type="button" @click="removeDetalle(index)"
                    class="w-full rounded-xl border border-rose-300 bg-rose-50 px-4 py-2 text-sm text-rose-700 transition hover:bg-rose-100">
                    Quitar
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div class="flex justify-end gap-3">
            <button type="button" @click="closeForm" class="rounded-xl border border-slate-300 px-4 py-2 text-sm">
              Cancelar
            </button>

            <button type="submit" :disabled="saving"
              class="rounded-xl bg-slate-800 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700 disabled:opacity-60">
              {{ saving ? "Guardando..." : editingId ? "Actualizar pedido" : "Crear pedido" }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </section>
</template>