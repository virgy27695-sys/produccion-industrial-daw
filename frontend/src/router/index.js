import { createRouter, createWebHistory } from 'vue-router'

import Dashboard from '../views/Dashboard.vue'
import ClientesView from '../views/ClientesView.vue'
import ModelosView from '../views/ModelosView.vue'
import PiezasView from '../views/PiezasView.vue'
import ProgramasView from '../views/ProgramasView.vue'
import PedidosView from '../views/PedidosView.vue'
import LoginView from '../views/LoginView.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/login',
      component: LoginView,
    },
    {
      path: '/',
      component: Dashboard,
      meta: { requiresAuth: true },
    },
    {
      path: '/clientes',
      component: ClientesView,
      meta: { requiresAuth: true, role: 'admin' },
    },
    {
      path: '/modelos',
      component: ModelosView,
      meta: { requiresAuth: true, role: 'admin' },
    },
    {
      path: '/piezas',
      component: PiezasView,
      meta: { requiresAuth: true },
    },
    {
      path: '/programas',
      component: ProgramasView,
      meta: { requiresAuth: true },
    },
    {
      path: '/pedidos',
      component: PedidosView,
      meta: { requiresAuth: true },
    },
  ],
})

router.beforeEach((to) => {
  const user = JSON.parse(localStorage.getItem('user') || sessionStorage.getItem('user') || 'null')

  if (to.meta.requiresAuth && !user) {
    return '/login'
  }

  if (to.meta.role && user?.role !== to.meta.role) {
    return '/'
  }

  if (to.path === '/login' && user) {
    return '/'
  }

  return true
})

export default router
