<template>
    <!--
        FOOTER GLOBAL REUTILIZABLE

        Este componente se usa dentro de MainLayout.
        Así evitamos duplicar footers en cada vista y mantenemos
        una única fuente de estilos para toda la aplicación.
    -->
    <footer class="border-t border-white/70 bg-white/85 shadow-[0_-8px_30px_rgba(15,23,42,0.04)] backdrop-blur-xl">
        <div
            class="mx-auto flex max-w-7xl flex-col gap-5 px-5 py-5 text-sm md:flex-row md:items-center md:justify-between">
            <!-- BLOQUE IZQUIERDO: IDENTIDAD -->
            <div class="flex items-center gap-4">
                <!-- LOGO -->
                <div class="flex h-16 w-[190px] items-center overflow-hidden">
                    <img src="/logo-isavex.png" alt="ISAVEX"
                        class="h-32 w-auto -translate-x-7 object-contain drop-shadow-[0_0_14px_rgba(89,199,216,0.2)]" />
                </div>

                <!-- TEXTO -->
                <div>
                    <p class="text-sm font-bold text-[#081426]">
                        ISAVEX
                    </p>

                    <p class="max-w-sm text-xs leading-5 text-slate-500">
                        Gestión inteligente de planificación, producción y trazabilidad industrial.
                    </p>
                </div>
            </div>

            <!-- BLOQUE CENTRAL: MÓDULOS -->
            <div class="hidden items-center gap-5 text-sm font-medium text-slate-500 lg:flex">
                <span class="transition hover:text-[#1597A8]">Producción</span>
                <span class="transition hover:text-[#1597A8]">Planning</span>
                <span class="transition hover:text-[#1597A8]">Trazabilidad</span>
                <span class="transition hover:text-[#1597A8]">Automoción</span>
            </div>

            <!-- BLOQUE DERECHO: COPYRIGHT -->
            <div class="text-xs text-slate-400">
                © 2026 ISAVEX · Software industrial
            </div>
        </div>
    </footer>
</template>