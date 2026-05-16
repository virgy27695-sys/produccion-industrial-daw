// IMPORTS
import { ref } from 'vue'

// COMPOSABLE CRUD REUTILIZABLE
//
// Objetivo:
// - centralizar loading
// - centralizar errores
// - evitar duplicación
// - reutilizar lógica async
// - simplificar vistas

export function useCrud() {
  // ESTADOS GLOBALES
  const loading = ref(false)
  const saving = ref(false)

  const error = ref('')
  const formError = ref('')

  // EJECUTAR CARGA
  //
  // Uso:
  // await executeLoad(async () => {})
  async function executeLoad(callback) {
    loading.value = true
    error.value = ''

    try {
      return await callback()
    } catch (e) {
      console.error(e)

      error.value = 'Se produjo un error al cargar los datos.'

      throw e
    } finally {
      loading.value = false
    }
  }

  // EJECUTAR GUARDADO
  //
  // Uso:
  // await executeSave(async () => {})
  async function executeSave(callback) {
    saving.value = true
    formError.value = ''

    try {
      return await callback()
    } catch (e) {
      console.error(e)

      formError.value = 'Se produjo un error al guardar.'

      throw e
    } finally {
      saving.value = false
    }
  }

  // RESET ERRORES
  function clearErrors() {
    error.value = ''
    formError.value = ''
  }

  // EXPORTACIÓN
  return {
    // STATES
    loading,
    saving,

    error,
    formError,

    // METHODS
    executeLoad,
    executeSave,
    clearErrors,
  }
}
