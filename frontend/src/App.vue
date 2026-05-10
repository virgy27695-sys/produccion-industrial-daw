<script setup>
import { computed } from "vue"
import { useRoute } from "vue-router"

import MainLayout from "./layouts/MainLayout.vue"
import GuestLayout from "./layouts/GuestLayout.vue"

const route = useRoute()

const hideLayout = computed(() => route.meta.hideLayout === true)
</script>

<template>
  <GuestLayout v-if="hideLayout" />
  <MainLayout v-else />
</template>