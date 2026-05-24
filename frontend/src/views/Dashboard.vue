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
  Timer,
  Gauge,
  CheckCircle2,
  XCircle,
} from "lucide-vue-next"

import { getDashboardResumen } from "../api/dashboard"

useHead({
  title: "Dashboard · ISAVEX",
})

const loading = ref(false)

const dashboard = ref({
  kpis: {},
  produccion_por_molde: [],
  produccion_por_turno: [],
  ultimos_partes: [],
  ultimos_pedidos: [],
  ultimas_entregas: [],
})

async function loadData() {
  loading.value = true

  try {
    const data = await getDashboardResumen()

    dashboard.value = {
      kpis: data?.kpis || {},
      produccion_por_molde: data?.produccion_por_molde || [],
      produccion_por_turno: data?.produccion_por_turno || [],
      ultimos_partes: data?.ultimos_partes || [],
      ultimos_pedidos: data?.ultimos_pedidos || [],
      ultimas_entregas: data?.ultimas_entregas || [],
    }
  } catch (error) {

    dashboard.value = {
      kpis: {},
      produccion_por_molde: [],
      produccion_por_turno: [],
      ultimos_partes: [],
      ultimos_pedidos: [],
      ultimas_entregas: [],
    }
  } finally {
    loading.value = false
  }
}

const cards = computed(() => [
  {
    label: "Fabricadas hoy",
    value: dashboard.value.kpis.fabricadas_hoy || 0,
    icon: Factory,
    tone: "cyan",
  },
  {
    label: "Rechazadas",
    value: dashboard.value.kpis.rechazadas_hoy || 0,
    icon: AlertTriangle,
    tone: "red",
  },
  {
    label: "Eficiencia",
    value: `${dashboard.value.kpis.eficiencia || 0}%`,
    icon: Gauge,
    tone: "green",
  },
  {
    label: "Moldes activos",
    value: dashboard.value.kpis.moldes_activos || 0,
    icon: Wrench,
    tone: "slate",
  },
  {
    label: "Entregas",
    value: dashboard.value.kpis.entregas_pendientes || 0,
    icon: Truck,
    tone: "violet",
  },
  {
    label: "Partes pendientes",
    value: dashboard.value.kpis.partes_pendientes || 0,
    icon: ClipboardList,
    tone: "amber",
  },
  {
    label: "Paros hoy",
    value: `${dashboard.value.kpis.paros_hoy_minutos || 0} min`,
    icon: Timer,
    tone: "orange",
  },
])

const estadoIndustrial = computed(() => {
  const estado = dashboard.value.kpis.estado_industrial || "correcto"

  if (estado === "critico") {
    return {
      label: "Crítico",
      text: "La producción presenta incidencias relevantes.",
      icon: XCircle,
      class: "bg-red-50 text-red-700 border-red-200",
      dot: "bg-red-500",
    }
  }

  if (estado === "riesgo") {
    return {
      label: "Riesgo",
      text: "Conviene revisar rechazos, paros o eficiencia.",
      icon: AlertTriangle,
      class: "bg-amber-50 text-amber-700 border-amber-200",
      dot: "bg-amber-500",
    }
  }

  return {
    label: "Correcto",
    text: "La producción se encuentra dentro de parámetros aceptables.",
    icon: CheckCircle2,
    class: "bg-green-50 text-green-700 border-green-200",
    dot: "bg-green-500",
  }
})

const maxMolde = computed(() => {
  const valores = dashboard.value.produccion_por_molde.map(
    (item) => item.fabricadas || 0
  )

  return Math.max(...valores, 1)
})

const maxTurno = computed(() => {
  const valores = dashboard.value.produccion_por_turno.map(
    (item) => item.fabricadas || 0
  )

  return Math.max(...valores, 1)
})

function percent(value, max) {
  return Math.round(((value || 0) / max) * 100)
}

function cardIconClass(tone) {
  return {
    "bg-cyan-100 text-cyan-700": tone === "cyan",
    "bg-red-100 text-red-700": tone === "red",
    "bg-green-100 text-green-700": tone === "green",
    "bg-slate-100 text-slate-700": tone === "slate",
    "bg-violet-100 text-violet-700": tone === "violet",
    "bg-amber-100 text-amber-700": tone === "amber",
    "bg-orange-100 text-orange-700": tone === "orange",
  }
}

function estadoBadgeClass(estado) {
  return {
    "bg-cyan-100 text-cyan-700": estado === "pendiente",
    "bg-green-100 text-green-700": estado === "validado",
    "bg-orange-100 text-orange-700": estado === "corregido",
  }
}

onMounted(loadData)
</script>

