@extends('layouts.documento')

@section('title', 'Abertura de Matrículas')

@section('content')
<div class="py-4">
<table class="table">
    <tr>
        <td class="w-50" colspan="3"
        {{-- remover a borda --}}
        style="border: none !important;"
        >
            <span class="text-bold text-h6"><b>Relatório de Abertura de Matrículas</b></span>
        </td>

        <td class="w-50 text-right" colspan="3"
         style="border: none !important;"
        >
            <span>Período: {{$dataInicial}} à {{$dataFinal}} </span>
        </td>
    </tr>

</table>

    <table class="table table-bordered" style="font-size: 13px">
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th class="text-left">Matrícula</th>
                <th style="width: 300px">Imóvel</th>
                <th>Data Abertura</th>
                {{-- <th style="width: 200px">Descrição Abertura</th> --}}
                <th>Protocolo</th>
                <th style="width: 200px">Matricula Anterior</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($matriculas as $index => $item)
            <tr>
                <td class="text-center">{{$index + 1}}</td>
                <td class="text-left">{{$item['matricula']}}</td>
                <td class="text-left" style="width: 300px">{{$item['descricao_imovel']}}</td>
                <td class="text-left">{{date('d/m/Y', strtotime($item['data_ato']))}}</td>
                {{-- <td class="text-left" style="width: 200px">{{$item['descricao_ato']}}</td> --}}
                <td class="text-left">{{$item['prenotacao']}}</td>
                <td class="text-left" style="width: 200px;font-size: 12px">

                    @foreach ($item['matriculas_anterior'] as $matricula)
                    {{$matricula['circunscricao']['nome']}} - Matrícula: {{$matricula['matricula']}}<br>
                    @endforeach


                </td>
            </tr>
            @endforeach
    </table>

</div>
@endsection