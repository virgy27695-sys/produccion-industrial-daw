<script setup>
// IMPORTS
import { computed, onMounted, ref } from "vue"
import { useHead } from "@vueuse/head"

// ICONOS
// Se usan para acciones compactas y responsive.
import {
    Pencil,
    Trash2,
} from "lucide-vue-next"

import { getClientes } from "../api/clientes"
import { getModelos } from "../api/modelos"
import { getMoldes } from "../api/moldes"

import {
    getPiezas,
    createPieza,
    updatePieza,
    deletePieza,
} from "../api/piezas"

import { useToastStore } from "../stores/toast"
import { isAdmin, isPlanificador } from "../utils/auth"

// COMPOSABLE CRUD
import { useCrud } from "../composables/useCrud"


// CONFIGURACIÓN DE LA PÁGINA
useHead({
    title: "Piezas · ISAVEX",
})


// ESTADO GENERAL
const toast = useToastStore()
const admin = isAdmin()
const planificador = isPlanificador()
const canManage = admin || planificador


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


// LISTAS PRINCIPALES
const piezas = ref([])
const clientes = ref([])
const modelos = ref([])
const moldes = ref([])


// BUSCADOR
const busqueda = ref("")


// FORMULARIO DE PIEZAS
const form = ref({
    id: null,
    codigo: "",
    denominacion: "",
    cliente_id: "",
    modelo_id: "",
    molde_id: "",
    lado_pieza: "",
    mercado: "",
    categoria_funcional: "",
})


// MODO EDICIÓN
const isEditing = computed(() => form.value.id !== null)


// MODELOS FILTRADOS POR CLIENTE
const modelosFiltrados = computed(() => {
    if (!form.value.cliente_id) return []

    return modelos.value.filter(
        (modelo) => Number(modelo.cliente_id) === Number(form.value.cliente_id)
    )
})


// FILTRADO DE PIEZAS
const piezasFiltradas = computed(() => {
    const texto = busqueda.value.trim().toLowerCase()

    if (!texto) return piezas.value

    return piezas.value.filter((pieza) => {
        return (
            (pieza.codigo || "").toLowerCase().includes(texto) ||
            (pieza.denominacion || "").toLowerCase().includes(texto) ||
            (pieza.modelo?.nombre || "").toLowerCase().includes(texto) ||
            (pieza.modelo?.cliente?.nombre || "").toLowerCase().includes(texto) ||
            (pieza.molde?.codigo || "").toLowerCase().includes(texto) ||
            (pieza.lado_pieza || "").toLowerCase().includes(texto) ||
            (pieza.mercado || "").toLowerCase().includes(texto) ||
            (pieza.categoria_funcional || "").toLowerCase().includes(texto)
        )
    })
})


// MÉTRICAS
const totalConMolde = computed(() =>
    piezas.value.filter((pieza) => pieza.molde_id || pieza.molde).length
)

const totalSinMolde = computed(() =>
    piezas.value.filter((pieza) => !pieza.molde_id && !pieza.molde).length
)

const categorias = computed(() => {
    const set = new Set(
        piezas.value
            .map((pieza) => pieza.categoria_funcional)
            .filter(Boolean)
    )

    return set.size
})


// CARGA DE DATOS
// Usa Promise.allSettled para que si una API falla,
// el resto de datos puedan seguir cargando.
async function loadData() {
    await executeLoad(async () => {
        const results = await Promise.allSettled([
            getPiezas(),
            getClientes(),
            getModelos(),
            getMoldes(),
        ])

        const [
            piezasResult,
            clientesResult,
            modelosResult,
            moldesResult,
        ] = results

        piezas.value =
            piezasResult.status === "fulfilled" && Array.isArray(piezasResult.value)
                ? piezasResult.value
                : []

        clientes.value =
            clientesResult.status === "fulfilled" && Array.isArray(clientesResult.value)
                ? clientesResult.value
                : []

        modelos.value =
            modelosResult.status === "fulfilled" && Array.isArray(modelosResult.value)
                ? modelosResult.value
                : []

        moldes.value =
            moldesResult.status === "fulfilled" && Array.isArray(moldesResult.value)
                ? moldesResult.value
                : []

        const hasErrors = results.some(
            (result) => result.status === "rejected"
        )

        if (hasErrors) {
            error.value = "Algunos datos no pudieron cargarse correctamente."
        }
    })
}


