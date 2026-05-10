<script setup>
import { ref, onMounted, computed } from "vue"
import { useHead } from "@vueuse/head"
import {
  PackageCheck,
  Clock,
  Factory,
  Truck,
  CheckCircle2,
  Activity,
  ShieldCheck,
} from "lucide-vue-next"

import { getPedidos } from "../api/pedidos"
import { isAdmin } from "../utils/auth"

useHead({
  title: "Dashboard · ISAVEX",
})

const admin = isAdmin()

const pedidos = ref([])
const loading = ref(false)

async function loadData() {
  loading.value = true

  try {
    pedidos.value = await getPedidos()
  } catch (error) {
    console.error(error)
  } finally {
    loading.value = false
  }
}

const totalPedidos = computed(() => pedidos.value.length)

const pendientes = computed(() =>
  pedidos.value.filter((p) => p.estado === "pendiente").length
)

const produccion = computed(() =>
  pedidos.value.filter((p) => p.estado === "produccion").length
)

const enviados = computed(() =>
  pedidos.value.filter((p) => p.estado === "enviado").length
)

const entregados = computed(() =>
  pedidos.value.filter((p) => p.estado === "entregado").length
)

const ultimosPedidos = computed(() =>
  [...pedidos.value].slice(0, 6)
)

const porcentajeEntregados = computed(() => {
  if (totalPedidos.value === 0) return 0

  return Math.round((entregados.value / totalPedidos.value) * 100)
})

const cards = computed(() => [
  {
    label: "Total pedidos",
    value: totalPedidos.value,
    icon: PackageCheck,
    detail: "Registros totales",
    tone: "slate",
  },
  {
    label: "Pendientes",
    value: pendientes.value,
    icon: Clock,
    detail: "Por iniciar",
    tone: "amber",
  },
  {
    label: "Producción",
    value: produccion.value,
    icon: Factory,
    detail: "En curso",
    tone: "cyan",
  },
  {
    label: "Enviados",
    value: enviados.value,
    icon: Truck,
    detail: "En tránsito",
    tone: "violet",
  },
  {
    label: "Entregados",
    value: entregados.value,
    icon: CheckCircle2,
    detail: "Finalizados",
    tone: "green",
  },
])

function estadoClass(estado) {
  return {
    "bg-amber-100 text-amber-700 ring-amber-200": estado === "pendiente",
    "bg-cyan-100 text-cyan-700 ring-cyan-200": estado === "produccion",
    "bg-violet-100 text-violet-700 ring-violet-200": estado === "enviado",
    "bg-green-100 text-green-700 ring-green-200": estado === "entregado",
  }
}

function cardIconClass(tone) {
  return {
    "bg-slate-100 text-slate-700": tone === "slate",
    "bg-amber-100 text-amber-700": tone === "amber",
    "bg-cyan-100 text-cyan-700": tone === "cyan",
    "bg-violet-100 text-violet-700": tone === "violet",
    "bg-green-100 text-green-700": tone === "green",
  }
}

onMounted(loadData)
</script>

