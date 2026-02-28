const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      {
        path: '',
        name: 'dashboard',
        component: () => import('pages/DashboardPage.vue'),
        meta: { title: 'Dashboard', publico: false },
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
        meta: { title: 'Transações', permissao: ['TRANSACAO_LISTAR'] },
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

  // ------ Cartório ------
  {
    path: '/protocolo',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      {
        path: '',
        name: 'protocolo.lista',
        component: () => import('pages/protocolo/ListaPage.vue'),
        meta: { title: 'Protocolos', permissao: ['PROTOCOLO_LISTAR'] },
      },
    ],
  },

  {
    path: '/doi',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      {
        path: '',
        name: 'doi.index',
        component: () => import('pages/EmDesenvolvimentoPage.vue'),
        meta: { title: 'DOI', permissao: ['DOI_LISTAR'] },
      },
    ],
  },

  {
    path: '/contratos',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      {
        path: '',
        name: 'contratos.lista',
        component: () => import('pages/EmDesenvolvimentoPage.vue'),
        meta: { title: 'Contratos', permissao: ['CONTRATO_LISTAR'] },
      },
    ],
  },

  {
    path: '/recibo',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      {
        path: '',
        name: 'recibo.lista',
        component: () => import('pages/EmDesenvolvimentoPage.vue'),
        meta: { title: 'Recibos', permissao: ['RECIBO_LISTAR'] },
      },
    ],
  },

  // ------ Financeiro (Em desenvolvimento) ------
  {
    path: '/caixa',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      {
        path: '',
        name: 'caixa',
        component: () => import('pages/EmDesenvolvimentoPage.vue'),
        meta: { title: 'Caixa', permissao: ['CAIXA_LISTAR'] },
      },
      {
        path: 'movimento',
        name: 'movimento-caixa',
        component: () => import('pages/EmDesenvolvimentoPage.vue'),
        meta: { title: 'Movimentos de Caixa', permissao: ['CAIXA_MOVIMENTO_LISTAR'] },
      },
    ],
  },

  // ------ Em desenvolvimento ------
  {
    path: '/agenda',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      {
        path: '',
        name: 'agenda',
        component: () => import('pages/EmDesenvolvimentoPage.vue'),
        meta: { title: 'Agenda' },
      },
    ],
  },

  {
    path: '/relatorios',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      {
        path: '',
        name: 'relatorios',
        component: () => import('pages/EmDesenvolvimentoPage.vue'),
        meta: { title: 'Relatórios' },
      },
    ],
  },

  // ------ Administração ------
  {
    path: '/administracao',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      {
        path: '',
        name: 'administracao.index',
        component: () => import('pages/EmDesenvolvimentoPage.vue'),
        meta: { title: 'Administração' },
      },
      {
        path: 'categoria',
        name: 'administracao.categoria',
        component: () => import('pages/EmDesenvolvimentoPage.vue'),
        meta: { title: 'Categorias', permissao: ['CATEGORIA_LISTAR'] },
      },
    ],
  },

  // ------ Sem permissão ------
  {
    path: '/sem-permissao',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      {
        path: '',
        name: 'sem-permissao',
        component: () => import('pages/SemPermissaoPage.vue'),
        meta: { title: 'Acesso Negado', publico: false },
      },
    ],
  },

  // ------ Auth ------
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
