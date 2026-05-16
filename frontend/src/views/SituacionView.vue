<script setup>
// IMPORTS
import { ref, onMounted, computed } from "vue"
import { useHead } from "@vueuse/head"

import {
    Activity,
    AlertTriangle,
    CheckCircle2,
    PackageCheck,
} from "lucide-vue-next"

import { apiGet } from "../api/http"

// COMPONENTES UI
import DataTable from "../components/ui/table/DataTable.vue"
import StatusBadge from "../components/ui/StatusBadge.vue"

// COMPOSABLE CRUD
import { useCrud } from "../composables/useCrud"


// CONFIGURACIÓN SEO
useHead({
    title: "Situación · ISAVEX",
})


// ESTADO PRINCIPAL
const datos = ref([])
const busqueda = ref("")


// CRUD GLOBAL
const {
    loading,
    error,
    executeLoad,
} = useCrud()


// FILTRADO
const datosFiltrados = computed(() => {
    const texto = busqueda.value.trim().toLowerCase()

    if (!texto) return datos.value

    return datos.value.filter((item) => {
        return (
            (item.pieza || "").toLowerCase().includes(texto) ||
            (item.denominacion || "").toLowerCase().includes(texto) ||
            (item.estado || "").toLowerCase().includes(texto)
        )
    })
})


// KPIS
const totalReferencias = computed(() => datos.value.length)

const totalCriticos = computed(() =>
    datos.value.filter((item) => item.estado === "critico").length
)

const totalMedios = computed(() =>
    datos.value.filter((item) => item.estado === "medio").length
)

const totalCorrectos = computed(() =>
    datos.value.filter((item) => item.estado !== "critico" && item.estado !== "medio").length
)


// CARGA DE DATOS
async function loadData() {
    await executeLoad(async () => {
        const response = await apiGet("/situacion")

        datos.value = Array.isArray(response)
            ? response
            : []
    })
}


// TONO DEL ESTADO
function estadoTone(estado) {
    if (estado === "critico") return "red"
    if (estado === "medio") return "amber"

    return "green"
}


// TEXTO DEL ESTADO
function estadoLabel(estado) {
    if (estado === "critico") return "Crítico"
    if (estado === "medio") return "Medio"

    return "Correcto"
}


// FORMATEAR NÚMEROS
function formatNumber(value) {
    return Number(value || 0).toLocaleString("es-ES")
}


// INICIALIZACIÓN
onMounted(loadData)
</script>

