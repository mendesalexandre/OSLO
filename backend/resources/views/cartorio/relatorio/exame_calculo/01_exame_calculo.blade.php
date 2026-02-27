@extends('layouts.documento')

@section('title', 'Relatório de Protocolo gerado para exame e cálculo')

@section('content')
<table class="table mb-1">
  <tr>
    <th class="text-center h2 text-bold bg-light text-uppercase"
    style="border: none !important;"
    >Livro de Exame e Cálculo</th>
  </tr>
</table>

<table class="table">
  <thead>
    <tr>
      <th class="text-left"
      style="border: none !important;width: 15% !important;"
      >Livro nº: 02</th>
      <th class="text-center"
      style="border: none !important;width: 15% !important;"
      >Folha</th>
      <th class="text-right"
      style="border: none !important;
      width: 70% !important;"
      >Período: {{$dataInicial}} a {{$dataFinal}}</th>
    </tr>
  </thead>
</table>

<table class="table table-bordered table-sm">
  <thead>
    <tr>
      {{-- <th class="text-center">O.S</th> --}}
      <th class="text-center">Protocolo</th>
      <th class="text-center">Data</th>
      <th>Apresentante</th>
      <th class="text-center">Natureza / Forma de Título</th>
      <th class="text-center">Prazo de Devolução</th>
      <th class="text-center">Promessa de Entrega</th>
      <th class="text-center">Data de Entrega</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($protocolos as $protocolo)
    <tr>
      {{-- <td class="text-center">{{$protocolo['idos']}}</td> --}}
      <td class="text-center">{{$protocolo['protocolo']}}</td>
      <td class="text-center">{{date('d/m/Y', strtotime($protocolo['data_protocolo']))}}</td>
      <td>{{$protocolo['apresentante']}}</td>
      <td>{{$protocolo['nome_natureza']}}</td>
      <td class="text-center">{{date('d/m/Y', strtotime($protocolo['prazo_devolucao']))}}</td>
      <td class="text-center">{{date('d/m/Y', strtotime($protocolo['data_promessa']))}}</td>
      <td class="text-center">
        {{$protocolo['data_entrega'] == null ? '' : date('d/m/Y', strtotime($protocolo['data_entrega']))}}
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
<table class="table table-bordered table-sm mt-4">
  <tr>
    <td class="text-center text-uppercase h2"
    style="border: none !important;"
    >
      Termo de Encerramento
    </td>
  </tr>
  <tr>
    <td class="text-center text-uppercase h5"
     style="border: none !important;"
    >
      Contém este livro página XX folhas, numeradas de <span class="page"> a <span class="topage">, todas por mim assinada com a rubrica _______________________
      Havendo protocolado  títulos, de nº XXXXXX a XXXXXX.
    </td>
  </tr>
  <tr>
    <td class="text-center text-uppercase h5"
    style="border: none !important;"
    >
      O referido é verdade e dou fé.
    </td>
  </tr>
</table>
@endsection