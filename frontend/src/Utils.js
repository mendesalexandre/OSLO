/**
 * Utilitários globais do projeto OSLO.
 */

export function formatarDinheiroBrasil(valor) {
  return Number(valor ?? 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
}

export function formatarData(data) {
  if (!data) return '-'
  return new Date(data).toLocaleDateString('pt-BR', {
    day: '2-digit', month: '2-digit', year: 'numeric',
  })
}

export function formatarDataHora(data) {
  if (!data) return '-'
  return new Date(data).toLocaleString('pt-BR', {
    day: '2-digit', month: '2-digit', year: 'numeric',
    hour: '2-digit', minute: '2-digit',
  })
}
