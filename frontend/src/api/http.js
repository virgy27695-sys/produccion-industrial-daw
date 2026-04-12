const API_URL = import.meta.env.VITE_API_URL

async function request(endpoint, options = {}) {
  try {
    const response = await fetch(`${API_URL}${endpoint}`, {
      headers: {
        'Content-Type': 'application/json',
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
