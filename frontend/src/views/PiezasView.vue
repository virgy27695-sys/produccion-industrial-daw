<script setup>
// IMPORTS

import { computed, onMounted, ref } from "vue"
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
import { isAdmin } from "../utils/auth"


// ESTADO GENERAL
const toast = useToastStore()
const admin = isAdmin()

const piezas = ref([])
const clientes = ref([])
const modelos = ref([])
const moldes = ref([])

const loading = ref(false)
const saving = ref(false)
const error = ref("")
const formError = ref("")
const busqueda = ref("")


// FORMULARIO DE PIEZAS
// Incluye nuevos campos productivos:
// - molde
// - lado
// - mercado
// - categoría funcional

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


// SABER SI EL FORMULARIO ESTÁ EN EDICIÓN

const isEditing = computed(() => form.value.id !== null)


// MODELOS FILTRADOS POR CLIENTE
// Esto evita que se mezclen modelos de otros clientes

const modelosFiltrados = computed(() => {
    if (!form.value.cliente_id) return []

    return modelos.value.filter(
        (modelo) => Number(modelo.cliente_id) === Number(form.value.cliente_id)
    )
})


// FILTRADO DE PIEZAS EN TABLA
// Busca por:
// - código
// - denominación
// - modelo
// - cliente
// - molde
// - lado
// - mercado
// - categoría funcional

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


// CARGA INICIAL DE DATOS
// Se cargan piezas, clientes, modelos y moldes a la vez

async function loadData() {
    loading.value = true
    error.value = ""

    try {
        const [piezasData, clientesData, modelosData, moldesData] =
            await Promise.all([
                getPiezas(),
                getClientes(),
                getModelos(),
                getMoldes(),
            ])

        piezas.value = piezasData
        clientes.value = clientesData
        modelos.value = modelosData
        moldes.value = moldesData
    } catch (e) {
        error.value = "No se pudieron cargar las piezas."
        console.error(e)
    } finally {
        loading.value = false
    }
}

// REINICIAR FORMULARIO
// Deja el formulario limpio para alta nueva

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

    formError.value = ""
}


// CARGAR DATOS DE UNA PIEZA EN EDICIÓN

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

    formError.value = ""
}

// AL CAMBIAR CLIENTE, LIMPIAMOS MODELO
// Para evitar que quede seleccionado un modelo
// que no pertenece al nuevo cliente

function onClienteChange() {
    form.value.modelo_id = ""
}


// DETECCIÓN AUTOMÁTICA DE LADO
//
// IMPORTANTE:
// - IZQ / LEFT -> izquierda
// - DER / RIGHT / DRC -> derecha
// - TI / TD / LHD / RHD NO definen lado de pieza
// Si no hay patrón claro, devolvemos neutra

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
// Casos típicos:
// - LHD
// - RHD
// - TI (tráfico izquierdo)
// - TD (tráfico derecho)
// Si no aparece patrón, devuelve vacío

function detectarMercado(denominacion) {
    const texto = (denominacion || "").toUpperCase()

    if (texto.includes("LHD")) return "LHD"
    if (texto.includes("RHD")) return "RHD"
    if (texto.includes(" TI")) return "TI"
    if (texto.includes(" TD")) return "TD"

    return ""
}


// DETECCIÓN AUTOMÁTICA DE CATEGORÍA FUNCIONAL
// Basada en palabras clave de la denominación.
// Esto permite clasificar piezas para análisis

function detectarCategoriaFuncional(denominacion) {
    const texto = (denominacion || "").toUpperCase()

    if (texto.includes("SOPORTE")) return "soporte"
    if (texto.includes("GUIA DE LUZ") || texto.includes("GUÍA DE LUZ")) return "guia_luz"
    if (texto.includes("REFLECTOR")) return "reflector"
    if (texto.includes("EMB") || texto.includes("EMBELLECEDOR")) return "embellecedor"

    return "otro"
}


// AUTORRELLENO DE CAMPOS PRODUCTIVOS
// Solo rellena automáticamente si el campo está vacío.
// Así no pisamos valores que el usuario haya
// corregido manualmente.

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
// Valida campos y envía datos a backend para crear o actualizar pieza según el caso

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
    } catch (e) {
        formError.value = "No se pudo guardar la pieza."
        toast.show("Error al guardar pieza", "error")
        console.error(e)
    } finally {
        saving.value = false
    }
}


// ELIMINAR PIEZA

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

// INICIALIZACIÓN

onMounted(loadData)
</script>

