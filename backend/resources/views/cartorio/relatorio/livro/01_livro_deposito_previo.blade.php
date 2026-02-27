@extends('layouts.documento')

@section('title', 'Relatório de Livro - Depósito Prévio')

@section('content')

<table class="table table-bordered">
    <thead>
        <tr>
            <th style="width: 50px;font-size: 8px">Número O.S</th>
            <th style="width: 50px;font-size: 8px">Data Recebido</th>
            <th style="width: 200px;font-size: 8px">Descrição</th>
            <th style="width: 50px;font-size: 8px">Protocolo</th>
            <th style="width: 50px;font-size: 8px">Livro</th>
            <th style="width: 50px;font-size: 8px">Folha</th>
            <th style="width: 50px;font-size: 8px">Selado</th>
            <th style="width: 50px;font-size: 8px">Devolução</th>
            <th style="width: 50px;font-size: 8px">Emolumentos</th>
            <th style="width: 50px;font-size: 8px">Crédito</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($ordensServico as $ordens )
            @foreach ($ordens->ordemServicoItem as $ato)
            {{-- {{ dd($ato) }} --}}
                <tr>
                    <td style="width: 50px;font-size: 8px">{{$ato->idos}}</td>
                    <td style="width: 50px;font-size: 8px">{{date('d/m/Y', strtotime($ato->datacaixa))}}</td>
                    <td style="width: 200px;font-size: 8px">{{$ato->descricao}}</td>
                    <td style="width: 50px;font-size: 8px">{{$ato->protocolo}}</td>
                    <td style="width: 50px;font-size: 8px">{{$ato->livro}}</td>
                    <td style="width: 50px;font-size: 8px">{{$ato->folhas}}</td>
                    <td style="width: 50px;font-size: 8px">{{$ato->dataselagem ? date('d/m/Y', strtotime($ato->dataselagem)) : ''}}</td>
                    <td style="width: 50px;font-size: 8px">{{$ato->datadevolucao ? date('d/m/Y', strtotime($ato->datadevolucao)) : ''}}</td>
                    <td style="width: 50px;font-size: 8px">{{number_format($ato->emolumentos,2,',','.')}}</td>
                    <td style="width: 50px;font-size: 8px">{{number_format($ato->soma_valor_total,2,',','.')}}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>


    {{-- <tfoot>
        <tr>
            <td style="width: 50px;font-size: 8px">Total Crédito</td>
        </tr>
        <tr>
            <td style="width: 50px;font-size: 8px">Total Selado</td>
        </tr>
        <tr>
            <td style="width: 50px;font-size: 8px">Total Devolvido</td>
        </tr>
        <tr>
            <td style="width: 50px;font-size: 8px">Saldo</td>
        </tr>
    </tfoot> --}}
</table>


@endsection