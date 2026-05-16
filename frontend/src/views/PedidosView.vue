<script setup>
// IMPORTS
import { ref, onMounted, computed } from "vue"
import { useHead } from "@vueuse/head"

import {
  Pencil,
  Trash2,
  Plus,
  Search,
} from "lucide-vue-next"

import {
  getPedidos,
  createPedido,
  updatePedido,
  deletePedido,
} from "../api/pedidos"

import { apiGet } from "../api/http"
import { isAdmin } from "../utils/auth"

// COMPOSABLE CRUD
import { useCrud } from "../composables/useCrud"


// CONFIGURACIÓN SEO
useHead({
  title: "Pedidos · ISAVEX",
})


// ROLES
const admin = isAdmin()


// ESTADO PRINCIPAL
const pedidos = ref([])
const programas = ref([])
const piezas = ref([])

const showForm = ref(false)
const editingId = ref(null)
const search = ref("")


// CRUD GLOBAL
const {
  loading,
  saving,
  error,
  formError,
  executeLoad,
  executeSave,
  clearErrors,
} = useCrud()


// FORMULARIO
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


// FILTRADO DE PEDIDOS
const pedidosFiltrados = computed(() => {
  const term = search.value.toLowerCase().trim()

  if (!term) return pedidos.value

  return pedidos.value.filter((pedido) => {
    const cliente = pedido.programa?.cliente?.nombre?.toLowerCase() || ""
    const estado = pedido.estado?.toLowerCase() || ""
    const id = String(pedido.id)

    return (
      id.includes(term) ||
      cliente.includes(term) ||
      estado.includes(term)
    )
  })
})


// KPIS
const totalPedidos = computed(() => pedidos.value.length)

const pedidosProduccion = computed(() =>
  pedidos.value.filter((pedido) => pedido.estado === "produccion").length
)

const pedidosEntregados = computed(() =>
  pedidos.value.filter((pedido) => pedido.estado === "entregado").length
)


// CARGA DE DATOS
// Promise.allSettled evita romper toda la vista
// si una de las peticiones falla.
async function loadData() {
  await executeLoad(async () => {
    const results = await Promise.allSettled([
      getPedidos(),
      apiGet("/programas"),
      apiGet("/piezas"),
    ])

    const [
      pedidosResult,
      programasResult,
      piezasResult,
    ] = results

    pedidos.value =
      pedidosResult.status === "fulfilled" && Array.isArray(pedidosResult.value)
        ? pedidosResult.value
        : []

    programas.value =
      programasResult.status === "fulfilled" && Array.isArray(programasResult.value)
        ? programasResult.value
        : []

    piezas.value =
      piezasResult.status === "fulfilled" && Array.isArray(piezasResult.value)
        ? piezasResult.value
        : []

    const hasErrors = results.some(
      (result) => result.status === "rejected"
    )

    if (hasErrors) {
      error.value = "Algunos datos no pudieron cargarse correctamente."
    }
  })
}


// REINICIAR FORMULARIO
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

  clearErrors()
}


// ABRIR MODAL CREACIÓN
function openCreate() {
  if (!admin) return

  editingId.value = null
  resetForm()
  showForm.value = true
}


// ABRIR MODAL EDICIÓN
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

  clearErrors()
  showForm.value = true
}


// CERRAR MODAL
function closeForm() {
  showForm.value = false
}


// AÑADIR LÍNEA
function addDetalle() {
  if (!admin) return

  form.value.detalles.push({
    pieza_id: "",
    cantidad: 1,
  })
}


// QUITAR LÍNEA
function removeDetalle(index) {
  if (!admin) return
  if (form.value.detalles.length === 1) return

  form.value.detalles.splice(index, 1)
}


