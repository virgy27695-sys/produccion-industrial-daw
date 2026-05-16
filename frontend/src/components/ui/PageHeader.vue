<script setup>
// PROPS
// Componente reutilizable para cabeceras de página

defineProps({
    // ETIQUETA SUPERIOR
    // Ejemplo:
    // - Producción
    // - Industrial
    // - Dashboard
    label: {
        type: String,
        default: "",
    },

    // TÍTULO PRINCIPAL
    title: {
        type: String,
        required: true,
    },

    // DESCRIPCIÓN INFERIOR
    description: {
        type: String,
        default: "",
    },
})
</script>

<template>
    <!--
        CABECERA REUTILIZABLE DE PÁGINA

        Objetivos:
        - unificar diseño
        - evitar duplicación
        - facilitar mantenimiento
        - mejorar consistencia visual
    -->
    <div
        class="relative overflow-hidden rounded-3xl border border-white/70 bg-white/75 p-4 shadow-lg shadow-slate-300/25 backdrop-blur-xl">
        <!-- EFECTO VISUAL -->
        <div class="absolute -right-16 -top-16 h-56 w-56 rounded-full bg-[#59C7D8]/20 blur-3xl" />

        <!-- CONTENIDO -->
        <div class="relative z-10">
            <!-- LABEL -->
            <div v-if="label"
                class="mb-2 inline-flex rounded-full border border-[#59C7D8]/40 bg-white/70 px-3 py-1.5 text-[10px] font-semibold uppercase tracking-[0.22em] text-[#1597A8]">
                {{ label }}
            </div>

            <!-- TÍTULO -->
            <h1 class="text-2xl font-bold text-[#081426] md:text-3xl">
                {{ title }}
            </h1>

            <!-- DESCRIPCIÓN -->
            <p v-if="description" class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                {{ description }}
            </p>
        </div>
    </div>
</template>