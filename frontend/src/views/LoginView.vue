<script setup>
import { ref } from "vue"
import { useRouter } from "vue-router"
import { login } from "../api/auth"

const router = useRouter()

const email = ref("")
const password = ref("")
const remember = ref(false)
const showPassword = ref(false)
const processing = ref(false)

const errors = ref({
    email: "",
    password: "",
    general: "",
})

function validateForm() {
    errors.value = {
        email: "",
        password: "",
        general: "",
    }

    let valid = true

    if (!email.value.trim()) {
        errors.value.email = "El correo electrónico es obligatorio."
        valid = false
    }

    if (!password.value.trim()) {
        errors.value.password = "La contraseña es obligatoria."
        valid = false
    }

    return valid
}

async function submit() {
    if (!validateForm()) return

    processing.value = true
    errors.value.general = ""

    try {
        const res = await login({
            email: email.value,
            password: password.value,
        })

        const user = res.user
        const token = res.token

        if (!user || !token) {
            errors.value.general = "No se pudo iniciar sesión correctamente."
            return
        }

        if (remember.value) {
            localStorage.setItem("user", JSON.stringify(user))
            localStorage.setItem("token", token)

            sessionStorage.removeItem("user")
            sessionStorage.removeItem("token")
        } else {
            sessionStorage.setItem("user", JSON.stringify(user))
            sessionStorage.setItem("token", token)

            localStorage.removeItem("user")
            localStorage.removeItem("token")
        }

        router.push("/")
    } catch (error) {
        errors.value.general = "Credenciales incorrectas."
    } finally {
        processing.value = false
    }
}
</script>

<template>
    <div class="mx-auto w-full max-w-6xl">
        <div
            class="grid overflow-hidden rounded-[2rem] border border-white/70 bg-white/80 shadow-2xl shadow-slate-300/60 backdrop-blur-xl lg:grid-cols-2">
            <!-- PANEL IZQUIERDO -->
            <div
                class="relative hidden min-h-[460px] overflow-hidden bg-gradient-to-br from-[#F8FBFD] via-[#EEF7FA] to-[#DFF3F8] p-10 lg:flex lg:flex-col lg:justify-between">
                <div class="absolute -right-24 top-10 h-80 w-80 rounded-full bg-[#59C7D8]/20 blur-3xl"></div>
                <div class="absolute -bottom-24 left-10 h-72 w-72 rounded-full bg-blue-400/10 blur-3xl"></div>

                <div class="relative z-10">
                    <img src="/logo-isavex.png" alt="ISAVEX"
                        class="mb-10 h-24 w-auto object-contain drop-shadow-[0_0_18px_rgba(89,199,216,0.35)]" />

                    <h1 class="max-w-md text-4xl font-bold leading-tight text-[#081426]">
                        Gestión inteligente de producción industrial
                    </h1>

                    <p class="mt-6 max-w-md text-sm leading-7 text-slate-600">
                        Planifica programas semanales, controla moldes, registra producción por turnos
                        y visualiza la situación real de planta desde una única plataforma.
                    </p>
                </div>

                <div class="relative z-10 grid grid-cols-3 gap-3 text-center text-xs text-[#081426]">
                    <div class="rounded-2xl border border-[#59C7D8]/40 bg-white/70 p-4 shadow-sm backdrop-blur">
                        Planning
                    </div>

                    <div class="rounded-2xl border border-[#59C7D8]/40 bg-white/70 p-4 shadow-sm backdrop-blur">
                        Producción
                    </div>

                    <div class="rounded-2xl border border-[#59C7D8]/40 bg-white/70 p-4 shadow-sm backdrop-blur">
                        Trazabilidad
                    </div>
                </div>
            </div>

            <!-- PANEL DERECHO -->
            <div class="flex items-center justify-center bg-white/90 p-6 sm:p-10">
                <div class="w-full max-w-md">
                    <div class="mb-8 text-center lg:text-left">
                        <h2 class="text-3xl font-bold text-slate-900">
                            Iniciar sesión
                        </h2>

                        <p class="mt-2 text-sm text-slate-500">
                            Accede al panel de planificación y control de producción.
                        </p>
                    </div>

                    <div v-if="errors.general"
                        class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                        {{ errors.general }}
                    </div>

                    <form class="space-y-5" @submit.prevent="submit">
                        <div>
                            <label for="email" class="mb-1 block text-sm font-medium text-slate-700">
                                Correo electrónico <span class="text-red-500">*</span>
                            </label>

                            <input id="email" v-model="email" type="email" autocomplete="username"
                                class="block w-full rounded-xl border border-slate-300 bg-white/80 px-4 py-3 text-slate-800 shadow-sm outline-none transition focus:border-[#59C7D8] focus:ring-4 focus:ring-cyan-100"
                                placeholder="correo@empresa.com" />

                            <p v-if="errors.email" class="mt-2 text-sm text-red-600">
                                {{ errors.email }}
                            </p>
                        </div>

                        <div>
                            <label for="password" class="mb-1 block text-sm font-medium text-slate-700">
                                Contraseña <span class="text-red-500">*</span>
                            </label>

                            <div class="relative">
                                <input id="password" v-model="password" :type="showPassword ? 'text' : 'password'"
                                    autocomplete="current-password"
                                    class="block w-full rounded-xl border border-slate-300 bg-white/80 px-4 py-3 pr-12 text-slate-800 shadow-sm outline-none transition focus:border-[#59C7D8] focus:ring-4 focus:ring-cyan-100"
                                    placeholder="Introduce tu contraseña" />

                                <button type="button"
                                    class="absolute inset-y-0 right-3 flex items-center justify-center text-slate-500 transition hover:text-slate-700"
                                    @click="showPassword = !showPassword"
                                    :aria-label="showPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'"
                                    :aria-pressed="showPassword">
                                    <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>

                                    <svg v-else xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M17.94 17.94A10.94 10.94 0 0112 20c-7 0-11-8-11-8a21.84 21.84 0 015.06-6.94" />
                                        <path d="M1 1l22 22" />
                                    </svg>
                                </button>
                            </div>

                            <p v-if="errors.password" class="mt-2 text-sm text-red-600">
                                {{ errors.password }}
                            </p>
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <label class="flex items-center gap-2">
                                <input v-model="remember" type="checkbox"
                                    class="h-4 w-4 rounded border-slate-300 text-[#59C7D8] focus:ring-[#59C7D8]" />

                                <span class="text-sm text-slate-600">
                                    Recuérdame
                                </span>
                            </label>

                            <button type="button"
                                class="text-sm font-medium text-cyan-700 underline underline-offset-4 hover:text-cyan-800">
                                ¿Has olvidado tu contraseña?
                            </button>
                        </div>

                        <button type="submit" :disabled="processing"
                            class="flex w-full justify-center rounded-xl bg-[#59C7D8] px-4 py-3 font-semibold text-[#081426] shadow-lg shadow-cyan-200/50 transition hover:bg-[#49B3C2] disabled:cursor-not-allowed disabled:opacity-60">
                            {{ processing ? "Iniciando sesión..." : "Iniciar sesión" }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