// REINICIAR FORMULARIO
function resetForm() {
    form.value = {
        id: null,
        codigo: "",
        denominacion: "",
        cliente_id: "",
        modelo_id: "",
        molde_id: "",
        lado_pieza: "",
        mercado: "",
        categoria_funcional: "",
    }

    clearErrors()
}


// CARGAR PIEZA EN EDICIÓN
function editPieza(pieza) {
    form.value = {
        id: pieza.id,
        codigo: pieza.codigo,
        denominacion: pieza.denominacion,
        cliente_id: pieza.modelo?.cliente?.id ?? "",
        modelo_id: pieza.modelo_id,
        molde_id: pieza.molde_id ?? "",
        lado_pieza: pieza.lado_pieza ?? "",
        mercado: pieza.mercado ?? "",
        categoria_funcional: pieza.categoria_funcional ?? "",
    }

    clearErrors()
}


// CAMBIO DE CLIENTE
function onClienteChange() {
    form.value.modelo_id = ""
}


// DETECCIÓN AUTOMÁTICA DEL LADO
function detectarLadoPieza(denominacion) {
    const texto = (denominacion || "").toUpperCase()

    if (
        texto.includes(" IZQ") ||
        texto.includes(" IZQUIERDA") ||
        texto.includes(" LEFT")
    ) {
        return "izquierda"
    }

    if (
        texto.includes(" DER") ||
        texto.includes(" DERECHA") ||
        texto.includes(" RIGHT") ||
        texto.includes(" DRC")
    ) {
        return "derecha"
    }

    return "neutra"
}


// DETECCIÓN AUTOMÁTICA DE MERCADO
function detectarMercado(denominacion) {
    const texto = (denominacion || "").toUpperCase()

    if (texto.includes("LHD")) return "LHD"
    if (texto.includes("RHD")) return "RHD"
    if (texto.includes(" TI")) return "TI"
    if (texto.includes(" TD")) return "TD"

    return ""
}


// DETECCIÓN AUTOMÁTICA DE CATEGORÍA
function detectarCategoriaFuncional(denominacion) {
    const texto = (denominacion || "").toUpperCase()

    if (texto.includes("SOPORTE")) return "soporte"
    if (texto.includes("GUIA DE LUZ") || texto.includes("GUÍA DE LUZ")) return "guia_luz"
    if (texto.includes("REFLECTOR")) return "reflector"
    if (texto.includes("EMB") || texto.includes("EMBELLECEDOR")) return "embellecedor"

    return "otro"
}


// AUTOCOMPLETADO PRODUCTIVO
function autocompletarCamposProductivos() {
    const denominacion = form.value.denominacion

    if (!denominacion.trim()) return

    if (!form.value.lado_pieza) {
        form.value.lado_pieza = detectarLadoPieza(denominacion)
    }

    if (!form.value.mercado) {
        form.value.mercado = detectarMercado(denominacion)
    }

    if (!form.value.categoria_funcional) {
        form.value.categoria_funcional = detectarCategoriaFuncional(denominacion)
    }
}


