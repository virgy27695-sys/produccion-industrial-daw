<template>
  <div class="container">
    <h3 class="mb-3">Piezas</h3>

    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    <table v-if="piezas.length" class="table table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Código</th>
          <th>Denominación</th>
          <th>Modelo</th>
          <th>Cliente</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="p in piezas" :key="p.id">
          <td>{{ p.id }}</td>
          <td>{{ p.codigo }}</td>
          <td>{{ p.denominacion }}</td>
          <td>{{ p.modelo }}</td>
          <td>{{ p.cliente }}</td>
        </tr>
      </tbody>
    </table>

    <div v-else-if="!error" class="text-muted">No hay piezas todavía.</div>
  </div>
</template>

<script>
import { ref, onMounted } from "vue";
import { getPiezas } from "../api";

export default {
  setup() {
    const piezas = ref([]);
    const error = ref("");

    onMounted(async () => {
      try {
        piezas.value = await getPiezas();
      } catch (e) {
        error.value = "No se pudo cargar piezas (revisa API/URL).";
      }
    });

    return { piezas, error };
  },
};
</script>