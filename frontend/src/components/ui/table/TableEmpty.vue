<!-- src/components/ui/table/TableEmpty.vue -->

<script setup>
// PROPS
defineProps({
    title: {
        type: String,
        default: "Sin datos",
    },

    description: {
        type: String,
        default: "No hay registros disponibles.",
    },
})
</script>

<template>
    <!-- ESTADO VACÍO -->
    <div class="rounded-3xl border border-dashed border-[#59C7D8]/30 bg-white/60 px-6 py-14 text-center">
        <!-- ICONO -->
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-3xl bg-cyan-50 text-3xl">
            📭
        </div>

        <!-- TÍTULO -->
        <h3 class="text-lg font-bold text-[#081426]">
            {{ title }}
        </h3>

        <!-- DESCRIPCIÓN -->
        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
            {{ description }}
        </p>
    </div>
</template>