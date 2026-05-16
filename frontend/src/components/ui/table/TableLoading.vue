<script setup>
// PROPS
defineProps({
    text: {
        type: String,
        default: "Cargando datos...",
    },
})
</script>

<template>
    <!-- ESTADO DE CARGA -->
    <div class="flex items-center justify-center rounded-3xl border border-slate-200 bg-slate-50/70 px-6 py-10">
        <div class="text-center">
            <!-- SPINNER -->
            <div class="mx-auto mb-4 h-10 w-10 animate-spin rounded-full border-4 border-slate-200 border-t-[#59C7D8]">
            </div>

            <!-- TEXTO -->
            <p class="text-sm font-medium text-slate-500">
                {{ text }}
            </p>
        </div>
    </div>
</template>