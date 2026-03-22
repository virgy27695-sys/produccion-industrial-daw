<script setup>
import { computed, onMounted, ref } from "vue"
import { getProgramas } from "../api/programas"

const programas = ref([])
const loading = ref(false)
const error = ref("")
const busqueda = ref("")

const programasFiltrados = computed(() => {
  const texto = busqueda.value.trim().toLowerCase()

  if (!texto) return programas.value

  return programas.value.filter((programa) => {
    const cliente = programa.cliente?.nombre?.toLowerCase() || ""
    const estado = programa.estado?.toLowerCase() || ""
    const observaciones = programa.observaciones?.toLowerCase() || ""
    const id = String(programa.id)

    return (
      id.includes(texto) ||
      cliente.includes(texto) ||
      estado.includes(texto) ||
      observaciones.includes(texto)
    )
  })
})

async function loadProgramas() {
  loading.value = true
  error.value = ""

  try {
    programas.value = await getProgramas()
  } catch (e) {
    error.value = "No se pudieron cargar los programas."
    console.error(e)
  } finally {
    loading.value = false
  }
}

onMounted(loadProgramas)
</script>

<template>
  <section class="space-y-6">
    <div>
      <h2 class="mb-4 text-2xl font-semibold text-slate-800">Programas</h2>
      <p class="text-slate-600">
        Consulta de programas de necesidades enviados por los clientes.
      </p>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
      <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h3 class="text-lg font-semibold text-slate-800">
          Listado de programas
        </h3>

        <input
          v-model="busqueda"
          type="text"
          placeholder="Buscar por ID, cliente, estado u observaciones"
          class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200 sm:max-w-sm"
        />
      </div>

      <p v-if="error" class="mb-4 text-sm text-red-600">
        {{ error }}
      </p>

      <p v-if="loading" class="text-sm text-slate-500">
        Cargando programas...
      </p>

      <div v-else class="overflow-x-auto">
        <table class="min-w-full border-separate border-spacing-y-2">
          <thead>
            <tr class="text-left text-sm text-slate-500">
              <th class="px-4 py-2">ID</th>
              <th class="px-4 py-2">Cliente</th>
              <th class="px-4 py-2">Solicitud</th>
              <th class="px-4 py-2">Entrega</th>
              <th class="px-4 py-2">Estado</th>
              <th class="px-4 py-2">Observaciones</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="programa in programasFiltrados"
              :key="programa.id"
              class="bg-slate-50 text-slate-800"
            >
              <td class="rounded-l-xl px-4 py-3 font-medium">
                #{{ programa.id }}
              </td>

              <td class="px-4 py-3">
                {{ programa.cliente?.nombre || "Sin cliente" }}
              </td>

              <td class="px-4 py-3">
                {{ programa.fecha_solicitud || "—" }}
              </td>

              <td class="px-4 py-3">
                {{ programa.fecha_entrega || "—" }}
              </td>

              <td class="px-4 py-3">
                <span
                  class="rounded-full px-3 py-1 text-xs font-medium"
                  :class="{
                    'bg-amber-100 text-amber-700': programa.estado === 'pendiente',
                    'bg-green-100 text-green-700': programa.estado === 'aprobado',
                    'bg-red-100 text-red-700': programa.estado === 'rechazado',
                  }"
                >
                  {{ programa.estado }}
                </span>
              </td>

              <td class="rounded-r-xl px-4 py-3">
                {{ programa.observaciones || "—" }}
              </td>
            </tr>

            <tr v-if="programasFiltrados.length === 0">
              <td colspan="6" class="px-4 py-6 text-center text-sm text-slate-500">
                No hay programas registrados.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</template>