// IMPORTS
import { getToken } from '../utils/auth'

// URL BASE API
const API_URL = import.meta.env.VITE_API_URL

// PETICIÓN BASE
async function request(endpoint, options = {}) {
  try {
    const token = getToken()

    const response = await fetch(`${API_URL}${endpoint}`, {
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',

        ...(token
          ? {
              Authorization: `Bearer ${token}`,
            }
          : {}),

        ...(options.headers || {}),
      },

      ...options,
    })

    const contentType = response.headers.get('content-type') || ''

    const isJson = contentType.includes('application/json')

    const data = isJson ? await response.json() : await response.text()

    if (!response.ok) {
      const message = isJson && data?.message ? data.message : 'Error en la petición'

      throw new Error(message)
    }

    return data
  } catch (error) {
    console.error('API ERROR:', error.message)

    throw error
  }
}

// GET
export function apiGet(endpoint) {
  return request(endpoint)
}

// POST
export function apiPost(endpoint, body) {
  return request(endpoint, {
    method: 'POST',
    body: JSON.stringify(body),
  })
}

// PUT
export function apiPut(endpoint, body) {
  return request(endpoint, {
    method: 'PUT',
    body: JSON.stringify(body),
  })
}

// PATCH
export function apiPatch(endpoint, body = {}) {
  return request(endpoint, {
    method: 'PATCH',
    body: JSON.stringify(body),
  })
}

// DELETE
export function apiDelete(endpoint) {
  return request(endpoint, {
    method: 'DELETE',
  })
}
