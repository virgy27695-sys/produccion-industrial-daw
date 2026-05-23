<script setup>
import { ref, onMounted, computed } from "vue"
import { useHead } from "@vueuse/head"
import {
  Factory,
  AlertTriangle,
  Activity,
  Wrench,
  Truck,
  ClipboardList,
} from "lucide-vue-next"

import { getDashboardResumen } from "../api/dashboard"

useHead({
  title: "Dashboard · ISAVEX",
})

const loading = ref(false)
const dashboard = ref({
  kpis: {},
  ultimos_partes: [],
  ultimos_pedidos: [],
  ultimas_entregas: [],
})

async function loadData() {
  loading.value = true

  try {
    dashboard.value = await getDashboardResumen()
  } catch (error) {
    console.error(error)
  } finally {
    loading.value = false
  }
}

const cards = computed(() => [
  {
    label: "Fabricadas hoy",
    value: dashboard.value.kpis.fabricadas_hoy || 0,
    icon: Factory,
  },
  {
    label: "Rechazadas",
    value: dashboard.value.kpis.rechazadas_hoy || 0,
    icon: AlertTriangle,
  },
  {
    label: "Eficiencia",
    value: `${dashboard.value.kpis.eficiencia || 0}%`,
    icon: Activity,
  },
  {
    label: "Moldes activos",
    value: dashboard.value.kpis.moldes_activos || 0,
    icon: Wrench,
  },
  {
    label: "Entregas pendientes",
    value: dashboard.value.kpis.entregas_pendientes || 0,
    icon: Truck,
  },
  {
    label: "Partes pendientes",
    value: dashboard.value.kpis.partes_pendientes || 0,
    icon: ClipboardList,
  },
])

onMounted(loadData)
</script>

<template>
  <section class="space-y-8">

    <div class="rounded-[2rem] border border-white/70 bg-white/80 p-8 shadow-xl">

      <h1 class="text-3xl font-bold text-[#081426]">
        Dashboard industrial
      </h1>

      <p class="mt-2 text-slate-500">
        Resumen operativo en tiempo real
      </p>

    </div>

    <div class="grid gap-4 md:grid-cols-3 xl:grid-cols-6">

      <article v-for="card in cards" :key="card.label"
        class="rounded-[1.75rem] border border-white/70 bg-white/80 p-5 shadow-lg">

        <div class="flex justify-between">

          <div>
            <p class="text-sm text-slate-500">
              {{ card.label }}
            </p>

            <p class="mt-2 text-3xl font-bold text-[#081426]">
              {{ card.value }}
            </p>
          </div>

          <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan-100 text-cyan-700">

            <component :is="card.icon" class="h-6 w-6" />

          </div>

        </div>

      </article>

    </div>


    <div class="rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-xl">

      <h2 class="mb-6 text-xl font-bold text-[#081426]">
        Últimos partes
      </h2>

      <div v-if="loading" class="text-slate-500">

        Cargando...
      </div>

      <table v-else class="w-full text-sm">

        <thead>

          <tr class="text-left text-slate-400">
            <th>Fecha</th>
            <th>Pieza</th>
            <th>Molde</th>
            <th>Estado</th>
          </tr>

        </thead>

        <tbody>

          <tr v-for="parte in dashboard.ultimos_partes" :key="parte.id">

            <td class="py-3">
              {{ parte.fecha }}
            </td>

            <td>
              {{ parte.pieza?.codigo }}
            </td>

            <td>
              {{ parte.molde?.codigo }}
            </td>

            <td>
              {{ parte.estado }}
            </td>

          </tr>

        </tbody>

      </table>

    </div>

  </section>
</template>