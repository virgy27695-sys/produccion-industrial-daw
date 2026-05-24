<script setup>
// IMPORTS
import { computed, onMounted, ref } from "vue"
import { useHead } from "@vueuse/head"

// APIS
import { getClientes } from "../api/clientes"

import {
    getModelos,
    createModelo,
    updateModelo,
    deleteModelo,
} from "../api/modelos"

// COMPONENTES UI
import ActionButtons from "../components/ui/ActionButtons.vue"
import DataTable from "../components/ui/table/DataTable.vue"
import BaseInput from "../components/ui/BaseInput.vue"
import BaseSelect from "../components/ui/BaseSelect.vue"
import BaseButton from "../components/ui/BaseButton.vue"
import ConfirmDialog from "../components/ui/ConfirmDialog.vue"

// STORES Y UTILS
import { useToastStore } from "../stores/toast"
import { isAdmin, isPlanificador } from "../utils/auth"

// COMPOSABLE CRUD
import { useCrud } from "../composables/useCrud"


// CONFIGURACIÓN SEO
useHead({
    title: "Modelos · ISAVEX",
})


// ESTADO GENERAL
const toast = useToastStore()
const admin = isAdmin()
const planificador = isPlanificador()
const canManage = admin || planificador

const modelos = ref([])
const clientes = ref([])
const busqueda = ref("")


// MODAL DE CONFIRMACIÓN
const showDeleteDialog = ref(false)
const modeloToDelete = ref(null)


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
    cliente_id: "",
})


// DETECTAR EDICIÓN
const isEditing = computed(() => form.value.id !== null)


// FILTRADO
const modelosFiltrados = computed(() => {
    const texto = busqueda.value.trim().toLowerCase()

    if (!texto) return modelos.value

    return modelos.value.filter((modelo) => {
        return (
            (modelo.nombre || "").toLowerCase().includes(texto) ||
            (modelo.cliente?.nombre || "").toLowerCase().includes(texto) ||
            String(modelo.id).includes(texto)
        )
    })
})


// CARGA DE DATOS
async function loadData() {
    await executeLoad(async () => {
        const [modelosData, clientesData] = await Promise.all([
            getModelos(),
            getClientes(),
        ])

        modelos.value = Array.isArray(modelosData)
            ? modelosData
            : []

        clientes.value = Array.isArray(clientesData)
            ? clientesData
            : []
    })
}


// RESET FORMULARIO
function resetForm() {
    form.value = {
        id: null,
        nombre: "",
        cliente_id: "",
    }

    clearErrors()
}


// EDITAR MODELO
function editModelo(modelo) {
    form.value = {
        id: modelo.id,
        nombre: modelo.nombre,
        cliente_id: modelo.cliente_id,
    }

    clearErrors()
}


// GUARDAR MODELO
async function submitForm() {
    clearErrors()

    if (!form.value.nombre.trim()) {
        formError.value = "El nombre del modelo es obligatorio."
        return
    }

    if (!form.value.cliente_id) {
        formError.value = "Debes seleccionar un cliente."
        return
    }

    try {
        await executeSave(async () => {
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
        })
    } catch (e) {
        formError.value = "No se pudo guardar el modelo."
        toast.show("Error al guardar modelo", "error")
    }
}


// ABRIR MODAL DE ELIMINACIÓN
function askDeleteModelo(modelo) {
    modeloToDelete.value = modelo
    showDeleteDialog.value = true
}


// CERRAR MODAL DE ELIMINACIÓN
function cancelDeleteModelo() {
    modeloToDelete.value = null
    showDeleteDialog.value = false
}


// CONFIRMAR ELIMINACIÓN
async function confirmDeleteModelo() {
    if (!modeloToDelete.value) return

    try {
        await deleteModelo(modeloToDelete.value.id)

        toast.show("Modelo eliminado")

        cancelDeleteModelo()

        await loadData()
    } catch (e) {
        error.value = "No se pudo eliminar el modelo."
        toast.show("Error al eliminar modelo", "error")
    }
}


// INIT
onMounted(loadData)
</script>

