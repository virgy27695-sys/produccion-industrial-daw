<script setup>
// PROPS
defineProps({
    show: {
        type: Boolean,
        default: false,
    },

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

    tone: {
        type: String,
        default: "danger",
    },
})


// EMITS
const emit = defineEmits([
    "confirm",
    "cancel",
])
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-[80] flex items-center justify-center bg-slate-900/45 p-4 backdrop-blur-sm">
        <div class="w-full max-w-md rounded-[2rem] border border-white/70 bg-white p-6 shadow-2xl shadow-slate-900/20">
            <div class="mb-5">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl" :class="{
                    'bg-red-100 text-red-700': tone === 'danger',
                    'bg-cyan-100 text-cyan-700': tone === 'primary',
                    'bg-amber-100 text-amber-700': tone === 'warning',
                }">
                    !
                </div>

                <h3 class="text-xl font-bold text-[#081426]">
                    {{ title }}
                </h3>

                <p class="mt-2 text-sm leading-6 text-slate-500">
                    {{ message }}
                </p>
            </div>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <button type="button"
                    class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                    @click="emit('cancel')">
                    {{ cancelText }}
                </button>

                <button type="button" class="rounded-2xl px-5 py-3 text-sm font-semibold text-white transition" :class="{
                    'bg-red-600 hover:bg-red-700': tone === 'danger',
                    'bg-[#081426] hover:bg-[#10233D]': tone === 'primary',
                    'bg-amber-600 hover:bg-amber-700': tone === 'warning',
                }" @click="emit('confirm')">
                    {{ confirmText }}
                </button>
            </div>
        </div>
    </div>
</template>