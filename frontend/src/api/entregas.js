// IMPORTS
import { apiGet, apiPost, apiPut, apiDelete } from './http'

// OBTENER ENTREGAS
export function getEntregas() {
  return apiGet('/entregas')
}

// CREAR ENTREGA
export function createEntrega(data) {
  return apiPost('/entregas', data)
}

// ACTUALIZAR ENTREGA
export function updateEntrega(id, data) {
  return apiPut(`/entregas/${id}`, data)
}

// ELIMINAR ENTREGA
export function deleteEntrega(id) {
  return apiDelete(`/entregas/${id}`)
}
