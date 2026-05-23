import { createRouter, createWebHistory } from 'vue-router'

// VISTAS
import Dashboard from '../views/Dashboard.vue'
import ClientesView from '../views/ClientesView.vue'
import ModelosView from '../views/ModelosView.vue'
import PiezasView from '../views/PiezasView.vue'
import ProgramasView from '../views/ProgramasView.vue'
import PedidosView from '../views/PedidosView.vue'
import LoginView from '../views/LoginView.vue'
import MoldesView from '../views/MoldesView.vue'
import ProduccionView from '../views/ProduccionView.vue'
import SituacionView from '../views/SituacionView.vue'
import MovimientosView from '../views/MovimientosView.vue'
import UsuariosView from '../views/UsuariosView.vue'
import PartesProduccionView from '../views/PartesProduccionView.vue'

const router = createRouter({
  history: createWebHistory(),

  routes: [
    {
      path: '/login',
      component: LoginView,
      meta: {
        hideLayout: true,
      },
    },

    {
      path: '/',
      component: Dashboard,
      meta: {
        requiresAuth: true,
      },
    },

    {
      path: '/clientes',
      component: ClientesView,
      meta: {
        requiresAuth: true,
        roles: ['admin', 'planificador'],
      },
    },

    {
      path: '/modelos',
      component: ModelosView,
      meta: {
        requiresAuth: true,
        roles: ['admin', 'planificador'],
      },
    },

    {
      path: '/piezas',
      component: PiezasView,
      meta: {
        requiresAuth: true,
        roles: ['admin', 'planificador'],
      },
    },

    {
      path: '/moldes',
      component: MoldesView,
      meta: {
        requiresAuth: true,
        roles: ['admin', 'planificador'],
      },
    },

    {
      path: '/programas',
      component: ProgramasView,
      meta: {
        requiresAuth: true,
        roles: ['admin', 'planificador'],
      },
    },

    {
      path: '/pedidos',
      component: PedidosView,
      meta: {
        requiresAuth: true,
        roles: ['admin', 'planificador', 'almacen'],
      },
    },

    {
      path: '/produccion',
      component: ProduccionView,
      meta: {
        requiresAuth: true,
        roles: ['admin', 'planificador', 'encargado'],
      },
    },

    {
      path: '/situacion',
      component: SituacionView,
      meta: {
        requiresAuth: true,
        roles: ['admin', 'planificador', 'encargado', 'almacen'],
      },
    },

    {
      path: '/movimientos',
      component: MovimientosView,
      meta: {
        requiresAuth: true,
        roles: ['admin', 'planificador', 'almacen'],
      },
    },

    {
      path: '/usuarios',
      component: UsuariosView,
      meta: {
        requiresAuth: true,
        roles: ['admin'],
      },
    },

    {
      path: '/partes-produccion',
      component: PartesProduccionView,
      meta: {
        requiresAuth: true,
        roles: ['admin', 'planificador', 'encargado'],
      },
    },
  ],
})

// GUARD DE AUTENTICACIÓN
router.beforeEach((to) => {
  const user = JSON.parse(localStorage.getItem('user') || sessionStorage.getItem('user') || 'null')

  if (to.meta.requiresAuth && !user) {
    return '/login'
  }

  if (to.meta.roles && !to.meta.roles.includes(user?.role)) {
    return '/'
  }

  if (to.path === '/login' && user) {
    return '/'
  }

  return true
})

export default router
