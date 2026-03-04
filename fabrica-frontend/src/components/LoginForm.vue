<template>
  <div class="container mt-5" style="max-width: 400px;">
    <h3 class="mb-4 text-center">Inicio de sesión</h3>

    <form @submit.prevent="login">
      <div class="mb-3">
        <label class="form-label">Correo electrónico</label>
        <input
          type="email"
          class="form-control"
          v-model="form.email"
          :class="{ 'is-invalid': errores.email }"
        />
        <div class="invalid-feedback">{{ errores.email }}</div>
      </div>

      <div class="mb-3">
        <label class="form-label">Contraseña</label>
        <input
          type="password"
          class="form-control"
          v-model="form.password"
          :class="{ 'is-invalid': errores.password }"
        />
        <div class="form-text">
          La contraseña debe tener al menos 8 caracteres, incluyendo mayúsculas,
          minúsculas, números y un carácter especial.
        </div>
        <div class="invalid-feedback">{{ errores.password }}</div>
      </div>

      <button type="submit" class="btn btn-primary w-100">Ingresar</button>
    </form>
  </div>
</template>

<script>
import { ref } from "vue";
import { login as apiLogin } from "../api";

export default {
  name: "LoginForm",
  setup() {
    const form = ref({ email: "", password: "" });
    const errores = ref({ email: "", password: "" });

    const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

    const login = async () => {
      errores.value = { email: "", password: "" };
      let valido = true;

      if (!form.value.email) {
        errores.value.email = "El correo es obligatorio.";
        valido = false;
      } else if (!/\S+@\S+\.\S+/.test(form.value.email)) {
        errores.value.email = "Correo no válido.";
        valido = false;
      }

      if (!form.value.password) {
        errores.value.password = "La contraseña es obligatoria.";
        valido = false;
      } else if (!passwordPattern.test(form.value.password)) {
        errores.value.password =
          "La contraseña debe tener 8 caracteres, mayúsculas, minúsculas, números y un carácter especial.";
        valido = false;
      }

      if (!valido) return;

      try {
        const data = await apiLogin(form.value);

        if (data.user) {
          localStorage.setItem("user", JSON.stringify(data.user));
          window.location.href = "/piezas";
        } else {
          errores.value.password = data.error || "Usuario o contraseña incorrectos";
        }
      } catch (e) {
        errores.value.password = "No se pudo conectar con la API";
      }
    };

    return { form, errores, login };
  },
};
</script>

<style scoped>
.container {
  background-color: #f8f9fa;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}
</style>