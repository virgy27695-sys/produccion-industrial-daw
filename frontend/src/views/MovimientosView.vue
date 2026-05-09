<script setup>
// IMPORTS
import { computed, onMounted, ref } from "vue"

import { apiGet } from "../api/http"

import {
    getFabricaciones,
    createFabricacion,
} from "../api/fabricaciones"

import {
    getEntregas,
    createEntrega,
} from "../api/entregas"


// ESTADO REACTIVO
const piezas = ref([])          // Piezas disponibles
const moldes = ref([])          // Moldes disponibles

const fabricaciones = ref([])   // Registros de fabricación
const entregas = ref([])        // Registros de entregas

const loading = ref(false)      // Control de carga
const saving = ref(false)       // Control de guardado

const error = ref("")           // Mensaje de error
const success = ref("")         // Mensaje de éxito


// FORMULARIO FABRICACIÓN
// Registra producción real por pieza, molde y turno.
const fabricacionForm = ref({
    pieza_id: "",
    molde_id: "",
    fecha: "",
    turno: "mañana",
    anio: new Date().getFullYear(),
    semana: 1,
    cantidad: 1,
    observaciones: "",
})


// FORMULARIO ENTREGA
// Registra entregas reales al cliente.
const entregaForm = ref({
    pieza_id: "",
    fecha: "",
    anio: new Date().getFullYear(),
    semana: 1,
    cantidad: 1,
})


// CARGAR DATOS
// Se usa Promise.all para cargar datos en paralelo.
async function loadData() {
    loading.value = true
    error.value = ""

    try {
        const [
            piezasData,
            moldesData,
            fabricacionesData,
            entregasData,
        ] = await Promise.all([
            apiGet("/piezas"),
            apiGet("/moldes"),
            getFabricaciones(),
            getEntregas(),
        ])

        piezas.value = piezasData
        moldes.value = moldesData
        fabricaciones.value = fabricacionesData
        entregas.value = entregasData
    } catch (e) {
        error.value = "No se pudieron cargar los movimientos."
        console.error(e)
    } finally {
        loading.value = false
    }
}


// CREAR FABRICACIÓN
// Al guardar, se envía la producción real de un turno.
async function submitFabricacion() {
    saving.value = true
    error.value = ""
    success.value = ""

    try {
        await createFabricacion({
            ...fabricacionForm.value,
            molde_id: fabricacionForm.value.molde_id || null,
            observaciones: fabricacionForm.value.observaciones || null,
        })

        success.value = "Fabricación registrada correctamente."

        fabricacionForm.value = {
            pieza_id: "",
            molde_id: "",
            fecha: "",
            turno: "mañana",
            anio: new Date().getFullYear(),
            semana: 1,
            cantidad: 1,
            observaciones: "",
        }

        await loadData()
    } catch (e) {
        error.value = e.message || "No se pudo registrar la fabricación."
        console.error(e)
    } finally {
        saving.value = false
    }
}


// CREAR ENTREGA
// Registra una entrega realizada al cliente.
async function submitEntrega() {
    saving.value = true
    error.value = ""
    success.value = ""

    try {
        await createEntrega(entregaForm.value)

        success.value = "Entrega registrada correctamente."

        entregaForm.value = {
            pieza_id: "",
            fecha: "",
            anio: new Date().getFullYear(),
            semana: 1,
            cantidad: 1,
        }

        await loadData()
    } catch (e) {
        error.value = e.message || "No se pudo registrar la entrega."
        console.error(e)
    } finally {
        saving.value = false
    }
}


// ÚLTIMAS FABRICACIONES
const ultimasFabricaciones = computed(() =>
    [...fabricaciones.value].slice(0, 10)
)


// ÚLTIMAS ENTREGAS
const ultimasEntregas = computed(() =>
    [...entregas.value].slice(0, 10)
)


// INICIALIZACIÓN
onMounted(loadData)
</script>

