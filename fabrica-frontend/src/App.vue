<template>
  <div>
    <Navbar />
    <div class="container mt-4">
      <RouterView />
    </div>
  </div>
</template>

<script setup>
import Navbar from "./components/Navbar.vue";
</script>