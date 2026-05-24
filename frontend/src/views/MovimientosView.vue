<script setup>
import { computed, onMounted, ref } from "vue"
import { useHead } from "@vueuse/head"

import {
    Boxes,
    AlertTriangle,
    PackageCheck,
} from "lucide-vue-next"

import { apiGet } from "../api/http"

useHead({
    title: "Stock · ISAVEX",
})

const loading = ref(false)
const error = ref("")

const piezas = ref([])

async function loadData() {

    loading.value = true
    error.value = ""

    try {

        piezas.value =
            await apiGet(
                "/piezas"
            )

    } catch {

        error.value =
            "No se pudo cargar el stock."

    } finally {

        loading.value = false
    }
}


const piezasCriticas = computed(() => {

    return piezas.value.filter(
        pieza =>
            Number(
                pieza.stock || 0
            ) <
            Number(
                pieza.stock_seguridad_dias || 0
            )
    )
})


function estadoStock(
    pieza
) {

    const stock =
        Number(
            pieza.stock || 0
        )

    const seguridad =
        Number(
            pieza.stock_seguridad_dias || 0
        )

    if (
        stock <= seguridad
    ) {
        return "Crítico"
    }

    if (
        stock <=
        seguridad * 1.5
    ) {
        return "Bajo"
    }

    return "Correcto"
}


function estadoClass(
    pieza
) {

    const estado =
        estadoStock(
            pieza
        )

    return {

        "bg-red-100 text-red-700":
            estado ===
            "Crítico",

        "bg-amber-100 text-amber-700":
            estado ===
            "Bajo",

        "bg-green-100 text-green-700":
            estado ===
            "Correcto",
    }
}

onMounted(
    loadData
)
</script>

<template>

    <section class="space-y-6">

        <div class="rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-xl">

            <div class="flex items-center gap-4">

                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-cyan-100 text-cyan-700">

                    <Boxes class="h-7 w-7" />

                </div>

                <div>

                    <h1 class="text-3xl font-bold text-[#081426]">

                        Stock y almacén

                    </h1>

                    <p class="mt-1 text-sm text-slate-500">

                        Control de stock y piezas críticas.

                    </p>

                </div>

            </div>

        </div>


        <div class="grid gap-4 md:grid-cols-3">

            <div class="rounded-[2rem] bg-white p-6 shadow">

                <PackageCheck class="mb-3 h-6 w-6 text-green-700" />

                <p class="text-sm text-slate-400">

                    Total piezas

                </p>

                <p class="text-3xl font-bold">

                    {{ piezas.length }}

                </p>

            </div>


            <div class="rounded-[2rem] bg-white p-6 shadow">

                <AlertTriangle class="mb-3 h-6 w-6 text-red-700" />

                <p class="text-sm text-slate-400">

                    Piezas críticas

                </p>

                <p class="text-3xl font-bold">

                    {{ piezasCriticas.length }}

                </p>

            </div>

        </div>


        <div class="rounded-[2rem] bg-white shadow overflow-hidden">

            <div class="border-b px-6 py-4">

                <h2 class="text-xl font-bold">

                    Estado de stock

                </h2>

            </div>

            <div v-if="loading" class="p-8 text-center text-slate-500">

                Cargando stock...

            </div>

            <div v-else-if="!piezas.length" class="p-8 text-center text-slate-500">

                No hay piezas disponibles.

            </div>

            <div v-else class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead>

                        <tr class="border-b text-left text-slate-400">

                            <th class="px-4 py-3">
                                Código
                            </th>

                            <th class="px-4 py-3">
                                Descripción
                            </th>

                            <th class="px-4 py-3">
                                Stock
                            </th>

                            <th class="px-4 py-3">
                                Seguridad
                            </th>

                            <th class="px-4 py-3">
                                Estado
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        <tr v-for="pieza in piezas" :key="pieza.id" class="border-b">

                            <td class="px-4 py-4 font-semibold">

                                {{ pieza.codigo }}

                            </td>

                            <td class="px-4 py-4">

                                {{ pieza.denominacion }}

                            </td>

                            <td class="px-4 py-4">

                                {{ pieza.stock || 0 }}

                            </td>

                            <td class="px-4 py-4">

                                {{ pieza.stock_seguridad_dias || 0 }}

                            </td>

                            <td class="px-4 py-4">

                                <span class="rounded-full px-3 py-1 text-xs font-semibold" :class="estadoClass(pieza)">

                                    {{ estadoStock(pieza) }}

                                </span>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </section>

</template>