<template>
    <section class="space-y-6">
        <div>
            <h2 class="text-2xl font-semibold text-slate-800">
                Movimientos
            </h2>

            <p class="text-slate-600">
                Registro de fabricaciones por turno y entregas reales al cliente.
            </p>
        </div>

        <p v-if="error" class="text-sm text-red-600">
            {{ error }}
        </p>

        <p v-if="success" class="text-sm text-green-600">
            {{ success }}
        </p>

        <p v-if="loading" class="text-sm text-slate-500">
            Cargando movimientos...
        </p>

        <div v-else class="grid gap-6 lg:grid-cols-2">

            <!-- FABRICACIÓN -->
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <h3 class="mb-4 text-lg font-semibold text-slate-800">
                    Registrar fabricación
                </h3>

                <form class="space-y-4" @submit.prevent="submitFabricacion">

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">
                            Pieza
                        </label>

                        <select v-model="fabricacionForm.pieza_id"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2" required>
                            <option value="">Selecciona una pieza</option>

                            <option v-for="pieza in piezas" :key="pieza.id" :value="pieza.id">
                                {{ pieza.codigo }} - {{ pieza.denominacion }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">
                            Molde utilizado
                        </label>

                        <select v-model="fabricacionForm.molde_id"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2">
                            <option value="">Sin molde asignado</option>

                            <option v-for="molde in moldes" :key="molde.id" :value="molde.id">
                                {{ molde.codigo }} - {{ molde.descripcion }}
                            </option>
                        </select>
                    </div>

                    <div class="grid gap-4 md:grid-cols-3">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">
                                Fecha
                            </label>

                            <input v-model="fabricacionForm.fecha" type="date"
                                class="w-full rounded-xl border border-slate-300 px-4 py-2" required />
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">
                                Turno
                            </label>

                            <select v-model="fabricacionForm.turno"
                                class="w-full rounded-xl border border-slate-300 px-4 py-2" required>
                                <option value="mañana">Mañana</option>
                                <option value="tarde">Tarde</option>
                                <option value="noche">Noche</option>
                            </select>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">
                                Cantidad
                            </label>

                            <input v-model.number="fabricacionForm.cantidad" type="number" min="1"
                                class="w-full rounded-xl border border-slate-300 px-4 py-2" required />
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">
                                Año
                            </label>

                            <input v-model.number="fabricacionForm.anio" type="number"
                                class="w-full rounded-xl border border-slate-300 px-4 py-2" required />
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">
                                Semana
                            </label>

                            <input v-model.number="fabricacionForm.semana" type="number" min="1" max="53"
                                class="w-full rounded-xl border border-slate-300 px-4 py-2" required />
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">
                            Observaciones
                        </label>

                        <textarea v-model="fabricacionForm.observaciones" rows="3"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2"
                            placeholder="Incidencias del turno, cambios de molde, paradas..."></textarea>
                    </div>

                    <button type="submit" :disabled="saving"
                        class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60">
                        {{ saving ? "Guardando..." : "Registrar fabricación" }}
                    </button>
                </form>
            </div>


            <!-- ENTREGA -->
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <h3 class="mb-4 text-lg font-semibold text-slate-800">
                    Registrar entrega
                </h3>

                <form class="space-y-4" @submit.prevent="submitEntrega">

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">
                            Pieza
                        </label>

                        <select v-model="entregaForm.pieza_id"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2" required>
                            <option value="">Selecciona una pieza</option>

                            <option v-for="pieza in piezas" :key="pieza.id" :value="pieza.id">
                                {{ pieza.codigo }} - {{ pieza.denominacion }}
                            </option>
                        </select>
                    </div>

                    <div class="grid gap-4 md:grid-cols-3">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">
                                Fecha
                            </label>

                            <input v-model="entregaForm.fecha" type="date"
                                class="w-full rounded-xl border border-slate-300 px-4 py-2" required />
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">
                                Año
                            </label>

                            <input v-model.number="entregaForm.anio" type="number"
                                class="w-full rounded-xl border border-slate-300 px-4 py-2" required />
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">
                                Semana
                            </label>

                            <input v-model.number="entregaForm.semana" type="number" min="1" max="53"
                                class="w-full rounded-xl border border-slate-300 px-4 py-2" required />
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">
                            Cantidad
                        </label>

                        <input v-model.number="entregaForm.cantidad" type="number" min="1"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2" required />
                    </div>

                    <button type="submit" :disabled="saving"
                        class="rounded-xl bg-green-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-green-700 disabled:cursor-not-allowed disabled:opacity-60">
                        {{ saving ? "Guardando..." : "Registrar entrega" }}
                    </button>
                </form>
            </div>
        </div>

        <!-- ÚLTIMOS MOVIMIENTOS -->
        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <h3 class="mb-4 text-lg font-semibold text-slate-800">
                    Últimas fabricaciones
                </h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b text-left text-slate-500">
                                <th class="px-3 py-2">Fecha</th>
                                <th class="px-3 py-2">Turno</th>
                                <th class="px-3 py-2">Pieza</th>
                                <th class="px-3 py-2">Molde</th>
                                <th class="px-3 py-2">Cantidad</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="fabricacion in ultimasFabricaciones" :key="fabricacion.id" class="border-b">
                                <td class="px-3 py-2">{{ fabricacion.fecha }}</td>
                                <td class="px-3 py-2">{{ fabricacion.turno }}</td>
                                <td class="px-3 py-2">
                                    {{ fabricacion.pieza?.codigo || "—" }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ fabricacion.molde?.codigo || "—" }}
                                </td>
                                <td class="px-3 py-2 font-semibold">
                                    {{ fabricacion.cantidad }}
                                </td>
                            </tr>

                            <tr v-if="ultimasFabricaciones.length === 0">
                                <td colspan="5" class="px-3 py-4 text-center text-slate-500">
                                    No hay fabricaciones registradas.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <h3 class="mb-4 text-lg font-semibold text-slate-800">
                    Últimas entregas
                </h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b text-left text-slate-500">
                                <th class="px-3 py-2">Fecha</th>
                                <th class="px-3 py-2">Pieza</th>
                                <th class="px-3 py-2">Semana</th>
                                <th class="px-3 py-2">Cantidad</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="entrega in ultimasEntregas" :key="entrega.id" class="border-b">
                                <td class="px-3 py-2">{{ entrega.fecha }}</td>
                                <td class="px-3 py-2">
                                    {{ entrega.pieza?.codigo || "—" }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ entrega.anio }}-S{{ String(entrega.semana).padStart(2, "0") }}
                                </td>
                                <td class="px-3 py-2 font-semibold">
                                    {{ entrega.cantidad }}
                                </td>
                            </tr>

                            <tr v-if="ultimasEntregas.length === 0">
                                <td colspan="4" class="px-3 py-4 text-center text-slate-500">
                                    No hay entregas registradas.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </section>
</template>