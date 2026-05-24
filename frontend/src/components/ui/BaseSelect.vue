<script setup>
// PROPS
defineProps({
    modelValue: {
        type: [String, Number],
        default: "",
    },

    disabled: {
        type: Boolean,
        default: false,
    },
})


// EMITS
const emit = defineEmits([
    "update:modelValue",
])
</script>

<template>
    <!-- SELECT REUTILIZABLE -->
    <select :value="modelValue" :disabled="disabled"
        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-cyan-400 focus:ring-4 focus:ring-cyan-100 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400"
        @change="emit('update:modelValue', $event.target.value)">
        <slot />
    </select>
</template>
