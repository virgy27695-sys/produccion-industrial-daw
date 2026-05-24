<script setup>
// PROPS
// Componente reutilizable para estados vacíos.

defineProps({
    // Título principal del mensaje vacío.
    title: {
        type: String,
        default: "Sin datos",
    },

    // Descripción adicional.
    description: {
        type: String,
        default: "No hay información disponible para mostrar.",
    },
})
</script>

<template>
    <!-- ESTADO VACÍO REUTILIZABLE -->
    <div class="rounded-3xl border border-dashed border-slate-300 bg-white/60 p-8 text-center">
        <p class="text-sm font-bold text-slate-700">
            {{ title }}
        </p>

        <p class="mt-2 text-sm text-slate-500">
            {{ description }}
        </p>
    </div>
</template>
