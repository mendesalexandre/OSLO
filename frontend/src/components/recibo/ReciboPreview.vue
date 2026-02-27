<template>
  <div class="recibo-preview">
    <!-- Header -->
    <div class="recibo-header">
      <h1 class="recibo-title">RECIBO DE PAGAMENTO</h1>
      <div class="recibo-subtitle">Cartório de Registro</div>
    </div>

    <!-- Informações do Recibo -->
    <div class="recibo-info-box">
      <div class="info-row">
        <span class="info-label">Número do Recibo:</span>
        <span class="info-value"><strong>{{ recibo.numero }}</strong></span>
      </div>
      <div class="info-row">
        <span class="info-label">Data de Emissão:</span>
        <span class="info-value">{{ formatarDataHora(recibo.data_emissao) }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">Protocolo:</span>
        <span class="info-value">{{ protocolo.numero }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">Solicitante:</span>
        <span class="info-value">{{ recibo.solicitante_nome }}</span>
      </div>
      <div class="info-row" v-if="recibo.solicitante_cpf_cnpj">
        <span class="info-label">CPF/CNPJ:</span>
        <span class="info-value">{{ recibo.solicitante_cpf_cnpj }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">Emitido por:</span>
        <span class="info-value">{{ recibo.usuario?.nome }}</span>
      </div>
    </div>

    <!-- Mensagem Principal -->
    <div class="recibo-mensagem">
      <p>
        Recebi(emos) de <strong>{{ recibo.solicitante_nome }}</strong>
        {{ recibo.solicitante_cpf_cnpj ? `(CPF/CNPJ: ${recibo.solicitante_cpf_cnpj})` : '' }}
        a quantia de <strong class="valor-destaque">{{ formatarDinheiroBrasil(recibo.valor_pago) }}</strong>
        {{ valorPorExtenso(recibo.valor_pago) }}, referente aos serviços prestados
        conforme discriminado abaixo.
      </p>
    </div>

    <!-- Atos/Serviços -->
    <div v-if="protocolo.itens && protocolo.itens.length > 0" class="recibo-section">
      <div class="section-title">Atos e Serviços Prestados</div>
      <table class="recibo-table">
        <thead>
          <tr>
            <th style="width: 10%">Código</th>
            <th style="width: 50%">Descrição</th>
            <th style="width: 10%" class="text-center">Qtd</th>
            <th style="width: 15%" class="text-right">Valor Unit.</th>
            <th style="width: 15%" class="text-right">Valor Total</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in protocolo.itens" :key="item.id">
            <td>{{ item.ato?.codigo || '-' }}</td>
            <td>{{ item.ato?.nome || item.descricao }}</td>
            <td class="text-center">{{ item.quantidade }}</td>
            <td class="text-right">{{ formatarDinheiroBrasil(item.valor_unitario) }}</td>
            <td class="text-right">{{ formatarDinheiroBrasil(item.valor_total) }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagamentos -->
    <div v-if="recibo.pagamentos && recibo.pagamentos.length > 0" class="recibo-section">
      <div class="section-title">Formas de Pagamento Recebidas</div>
      <table class="recibo-table">
        <thead>
          <tr>
            <th style="width: 20%">Data</th>
            <th style="width: 25%">Forma</th>
            <th style="width: 25%">Meio</th>
            <th style="width: 30%" class="text-right">Valor</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="pagamento in recibo.pagamentos" :key="pagamento.id">
            <td>{{ formatarDataHora(pagamento.data_pagamento) }}</td>
            <td>{{ pagamento.formaPagamento?.nome || '-' }}</td>
            <td>{{ pagamento.meioPagamento?.nome || '-' }}</td>
            <td class="text-right valor-destaque">{{ formatarDinheiroBrasil(pagamento.pivot?.valor || pagamento.valor) }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Totais -->
    <div class="recibo-totais">
      <div class="total-row">
        <span class="total-label">Valor Total dos Serviços:</span>
        <span class="total-value">{{ formatarDinheiroBrasil(recibo.valor_total) }}</span>
      </div>
      <div v-if="recibo.valor_isento > 0" class="total-row">
        <span class="total-label">Valor Isento:</span>
        <span class="total-value isento">- {{ formatarDinheiroBrasil(recibo.valor_isento) }}</span>
      </div>
      <div class="total-row grand-total">
        <span class="total-label">VALOR PAGO:</span>
        <span class="total-value pago">{{ formatarDinheiroBrasil(recibo.valor_pago) }}</span>
      </div>
    </div>

    <div v-if="recibo.observacao" class="recibo-observacao">
      <strong>Observação:</strong> {{ recibo.observacao }}
    </div>

    <!-- Rodapé -->
    <div class="recibo-footer">
      <p>Este recibo comprova o pagamento dos serviços acima discriminados.</p>
      <p class="data-emissao">Documento emitido eletronicamente em {{ formatarDataHora(new Date()) }}</p>
    </div>

    <!-- Assinatura -->
    <div class="recibo-assinatura">
      <div class="assinatura-linha">
        <span>Assinatura do Responsável</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { formatarDinheiroBrasil } from 'src/Utils'
import { date } from 'quasar'

defineOptions({ name: 'ReciboPreview' })

defineProps({
  recibo: {
    type: Object,
    required: true,
  },
  protocolo: {
    type: Object,
    required: true,
  },
})

function formatarDataHora(data) {
  if (!data) return ''
  return date.formatDate(data, 'DD/MM/YYYY HH:mm')
}

function valorPorExtenso(valor) {
  // Simplificado - apenas retorna formatado entre parênteses
  return `(${formatarDinheiroBrasil(valor)})`
}
</script>

<style lang="scss" scoped>
.recibo-preview {
  font-family: 'Arial', sans-serif;
  font-size: 11pt;
  line-height: 1.5;
  color: #333;
  padding: 40px;
  background: white;
  max-width: 900px;
  margin: 0 auto;
}

.recibo-header {
  text-align: center;
  margin-bottom: 30px;
  padding-bottom: 20px;
  border-bottom: 2px solid #2563eb;
}

.recibo-title {
  font-size: 24pt;
  color: #2563eb;
  margin: 0 0 5px 0;
  font-weight: 700;
}

.recibo-subtitle {
  font-size: 10pt;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.recibo-info-box {
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 20px;
  margin-bottom: 25px;
}

.info-row {
  display: flex;
  margin-bottom: 10px;
  line-height: 1.6;
}

.info-row:last-child {
  margin-bottom: 0;
}

.info-label {
  font-weight: bold;
  color: #475569;
  min-width: 150px;
  flex-shrink: 0;
}

.info-value {
  color: #1e293b;
  flex: 1;
}

.recibo-mensagem {
  margin: 30px 0;
  padding: 20px;
  background-color: #f0f9ff;
  border-left: 4px solid #2563eb;
  border-radius: 4px;
  font-size: 12pt;
  line-height: 1.8;
}

.recibo-mensagem p {
  margin: 0;
}

.recibo-section {
  margin: 25px 0;
}

.section-title {
  font-size: 14pt;
  font-weight: bold;
  color: #1e293b;
  margin-bottom: 15px;
  padding-bottom: 8px;
  border-bottom: 2px solid #e2e8f0;
}

.recibo-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 20px;
}

.recibo-table thead {
  background-color: #f1f5f9;
}

.recibo-table th {
  font-weight: bold;
  text-align: left;
  padding: 12px 10px;
  border-bottom: 2px solid #cbd5e1;
  color: #475569;
  font-size: 10pt;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.recibo-table td {
  padding: 10px;
  border-bottom: 1px solid #e2e8f0;
}

.text-center {
  text-align: center;
}

.text-right {
  text-align: right;
}

.valor-destaque {
  color: #16a34a;
  font-weight: bold;
}

.recibo-totais {
  margin-top: 30px;
  padding: 20px;
  background-color: #f8fafc;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
}

.total-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 8px;
  font-size: 11pt;
}

.total-row.grand-total {
  margin-top: 12px;
  padding-top: 12px;
  border-top: 2px solid #cbd5e1;
  font-size: 14pt;
  font-weight: bold;
}

.total-label {
  font-weight: 600;
  color: #475569;
}

.total-value {
  font-weight: bold;
  color: #1e293b;
}

.total-value.isento {
  color: #2563eb;
}

.total-value.pago {
  color: #16a34a;
}

.recibo-observacao {
  margin-top: 20px;
  padding: 15px;
  background-color: #fef3c7;
  border-left: 4px solid #f59e0b;
  border-radius: 4px;
}

.recibo-observacao strong {
  color: #92400e;
}

.recibo-footer {
  margin-top: 50px;
  padding-top: 20px;
  border-top: 1px solid #e2e8f0;
  text-align: center;
  font-size: 9pt;
  color: #64748b;
}

.recibo-footer p {
  margin: 5px 0;
}

.recibo-assinatura {
  margin-top: 60px;
  text-align: center;
}

.assinatura-linha {
  display: inline-block;
  min-width: 300px;
  padding-top: 8px;
  border-top: 1px solid #000;
  font-size: 10pt;
}

@media print {
  .recibo-preview {
    padding: 20px;
  }
}
</style>
