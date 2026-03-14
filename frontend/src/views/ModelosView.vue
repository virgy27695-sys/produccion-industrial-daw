<script setup>
import { computed, onMounted, ref } from "vue"
import { getClientes } from "../api/clientes"
import {
    getModelos,
    createModelo,
    updateModelo,
    deleteModelo,
} from "../api/modelos"
import { useToastStore } from "../stores/toast"

const toast = useToastStore()

const modelos = ref([])
const clientes = ref([])
const loading = ref(false)
const saving = ref(false)
const error = ref("")
const formError = ref("")

const form = ref({
    id: null,
    nombre: "",
    cliente_id: "",
})

const isEditing = computed(() => form.value.id !== null)

async function loadData() {
    loading.value = true
    error.value = ""

    try {
        const [modelosData, clientesData] = await Promise.all([
            getModelos(),
            getClientes(),
        ])

        modelos.value = modelosData
        clientes.value = clientesData
    } catch (e) {
        error.value = "No se pudieron cargar los modelos."
        console.error(e)
    } finally {
        loading.value = false
    }
}

function resetForm() {
    form.value = {
        id: null,
        nombre: "",
        cliente_id: "",
    }
    formError.value = ""
}

function editModelo(modelo) {
    form.value = {
        id: modelo.id,
        nombre: modelo.nombre,
        cliente_id: modelo.cliente_id,
    }
    formError.value = ""
}

async function submitForm() {
    formError.value = ""

    if (!form.value.nombre.trim()) {
        formError.value = "El nombre del modelo es obligatorio."
        return
    }

    if (!form.value.cliente_id) {
        formError.value = "Debes seleccionar un cliente."
        return
    }

    saving.value = true

    try {
        const payload = {
            nombre: form.value.nombre.trim(),
            cliente_id: form.value.cliente_id,
        }

        if (isEditing.value) {
            await updateModelo(form.value.id, payload)
            toast.show("Modelo actualizado")
        } else {
            await createModelo(payload)
            toast.show("Modelo creado correctamente")
        }

        resetForm()
        await loadData()
    } catch (e) {
        formError.value = "No se pudo guardar el modelo."
        toast.show("Error al guardar modelo", "error")
        console.error(e)
    } finally {
        saving.value = false
    }
}

async function removeModelo(modelo) {
    const confirmacion = window.confirm(
        `¿Seguro que quieres eliminar el modelo "${modelo.nombre}"?`
    )

    if (!confirmacion) return

    try {
        await deleteModelo(modelo.id)
        toast.show("Modelo eliminado")
        await loadData()
    } catch (e) {
        error.value = "No se pudo eliminar el modelo."
        toast.show("Error al eliminar modelo", "error")
        console.error(e)
    }
}

onMounted(loadData)
</script>

<template>
    <section class="space-y-6">
        <div>
            <h2 class="mb-4 text-2xl font-semibold text-slate-800">Modelos</h2>
            <p class="text-slate-600">
                Gestión de modelos de vehículo asociados a cada cliente.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 xl:col-span-1">
                <h3 class="mb-4 text-lg font-semibold text-slate-800">
                    {{ isEditing ? "Editar modelo" : "Nuevo modelo" }}
                </h3>

                <form class="space-y-4" @submit.prevent="submitForm">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">
                            Cliente
                        </label>
                        <select v-model="form.cliente_id"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2 text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                            <option value="">Selecciona un cliente</option>
                            <option v-for="cliente in clientes" :key="cliente.id" :value="cliente.id">
                                {{ cliente.nombre }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">
                            Nombre del modelo
                        </label>
                        <input v-model="form.nombre" type="text"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2 text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                            placeholder="Ej. A1" />
                    </div>

                    <p v-if="formError" class="text-sm text-red-600">
                        {{ formError }}
                    </p>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button type="submit" :disabled="saving"
                            class="rounded-xl bg-blue-600 px-4 py-2 font-medium text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60">
                            {{ saving ? "Guardando..." : isEditing ? "Actualizar" : "Crear modelo" }}
                        </button>

                        <button v-if="isEditing" type="button" @click="resetForm"
                            class="rounded-xl border border-slate-300 px-4 py-2 font-medium text-slate-700 transition hover:bg-slate-100">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 xl:col-span-2">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <h3 class="text-lg font-semibold text-slate-800">
                        Listado de modelos
                    </h3>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-sm text-slate-600">
                        {{ modelos.length }} registros
                    </span>
                </div>

                <p v-if="error" class="mb-4 text-sm text-red-600">
                    {{ error }}
                </p>

                <p v-if="loading" class="text-sm text-slate-500">
                    Cargando modelos...
                </p>

                <div v-else class="overflow-x-auto">
                    <table class="min-w-full border-separate border-spacing-y-2">
                        <thead>
                            <tr class="text-left text-sm text-slate-500">
                                <th class="px-4 py-2">ID</th>
                                <th class="px-4 py-2">Modelo</th>
                                <th class="px-4 py-2">Cliente</th>
                                <th class="px-4 py-2 text-right">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="modelo in modelos" :key="modelo.id" class="bg-slate-50 text-slate-800">
                                <td class="rounded-l-xl px-4 py-3">{{ modelo.id }}</td>
                                <td class="px-4 py-3 font-medium">{{ modelo.nombre }}</td>
                                <td class="px-4 py-3">{{ modelo.cliente?.nombre }}</td>
                                <td class="rounded-r-xl px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <button @click="editModelo(modelo)"
                                            class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm font-medium text-slate-700 transition hover:bg-white">
                                            Editar
                                        </button>

                                        <button @click="removeModelo(modelo)"
                                            class="rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white transition hover:bg-red-700">
                                            Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="modelos.length === 0">
                                <td colspan="4" class="px-4 py-6 text-center text-sm text-slate-500">
                                    No hay modelos registrados.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</template>