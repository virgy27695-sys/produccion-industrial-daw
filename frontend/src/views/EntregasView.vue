<script setup>
import { ref, onMounted } from "vue"
import { useHead } from "@vueuse/head"
import { Truck, Trash2, Save } from "lucide-vue-next"

import ConfirmModal from "../components/ui/ConfirmModal.vue"

import {
    getEntregas,
    createEntrega,
    deleteEntrega,
} from "../api/entregas"

import { getPiezas } from "../api/piezas"

useHead({
    title: "Entregas · ISAVEX",
})

const loading = ref(false)
const saving = ref(false)
const error = ref("")

const entregas = ref([])
const piezas = ref([])

const showDeleteModal = ref(false)
const entregaSeleccionada = ref(null)

const form = ref({
    pieza_id: "",
    fecha: new Date().toISOString().split("T")[0],
    anio: new Date().getFullYear(),
    semana: getWeekNumber(new Date()),
    cantidad: 1,
})

function getWeekNumber(date) {
    const target = new Date(date.valueOf())
    const dayNumber = (date.getDay() + 6) % 7

    target.setDate(
        target.getDate() - dayNumber + 3
    )

    const firstThursday =
        target.valueOf()

    target.setMonth(0, 1)

    if (target.getDay() !== 4) {
        target.setMonth(
            0,
            1 + ((4 - target.getDay()) + 7) % 7
        )
    }

    return 1 + Math.ceil(
        (firstThursday - target) /
        604800000
    )
}

function resetForm() {
    form.value = {
        pieza_id: "",
        fecha: new Date()
            .toISOString()
            .split("T")[0],
        anio: new Date().getFullYear(),
        semana: getWeekNumber(
            new Date()
        ),
        cantidad: 1,
    }
}

async function loadData() {

    try {

        loading.value = true
        error.value = ""

        const [
            entregasData,
            piezasData,
        ] = await Promise.all([
            getEntregas(),
            getPiezas(),
        ])

        entregas.value =
            Array.isArray(entregasData)
                ? entregasData
                : []

        piezas.value =
            Array.isArray(piezasData)
                ? piezasData
                : []

    }
    catch {

        error.value =
            "No se pudieron cargar las entregas."

    }
    finally {

        loading.value = false
    }
}

async function saveEntrega() {

    try {

        saving.value = true
        error.value = ""

        await createEntrega({
            pieza_id:
                form.value.pieza_id,

            fecha:
                form.value.fecha,

            anio:
                Number(
                    form.value.anio
                ),

            semana:
                Number(
                    form.value.semana
                ),

            cantidad:
                Number(
                    form.value.cantidad
                ),
        })

        resetForm()

        await loadData()

    }
    catch {

        error.value =
            "No se pudo guardar la entrega."

    }
    finally {

        saving.value = false
    }
}

function removeEntrega(
    entrega
) {
    entregaSeleccionada.value =
        entrega

    showDeleteModal.value =
        true
}

async function confirmDelete() {

    try {

        await deleteEntrega(
            entregaSeleccionada
                .value.id
        )

        await loadData()

    }
    catch {

        error.value =
            "No se pudo eliminar"

    }
    finally {

        showDeleteModal.value =
            false
    }
}

onMounted(loadData)
</script>

<template>

    <section class="space-y-6">

        <ConfirmModal :show="showDeleteModal" title="Eliminar entrega" message="¿Eliminar entrega?"
            confirm-text="Eliminar" cancel-text="Cancelar" @confirm="confirmDelete" @cancel="showDeleteModal = false" />

        <div class="rounded-[2rem] bg-white p-6 shadow">

            <div class="flex items-center gap-4">

                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-cyan-100">

                    <Truck class="h-6 w-6" />

                </div>

                <div>

                    <h1 class="text-3xl font-bold">

                        Entregas

                    </h1>

                    <p class="text-slate-500">

                        Registro de entregas

                    </p>

                </div>

            </div>

        </div>


        <div class="rounded-[2rem] bg-white p-6 shadow">

            <h2 class="mb-6 text-xl font-bold">

                Nueva entrega

            </h2>

            <div class="grid gap-4 md:grid-cols-2">

                <div>

                    <label class="mb-2 block">

                        Pieza

                    </label>

                    <select v-model="form.pieza_id" class="w-full rounded-2xl border px-4 py-3">

                        <option value="">
                            Seleccionar pieza
                        </option>

                        <option v-for="pieza in piezas" :key="pieza.id" :value="pieza.id">

                            {{ pieza.codigo }}
                            -
                            {{ pieza.denominacion }}

                        </option>

                    </select>

                </div>


                <div>

                    <label class="mb-2 block">

                        Fecha

                    </label>

                    <input v-model="form.fecha" type="date" class="w-full rounded-2xl border px-4 py-3" />

                </div>


                <div>

                    <label class="mb-2 block">

                        Semana

                    </label>

                    <input v-model="form.semana" type="number" class="w-full rounded-2xl border px-4 py-3" />

                </div>


                <div>

                    <label class="mb-2 block">

                        Cantidad

                    </label>

                    <input v-model="form.cantidad" type="number" class="w-full rounded-2xl border px-4 py-3" />

                </div>

            </div>


            <button @click="saveEntrega" class="mt-5 rounded-2xl bg-[#081426] px-5 py-3 text-white">

                <Save class="mr-2 inline h-4 w-4" />

                Guardar entrega

            </button>

        </div>


        <div class="rounded-[2rem] bg-white shadow">

            <table class="w-full">

                <tbody>

                    <tr v-for="entrega in entregas" :key="entrega.id">

                        <td class="px-4 py-4">

                            {{ entrega.pieza?.codigo }}

                        </td>

                        <td>

                            {{ entrega.cantidad }}

                        </td>

                        <td>

                            <button @click="removeEntrega(entrega)" class="rounded-xl bg-red-50 px-3 py-2">

                                <Trash2 class="h-4 w-4" />

                            </button>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </section>

</template>