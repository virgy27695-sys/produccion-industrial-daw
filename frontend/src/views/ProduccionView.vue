<script setup>
// IMPORTS
import { computed, onMounted, ref } from "vue"
import { getResumenProduccion } from "../api/produccion"


// ESTADO REACTIVO
const resumen = ref([])        // Datos de producción por molde y semana
const loading = ref(false)     // Control de carga
const error = ref("")          // Mensaje de error
const busqueda = ref("")       // Texto de búsqueda


// FILTRADO DEL RESUMEN
// Permite buscar por:
// - código de molde
// - descripción
// - año
// - semana
const resumenFiltrado = computed(() => {
    const texto = busqueda.value.trim().toLowerCase()

    if (!texto) return resumen.value

    return resumen.value.filter((item) => {
        return (
            (item.molde_codigo || "").toLowerCase().includes(texto) ||
            (item.descripcion || "").toLowerCase().includes(texto) ||
            String(item.anio || "").includes(texto) ||
            String(item.semana || "").includes(texto)
        )
    })
})


// TOTAL DE PIEZAS PLANIFICADAS
const totalPiezas = computed(() => {
    return resumenFiltrado.value.reduce(
        (total, item) => total + Number(item.total_piezas || 0),
        0
    )
})


// TOTAL DE CICLOS NECESARIOS
const totalCiclos = computed(() => {
    return resumenFiltrado.value.reduce(
        (total, item) => total + Number(item.ciclos_necesarios || 0),
        0
    )
})


// TOTAL DE MOLDES DISTINTOS
const totalMoldes = computed(() => {
    const moldes = new Set(
        resumenFiltrado.value.map((item) => item.molde_codigo)
    )

    return moldes.size
})


// CARGA DE DATOS DESDE API
async function loadResumenProduccion() {
    loading.value = true
    error.value = ""

    try {
        resumen.value = await getResumenProduccion()
    } catch (e) {
        error.value = "No se pudo cargar el resumen de producción."
        console.error(e)
    } finally {
        loading.value = false
    }
}


// FORMATEAR SEMANA
// Ejemplo: 2026-S08
function formatSemana(item) {
    return `${item.anio}-S${String(item.semana).padStart(2, "0")}`
}


// INICIALIZACIÓN
onMounted(loadResumenProduccion)
</script>

<template>
    <section class="space-y-6">
        <div>
            <h2 class="mb-4 text-2xl font-semibold text-slate-800">
                Producción
            </h2>
            <p class="text-slate-600">
                Planificación de producción agrupada por molde y semana.
            </p>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm text-slate-500">Moldes implicados</p>
                <p class="mt-1 text-3xl font-bold text-slate-800">
                    {{ totalMoldes }}
                </p>
            </div>

            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm text-slate-500">Piezas planificadas</p>
                <p class="mt-1 text-3xl font-bold text-slate-800">
                    {{ totalPiezas }}
                </p>
            </div>

            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm text-slate-500">Ciclos necesarios</p>
                <p class="mt-1 text-3xl font-bold text-slate-800">
                    {{ totalCiclos }}
                </p>
            </div>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-lg font-semibold text-slate-800">
                    Resumen por molde
                </h3>

                <input v-model="busqueda" type="text" placeholder="Buscar por molde, descripción, año o semana"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200 sm:max-w-md" />
            </div>

            <p v-if="error" class="mb-4 text-sm text-red-600">
                {{ error }}
            </p>

            <p v-if="loading" class="text-sm text-slate-500">
                Cargando planificación...
            </p>

            <div v-else class="overflow-x-auto">
                <table class="min-w-full border-separate border-spacing-y-2">
                    <thead>
                        <tr class="text-left text-sm text-slate-500">
                            <th class="px-4 py-2">Molde</th>
                            <th class="px-4 py-2">Descripción</th>
                            <th class="px-4 py-2">Semana</th>
                            <th class="px-4 py-2">Total piezas</th>
                            <th class="px-4 py-2">Cavidades</th>
                            <th class="px-4 py-2">Ciclos necesarios</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="item in resumenFiltrado" :key="`${item.molde_codigo}-${item.anio}-${item.semana}`"
                            class="bg-slate-50 text-slate-800">
                            <td class="rounded-l-xl px-4 py-3 font-medium">
                                {{ item.molde_codigo }}
                            </td>

                            <td class="px-4 py-3">
                                {{ item.descripcion }}
                            </td>

                            <td class="px-4 py-3">
                                {{ formatSemana(item) }}
                            </td>

                            <td class="px-4 py-3 font-semibold">
                                {{ item.total_piezas }}
                            </td>

                            <td class="px-4 py-3">
                                {{ item.cavidades }}
                            </td>

                            <td class="rounded-r-xl px-4 py-3 font-semibold">
                                {{ item.ciclos_necesarios }}
                            </td>
                        </tr>

                        <tr v-if="resumenFiltrado.length === 0">
                            <td colspan="6" class="px-4 py-6 text-center text-sm text-slate-500">
                                No hay datos de producción disponibles.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</template>