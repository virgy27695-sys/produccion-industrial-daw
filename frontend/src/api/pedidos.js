import { apiGet } from './http'

export function getPedidos() {
  return apiGet('/pedidos')
}
