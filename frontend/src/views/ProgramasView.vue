<script setup>
// IMPORTS
import { computed, onMounted, ref } from "vue"
import { useHead } from "@vueuse/head"

import {
  getProgramas,
  importarPrograma,
} from "../api/programas"

// COMPOSABLE CRUD
import { useCrud } from "../composables/useCrud"


// CONFIGURACIÓN SEO / TÍTULO
useHead({
  title: "Programas · ISAVEX",
})


// ESTADO REACTIVO
const programas = ref([])

const busqueda = ref("")

const selectedProgramaId = ref("")
const selectedFile = ref(null)

const success = ref("")


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


// IMPORTACIÓN
// Reutilizamos saving como estado de importación.
const importing = saving


// MÉTRICAS GENERALES
const totalProgramas = computed(() => programas.value.length)

const totalLineas = computed(() => {
  return programas.value.reduce(
    (total, programa) =>
      total + (programa.detalles?.length || 0),
    0
  )
})

const totalUnidadesGlobal = computed(() => {
  return programas.value.reduce((total, programa) => {
    return (
      total +
      getTotalUnidades(programa.detalles)
    )
  }, 0)
})


// FILTRADO DE PROGRAMAS
//
// Busca por:
// - cliente
// - estado
// - observaciones
// - código pieza
// - denominación
// - molde
// - modelo
// - lado
// - mercado
// - categoría
// - familia
// - año / semana
const programasFiltrados = computed(() => {
  const texto = busqueda.value
    .trim()
    .toLowerCase()

  if (!texto) return programas.value

  return programas.value.filter((programa) => {
    const cliente =
      programa.cliente?.nombre?.toLowerCase() || ""

    const estado =
      programa.estado?.toLowerCase() || ""

    const observaciones =
      programa.observaciones?.toLowerCase() || ""

    const id = String(programa.id)

    const detallesTexto = (programa.detalles || [])
      .map((detalle) => {
        return [
          detalle.pieza?.codigo || "",
          detalle.pieza?.denominacion || "",
          detalle.familia_texto || "",
          String(detalle.anio || ""),
          String(detalle.semana || ""),
          String(detalle.cantidad || ""),
          detalle.pieza?.molde?.codigo || "",
          detalle.pieza?.modelo?.nombre || "",
          detalle.pieza?.lado_pieza || "",
          detalle.pieza?.mercado || "",
          detalle.pieza?.categoria_funcional || "",
        ]
          .join(" ")
          .toLowerCase()
      })
      .join(" ")

    return (
      id.includes(texto) ||
      cliente.includes(texto) ||
      estado.includes(texto) ||
      observaciones.includes(texto) ||
      detallesTexto.includes(texto)
    )
  })
})


// CARGA DE PROGRAMAS
//
// Promise.allSettled evita romper toda la vista
// si falla una petición.
async function loadProgramas() {
  await executeLoad(async () => {
    const results = await Promise.allSettled([
      getProgramas(),
    ])

    const [programasResult] = results

    if (programasResult.status === "fulfilled") {
      programas.value = Array.isArray(
        programasResult.value
      )
        ? programasResult.value
        : []
    } else {

      programas.value = []

      error.value =
        "No se pudieron cargar los programas."
    }
  })
}


// CAMBIO DE ARCHIVO
function onFileChange(event) {
  selectedFile.value =
    event.target.files?.[0] || null
}


// IMPORTAR EXCEL
//
// Actualiza semanas y cantidades
// del programa seleccionado.
//
// IMPORTANTE:
// - No crea piezas nuevas
// - Solo actualiza referencias existentes
async function importarExcel() {
  clearErrors()

  success.value = ""

  if (!selectedProgramaId.value) {
    formError.value =
      "Debes seleccionar un programa."

    return
  }

  if (!selectedFile.value) {
    formError.value =
      "Debes seleccionar un archivo Excel."

    return
  }

  try {
    await executeSave(async () => {
      await importarPrograma(
        selectedProgramaId.value,
        selectedFile.value
      )

      success.value =
        "Programa importado correctamente."

      selectedProgramaId.value = ""
      selectedFile.value = null

      await loadProgramas()
    })
  } catch (e) {
    formError.value =
      e.message ||
      "No se pudo importar el programa."

  }
}


// TOTAL DE UNIDADES
function getTotalUnidades(detalles = []) {
  return detalles.reduce(
    (total, detalle) =>
      total + Number(detalle.cantidad || 0),
    0
  )
}


// FORMATEAR SEMANAS
//
// Ejemplo:
// 2026-S08
// 2026-S09
function getSemanasTexto(detalles = []) {
  const semanas = [
    ...new Set(
      detalles.map(
        (detalle) =>
          `${detalle.anio}-S${String(
            detalle.semana
          ).padStart(2, "0")}`
      )
    ),
  ]

  if (semanas.length === 0) {
    return "—"
  }

  return semanas.join(", ")
}


