@extends('layouts.documento')

@section('title', 'Relatório de Atos fora do Depósito Prévio')

@section('content')
<table class="table mb-1">
    <tr>
        <th class="text-center h2 text-bold bg-light text-uppercase"
            style="border: none !important;">Relatório de Atos fora do Depósito Prévio</th>
    </tr>
</table>

<table class="table">
    <thead>
        <tr>
            <th class="text-left"
                style="border: none !important;width: 15% !important;">Livro nº: 02</th>
            <th class="text-center"
                style="border: none !important;width: 15% !important;">Folha</th>
            {{-- <th class="text-right"
                style="border: none !important;
      width: 70% !important;">Período: {{$dataInicial}} a {{$dataFinal}}</th> --}}
        </tr>
    </thead>
</table>

<table class="table table-bordered table-sm">
    <thead>
        <tr>
            {{-- <th class="text-center">O.S</th> --}}
            <th class="text-center">ID Controle</th>
            <th class="text-center">Ordem Serviço</th>
            <th>Apresentante</th>
            <th class="text-center">Dt. Entrada</th>
            <th class="text-center">Dt. Pagamento</th>
            <th class="text-center">Foi finalizado?</th>
            <th class="text-center">Quantidade</th>
            <th class="text-center">Crédito</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($protocolos as $protocolo)
        <tr>
            {{-- <td class="text-center">{{$protocolo['idos']}}</td> --}}
            <td class="text-center">{{$protocolo['idcontrole']}}</td>
            <td class="text-center">{{$protocolo['ordem_servico_id']}}</td>
            <td>{{$protocolo['apresentante']}}</td>
            <td class="text-center">{{$protocolo['data_entrada']}}</td>
            <td class="text-center">{{$protocolo['data_finalizacao'] ?? 'Não'}}</td>
            <td class="text-center">{{$protocolo['data_pagamento']}}</td>
            <td class="text-center">{{$protocolo['qtd']}}</td>
            <td class="text-center">{{$protocolo['credito']}}</td>

        </tr>
        @endforeach
    </tbody>
</table>
{{-- <table class="table table-bordered table-sm mt-4">
    <tr>
        <td class="text-center text-uppercase h2"
            style="border: none !important;">
            Termo de Encerramento
        </td>
    </tr>

</table> --}}
@endsection