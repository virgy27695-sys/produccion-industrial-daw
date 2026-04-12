import { apiPost } from "./http"

export function login(data) {
  return apiPost("/login", data)
}

export function logout() {
  return apiPost("/logout")
}