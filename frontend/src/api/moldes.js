import { apiGet, apiPost, apiPut, apiDelete } from "./http"

export function getMoldes() {
  return apiGet("/moldes")
}

export function getMolde(id) {
  return apiGet(`/moldes/${id}`)
}

export function createMolde(data) {
  return apiPost("/moldes", data)
}

export function updateMolde(id, data) {
  return apiPut(`/moldes/${id}`, data)
}

export function deleteMolde(id) {
  return apiDelete(`/moldes/${id}`)
}