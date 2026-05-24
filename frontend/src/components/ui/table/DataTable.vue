<script setup>
import TableLoading from "./TableLoading.vue"
import TableEmpty from "./TableEmpty.vue"

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
</script>

<template>

    <TableLoading v-if="loading" :text="loadingText" />

    <TableEmpty v-else-if="empty" :title="emptyTitle" :description="emptyDescription" />

    <div v-else class="overflow-x-auto rounded-3xl">
        <slot />
    </div>

</template>
