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

// OBTENER ROL
export function getRole() {
  const user = getCurrentUser()

  return user?.role || null
}

// COMPROBAR ROLES
export function hasRole(roles = []) {
  const role = getRole()

  return roles.includes(role)
}

export function isAdmin() {
  return hasRole(['admin'])
}

export function isPlanificador() {
  return hasRole(['planificador'])
}

export function isEncargado() {
  return hasRole(['encargado'])
}

export function isAlmacen() {
  return hasRole(['almacen'])
}

export function canValidateProduction() {
  return hasRole(['admin', 'planificador'])
}

// CERRAR SESIÓN
export function logout() {
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  sessionStorage.removeItem('token')
  sessionStorage.removeItem('user')
}
