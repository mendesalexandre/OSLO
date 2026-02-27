@extends('layouts.documento')

@section('title', 'Relatório Detalhado de Depósito Prévio')

@section('content')

<style>
  table {
    font-size: 11px;
  }

  p, h4 {
    font-size: 12px;
    margin: 0;
    padding: 2px 0;
  }

  th {
    background-color: #f0f0f0;
  }

  .alerta-diferenca {
    color: red;
    font-weight: bold;
  }

  .ok-diferenca {
    color: green;
    font-weight: bold;
  }

  .data-divergente {
    color: red;
    font-weight: bold;
  }
</style>

@foreach ($dados as $registro)
  @php
    $totalEmolumentos = collect($registro['atos'])->sum(fn($ato) => (float) $ato['emolumentos']);
    $totalCredito = collect($registro['atos'])->sum(fn($ato) => (float) $ato['credito']);
    $totalRecebido = (float) $registro['totalrecebido'];
    $diferenca = $totalRecebido - $totalCredito;

    $dataCaixaPrincipal = \Carbon\Carbon::parse($registro['datacaixa'])->format('Y-m-d');
  @endphp

  <div style="page-break-inside: avoid; margin-bottom: 40px;">
    <h4>O.S: {{ $registro['idos'] }} - IDCaixa nº {{ $registro['idcaixa'] }} - {{ \Carbon\Carbon::parse($registro['datacaixa'])->format('d/m/Y') }}</h4>
    <p><strong>Funcionário:</strong> {{ $registro['funcionario'] }}</p>
    <p><strong>Histórico:</strong> {{ $registro['historico'] }}</p>
    <p><strong>Total Recebido:</strong> R$ {{ number_format($totalRecebido, 2, ',', '.') }}</p>
    <p><strong>Total Crédito (somado dos atos):</strong> R$ {{ number_format($totalCredito, 2, ',', '.') }}</p>

    @if (abs($diferenca) > 0.01)
      <p class="alerta-diferenca">⚠ Diferença entre recebido e crédito: R$ {{ number_format($diferenca, 2, ',', '.') }}</p>
    @else
      <p class="ok-diferenca">✓ Valores conferem com a soma de créditos</p>
    @endif

    <table width="100%" border="1" cellspacing="0" cellpadding="4">
      <thead>
        <tr>
          <th>Código</th>
          <th>Descrição</th>
          <th>Qtd</th>
          <th>Emolumentos</th>
          <th>Crédito</th>
          <th>Data Caixa</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($registro['atos'] as $ato)
          @php
            $dataAto = \Carbon\Carbon::parse($ato['datacaixa'])->format('Y-m-d');
            $dataAtoFormatada = \Carbon\Carbon::parse($ato['datacaixa'])->format('d/m/Y');
            $dataDivergente = $dataAto !== $dataCaixaPrincipal;
          @endphp
          <tr>
            <td>{{ $ato['codigo'] }}</td>
            <td>{{ $ato['descricao_ato'] }}</td>
            <td>{{ $ato['qtd'] }}</td>
            <td>R$ {{ number_format($ato['emolumentos'], 2, ',', '.') }}</td>
            <td>R$ {{ number_format($ato['credito'], 2, ',', '.') }}</td>
            <td>
              @if ($dataDivergente)
                <span class="data-divergente">
                  {{ $dataAtoFormatada }} ⚠
                </span>
              @else
                {{ $dataAtoFormatada }}
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <th colspan="3" align="right">Totais:</th>
          <th>R$ {{ number_format($totalEmolumentos, 2, ',', '.') }}</th>
          <th>R$ {{ number_format($totalCredito, 2, ',', '.') }}</th>
          <th></th>
        </tr>
      </tfoot>
    </table>
  </div>
@endforeach

@endsection
