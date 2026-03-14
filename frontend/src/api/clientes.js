import { apiGet, apiPost, apiPut, apiDelete } from './http'

export function getClientes() {
  return apiGet('/clientes')
}

export function createCliente(data) {
  return apiPost('/clientes', data)
}

export function updateCliente(id, data) {
  return apiPut(`/clientes/${id}`, data)
}

export function deleteCliente(id) {
  return apiDelete(`/clientes/${id}`)
}
