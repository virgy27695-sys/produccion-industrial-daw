// IMPORTS
import { apiGet, apiPost, apiPut, apiDelete } from './http'

// OBTENER PROGRAMAS
export function getProgramas() {
  return apiGet('/programas')
}

// OBTENER UN PROGRAMA
export function getPrograma(id) {
  return apiGet(`/programas/${id}`)
}

// CREAR PROGRAMA
export function createPrograma(data) {
  return apiPost('/programas', data)
}

// ACTUALIZAR PROGRAMA
export function updatePrograma(id, data) {
  return apiPut(`/programas/${id}`, data)
}

// ELIMINAR PROGRAMA
export function deletePrograma(id) {
  return apiDelete(`/programas/${id}`)
}

// IMPORTAR EXCEL DE PROGRAMA
// Envía un archivo Excel al backend para actualizar las semanas del programa.
// Se usa FormData porque estamos enviando un archivo, no JSON.
export function importarPrograma(id, file) {
  const formData = new FormData()
  formData.append('file', file)

  return fetch(`${import.meta.env.VITE_API_URL}/programas/${id}/importar`, {
    method: 'POST',
    body: formData,
  }).then(async (response) => {
    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.message || 'Error al importar el programa')
    }

    return data
  })
}
