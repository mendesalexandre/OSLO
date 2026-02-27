const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      {
        path: '',
        name: 'home',
        component: () => import('pages/IndexPage.vue'),
        meta: { title: 'Início', publico: false },
      },
    ],
  },

  {
    path: '/indicador-pessoal',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      {
        path: '',
        name: 'indicador-pessoal.lista',
        component: () => import('pages/indicador-pessoal/ListaPage.vue'),
        meta: { title: 'Indicador Pessoal' },
      },
      {
        path: 'novo',
        name: 'indicador-pessoal.novo',
        component: () => import('pages/indicador-pessoal/FormPage.vue'),
        meta: { title: 'Novo Indicador Pessoal' },
      },
      {
        path: ':id/editar',
        name: 'indicador-pessoal.editar',
        component: () => import('pages/indicador-pessoal/FormPage.vue'),
        meta: { title: 'Editar Indicador Pessoal' },
      },
      {
        path: ':cpfCnpj/versoes',
        name: 'indicador-pessoal.versoes',
        component: () => import('pages/indicador-pessoal/VersoesPage.vue'),
        meta: { title: 'Histórico de Versões' },
      },
    ],
  },

  {
    path: '/auth',
    component: () => import('layouts/AuthLayout.vue'),
    children: [
      {
        path: 'login',
        name: 'login',
        component: () => import('pages/auth/LoginPage.vue'),
        meta: { title: 'Entrar', publico: true },
      },
    ],
  },

  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue'),
  },
]

export default routes
