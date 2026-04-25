<script setup>
// IMPORTS
import { ref, onMounted, computed } from "vue"
import { apiGet } from "../api/http"


// ESTADO REACTIVO
const datos = ref([])        // Datos de situación por pieza
const loading = ref(false)   // Control de carga
const error = ref("")        // Mensaje de error
const busqueda = ref("")     // Filtro de búsqueda


// FILTRADO
// Permite buscar por:
// - código
// - denominación
const datosFiltrados = computed(() => {
    const texto = busqueda.value.trim().toLowerCase()

    if (!texto) return datos.value

    return datos.value.filter((item) => {
        return (
            (item.pieza || "").toLowerCase().includes(texto) ||
            (item.denominacion || "").toLowerCase().includes(texto)
        )
    })
})


// CARGA DE DATOS
async function loadData() {
    loading.value = true
    error.value = ""

    try {
        datos.value = await apiGet("/situacion")
    } catch (e) {
        error.value = "No se pudo cargar la situación."
        console.error(e)
    } finally {
        loading.value = false
    }
}


// SEMÁFORO (colores)
function getColor(estado) {
    if (estado === "critico") return "bg-red-100 text-red-700"
    if (estado === "medio") return "bg-amber-100 text-amber-700"
    return "bg-green-100 text-green-700"
}


// INICIALIZACIÓN
onMounted(loadData)
</script>

<template>
    <section class="space-y-6">

        <!-- TÍTULO -->
        <div>
            <h2 class="text-2xl font-semibold text-slate-800">
                Situación de Producción
            </h2>
            <p class="text-slate-600">
                Control de stock, producción y entregas por pieza.
            </p>
        </div>

        <!-- BUSCADOR -->
        <div class="bg-white p-4 rounded-xl shadow">
            <input v-model="busqueda" placeholder="Buscar por código o denominación..." class="input w-full" />
        </div>

        <!-- ERRORES -->
        <p v-if="error" class="text-red-600">
            {{ error }}
        </p>

        <!-- CARGA -->
        <p v-if="loading" class="text-slate-500">
            Cargando datos...
        </p>

        <!-- TABLA -->
        <div v-else class="bg-white p-6 rounded-2xl shadow overflow-x-auto">

            <table class="w-full text-sm">

                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Pieza</th>
                        <th>Programa</th>
                        <th>Fabricado</th>
                        <th>Entregado</th>
                        <th>Stock</th>
                        <th>Seguridad</th>
                        <th>Disponible</th>
                        <th>Pendiente</th>
                        <th>Estado</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="item in datosFiltrados" :key="item.pieza">

                        <td class="font-medium">
                            {{ item.pieza }}
                        </td>

                        <td>
                            {{ item.denominacion }}
                        </td>

                        <td>
                            {{ item.programado }}
                        </td>

                        <td>
                            {{ item.fabricado }}
                        </td>

                        <td>
                            {{ item.entregado }}
                        </td>

                        <td>
                            {{ item.stock_actual }}
                        </td>

                        <td>
                            {{ item.stock_seguridad }}
                        </td>

                        <td>
                            {{ item.disponible }}
                        </td>

                        <td class="font-semibold">
                            {{ item.pendiente }}
                        </td>

                        <!-- SEMÁFORO -->
                        <td>
                            <span class="px-3 py-1 rounded-full text-xs font-medium" :class="getColor(item.estado)">
                                {{ item.estado }}
                            </span>
                        </td>

                    </tr>

                    <tr v-if="datosFiltrados.length === 0">
                        <td colspan="10" class="text-center text-slate-500 py-6">
                            No hay datos disponibles.
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

    </section>
</template>