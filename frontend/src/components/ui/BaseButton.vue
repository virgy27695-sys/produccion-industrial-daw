<script setup>
// PROPS
defineProps({
    // COLOR VISUAL
    tone: {
        type: String,
        default: "primary",
    },

    // BOTÓN DESHABILITADO
    disabled: {
        type: Boolean,
        default: false,
    },

    // TIPO HTML
    type: {
        type: String,
        default: "button",
    },
})
</script>

<template>
    <!--
        BOTÓN REUTILIZABLE GLOBAL

        Ventajas:
        - diseño consistente
        - mantenimiento sencillo
        - menos duplicación
        - responsive uniforme
    -->

    <button :type="type" :disabled="disabled"
        class="inline-flex items-center justify-center rounded-2xl px-4 py-2.5 text-sm font-semibold transition-all duration-200 disabled:cursor-not-allowed disabled:opacity-60"
        :class="{
            'bg-[#081426] text-white hover:bg-[#10233D]':
                tone === 'primary',

            'bg-red-600 text-white hover:bg-red-700':
                tone === 'danger',

            'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50':
                tone === 'secondary',

            'bg-cyan-600 text-white hover:bg-cyan-700':
                tone === 'cyan',
        }">
        <slot />
    </button>
</template>