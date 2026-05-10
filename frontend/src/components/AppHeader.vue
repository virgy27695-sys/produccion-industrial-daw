<script setup>
import { computed } from "vue"
import { useRoute, useRouter } from "vue-router"
import { Bell, Search, CalendarDays, Menu } from "lucide-vue-next"
import { getCurrentUser, logout } from "../utils/auth"

defineProps({
    sidebarCollapsed: Boolean,
})

const emit = defineEmits(["toggleSidebar"])

const router = useRouter()
const route = useRoute()
const user = getCurrentUser()

const pageTitle = computed(() => {
    const map = {
        "/": "Dashboard",
        "/clientes": "Clientes",
        "/modelos": "Modelos",
        "/piezas": "Piezas",
        "/moldes": "Moldes",
        "/programas": "Programas",
        "/produccion": "Producción",
        "/pedidos": "Pedidos",
        "/situacion": "Situación",
    }

    return map[route.path] || "ISAVEX"
})

const today = new Date().toLocaleDateString("es-ES", {
    weekday: "short",
    day: "2-digit",
    month: "short",
})

function cerrarSesion() {
    logout()
    router.push("/login")
}
</script>

<template>
    <header class="sticky top-0 z-30 border-b border-white/60 bg-white/70 backdrop-blur-xl">
        <div class="flex h-16 items-center justify-between gap-4 px-4 md:px-6">
            <!-- IZQUIERDA -->
            <div class="flex min-w-0 items-center gap-4">
                <button @click="emit('toggleSidebar')"
                    class="flex h-10 w-10 items-center justify-center rounded-2xl border border-[#59C7D8]/30 bg-white/80 text-[#081426] shadow-sm transition hover:bg-[#59C7D8]/10"
                    title="Abrir/cerrar menú">
                    <Menu class="h-5 w-5" />
                </button>

                <div class="hidden md:block">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#1597A8]">
                        Panel ISAVEX
                    </p>
                    <h1 class="text-lg font-bold text-[#081426]">
                        {{ pageTitle }}
                    </h1>
                </div>

                <div
                    class="hidden items-center gap-3 rounded-2xl border border-slate-200/70 bg-white/80 px-4 py-2.5 shadow-sm backdrop-blur lg:flex">
                    <Search class="h-4 w-4 text-slate-400" />

                    <input type="text" placeholder="Buscar pieza, molde, pedido..."
                        class="w-72 bg-transparent text-sm text-slate-700 outline-none placeholder:text-slate-400" />
                </div>
            </div>

            <!-- DERECHA -->
            <div class="flex shrink-0 items-center gap-3">
                <div
                    class="hidden items-center gap-2 rounded-2xl border border-slate-200/70 bg-white/80 px-4 py-2.5 text-sm text-slate-600 shadow-sm md:flex">
                    <CalendarDays class="h-4 w-4 text-[#1597A8]" />
                    <span class="capitalize">{{ today }}</span>
                </div>

                <button
                    class="relative flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-200/70 bg-white/80 text-slate-600 shadow-sm transition hover:bg-white"
                    title="Notificaciones">
                    <Bell class="h-5 w-5" />
                    <span class="absolute right-2 top-2 h-2 w-2 rounded-full bg-[#59C7D8]" />
                </button>

                <div
                    class="flex items-center gap-3 rounded-2xl border border-slate-200/70 bg-white/80 px-2.5 py-2 shadow-sm backdrop-blur">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-2xl bg-[#081426] text-sm font-bold text-white">
                        {{ user?.name?.charAt(0) || "U" }}
                    </div>

                    <div class="hidden text-left sm:block">
                        <p class="text-sm font-semibold leading-4 text-slate-800">
                            {{ user?.name || "Usuario" }}
                        </p>
                        <p class="text-xs text-slate-500">
                            {{ user?.role || "sin rol" }}
                        </p>
                    </div>

                    <button @click="cerrarSesion"
                        class="rounded-xl bg-[#081426] px-3.5 py-2 text-sm font-medium text-white transition hover:bg-[#10233D]">
                        Salir
                    </button>
                </div>
            </div>
        </div>
    </header>
</template>