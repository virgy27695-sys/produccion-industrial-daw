import { apiGet, apiPost, apiPut, apiDelete } from './http'

export function getPedidos() {
  return apiGet('/pedidos')
}

export function createPedido(data) {
  return apiPost('/pedidos', data)
}

export function updatePedido(id, data) {
  return apiPut(`/pedidos/${id}`, data)
}

export function deletePedido(id) {
  return apiDelete(`/pedidos/${id}`)
}
