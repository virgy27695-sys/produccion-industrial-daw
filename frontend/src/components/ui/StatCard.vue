<script setup>
// PROPS
// Tarjeta estadística reutilizable

defineProps({
    // TEXTO SUPERIOR
    label: {
        type: String,
        required: true,
    },

    // VALOR PRINCIPAL
    value: {
        type: [String, Number],
        required: true,
    },

    // TONALIDAD VISUAL
    // Permite reutilizar el componente
    // con distintos colores
    tone: {
        type: String,
        default: "slate",
    },
})
</script>

<template>
    <!--
        TARJETA ESTADÍSTICA REUTILIZABLE

        Uso:
        - KPIs
        - dashboards
        - métricas
        - contadores
        - estados

        Ventajas:
        - diseño uniforme
        - reutilización
        - menor duplicación
        - responsive consistente
    -->
    <div class="rounded-2xl border p-3 shadow-sm" :class="{
        'border-white/70 bg-white/80': tone === 'slate',
        'border-cyan-200/60 bg-cyan-50/80': tone === 'cyan',
        'border-green-200/60 bg-green-50/80': tone === 'green',
        'border-amber-200/60 bg-amber-50/80': tone === 'amber',
        'border-violet-200/60 bg-violet-50/80': tone === 'violet',
        'border-red-200/60 bg-red-50/80': tone === 'red',
    }">
        <!-- LABEL -->
        <p class="text-[10px] font-semibold uppercase tracking-[0.16em]" :class="{
            'text-slate-400': tone === 'slate',
            'text-cyan-600': tone === 'cyan',
            'text-green-600': tone === 'green',
            'text-amber-600': tone === 'amber',
            'text-violet-600': tone === 'violet',
            'text-red-600': tone === 'red',
        }">
            {{ label }}
        </p>

        <!-- VALOR -->
        <p class="mt-1 text-2xl font-bold" :class="{
            'text-[#081426]': tone === 'slate',
            'text-cyan-700': tone === 'cyan',
            'text-green-700': tone === 'green',
            'text-amber-700': tone === 'amber',
            'text-violet-700': tone === 'violet',
            'text-red-700': tone === 'red',
        }">
            {{ value }}
        </p>
    </div>
</template>