<template>
  <section class="space-y-8">
    <!-- HERO -->
    <div
      class="relative overflow-hidden rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-xl shadow-slate-300/40 backdrop-blur-xl md:p-8">
      <div class="absolute -right-16 -top-16 h-60 w-60 rounded-full bg-[#59C7D8]/20 blur-3xl" />

      <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
        <div>
          <div
            class="mb-3 inline-flex rounded-full border border-[#59C7D8]/40 bg-white/70 px-4 py-2 text-xs font-semibold uppercase tracking-[0.25em] text-[#1597A8]">
            Panel industrial
          </div>

          <h1 class="text-3xl font-bold text-[#081426] md:text-4xl">
            Dashboard industrial
          </h1>

          <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600 md:text-base">
            Resumen operativo de producción, rechazos, paros, moldes activos y estado industrial.
          </p>
        </div>

        <div class="rounded-3xl border p-5 shadow-sm backdrop-blur-xl" :class="estadoIndustrial.class">
          <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/70">
              <component :is="estadoIndustrial.icon" class="h-6 w-6" />
            </div>

            <div>
              <div class="flex items-center gap-2">
                <span class="h-2.5 w-2.5 rounded-full" :class="estadoIndustrial.dot" />

                <p class="text-xs font-semibold uppercase tracking-[0.2em]">
                  Estado industrial
                </p>
              </div>

              <p class="mt-1 text-2xl font-black">
                {{ estadoIndustrial.label }}
              </p>

              <p class="mt-1 text-sm opacity-80">
                {{ estadoIndustrial.text }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- KPIS -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7">
      <article v-for="card in cards" :key="card.label"
        class="rounded-[1.75rem] border border-white/70 bg-white/80 p-5 shadow-lg shadow-slate-300/30 backdrop-blur-xl">
        <div class="mb-4 flex items-center justify-between">
          <div class="flex h-12 w-12 items-center justify-center rounded-2xl" :class="cardIconClass(card.tone)">
            <component :is="card.icon" class="h-6 w-6" />
          </div>
        </div>

        <p class="text-sm font-medium text-slate-500">
          {{ card.label }}
        </p>

        <p class="mt-2 text-3xl font-black text-[#081426]">
          {{ card.value }}
        </p>
      </article>
    </div>

    <!-- GRÁFICAS SIMPLES -->
    <div class="grid gap-6 xl:grid-cols-2">
      <!-- PRODUCCIÓN POR MOLDE -->
      <div class="rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-xl shadow-slate-300/40 backdrop-blur-xl">
        <div class="mb-6">
          <h2 class="text-xl font-bold text-[#081426]">
            Producción por molde
          </h2>

          <p class="text-sm text-slate-500">
            Comparativa de piezas fabricadas, buenas, rechazadas y paros.
          </p>
        </div>

        <div v-if="loading" class="text-sm text-slate-500">
          Cargando datos...
        </div>

        <div v-else-if="!dashboard.produccion_por_molde.length"
          class="rounded-2xl bg-slate-50 p-6 text-sm text-slate-500">
          No hay producción por molde.
        </div>

        <div v-else class="space-y-5">
          <div v-for="item in dashboard.produccion_por_molde" :key="item.molde" class="space-y-2">
            <div class="flex items-center justify-between gap-4">
              <div>
                <p class="font-bold text-slate-800">
                  {{ item.molde }}
                </p>

                <p class="text-xs text-slate-400">
                  {{ item.descripcion || "Molde de producción" }}
                </p>
              </div>

              <p class="text-sm font-black text-[#081426]">
                {{ item.fabricadas }} uds
              </p>
            </div>

            <div class="h-3 overflow-hidden rounded-full bg-slate-100">
              <div class="h-full rounded-full bg-[#59C7D8]"
                :style="{ width: `${percent(item.fabricadas, maxMolde)}%` }" />
            </div>

            <div class="flex flex-wrap gap-2 text-xs">
              <span class="rounded-full bg-green-50 px-3 py-1 font-semibold text-green-700">
                Buenas: {{ item.buenas }}
              </span>

              <span class="rounded-full bg-red-50 px-3 py-1 font-semibold text-red-700">
                Rechazadas: {{ item.rechazadas }}
              </span>

              <span class="rounded-full bg-orange-50 px-3 py-1 font-semibold text-orange-700">
                Paros: {{ item.paros_minutos }} min
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- PRODUCCIÓN POR TURNO -->
      <div class="rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-xl shadow-slate-300/40 backdrop-blur-xl">
        <div class="mb-6">
          <h2 class="text-xl font-bold text-[#081426]">
            Producción por turno
          </h2>

          <p class="text-sm text-slate-500">
            Distribución de producción por mañana, tarde y noche.
          </p>
        </div>

        <div v-if="loading" class="text-sm text-slate-500">
          Cargando datos...
        </div>

        <div v-else-if="!dashboard.produccion_por_turno.length"
          class="rounded-2xl bg-slate-50 p-6 text-sm text-slate-500">
          No hay producción por turno.
        </div>

        <div v-else class="space-y-5">
          <div v-for="item in dashboard.produccion_por_turno" :key="item.turno"
            class="rounded-2xl border border-slate-100 bg-white/70 p-4">
            <div class="mb-3 flex items-center justify-between">
              <p class="font-bold capitalize text-slate-800">
                {{ item.turno }}
              </p>

              <p class="text-sm font-black text-[#081426]">
                {{ item.fabricadas }} uds
              </p>
            </div>

            <div class="h-3 overflow-hidden rounded-full bg-slate-100">
              <div class="h-full rounded-full bg-[#081426]"
                :style="{ width: `${percent(item.fabricadas, maxTurno)}%` }" />
            </div>

            <div class="mt-3 text-xs font-semibold text-red-600">
              Rechazadas: {{ item.rechazadas }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- TABLAS -->
    <div class="grid gap-6 xl:grid-cols-[1.4fr_0.9fr]">
      <!-- ÚLTIMOS PARTES -->
      <div class="rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-xl shadow-slate-300/40 backdrop-blur-xl">
        <h2 class="mb-6 text-xl font-bold text-[#081426]">
          Últimos partes
        </h2>

        <div v-if="loading" class="text-sm text-slate-500">
          Cargando...
        </div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead>
              <tr class="text-left text-xs uppercase tracking-[0.18em] text-slate-400">
                <th class="px-4 py-3">Fecha</th>
                <th class="px-4 py-3">Pieza</th>
                <th class="px-4 py-3">Molde</th>
                <th class="px-4 py-3">Turno</th>
                <th class="px-4 py-3">Estado</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="parte in dashboard.ultimos_partes" :key="parte.id" class="border-t border-slate-100">
                <td class="px-4 py-3">
                  {{ parte.fecha }}
                </td>

                <td class="px-4 py-3 font-semibold text-slate-800">
                  {{ parte.pieza?.codigo || "-" }}
                </td>

                <td class="px-4 py-3">
                  {{ parte.molde?.codigo || "-" }}
                </td>

                <td class="px-4 py-3 capitalize">
                  {{ parte.turno }}
                </td>

                <td class="px-4 py-3">
                  <span class="rounded-full px-3 py-1 text-xs font-semibold" :class="estadoBadgeClass(parte.estado)">
                    {{ parte.estado }}
                  </span>
                </td>
              </tr>

              <tr v-if="dashboard.ultimos_partes.length === 0">
                <td colspan="5" class="px-4 py-8 text-center text-slate-400">
                  No hay partes registrados.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- ACTIVIDAD -->
      <aside class="space-y-6">
        <div
          class="rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-xl shadow-slate-300/40 backdrop-blur-xl">
          <h2 class="text-xl font-bold text-[#081426]">
            Últimos pedidos
          </h2>

          <div class="mt-4 space-y-3">
            <div v-for="pedido in dashboard.ultimos_pedidos" :key="pedido.id" class="rounded-2xl bg-slate-50 p-4">
              <p class="font-bold text-slate-800">
                Pedido #{{ pedido.id }}
              </p>

              <p class="text-sm text-slate-500">
                {{ pedido.programa?.cliente?.nombre || "Sin cliente" }}
              </p>
            </div>

            <p v-if="dashboard.ultimos_pedidos.length === 0" class="text-sm text-slate-400">
              No hay pedidos recientes.
            </p>
          </div>
        </div>

        <div
          class="rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-xl shadow-slate-300/40 backdrop-blur-xl">
          <h2 class="text-xl font-bold text-[#081426]">
            Últimas entregas
          </h2>

          <div class="mt-4 space-y-3">
            <div v-for="entrega in dashboard.ultimas_entregas" :key="entrega.id" class="rounded-2xl bg-slate-50 p-4">
              <p class="font-bold text-slate-800">
                {{ entrega.pieza?.codigo || "Pieza" }}
              </p>

              <p class="text-sm text-slate-500">
                Cantidad: {{ entrega.cantidad || 0 }}
              </p>
            </div>

            <p v-if="dashboard.ultimas_entregas.length === 0" class="text-sm text-slate-400">
              No hay entregas recientes.
            </p>
          </div>
        </div>
      </aside>
    </div>
  </section>
</template>