<template>
    <section class="space-y-4 md:space-y-5">
        <!-- HERO -->
        <div class="isavex-card overflow-hidden p-4 md:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <!-- TEXTO -->
                <div>
                    <div
                        class="mb-3 inline-flex items-center rounded-full border border-cyan-200 bg-white/70 px-3 py-1.5 text-[10px] font-semibold uppercase tracking-[0.22em] text-cyan-700 md:text-xs">
                        Control operativo
                    </div>

                    <h1 class="text-3xl font-black tracking-tight text-slate-900 md:text-4xl">
                        Situación
                    </h1>

                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500 md:text-base">
                        Control de stock, producción, entregas y disponibilidad por pieza.
                    </p>
                </div>

                <!-- KPIS -->
                <div class="grid grid-cols-2 gap-3 md:grid-cols-4 md:gap-4">
                    <div class="rounded-2xl border border-white/60 bg-white/80 p-4 shadow-sm md:rounded-3xl md:p-5">
                        <div
                            class="mb-3 flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-100 text-slate-700">
                            <Activity class="h-5 w-5" />
                        </div>

                        <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-400 md:text-xs">
                            Referencias
                        </p>

                        <p class="mt-1 text-3xl font-black text-slate-900">
                            {{ totalReferencias }}
                        </p>
                    </div>

                    <div class="rounded-2xl border border-red-100 bg-red-50/80 p-4 shadow-sm md:rounded-3xl md:p-5">
                        <div
                            class="mb-3 flex h-10 w-10 items-center justify-center rounded-2xl bg-red-100 text-red-700">
                            <AlertTriangle class="h-5 w-5" />
                        </div>

                        <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-red-500 md:text-xs">
                            Críticos
                        </p>

                        <p class="mt-1 text-3xl font-black text-red-700">
                            {{ totalCriticos }}
                        </p>
                    </div>

                    <div class="rounded-2xl border border-amber-100 bg-amber-50/80 p-4 shadow-sm md:rounded-3xl md:p-5">
                        <div
                            class="mb-3 flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-100 text-amber-700">
                            <PackageCheck class="h-5 w-5" />
                        </div>

                        <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-amber-600 md:text-xs">
                            Medios
                        </p>

                        <p class="mt-1 text-3xl font-black text-amber-700">
                            {{ totalMedios }}
                        </p>
                    </div>

                    <div class="rounded-2xl border border-green-100 bg-green-50/80 p-4 shadow-sm md:rounded-3xl md:p-5">
                        <div
                            class="mb-3 flex h-10 w-10 items-center justify-center rounded-2xl bg-green-100 text-green-700">
                            <CheckCircle2 class="h-5 w-5" />
                        </div>

                        <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-green-600 md:text-xs">
                            Correctos
                        </p>

                        <p class="mt-1 text-3xl font-black text-green-700">
                            {{ totalCorrectos }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- CONTENEDOR PRINCIPAL -->
        <div class="isavex-card p-4 md:p-5">
            <!-- HEADER -->
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-xl font-bold text-slate-900 md:text-2xl">
                        Situación de producción
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        {{ datosFiltrados.length }} referencias encontradas.
                    </p>
                </div>

                <!-- BUSCADOR -->
                <div
                    class="flex w-full items-center gap-3 rounded-2xl border border-slate-200/70 bg-white/80 px-4 py-3 shadow-sm lg:max-w-md">
                    <span class="text-slate-400">⌕</span>

                    <input v-model="busqueda" type="text" placeholder="Buscar código, denominación o estado..."
                        class="w-full bg-transparent text-sm text-slate-700 outline-none placeholder:text-slate-400" />
                </div>
            </div>

            <!-- ERROR -->
            <p v-if="error" class="mb-4 text-sm font-medium text-red-600">
                {{ error }}
            </p>

            <!-- DATA TABLE -->
            <DataTable :loading="loading" :empty="datosFiltrados.length === 0" loading-text="Cargando situación..."
                empty-title="Sin datos de situación"
                empty-description="Todavía no hay información disponible de stock, producción o entregas.">
                <!-- CARDS MÓVIL -->
                <div class="grid gap-3 md:hidden">
                    <div v-for="item in datosFiltrados" :key="item.pieza"
                        class="rounded-2xl border border-slate-200 bg-white/80 p-4 shadow-sm">
                        <div class="mb-3 flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-bold text-[#081426]">
                                    {{ item.pieza }}
                                </p>

                                <p class="mt-1 line-clamp-2 text-sm text-slate-500">
                                    {{ item.denominacion }}
                                </p>
                            </div>

                            <StatusBadge :label="estadoLabel(item.estado)" :tone="estadoTone(item.estado)" />
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div class="rounded-2xl bg-slate-50 p-3">
                                <p class="text-xs text-slate-400">Programa</p>
                                <p class="font-bold text-slate-800">{{ formatNumber(item.programado) }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-3">
                                <p class="text-xs text-slate-400">Fabricado</p>
                                <p class="font-bold text-slate-800">{{ formatNumber(item.fabricado) }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-3">
                                <p class="text-xs text-slate-400">Entregado</p>
                                <p class="font-bold text-slate-800">{{ formatNumber(item.entregado) }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-3">
                                <p class="text-xs text-slate-400">Stock</p>
                                <p class="font-bold text-slate-800">{{ formatNumber(item.stock_actual) }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-3">
                                <p class="text-xs text-slate-400">Disponible</p>
                                <p class="font-bold text-slate-800">{{ formatNumber(item.disponible) }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-3">
                                <p class="text-xs text-slate-400">Pendiente</p>
                                <p class="font-bold text-slate-800">{{ formatNumber(item.pendiente) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TABLA DESKTOP -->
                <div class="hidden overflow-x-auto md:block">
                    <table class="min-w-full border-separate border-spacing-y-2 text-sm">
                        <thead>
                            <tr class="text-left text-xs uppercase tracking-[0.18em] text-slate-400">
                                <th class="px-4 py-2">Código</th>
                                <th class="px-4 py-2">Pieza</th>
                                <th class="px-4 py-2">Programa</th>
                                <th class="px-4 py-2">Fabricado</th>
                                <th class="px-4 py-2">Entregado</th>
                                <th class="px-4 py-2">Stock</th>
                                <th class="px-4 py-2">Seguridad</th>
                                <th class="px-4 py-2">Disponible</th>
                                <th class="px-4 py-2">Pendiente</th>
                                <th class="px-4 py-2">Estado</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="item in datosFiltrados" :key="item.pieza"
                                class="bg-white/90 text-slate-800 shadow-sm">
                                <td class="rounded-l-2xl px-4 py-4 font-bold text-[#081426]">
                                    {{ item.pieza }}
                                </td>

                                <td class="max-w-[320px] px-4 py-4 text-slate-600">
                                    {{ item.denominacion }}
                                </td>

                                <td class="px-4 py-4">
                                    {{ formatNumber(item.programado) }}
                                </td>

                                <td class="px-4 py-4">
                                    {{ formatNumber(item.fabricado) }}
                                </td>

                                <td class="px-4 py-4">
                                    {{ formatNumber(item.entregado) }}
                                </td>

                                <td class="px-4 py-4">
                                    {{ formatNumber(item.stock_actual) }}
                                </td>

                                <td class="px-4 py-4">
                                    {{ formatNumber(item.stock_seguridad) }}
                                </td>

                                <td class="px-4 py-4">
                                    {{ formatNumber(item.disponible) }}
                                </td>

                                <td class="px-4 py-4 font-bold text-[#081426]">
                                    {{ formatNumber(item.pendiente) }}
                                </td>

                                <td class="rounded-r-2xl px-4 py-4">
                                    <StatusBadge :label="estadoLabel(item.estado)" :tone="estadoTone(item.estado)" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </DataTable>
        </div>
    </section>
</template>