<script setup>
// IMPORTS
import { computed, onMounted, ref } from "vue"
import { useHead } from "@vueuse/head"

// API CLIENTES
import {
    getClientes,
    createCliente,
    updateCliente,
    deleteCliente,
} from "../api/clientes"

// COMPONENTES UI
import ActionButtons from "../components/ui/ActionButtons.vue"
import DataTable from "../components/ui/table/DataTable.vue"
import BaseInput from "../components/ui/BaseInput.vue"
import BaseButton from "../components/ui/BaseButton.vue"
import ConfirmDialog from "../components/ui/ConfirmDialog.vue"

// STORES Y UTILS
import { useToastStore } from "../stores/toast"
import { isAdmin, isPlanificador } from "../utils/auth"

// COMPOSABLE CRUD
import { useCrud } from "../composables/useCrud"


// CONFIGURACIÓN SEO
useHead({
    title: "Clientes · ISAVEX",
})


// ESTADO GENERAL
const toast = useToastStore()
const admin = isAdmin()
const planificador = isPlanificador()
const canManage = admin || planificador

const clientes = ref([])
const busqueda = ref("")


// MODAL DE CONFIRMACIÓN
const showDeleteDialog = ref(false)
const clienteToDelete = ref(null)


// CRUD GLOBAL
const {
    loading,
    saving,
    error,
    formError,
    executeLoad,
    executeSave,
    clearErrors,
} = useCrud()


// FORMULARIO
const form = ref({
    id: null,
    nombre: "",
})


// DETECTAR EDICIÓN
const isEditing = computed(() => form.value.id !== null)


// FILTRADO DE CLIENTES
const clientesFiltrados = computed(() => {
    const texto = busqueda.value.trim().toLowerCase()

    if (!texto) return clientes.value

    return clientes.value.filter((cliente) =>
        (cliente.nombre || "").toLowerCase().includes(texto) ||
        String(cliente.id || "").includes(texto)
    )
})


// CARGAR CLIENTES
async function loadClientes() {
    await executeLoad(async () => {
        const data = await getClientes()

        clientes.value = Array.isArray(data)
            ? data
            : []
    })
}


// REINICIAR FORMULARIO
function resetForm() {
    form.value = {
        id: null,
        nombre: "",
    }

    clearErrors()
}


// EDITAR CLIENTE
function editCliente(cliente) {
    form.value = {
        id: cliente.id,
        nombre: cliente.nombre,
    }

    clearErrors()
}


// GUARDAR CLIENTE
async function submitForm() {
    clearErrors()

    if (!form.value.nombre.trim()) {
        formError.value = "El nombre del cliente es obligatorio."
        return
    }

    try {
        await executeSave(async () => {
            const payload = {
                nombre: form.value.nombre.trim(),
            }

            if (isEditing.value) {
                await updateCliente(form.value.id, payload)
                toast.show("Cliente actualizado")
            } else {
                await createCliente(payload)
                toast.show("Cliente creado correctamente")
            }

            resetForm()
            await loadClientes()
        })
    } catch (e) {
        formError.value = "No se pudo guardar el cliente."
        toast.show("Error al guardar cliente", "error")
    }
}


// ABRIR MODAL DE ELIMINACIÓN
function askDeleteCliente(cliente) {
    clienteToDelete.value = cliente
    showDeleteDialog.value = true
}


// CERRAR MODAL DE ELIMINACIÓN
function cancelDeleteCliente() {
    clienteToDelete.value = null
    showDeleteDialog.value = false
}


// CONFIRMAR ELIMINACIÓN
async function confirmDeleteCliente() {
    if (!clienteToDelete.value) return

    try {
        await deleteCliente(clienteToDelete.value.id)

        toast.show("Cliente eliminado")

        cancelDeleteCliente()

        await loadClientes()
    } catch (e) {
        error.value = "No se pudo eliminar el cliente."
        toast.show("Error al eliminar cliente", "error")
    }
}


// INICIALIZACIÓN
onMounted(loadClientes)
</script>

