import { apiGet } from "./http"

export function getDashboardResumen() {
  return apiGet("/dashboard/resumen")
}
