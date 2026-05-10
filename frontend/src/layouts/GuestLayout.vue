<script setup>
import { ref } from "vue"

const showLogin = ref(false)
</script>

<template>
    <div class="flex min-h-screen flex-col bg-gradient-to-br from-[#F8FBFD] via-[#EEF7FA] to-[#DFF3F8]">
        <!-- HEADER -->
        <header class="sticky top-0 z-40 border-b border-white/70 bg-white/80 shadow-sm backdrop-blur-xl">
            <div class="mx-auto flex h-24 w-full max-w-7xl items-center justify-between px-4 sm:h-28 sm:px-6">
                <!-- LOGO GRANDE -->
                <div class="flex h-24 w-[260px] items-center overflow-hidden sm:h-28 sm:w-[360px]">
                    <img src="/logo-isavex.png" alt="ISAVEX"
                        class="h-64 w-auto -translate-x-8 object-contain drop-shadow-[0_0_18px_rgba(89,199,216,0.35)] sm:h-80 sm:-translate-x-10" />
                </div>

                <button v-if="!showLogin" @click="showLogin = true"
                    class="rounded-2xl border border-[#59C7D8]/50 bg-white/80 px-4 py-2 text-sm font-semibold text-[#081426] shadow-sm transition hover:bg-[#59C7D8]/15 sm:rounded-full sm:px-5">
                    Iniciar sesión
                </button>
            </div>
        </header>

        <!-- MAIN -->
        <main class="relative flex flex-1 items-center justify-center overflow-hidden px-4 py-8 sm:py-12">
            <!-- GLOWS -->
            <div class="absolute left-0 top-24 h-72 w-72 rounded-full bg-[#59C7D8]/20 blur-3xl"></div>
            <div class="absolute bottom-10 right-0 h-80 w-80 rounded-full bg-blue-300/20 blur-3xl"></div>

            <!-- LANDING SIEMPRE DETRÁS -->
            <section
                class="relative z-0 mx-auto grid w-full max-w-7xl items-center gap-10 transition-all duration-300 lg:grid-cols-2"
                :class="showLogin ? 'pointer-events-none opacity-25 blur-[1px]' : 'opacity-100'">
                <div>
                    <div
                        class="mb-5 inline-flex rounded-full border border-[#59C7D8]/40 bg-white/80 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.25em] text-[#1597A8] shadow-sm backdrop-blur">
                        Software industrial
                    </div>

                    <h1 class="max-w-3xl text-4xl font-bold leading-tight text-[#081426] sm:text-5xl">
                        Planificación, producción y trazabilidad industrial desde una única plataforma.
                    </h1>

                    <p class="mt-6 max-w-2xl text-base leading-8 text-slate-600 sm:text-lg">
                        ISAVEX ayuda a empresas auxiliares de automoción a sustituir procesos manuales basados en Excel
                        por una solución visual, moderna y centralizada.
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <button v-if="!showLogin" @click="showLogin = true"
                            class="rounded-2xl bg-[#59C7D8] px-6 py-3 font-semibold text-[#081426] shadow-lg shadow-cyan-200/60 transition hover:bg-[#49B3C2]">
                            Acceder al sistema
                        </button>

                        <div
                            class="rounded-2xl border border-white/70 bg-white/80 px-6 py-3 text-center font-medium text-slate-600 shadow-sm backdrop-blur">
                            Producción inteligente
                        </div>
                    </div>
                </div>

                <div
                    class="rounded-[2rem] border border-white/70 bg-white/80 p-5 shadow-2xl shadow-slate-300/50 backdrop-blur-xl sm:p-8">
                    <div class="grid gap-4">
                        <div
                            class="rounded-3xl bg-gradient-to-br from-[#081426] via-[#10233D] to-[#163047] p-6 text-white shadow-xl">
                            <p class="text-sm text-[#A8B6C6]">
                                Planning semanal
                            </p>

                            <p class="mt-2 text-2xl font-bold sm:text-3xl">
                                Control productivo
                            </p>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div class="rounded-2xl border border-[#59C7D8]/30 bg-white/80 p-4 text-center shadow-sm">
                                <p class="text-xs text-slate-500">Piezas</p>
                                <p class="mt-1 text-2xl font-bold text-[#081426]">128</p>
                            </div>

                            <div class="rounded-2xl border border-[#59C7D8]/30 bg-white/80 p-4 text-center shadow-sm">
                                <p class="text-xs text-slate-500">Moldes</p>
                                <p class="mt-1 text-2xl font-bold text-[#081426]">45</p>
                            </div>

                            <div class="rounded-2xl border border-[#59C7D8]/30 bg-white/80 p-4 text-center shadow-sm">
                                <p class="text-xs text-slate-500">Turnos</p>
                                <p class="mt-1 text-2xl font-bold text-[#081426]">3</p>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-[#59C7D8]/30 bg-white/80 p-5 shadow-sm">
                            <p class="text-sm font-semibold text-[#081426]">
                                Semáforo productivo
                            </p>

                            <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
                                <div
                                    class="rounded-xl bg-green-100 px-3 py-2 text-center text-xs font-semibold text-green-700">
                                    OK
                                </div>

                                <div
                                    class="rounded-xl bg-amber-100 px-3 py-2 text-center text-xs font-semibold text-amber-700">
                                    Medio
                                </div>

                                <div
                                    class="rounded-xl bg-red-100 px-3 py-2 text-center text-xs font-semibold text-red-700">
                                    Crítico
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- LOGIN FLOTANTE ENCIMA -->
            <div v-if="showLogin" class="absolute inset-0 z-10 flex items-center justify-center px-4 py-8">
                <div class="w-full max-w-6xl">
                    <router-view />
                </div>
            </div>
        </main>

        <!-- FOOTER -->
        <footer class="mt-auto border-t border-white/70 bg-white/80 text-slate-700 backdrop-blur-xl">
            <div class="mx-auto grid max-w-7xl gap-8 px-6 py-8 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <!-- LOGO GRANDE FOOTER -->
                    <div class="flex h-20 w-[260px] items-center overflow-hidden">
                        <img src="/logo-isavex.png" alt="ISAVEX" class="h-64 w-auto -translate-x-8 object-contain" />
                    </div>

                    <p class="mt-3 text-sm leading-6 text-slate-600">
                        Plataforma digital para la planificación y control de producción industrial.
                    </p>
                </div>

                <div>
                    <h3 class="mb-3 text-xs font-semibold uppercase tracking-[0.25em] text-[#1597A8]">
                        Módulos
                    </h3>

                    <ul class="space-y-2 text-sm text-slate-600">
                        <li>Piezas</li>
                        <li>Moldes</li>
                        <li>Programas</li>
                        <li>Producción</li>
                    </ul>
                </div>

                <div>
                    <h3 class="mb-3 text-xs font-semibold uppercase tracking-[0.25em] text-[#1597A8]">
                        Gestión
                    </h3>

                    <ul class="space-y-2 text-sm text-slate-600">
                        <li>Planning semanal</li>
                        <li>Situación productiva</li>
                        <li>Movimientos</li>
                        <li>Entregas</li>
                    </ul>
                </div>

                <div>
                    <h3 class="mb-3 text-xs font-semibold uppercase tracking-[0.25em] text-[#1597A8]">
                        Sobre ISAVEX
                    </h3>

                    <p class="text-sm leading-6 text-slate-600">
                        Solución orientada a empresas auxiliares de automoción para reducir errores, mejorar la
                        trazabilidad y digitalizar la planificación productiva.
                    </p>
                </div>
            </div>

            <div class="border-t border-slate-200 py-4">
                <p class="text-center text-xs text-slate-500">
                    © 2026 ISAVEX. Todos los derechos reservados.
                </p>
            </div>
        </footer>
    </div>
</template>