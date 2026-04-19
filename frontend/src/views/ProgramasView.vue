<script setup>
// IMPORTS
import { computed, onMounted, ref } from "vue"
import { getProgramas } from "../api/programas"


// ESTADO REACTIVO
const programas = ref([])      // Lista de programas
const loading = ref(false)     // Control de carga
const error = ref("")          // Mensaje de error
const busqueda = ref("")       // Texto de búsqueda


// FILTRADO DE PROGRAMAS
// Permite buscar por:
// - cliente
// - estado
// - observaciones
// - código de pieza
// - denominación
// - molde
// - lado
// - mercado
// - categoría funcional
// - familia
// - año / semana

const programasFiltrados = computed(() => {
  const texto = busqueda.value.trim().toLowerCase()

  if (!texto) return programas.value

  return programas.value.filter((programa) => {
    const cliente = programa.cliente?.nombre?.toLowerCase() || ""
    const estado = programa.estado?.toLowerCase() || ""
    const observaciones = programa.observaciones?.toLowerCase() || ""
    const id = String(programa.id)

    // Concatenar texto de detalles para búsqueda
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


// CARGA DE DATOS DESDE API
async function loadProgramas() {
  loading.value = true
  error.value = ""

  try {
    programas.value = await getProgramas()
  } catch (e) {
    error.value = "No se pudieron cargar los programas."
    console.error(e)
  } finally {
    loading.value = false
  }
}


// CALCULAR TOTAL DE UNIDADES
function getTotalUnidades(detalles = []) {
  return detalles.reduce(
    (total, detalle) => total + Number(detalle.cantidad || 0),
    0
  )
}


// FORMATEAR SEMANAS
// Ejemplo: 2026-S08, 2026-S09

function getSemanasTexto(detalles = []) {
  const semanas = [
    ...new Set(
      detalles.map(
        (detalle) =>
          `${detalle.anio}-S${String(detalle.semana).padStart(2, "0")}`
      )
    ),
  ]

  if (semanas.length === 0) return "—"

  return semanas.join(", ")
}


// INICIALIZACIÓN
onMounted(loadProgramas)
</script>

<template>
  <section class="space-y-6">
    <div>
      <h2 class="mb-4 text-2xl font-semibold text-slate-800">Programas</h2>
      <p class="text-slate-600">
        Gestión de programas semanales de necesidades enviados por cliente.
      </p>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
      <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h3 class="text-lg font-semibold text-slate-800">
          Listado de programas
        </h3>

        <input v-model="busqueda" type="text"
          placeholder="Buscar por cliente, pieza, molde, lado, mercado, semana o familia"
          class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200 sm:max-w-md" />
      </div>

      <p v-if="error" class="mb-4 text-sm text-red-600">
        {{ error }}
      </p>

      <p v-if="loading" class="text-sm text-slate-500">
        Cargando programas...
      </p>

      <div v-else class="space-y-6">
        <div v-for="programa in programasFiltrados" :key="programa.id"
          class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
          <div class="mb-5 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-2">
              <h3 class="text-lg font-semibold text-slate-800">
                Programa #{{ programa.id }}
              </h3>

              <p class="text-sm text-slate-600">
                <span class="font-medium">Cliente:</span>
                {{ programa.cliente?.nombre || "Sin cliente" }}
              </p>

              <p class="text-sm text-slate-600">
                <span class="font-medium">Fecha solicitud:</span>
                {{ programa.fecha_solicitud || "—" }}
              </p>

              <p class="text-sm text-slate-600">
                <span class="font-medium">Fecha entrega:</span>
                {{ programa.fecha_entrega || "—" }}
              </p>

              <p class="text-sm text-slate-600">
                <span class="font-medium">Estado:</span>
                <span class="ml-2 rounded-full px-3 py-1 text-xs font-medium" :class="{
                  'bg-amber-100 text-amber-700': programa.estado === 'pendiente',
                  'bg-green-100 text-green-700': programa.estado === 'aprobado',
                  'bg-red-100 text-red-700': programa.estado === 'rechazado',
                }">
                  {{ programa.estado }}
                </span>
              </p>

              <p class="text-sm text-slate-600">
                <span class="font-medium">Observaciones:</span>
                {{ programa.observaciones || "Sin observaciones" }}
              </p>
            </div>

            <div class="grid gap-3 sm:grid-cols-3 lg:min-w-[420px]">
              <div class="rounded-xl bg-white p-4 ring-1 ring-slate-200">
                <p class="text-xs uppercase tracking-wide text-slate-500">Líneas</p>
                <p class="mt-1 text-2xl font-bold text-slate-800">
                  {{ programa.detalles?.length || 0 }}
                </p>
              </div>

              <div class="rounded-xl bg-white p-4 ring-1 ring-slate-200">
                <p class="text-xs uppercase tracking-wide text-slate-500">Unidades</p>
                <p class="mt-1 text-2xl font-bold text-slate-800">
                  {{ getTotalUnidades(programa.detalles) }}
                </p>
              </div>

              <div class="rounded-xl bg-white p-4 ring-1 ring-slate-200">
                <p class="text-xs uppercase tracking-wide text-slate-500">Semanas</p>
                <p class="mt-1 text-sm font-medium text-slate-700">
                  {{ getSemanasTexto(programa.detalles) }}
                </p>
              </div>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full border-separate border-spacing-y-2">
              <thead>
                <tr class="text-left text-sm text-slate-500">
                  <th class="px-4 py-2">Código</th>
                  <th class="px-4 py-2">Pieza</th>
                  <th class="px-4 py-2">Modelo</th>
                  <th class="px-4 py-2">Molde</th>
                  <th class="px-4 py-2">Lado</th>
                  <th class="px-4 py-2">Mercado</th>
                  <th class="px-4 py-2">Categoría</th>
                  <th class="px-4 py-2">Familia</th>
                  <th class="px-4 py-2">Año</th>
                  <th class="px-4 py-2">Semana</th>
                  <th class="px-4 py-2">Cantidad</th>
                </tr>
              </thead>

              <tbody>
                <tr v-for="detalle in programa.detalles" :key="detalle.id" class="bg-white text-slate-800">
                  <td class="rounded-l-xl px-4 py-3 font-medium">
                    {{ detalle.pieza?.codigo || "—" }}
                  </td>

                  <td class="px-4 py-3">
                    {{ detalle.pieza?.denominacion || "Sin pieza" }}
                  </td>

                  <td class="px-4 py-3">
                    {{ detalle.pieza?.modelo?.nombre || "—" }}
                  </td>

                  <td class="px-4 py-3">
                    {{ detalle.pieza?.molde?.codigo || "Sin molde" }}
                  </td>

                  <td class="px-4 py-3">
                    {{ detalle.pieza?.lado_pieza || "—" }}
                  </td>

                  <td class="px-4 py-3">
                    {{ detalle.pieza?.mercado || "—" }}
                  </td>

                  <td class="px-4 py-3">
                    {{ detalle.pieza?.categoria_funcional || "—" }}
                  </td>

                  <td class="px-4 py-3">
                    {{ detalle.familia_texto || "—" }}
                  </td>

                  <td class="px-4 py-3">
                    {{ detalle.anio }}
                  </td>

                  <td class="px-4 py-3">
                    {{ detalle.semana }}
                  </td>

                  <td class="rounded-r-xl px-4 py-3 font-semibold">
                    {{ detalle.cantidad }}
                  </td>
                </tr>

                <tr v-if="!programa.detalles || programa.detalles.length === 0">
                  <td colspan="11" class="px-4 py-6 text-center text-sm text-slate-500">
                    Este programa no tiene líneas cargadas.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div v-if="programasFiltrados.length === 0"
          class="rounded-2xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500">
          No hay programas registrados.
        </div>
      </div>
    </div>
  </section>
</template>