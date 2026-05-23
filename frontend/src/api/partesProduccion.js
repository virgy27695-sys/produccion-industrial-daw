import { apiGet, apiPost, apiPut, apiPatch, apiDelete } from './http'

// OBTENER TODOS
export function getPartesProduccion() {
  return apiGet('/partes-produccion')
}

// CREAR
export function createParteProduccion(data) {
  return apiPost('/partes-produccion', data)
}

// ACTUALIZAR
export function updateParteProduccion(id, data) {
  return apiPut(`/partes-produccion/${id}`, data)
}

// VALIDAR
export function validarParteProduccion(id) {
  return apiPatch(`/partes-produccion/${id}/validar`)
}

// CORREGIR
export function corregirParteProduccion(id) {
  return apiPatch(`/partes-produccion/${id}/corregir`)
}

// ELIMINAR
export function deleteParteProduccion(id) {
  return apiDelete(`/partes-produccion/${id}`)
}
