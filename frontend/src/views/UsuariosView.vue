<script setup>
// IMPORTS
import { computed, onMounted, ref } from "vue"
import { useHead } from "@vueuse/head"

// API
import {
    getUsers,
    createUser,
    updateUser,
    deleteUser,
} from "../api/users"

// COMPONENTES UI
import ActionButtons from "../components/ui/ActionButtons.vue"
import DataTable from "../components/ui/table/DataTable.vue"
import BaseInput from "../components/ui/BaseInput.vue"
import BaseSelect from "../components/ui/BaseSelect.vue"
import BaseButton from "../components/ui/BaseButton.vue"
import ConfirmDialog from "../components/ui/ConfirmDialog.vue"

// STORES
import { useToastStore } from "../stores/toast"

// COMPOSABLE CRUD
import { useCrud } from "../composables/useCrud"


// SEO
useHead({
    title: "Usuarios · ISAVEX",
})


// STORE
const toast = useToastStore()


// ESTADO
const users = ref([])
const busqueda = ref("")


// MODAL DELETE
const showDeleteDialog = ref(false)
const userToDelete = ref(null)


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


// FORM
const form = ref({
    id: null,
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    role: "encargado",
})


// EDITANDO
const isEditing = computed(() => form.value.id !== null)


// FILTRO
const usersFiltrados = computed(() => {
    const texto = busqueda.value.trim().toLowerCase()

    if (!texto) return users.value

    return users.value.filter((user) => {
        return (
            (user.name || "").toLowerCase().includes(texto) ||
            (user.email || "").toLowerCase().includes(texto) ||
            (user.role || "").toLowerCase().includes(texto)
        )
    })
})


// CARGAR
async function loadUsers() {
    await executeLoad(async () => {
        const data = await getUsers()

        users.value = Array.isArray(data)
            ? data
            : []
    })
}


// RESET
function resetForm() {
    form.value = {
        id: null,
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
        role: "encargado",
    }
    clearErrors()
}


// EDITAR
function editUser(user) {
    form.value = {
        id: user.id,
        name: user.name,
        email: user.email,
        password: "",
        password_confirmation: "",
        role: user.role,
    }

    clearErrors()
}


// VALIDAR PASSWORD
function validatePassword(password) {
    const regex =
        /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/

    return regex.test(password)
}


// GUARDAR
async function submitForm() {
    clearErrors()

    if (!form.value.name.trim()) {
        formError.value = "El nombre es obligatorio."
        return
    }

    if (!form.value.email.trim()) {
        formError.value = "El email es obligatorio."
        return
    }

    // PASSWORD NUEVA
    if (!isEditing.value || form.value.password) {
        if (!validatePassword(form.value.password)) {
            formError.value =
                "La contraseña debe tener mínimo 8 caracteres, mayúsculas, minúsculas, números y símbolos."

            return
        }

        if (
            form.value.password !==
            form.value.password_confirmation
        ) {
            formError.value =
                "Las contraseñas no coinciden."

            return
        }
    }

    try {
        await executeSave(async () => {
            const payload = {
                name: form.value.name,
                email: form.value.email,
                role: form.value.role,
            }

            // SOLO ENVIAMOS PASSWORD SI EXISTE
            if (form.value.password) {
                payload.password = form.value.password
                payload.password_confirmation =
                    form.value.password_confirmation
            }

            if (isEditing.value) {
                await updateUser(form.value.id, payload)

                toast.show("Usuario actualizado")
            } else {
                await createUser(payload)

                toast.show("Usuario creado")
            }

            resetForm()

            await loadUsers()
        })
    } catch (e) {
        formError.value =
            "No se pudo guardar el usuario."

        toast.show("Error al guardar usuario", "error")

        console.error(e)
    }
}


// ABRIR DELETE
function askDeleteUser(user) {
    userToDelete.value = user
    showDeleteDialog.value = true
}


// CANCEL DELETE
function cancelDeleteUser() {
    userToDelete.value = null
    showDeleteDialog.value = false
}


// CONFIRM DELETE
async function confirmDeleteUser() {
    if (!userToDelete.value) return

    try {
        await deleteUser(userToDelete.value.id)

        toast.show("Usuario eliminado")

        cancelDeleteUser()

        await loadUsers()
    } catch (e) {
        error.value =
            "No se pudo eliminar el usuario."

        toast.show("Error al eliminar usuario", "error")

        console.error(e)
    }
}


// INIT
onMounted(loadUsers)
</script>