<template>
    <section class="space-y-6">
        <!--CABECERA -->
        <div>
            <h2 class="mb-4 text-2xl font-semibold text-slate-800">Piezas</h2>
            <p class="text-slate-600">
                Gestión de piezas fabricadas, asociadas a cliente, modelo y molde,
                con información productiva adicional.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-6" :class="admin ? 'xl:grid-cols-3' : 'xl:grid-cols-1'">
            <!--FORMULARIO (solo admin) -->
            <div v-if="admin" class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 xl:col-span-1">
                <h3 class="mb-4 text-lg font-semibold text-slate-800">
                    {{ isEditing ? "Editar pieza" : "Nueva pieza" }}
                </h3>

                <form class="space-y-4" @submit.prevent="submitForm">
                    <!-- Código -->
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">
                            Código
                        </label>
                        <input v-model="form.codigo" type="text"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2 text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                            placeholder="Ej. 90112502" />
                    </div>

                    <!-- Denominación -->
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">
                            Denominación
                        </label>
                        <input v-model="form.denominacion" type="text" @blur="autocompletarCamposProductivos"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2 text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                            placeholder="Ej. SOPORTE INF DRL AUDI AU270 LED I" />
                        <p class="mt-1 text-xs text-slate-500">
                            Al salir del campo se intentan detectar automáticamente lado, mercado y categoría.
                        </p>
                    </div>

                    <!-- Cliente -->
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

                    <!-- Modelo -->
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

                    <!-- Molde -->
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">
                            Molde
                        </label>
                        <select v-model="form.molde_id"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2 text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                            <option value="">Molde (opcional)</option>
                            <option v-for="molde in moldes" :key="molde.id" :value="molde.id">
                                {{ molde.codigo }} - {{ molde.descripcion }}
                            </option>
                        </select>
                    </div>

                    <!-- Lado -->
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">
                            Lado de pieza
                        </label>
                        <select v-model="form.lado_pieza"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2 text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                            <option value="">Sin definir</option>
                            <option value="izquierda">Izquierda</option>
                            <option value="derecha">Derecha</option>
                            <option value="neutra">Neutra</option>
                        </select>
                    </div>

                    <!-- Mercado -->
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">
                            Mercado
                        </label>
                        <select v-model="form.mercado"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2 text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                            <option value="">Sin definir</option>
                            <option value="LHD">LHD</option>
                            <option value="RHD">RHD</option>
                            <option value="TI">TI</option>
                            <option value="TD">TD</option>
                        </select>
                    </div>

                    <!-- Categoría funcional -->
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">
                            Categoría funcional
                        </label>
                        <select v-model="form.categoria_funcional"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2 text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                            <option value="">Sin definir</option>
                            <option value="soporte">Soporte</option>
                            <option value="guia_luz">Guía de luz</option>
                            <option value="reflector">Reflector</option>
                            <option value="embellecedor">Embellecedor</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>

                    <!-- Error del formulario -->
                    <p v-if="formError" class="text-sm text-red-600">
                        {{ formError }}
                    </p>

                    <!-- Botones -->
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

            <!--TABLA DE PIEZAS-->
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
                :class="admin ? 'xl:col-span-2' : 'xl:col-span-1'">
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="text-lg font-semibold text-slate-800">
                        Listado de piezas
                    </h3>

                    <input v-model="busqueda" type="text"
                        placeholder="Buscar por código, denominación, modelo, cliente, molde, lado o mercado"
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
                                <th class="px-4 py-2">Molde</th>
                                <th class="px-4 py-2">Lado</th>
                                <th class="px-4 py-2">Mercado</th>
                                <th class="px-4 py-2">Categoría</th>
                                <th v-if="admin" class="px-4 py-2 text-right">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="pieza in piezasFiltradas" :key="pieza.id" class="bg-slate-50 text-slate-800">
                                <td class="rounded-l-xl px-4 py-3 font-medium">
                                    {{ pieza.codigo }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ pieza.denominacion }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ pieza.modelo?.nombre || "—" }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ pieza.molde?.codigo || "—" }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ pieza.lado_pieza || "—" }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ pieza.mercado || "—" }}
                                </td>

                                <td class="px-4 py-3" :class="!admin ? 'rounded-r-xl' : ''">
                                    {{ pieza.categoria_funcional || "—" }}
                                </td>

                                <td v-if="admin" class="rounded-r-xl px-4 py-3">
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
                                <td :colspan="admin ? 8 : 7" class="px-4 py-6 text-center text-sm text-slate-500">
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