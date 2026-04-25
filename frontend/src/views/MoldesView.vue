<script setup>
// IMPORTS
import { computed, onMounted, ref } from "vue"
import { getMoldes } from "../api/moldes"


// ESTADO REACTIVO
const moldes = ref([])        // Lista de moldes
const loading = ref(false)    // Control de carga
const error = ref("")         // Mensaje de error
const busqueda = ref("")      // Texto de búsqueda


// FILTRADO DE MOLDES
// Permite buscar por:
// - código
// - descripción
// - máquina
// - tipo de configuración
// - piezas asociadas
const moldesFiltrados = computed(() => {
  const texto = busqueda.value.trim().toLowerCase()

  if (!texto) return moldes.value

  return moldes.value.filter((molde) => {
    const piezasTexto = (molde.piezas || [])
      .map((pieza) => {
        return [
          pieza.codigo || "",
          pieza.denominacion || "",
          pieza.lado_pieza || "",
          pieza.mercado || "",
          pieza.categoria_funcional || "",
        ]
          .join(" ")
          .toLowerCase()
      })
      .join(" ")

    return (
      (molde.codigo || "").toLowerCase().includes(texto) ||
      (molde.descripcion || "").toLowerCase().includes(texto) ||
      (molde.maquina || "").toLowerCase().includes(texto) ||
      (molde.tipo_configuracion || "").toLowerCase().includes(texto) ||
      piezasTexto.includes(texto)
    )
  })
})


// CARGA DE DATOS DESDE API
async function loadMoldes() {
  loading.value = true
  error.value = ""

  try {
    moldes.value = await getMoldes()
  } catch (e) {
    error.value = "No se pudieron cargar los moldes."
    console.error(e)
  } finally {
    loading.value = false
  }
}


// FORMATEAR TIPO DE CONFIGURACIÓN
function formatTipoConfiguracion(tipo) {
  const tipos = {
    simple: "Simple",
    izquierda_derecha: "Izquierda / derecha",
    multiple_referencias: "Múltiples referencias",
  }

  return tipos[tipo] || "—"
}


// INICIALIZACIÓN
onMounted(loadMoldes)
</script>

<template>
  <section class="space-y-6">
    <div>
      <h2 class="mb-4 text-2xl font-semibold text-slate-800">Moldes</h2>
      <p class="text-slate-600">
        Consulta de moldes de producción y piezas asociadas a cada molde.
      </p>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
      <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h3 class="text-lg font-semibold text-slate-800">
          Listado de moldes
        </h3>

        <input v-model="busqueda" type="text" placeholder="Buscar por código, descripción, máquina o pieza"
          class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200 sm:max-w-md" />
      </div>

      <p v-if="error" class="mb-4 text-sm text-red-600">
        {{ error }}
      </p>

      <p v-if="loading" class="text-sm text-slate-500">
        Cargando moldes...
      </p>

      <div v-else class="grid gap-5">
        <div v-for="molde in moldesFiltrados" :key="molde.id"
          class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
          <div class="mb-5 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-2">
              <h3 class="text-lg font-semibold text-slate-800">
                {{ molde.codigo }}
              </h3>

              <p class="text-sm text-slate-600">
                <span class="font-medium">Descripción:</span>
                {{ molde.descripcion }}
              </p>

              <p class="text-sm text-slate-600">
                <span class="font-medium">Tipo:</span>
                {{ formatTipoConfiguracion(molde.tipo_configuracion) }}
              </p>

              <p class="text-sm text-slate-600">
                <span class="font-medium">Máquina:</span>
                {{ molde.maquina || "Sin máquina asignada" }}
              </p>

              <p class="text-sm text-slate-600">
                <span class="font-medium">Estado:</span>
                <span class="ml-2 rounded-full px-3 py-1 text-xs font-medium"
                  :class="molde.activo ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                  {{ molde.activo ? "Activo" : "Inactivo" }}
                </span>
              </p>
            </div>

            <div class="grid gap-3 sm:grid-cols-3 lg:min-w-[420px]">
              <div class="rounded-xl bg-white p-4 ring-1 ring-slate-200">
                <p class="text-xs uppercase tracking-wide text-slate-500">Cavidades</p>
                <p class="mt-1 text-2xl font-bold text-slate-800">
                  {{ molde.cavidades }}
                </p>
              </div>

              <div class="rounded-xl bg-white p-4 ring-1 ring-slate-200">
                <p class="text-xs uppercase tracking-wide text-slate-500">Stock seguridad</p>
                <p class="mt-1 text-2xl font-bold text-slate-800">
                  {{ molde.stock_seguridad_dias }} días
                </p>
              </div>

              <div class="rounded-xl bg-white p-4 ring-1 ring-slate-200">
                <p class="text-xs uppercase tracking-wide text-slate-500">Piezas</p>
                <p class="mt-1 text-2xl font-bold text-slate-800">
                  {{ molde.piezas?.length || 0 }}
                </p>
              </div>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full border-separate border-spacing-y-2">
              <thead>
                <tr class="text-left text-sm text-slate-500">
                  <th class="px-4 py-2">Código</th>
                  <th class="px-4 py-2">Denominación</th>
                  <th class="px-4 py-2">Lado</th>
                  <th class="px-4 py-2">Mercado</th>
                  <th class="px-4 py-2">Categoría</th>
                </tr>
              </thead>

              <tbody>
                <tr v-for="pieza in molde.piezas" :key="pieza.id" class="bg-white text-slate-800">
                  <td class="rounded-l-xl px-4 py-3 font-medium">
                    {{ pieza.codigo }}
                  </td>

                  <td class="px-4 py-3">
                    {{ pieza.denominacion }}
                  </td>

                  <td class="px-4 py-3">
                    {{ pieza.lado_pieza || "—" }}
                  </td>

                  <td class="px-4 py-3">
                    {{ pieza.mercado || "—" }}
                  </td>

                  <td class="rounded-r-xl px-4 py-3">
                    {{ pieza.categoria_funcional || "—" }}
                  </td>
                </tr>

                <tr v-if="!molde.piezas || molde.piezas.length === 0">
                  <td colspan="5" class="px-4 py-6 text-center text-sm text-slate-500">
                    Este molde todavía no tiene piezas asociadas.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div v-if="moldesFiltrados.length === 0"
          class="rounded-2xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500">
          No hay moldes registrados.
        </div>
      </div>
    </div>
  </section>
</template>