<template>
    <section class="space-y-4 md:space-y-5">
        <!-- HERO -->
        <div class="isavex-card p-4 md:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <div
                        class="mb-3 inline-flex items-center rounded-full border border-cyan-200 bg-white/70 px-3 py-1.5 text-[10px] font-semibold uppercase tracking-[0.22em] text-cyan-700 md:text-xs">
                        Administración
                    </div>

                    <h1 class="text-3xl font-black tracking-tight text-slate-900 md:text-4xl">
                        Usuarios
                    </h1>

                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500 md:text-base">
                        Gestión de usuarios, roles y seguridad del sistema.
                    </p>
                </div>

                <div class="rounded-3xl border border-cyan-100 bg-cyan-50/80 p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-600">
                        Usuarios activos
                    </p>

                    <p class="mt-2 text-4xl font-black text-cyan-800">
                        {{ users.length }}
                    </p>
                </div>
            </div>
        </div>

        <!-- GRID -->
        <div class="grid gap-4 md:gap-5 xl:grid-cols-[380px_1fr]">
            <!-- FORM -->
            <div class="isavex-card p-4 md:p-5">
                <div class="mb-4">
                    <h3 class="text-xl font-bold text-slate-900 md:text-2xl">
                        {{ isEditing ? "Editar usuario" : "Nuevo usuario" }}
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Gestión de accesos y roles.
                    </p>
                </div>

                <form class="space-y-4" @submit.prevent="submitForm">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Nombre
                        </label>

                        <BaseInput v-model="form.name" placeholder="Nombre completo" />
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Email
                        </label>

                        <BaseInput v-model="form.email" type="email" placeholder="correo@empresa.com" />
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Rol
                        </label>

                        <BaseSelect v-model="form.role">
                            <option value="usuario">
                                Usuario
                            </option>

                            <option value="admin">
                                Administrador
                            </option>
                        </BaseSelect>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Contraseña
                        </label>

                        <BaseInput v-model="form.password" type="password" placeholder="********" />

                        <p class="mt-2 text-xs leading-5 text-slate-400">
                            Mínimo 8 caracteres, mayúsculas, minúsculas,
                            números y símbolos.
                        </p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Confirmar contraseña
                        </label>

                        <BaseInput v-model="form.password_confirmation" type="password" placeholder="********" />
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
                                        ? "Actualizar usuario"
                                        : "Crear usuario"
                            }}
                        </BaseButton>

                        <BaseButton v-if="isEditing" tone="secondary" class="py-3" @click="resetForm">
                            Cancelar edición
                        </BaseButton>
                    </div>
                </form>
            </div>

            <!-- TABLA -->
            <div class="isavex-card p-4 md:p-5">
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 md:text-2xl">
                            Usuarios del sistema
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Gestión de accesos y permisos.
                        </p>
                    </div>

                    <div
                        class="rounded-2xl border border-cyan-200 bg-cyan-50 px-4 py-2 text-sm font-semibold text-cyan-700">
                        {{ usersFiltrados.length }} registros
                    </div>
                </div>

                <!-- BUSCADOR -->
                <div class="mb-4">
                    <BaseInput v-model="busqueda" placeholder="Buscar usuario..." />
                </div>

                <!-- ERROR -->
                <p v-if="error" class="mb-4 text-sm font-medium text-red-600">
                    {{ error }}
                </p>

                <!-- DATA -->
                <DataTable :loading="loading" :empty="usersFiltrados.length === 0" loading-text="Cargando usuarios..."
                    empty-title="Sin usuarios" empty-description="No existen usuarios registrados.">
                    <!-- MOBILE -->
                    <div class="grid gap-3 md:hidden">
                        <div v-for="user in usersFiltrados" :key="user.id"
                            class="rounded-2xl border border-slate-200 bg-white/80 p-4 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="font-semibold text-slate-800">
                                        {{ user.name }}
                                    </p>

                                    <p class="text-sm text-slate-500">
                                        {{ user.email }}
                                    </p>

                                    <span
                                        class="mt-2 inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                        {{ user.role }}
                                    </span>
                                </div>

                                <ActionButtons @edit="editUser(user)" @delete="askDeleteUser(user)" />
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
                                        Usuario
                                    </th>

                                    <th class="px-4 py-3 font-semibold">
                                        Email
                                    </th>

                                    <th class="px-4 py-3 font-semibold">
                                        Rol
                                    </th>

                                    <th class="px-4 py-3 text-right font-semibold">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="user in usersFiltrados" :key="user.id"
                                    class="border-b border-slate-100 transition hover:bg-white/60">
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="flex h-10 w-10 items-center justify-center rounded-2xl bg-cyan-100 text-sm font-black text-cyan-700">
                                                {{ user.name.charAt(0) }}
                                            </div>

                                            <div>
                                                <p class="font-semibold text-slate-800">
                                                    {{ user.name }}
                                                </p>

                                                <p class="text-sm text-slate-400">
                                                    Usuario del sistema
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-4 py-4 text-slate-600">
                                        {{ user.email }}
                                    </td>

                                    <td class="px-4 py-4">
                                        <span
                                            class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                            {{ user.role }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-4">
                                        <div class="flex justify-end">
                                            <ActionButtons @edit="editUser(user)" @delete="askDeleteUser(user)" />
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </DataTable>
            </div>
        </div>

        <!-- MODAL -->
        <ConfirmDialog :show="showDeleteDialog" title="Eliminar usuario"
            :message="`¿Seguro que quieres eliminar el usuario '${userToDelete?.name || ''}'? Esta acción no se puede deshacer.`"
            confirm-text="Eliminar" cancel-text="Cancelar" tone="danger" @confirm="confirmDeleteUser"
            @cancel="cancelDeleteUser" />
    </section>
</template>