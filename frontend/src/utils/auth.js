// GUARDAR TOKEN
export function setToken(token, remember = true) {
  const storage = remember ? localStorage : sessionStorage

  storage.setItem('token', token)
}

// OBTENER TOKEN
export function getToken() {
  return localStorage.getItem('token') || sessionStorage.getItem('token')
}

// GUARDAR USUARIO
export function setCurrentUser(user, remember = true) {
  const storage = remember ? localStorage : sessionStorage

  storage.setItem('user', JSON.stringify(user))
}

// OBTENER USUARIO
export function getCurrentUser() {
  const user = localStorage.getItem('user') || sessionStorage.getItem('user')

  return user ? JSON.parse(user) : null
}

// OBTENER ROL
export function getRole() {
  return getCurrentUser()?.role || null
}

// COMPROBAR ROLES
export function hasRole(roles = []) {
  const role = getRole()

  if (!role) {
    return false
  }

  // Permite pasar:
  // hasRole("admin")
  // hasRole(["admin","encargado"])

  const rolesArray = Array.isArray(roles) ? roles : [roles]

  return rolesArray.includes(role)
}

// ADMIN
export function isAdmin() {
  return hasRole('admin')
}

// PLANIFICADOR
export function isPlanificador() {
  return hasRole('planificador')
}

// ENCARGADO
export function isEncargado() {
  return hasRole('encargado')
}

// ALMACÉN
export function isAlmacen() {
  return hasRole('almacen')
}

// VALIDACIÓN DE PARTES
export function canValidateProduction() {
  return hasRole(['admin', 'planificador', 'encargado'])
}

// ELIMINAR TOKEN
export function removeToken() {
  logout()
}

// CERRAR SESIÓN
export function logout() {
  localStorage.removeItem('token')

  localStorage.removeItem('user')

  sessionStorage.removeItem('token')

  sessionStorage.removeItem('user')
}
