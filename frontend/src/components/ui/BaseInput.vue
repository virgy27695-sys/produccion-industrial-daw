<script setup>

defineProps({

    modelValue: {
        type: [String, Number],
        default: "",
    },

    placeholder: {
        type: String,
        default: "",
    },

    type: {
        type: String,
        default: "text",
    },

    disabled: {
        type: Boolean,
        default: false,
    },

    min: {
        type: [String, Number],
        default: null,
    },

    max: {
        type: [String, Number],
        default: null,
    },

    step: {
        type: [String, Number],
        default: null,
    },

})

const emit = defineEmits([
    "update:modelValue",
])

</script>

<template>

    <input :value="modelValue" :type="type" :placeholder="placeholder" :disabled="disabled" :min="min" :max="max"
        :step="step"
        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-cyan-400 focus:ring-4 focus:ring-cyan-100 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400"
        @input="
            emit(
                'update:modelValue',
                $event.target.value
            )
            " />

</template>
