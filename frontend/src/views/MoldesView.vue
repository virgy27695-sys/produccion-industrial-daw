<script setup>
import { computed, onMounted, ref } from "vue"
import { getMoldes, createMolde, updateMolde, deleteMolde } from "../api/moldes"
import { isAdmin } from "../utils/auth"

const admin = isAdmin()

const moldes = ref([])
const loading = ref(false)
const saving = ref(false)
const error = ref("")
const search = ref("")
const showForm = ref(false)
const editingId = ref(null)

const form = ref({
  codigo: "",
  descripcion: "",
  cavidades: 1,
  tipo_configuracion: "simple",
  maquina: "",
  tiempo_ciclo_segundos: "",
  stock_seguridad_dias: 0,
  activo: true,
})

const moldesFiltrados = computed(() => {
  const term = search.value.trim().toLowerCase()

  if (!term) return moldes.value

  return moldes.value.filter((molde) => {
    return (
      molde.codigo?.toLowerCase().includes(term) ||
      molde.descripcion?.toLowerCase().includes(term) ||
      molde.maquina?.toLowerCase().includes(term) ||
      molde.tipo_configuracion?.toLowerCase().includes(term)
    )
  })
})

async function loadMoldes() {
  loading.value = true
  error.value = ""

  try {
    moldes.value = await getMoldes()
  } catch (e) {
    error.value = "No se pudieron cargar los moldes."
    console.error(e)
  } finally {
    loading.value = false
  }
}

function resetForm() {
  form.value = {
    codigo: "",
    descripcion: "",
    cavidades: 1,
    tipo_configuracion: "simple",
    maquina: "",
    tiempo_ciclo_segundos: "",
    stock_seguridad_dias: 0,
    activo: true,
  }
}

function openCreate() {
  if (!admin) return
  editingId.value = null
  resetForm()
  showForm.value = true
}

function openEdit(molde) {
  if (!admin) return

  editingId.value = molde.id
  form.value = {
    codigo: molde.codigo,
    descripcion: molde.descripcion,
    cavidades: molde.cavidades,
    tipo_configuracion: molde.tipo_configuracion,
    maquina: molde.maquina || "",
    tiempo_ciclo_segundos: molde.tiempo_ciclo_segundos || "",
    stock_seguridad_dias: molde.stock_seguridad_dias,
    activo: Boolean(molde.activo),
  }
  showForm.value = true
}

function closeForm() {
  showForm.value = false
}

async function submitForm() {
  saving.value = true
  error.value = ""

  try {
    const payload = {
      ...form.value,
      tiempo_ciclo_segundos: form.value.tiempo_ciclo_segundos || null,
      activo: Boolean(form.value.activo),
    }

    if (editingId.value) {
      await updateMolde(editingId.value, payload)
    } else {
      await createMolde(payload)
    }

    closeForm()
    resetForm()
    await loadMoldes()
  } catch (e) {
    error.value = e.message || "No se pudo guardar el molde."
    console.error(e)
  } finally {
    saving.value = false
  }
}

async function removeMolde(id) {
  if (!admin) return
  if (!confirm("¿Seguro que quieres eliminar este molde?")) return

  try {
    await deleteMolde(id)
    await loadMoldes()
  } catch (e) {
    error.value = e.message || "No se pudo eliminar el molde."
    console.error(e)
  }
}

onMounted(loadMoldes)
</script>

