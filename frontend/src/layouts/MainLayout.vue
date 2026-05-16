<script setup>
// IMPORTS
import { ref } from "vue"

import Sidebar from "../components/Sidebar.vue"
import AppHeader from "../components/AppHeader.vue"
import AppFooter from "../components/AppFooter.vue"
import Toast from "../components/Toast.vue"


// SIDEBAR ESCRITORIO
const sidebarCollapsed = ref(false)


// SIDEBAR MÓVIL
const mobileSidebarOpen = ref(false)


// TOGGLE SIDEBAR
function toggleSidebar() {
  const isMobile = window.matchMedia("(max-width: 767px)").matches

  if (isMobile) {
    mobileSidebarOpen.value = !mobileSidebarOpen.value
    return
  }

  sidebarCollapsed.value = !sidebarCollapsed.value
}


// CERRAR MÓVIL
function closeMobileSidebar() {
  mobileSidebarOpen.value = false
}
</script>

<template>
  <div class="relative flex min-h-screen overflow-hidden bg-gradient-to-br from-[#F8FBFD] via-[#EEF7FA] to-[#DFF3F8]">

    <!-- SIDEBAR DESKTOP -->
    <Sidebar class="hidden md:flex" :collapsed="sidebarCollapsed" />

    <!-- OVERLAY -->
    <div v-if="mobileSidebarOpen" class="fixed inset-0 z-40 bg-[#081426]/40 backdrop-blur-sm md:hidden"
      @click="closeMobileSidebar" />

    <!-- SIDEBAR MÓVIL -->
    <div
      class="fixed inset-y-0 left-0 z-50 w-[280px] max-w-[82vw] transform transition-transform duration-300 md:hidden"
      :class="mobileSidebarOpen
          ? 'translate-x-0'
          : '-translate-x-full'
        ">
      <Sidebar :collapsed="false" @click="closeMobileSidebar" />
    </div>

    <!-- CONTENIDO -->
    <div class="flex min-w-0 flex-1 flex-col overflow-hidden">

      <!-- HEADER -->
      <AppHeader :sidebar-collapsed="sidebarCollapsed" @toggle-sidebar="toggleSidebar" />

      <!-- MAIN -->
      <main class="flex-1 overflow-x-hidden overflow-y-auto p-2 md:p-4 lg:p-5">

        <div class="isavex-card min-h-full overflow-hidden p-3 md:p-5">

          <router-view />
        </div>
      </main>

      <!-- FOOTER -->
      <div class="hidden md:block">
        <AppFooter />
      </div>
    </div>

    <!-- TOAST -->
    <Toast />
  </div>
</template>