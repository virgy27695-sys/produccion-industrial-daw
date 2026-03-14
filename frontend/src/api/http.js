const API_URL = import.meta.env.VITE_API_URL

async function request(endpoint, options = {}) {
  try {
    const response = await fetch(`${API_URL}${endpoint}`, {
      headers: {
        'Content-Type': 'application/json',
      },
      ...options,
    })

    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.message || 'Error en la petición')
    }

    return data
  } catch (error) {
    console.error('API ERROR:', error.message)
    throw error
  }
}

export function apiGet(endpoint) {
  return request(endpoint)
}

export function apiPost(endpoint, body) {
  return request(endpoint, {
    method: 'POST',
    body: JSON.stringify(body),
  })
}

export function apiPut(endpoint, body) {
  return request(endpoint, {
    method: 'PUT',
    body: JSON.stringify(body),
  })
}

export function apiDelete(endpoint) {
  return request(endpoint, {
    method: 'DELETE',
  })
}
