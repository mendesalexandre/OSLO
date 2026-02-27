@extends('layouts.documento')

@section('title', 'Relatório Diário de Fluxo de Caixa - ' . $cartorio->nome)
{{-- @section('header-title', 'Relatório de Exemplo - Laravel')
@section('footer-text', 'Relatório gerado automaticamente') --}}
@section('content')
<div>

  @foreach ($ordens as $titulo => $atos )
    <h5>{{$titulo}}</h5>

  {{-- DADOS DO CAIXA --}}
  <table class="table table-bordered">
    <thead>
      <tr>
        <th style="min-width: 50px;max-width: 50px">IDCAIXA</th>
        <th style="min-width: 20px;max-width: 20px">Data</th>
        <th style="min-width: 20px;max-width: 50px">O.S</th>
        <th style="min-width: 200px;max-width: 20px">Histórico</th>
        <th style="min-width: 20px;max-width: 20px">Total Recebido</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($atos as $item)
      <tr>
        <td style="min-width: 50px;max-width: 50px">{{$item['idcaixa']}}</td>
        <td style="min-width: 20px;max-width: 50px">{{$item['idos']}}</td>
        <td style="min-width: 20px;max-width: 20px">{{date('d/m/Y', strtotime($item['datacaixa']))}}</td>
        <td style="min-width: 200px;max-width: 200px;">{{$item['historico']}}</td>
        <td style="min-width: 20px;max-width: 20px">{{$item['totalrecebido']}}</td>
      </tbody>
      </tr>
    @endforeach

  </table>

    @endforeach
</div>

@endsection