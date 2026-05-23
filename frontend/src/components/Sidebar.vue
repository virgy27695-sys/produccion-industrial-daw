<script setup>
// IMPORTS
import { computed } from "vue"
import { RouterLink, useRoute } from "vue-router"

import {
  LayoutDashboard,
  Users,
  Boxes,
  Box,
  Wrench,
  ClipboardList,
  Factory,
  ShoppingCart,
  Activity,
  ShieldCheck,
  FileText,
} from "lucide-vue-next"

import {
  isAdmin,
  isPlanificador,
  isEncargado,
  isAlmacen,
} from "../utils/auth"


// PROPS
defineProps({
  collapsed: Boolean,
})


const route = useRoute()

const admin = isAdmin()
const planificador = isPlanificador()
const encargado = isEncargado()
const almacen = isAlmacen()


// ELEMENTOS DEL MENÚ POR ROL
const items = computed(() => {
  const baseItems = [
    {
      name: "Dashboard",
      icon: LayoutDashboard,
      to: "/",
    },
  ]

  // PLANIFICADOR / ADMIN
  if (planificador || admin) {
    baseItems.push(
      {
        name: "Clientes",
        icon: Users,
        to: "/clientes",
      },
      {
        name: "Modelos",
        icon: Boxes,
        to: "/modelos",
      },
      {
        name: "Piezas",
        icon: Box,
        to: "/piezas",
      },
      {
        name: "Moldes",
        icon: Wrench,
        to: "/moldes",
      },
      {
        name: "Programas",
        icon: ClipboardList,
        to: "/programas",
      },
      {
        name: "Producción",
        icon: Factory,
        to: "/produccion",
      },
      {
        name: "Pedidos",
        icon: ShoppingCart,
        to: "/pedidos",
      },
      {
        name: "Situación",
        icon: Activity,
        to: "/situacion",
      }
    )
  }

  // ENCARGADO / PLANIFICADOR / ADMIN
  if (encargado || planificador || admin) {
    baseItems.push({
      name: "Partes",
      icon: FileText,
      to: "/partes-produccion",
    })
  }

  // ALMACÉN / PLANIFICADOR / ADMIN
  if (almacen || planificador || admin) {
    baseItems.push({
      name: "Movimientos",
      icon: Activity,
      to: "/movimientos",
    })
  }

  // ADMIN
  if (admin) {
    baseItems.push({
      name: "Usuarios",
      icon: ShieldCheck,
      to: "/usuarios",
    })
  }

  return baseItems
})


// RUTA ACTIVA
function isActive(path) {
  return route.path === path
}
</script>

<template>
  <aside :class="[
    collapsed ? 'w-[72px]' : 'w-[255px]',
    'relative flex h-screen shrink-0 flex-col overflow-hidden border-r border-white/70 bg-gradient-to-b from-[#F8FBFD] via-[#EEF7FA] to-[#DFF3F8] shadow-xl shadow-slate-300/30 transition-all duration-300 md:flex',
  ]">
    <!-- FONDO DECORATIVO -->
    <div class="absolute -top-24 left-4 h-64 w-64 rounded-full bg-cyan-400/10 blur-3xl" />

    <!-- CABECERA -->
    <div class="relative z-10 flex h-20 shrink-0 items-center transition-all duration-300 md:h-24"
      :class="collapsed ? 'justify-center px-0' : 'px-5'">
      <!-- LOGO COMPLETO -->
      <img v-if="!collapsed" src="/logo-isavex.png" alt="ISAVEX"
        class="h-16 w-auto -translate-x-2 object-contain drop-shadow-[0_0_14px_rgba(89,199,216,0.25)] md:h-24 md:-translate-x-4" />

      <!-- LOGO COMPACTO -->
      <div v-else
        class="flex h-12 w-12 items-center justify-center rounded-2xl border border-[#59C7D8]/30 bg-white/85 shadow-md shadow-slate-200/60 backdrop-blur-xl"
        title="ISAVEX">
        <img src="/logo-isavex.png" alt="ISAVEX" class="h-8 w-8 object-contain" />
      </div>
    </div>

    <!-- NAVEGACIÓN -->
    <nav class="relative z-10 min-h-0 flex-1 space-y-2 overflow-y-auto px-2 py-3">
      <RouterLink v-for="item in items" :key="item.to" :to="item.to" :title="collapsed ? item.name : ''" :class="[
        isActive(item.to)
          ? 'bg-gradient-to-r from-[#59C7D8] to-[#49B3C2] text-[#081426] shadow-lg shadow-cyan-500/20'
          : 'text-slate-600 hover:bg-white/80 hover:text-[#081426]',

        collapsed
          ? 'mx-auto h-12 w-12 justify-center px-0'
          : 'h-12 justify-start px-4',

        'group flex items-center gap-3 rounded-2xl text-sm transition-all duration-200',
      ]">
        <!-- ICONO -->
        <component :is="item.icon" :class="[
          collapsed ? 'h-5 w-5' : 'h-4 w-4',
          'shrink-0',
        ]" />

        <!-- TEXTO -->
        <span v-if="!collapsed" class="truncate font-medium">
          {{ item.name }}
        </span>
      </RouterLink>
    </nav>

    <!-- FOOTER SIDEBAR -->
    <div class="relative z-10 mt-auto shrink-0 p-3">
      <!-- ABIERTO -->
      <div v-if="!collapsed" class="rounded-2xl border border-[#59C7D8]/25 bg-white/75 p-4 shadow-sm backdrop-blur-xl">
        <div class="mb-2 flex items-center gap-2 text-[10px] font-semibold uppercase tracking-[0.22em] text-[#1597A8]">
          <div class="h-2 w-2 rounded-full bg-cyan-300 shadow-[0_0_12px_#59C7D8]" />

          Sistema activo
        </div>

        <p class="text-xs leading-5 text-slate-600">
          Plataforma de planificación,
          producción y trazabilidad industrial.
        </p>
      </div>

      <!-- CERRADO -->
      <div v-else
        class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl border border-[#59C7D8]/25 bg-white/80 shadow-sm backdrop-blur-xl"
        title="Sistema activo">
        <Activity class="h-5 w-5 text-[#1597A8]" />
      </div>
    </div>
  </aside>
</template>