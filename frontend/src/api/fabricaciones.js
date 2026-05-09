// IMPORTS
import { apiGet, apiPost, apiPut, apiDelete } from './http'

// OBTENER FABRICACIONES
export function getFabricaciones() {
  return apiGet('/fabricaciones')
}

// CREAR FABRICACIÓN
export function createFabricacion(data) {
  return apiPost('/fabricaciones', data)
}

// ACTUALIZAR FABRICACIÓN
export function updateFabricacion(id, data) {
  return apiPut(`/fabricaciones/${id}`, data)
}

// ELIMINAR FABRICACIÓN
export function deleteFabricacion(id) {
  return apiDelete(`/fabricaciones/${id}`)
}
