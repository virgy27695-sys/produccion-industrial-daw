<script setup>
// IMPORTS
// Vue reactivity y ciclo de vida.
import { computed, onMounted, ref } from "vue"

// Gestión dinámica del título de la página.
import { useHead } from "@vueuse/head"

// Iconografía visual para KPIs y paneles.
import {
    Factory,
    Boxes,
    RotateCcw,
    Search,
    Activity,
    CalendarDays,
} from "lucide-vue-next"


// API DE PRODUCCIÓN
// Obtiene el resumen productivo agrupado por molde y semana.
import { getResumenProduccion } from "../api/produccion"


// COMPOSABLE CRUD
import { useCrud } from "../composables/useCrud"


// CONFIGURACIÓN SEO / TÍTULO
// Cambia automáticamente el título de la pestaña del navegador.
useHead({
    title: "Producción · ISAVEX",
})


// ESTADO PRINCIPAL
// resumen: datos obtenidos desde backend.
// busqueda: filtro textual de búsqueda.
const resumen = ref([])
const busqueda = ref("")


// CRUD GLOBAL
// Centraliza loading y errores reutilizables.
const {
    loading,
    error,
    executeLoad,
} = useCrud()


// FILTRADO DE PRODUCCIÓN
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


// KPI: TOTAL DE PIEZAS
// Suma todas las piezas planificadas.
const totalPiezas = computed(() =>
    resumenFiltrado.value.reduce(
        (total, item) => total + Number(item.total_piezas || 0),
        0
    )
)


// KPI: TOTAL DE CICLOS
// Suma todos los ciclos necesarios.
const totalCiclos = computed(() =>
    resumenFiltrado.value.reduce(
        (total, item) => total + Number(item.ciclos_necesarios || 0),
        0
    )
)


// KPI: TOTAL DE MOLDES
// Cuenta moldes distintos presentes en la planificación.
const totalMoldes = computed(() => {
    const moldes = new Set(
        resumenFiltrado.value.map((item) => item.molde_codigo)
    )

    return moldes.size
})


// KPI: SEMANAS ACTIVAS
// Cuenta cuántas semanas distintas tienen producción.
const semanasActivas = computed(() => {
    const semanas = new Set(
        resumenFiltrado.value.map(
            (item) => `${item.anio}-S${item.semana}`
        )
    )

    return semanas.size
})


// KPI: PROMEDIO DE CAVIDADES
// Calcula la media de cavidades entre todos los moldes.
const promedioCavidades = computed(() => {
    if (resumenFiltrado.value.length === 0) return 0

    const total = resumenFiltrado.value.reduce(
        (acc, item) => acc + Number(item.cavidades || 0),
        0
    )

    return Math.round(
        total / resumenFiltrado.value.length
    )
})


// TOP MOLDES
// Obtiene los moldes con más volumen productivo.
const topMoldes = computed(() =>
    [...resumenFiltrado.value]
        .sort(
            (a, b) =>
                Number(b.total_piezas || 0) -
                Number(a.total_piezas || 0)
        )
        .slice(0, 4)
)


// CARGA DE DATOS
// Promise.allSettled evita romper la vista
// si falla alguna petición.
async function loadResumenProduccion() {
    await executeLoad(async () => {
        const results = await Promise.allSettled([
            getResumenProduccion(),
        ])

        const [resumenResult] = results

        if (resumenResult.status === "fulfilled") {
            resumen.value = Array.isArray(
                resumenResult.value
            )
                ? resumenResult.value
                : []
        } else {
            console.error(
                "Error cargando producción:",
                resumenResult.reason
            )

            resumen.value = []

            error.value =
                "No se pudo cargar el resumen de producción."
        }
    })
}


// FORMATEAR SEMANA
// Convierte año + semana en formato visual estándar.
function formatSemana(item) {
    return `${item.anio}-S${String(
        item.semana
    ).padStart(2, "0")}`
}


// FORMATEAR NÚMEROS
// Aplica formato español con separadores de miles.
function formatNumber(value) {
    return Number(value || 0).toLocaleString(
        "es-ES"
    )
}


