<script setup>
import { computed } from "vue"
import { useRoute } from "vue-router"
import MainLayout from "./layouts/MainLayout.vue"

const route = useRoute()

const hideLayout = computed(() => route.meta.hideLayout === true)
</script>

<template>
  <router-view v-if="hideLayout" />
  <MainLayout v-else />
</template>