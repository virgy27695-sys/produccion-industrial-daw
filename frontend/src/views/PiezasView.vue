<script setup>
import { ref, onMounted } from "vue"
import { getClientes } from "../api/clientes"
import { getModelos } from "../api/modelos"
import { getPiezas } from "../api/piezas"

const piezas = ref([])
const clientes = ref([])
const modelos = ref([])

const clienteSeleccionado = ref("")

async function loadClientes() {
    clientes.value = await getClientes()
}

async function clienteChange() {
    if (!clienteSeleccionado.value) {
        modelos.value = []
        return
    }

    const todos = await getModelos()
    modelos.value = todos.filter(
        (m) => Number(m.cliente_id) === Number(clienteSeleccionado.value)
    )
}
async function loadPiezas() {
    piezas.value = await getPiezas()
}

onMounted(() => {
    loadClientes()
    loadPiezas()
})
</script>

<template>
    <div class="space-y-6">
        <h1 class="text-2xl font-bold">Piezas</h1>

        <div class="grid gap-4 max-w-md">
            <select v-model="clienteSeleccionado" @change="clienteChange" class="p-2 border rounded">
                <option value="">Selecciona cliente</option>

                <option v-for="c in clientes" :key="c.id" :value="c.id">
                    {{ c.nombre }}
                </option>
            </select>

            <select class="p-2 border rounded">
                <option value="">Selecciona modelo</option>

                <option v-for="m in modelos" :key="m.id" :value="m.id">
                    {{ m.nombre }}
                </option>
            </select>
        </div>

        <table class="w-full bg-white rounded shadow overflow-hidden">
            <thead class="bg-slate-200">
                <tr>
                    <th class="p-3 text-left">Código</th>
                    <th class="p-3 text-left">Denominación</th>
                    <th class="p-3 text-left">Modelo</th>
                    <th class="p-3 text-left">Cliente</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="pieza in piezas" :key="pieza.id" class="border-t">
                    <td class="p-3">{{ pieza.codigo }}</td>
                    <td class="p-3">{{ pieza.denominacion }}</td>
                    <td class="p-3">{{ pieza.modelo.nombre }}</td>
                    <td class="p-3">{{ pieza.modelo.cliente.nombre }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