// GUARDAR PEDIDO
async function submitForm() {
  clearErrors()

  if (!form.value.programa_id) {
    formError.value = "Debes seleccionar un programa."
    return
  }

  if (!form.value.estado) {
    formError.value = "Debes seleccionar un estado."
    return
  }

  if (!form.value.detalles.length) {
    formError.value = "Debes añadir al menos una línea."
    return
  }

  try {
    await executeSave(async () => {
      if (editingId.value) {
        if (admin) {
          await updatePedido(editingId.value, form.value)
        } else {
          const pedidoOriginal = pedidos.value.find(
            (pedido) => pedido.id === editingId.value
          )

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
    })
  } catch (e) {
    formError.value = e.message || "No se pudo guardar el pedido."
    console.error(e)
  }
}


// ELIMINAR PEDIDO
async function removePedido(id) {
  if (!admin) return

  const confirmacion = window.confirm(
    "¿Seguro que quieres eliminar este pedido?"
  )

  if (!confirmacion) return

  try {
    await deletePedido(id)
    await loadData()
  } catch (e) {
    error.value = e.message || "No se pudo eliminar el pedido."
    console.error(e)
  }
}


// TOTAL DE PIEZAS
function getTotalPiezas(detalles = []) {
  return detalles.reduce(
    (total, detalle) => total + Number(detalle.cantidad || 0),
    0
  )
}


// CLASE VISUAL DE ESTADO
function estadoClass(estado) {
  return {
    "bg-amber-100 text-amber-700 ring-amber-200": estado === "pendiente",
    "bg-cyan-100 text-cyan-700 ring-cyan-200": estado === "produccion",
    "bg-violet-100 text-violet-700 ring-violet-200": estado === "enviado",
    "bg-green-100 text-green-700 ring-green-200": estado === "entregado",
  }
}


// INICIALIZACIÓN
onMounted(loadData)
</script>

<template>
  <section class="space-y-6">
    <!-- HERO -->
    <div
      class="relative overflow-hidden rounded-[2rem] border border-white/70 bg-white/75 p-6 shadow-xl shadow-slate-300/40 backdrop-blur-xl">
      <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-[#59C7D8]/20 blur-3xl"></div>

      <div class="relative z-10 flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">
        <div>
          <div
            class="mb-3 inline-flex rounded-full border border-[#59C7D8]/40 bg-white/70 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.25em] text-[#1597A8]">
            Gestión industrial
          </div>

          <h1 class="text-3xl font-bold text-[#081426]">
            Pedidos
          </h1>

          <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600">
            Control y seguimiento de pedidos asociados a programas productivos, piezas y estados de fabricación.
          </p>
        </div>

        <div class="grid gap-3 sm:grid-cols-3">
          <div class="rounded-3xl border border-white/70 bg-white/80 p-5 shadow-sm backdrop-blur">
            <p class="text-xs uppercase tracking-[0.18em] text-slate-400">
              Total
            </p>

            <p class="mt-2 text-3xl font-bold text-[#081426]">
              {{ totalPedidos }}
            </p>
          </div>

          <div class="rounded-3xl border border-cyan-200/60 bg-cyan-50/80 p-5 shadow-sm">
            <p class="text-xs uppercase tracking-[0.18em] text-cyan-500">
              Producción
            </p>

            <p class="mt-2 text-3xl font-bold text-cyan-700">
              {{ pedidosProduccion }}
            </p>
          </div>

          <div class="rounded-3xl border border-green-200/60 bg-green-50/80 p-5 shadow-sm">
            <p class="text-xs uppercase tracking-[0.18em] text-green-500">
              Entregados
            </p>

            <p class="mt-2 text-3xl font-bold text-green-700">
              {{ pedidosEntregados }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- TOOLBAR -->
    <div
      class="flex flex-col gap-4 rounded-[1.75rem] border border-white/70 bg-white/75 p-5 shadow-lg shadow-slate-300/30 backdrop-blur-xl lg:flex-row lg:items-center lg:justify-between">
      <div
        class="flex w-full items-center gap-3 rounded-2xl border border-slate-200/70 bg-white/80 px-4 py-3 shadow-sm lg:max-w-md">
        <Search class="h-5 w-5 text-slate-400" />

        <input v-model="search" type="text" placeholder="Buscar pedido, cliente o estado..."
          class="w-full bg-transparent text-sm text-slate-700 outline-none placeholder:text-slate-400" />
      </div>

      <div class="flex items-center gap-3">
        <div
          class="hidden rounded-2xl border border-[#59C7D8]/30 bg-cyan-50/70 px-4 py-2 text-sm font-medium text-cyan-700 md:block">
          {{ pedidosFiltrados.length }} resultados
        </div>

        <button v-if="admin" @click="openCreate"
          class="rounded-2xl bg-[#59C7D8] px-5 py-3 text-sm font-semibold text-[#081426] shadow-lg shadow-cyan-200/60 transition hover:bg-[#49B3C2]">
          Nuevo pedido
        </button>
      </div>
    </div>

    <!-- ERROR -->
    <p v-if="error || formError"
      class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
      {{ error || formError }}
    </p>

    <!-- LISTADO -->
    <div class="rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-xl shadow-slate-300/30 backdrop-blur-xl">
      <p v-if="loading" class="text-sm text-slate-500">
        Cargando pedidos...
      </p>

      <div v-else class="grid gap-4">
        <div v-for="pedido in pedidosFiltrados" :key="pedido.id"
          class="rounded-[1.75rem] border border-white/70 bg-white/70 p-5 shadow-sm backdrop-blur transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
          <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-3">
              <div class="flex flex-wrap items-center gap-3">
                <h3 class="text-lg font-bold text-[#081426]">
                  Pedido #{{ pedido.id }}
                </h3>

                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold ring-1"
                  :class="estadoClass(pedido.estado)">
                  {{ pedido.estado }}
                </span>
              </div>

              <div class="grid gap-2 text-sm text-slate-600 sm:grid-cols-2 lg:grid-cols-3">
                <p>
                  <span class="font-semibold text-slate-800">Programa:</span>
                  #{{ pedido.programa?.id || "—" }}
                </p>

                <p>
                  <span class="font-semibold text-slate-800">Cliente:</span>
                  {{ pedido.programa?.cliente?.nombre || "Sin cliente" }}
                </p>

                <p>
                  <span class="font-semibold text-slate-800">Fecha:</span>
                  {{ pedido.fecha_pedido || "—" }}
                </p>

                <p>
                  <span class="font-semibold text-slate-800">Líneas:</span>
                  {{ pedido.detalles.length }}
                </p>

                <p>
                  <span class="font-semibold text-slate-800">Total piezas:</span>
                  {{ getTotalPiezas(pedido.detalles) }}
                </p>

                <p>
                  <span class="font-semibold text-slate-800">Observaciones:</span>
                  {{ pedido.programa?.observaciones || "Sin observaciones" }}
                </p>
              </div>
            </div>

            <!-- ACCIONES -->
            <div class="flex flex-wrap gap-2">
              <button @click="openEdit(pedido)"
                class="flex h-11 w-11 items-center justify-center rounded-2xl border border-amber-200 bg-amber-50 text-amber-700 transition hover:bg-amber-100"
                :title="admin ? 'Editar pedido' : 'Cambiar estado'">
                <Pencil class="h-4 w-4" />
              </button>

              <button v-if="admin" @click="removePedido(pedido.id)"
                class="flex h-11 w-11 items-center justify-center rounded-2xl border border-rose-200 bg-rose-50 text-rose-700 transition hover:bg-rose-100"
                title="Eliminar pedido">
                <Trash2 class="h-4 w-4" />
              </button>
            </div>
          </div>

          <!-- DETALLES -->
          <div class="mt-5 overflow-x-auto">
            <table class="min-w-full border-separate border-spacing-y-2 text-sm">
              <thead>
                <tr class="text-left text-xs uppercase tracking-[0.18em] text-slate-400">
                  <th class="px-4 py-2">Pieza</th>
                  <th class="px-4 py-2">Molde</th>
                  <th class="px-4 py-2">Lado</th>
                  <th class="px-4 py-2">Categoría</th>
                  <th class="px-4 py-2">Cantidad</th>
                </tr>
              </thead>

              <tbody>
                <tr v-for="detalle in pedido.detalles" :key="detalle.id" class="bg-white/90 text-slate-800 shadow-sm">
                  <td class="rounded-l-2xl px-4 py-3">
                    <span class="font-bold text-[#081426]">
                      {{ detalle.pieza?.codigo || "—" }}
                    </span>

                    <span class="text-slate-500">
                      - {{ detalle.pieza?.denominacion || "Sin denominación" }}
                    </span>
                  </td>

                  <td class="px-4 py-3">
                    {{ detalle.pieza?.molde?.codigo || "Sin molde" }}
                  </td>

                  <td class="px-4 py-3">
                    {{ detalle.pieza?.lado_pieza || "—" }}
                  </td>

                  <td class="px-4 py-3">
                    {{ detalle.pieza?.categoria_funcional || "—" }}
                  </td>

                  <td class="rounded-r-2xl px-4 py-3 font-bold text-[#081426]">
                    {{ detalle.cantidad }}
                  </td>
                </tr>

                <tr v-if="pedido.detalles.length === 0">
                  <td colspan="5" class="rounded-2xl bg-slate-50 px-4 py-6 text-center text-slate-500">
                    Este pedido no tiene líneas.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- VACÍO -->
        <div v-if="pedidosFiltrados.length === 0"
          class="rounded-[1.75rem] border border-dashed border-[#59C7D8]/40 bg-white/60 p-10 text-center text-sm text-slate-500">
          No hay pedidos registrados.
        </div>
      </div>
    </div>

    <!-- MODAL -->
    <div v-if="showForm"
      class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4 backdrop-blur-sm">
      <div
        class="max-h-[90vh] w-full max-w-4xl overflow-y-auto rounded-[2rem] border border-white/70 bg-white/95 p-6 shadow-2xl shadow-slate-900/20 backdrop-blur-xl">
        <div class="mb-6 flex items-center justify-between gap-4">
          <div>
            <h3 class="text-xl font-bold text-[#081426]">
              {{ editingId ? "Editar pedido" : "Nuevo pedido" }}
            </h3>

            <p class="text-sm text-slate-500">
              {{ editingId ? "Actualiza la información del pedido." : "Crea un pedido asociado a un programa." }}
            </p>
          </div>

          <button @click="closeForm"
            class="rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-500 transition hover:bg-slate-50">
            Cerrar
          </button>
        </div>

        <form class="space-y-6" @submit.prevent="submitForm">
          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="mb-1 block text-sm font-semibold text-slate-700">
                Programa
              </label>

              <select v-model="form.programa_id" class="isavex-input" :disabled="!admin" required>
                <option value="">Selecciona un programa</option>

                <option v-for="programa in programas" :key="programa.id" :value="programa.id">
                  #{{ programa.id }} - {{ programa.cliente?.nombre || "Sin cliente" }}
                </option>
              </select>
            </div>

            <div>
              <label class="mb-1 block text-sm font-semibold text-slate-700">
                Fecha pedido
              </label>

              <input v-model="form.fecha_pedido" type="date" class="isavex-input" :disabled="!admin" />
            </div>

            <div class="md:col-span-2">
              <label class="mb-1 block text-sm font-semibold text-slate-700">
                Estado
              </label>

              <select v-model="form.estado" class="isavex-input" required>
                <option value="pendiente">Pendiente</option>
                <option value="produccion">Producción</option>
                <option value="enviado">Enviado</option>
                <option value="entregado">Entregado</option>
              </select>
            </div>
          </div>

          <!-- LÍNEAS -->
          <div>
            <div class="mb-3 flex items-center justify-between">
              <h4 class="text-lg font-bold text-[#081426]">
                Líneas del pedido
              </h4>

              <button v-if="admin" type="button" @click="addDetalle"
                class="rounded-2xl border border-[#59C7D8]/40 bg-cyan-50 px-4 py-2 text-sm font-semibold text-cyan-700 transition hover:bg-cyan-100">
                <div class="flex items-center gap-2">
                  <Plus class="h-4 w-4" />
                  <span>Añadir línea</span>
                </div>
              </button>
            </div>

            <div class="space-y-3">
              <div v-for="(detalle, index) in form.detalles" :key="index"
                class="grid gap-3 rounded-2xl border border-slate-200 bg-slate-50/70 p-4 md:grid-cols-[1fr_160px_120px]">
                <div>
                  <label class="mb-1 block text-sm font-semibold text-slate-700">
                    Pieza
                  </label>

                  <select v-model="detalle.pieza_id" class="isavex-input" :disabled="!admin" required>
                    <option value="">Selecciona una pieza</option>

                    <option v-for="pieza in piezas" :key="pieza.id" :value="pieza.id">
                      {{ pieza.codigo }} - {{ pieza.denominacion }}
                    </option>
                  </select>
                </div>

                <div>
                  <label class="mb-1 block text-sm font-semibold text-slate-700">
                    Cantidad
                  </label>

                  <input v-model.number="detalle.cantidad" type="number" min="1" class="isavex-input" :disabled="!admin"
                    required />
                </div>

                <div v-if="admin" class="flex items-end">
                  <button type="button" @click="removeDetalle(index)"
                    class="flex h-12 w-full items-center justify-center rounded-2xl border border-rose-200 bg-rose-50 text-rose-700 transition hover:bg-rose-100"
                    title="Quitar línea">
                    <Trash2 class="h-4 w-4" />
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- BOTONES MODAL -->
          <div class="flex justify-end gap-3">
            <button type="button" @click="closeForm"
              class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
              Cancelar
            </button>

            <button type="submit" :disabled="saving"
              class="rounded-2xl bg-[#59C7D8] px-5 py-3 text-sm font-semibold text-[#081426] shadow-lg shadow-cyan-200/60 transition hover:bg-[#49B3C2] disabled:cursor-not-allowed disabled:opacity-60">
              {{ saving ? "Guardando..." : editingId ? "Actualizar pedido" : "Crear pedido" }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </section>
</template>