<script setup>
defineProps({
    show: Boolean,
    title: {
        type: String,
        default: "Confirmar acción",
    },
    message: {
        type: String,
        default: "¿Seguro que quieres continuar?",
    },
    confirmText: {
        type: String,
        default: "Confirmar",
    },
    cancelText: {
        type: String,
        default: "Cancelar",
    },
})

defineEmits([
    "confirm",
    "cancel",
])
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/40 p-4 backdrop-blur-sm">
        <div class="w-full max-w-md rounded-[2rem] border border-white/70 bg-white p-6 shadow-2xl">
            <h2 class="text-xl font-black text-[#081426]">
                {{ title }}
            </h2>

            <p class="mt-3 text-sm leading-6 text-slate-600">
                {{ message }}
            </p>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" @click="$emit('cancel')"
                    class="rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                    {{ cancelText }}
                </button>

                <button type="button" @click="$emit('confirm')"
                    class="rounded-2xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                    {{ confirmText }}
                </button>
            </div>
        </div>
    </div>
</template>