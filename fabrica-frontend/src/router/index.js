import { createRouter, createWebHistory } from "vue-router";

import LoginForm from "../components/LoginForm.vue";
import PiezasList from "../components/PiezasList.vue";

const routes = [
  { path: "/", redirect: "/login" },
  { path: "/login", component: LoginForm },
  { path: "/piezas", component: PiezasList },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;