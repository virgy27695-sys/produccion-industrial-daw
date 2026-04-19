<script setup>
import { computed, onMounted, ref } from "vue"
import {
    getClientes,
    createCliente,
    updateCliente,
    deleteCliente,
} from "../api/clientes"
import { useToastStore } from "../stores/toast"
import { isAdmin } from "../utils/auth"

const toast = useToastStore()
const admin = isAdmin()

const clientes = ref([])
const loading = ref(false)
const error = ref("")
const formError = ref("")
const saving = ref(false)

const form = ref({
    id: null,
    nombre: "",
})

const isEditing = computed(() => form.value.id !== null)

async function loadClientes() {
    loading.value = true
    error.value = ""

    try {
        clientes.value = await getClientes()
    } catch (e) {
        error.value = "No se pudieron cargar los clientes."
        console.error(e)
    } finally {
        loading.value = false
    }
}

function resetForm() {
    form.value = {
        id: null,
        nombre: "",
    }
    formError.value = ""
}

function editCliente(cliente) {
    form.value = {
        id: cliente.id,
        nombre: cliente.nombre,
    }
    formError.value = ""
}

async function submitForm() {
    formError.value = ""

    if (!form.value.nombre.trim()) {
        formError.value = "El nombre es obligatorio."
        return
    }

    saving.value = true

    try {
        if (isEditing.value) {
            await updateCliente(form.value.id, {
                nombre: form.value.nombre.trim(),
            })
            toast.show("Cliente actualizado")
        } else {
            await createCliente({
                nombre: form.value.nombre.trim(),
            })
            toast.show("Cliente creado correctamente")
        }

        resetForm()
        await loadClientes()
    } catch (e) {
        formError.value = "No se pudo guardar el cliente."
        toast.show("Error al guardar cliente", "error")
        console.error(e)
    } finally {
        saving.value = false
    }
}

async function removeCliente(cliente) {
    const confirmacion = window.confirm(
        `¿Seguro que quieres eliminar a "${cliente.nombre}"?`
    )

    if (!confirmacion) return

    try {
        await deleteCliente(cliente.id)
        toast.show("Cliente eliminado")
        await loadClientes()
    } catch (e) {
        error.value = "No se pudo eliminar el cliente."
        toast.show("Error al eliminar cliente", "error")
        console.error(e)
    }
}

onMounted(loadClientes)
</script>

<template>
    <section class="space-y-6">
        <div>
            <h2 class="mb-4 text-2xl font-semibold text-slate-800">Clientes</h2>
            <p class="text-slate-600">
                Gestión de marcas y fabricantes asociados al sistema.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-6" :class="admin ? 'xl:grid-cols-3' : 'xl:grid-cols-1'">
            <div v-if="admin" class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 xl:col-span-1">
                <h3 class="mb-4 text-lg font-semibold text-slate-800">
                    {{ isEditing ? "Editar cliente" : "Nuevo cliente" }}
                </h3>

                <form class="space-y-4" @submit.prevent="submitForm">
                    <div>
                        <label for="nombre" class="mb-1 block text-sm font-medium text-slate-700">
                            Nombre
                        </label>
                        <input id="nombre" v-model="form.nombre" type="text"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2 text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                            placeholder="Ej. Audi" />
                    </div>

                    <p v-if="formError" class="text-sm text-red-600">
                        {{ formError }}
                    </p>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button type="submit" :disabled="saving"
                            class="rounded-xl bg-blue-600 px-4 py-2 font-medium text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60">
                            {{ saving ? "Guardando..." : isEditing ? "Actualizar" : "Crear cliente" }}
                        </button>

                        <button v-if="isEditing" type="button" @click="resetForm"
                            class="rounded-xl border border-slate-300 px-4 py-2 font-medium text-slate-700 transition hover:bg-slate-100">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
                :class="admin ? 'xl:col-span-2' : 'xl:col-span-1'">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <h3 class="text-lg font-semibold text-slate-800">
                        Listado de clientes
                    </h3>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-sm text-slate-600">
                        {{ clientes.length }} registros
                    </span>
                </div>

                <p v-if="error" class="mb-4 text-sm text-red-600">
                    {{ error }}
                </p>

                <p v-if="loading" class="text-sm text-slate-500">
                    Cargando clientes...
                </p>

                <div v-else class="overflow-x-auto">
                    <table class="min-w-full border-separate border-spacing-y-2">
                        <thead>
                            <tr class="text-left text-sm text-slate-500">
                                <th class="px-4 py-2">ID</th>
                                <th class="px-4 py-2">Nombre</th>
                                <th v-if="admin" class="px-4 py-2 text-right">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="cliente in clientes" :key="cliente.id" class="bg-slate-50 text-slate-800">
                                <td class="px-4 py-3" :class="admin ? 'rounded-l-xl' : 'rounded-l-xl'">
                                    {{ cliente.id }}
                                </td>
                                <td class="px-4 py-3 font-medium" :class="!admin ? 'rounded-r-xl' : ''">
                                    {{ cliente.nombre }}
                                </td>
                                <td v-if="admin" class="rounded-r-xl px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <button @click="editCliente(cliente)"
                                            class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm font-medium text-slate-700 transition hover:bg-white">
                                            Editar
                                        </button>

                                        <button @click="removeCliente(cliente)"
                                            class="rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white transition hover:bg-red-700">
                                            Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="clientes.length === 0">
                                <td :colspan="admin ? 3 : 2" class="px-4 py-6 text-center text-sm text-slate-500">
                                    No hay clientes registrados.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</template>