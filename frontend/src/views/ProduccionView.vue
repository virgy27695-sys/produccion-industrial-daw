<script setup>
import { computed, onMounted, ref } from "vue"
import { useHead } from "@vueuse/head"

import {
    Factory,
    Boxes,
    RotateCcw,
    Search,
    Activity,
    CalendarDays,
} from "lucide-vue-next"

import { getResumenProduccion } from "../api/produccion"
import { useCrud } from "../composables/useCrud"

useHead({
    title: "Producción · ISAVEX",
})

const resumen = ref([])
const busqueda = ref("")

const {
    loading,
    error,
    executeLoad,
} = useCrud()


const resumenFiltrado = computed(() => {
    const texto = busqueda.value
        .trim()
        .toLowerCase()

    if (!texto) return resumen.value

    return resumen.value.filter((item) =>
        (
            (item.molde_codigo || "")
            + (item.referencias || "")
            + (item.modelo || "")
            + item.anio
            + item.semana
        )
            .toLowerCase()
            .includes(texto)
    )
})


// KPI producción prevista
const totalPiezas = computed(() =>
    resumenFiltrado.value.reduce(
        (total, item) =>
            total +
            Number(item.cantidad_prevista || 0),
        0
    )
)


// KPI ciclos
const totalCiclos = computed(() =>
    resumenFiltrado.value.reduce(
        (total, item) =>
            total +
            Number(item.ciclos_necesarios || 0),
        0
    )
)


// KPI moldes
const totalMoldes = computed(() => {
    return new Set(
        resumenFiltrado.value.map(
            (item) => item.molde_codigo
        )
    ).size
})


// KPI semanas
const semanasActivas = computed(() => {
    return new Set(
        resumenFiltrado.value.map(
            (item) =>
                `${item.anio}-${item.semana}`
        )
    ).size
})


// Top moldes
const topMoldes = computed(() =>
    [...resumenFiltrado.value]
        .sort(
            (a, b) =>
                Number(
                    b.cantidad_prevista || 0
                ) -
                Number(
                    a.cantidad_prevista || 0
                )
        )
        .slice(0, 4)
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

    if (cantidad >= 10000)
        return "Alta"

    if (cantidad >= 3000)
        return "Media"

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

onMounted(
    loadResumenProduccion
)
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

                        Planificación por molde y referencias asociadas.

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

                            Producción

                        </p>

                        <p class="text-3xl font-bold">

                            {{ formatNumber(totalPiezas) }}

                        </p>

                    </div>

                    <div class="rounded-3xl bg-white p-5">

                        <RotateCcw class="mb-3 h-5 w-5 text-violet-700" />

                        <p class="text-xs text-slate-400">

                            Ciclos

                        </p>

                        <p class="text-3xl font-bold">

                            {{ formatNumber(totalCiclos) }}

                        </p>

                    </div>

                    <div class="rounded-3xl bg-white p-5">

                        <CalendarDays class="mb-3 h-5 w-5 text-green-700" />

                        <p class="text-xs text-slate-400">

                            Semanas

                        </p>

                        <p class="text-3xl font-bold">

                            {{ semanasActivas }}

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


        <div class="grid gap-4">

            <div v-for="item in resumenFiltrado" :key="`${item.molde_codigo}-${item.semana}`"
                class="rounded-[2rem] border border-white/70 bg-white p-6 shadow">

                <div class="flex justify-between">

                    <div>

                        <h2 class="text-xl font-bold text-[#081426]">

                            {{ item.molde_codigo }}

                        </h2>

                        <p class="mt-2 text-sm text-slate-500">

                            Ref:
                            {{ item.referencias }}

                        </p>

                        <p class="mt-1 text-sm text-slate-500">

                            Modelo:
                            {{ item.modelo }}

                        </p>

                    </div>

                    <span class="rounded-full px-3 py-1 text-xs font-bold" :class="intensidadClass(item)">

                        {{ intensidadProduccion(item) }}

                    </span>

                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-4">

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
                            Producción
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
                            Ciclos
                        </p>

                        <p class="font-bold">
                            {{ formatNumber(item.ciclos_necesarios) }}
                        </p>
                    </div>

                </div>

            </div>

            <div v-if="!resumenFiltrado.length" class="rounded-[2rem] bg-slate-50 p-8 text-center text-slate-500">

                No hay producción disponible.

            </div>

        </div>

    </section>
</template>