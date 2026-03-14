import { apiGet, apiPost, apiPut, apiDelete } from './http'

export function getModelos() {
  return apiGet('/modelos')
}

export function createModelo(data) {
  return apiPost('/modelos', data)
}

export function updateModelo(id, data) {
  return apiPut(`/modelos/${id}`, data)
}

export function deleteModelo(id) {
  return apiDelete(`/modelos/${id}`)
}
