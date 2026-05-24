<template>
    <!--
        CONTENEDOR VISUAL REUTILIZABLE

        Uso:
        - tarjetas
        - tablas
        - bloques visuales
        - formularios
        - paneles

        Ventajas:
        - diseño unificado
        - reutilización
        - mantenimiento sencillo
        - consistencia visual
    -->
    <div class="rounded-3xl border border-white/70 bg-white/80 shadow-lg shadow-slate-300/25 backdrop-blur-xl">
        <!-- CONTENIDO INYECTADO -->
        <slot />
    </div>
</template>
