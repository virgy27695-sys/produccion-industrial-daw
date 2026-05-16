<script setup>
// IMPORTS
import { computed, onMounted, ref } from "vue"
import { useHead } from "@vueuse/head"

import { getMoldes } from "../api/moldes"

// COMPONENTES UI REUTILIZABLES
import AppCard from "../components/ui/AppCard.vue"
import PageHeader from "../components/ui/PageHeader.vue"
import SearchInput from "../components/ui/SearchInput.vue"
import StatCard from "../components/ui/StatCard.vue"
import StatusBadge from "../components/ui/StatusBadge.vue"
import EmptyState from "../components/ui/EmptyState.vue"

// COMPOSABLE CRUD
import { useCrud } from "../composables/useCrud"


// CONFIGURACIÓN DE LA PÁGINA
useHead({
  title: "Moldes · ISAVEX",
})


// ESTADO REACTIVO
const moldes = ref([])
const busqueda = ref("")


// CRUD GLOBAL
const {
  loading,
  error,
  executeLoad,
} = useCrud()


// FILTRADO DE MOLDES
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


// MÉTRICAS GENERALES
const totalMoldes = computed(() => moldes.value.length)

const moldesActivos = computed(() =>
  moldes.value.filter((molde) => molde.activo).length
)

const totalPiezasAsociadas = computed(() =>
  moldes.value.reduce(
    (total, molde) => total + (molde.piezas?.length || 0),
    0
  )
)

const totalCavidades = computed(() =>
  moldes.value.reduce(
    (total, molde) => total + Number(molde.cavidades || 0),
    0
  )
)


