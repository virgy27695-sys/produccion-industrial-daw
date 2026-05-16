<!-- src/components/ui/table/DataTable.vue -->

<script setup>
// PROPS
defineProps({
    loading: Boolean,

    empty: Boolean,

    loadingText: {
        type: String,
        default: "Cargando datos...",
    },

    emptyTitle: {
        type: String,
        default: "Sin datos",
    },

    emptyDescription: {
        type: String,
        default: "No hay registros disponibles.",
    },
})


// COMPONENTES
import TableLoading from "./TableLoading.vue"
import TableEmpty from "./TableEmpty.vue"
</script>

<template>
    <!-- LOADING -->
    <TableLoading v-if="loading" :text="loadingText" />

    <!-- EMPTY -->
    <TableEmpty v-else-if="empty" :title="emptyTitle" :description="emptyDescription" />

    <!-- TABLA -->
    <div v-else class="overflow-x-auto">
        <slot />
    </div>
</template>