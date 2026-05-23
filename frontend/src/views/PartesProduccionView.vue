<script setup>
import { ref, onMounted } from "vue"

import {
    getPartesProduccion,
    createParteProduccion,
    validarParteProduccion,
    corregirParteProduccion,
    deleteParteProduccion,
} from "../api/partesProduccion"

import { getPiezas } from "../api/piezas"
import { getMoldes } from "../api/moldes"

import {
    getCurrentUser,
    canValidateProduction,
} from "../utils/auth"

const canValidate = canValidateProduction()

const loading = ref(false)
const saving = ref(false)
const error = ref("")

const partes = ref([])
const piezas = ref([])
const moldes = ref([])

const form = ref({
    user_id: null,
    pieza_id: "",
    molde_id: "",
    fecha: new Date().toISOString().split("T")[0],
    turno: "mañana",
    maquina: "",
    cantidad_fabricada: 0,
    cantidad_buena: 0,
    cantidad_rechazada: 0,
    minutos_paro: 0,
    motivo_paro: "",
    motivo_rechazo: "",
    averia: "",
    estado: "pendiente",
    observaciones: "",
})

function resetForm() {
    const user = getCurrentUser()

    form.value = {
        user_id: user?.id || null,
        pieza_id: "",
        molde_id: "",
        fecha: new Date().toISOString().split("T")[0],
        turno: "mañana",
        maquina: "",
        cantidad_fabricada: 0,
        cantidad_buena: 0,
        cantidad_rechazada: 0,
        minutos_paro: 0,
        motivo_paro: "",
        motivo_rechazo: "",
        averia: "",
        estado: "pendiente",
        observaciones: "",
    }
}

async function loadData() {
    try {
        loading.value = true
        error.value = ""

        const [partesData, piezasData, moldesData] = await Promise.all([
            getPartesProduccion(),
            getPiezas(),
            getMoldes(),
        ])

        partes.value = Array.isArray(partesData) ? partesData : []
        piezas.value = Array.isArray(piezasData) ? piezasData : []
        moldes.value = Array.isArray(moldesData) ? moldesData : []
    } catch (e) {
        error.value = "No se pudieron cargar los datos."
        console.error(e)
    } finally {
        loading.value = false
    }
}

async function saveParte() {
    const user = getCurrentUser()

    if (!user) {
        error.value = "No hay usuario autenticado."
        return
    }

    if (!form.value.pieza_id) {
        error.value = "Debes seleccionar una pieza."
        return
    }

    try {
        saving.value = true
        error.value = ""

        await createParteProduccion({
            ...form.value,
            user_id: user.id,
            molde_id: form.value.molde_id || null,
            cantidad_fabricada: Number(form.value.cantidad_fabricada || 0),
            cantidad_buena: Number(form.value.cantidad_buena || 0),
            cantidad_rechazada: Number(form.value.cantidad_rechazada || 0),
            minutos_paro: Number(form.value.minutos_paro || 0),
        })

        resetForm()
        await loadData()
    } catch (e) {
        error.value = "No se pudo guardar el parte de producción."
        console.error(e)
    } finally {
        saving.value = false
    }
}

async function updateEstado(parte, estado) {
    try {
        error.value = ""

        if (estado === "validado") {
            await validarParteProduccion(parte.id)
        } else {
            await corregirParteProduccion(parte.id)
        }

        await loadData()
    } catch (e) {
        error.value = "No se pudo actualizar el estado."
        console.error(e)
    }
}

async function deleteParte(parte) {
    if (!confirm("¿Eliminar este parte?")) return

    try {
        error.value = ""

        await deleteParteProduccion(parte.id)

        await loadData()
    } catch (e) {
        error.value = "No se pudo eliminar el parte."
        console.error(e)
    }
}

onMounted(async () => {
    resetForm()
    await loadData()
})
</script>

