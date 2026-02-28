export const menuItens = [
  {
    icone: 'home',
    tooltip: 'Início',
    label: 'Início',
    rota: { name: 'dashboard' },
  },
  {
    icone: 'plus-square',
    tooltip: 'Novo Protocolo',
    label: 'Novo Protocolo',
    acao: 'criarProtocolo',
    permissao: ['PROTOCOLO_CRIAR'],
  },
  {
    icone: 'menu',
    tooltip: 'Menu de Navegação',
    label: 'Navegação',
    acao: 'menuNavegacao',
  },
  {
    icone: 'search',
    tooltip: 'Buscar',
    label: 'Buscar',
    rota: { name: 'consulta.geral' },
  },

  { separador: true, label: 'Módulos' },

  {
    icone: 'files',
    tooltip: 'Protocolos',
    label: 'Protocolos',
    rota: { name: 'protocolo.lista' },
    permissao: ['PROTOCOLO_LISTAR'],
  },
  {
    icone: 'file-pen-line',
    tooltip: 'DOI',
    label: 'DOI',
    rota: { name: 'doi.index' },
    permissao: ['DOI_LISTAR'],
  },
  {
    icone: 'folder',
    tooltip: 'Contratos',
    label: 'Contratos',
    rota: { name: 'contratos.lista' },
    permissao: ['CONTRATO_LISTAR'],
  },
  {
    icone: 'receipt',
    tooltip: 'Recibos',
    label: 'Recibos',
    rota: { name: 'recibo.lista' },
    permissao: ['RECIBO_LISTAR'],
  },
  {
    icone: 'calendar-days',
    tooltip: 'Agenda',
    label: 'Agenda',
    rota: { name: 'agenda' },
  },

  { separador: true, label: 'Cadastros' },

  {
    icone: 'user',
    tooltip: 'Indicador Pessoal',
    label: 'Indicador Pessoal',
    rota: { name: 'indicador-pessoal.lista' },
  },
  {
    icone: 'ban',
    tooltip: 'Indisponibilidades',
    label: 'Indisponibilidades',
    rota: { name: 'indisponibilidades.lista' },
  },

  { separador: true, label: 'Financeiro' },

  {
    icone: 'store',
    tooltip: 'Caixa',
    label: 'Caixa',
    rota: { name: 'caixa' },
    permissao: ['CAIXA_LISTAR'],
  },
  {
    icone: 'landmark',
    tooltip: 'Movimentos de Caixa',
    label: 'Movimentos',
    rota: { name: 'movimento-caixa' },
    permissao: ['CAIXA_MOVIMENTO_LISTAR'],
  },
  {
    icone: 'arrow-right-left',
    tooltip: 'Transações',
    label: 'Transações',
    rota: { name: 'transacoes.lista' },
    permissao: ['TRANSACAO_LISTAR'],
  },
  {
    icone: 'tags',
    tooltip: 'Categorias',
    label: 'Categorias',
    rota: { name: 'administracao.categoria' },
    permissao: ['CATEGORIA_LISTAR'],
  },
  {
    icone: 'pie-chart',
    tooltip: 'Relatórios',
    label: 'Relatórios',
    rota: { name: 'relatorios' },
  },
]
