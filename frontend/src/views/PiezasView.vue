<script setup>
import { computed, onMounted, ref } from "vue"
import { getClientes } from "../api/clientes"
import { getModelos } from "../api/modelos"
import {
    getPiezas,
    createPieza,
    updatePieza,
    deletePieza,
} from "../api/piezas"
import { useToastStore } from "../stores/toast"

const toast = useToastStore()

const piezas = ref([])
const clientes = ref([])
const modelos = ref([])
const loading = ref(false)
const saving = ref(false)
const error = ref("")
const formError = ref("")
const busqueda = ref("")

const form = ref({
    id: null,
    codigo: "",
    denominacion: "",
    cliente_id: "",
    modelo_id: "",
})

const isEditing = computed(() => form.value.id !== null)

const modelosFiltrados = computed(() => {
    if (!form.value.cliente_id) return []
    return modelos.value.filter(
        (m) => Number(m.cliente_id) === Number(form.value.cliente_id)
    )
})

const piezasFiltradas = computed(() => {
    const texto = busqueda.value.trim().toLowerCase()
    if (!texto) return piezas.value

    return piezas.value.filter((pieza) => {
        return (
            pieza.codigo.toLowerCase().includes(texto) ||
            pieza.denominacion.toLowerCase().includes(texto) ||
            pieza.modelo?.nombre?.toLowerCase().includes(texto) ||
            pieza.modelo?.cliente?.nombre?.toLowerCase().includes(texto)
        )
    })
})

async function loadData() {
    loading.value = true
    error.value = ""

    try {
        const [piezasData, clientesData, modelosData] = await Promise.all([
            getPiezas(),
            getClientes(),
            getModelos(),
        ])

        piezas.value = piezasData
        clientes.value = clientesData
        modelos.value = modelosData
    } catch (e) {
        error.value = "No se pudieron cargar las piezas."
        console.error(e)
    } finally {
        loading.value = false
    }
}

function resetForm() {
    form.value = {
        id: null,
        codigo: "",
        denominacion: "",
        cliente_id: "",
        modelo_id: "",
    }
    formError.value = ""
}

function editPieza(pieza) {
    form.value = {
        id: pieza.id,
        codigo: pieza.codigo,
        denominacion: pieza.denominacion,
        cliente_id: pieza.modelo?.cliente?.id ?? "",
        modelo_id: pieza.modelo_id,
    }
    formError.value = ""
}

function onClienteChange() {
    form.value.modelo_id = ""
}

async function submitForm() {
    formError.value = ""

    if (!form.value.codigo.trim()) {
        formError.value = "El código es obligatorio."
        return
    }

    if (!form.value.denominacion.trim()) {
        formError.value = "La denominación es obligatoria."
        return
    }

    if (!form.value.cliente_id) {
        formError.value = "Debes seleccionar un cliente."
        return
    }

    if (!form.value.modelo_id) {
        formError.value = "Debes seleccionar un modelo."
        return
    }

    saving.value = true

    try {
        const payload = {
            codigo: form.value.codigo.trim(),
            denominacion: form.value.denominacion.trim(),
            modelo_id: form.value.modelo_id,
        }

        if (isEditing.value) {
            await updatePieza(form.value.id, payload)
            toast.show("Pieza actualizada")
        } else {
            await createPieza(payload)
            toast.show("Pieza creada correctamente")
        }

        resetForm()
        await loadData()
    } catch (e) {
        formError.value = "No se pudo guardar la pieza."
        toast.show("Error al guardar pieza", "error")
        console.error(e)
    } finally {
        saving.value = false
    }
}

async function removePieza(pieza) {
    const confirmacion = window.confirm(
        `¿Seguro que quieres eliminar la pieza "${pieza.codigo}"?`
    )

    if (!confirmacion) return

    try {
        await deletePieza(pieza.id)
        toast.show("Pieza eliminada")
        await loadData()
    } catch (e) {
        error.value = "No se pudo eliminar la pieza."
        toast.show("Error al eliminar pieza", "error")
        console.error(e)
    }
}