<template>
    <section class="space-y-4 md:space-y-5">
        <!-- HERO -->
        <div class="isavex-card overflow-hidden p-4 md:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <div
                        class="mb-3 inline-flex items-center rounded-full border border-cyan-200 bg-white/70 px-3 py-1.5 text-[10px] font-semibold uppercase tracking-[0.22em] text-cyan-700 md:text-xs">
                        Gestión comercial
                    </div>

                    <h1 class="text-3xl font-black tracking-tight text-slate-900 md:text-4xl">
                        Clientes
                    </h1>

                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500 md:text-base">
                        Administración de clientes industriales asociados al sistema ISAVEX.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-3 md:gap-4">
                    <div class="rounded-2xl border border-white/60 bg-white/80 p-4 shadow-sm md:rounded-3xl md:p-5">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-400 md:text-xs">
                            Registros
                        </p>

                        <p class="mt-1 text-3xl font-black text-slate-900 md:text-4xl">
                            {{ clientes.length }}
                        </p>
                    </div>

                    <div class="rounded-2xl border border-cyan-100 bg-cyan-50/80 p-4 shadow-sm md:rounded-3xl md:p-5">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-cyan-600 md:text-xs">
                            Sistema
                        </p>

                        <p class="mt-1 text-base font-bold text-cyan-800 md:text-lg">
                            ISAVEX ERP
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- GRID PRINCIPAL -->
        <div class="grid gap-4 md:gap-5" :class="canManage ? 'xl:grid-cols-[340px_1fr]' : 'xl:grid-cols-1'">
            <!-- FORMULARIO -->
            <div v-if="canManage" class="isavex-card p-4 md:p-5">
                <div class="mb-4">
                    <h3 class="text-xl font-bold text-slate-900 md:text-2xl">
                        {{ isEditing ? "Editar cliente" : "Nuevo cliente" }}
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Alta y mantenimiento de clientes.
                    </p>
                </div>

                <form class="space-y-4" @submit.prevent="submitForm">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Nombre del cliente
                        </label>

                        <BaseInput v-model="form.nombre" placeholder="Ej. Valeo, Audi, Bosch..." />
                    </div>

                    <p v-if="formError" class="text-sm font-medium text-red-600">
                        {{ formError }}
                    </p>

                    <div class="flex flex-col gap-3">
                        <BaseButton type="submit" :disabled="saving" class="py-3">
                            {{
                                saving
                                    ? "Guardando..."
                                    : isEditing
                                        ? "Actualizar cliente"
                                        : "Crear cliente"
                            }}
                        </BaseButton>

                        <BaseButton v-if="isEditing" tone="secondary" @click="resetForm" class="py-3">
                            Cancelar edición
                        </BaseButton>
                    </div>
                </form>
            </div>

            <!-- LISTADO -->
            <div class="isavex-card p-4 md:p-5">
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 md:text-2xl">
                            Listado de clientes
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Clientes registrados en la plataforma.
                        </p>
                    </div>

                    <div
                        class="w-fit rounded-2xl border border-cyan-200 bg-cyan-50 px-4 py-2 text-sm font-semibold text-cyan-700">
                        {{ clientesFiltrados.length }} registros
                    </div>
                </div>

                <div
                    class="mb-4 flex w-full items-center gap-3 rounded-2xl border border-slate-200/70 bg-white/80 px-4 py-3 shadow-sm lg:max-w-md">
                    <span class="text-slate-400">⌕</span>

                    <BaseInput v-model="busqueda" placeholder="Buscar cliente..." />
                </div>

                <p v-if="error" class="mb-4 text-sm font-medium text-red-600">
                    {{ error }}
                </p>

                <DataTable :loading="loading" :empty="clientesFiltrados.length === 0"
                    loading-text="Cargando clientes..." empty-title="Sin clientes registrados"
                    empty-description="Todavía no hay clientes disponibles en el sistema.">

                    <!-- MOBILE -->
                    <div class="grid gap-3 md:hidden">
                        <div v-for="cliente in clientesFiltrados" :key="cliente.id"
                            class="rounded-2xl border border-slate-200 bg-white/80 p-4 shadow-sm">

                            <div class="flex items-start justify-between gap-3">
                                <div class="flex min-w-0 items-center gap-3">
                                    <div
                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-cyan-100 text-sm font-black text-cyan-700">
                                        {{ cliente.nombre.charAt(0) }}
                                    </div>

                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-slate-800">
                                            {{ cliente.nombre }}
                                        </p>

                                        <p class="text-xs text-slate-400">
                                            #{{ cliente.id }} · Cliente industrial
                                        </p>
                                    </div>
                                </div>

                                <ActionButtons v-if="canManage" @edit="editCliente(cliente)"
                                    @delete="askDeleteCliente(cliente)" />
                            </div>
                        </div>
                    </div>

                    <!-- DESKTOP -->
                    <div class="hidden overflow-x-auto md:block">
                        <table class="min-w-full">
                            <thead>
                                <tr
                                    class="border-b border-slate-200 text-left text-xs uppercase tracking-[0.2em] text-slate-400">
                                    <th class="px-4 py-3 font-semibold">
                                        ID
                                    </th>

                                    <th class="px-4 py-3 font-semibold">
                                        Cliente
                                    </th>

                                    <th v-if="canManage" class="px-4 py-3 text-right font-semibold">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="cliente in clientesFiltrados" :key="cliente.id"
                                    class="border-b border-slate-100 transition hover:bg-white/60">

                                    <td class="px-4 py-4">
                                        <span
                                            class="rounded-xl bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">
                                            #{{ cliente.id }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="flex h-10 w-10 items-center justify-center rounded-2xl bg-cyan-100 text-sm font-black text-cyan-700">
                                                {{ cliente.nombre.charAt(0) }}
                                            </div>

                                            <div>
                                                <p class="font-semibold text-slate-800">
                                                    {{ cliente.nombre }}
                                                </p>

                                                <p class="text-sm text-slate-400">
                                                    Cliente industrial
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <td v-if="canManage" class="px-4 py-4">
                                        <div class="flex justify-end">
                                            <ActionButtons @edit="editCliente(cliente)"
                                                @delete="askDeleteCliente(cliente)" />
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </DataTable>
            </div>
        </div>

        <!-- MODAL CONFIRMACIÓN -->
        <ConfirmDialog :show="showDeleteDialog" title="Eliminar cliente"
            :message="`¿Seguro que quieres eliminar el cliente '${clienteToDelete?.nombre || ''}'? Esta acción no se puede deshacer.`"
            confirm-text="Eliminar" cancel-text="Cancelar" tone="danger" @confirm="confirmDeleteCliente"
            @cancel="cancelDeleteCliente" />
    </section>
</template>
