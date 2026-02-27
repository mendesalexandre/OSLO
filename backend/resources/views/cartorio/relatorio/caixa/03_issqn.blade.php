@extends('layouts.documento')

@section('title', 'Relatório ISSQN - Depósito Prévio e Posterior')

@section('content')

<style>
  div.header {
    display: block;
    width: 100%;
    border-bottom: 1px solid #000;
    padding: 5px 0;
    font-size: 12px;
  }
  .table {
    table-layout: fixed !important;
    border-collapse: collapse;
    width: 100%;
    border: none !important;
  }
  .table thead tr th {
    border: none !important;
    border-bottom: 1px solid #000 !important;
    font-size: 10px;
    padding: 6px;
    text-align: left;
    vertical-align: middle;
  }
  .table tbody tr:not(.total) td {
    border-bottom: 1px dashed #000 !important;
    border-left: none !important;
    border-right: none !important;
    border-top: none !important;
    font-size: 10px;
    padding: 3px;
    text-align: left;
    vertical-align: middle;
  }
  .table td.descricao {
    text-align: left;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .table tr.total td {
    font-weight: bold;
    border-top: 1px solid #000 !important;
    border-bottom: none !important;
    border-left: none !important;
    border-right: none !important;
    font-size: 10px;
  }
</style>

@php
  $formatarData = fn($date) => \Carbon\Carbon::parse($date)->format('d/m/Y') ?? '--/--';

  $totaisPrevio = [
    'credito' => $issqnPrevio->sum('credito'),
    'issqn' => $issqnPrevio->sum('total_issqn'),
    'total' => $issqnPrevio->sum('valortotal'),
    'qtd' => $issqnPrevio->sum('qtd'),
  ];

  $totaisPosterior = [
    'credito' => $issqnPosterior->sum('credito'),
    'issqn' => $issqnPosterior->sum('total_issqn'),
    'total' => $issqnPosterior->sum('valortotal'),
    'qtd' => $issqnPosterior->sum('qtd'),
  ];
@endphp

{{-- DEPÓSITO PRÉVIO --}}
<div class="header" style="margin-top: 10px;">
  <div style="overflow: hidden; width: 100%;">
    <div style="float: left; width: 50%; text-align: left;">
      <strong class="text-uppercase">ISSQN de Atos Finalizados</strong>
    </div>
    <div style="float: right; width: 50%; text-align: right;">
      <strong>Período: {{ $formatarData($dataInicial) }} a {{ $formatarData($dataFinal) }}</strong>
    </div>
  </div>
</div>

<div class="tabela-bloco">
  <table class="table table-bordered">
    <colgroup>
      <col style="width: 30px;" />
      <col style="width: 50px;" />
      <col style="width: 80px;" />
      <col style="width: 80px;" />
      <col style="width: 100px;" />
      <col style="width: 100px;" />
      <col style="width: 400px;" />
      <col style="width: 90px;" />
      <col style="width: 90px;" />
      <col style="width: 90px;" />
      <col style="width: 90px;" />
    </colgroup>

    <thead>
      <tr>
        <th>#</th>
        <th>Nr. OS</th>
        <th>ID Ato</th>
        <th>Data Caixa</th>
        <th>Data Selo</th>
        <th>Selo Digital</th>
        <th>Descrição</th>
        <th>Tipo Ato</th>
        <th>Emolumentos</th>
        <th>Qtd</th>
        <th>ISSQN</th>
        <th>Crédito</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($issqnPrevio as $index => $registro)
      <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $registro->idos }}</td>
        <td>{{ $registro->idcontrole }}</td>
        <td>{{ $registro->datacaixa ? date('d/m/Y', strtotime($registro->datacaixa)) : '-' }}</td>
        <td>{{ $registro->dataselagem ? date('d/m/Y', strtotime($registro->dataselagem)) : '-' }}</td>
        <td>{{ $registro->selo_digital }}</td>
        <td class="descricao">{{ $registro->descricao }}</td>
        <td>{{ $registro->descricao_tipo_deposito }}</td>
        <td>R$ {{ number_format($registro->valortotal, 2, ',', '.') }}</td>
        <td>{{ $registro->qtd }}</td>
        <td>R$ {{ number_format($registro->valor_issqn, 2, ',', '.') }}</td>
        <td>R$ {{ number_format($registro->credito, 2, ',', '.') }}</td>
      </tr>
      @endforeach

      <tr class="total">
        <td colspan="8" class="text-right">Totais:</td>
        <td>R$ {{ number_format($totaisPrevio['total'], 2, ',', '.') }}</td>
        <td>{{$totaisPrevio['qtd']}}</td>
        <td>R$ {{ number_format($totaisPrevio['issqn'], 2, ',', '.') }}</td>
        <td>R$ {{ number_format($totaisPrevio['credito'], 2, ',', '.') }}</td>
      </tr>
    </tbody>
  </table>
