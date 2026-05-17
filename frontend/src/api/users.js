// IMPORTS
import { apiGet, apiPost, apiPut, apiDelete } from './http'

// OBTENER USUARIOS
export function getUsers() {
  return apiGet('/users')
}

// CREAR USUARIO
export function createUser(data) {
  return apiPost('/users', data)
}

// ACTUALIZAR USUARIO
export function updateUser(id, data) {
  return apiPut(`/users/${id}`, data)
}

// ELIMINAR USUARIO
export function deleteUser(id) {
  return apiDelete(`/users/${id}`)
}
