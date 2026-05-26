<script setup>
import { computed, onMounted, ref } from "vue"
import { useHead } from "@vueuse/head"

import {
    Factory,
    Boxes,
    Search,
    Activity,
    CalendarDays,
} from "lucide-vue-next"

import { getResumenProduccion } from "../api/produccion"
import { useCrud } from "../composables/useCrud"
import { getCurrentUser } from "../utils/auth"

useHead({
    title: "Producción · ISAVEX",
})

const resumen = ref([])
const busqueda = ref("")
const estadosProduccion = ref({})

const {
    loading,
    error,
    executeLoad,
} = useCrud()

const currentUser = computed(() =>
    getCurrentUser()
)

const canChangeEstado = computed(() =>
    ["admin", "encargado"].includes(
        currentUser.value?.role
    )
)

const resumenFiltrado = computed(() => {
    const texto = busqueda.value
        .trim()
        .toLowerCase()

    if (!texto) {
        return resumen.value
    }

    return resumen.value.filter((item) =>
        (
            (item.molde_codigo || "") +
            (item.referencias || "") +
            (item.modelo || "") +
            item.anio +
            item.semana
        )
            .toLowerCase()
            .includes(texto)
    )
})

const totalPiezas = computed(() =>
    resumenFiltrado.value.reduce(
        (total, item) =>
            total +
            Number(item.cantidad_prevista || 0),
        0
    )
)

const totalMoldes = computed(() => {
    return new Set(
        resumenFiltrado.value.map(
            (item) => item.molde_codigo
        )
    ).size
})

const produccionesEnCurso = computed(() =>
    resumenFiltrado.value.filter(
        (item) =>
            getEstadoProduccion(item) === "en_produccion"
    ).length
)

const produccionesFinalizadas = computed(() =>
    resumenFiltrado.value.filter(
        (item) =>
            getEstadoProduccion(item) === "finalizado"
    ).length
)

async function loadResumenProduccion() {
    await executeLoad(async () => {
        const data =
            await getResumenProduccion()

        resumen.value =
            Array.isArray(data)
                ? data
                : []
    })
}

function loadEstadosProduccion() {
    estadosProduccion.value =
        JSON.parse(
            localStorage.getItem("estadosProduccion") ||
            "{}"
        )
}

function saveEstadosProduccion() {
    localStorage.setItem(
        "estadosProduccion",
        JSON.stringify(
            estadosProduccion.value
        )
    )
}

function getProduccionKey(item) {
    return [
        item.molde_codigo,
        item.referencias,
        item.anio,
        item.semana,
    ].join("-")
}

function getEstadoProduccion(item) {
    return (
        estadosProduccion.value[
        getProduccionKey(item)
        ] || "pendiente"
    )
}

function updateEstadoProduccion(item, estado) {
    if (!canChangeEstado.value) {
        return
    }

    estadosProduccion.value = {
        ...estadosProduccion.value,
        [getProduccionKey(item)]: estado,
    }

    saveEstadosProduccion()
}

function estadoProduccionLabel(estado) {
    return {
        pendiente: "Pendiente",
        en_produccion: "En producción",
        finalizado: "Finalizado",
    }[estado] || "Pendiente"
}

function estadoProduccionClass(estado) {
    return {
        "bg-slate-100 text-slate-700":
            estado === "pendiente",

        "bg-cyan-100 text-cyan-700":
            estado === "en_produccion",

        "bg-green-100 text-green-700":
            estado === "finalizado",
    }
}

function formatSemana(item) {
    return `${item.anio}-S${String(
        item.semana
    ).padStart(2, "0")}`
}

function formatNumber(value) {
    return Number(
        value || 0
    ).toLocaleString("es-ES")
}

function intensidadProduccion(item) {
    const cantidad =
        Number(
            item.cantidad_prevista || 0
        )

    if (cantidad >= 10000) {
        return "Alta"
    }

    if (cantidad >= 3000) {
        return "Media"
    }

    return "Baja"
}

function intensidadClass(item) {
    const intensidad =
        intensidadProduccion(item)

    return {
        "bg-red-100 text-red-700":
            intensidad === "Alta",

        "bg-amber-100 text-amber-700":
            intensidad === "Media",

        "bg-green-100 text-green-700":
            intensidad === "Baja",
    }
}

onMounted(async () => {
    loadEstadosProduccion()
    await loadResumenProduccion()
})
</script>

