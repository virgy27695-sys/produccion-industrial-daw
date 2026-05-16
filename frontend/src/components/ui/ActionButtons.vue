<script setup>
// IMPORTS
import {
    Pencil,
    Trash2,
} from "lucide-vue-next"


// EMITS
const emit = defineEmits([
    "edit",
    "delete",
])
</script>

<template>
    <!-- BOTONES ACCIONES -->
    <div class="flex items-center gap-2">
        <!-- EDITAR -->
        <button
            class="flex h-10 w-10 items-center justify-center rounded-xl border border-amber-200 bg-amber-50 text-amber-700 transition hover:bg-amber-100"
            title="Editar" @click="emit('edit')">
            <Pencil class="h-4 w-4" />
        </button>

        <!-- ELIMINAR -->
        <button
            class="flex h-10 w-10 items-center justify-center rounded-xl border border-red-200 bg-red-50 text-red-700 transition hover:bg-red-100"
            title="Eliminar" @click="emit('delete')">
            <Trash2 class="h-4 w-4" />
        </button>
    </div>
</template>