import { apiGet, apiPost, apiPut, apiDelete } from "./http"

export function getProgramas() {
  return apiGet("/programas")
}

export function getPrograma(id) {
  return apiGet(`/programas/${id}`)
}

export function createPrograma(data) {
  return apiPost("/programas", data)
}

export function updatePrograma(id, data) {
  return apiPut(`/programas/${id}`, data)
}

export function deletePrograma(id) {
  return apiDelete(`/programas/${id}`)
}