// IMPORTS
import { apiGet } from './http'

// OBTENER RESUMEN DE PRODUCCIÓN
// Llama al backend para obtener la planificación por molde
export function getResumenProduccion() {
  return apiGet('/produccion/resumen')
}