<template>
  <section class="space-y-8">
    <!-- CABECERA -->
    <div
      class="relative overflow-hidden rounded-[2rem] border border-white/70 bg-white/75 p-6 shadow-xl shadow-slate-300/40 backdrop-blur-xl md:p-8">
      <div class="absolute -right-16 -top-16 h-60 w-60 rounded-full bg-[#59C7D8]/20 blur-3xl"></div>

      <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
        <div>
          <div
            class="mb-3 inline-flex rounded-full border border-[#59C7D8]/40 bg-white/70 px-4 py-2 text-xs font-semibold uppercase tracking-[0.25em] text-[#1597A8]">
            Panel industrial
          </div>

          <h1 class="text-3xl font-bold text-[#081426] md:text-4xl">
            Dashboard de producción
          </h1>

          <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600 md:text-base">
            Resumen operativo de pedidos, estados productivos y actividad reciente del sistema ISAVEX.
          </p>
        </div>

        <div class="grid gap-3 sm:grid-cols-2 lg:min-w-[360px]">
          <div class="rounded-3xl border border-[#59C7D8]/30 bg-white/80 p-5 shadow-sm backdrop-blur">
            <div class="flex items-center gap-3">
              <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#59C7D8]/20 text-[#1597A8]">
                <Activity class="h-6 w-6" />
              </div>

              <div>
                <p class="text-xs text-slate-500">Rendimiento</p>
                <p class="text-2xl font-bold text-[#081426]">
                  {{ porcentajeEntregados }}%
                </p>
              </div>
            </div>
          </div>

          <div class="rounded-3xl border border-[#59C7D8]/30 bg-white/80 p-5 shadow-sm backdrop-blur">
            <div class="flex items-center gap-3">
              <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-green-100 text-green-700">
                <ShieldCheck class="h-6 w-6" />
              </div>

              <div>
                <p class="text-xs text-slate-500">Rol actual</p>
                <p class="text-lg font-bold text-[#081426]">
                  {{ admin ? "Administrador" : "Usuario" }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- MÉTRICAS -->
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
      <article v-for="card in cards" :key="card.label"
        class="group rounded-[1.75rem] border border-white/70 bg-white/80 p-5 shadow-lg shadow-slate-300/30 backdrop-blur-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
        <div class="mb-5 flex items-center justify-between">
          <div class="flex h-12 w-12 items-center justify-center rounded-2xl" :class="cardIconClass(card.tone)">
            <component :is="card.icon" class="h-6 w-6" />
          </div>

          <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-500">
            {{ card.detail }}
          </span>
        </div>

        <p class="text-sm font-medium text-slate-500">
          {{ card.label }}
        </p>

        <p class="mt-2 text-3xl font-bold text-[#081426]">
          {{ card.value }}
        </p>
      </article>
    </div>

    <!-- CONTENIDO INFERIOR -->
    <div class="grid gap-6 xl:grid-cols-[1.5fr_0.8fr]">
      <!-- ÚLTIMOS PEDIDOS -->
      <div class="rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-xl shadow-slate-300/40 backdrop-blur-xl">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h2 class="text-xl font-bold text-[#081426]">
              Últimos pedidos
            </h2>

            <p class="text-sm text-slate-500">
              Seguimiento reciente de pedidos registrados.
            </p>
          </div>

          <RouterLink to="/pedidos"
            class="rounded-2xl border border-[#59C7D8]/40 bg-white/70 px-4 py-2 text-sm font-semibold text-[#081426] shadow-sm transition hover:bg-[#59C7D8]/15">
            Ver pedidos
          </RouterLink>
        </div>

        <p v-if="loading" class="rounded-2xl bg-slate-50 p-5 text-sm text-slate-500">
          Cargando datos...
        </p>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full border-separate border-spacing-y-3 text-sm">
            <thead>
              <tr class="text-left text-xs uppercase tracking-[0.18em] text-slate-400">
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Cliente</th>
                <th class="px-4 py-2">Estado</th>
                <th class="px-4 py-2">Fecha</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="pedido in ultimosPedidos" :key="pedido.id" class="bg-white/80 text-slate-700 shadow-sm">
                <td class="rounded-l-2xl px-4 py-4 font-bold text-[#081426]">
                  #{{ pedido.id }}
                </td>

                <td class="px-4 py-4">
                  {{ pedido.programa?.cliente?.nombre || "—" }}
                </td>

                <td class="px-4 py-4">
                  <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold ring-1"
                    :class="estadoClass(pedido.estado)">
                    {{ pedido.estado }}
                  </span>
                </td>

                <td class="rounded-r-2xl px-4 py-4 text-slate-500">
                  {{ pedido.fecha_pedido || "—" }}
                </td>
              </tr>

              <tr v-if="ultimosPedidos.length === 0">
                <td colspan="4" class="rounded-2xl bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                  No hay pedidos registrados.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- PANEL LATERAL -->
      <aside class="space-y-6">
        <div
          class="rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-xl shadow-slate-300/40 backdrop-blur-xl">
          <h2 class="text-lg font-bold text-[#081426]">
            Estado operativo
          </h2>

          <div class="mt-5 space-y-4">
            <div class="flex items-center justify-between rounded-2xl bg-green-50 px-4 py-3">
              <span class="text-sm font-medium text-green-700">Entregados</span>
              <span class="font-bold text-green-700">{{ entregados }}</span>
            </div>

            <div class="flex items-center justify-between rounded-2xl bg-cyan-50 px-4 py-3">
              <span class="text-sm font-medium text-cyan-700">En producción</span>
              <span class="font-bold text-cyan-700">{{ produccion }}</span>
            </div>

            <div class="flex items-center justify-between rounded-2xl bg-amber-50 px-4 py-3">
              <span class="text-sm font-medium text-amber-700">Pendientes</span>
              <span class="font-bold text-amber-700">{{ pendientes }}</span>
            </div>
          </div>
        </div>

        <div
          class="rounded-[2rem] bg-gradient-to-br from-[#081426] via-[#10233D] to-[#163047] p-6 text-white shadow-xl">
          <p class="text-sm text-[#A8B6C6]">
            ISAVEX
          </p>

          <h3 class="mt-2 text-2xl font-bold">
            Producción inteligente
          </h3>

          <p class="mt-4 text-sm leading-7 text-[#A8B6C6]">
            Centraliza pedidos, planificación, fabricación y trazabilidad desde una única plataforma.
          </p>
        </div>
      </aside>
    </div>
  </section>
</template>