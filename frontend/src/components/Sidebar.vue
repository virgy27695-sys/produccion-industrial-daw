<template>
  <aside class="w-64 bg-slate-900 text-slate-200 min-h-screen p-5">

    <h2 class="text-xl font-semibold mb-8">
      Producción
    </h2>

    <nav class="flex flex-col gap-4 text-sm">

      <RouterLink to="/" class="hover:text-blue-400">
        Dashboard
      </RouterLink>

      <RouterLink to="/clientes" class="hover:text-blue-400">
        Clientes
      </RouterLink>

      <RouterLink to="/modelos" class="hover:text-blue-400">
        Modelos
      </RouterLink>

      <RouterLink to="/piezas" class="hover:text-blue-400">
        Piezas
      </RouterLink>

      <RouterLink to="/programas" class="hover:text-blue-400">
        Programas
      </RouterLink>

      <RouterLink to="/pedidos" class="hover:text-blue-400">
        Pedidos
      </RouterLink>

    </nav>

  </aside>
</template>
