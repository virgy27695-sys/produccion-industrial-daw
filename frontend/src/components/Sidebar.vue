<script setup>
import { computed } from "vue"
import { RouterLink, useRoute } from "vue-router"

const props = defineProps({
  collapsed: Boolean,
})

const emit = defineEmits(["toggle"])

const route = useRoute()

const items = [
  {
    name: "Dashboard",
    icon: "fa-solid fa-table-columns",
    to: "/",
  },
  {
    name: "Clientes",
    icon: "fa-regular fa-user",
    to: "/clients",
  },
  {
    name: "Modelos",
    icon: "fa-solid fa-cubes",
    to: "/models",
  },
  {
    name: "Piezas",
    icon: "fa-solid fa-cube",
    to: "/parts",
  },
  {
    name: "Moldes",
    icon: "fa-solid fa-wrench",
    to: "/molds",
  },
  {
    name: "Programas",
    icon: "fa-regular fa-clipboard",
    to: "/programs",
  },
  {
    name: "Producción",
    icon: "fa-solid fa-industry",
    to: "/production",
  },
  {
    name: "Pedidos",
    icon: "fa-solid fa-cart-shopping",
    to: "/orders",
  },
  {
    name: "Situación",
    icon: "fa-solid fa-wave-square",
    to: "/status",
  },
]

function isActive(path) {
  return route.path === path
}
</script>

<template>
  <aside :class="[
    collapsed ? 'w-[92px]' : 'w-[280px]',
    'relative flex min-h-screen flex-col overflow-hidden border-r border-white/70 bg-gradient-to-b from-[#F8FBFD] via-[#EEF7FA] to-[#DFF3F8] shadow-xl shadow-slate-300/40 transition-all duration-300',
  ]">
    <!-- GLOW -->
    <div class="absolute -top-20 left-10 h-72 w-72 rounded-full bg-cyan-400/10 blur-3xl"></div>

    <!-- HEADER -->
      <div class="overflow-hidden transition-all duration-300"
        :class="collapsed ? 'w-0 opacity-0' : 'w-[220px] opacity-100'">
        <img src="/logo-isavex.png" alt="ISAVEX"
          class="h-28 w-auto -translate-x-4 object-contain drop-shadow-[0_0_14px_rgba(89,199,216,0.25)]" />
      </div>

    <!-- NAV -->
    <nav class="relative z-10 flex-1 space-y-2 px-4 py-5">
      <RouterLink v-for="item in items" :key="item.to" :to="item.to" :class="[
        isActive(item.to)
          ? 'bg-gradient-to-r from-[#59C7D8] to-[#49B3C2] text-[#081426] shadow-lg shadow-cyan-500/20'
          : 'text-slate-600 hover:bg-white/70 hover:text-[#081426]',
        collapsed
          ? 'justify-center px-0'
          : 'justify-start px-4',
        'group flex h-14 items-center gap-4 rounded-2xl transition-all duration-200',
      ]">
        <i :class="[item.icon, 'text-lg']"></i>

        <span v-if="!collapsed" class="text-sm font-medium">
          {{ item.name }}
        </span>
      </RouterLink>
    </nav>

    <!-- FOOT -->
    <div class="relative z-10 p-4">
      <div class="rounded-3xl border border-[#59C7D8]/30 bg-white/70 p-5 shadow-sm backdrop-blur-xl">
        <div class="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.25em] text-[#59C7D8]">
          <div class="h-2 w-2 rounded-full bg-cyan-300 shadow-[0_0_12px_#59C7D8]"></div>
          <span v-if="!collapsed">
            Sistema activo
          </span>
        </div>

        <p v-if="!collapsed" class="text-sm leading-7 text-slate-600">
          Plataforma de planificación, producción y trazabilidad industrial.
        </p>
      </div>
    </div>
  </aside>
</template>