</div>

{{-- DEPÓSITO POSTERIOR --}}
<div class="header" style="margin-top: 40px;">
  <div style="overflow: hidden; width: 100%;">
    <div style="float: left; width: 50%; text-align: left;">
      <strong class="text-uppercase">ISSQN de Atos Posteriores (Recebimento Futuro)</strong>
    </div>
    <div style="float: right; width: 50%; text-align: right;">
      <strong>Período: {{ $formatarData($dataInicial) }} a {{ $formatarData($dataFinal) }}</strong>
    </div>
  </div>
</div>

<div class="tabela-bloco">
  <table class="table table-bordered">
       <colgroup>
      <col style="width: 30px;" />
      <col style="width: 50px;" />
      <col style="width: 80px;" />
      <col style="width: 80px;" />
      <col style="width: 100px;" />
      <col style="width: 100px;" />
      <col style="width: 400px;" />
      <col style="width: 90px;" />
      <col style="width: 90px;" />
      <col style="width: 90px;" />
      <col style="width: 90px;" />
    </colgroup>

    <thead>
      <tr>
        <th>#</th>
        <th>Nr. OS</th>
        <th>ID Ato</th>
        <th>Data Caixa</th>
        <th>Data Selo</th>
        <th>Selo Digital</th>
        <th>Descrição</th>
        <th>Tipo Ato</th>
        <th>Emolumentos</th>
        <th>Qtd</th>
        <th>ISSQN</th>
        <th>Crédito</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($issqnPosterior as $index => $registro)
      <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $registro->idos }}</td>
        <td>{{ $registro->idcontrole }}</td>
        <td>{{ $registro->datacaixa ? date('d/m/Y', strtotime($registro->datacaixa)) : '-' }}</td>
        <td>{{ $formatarData($registro->dataselagem) }}</td>
        <td>{{ $registro->selo_digital }}</td>
        <td class="descricao">{{ $registro->descricao }}</td>
        <td>{{ $registro->descricao_tipo_deposito }}</td>
        <td>R$ {{ number_format($registro->emolumentos, 2, ',', '.') }}</td>
        <td>{{ $registro->qtd }}</td>
        <td>R$ {{ number_format($registro->total_issqn, 2, ',', '.') }}</td>
        <td>R$ {{ number_format($registro->valortotal, 2, ',', '.') }}</td>
      </tr>
      @endforeach

      <tr class="total">
        <td colspan="8" class="text-right">Totais:</td>
        <td>R$ {{ number_format($totaisPosterior['total'], 2, ',', '.') }}</td>
        <td>{{$totaisPosterior['qtd']}}</td>
        <td>R$ {{ number_format($totaisPosterior['issqn'], 2, ',', '.') }}</td>
        <td>R$ {{ number_format($totaisPosterior['credito'], 2, ',', '.') }}</td>
      </tr>
    </tbody>
  </table>
</div>

@endsection
