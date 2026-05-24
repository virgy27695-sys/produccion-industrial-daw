<script setup>
// PROPS
// Badge reutilizable para estados, categorías y etiquetas.

defineProps({
    // Texto visible del badge.
    label: {
        type: String,
        required: true,
    },

    // Color visual del badge.
    tone: {
        type: String,
        default: "slate",
    },
})
</script>

<template>
    <!-- BADGE REUTILIZABLE -->
    <span :class="{
        'bg-slate-100 text-slate-600 ring-slate-200': tone === 'slate',
        'bg-cyan-100 text-cyan-700 ring-cyan-200': tone === 'cyan',
        'bg-green-100 text-green-700 ring-green-200': tone === 'green',
        'bg-amber-100 text-amber-700 ring-amber-200': tone === 'amber',
        'bg-violet-100 text-violet-700 ring-violet-200': tone === 'violet',
        'bg-red-100 text-red-700 ring-red-200': tone === 'red',

        'bg-orange-100 text-orange-700 ring-orange-200': tone === 'pending',

        'bg-emerald-100 text-emerald-700 ring-emerald-200': tone === 'success',

        'bg-rose-100 text-rose-700 ring-rose-200': tone === 'critical'
    }">
        {{ label }}
    </span>
</template>
