<script setup>
// IMPORTS
import { computed } from "vue"
import { useRoute, useRouter } from "vue-router"

import {
    Bell,
    Search,
    CalendarDays,
    Menu,
} from "lucide-vue-next"

import {
    getCurrentUser,
    logout,
} from "../utils/auth"


// PROPS
defineProps({
    sidebarCollapsed: Boolean,
})


// EVENTOS
const emit = defineEmits([
    "toggleSidebar",
])


const router = useRouter()
const route = useRoute()

const user = getCurrentUser()


// TÍTULO DE PÁGINA
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
        "/movimientos": "Movimientos",
        "/usuarios": "Usuarios",
    }

    return map[route.path] || "ISAVEX"
})


// FECHA ACTUAL
const today = new Date().toLocaleDateString("es-ES", {
    weekday: "short",
    day: "2-digit",
    month: "short",
})


// CERRAR SESIÓN
function cerrarSesion() {
    logout()

    router.push("/login")
}
</script>

<template>
    <!-- HEADER PRINCIPAL -->
    <header class="sticky top-0 z-30 border-b border-white/70 bg-white/85 shadow-sm backdrop-blur-xl">
        <div class="flex h-16 items-center justify-between gap-3 px-3 md:h-20 md:gap-4 md:px-7">
            <!-- ZONA IZQUIERDA -->
            <div class="flex min-w-0 items-center gap-3 md:gap-4">
                <!-- BOTÓN SIDEBAR -->
                <button @click="emit('toggleSidebar')"
                    class="flex h-10 w-10 items-center justify-center rounded-2xl border border-[#59C7D8]/35 bg-white/90 text-[#081426] shadow-md shadow-slate-200/70 transition hover:-translate-y-0.5 hover:bg-[#59C7D8]/10 md:h-11 md:w-11"
                    title="Abrir/cerrar menú">
                    <Menu class="h-5 w-5" />
                </button>

                <!-- TÍTULO -->
                <div class="min-w-0">
                    <p
                        class="hidden text-xs font-semibold uppercase leading-4 tracking-[0.24em] text-[#1597A8] md:block">
                        Panel ISAVEX
                    </p>

                    <h1 class="truncate text-lg font-bold leading-6 tracking-tight text-[#081426] md:text-xl">
                        {{ pageTitle }}
                    </h1>
                </div>

                <!-- BUSCADOR GLOBAL -->
                <div
                    class="hidden items-center gap-3 rounded-2xl border border-slate-200/80 bg-white/90 px-4 py-3 shadow-md shadow-slate-200/60 backdrop-blur lg:flex">
                    <Search class="h-4 w-4 text-slate-400" />

                    <input type="text" placeholder="Buscar pieza, molde, pedido..."
                        class="w-80 bg-transparent text-sm text-slate-700 outline-none placeholder:text-slate-400" />
                </div>
            </div>

            <!-- ZONA DERECHA -->
            <div class="flex shrink-0 items-center gap-2 md:gap-3">
                <!-- FECHA -->
                <div
                    class="hidden items-center gap-2 rounded-2xl border border-slate-200/80 bg-white/90 px-4 py-3 text-sm text-slate-600 shadow-md shadow-slate-200/60 md:flex">
                    <CalendarDays class="h-4 w-4 text-[#1597A8]" />

                    <span class="capitalize">
                        {{ today }}
                    </span>
                </div>

                <!-- NOTIFICACIONES -->
                <button
                    class="relative flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-200/80 bg-white/90 text-slate-600 shadow-md shadow-slate-200/60 transition hover:-translate-y-0.5 hover:bg-white md:h-11 md:w-11"
                    title="Notificaciones">
                    <Bell class="h-5 w-5" />

                    <span
                        class="absolute right-2.5 top-2.5 h-2.5 w-2.5 rounded-full bg-[#59C7D8] shadow-[0_0_10px_rgba(89,199,216,0.9)]" />
                </button>

                <!-- USUARIO -->
                <div
                    class="flex items-center gap-2 rounded-2xl border border-slate-200/80 bg-white/90 px-2 py-2 shadow-md shadow-slate-200/60 backdrop-blur-xl md:px-3 md:py-2.5">
                    <!-- AVATAR -->
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-2xl bg-[#081426] text-sm font-bold text-white shadow-sm md:h-11 md:w-11">
                        {{ user?.name?.charAt(0) || "U" }}
                    </div>

                    <!-- DATOS -->
                    <div class="hidden text-left sm:block">
                        <p class="text-sm font-semibold leading-5 text-slate-800">
                            {{ user?.name || "Usuario" }}
                        </p>

                        <p class="text-xs capitalize leading-4 text-slate-500">
                            {{ user?.role || "sin rol" }}
                        </p>
                    </div>

                    <!-- SALIR -->
                    <button @click="cerrarSesion"
                        class="hidden rounded-xl bg-[#081426] px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-[#10233D] sm:block">
                        Salir
                    </button>
                </div>
            </div>
        </div>
    </header>
</template>