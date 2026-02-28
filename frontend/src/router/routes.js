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
    ],
  },

  {
    path: '/indisponibilidades',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      {
        path: '',
        name: 'indisponibilidades.lista',
        component: () => import('pages/indisponibilidade/ListaPage.vue'),
        meta: { title: 'Indisponibilidades' },
      },
      {
        path: 'nova',
        name: 'indisponibilidades.nova',
        component: () => import('pages/indisponibilidade/FormPage.vue'),
        meta: { title: 'Nova Indisponibilidade' },
      },
      {
        path: ':id/editar',
        name: 'indisponibilidades.editar',
        component: () => import('pages/indisponibilidade/FormPage.vue'),
        meta: { title: 'Editar Indisponibilidade' },
      },
    ],
  },

  {
    path: '/transacoes',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      {
        path: '',
        name: 'transacoes.lista',
        component: () => import('pages/transacao/ListaPage.vue'),
        meta: { title: 'Transações' },
      },
    ],
  },

  {
    path: '/consulta',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      {
        path: '',
        name: 'consulta.geral',
        component: () => import('pages/consulta/ConsultaGeralPage.vue'),
        meta: { title: 'Consulta Geral' },
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
