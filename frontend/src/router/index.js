import { createRouter, createWebHistory } from 'vue-router'

import { hasRole, getCurrentUser } from '../utils/auth'

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
import EntregasView from '../views/EntregasView.vue'

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
        roles: ['admin', 'planificador', 'almacen'],
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
      path: '/entregas',
      component: EntregasView,
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
  const user = getCurrentUser()

  if (to.meta.requiresAuth && !user) {
    return '/login'
  }

  if (to.path === '/login' && user) {
    if (user.role === 'encargado') {
      return '/produccion'
    }

    return '/'
  }

  if (to.path === '/' && user?.role === 'encargado') {
    return '/produccion'
  }

  if (to.meta.roles && !hasRole(to.meta.roles)) {
    if (user?.role === 'encargado') {
      return '/produccion'
    }

    if (user?.role === 'almacen') {
      return '/'
    }

    return '/'
  }

  return true
})

export default router
