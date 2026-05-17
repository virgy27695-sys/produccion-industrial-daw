<template>
    <!-- FOOTER GLOBAL REUTILIZABLE -->
    <footer class="border-t border-white/70 bg-white/90 shadow-[0_-8px_30px_rgba(15,23,42,0.04)] backdrop-blur-xl">
        <div
            class="mx-auto flex max-w-7xl flex-col items-center gap-2 px-4 py-3 text-center md:flex-row md:items-center md:justify-between md:gap-5 md:px-5 md:py-5 md:text-left">

            <!-- BLOQUE IZQUIERDO -->
            <div class="flex flex-col items-center gap-2 md:flex-row md:gap-4">
                <!-- LOGO -->
                <div class="flex h-9 w-[120px] items-center justify-center overflow-hidden md:h-14 md:w-[170px]">
                    <img src="/logo-isavex.png" alt="ISAVEX"
                        class="h-24 w-auto -translate-x-4 object-contain drop-shadow-[0_0_14px_rgba(89,199,216,0.18)] md:h-32 md:-translate-x-6" />
                </div>

                <!-- TEXTO -->
                <div class="hidden sm:block">
                    <p class="text-xs font-bold text-[#081426] md:text-sm">
                        ISAVEX
                    </p>

                    <p class="max-w-xs text-[11px] leading-4 text-slate-500 md:max-w-sm md:text-xs md:leading-5">
                        Gestión inteligente de planificación, producción y trazabilidad industrial.
                    </p>
                </div>
            </div>

            <!-- BLOQUE CENTRAL -->
            <div class="hidden items-center gap-5 text-sm font-medium text-slate-500 lg:flex">
                <span class="transition hover:text-[#1597A8]">Producción</span>
                <span class="transition hover:text-[#1597A8]">Planning</span>
                <span class="transition hover:text-[#1597A8]">Trazabilidad</span>
                <span class="transition hover:text-[#1597A8]">Automoción</span>
            </div>

            <!-- BLOQUE DERECHO -->
            <div class="text-[10px] leading-4 text-slate-400 md:text-xs">
                © 2026 ISAVEX · Software industrial
            </div>
        </div>
    </footer>
</template>