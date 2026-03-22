import { apiGet } from "./http"

export function getProgramas() {
  return apiGet("/programas")
}