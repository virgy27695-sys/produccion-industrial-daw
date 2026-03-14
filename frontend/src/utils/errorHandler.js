export function handleError(error) {
  if (error.message.includes('Failed to fetch')) {
    return 'No se puede conectar con el servidor'
  }

  return error.message || 'Error inesperado'
}
