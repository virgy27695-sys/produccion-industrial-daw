import { createRouter, createWebHistory } from 'vue-router'

import Dashboard from '../views/Dashboard.vue'
import ClientesView from '../views/ClientesView.vue'
import ModelosView from '../views/ModelosView.vue'
import PiezasView from '../views/PiezasView.vue'
import ProgramasView from '../views/ProgramasView.vue'
import PedidosView from '../views/PedidosView.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      component: Dashboard,
    },
    {
      path: '/clientes',
      component: ClientesView,
    },
    {
      path: '/modelos',
      component: ModelosView,
    },
    {
      path: '/piezas',
      component: PiezasView,
    },
    {
      path: '/programas',
      component: ProgramasView,
    },
    {
      path: '/pedidos',
      component: PedidosView,
    },
  ],
})

export default router
