<script setup>
import { ref } from "vue"
import { useRoute } from "vue-router"
import {
  LayoutDashboard,
  Users,
  Boxes,
  Package,
  ClipboardList,
  ShoppingCart,
  Menu,
  X,
} from "lucide-vue-next"

const route = useRoute()
const open = ref(false)

const links = [
  { name: "Dashboard", path: "/", icon: LayoutDashboard },
  { name: "Clientes", path: "/clientes", icon: Users },
  { name: "Modelos", path: "/modelos", icon: Boxes },
  { name: "Piezas", path: "/piezas", icon: Package },
  { name: "Programas", path: "/programas", icon: ClipboardList },
  { name: "Pedidos", path: "/pedidos", icon: ShoppingCart },
]

function isActive(path) {
  return route.path === path
}

function closeMenu() {
  open.value = false
}
</script>

<template>
    <!-- Botón móvil -->
    <button
      @click="open = true"
      class="fixed left-4 top-4 z-50 rounded-xl bg-white p-2 text-slate-700 shadow-md ring-1 ring-slate-200 md:hidden"
      aria-label="Abrir menú"
    >
      <Menu class="h-5 w-5" />
    </button>

    <!-- Overlay móvil -->
    <div
      v-if="open"
      class="fixed inset-0 z-40 bg-black/40 md:hidden"
      @click="closeMenu"
    ></div>

    <!-- Sidebar -->
    <aside
      class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col border-r border-slate-200 bg-white p-4 transition-transform duration-300 md:static md:translate-x-0"
      :class="open ? 'translate-x-0' : '-translate-x-full'"
    >
      <div class="mb-8 flex items-center justify-between">
        <h1 class="text-xl font-bold text-slate-800">
          Producción
        </h1>

        <button
          @click="closeMenu"
          class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 md:hidden"
          aria-label="Cerrar menú"
        >
          <X class="h-5 w-5" />
        </button>
      </div>

      <nav class="flex flex-col gap-2">
        <RouterLink
          v-for="link in links"
          :key="link.path"
          :to="link.path"
          @click="closeMenu"
          class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition"
          :class="isActive(link.path)
            ? 'bg-blue-600 text-white'
            : 'text-slate-700 hover:bg-slate-100'"
        >
          <component :is="link.icon" class="h-5 w-5" />
          <span>{{ link.name }}</span>
        </RouterLink>
      </nav>
    </aside>
</template>