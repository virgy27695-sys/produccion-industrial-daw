// GUARDAR TOKEN
export function setToken(token) {
  localStorage.setItem('token', token)
}

// OBTENER TOKEN
export function getToken() {
  return localStorage.getItem('token') || sessionStorage.getItem('token')
}

// ELIMINAR TOKEN
export function removeToken() {
  localStorage.removeItem('token')
  sessionStorage.removeItem('token')
}

// GUARDAR USUARIO
export function setCurrentUser(user) {
  localStorage.setItem('user', JSON.stringify(user))
}

// OBTENER USUARIO
export function getCurrentUser() {
  const user = localStorage.getItem('user') || sessionStorage.getItem('user')

  return user ? JSON.parse(user) : null
}

// COMPROBAR SI ES ADMIN
export function isAdmin() {
  const user = getCurrentUser()

  return user?.role === 'admin'
}

// CERRAR SESIÓN
export function logout() {
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  sessionStorage.removeItem('token')
  sessionStorage.removeItem('user')
}