<template>
    <section class="space-y-5">
        <div class="isavex-card p-5">
            <h1 class="text-2xl font-black text-slate-900">
                Partes de producción
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                Registro de fabricación, incidencias, averías y paros por turno.
            </p>
        </div>

        <p v-if="error" class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
            {{ error }}
        </p>

        <div class="isavex-card p-5">
            <h2 class="mb-4 text-xl font-bold text-slate-900">
                Nuevo parte
            </h2>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Pieza
                    </label>

                    <select v-model="form.pieza_id"
                        class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm outline-none">
                        <option value="">Seleccionar pieza</option>

                        <option v-for="pieza in piezas" :key="pieza.id" :value="pieza.id">
                            {{ pieza.codigo || pieza.nombre || pieza.denominacion }}
                        </option>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Molde
                    </label>

                    <select v-model="form.molde_id"
                        class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm outline-none">
                        <option value="">Seleccionar molde</option>

                        <option v-for="molde in moldes" :key="molde.id" :value="molde.id">
                            {{ molde.codigo || molde.nombre }}
                        </option>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Fecha
                    </label>

                    <input v-model="form.fecha" type="date"
                        class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm outline-none" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Turno
                    </label>

                    <select v-model="form.turno"
                        class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm outline-none">
                        <option value="mañana">Mañana</option>
                        <option value="tarde">Tarde</option>
                        <option value="noche">Noche</option>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Máquina
                    </label>

                    <input v-model="form.maquina" placeholder="Ej. Inyectora 3"
                        class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm outline-none" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Fabricadas
                    </label>

                    <input v-model="form.cantidad_fabricada" type="number" min="0"
                        class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm outline-none" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Buenas
                    </label>

                    <input v-model="form.cantidad_buena" type="number" min="0"
                        class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm outline-none" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Rechazadas
                    </label>

                    <input v-model="form.cantidad_rechazada" type="number" min="0"
                        class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm outline-none" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Minutos paro
                    </label>

                    <input v-model="form.minutos_paro" type="number" min="0"
                        class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm outline-none" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Motivo paro
                    </label>

                    <input v-model="form.motivo_paro" placeholder="Ej. cambio molde"
                        class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm outline-none" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Motivo rechazo
                    </label>

                    <input v-model="form.motivo_rechazo" placeholder="Ej. pieza deformada"
                        class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm outline-none" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Avería
                    </label>

                    <select v-model="form.averia"
                        class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm outline-none">
                        <option value="">Sin avería</option>
                        <option value="molde">Molde</option>
                        <option value="maquina">Máquina</option>
                        <option value="calentadores">Calentadores</option>
                        <option value="seprom">Seprom</option>
                        <option value="otra">Otra</option>
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <label class="mb-2 block text-sm font-semibold text-slate-700">
                    Observaciones
                </label>

                <textarea v-model="form.observaciones" rows="4" placeholder="Observaciones del turno..."
                    class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm outline-none" />
            </div>

            <button type="button" :disabled="saving" @click="saveParte"
                class="mt-5 rounded-2xl bg-[#081426] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#10233D] disabled:cursor-not-allowed disabled:opacity-60">
                {{ saving ? "Guardando..." : "Guardar parte" }}
            </button>
        </div>

        <div class="isavex-card overflow-hidden">
            <div class="border-b border-slate-100 px-5 py-4">
                <h2 class="font-semibold text-slate-800">
                    Historial de partes
                </h2>
            </div>

            <div v-if="loading" class="p-8 text-center text-slate-400">
                Cargando partes...
            </div>

            <div v-else-if="!partes.length" class="p-8 text-center text-slate-400">
                No existen partes registrados.
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs uppercase tracking-[0.18em] text-slate-400">
                            <th class="px-4 py-3">Fecha</th>
                            <th class="px-4 py-3">Pieza</th>
                            <th class="px-4 py-3">Molde</th>
                            <th class="px-4 py-3">Turno</th>
                            <th class="px-4 py-3">Máquina</th>
                            <th class="px-4 py-3">Fabricadas</th>
                            <th class="px-4 py-3">Buenas</th>
                            <th class="px-4 py-3">Rechazadas</th>
                            <th class="px-4 py-3">Paro</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="parte in partes" :key="parte.id" class="border-t border-slate-100">
                            <td class="px-4 py-3">
                                {{ parte.fecha }}
                            </td>

                            <td class="px-4 py-3">
                                {{ parte.pieza?.codigo || parte.pieza?.nombre || parte.pieza?.denominacion || "-" }}
                            </td>

                            <td class="px-4 py-3">
                                {{ parte.molde?.codigo || parte.molde?.nombre || "-" }}
                            </td>

                            <td class="px-4 py-3 capitalize">
                                {{ parte.turno }}
                            </td>

                            <td class="px-4 py-3">
                                {{ parte.maquina || "-" }}
                            </td>

                            <td class="px-4 py-3 font-semibold">
                                {{ parte.cantidad_fabricada }}
                            </td>

                            <td class="px-4 py-3 text-green-700">
                                {{ parte.cantidad_buena }}
                            </td>

                            <td class="px-4 py-3 text-red-700">
                                {{ parte.cantidad_rechazada }}
                            </td>

                            <td class="px-4 py-3">
                                {{ parte.minutos_paro }} min
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex flex-col gap-2">
                                    <span
                                        class="w-fit rounded-xl bg-cyan-100 px-3 py-1 text-xs font-semibold text-cyan-700">
                                        {{ parte.estado }}
                                    </span>

                                    <div v-if="canValidate" class="flex gap-2">
                                        <button @click="updateEstado(parte, 'validado')"
                                            class="rounded-lg bg-green-100 px-2 py-1 text-xs font-semibold text-green-700">
                                            Validar
                                        </button>

                                        <button @click="updateEstado(parte, 'corregido')"
                                            class="rounded-lg bg-orange-100 px-2 py-1 text-xs font-semibold text-orange-700">
                                            Corregir
                                        </button>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                <button @click="deleteParte(parte)"
                                    class="rounded-lg bg-red-100 px-2 py-1 text-xs font-semibold text-red-700">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</template>