// DETECTAR INTENSIDAD PRODUCTIVA
// Clasifica la carga de producción en:
// - Alta
// - Media
// - Baja
function intensidadProduccion(item) {
    const piezas = Number(
        item.total_piezas || 0
    )

    if (piezas >= 10000) return "Alta"
    if (piezas >= 3000) return "Media"

    return "Baja"
}


// CLASE VISUAL DE INTENSIDAD
// Define colores según el nivel de producción.
function intensidadClass(item) {
    const intensidad =
        intensidadProduccion(item)

    return {
        "bg-red-100 text-red-700 ring-red-200":
            intensidad === "Alta",

        "bg-amber-100 text-amber-700 ring-amber-200":
            intensidad === "Media",

        "bg-green-100 text-green-700 ring-green-200":
            intensidad === "Baja",
    }
}


// INICIALIZACIÓN
// Carga los datos automáticamente al abrir la vista.
onMounted(loadResumenProduccion)
</script>


<template>
    <section class="space-y-6">

        <!-- HERO PRINCIPAL -->
        <!-- Cabecera premium con KPIs industriales -->
        <div
            class="relative overflow-hidden rounded-[2rem] border border-white/70 bg-white/75 p-6 shadow-xl shadow-slate-300/40 backdrop-blur-xl">

            <!-- EFECTO VISUAL DECORATIVO -->
            <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-[#59C7D8]/20 blur-3xl">
            </div>

            <div class="relative z-10 flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">

                <!-- TÍTULO Y DESCRIPCIÓN -->
                <div>
                    <div
                        class="mb-3 inline-flex rounded-full border border-[#59C7D8]/40 bg-white/70 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.25em] text-[#1597A8]">
                        Planta productiva
                    </div>

                    <h1 class="text-3xl font-bold text-[#081426]">
                        Producción
                    </h1>

                    <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600">
                        Planificación industrial agrupada por molde, semana,
                        cavidades y ciclos necesarios.
                    </p>
                </div>

                <!-- KPIs -->
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">

                    <!-- KPI MOLDES -->
                    <div class="rounded-3xl border border-white/70 bg-white/80 p-5 shadow-sm backdrop-blur">
                        <div
                            class="mb-3 flex h-11 w-11 items-center justify-center rounded-2xl bg-cyan-100 text-cyan-700">
                            <Factory class="h-5 w-5" />
                        </div>

                        <p class="text-xs uppercase tracking-[0.18em] text-slate-400">
                            Moldes
                        </p>

                        <p class="mt-1 text-3xl font-bold text-[#081426]">
                            {{ totalMoldes }}
                        </p>
                    </div>

                    <!-- KPI PIEZAS -->
                    <div class="rounded-3xl border border-white/70 bg-white/80 p-5 shadow-sm backdrop-blur">
                        <div
                            class="mb-3 flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-100 text-blue-700">
                            <Boxes class="h-5 w-5" />
                        </div>

                        <p class="text-xs uppercase tracking-[0.18em] text-slate-400">
                            Piezas
                        </p>

                        <p class="mt-1 text-3xl font-bold text-[#081426]">
                            {{ formatNumber(totalPiezas) }}
                        </p>
                    </div>

                    <!-- KPI CICLOS -->
                    <div class="rounded-3xl border border-white/70 bg-white/80 p-5 shadow-sm backdrop-blur">
                        <div
                            class="mb-3 flex h-11 w-11 items-center justify-center rounded-2xl bg-violet-100 text-violet-700">
                            <RotateCcw class="h-5 w-5" />
                        </div>

                        <p class="text-xs uppercase tracking-[0.18em] text-slate-400">
                            Ciclos
                        </p>

                        <p class="mt-1 text-3xl font-bold text-[#081426]">
                            {{ formatNumber(totalCiclos) }}
                        </p>
                    </div>

                    <!-- KPI SEMANAS -->
                    <div class="rounded-3xl border border-white/70 bg-white/80 p-5 shadow-sm backdrop-blur">
                        <div
                            class="mb-3 flex h-11 w-11 items-center justify-center rounded-2xl bg-green-100 text-green-700">
                            <CalendarDays class="h-5 w-5" />
                        </div>

                        <p class="text-xs uppercase tracking-[0.18em] text-slate-400">
                            Semanas
                        </p>

                        <p class="mt-1 text-3xl font-bold text-[#081426]">
                            {{ semanasActivas }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- TOOLBAR -->
        <div
            class="flex flex-col gap-4 rounded-[1.75rem] border border-white/70 bg-white/75 p-5 shadow-lg shadow-slate-300/30 backdrop-blur-xl lg:flex-row lg:items-center lg:justify-between">

            <!-- BUSCADOR -->
            <div
                class="flex w-full items-center gap-3 rounded-2xl border border-slate-200/70 bg-white/80 px-4 py-3 shadow-sm lg:max-w-md">
                <Search class="h-5 w-5 text-slate-400" />

                <input v-model="busqueda" type="text" placeholder="Buscar molde, descripción, año o semana..."
                    class="w-full bg-transparent text-sm text-slate-700 outline-none placeholder:text-slate-400" />
            </div>

            <!-- MÉTRICAS -->
            <div class="flex flex-wrap items-center gap-3">
                <div
                    class="rounded-2xl border border-[#59C7D8]/30 bg-cyan-50/70 px-4 py-2 text-sm font-medium text-cyan-700">
                    {{ resumenFiltrado.length }} registros
                </div>

                <div
                    class="rounded-2xl border border-slate-200/70 bg-white/80 px-4 py-2 text-sm font-medium text-slate-600">
                    Cavidades medias:
                    {{ promedioCavidades }}
                </div>
            </div>
        </div>

        <!-- ERROR -->
        <p v-if="error" class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
            {{ error }}
        </p>

        <!-- CONTENIDO PRINCIPAL -->
        <div class="grid gap-6 xl:grid-cols-[1.4fr_0.75fr]">

            <!-- TABLA DE PRODUCCIÓN -->
            <div
                class="rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-xl shadow-slate-300/30 backdrop-blur-xl">

                <!-- CABECERA -->
                <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-[#081426]">
                            Resumen por molde
                        </h2>

                        <p class="text-sm text-slate-500">
                            Producción planificada por semana y molde.
                        </p>
                    </div>

                    <div class="rounded-2xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-600">
                        {{ formatNumber(totalPiezas) }} piezas
                    </div>
                </div>

                <!-- ESTADO CARGA -->
                <p v-if="loading" class="rounded-2xl bg-slate-50 p-5 text-sm text-slate-500">
                    Cargando planificación...
                </p>

                <!-- TABLA -->
                <div v-else class="overflow-x-auto">

                    <table class="min-w-full border-separate border-spacing-y-3 text-sm">

                        <!-- CABECERA TABLA -->
                        <thead>
                            <tr class="text-left text-xs uppercase tracking-[0.18em] text-slate-400">
                                <th class="px-4 py-2">Molde</th>
                                <th class="px-4 py-2">Descripción</th>
                                <th class="px-4 py-2">Semana</th>
                                <th class="px-4 py-2">Piezas</th>
                                <th class="px-4 py-2">Cav.</th>
                                <th class="px-4 py-2">Ciclos</th>
                                <th class="px-4 py-2">Carga</th>
                            </tr>
                        </thead>

                        <!-- FILAS -->
                        <tbody>

                            <!-- REGISTROS -->
                            <tr v-for="item in resumenFiltrado"
                                :key="`${item.molde_codigo}-${item.anio}-${item.semana}`"
                                class="bg-white/90 text-slate-800 shadow-sm">

                                <!-- MOLDE -->
                                <td class="rounded-l-2xl px-4 py-4 font-bold text-[#081426]">
                                    {{ item.molde_codigo || "Sin molde" }}
                                </td>

                                <!-- DESCRIPCIÓN -->
                                <td class="max-w-[280px] px-4 py-4 text-slate-600">
                                    {{ item.descripcion || "Sin descripción" }}
                                </td>

                                <!-- SEMANA -->
                                <td class="px-4 py-4">
                                    <span
                                        class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                        {{ formatSemana(item) }}
                                    </span>
                                </td>

                                <!-- PIEZAS -->
                                <td class="px-4 py-4 font-bold text-[#081426]">
                                    {{ formatNumber(item.total_piezas) }}
                                </td>

                                <!-- CAVIDADES -->
                                <td class="px-4 py-4">
                                    {{ item.cavidades || 0 }}
                                </td>

                                <!-- CICLOS -->
                                <td class="px-4 py-4 font-semibold">
                                    {{ formatNumber(item.ciclos_necesarios) }}
                                </td>

                                <!-- INTENSIDAD -->
                                <td class="rounded-r-2xl px-4 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold ring-1"
                                        :class="intensidadClass(item)">
                                        {{ intensidadProduccion(item) }}
                                    </span>
                                </td>
                            </tr>

                            <!-- ESTADO VACÍO -->
                            <tr v-if="resumenFiltrado.length === 0">
                                <td colspan="7"
                                    class="rounded-2xl bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                                    No hay datos de producción disponibles.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- PANEL LATERAL -->
            <aside class="space-y-6">

                <!-- TOP MOLDES -->
                <div
                    class="rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-xl shadow-slate-300/30 backdrop-blur-xl">

                    <!-- CABECERA -->
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#59C7D8]/20 text-[#1597A8]">
                            <Activity class="h-6 w-6" />
                        </div>

                        <div>
                            <h2 class="text-lg font-bold text-[#081426]">
                                Carga productiva
                            </h2>

                            <p class="text-sm text-slate-500">
                                Moldes con mayor volumen.
                            </p>
                        </div>
                    </div>

                    <!-- LISTADO -->
                    <div class="mt-5 space-y-4">

                        <!-- TARJETAS -->
                        <div v-for="molde in topMoldes" :key="`${molde.molde_codigo}-${molde.anio}-${molde.semana}`"
                            class="rounded-2xl border border-slate-200/70 bg-white/80 p-4 shadow-sm">

                            <div class="flex items-start justify-between gap-3">

                                <div>
                                    <p class="font-bold text-[#081426]">
                                        {{ molde.molde_codigo || "Sin molde" }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ formatSemana(molde) }}
                                    </p>
                                </div>

                                <span class="rounded-full bg-cyan-100 px-3 py-1 text-xs font-bold text-cyan-700">
                                    {{ formatNumber(molde.total_piezas) }}
                                </span>
                            </div>

                            <!-- BARRA VISUAL -->
                            <div class="mt-4 h-2 overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full bg-[#59C7D8]" :style="{
                                    width: `${Math.min(
                                        100,
                                        (Number(molde.total_piezas || 0) / Math.max(totalPiezas, 1)) * 100 * 4
                                    )}%`,
                                }">
                                </div>
                            </div>
                        </div>

                        <!-- ESTADO VACÍO -->
                        <div v-if="topMoldes.length === 0"
                            class="rounded-2xl bg-slate-50 p-5 text-center text-sm text-slate-500">
                            No hay moldes destacados.
                        </div>
                    </div>
                </div>

                <!-- PANEL INFORMATIVO -->
                <div
                    class="overflow-hidden rounded-[2rem] bg-gradient-to-br from-[#081426] via-[#10233D] to-[#163047] p-6 text-white shadow-xl">

                    <div class="mb-6 flex h-14 w-14 items-center justify-center rounded-2xl bg-white/10 text-[#59C7D8]">
                        <Factory class="h-7 w-7" />
                    </div>

                    <p class="text-sm text-[#A8B6C6]">
                        ISAVEX Producción
                    </p>

                    <h3 class="mt-2 text-2xl font-bold">
                        Visión industrial por molde
                    </h3>

                    <p class="mt-4 text-sm leading-7 text-[#A8B6C6]">
                        Esta vista permite anticipar carga de trabajo,
                        ciclos necesarios y volumen de piezas por semana.
                    </p>
                </div>
            </aside>
        </div>
    </section>
</template>