<template>
    <section class="space-y-6">
        <div class="relative overflow-hidden rounded-[2rem] border border-white/70 bg-white/75 p-6 shadow-xl">
            <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-[#59C7D8]/20 blur-3xl" />

            <div class="relative z-10 flex flex-col gap-6 xl:flex-row xl:justify-between">
                <div>
                    <div
                        class="mb-3 inline-flex rounded-full border border-[#59C7D8]/40 bg-white/70 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.25em] text-[#1597A8]">
                        Planning industrial
                    </div>

                    <h1 class="text-3xl font-bold text-[#081426]">
                        Producción
                    </h1>

                    <p class="mt-3 text-sm text-slate-600">
                        Órdenes de fabricación agrupadas por molde, semana y referencias asociadas.
                    </p>

                    <p v-if="canChangeEstado" class="mt-2 text-xs font-medium text-cyan-700">
                        El encargado puede indicar el estado operativo del molde una sola vez para todas sus
                        referencias.
                    </p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-3xl bg-white p-5">
                        <Factory class="mb-3 h-5 w-5 text-cyan-700" />

                        <p class="text-xs text-slate-400">
                            Moldes
                        </p>

                        <p class="text-3xl font-bold">
                            {{ totalMoldes }}
                        </p>
                    </div>

                    <div class="rounded-3xl bg-white p-5">
                        <Boxes class="mb-3 h-5 w-5 text-blue-700" />

                        <p class="text-xs text-slate-400">
                            Producción prevista
                        </p>

                        <p class="text-3xl font-bold">
                            {{ formatNumber(totalPiezas) }}
                        </p>
                    </div>

                    <div class="rounded-3xl bg-white p-5">
                        <Activity class="mb-3 h-5 w-5 text-orange-700" />

                        <p class="text-xs text-slate-400">
                            En producción
                        </p>

                        <p class="text-3xl font-bold">
                            {{ produccionesEnCurso }}
                        </p>
                    </div>

                    <div class="rounded-3xl bg-white p-5">
                        <CalendarDays class="mb-3 h-5 w-5 text-green-700" />

                        <p class="text-xs text-slate-400">
                            Finalizadas
                        </p>

                        <p class="text-3xl font-bold">
                            {{ produccionesFinalizadas }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-[2rem] border border-white/70 bg-white p-4">
            <div class="flex items-center gap-3 rounded-2xl border px-4 py-3">
                <Search class="h-5 w-5 text-slate-400" />

                <input v-model="busqueda" type="text" placeholder="Buscar molde, referencia o modelo..."
                    class="w-full outline-none" />
            </div>
        </div>

        <p v-if="error" class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
            {{ error }}
        </p>

        <div v-if="loading" class="rounded-[2rem] bg-white p-8 text-center text-slate-500">
            Cargando producción...
        </div>

        <div v-else class="grid gap-4">
            <div v-for="item in resumenFiltrado" :key="getProduccionKey(item)"
                class="rounded-[2rem] border border-white/70 bg-white p-6 shadow">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-[#081426]">
                            Molde {{ item.molde_codigo }}
                        </h2>

                        <p class="mt-2 text-sm text-slate-500">
                            Referencias:
                            {{ item.referencias }}
                        </p>

                        <p class="mt-1 text-sm text-slate-500">
                            Modelo:
                            {{ item.modelo || "Sin modelo" }}
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-full px-3 py-1 text-xs font-bold" :class="intensidadClass(item)">
                            {{ intensidadProduccion(item) }}
                        </span>

                        <span class="rounded-full px-3 py-1 text-xs font-bold"
                            :class="estadoProduccionClass(getEstadoProduccion(item))">
                            {{ estadoProduccionLabel(getEstadoProduccion(item)) }}
                        </span>
                    </div>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-5">
                    <div>
                        <p class="text-xs text-slate-400">
                            Semana
                        </p>

                        <p class="font-bold">
                            {{ formatSemana(item) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-400">
                            Cantidad por referencia
                        </p>

                        <p class="font-bold">
                            {{ formatNumber(item.cantidad_prevista) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-400">
                            Cavidades
                        </p>

                        <p class="font-bold">
                            {{ item.cavidades }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-400">
                            Ciclos necesarios
                        </p>

                        <p class="font-bold">
                            {{ formatNumber(item.ciclos_necesarios) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-400">
                            Estado
                        </p>

                        <p class="font-bold">
                            {{ estadoProduccionLabel(getEstadoProduccion(item)) }}
                        </p>
                    </div>
                </div>

                <div v-if="canChangeEstado" class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="mb-3 text-sm font-semibold text-slate-700">
                        Estado operativo del molde
                    </p>

                    <div class="flex flex-wrap gap-2">
                        <button type="button" @click="updateEstadoProduccion(item, 'pendiente')"
                            class="rounded-xl px-4 py-2 text-sm font-semibold" :class="getEstadoProduccion(item) === 'pendiente'
                                    ? 'bg-slate-800 text-white'
                                    : 'bg-white text-slate-600'
                                ">
                            Pendiente
                        </button>

                        <button type="button" @click="updateEstadoProduccion(item, 'en_produccion')"
                            class="rounded-xl px-4 py-2 text-sm font-semibold" :class="getEstadoProduccion(item) === 'en_produccion'
                                    ? 'bg-cyan-600 text-white'
                                    : 'bg-white text-slate-600'
                                ">
                            En producción
                        </button>

                        <button type="button" @click="updateEstadoProduccion(item, 'finalizado')"
                            class="rounded-xl px-4 py-2 text-sm font-semibold" :class="getEstadoProduccion(item) === 'finalizado'
                                    ? 'bg-green-600 text-white'
                                    : 'bg-white text-slate-600'
                                ">
                            Finalizado
                        </button>
                    </div>

                    <p class="mt-3 text-xs text-slate-500">
                        Esta acción se aplica al molde completo y a todas las referencias asociadas. El parte de
                        producción registrará las cantidades reales fabricadas.
                    </p>
                </div>
            </div>

            <div v-if="!resumenFiltrado.length" class="rounded-[2rem] bg-slate-50 p-8 text-center text-slate-500">
                No hay producción disponible.
            </div>
        </div>
    </section>
</template>