<script setup>
import { computed } from "vue"
import { useRouter } from "vue-router"
import { logout as apiLogout } from "../api/auth"

const router = useRouter()

const user = computed(() => {
  const localUser = localStorage.getItem("user")
  const sessionUser = sessionStorage.getItem("user")
  return JSON.parse(localUser || sessionUser || "null")
})

async function logout() {
  try {
    await apiLogout()
  } catch (error) {
    console.error(error)
  } finally {
    localStorage.removeItem("user")
    sessionStorage.removeItem("user")
    router.push("/login")
  }
}
</script>

<template>
  <header class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
    <div class="flex items-center justify-between gap-4">
      <div>
        <h1 class="text-lg font-semibold text-slate-800">
          Sistema de Producción Industrial
        </h1>
        <p class="text-sm text-slate-500">
          Panel de gestión
        </p>
      </div>

      <div class="flex items-center gap-3">
        <span class="rounded-full bg-slate-100 px-3 py-1 text-sm text-slate-600">
          {{ user?.name || "Usuario" }}<span v-if="user?.role"> ({{ user.role }})</span>
        </span>

        <button
          @click="logout"
          class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100"
        >
          Salir
        </button>
      </div>
    </div>
  </header>
</template>