onMounted(loadData)
</script>

<template>
    <section class="space-y-6">
        <div>
            <h2 class="mb-4 text-2xl font-semibold text-slate-800">Piezas</h2>
            <p class="text-slate-600">
                Gestión de piezas fabricadas, asociadas a modelos y clientes.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 xl:col-span-1">
                <h3 class="mb-4 text-lg font-semibold text-slate-800">
                    {{ isEditing ? "Editar pieza" : "Nueva pieza" }}
                </h3>

                <form class="space-y-4" @submit.prevent="submitForm">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">
                            Código
                        </label>
                        <input v-model="form.codigo" type="text"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2 text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                            placeholder="Ej. 90112502" />
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">
                            Denominación
                        </label>
                        <input v-model="form.denominacion" type="text"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2 text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                            placeholder="Ej. SOPORTE INF DRL..." />
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">
                            Cliente
                        </label>
                        <select v-model="form.cliente_id" @change="onClienteChange"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2 text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                            <option value="">Selecciona un cliente</option>
                            <option v-for="cliente in clientes" :key="cliente.id" :value="cliente.id">
                                {{ cliente.nombre }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">
                            Modelo
                        </label>
                        <select v-model="form.modelo_id"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2 text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                            <option value="">Selecciona un modelo</option>
                            <option v-for="modelo in modelosFiltrados" :key="modelo.id" :value="modelo.id">
                                {{ modelo.nombre }}
                            </option>
                        </select>
                    </div>

                    <p v-if="formError" class="text-sm text-red-600">
                        {{ formError }}
                    </p>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button type="submit" :disabled="saving"
                            class="rounded-xl bg-blue-600 px-4 py-2 font-medium text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60">
                            {{ saving ? "Guardando..." : isEditing ? "Actualizar" : "Crear pieza" }}
                        </button>

                        <button v-if="isEditing" type="button" @click="resetForm"
                            class="rounded-xl border border-slate-300 px-4 py-2 font-medium text-slate-700 transition hover:bg-slate-100">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 xl:col-span-2">
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="text-lg font-semibold text-slate-800">
                        Listado de piezas
                    </h3>

                    <input v-model="busqueda" type="text"
                        placeholder="Buscar por código, denominación, modelo o cliente"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200 sm:max-w-md" />
                </div>

                <p v-if="error" class="mb-4 text-sm text-red-600">
                    {{ error }}
                </p>

                <p v-if="loading" class="text-sm text-slate-500">
                    Cargando piezas...
                </p>

                <div v-else class="overflow-x-auto">
                    <table class="min-w-full border-separate border-spacing-y-2">
                        <thead>
                            <tr class="text-left text-sm text-slate-500">
                                <th class="px-4 py-2">Código</th>
                                <th class="px-4 py-2">Denominación</th>
                                <th class="px-4 py-2">Modelo</th>
                                <th class="px-4 py-2">Cliente</th>
                                <th class="px-4 py-2 text-right">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="pieza in piezasFiltradas" :key="pieza.id" class="bg-slate-50 text-slate-800">
                                <td class="rounded-l-xl px-4 py-3 font-medium">{{ pieza.codigo }}</td>
                                <td class="px-4 py-3">{{ pieza.denominacion }}</td>
                                <td class="px-4 py-3">{{ pieza.modelo?.nombre }}</td>
                                <td class="px-4 py-3">{{ pieza.modelo?.cliente?.nombre }}</td>
                                <td class="rounded-r-xl px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <button @click="editPieza(pieza)"
                                            class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm font-medium text-slate-700 transition hover:bg-white">
                                            Editar
                                        </button>

                                        <button @click="removePieza(pieza)"
                                            class="rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white transition hover:bg-red-700">
                                            Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="piezasFiltradas.length === 0">
                                <td colspan="5" class="px-4 py-6 text-center text-sm text-slate-500">
                                    No hay piezas registradas.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</template>