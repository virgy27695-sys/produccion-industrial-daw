import { apiGet, apiPost, apiPut, apiDelete } from './http'

export function getPiezas() {
  return apiGet('/piezas')
}

export function createPieza(data) {
  return apiPost('/piezas', data)
}

export function updatePieza(id, data) {
  return apiPut(`/piezas/${id}`, data)
}

export function deletePieza(id) {
  return apiDelete(`/piezas/${id}`)
}