<template>
  <section class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
      <div>
        <h2 class="text-2xl font-semibold text-slate-800">Moldes</h2>
        <p class="text-slate-600">
          Gestión de moldes y parámetros de capacidad productiva.
        </p>
      </div>

      <div class="flex flex-col gap-3 sm:flex-row">
        <input
          v-model="search"
          type="text"
          placeholder="Buscar molde..."
          class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200 sm:w-80"
        />

        <button
          v-if="admin"
          @click="openCreate"
          class="rounded-xl bg-slate-800 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700"
        >
          Nuevo molde
        </button>
      </div>
    </div>

    <p v-if="error" class="text-sm text-red-600">
      {{ error }}
    </p>

    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
      <p v-if="loading" class="text-sm text-slate-500">
        Cargando moldes...
      </p>

      <div v-else class="overflow-x-auto">
        <table class="min-w-full border-separate border-spacing-y-2">
          <thead>
            <tr class="text-left text-sm text-slate-500">
              <th class="px-4 py-2">Código</th>
              <th class="px-4 py-2">Descripción</th>
              <th class="px-4 py-2">Cavidades</th>
              <th class="px-4 py-2">Configuración</th>
              <th class="px-4 py-2">Máquina</th>
              <th class="px-4 py-2">Ciclo (s)</th>
              <th class="px-4 py-2">Stock seg.</th>
              <th class="px-4 py-2">Estado</th>
              <th class="px-4 py-2">Acciones</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="molde in moldesFiltrados"
              :key="molde.id"
              class="bg-slate-50 text-slate-800"
            >
              <td class="rounded-l-xl px-4 py-3 font-medium">
                {{ molde.codigo }}
              </td>
              <td class="px-4 py-3">{{ molde.descripcion }}</td>
              <td class="px-4 py-3">{{ molde.cavidades }}</td>
              <td class="px-4 py-3">{{ molde.tipo_configuracion }}</td>
              <td class="px-4 py-3">{{ molde.maquina || "—" }}</td>
              <td class="px-4 py-3">{{ molde.tiempo_ciclo_segundos || "—" }}</td>
              <td class="px-4 py-3">{{ molde.stock_seguridad_dias }} días</td>
              <td class="px-4 py-3">
                <span
                  class="rounded-full px-3 py-1 text-xs font-medium"
                  :class="molde.activo ? 'bg-green-100 text-green-700' : 'bg-slate-200 text-slate-700'"
                >
                  {{ molde.activo ? "Activo" : "Inactivo" }}
                </span>
              </td>
              <td class="rounded-r-xl px-4 py-3">
                <div v-if="admin" class="flex gap-2">
                  <button
                    @click="openEdit(molde)"
                    class="rounded-lg border border-amber-300 bg-amber-50 px-3 py-2 text-sm text-amber-700 hover:bg-amber-100"
                  >
                    Editar
                  </button>
                  <button
                    @click="removeMolde(molde.id)"
                    class="rounded-lg border border-rose-300 bg-rose-50 px-3 py-2 text-sm text-rose-700 hover:bg-rose-100"
                  >
                    Eliminar
                  </button>
                </div>
              </td>
            </tr>

            <tr v-if="moldesFiltrados.length === 0">
              <td colspan="9" class="px-4 py-6 text-center text-sm text-slate-500">
                No hay moldes registrados.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div
      v-if="showForm"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
    >
      <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl">
        <div class="mb-6 flex items-center justify-between">
          <h3 class="text-xl font-bold text-slate-800">
            {{ editingId ? "Editar molde" : "Nuevo molde" }}
          </h3>

          <button
            @click="closeForm"
            class="rounded-lg px-3 py-2 text-sm text-slate-500 hover:bg-slate-100"
          >
            Cerrar
          </button>
        </div>

        <form class="space-y-6" @submit.prevent="submitForm">
          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="mb-1 block text-sm font-medium text-slate-700">Código</label>
              <input v-model="form.codigo" type="text" class="w-full rounded-xl border border-slate-300 px-4 py-2" required />
            </div>

            <div>
              <label class="mb-1 block text-sm font-medium text-slate-700">Descripción</label>
              <input v-model="form.descripcion" type="text" class="w-full rounded-xl border border-slate-300 px-4 py-2" required />
            </div>

            <div>
              <label class="mb-1 block text-sm font-medium text-slate-700">Cavidades</label>
              <input v-model.number="form.cavidades" type="number" min="1" class="w-full rounded-xl border border-slate-300 px-4 py-2" required />
            </div>

            <div>
              <label class="mb-1 block text-sm font-medium text-slate-700">Configuración</label>
              <select v-model="form.tipo_configuracion" class="w-full rounded-xl border border-slate-300 px-4 py-2" required>
                <option value="simple">Simple</option>
                <option value="izquierda_derecha">Izquierda / Derecha</option>
                <option value="multiple_referencias">Múltiples referencias</option>
              </select>
            </div>

            <div>
              <label class="mb-1 block text-sm font-medium text-slate-700">Máquina</label>
              <input v-model="form.maquina" type="text" class="w-full rounded-xl border border-slate-300 px-4 py-2" />
            </div>

            <div>
              <label class="mb-1 block text-sm font-medium text-slate-700">Tiempo ciclo (s)</label>
              <input v-model.number="form.tiempo_ciclo_segundos" type="number" min="1" class="w-full rounded-xl border border-slate-300 px-4 py-2" />
            </div>

            <div>
              <label class="mb-1 block text-sm font-medium text-slate-700">Stock seguridad (días)</label>
              <input v-model.number="form.stock_seguridad_dias" type="number" min="0" class="w-full rounded-xl border border-slate-300 px-4 py-2" required />
            </div>

            <div class="flex items-center gap-3 pt-7">
              <input v-model="form.activo" type="checkbox" class="h-4 w-4" />
              <label class="text-sm font-medium text-slate-700">Molde activo</label>
            </div>
          </div>

          <div class="flex justify-end gap-3">
            <button type="button" @click="closeForm" class="rounded-xl border border-slate-300 px-4 py-2 text-sm">
              Cancelar
            </button>

            <button
              type="submit"
              :disabled="saving"
              class="rounded-xl bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-60"
            >
              {{ saving ? "Guardando..." : editingId ? "Actualizar molde" : "Crear molde" }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </section>
</template>