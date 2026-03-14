<script setup>
import { computed } from "vue"
import { useToastStore } from "../stores/toast"

const toast = useToastStore()

const visible = computed(() => toast.message !== "")
</script>

<template>
  <transition name="fade">
    <div v-if="visible" class="fixed top-6 right-6 z-50 rounded-xl px-6 py-3 shadow-lg text-white"
      :class="toast.type === 'error' ? 'bg-red-600' : 'bg-green-600'">
      {{ toast.message }}
    </div>
  </transition>
</template>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