// CARGA DE DATOS
// Se utiliza executeLoad() para reutilizar
// lógica global de carga y errores.
async function loadMoldes() {
  await executeLoad(async () => {
    const results = await Promise.allSettled([
      getMoldes(),
    ])

    const [moldesResult] = results

    if (moldesResult.status === "fulfilled") {
      moldes.value = Array.isArray(moldesResult.value)
        ? moldesResult.value
        : []
    } else {
      console.error(
        "Error cargando moldes:",
        moldesResult.reason
      )

      moldes.value = []

      error.value =
        "No se pudieron cargar los moldes."
    }
  })
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


// TONO VISUAL DE CATEGORÍA
function categoriaTone(categoria) {
  const tonos = {
    soporte: "cyan",
    guia_luz: "blue",
    reflector: "amber",
    embellecedor: "violet",
    otro: "slate",
  }

  return tonos[categoria] || "slate"
}


// FORMATEAR CATEGORÍA
function formatCategoria(categoria) {
  const categorias = {
    soporte: "Soporte",
    guia_luz: "Guía de luz",
    reflector: "Reflector",
    embellecedor: "Embellecedor",
    otro: "Otro",
  }

  return categorias[categoria] || "—"
}


// INICIALIZACIÓN
onMounted(loadMoldes)
</script>

<template>
  <section class="space-y-4">
    <!-- CABECERA COMPACTA -->
    <PageHeader label="Herramental industrial" title="Moldes"
      description="Consulta de moldes de producción, cavidades, configuración técnica y piezas asociadas." />

    <!-- KPIs GENERALES -->
    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
      <StatCard label="Moldes" :value="totalMoldes" tone="slate" />
      <StatCard label="Activos" :value="moldesActivos" tone="green" />
      <StatCard label="Piezas" :value="totalPiezasAsociadas" tone="cyan" />
      <StatCard label="Cavidades" :value="totalCavidades" tone="violet" />
    </div>

    <!-- CONTENEDOR PRINCIPAL -->
    <AppCard class="p-4 md:p-5">
      <!-- CABECERA DEL LISTADO -->
      <div class="mb-4 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div>
          <h2 class="text-lg font-bold text-[#081426]">
            Listado de moldes
          </h2>

          <p class="text-xs text-slate-500">
            {{ moldesFiltrados.length }} registros encontrados.
          </p>
        </div>

        <!-- BUSCADOR REUTILIZABLE -->
        <SearchInput v-model="busqueda" placeholder="Buscar código, máquina, configuración o pieza..." />
      </div>

      <!-- ERROR GENERAL -->
      <p v-if="error"
        class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
        {{ error }}
      </p>

      <!-- ESTADO DE CARGA -->
      <p v-if="loading" class="text-sm text-slate-500">
        Cargando moldes...
      </p>

      <!-- LISTADO -->
      <div v-else class="grid gap-3">
        <!-- TARJETA COMPACTA DE MOLDE -->
        <div v-for="molde in moldesFiltrados" :key="molde.id"
          class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4 transition hover:bg-white/80 hover:shadow-md">
          <!-- INFORMACIÓN SUPERIOR DEL MOLDE -->
          <div class="mb-3 grid gap-4 xl:grid-cols-[1fr_360px] xl:items-start">
            <div class="min-w-0">
              <div class="mb-2 flex flex-wrap items-center gap-2">
                <h3 class="text-lg font-bold text-[#081426]">
                  {{ molde.codigo }}
                </h3>

                <StatusBadge :label="molde.activo ? 'Activo' : 'Inactivo'" :tone="molde.activo ? 'green' : 'red'" />

                <StatusBadge :label="formatTipoConfiguracion(molde.tipo_configuracion)" tone="cyan" />
              </div>

              <p class="line-clamp-2 text-sm leading-6 text-slate-600">
                {{ molde.descripcion || "Sin descripción" }}
              </p>

              <p class="mt-1 text-xs text-slate-500">
                Máquina:
                <span class="font-semibold text-slate-700">
                  {{ molde.maquina || "Sin máquina asignada" }}
                </span>
              </p>
            </div>

            <!-- KPIs DEL MOLDE COMPACTOS -->
            <div class="grid grid-cols-3 gap-2">
              <StatCard label="Cav." :value="molde.cavidades || 0" tone="cyan" />

              <StatCard label="Stock" :value="`${molde.stock_seguridad_dias || 0}d`" tone="amber" />

              <StatCard label="Piezas" :value="molde.piezas?.length || 0" tone="violet" />
            </div>
          </div>

          <!-- TABLA COMPACTA DE PIEZAS ASOCIADAS -->
          <div class="overflow-x-auto">
            <table class="min-w-full text-xs">
              <thead>
                <tr class="border-b border-slate-200 text-left uppercase tracking-[0.16em] text-slate-400">
                  <th class="px-3 py-2">Código</th>
                  <th class="px-3 py-2">Denominación</th>
                  <th class="px-3 py-2">Lado</th>
                  <th class="px-3 py-2">Mercado</th>
                  <th class="px-3 py-2">Categoría</th>
                </tr>
              </thead>

              <tbody>
                <tr v-for="pieza in molde.piezas" :key="pieza.id" class="border-b border-slate-100 bg-white/70">
                  <td class="px-3 py-2 font-bold text-[#081426]">
                    {{ pieza.codigo }}
                  </td>

                  <td class="max-w-[420px] px-3 py-2 text-slate-600">
                    {{ pieza.denominacion }}
                  </td>

                  <td class="px-3 py-2">
                    {{ pieza.lado_pieza || "—" }}
                  </td>

                  <td class="px-3 py-2">
                    {{ pieza.mercado || "—" }}
                  </td>

                  <td class="px-3 py-2">
                    <StatusBadge :label="formatCategoria(pieza.categoria_funcional)"
                      :tone="categoriaTone(pieza.categoria_funcional)" />
                  </td>
                </tr>

                <tr v-if="!molde.piezas || molde.piezas.length === 0">
                  <td colspan="5" class="px-3 py-4 text-center text-sm text-slate-500">
                    Este molde todavía no tiene piezas asociadas.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- ESTADO VACÍO GENERAL -->
        <EmptyState v-if="moldesFiltrados.length === 0" title="Sin moldes registrados"
          description="Todavía no existen moldes disponibles en el sistema." />
      </div>
    </AppCard>
  </section>
</template>