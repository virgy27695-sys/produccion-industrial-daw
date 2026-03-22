<script setup>
import Sidebar from "../components/Sidebar.vue"
import Navbar from "../components/Navbar.vue"
import Toast from "../components/Toast.vue"
</script>

<template>
  <div class="min-h-screen bg-slate-100 md:flex">
    <Sidebar />

    <div class="flex-1 md:ml-0">
      <Navbar />

      <main class="min-h-screen p-4 pt-20 md:p-6 md:pt-6">
        <router-view />
      </main>
    </div>

    <Toast />
  </div>
</template>