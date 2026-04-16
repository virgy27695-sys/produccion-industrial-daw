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

        if (remember.value) {
            localStorage.setItem("user", JSON.stringify(user))
            sessionStorage.removeItem("user")
        } else {
            sessionStorage.setItem("user", JSON.stringify(user))
            localStorage.removeItem("user")
        }

        router.push("/")
    } catch (error) {
        errors.value.general = "Credenciales incorrectas."
        console.error(error)
    } finally {
        processing.value = false
    }
}
</script>

<template>
    <div class="relative flex min-h-screen items-center justify-center overflow-hidden bg-slate-100 px-4 py-10">
        <!-- Fondo decorativo -->
        <div class="absolute inset-0">
            <div class="absolute left-0 top-0 h-72 w-72 rounded-full bg-sky-100 blur-3xl"></div>
            <div class="absolute bottom-0 right-0 h-80 w-80 rounded-full bg-blue-100 blur-3xl"></div>
        </div>

        <div
            class="relative z-10 grid w-full max-w-6xl grid-cols-1 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl lg:grid-cols-2">
            <!-- Lado izquierdo -->
            <div
                class="hidden bg-gradient-to-br from-slate-900 via-slate-800 to-sky-900 p-10 text-white lg:flex lg:flex-col lg:justify-between">
                <div>  

                    <h1 class="max-w-md text-4xl font-bold leading-tight">
                        Sistema de Producción Industrial
                    </h1>

                </div>
            </div>

            <!-- Lado derecho -->
            <div class="flex items-center justify-center bg-white p-6 sm:p-10">
                <div class="w-full max-w-md">
                    <div class="mb-8 text-center lg:text-left">
                        <h2 class="text-3xl font-bold text-slate-800">
                            Iniciar sesión
                        </h2>

                        <p class="mt-2 text-sm text-slate-500">
                            Introduce tus credenciales para acceder al panel de gestión.
                        </p>
                    </div>

                    <div v-if="errors.general"
                        class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                        {{ errors.general }}
                    </div>

                    <form @submit.prevent="submit" class="space-y-5">
                        <div>
                            <label for="email" class="mb-1 block text-sm font-medium text-slate-700">
                                Correo electrónico <span class="text-red-500">*</span>
                            </label>

                            <input id="email" v-model="email" type="email" autocomplete="username"
                                class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-800 shadow-sm outline-none transition focus:border-sky-600 focus:ring-4 focus:ring-sky-100"
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
                                    class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 pr-12 text-slate-800 shadow-sm outline-none transition focus:border-sky-600 focus:ring-4 focus:ring-sky-100"
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
                                    class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500" />
                                <span class="text-sm text-slate-600">Recuérdame</span>
                            </label>

                            <button type="button"
                                class="text-sm font-medium text-sky-700 underline underline-offset-4 hover:text-sky-800">
                                ¿Has olvidado tu contraseña?
                            </button>
                        </div>

                        <button type="submit" :disabled="processing"
                            class="flex w-full justify-center rounded-xl bg-sky-600 px-4 py-3 font-medium text-white transition hover:bg-sky-700 disabled:cursor-not-allowed disabled:opacity-60">
                            {{ processing ? "Iniciando sesión..." : "Iniciar sesión" }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>