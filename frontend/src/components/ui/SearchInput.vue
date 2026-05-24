<script setup>
// PROPS
// Input reutilizable para buscadores de vistas.

defineProps({
    // Valor enlazado con v-model desde la vista.
    modelValue: {
        type: String,
        default: "",
    },

    // Texto que aparece cuando el campo está vacío.
    placeholder: {
        type: String,
        default: "Buscar...",
    },
})

// EVENTOS
// Permite actualizar el valor del v-model desde el componente padre.
const emit = defineEmits(["update:modelValue"])
</script>

<template>
    <!-- BUSCADOR REUTILIZABLE -->
    <div
        class="flex w-full items-center gap-3 rounded-2xl border border-slate-200/70 bg-white/80 px-4 py-2.5 shadow-sm backdrop-blur lg:max-w-md">
        <span class="text-slate-400">⌕</span>

        <input :value="modelValue" type="text" :placeholder="placeholder"
            class="w-full bg-transparent text-sm text-slate-700 outline-none placeholder:text-slate-400"
            @input="emit('update:modelValue', $event.target.value)" />
    </div>
</template>