// GUARDAR PIEZA
async function submitForm() {
    clearErrors()

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

    try {
        await executeSave(async () => {
            const payload = {
                codigo: form.value.codigo.trim(),
                denominacion: form.value.denominacion.trim(),
                modelo_id: form.value.modelo_id,
                molde_id: form.value.molde_id || null,
                lado_pieza: form.value.lado_pieza || null,
                mercado: form.value.mercado || null,
                categoria_funcional: form.value.categoria_funcional || null,
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
        })
    } catch (e) {
        formError.value = "No se pudo guardar la pieza."
        toast.show("Error al guardar pieza", "error")
    }
}


// ELIMINAR PIEZA
async function removePieza(pieza) {
    try {
        await deletePieza(pieza.id)

        toast.show("Pieza eliminada")

        await loadData()

    } catch {
        error.value = "No se pudo eliminar la pieza."

        toast.show(
            "Error al eliminar pieza",
            "error"
        )
    }
}


// FORMATEAR CATEGORÍA
function formatCategoria(value) {
    const categorias = {
        soporte: "Soporte",
        guia_luz: "Guía de luz",
        reflector: "Reflector",
        embellecedor: "Embellecedor",
        otro: "Otro",
    }

    return categorias[value] || "—"
}


// CLASES VISUALES DE BADGES
function badgeClass(value) {
    return {
        "bg-cyan-100 text-cyan-700 ring-cyan-200": value === "soporte",
        "bg-blue-100 text-blue-700 ring-blue-200": value === "guia_luz",
        "bg-amber-100 text-amber-700 ring-amber-200": value === "reflector",
        "bg-violet-100 text-violet-700 ring-violet-200": value === "embellecedor",
        "bg-slate-100 text-slate-600 ring-slate-200": !value || value === "otro",
    }
}


// INICIALIZACIÓN
onMounted(loadData)
</script>

<template>
    <section class="space-y-6">
        <!-- HERO PRINCIPAL -->
        <div
            class="relative overflow-hidden rounded-[2rem] border border-white/70 bg-white/75 p-6 shadow-xl shadow-slate-300/40 backdrop-blur-xl">
            <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-[#59C7D8]/20 blur-3xl"></div>

            <div class="relative z-10 flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">
                <div>
                    <div
                        class="mb-3 inline-flex rounded-full border border-[#59C7D8]/40 bg-white/70 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.25em] text-[#1597A8]">
                        Catálogo técnico
                    </div>

                    <h1 class="text-3xl font-bold text-[#081426]">
                        Piezas
                    </h1>

                    <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600">
                        Gestión de referencias fabricadas, asociadas a cliente, modelo, molde e información productiva.
                    </p>
                </div>

                <!-- KPIs DE LA VISTA -->
                <div class="grid gap-3 sm:grid-cols-3">
                    <div class="rounded-3xl border border-white/70 bg-white/80 p-5 shadow-sm backdrop-blur">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Total</p>
                        <p class="mt-2 text-3xl font-bold text-[#081426]">{{ piezas.length }}</p>
                    </div>

                    <div class="rounded-3xl border border-cyan-200/60 bg-cyan-50/80 p-5 shadow-sm">
                        <p class="text-xs uppercase tracking-[0.18em] text-cyan-600">Con molde</p>
                        <p class="mt-2 text-3xl font-bold text-cyan-700">{{ totalConMolde }}</p>
                    </div>

                    <div class="rounded-3xl border border-amber-200/60 bg-amber-50/80 p-5 shadow-sm">
                        <p class="text-xs uppercase tracking-[0.18em] text-amber-600">Categorías</p>
                        <p class="mt-2 text-3xl font-bold text-amber-700">{{ categorias }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- CONTENEDOR PRINCIPAL -->
        <div class="grid grid-cols-1 gap-6" :class="canManage ? 'xl:grid-cols-[420px_1fr]' : 'xl:grid-cols-1'">
            <!-- FORMULARIO DE ALTA / EDICIÓN -->
            <div v-if="canManage"
                class="rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-xl shadow-slate-300/30 backdrop-blur-xl">
                <div class="mb-6">
                    <h3 class="text-xl font-bold text-[#081426]">
                        {{ isEditing ? "Editar pieza" : "Nueva pieza" }}
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Alta de referencias con datos técnicos y productivos.
                    </p>
                </div>

                <form class="space-y-4" @submit.prevent="submitForm">
                    <!-- CÓDIGO DE PIEZA -->
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-700">Código</label>
                        <input v-model="form.codigo" type="text" class="isavex-input" placeholder="Ej. 90112502" />
                    </div>

                    <!-- DENOMINACIÓN -->
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-700">Denominación</label>
                        <input v-model="form.denominacion" type="text" @blur="autocompletarCamposProductivos"
                            class="isavex-input" placeholder="Ej. SOPORTE INF DRL AUDI AU270 LED I" />
                        <p class="mt-2 text-xs leading-5 text-slate-500">
                            Al salir del campo se intenta detectar lado, mercado y categoría automáticamente.
                        </p>
                    </div>

                    <!-- CLIENTE Y MODELO -->
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-semibold text-slate-700">Cliente</label>
                            <select v-model="form.cliente_id" @change="onClienteChange" class="isavex-input">
                                <option value="">Cliente</option>
                                <option v-for="cliente in clientes" :key="cliente.id" :value="cliente.id">
                                    {{ cliente.nombre }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-semibold text-slate-700">Modelo</label>
                            <select v-model="form.modelo_id" class="isavex-input">
                                <option value="">Modelo</option>
                                <option v-for="modelo in modelosFiltrados" :key="modelo.id" :value="modelo.id">
                                    {{ modelo.nombre }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- MOLDE ASOCIADO -->
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-700">Molde</label>
                        <select v-model="form.molde_id" class="isavex-input">
                            <option value="">Molde opcional</option>
                            <option v-for="molde in moldes" :key="molde.id" :value="molde.id">
                                {{ molde.codigo }} - {{ molde.descripcion }}
                            </option>
                        </select>
                    </div>

                    <!-- CAMPOS PRODUCTIVOS -->
                    <div class="grid gap-4 sm:grid-cols-3">
                        <div>
                            <label class="mb-1 block text-sm font-semibold text-slate-700">Lado</label>
                            <select v-model="form.lado_pieza" class="isavex-input">
                                <option value="">—</option>
                                <option value="izquierda">Izquierda</option>
                                <option value="derecha">Derecha</option>
                                <option value="neutra">Neutra</option>
                            </select>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-semibold text-slate-700">Mercado</label>
                            <select v-model="form.mercado" class="isavex-input">
                                <option value="">—</option>
                                <option value="LHD">LHD</option>
                                <option value="RHD">RHD</option>
                                <option value="TI">TI</option>
                                <option value="TD">TD</option>
                            </select>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-semibold text-slate-700">Categoría</label>
                            <select v-model="form.categoria_funcional" class="isavex-input">
                                <option value="">—</option>
                                <option value="soporte">Soporte</option>
                                <option value="guia_luz">Guía luz</option>
                                <option value="reflector">Reflector</option>
                                <option value="embellecedor">Embellecedor</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                    </div>

                    <!-- ERROR DEL FORMULARIO -->
                    <p v-if="formError" class="text-sm font-semibold text-red-600">
                        {{ formError }}
                    </p>

                    <!-- ACCIONES DEL FORMULARIO -->
                    <div class="flex flex-col gap-3 pt-2">
                        <button type="submit" :disabled="saving"
                            class="isavex-button px-4 py-3 text-sm disabled:opacity-60">
                            {{ saving ? "Guardando..." : isEditing ? "Actualizar pieza" : "Crear pieza" }}
                        </button>

                        <button v-if="isEditing" type="button" @click="resetForm"
                            class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                            Cancelar edición
                        </button>
                    </div>
                </form>
            </div>

            <!-- LISTADO DE PIEZAS -->
            <div class="rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-xl shadow-slate-300/30 backdrop-blur-xl"
                :class="canManage ? '' : 'xl:col-span-1'">
                <div class="mb-5 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-[#081426]">Listado de piezas</h3>
                        <p class="mt-1 text-sm text-slate-500">
                            Referencias disponibles en el sistema.
                        </p>
                    </div>

                    <!-- BUSCADOR -->
                    <div
                        class="flex w-full items-center gap-3 rounded-2xl border border-slate-200/70 bg-white/80 px-4 py-3 shadow-sm lg:max-w-md">
                        <span class="text-slate-400">⌕</span>
                        <input v-model="busqueda" type="text" placeholder="Buscar código, modelo, molde, mercado..."
                            class="w-full bg-transparent text-sm text-slate-700 outline-none placeholder:text-slate-400" />
                    </div>
                </div>

                <!-- ERROR GENERAL -->
                <p v-if="error"
                    class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                    {{ error }}
                </p>

                <!-- ESTADO DE CARGA -->
                <p v-if="loading" class="text-sm text-slate-500">
                    Cargando piezas...
                </p>

                <!-- TABLA -->
                <div v-else class="overflow-x-auto">
                    <table class="min-w-full border-separate border-spacing-y-3 text-sm">
                        <thead>
                            <tr class="text-left text-xs uppercase tracking-[0.18em] text-slate-400">
                                <th class="px-4 py-2">Código</th>
                                <th class="px-4 py-2">Denominación</th>
                                <th class="px-4 py-2">Modelo</th>
                                <th class="px-4 py-2">Molde</th>
                                <th class="px-4 py-2">Lado</th>
                                <th class="px-4 py-2">Mercado</th>
                                <th class="px-4 py-2">Categoría</th>
                                <th v-if="canManage" class="px-4 py-2 text-right">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="pieza in piezasFiltradas" :key="pieza.id"
                                class="bg-white/90 text-slate-800 shadow-sm">
                                <td class="rounded-l-2xl px-4 py-4 font-bold text-[#081426]">
                                    {{ pieza.codigo }}
                                </td>

                                <td class="max-w-[360px] px-4 py-4 text-slate-600">
                                    {{ pieza.denominacion }}
                                </td>

                                <td class="px-4 py-4">
                                    {{ pieza.modelo?.nombre || "—" }}
                                </td>

                                <td class="px-4 py-4">
                                    <span
                                        class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                        {{ pieza.molde?.codigo || "Sin molde" }}
                                    </span>
                                </td>

                                <td class="px-4 py-4">
                                    {{ pieza.lado_pieza || "—" }}
                                </td>

                                <td class="px-4 py-4">
                                    {{ pieza.mercado || "—" }}
                                </td>

                                <td class="px-4 py-4" :class="!canManage ? 'rounded-r-2xl' : ''">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold ring-1"
                                        :class="badgeClass(pieza.categoria_funcional)">
                                        {{ formatCategoria(pieza.categoria_funcional) }}
                                    </span>
                                </td>

                                <!-- ACCIONES -->
                                <td v-if="canManage" class="rounded-r-2xl px-4 py-4">
                                    <div class="flex justify-end gap-2">
                                        <button @click="editPieza(pieza)"
                                            class="flex h-10 w-10 items-center justify-center rounded-xl border border-amber-200 bg-amber-50 text-amber-700 transition hover:scale-105 hover:bg-amber-100"
                                            title="Editar pieza">
                                            <Pencil class="h-4 w-4" />
                                        </button>

                                        <button @click="removePieza(pieza)"
                                            class="flex h-10 w-10 items-center justify-center rounded-xl border border-red-200 bg-red-50 text-red-700 transition hover:scale-105 hover:bg-red-100"
                                            title="Eliminar pieza">
                                            <Trash2 class="h-4 w-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="piezasFiltradas.length === 0">
                                <td :colspan="canManage ? 8 : 7"
                                    class="rounded-2xl bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
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