<template>
    <section class="space-y-4 md:space-y-5">
        <!-- HERO -->
        <div class="isavex-card overflow-hidden p-4 md:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <div
                        class="mb-3 inline-flex items-center rounded-full border border-cyan-200 bg-white/70 px-3 py-1.5 text-[10px] font-semibold uppercase tracking-[0.22em] text-cyan-700 md:text-xs">
                        Ingeniería producto
                    </div>

                    <h1 class="text-3xl font-black tracking-tight text-slate-900 md:text-4xl">
                        Modelos
                    </h1>

                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500 md:text-base">
                        Gestión de plataformas y modelos asociados a fabricantes.
                    </p>
                </div>

                <!-- KPIs -->
                <div class="grid grid-cols-2 gap-3 md:gap-4">
                    <div class="rounded-2xl border border-white/60 bg-white/80 p-4 shadow-sm md:rounded-3xl md:p-5">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-400 md:text-xs">
                            Registros
                        </p>

                        <p class="mt-1 text-3xl font-black text-slate-900 md:text-4xl">
                            {{ modelos.length }}
                        </p>
                    </div>

                    <div class="rounded-2xl border border-cyan-100 bg-cyan-50/80 p-4 shadow-sm md:rounded-3xl md:p-5">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-cyan-600 md:text-xs">
                            Clientes
                        </p>

                        <p class="mt-1 text-3xl font-black text-cyan-800 md:text-4xl">
                            {{ clientes.length }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- GRID -->
        <div class="grid gap-4 md:gap-5" :class="canManage ? 'xl:grid-cols-[360px_1fr]' : 'xl:grid-cols-1'">
            <!-- FORM -->
            <div v-if="canManage" class="isavex-card p-4 md:p-5">
                <div class="mb-4">
                    <h3 class="text-xl font-bold text-slate-900 md:text-2xl">
                        {{ isEditing ? "Editar modelo" : "Nuevo modelo" }}
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Alta y mantenimiento de modelos industriales.
                    </p>
                </div>

                <form class="space-y-4" @submit.prevent="submitForm">
                    <!-- CLIENTE -->
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Cliente
                        </label>

                        <BaseSelect v-model="form.cliente_id">
                            <option value="">
                                Selecciona un cliente
                            </option>

                            <option v-for="cliente in clientes" :key="cliente.id" :value="cliente.id">
                                {{ cliente.nombre }}
                            </option>
                        </BaseSelect>
                    </div>

                    <!-- MODELO -->
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Nombre del modelo
                        </label>

                        <BaseInput v-model="form.nombre" placeholder="Ej. A1, Golf VIII..." />
                    </div>

                    <!-- ERROR -->
                    <p v-if="formError" class="text-sm font-medium text-red-600">
                        {{ formError }}
                    </p>

                    <!-- BOTONES -->
                    <div class="flex flex-col gap-3">
                        <BaseButton type="submit" :disabled="saving" class="py-3">
                            {{
                                saving
                                    ? "Guardando..."
                                    : isEditing
                                        ? "Actualizar modelo"
                                        : "Crear modelo"
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
                <!-- HEADER -->
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 md:text-2xl">
                            Listado de modelos
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Plataformas registradas en ISAVEX.
                        </p>
                    </div>

                    <div
                        class="w-fit rounded-2xl border border-cyan-200 bg-cyan-50 px-4 py-2 text-sm font-semibold text-cyan-700">
                        {{ modelosFiltrados.length }} registros
                    </div>
                </div>

                <!-- BUSCADOR -->
                <div
                    class="mb-4 flex w-full items-center gap-3 rounded-2xl border border-slate-200/70 bg-white/80 px-4 py-3 shadow-sm lg:max-w-md">
                    <span class="text-slate-400">
                        ⌕
                    </span>

                    <BaseInput v-model="busqueda" placeholder="Buscar modelo o cliente..." />
                </div>

                <!-- ERROR -->
                <p v-if="error" class="mb-4 text-sm font-medium text-red-600">
                    {{ error }}
                </p>

                <!-- DATA TABLE -->
                <DataTable :loading="loading" :empty="modelosFiltrados.length === 0" loading-text="Cargando modelos..."
                    empty-title="Sin modelos registrados" empty-description="Todavía no existen modelos en el sistema.">

                    <!-- MOBILE -->
                    <div class="grid gap-3 md:hidden">
                        <div v-for="modelo in modelosFiltrados" :key="modelo.id"
                            class="rounded-2xl border border-slate-200 bg-white/80 p-4 shadow-sm">

                            <div class="flex items-start justify-between gap-3">
                                <div class="flex min-w-0 items-center gap-3">
                                    <div
                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-cyan-100 text-sm font-black text-cyan-700">
                                        {{ modelo.nombre.charAt(0) }}
                                    </div>

                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-slate-800">
                                            {{ modelo.nombre }}
                                        </p>

                                        <p class="text-xs text-slate-400">
                                            {{ modelo.cliente?.nombre }}
                                        </p>
                                    </div>
                                </div>

                                <ActionButtons v-if="canManage" @edit="editModelo(modelo)"
                                    @delete="askDeleteModelo(modelo)" />
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
                                        Modelo
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
                                <tr v-for="modelo in modelosFiltrados" :key="modelo.id"
                                    class="border-b border-slate-100 transition hover:bg-white/60">

                                    <td class="px-4 py-4">
                                        <span
                                            class="rounded-xl bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">
                                            #{{ modelo.id }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="flex h-10 w-10 items-center justify-center rounded-2xl bg-cyan-100 text-sm font-black text-cyan-700">
                                                {{ modelo.nombre.charAt(0) }}
                                            </div>

                                            <div>
                                                <p class="font-semibold text-slate-800">
                                                    {{ modelo.nombre }}
                                                </p>

                                                <p class="text-sm text-slate-400">
                                                    Modelo industrial
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-4 py-4">
                                        <span
                                            class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                            {{ modelo.cliente?.nombre }}
                                        </span>
                                    </td>

                                    <td v-if="canManage" class="px-4 py-4">
                                        <div class="flex justify-end">
                                            <ActionButtons @edit="editModelo(modelo)"
                                                @delete="askDeleteModelo(modelo)" />
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
        <ConfirmDialog :show="showDeleteDialog" title="Eliminar modelo"
            :message="`¿Seguro que quieres eliminar el modelo '${modeloToDelete?.nombre || ''}'? Esta acción no se puede deshacer.`"
            confirm-text="Eliminar" cancel-text="Cancelar" tone="danger" @confirm="confirmDeleteModelo"
            @cancel="cancelDeleteModelo" />
    </section>
</template>
