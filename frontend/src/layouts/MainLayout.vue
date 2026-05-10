<script setup>
import { ref } from "vue"

import Sidebar from "../components/Sidebar.vue"
import AppHeader from "../components/AppHeader.vue"
import Toast from "../components/Toast.vue"

const sidebarCollapsed = ref(false)

function toggleSidebar() {
  sidebarCollapsed.value = !sidebarCollapsed.value
}
</script>

<template>
  <div class="flex min-h-screen bg-gradient-to-br from-[#F8FBFD] via-[#EEF7FA] to-[#DFF3F8]">
    <!-- SIDEBAR -->
    <Sidebar :collapsed="sidebarCollapsed" @toggle="toggleSidebar" />

    <!-- ZONA PRINCIPAL -->
    <div class="flex min-h-screen flex-1 flex-col overflow-hidden">
      <!-- HEADER -->
      <AppHeader :sidebar-collapsed="sidebarCollapsed" @toggle-sidebar="toggleSidebar" />

      <!-- CONTENIDO -->
      <main class="flex-1 p-4 md:p-7">
        <div class="isavex-card min-h-full p-5 md:p-8">
          <router-view />
        </div>
      </main>

      <!-- FOOTER -->
      <footer class="border-t border-white/70 bg-white/70 backdrop-blur-xl">
        <div
          class="mx-auto flex max-w-7xl flex-col gap-6 px-6 py-5 text-sm md:flex-row md:items-center md:justify-between">
          <div class="flex items-center gap-4">
            <div class="flex h-16 w-[220px] items-center overflow-hidden">
              <img src="/logo-isavex.png" alt="ISAVEX" class="h-52 w-auto -translate-x-8 object-contain" />
            </div>

            <div>
              <p class="font-semibold text-[#081426]">
                ISAVEX
              </p>

              <p class="text-xs text-slate-500">
                Gestión inteligente de producción industrial
              </p>
            </div>
          </div>

          <div class="flex flex-wrap items-center gap-5 text-slate-500">
            <span>Producción</span>
            <span>Planning</span>
            <span>Trazabilidad</span>
            <span>Automoción</span>
          </div>

          <div class="text-xs text-slate-400">
            © 2026 ISAVEX · Software industrial
          </div>
        </div>
      </footer>
    </div>

    <Toast />
  </div>
</template>