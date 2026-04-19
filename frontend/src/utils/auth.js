export function getCurrentUser() {
  return JSON.parse(localStorage.getItem('user') || sessionStorage.getItem('user') || 'null')
}

export function isAdmin() {
  const user = getCurrentUser()
  return user?.role === 'admin'
}
