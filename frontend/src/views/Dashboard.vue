<script setup>
import { ref, onMounted } from "vue"

import { getClientes } from "../api/clientes"
import { getModelos } from "../api/modelos"
import { getPiezas } from "../api/piezas"
import { getPedidos } from "../api/pedidos"

const clientes = ref(0)
const modelos = ref(0)
const piezas = ref(0)
const pedidos = ref(0)
const error = ref("")

onMounted(async () => {
  try {
    const [clientesData, modelosData, piezasData, pedidosData] = await Promise.all([
      getClientes(),
      getModelos(),
      getPiezas(),
      getPedidos()
    ])

    clientes.value = clientesData.length
    modelos.value = modelosData.length
    piezas.value = piezasData.length
    pedidos.value = pedidosData.length
  } catch (e) {
    error.value = "No se pudo conectar con la API."
    console.error(e)
  }
})
</script>


<template>

  <div>

    <h2 class="text-2xl font-semibold mb-6 text-slate-800">
      Dashboard de Producción
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

      <div class="bg-white p-6 rounded-xl shadow">
        <h3 class="text-sm text-slate-500">Clientes</h3>
        <p class="text-3xl font-bold text-slate-800 mt-2">
          {{ clientes }}
        </p>
      </div>

      <div class="bg-white p-6 rounded-xl shadow">
        <h3 class="text-sm text-slate-500">Modelos</h3>
        <p class="text-3xl font-bold text-slate-800 mt-2">
          {{ modelos }}
        </p>
      </div>

      <div class="bg-white p-6 rounded-xl shadow">
        <h3 class="text-sm text-slate-500">Piezas</h3>
        <p class="text-3xl font-bold text-slate-800 mt-2">
          {{ piezas }}
        </p>
      </div>

      <div class="bg-white p-6 rounded-xl shadow">
        <h3 class="text-sm text-slate-500">Pedidos</h3>
        <p class="text-3xl font-bold text-slate-800 mt-2">
          {{ pedidos }}
        </p>
      </div>

    </div>

  </div>

</template>