// CLASE VISUAL DE ESTADO
function estadoClass(estado) {
  return {
    "bg-amber-100 text-amber-700 ring-amber-200":
      estado === "pendiente",

    "bg-green-100 text-green-700 ring-green-200":
      estado === "aprobado",

    "bg-red-100 text-red-700 ring-red-200":
      estado === "rechazado",
  }
}


// INICIALIZACIÓN
onMounted(loadProgramas)
</script>

<template>
  <section class="space-y-6">
    <!-- HERO -->
    <div
      class="relative overflow-hidden rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-xl shadow-slate-300/30 backdrop-blur-xl">
      <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-[#59C7D8]/20 blur-3xl"></div>

      <div class="relative z-10 flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">
        <div>
          <div
            class="mb-3 inline-flex rounded-full border border-[#59C7D8]/40 bg-white/70 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.25em] text-[#1597A8]">
            Planificación productiva
          </div>

          <h1 class="text-3xl font-bold text-[#081426]">
            Programas
          </h1>

          <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600">
            Gestión de necesidades semanales enviadas por clientes
            para planificación de fabricación y producción.
          </p>
        </div>

        <!-- KPIs -->
        <div class="grid gap-3 sm:grid-cols-3">
          <div class="rounded-3xl border border-white/70 bg-white/80 p-5 shadow-sm backdrop-blur">
            <p class="text-xs uppercase tracking-[0.18em] text-slate-400">
              Programas
            </p>

            <p class="mt-2 text-3xl font-bold text-[#081426]">
              {{ totalProgramas }}
            </p>
          </div>

          <div class="rounded-3xl border border-cyan-200/60 bg-cyan-50/80 p-5 shadow-sm">
            <p class="text-xs uppercase tracking-[0.18em] text-cyan-600">
              Líneas
            </p>

            <p class="mt-2 text-3xl font-bold text-cyan-700">
              {{ totalLineas }}
            </p>
          </div>

          <div class="rounded-3xl border border-amber-200/60 bg-amber-50/80 p-5 shadow-sm">
            <p class="text-xs uppercase tracking-[0.18em] text-amber-600">
              Unidades
            </p>

            <p class="mt-2 text-3xl font-bold text-amber-700">
              {{ totalUnidadesGlobal }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- CONTENIDO -->
    <div class="rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-xl shadow-slate-300/30 backdrop-blur-xl">
      <!-- CABECERA -->
      <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
          <h3 class="text-xl font-bold text-[#081426]">
            Listado de programas
          </h3>

          <p class="mt-1 text-sm text-slate-500">
            Seguimiento de necesidades semanales y planificación.
          </p>
        </div>

        <!-- BUSCADOR -->
        <div
          class="flex w-full items-center gap-3 rounded-2xl border border-slate-200/70 bg-white/80 px-4 py-3 shadow-sm lg:max-w-md">
          <span class="text-slate-400">⌕</span>

          <input v-model="busqueda" type="text" placeholder="Buscar cliente, pieza, molde, semana..."
            class="w-full bg-transparent text-sm text-slate-700 outline-none placeholder:text-slate-400" />
        </div>
      </div>

      <!-- ALERTAS -->
      <p v-if="error || formError"
        class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
        {{ error || formError }}
      </p>

      <p v-if="success"
        class="mb-4 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-700">
        {{ success }}
      </p>

      <!-- IMPORTACIÓN EXCEL -->
      <div class="mb-8 rounded-3xl border border-dashed border-slate-300 bg-slate-50/80 p-5">
        <div class="grid gap-4 lg:grid-cols-[1fr_1fr_auto] lg:items-end">
          <!-- PROGRAMA -->
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">
              Programa
            </label>

            <select v-model="selectedProgramaId" class="isavex-input">
              <option value="">
                Selecciona un programa
              </option>

              <option v-for="programa in programas" :key="programa.id" :value="programa.id">
                Programa #{{ programa.id }} —
                {{ programa.cliente?.nombre }}
              </option>
            </select>
          </div>

          <!-- ARCHIVO -->
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">
              Excel cliente
            </label>

            <input type="file" accept=".xlsx,.xls" class="isavex-input" @change="onFileChange" />
          </div>

          <!-- BOTÓN -->
          <button type="button" :disabled="importing" @click="importarExcel"
            class="isavex-button px-5 py-3 text-sm disabled:opacity-60">
            {{
              importing
                ? "Importando..."
                : "Importar Excel"
            }}
          </button>
        </div>

        <p class="mt-3 text-xs leading-6 text-slate-500">
          Solo se actualizan referencias existentes.
          Las piezas nuevas deben crearse manualmente.
        </p>
      </div>

      <!-- LOADING -->
      <p v-if="loading" class="text-sm text-slate-500">
        Cargando programas...
      </p>

      <!-- LISTADO -->
      <div v-else class="space-y-6">
        <div v-for="programa in programasFiltrados" :key="programa.id"
          class="rounded-[2rem] border border-slate-200 bg-slate-50/80 p-5">
          <!-- CABECERA PROGRAMA -->
          <div class="mb-5 flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
            <div class="space-y-2">
              <h3 class="text-xl font-bold text-[#081426]">
                Programa #{{ programa.id }}
              </h3>

              <p class="text-sm text-slate-600">
                <span class="font-semibold">
                  Cliente:
                </span>

                {{ programa.cliente?.nombre }}
              </p>

              <p class="text-sm text-slate-600">
                <span class="font-semibold">
                  Solicitud:
                </span>

                {{
                  programa.fecha_solicitud || "—"
                }}
              </p>

              <p class="text-sm text-slate-600">
                <span class="font-semibold">
                  Entrega:
                </span>

                {{
                  programa.fecha_entrega || "—"
                }}
              </p>

              <div class="pt-1">
                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold ring-1"
                  :class="estadoClass(programa.estado)">
                  {{ programa.estado }}
                </span>
              </div>

              <p class="max-w-2xl text-sm leading-6 text-slate-500">
                {{
                  programa.observaciones ||
                  "Sin observaciones"
                }}
              </p>
            </div>

            <!-- KPIS -->
            <div class="grid gap-3 sm:grid-cols-3 xl:min-w-[460px]">
              <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs uppercase tracking-wide text-slate-500">
                  Líneas
                </p>

                <p class="mt-1 text-2xl font-bold text-slate-800">
                  {{
                    programa.detalles?.length || 0
                  }}
                </p>
              </div>

              <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs uppercase tracking-wide text-slate-500">
                  Unidades
                </p>

                <p class="mt-1 text-2xl font-bold text-slate-800">
                  {{
                    getTotalUnidades(
                      programa.detalles
                    )
                  }}
                </p>
              </div>

              <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs uppercase tracking-wide text-slate-500">
                  Semanas
                </p>

                <p class="mt-2 text-xs font-semibold leading-5 text-slate-700">
                  {{
                    getSemanasTexto(
                      programa.detalles
                    )
                  }}
                </p>
              </div>
            </div>
          </div>

          <!-- TABLA -->
          <div class="overflow-x-auto">
            <table class="min-w-full border-separate border-spacing-y-2 text-sm">
              <thead>
                <tr class="text-left text-xs uppercase tracking-[0.18em] text-slate-400">
                  <th class="px-4 py-2">Código</th>
                  <th class="px-4 py-2">Pieza</th>
                  <th class="px-4 py-2">Modelo</th>
                  <th class="px-4 py-2">Molde</th>
                  <th class="px-4 py-2">Lado</th>
                  <th class="px-4 py-2">Mercado</th>
                  <th class="px-4 py-2">Familia</th>
                  <th class="px-4 py-2">Semana</th>
                  <th class="px-4 py-2">Cantidad</th>
                </tr>
              </thead>

              <tbody>
                <tr v-for="detalle in programa.detalles" :key="detalle.id" class="bg-white text-slate-800 shadow-sm">
                  <td class="rounded-l-2xl px-4 py-4 font-bold text-[#081426]">
                    {{
                      detalle.pieza?.codigo || "—"
                    }}
                  </td>

                  <td class="px-4 py-4">
                    {{
                      detalle.pieza?.denominacion ||
                      "Sin pieza"
                    }}
                  </td>

                  <td class="px-4 py-4">
                    {{
                      detalle.pieza?.modelo
                        ?.nombre || "—"
                    }}
                  </td>

                  <td class="px-4 py-4">
                    {{
                      detalle.pieza?.molde
                        ?.codigo || "—"
                    }}
                  </td>

                  <td class="px-4 py-4">
                    {{
                      detalle.pieza?.lado_pieza ||
                      "—"
                    }}
                  </td>

                  <td class="px-4 py-4">
                    {{
                      detalle.pieza?.mercado ||
                      "—"
                    }}
                  </td>

                  <td class="px-4 py-4">
                    {{
                      detalle.familia_texto ||
                      "—"
                    }}
                  </td>

                  <td class="px-4 py-4">
                    {{ detalle.anio }}-S{{
                      String(
                        detalle.semana
                      ).padStart(2, "0")
                    }}
                  </td>

                  <td class="rounded-r-2xl px-4 py-4 font-bold">
                    {{ detalle.cantidad }}
                  </td>
                </tr>

                <tr v-if="
                  !programa.detalles ||
                  programa.detalles.length === 0
                ">
                  <td colspan="9" class="rounded-2xl bg-white px-4 py-8 text-center text-sm text-slate-500">
                    Este programa no tiene líneas cargadas.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- VACÍO -->
        <div v-if="programasFiltrados.length === 0"
          class="rounded-3xl border border-dashed border-slate-300 p-10 text-center text-sm text-slate-500">
          No hay programas registrados.
        </div>
      </div>
    </div>
  </section>
</template>
