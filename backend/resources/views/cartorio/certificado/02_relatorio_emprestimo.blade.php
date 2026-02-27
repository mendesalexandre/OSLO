@extends('layouts.documento')

@section('title', 'Relatório de Empréstimo de Certificado Digital - ' . $cartorio->nome)
{{-- @section('header-title', 'Relatório de Exemplo - Laravel')
@section('footer-text', 'Relatório gerado automaticamente') --}}
@section('content')
<table class="table table-bordered mb-0"
style="font-size:12px"
>
   <thead>
    <tr>
        <th class="text-center">#</th>
        {{-- <th>Data Inicial</th> --}}
        <th>Responsável</th>
        <th>Setor</th>
        <th>Titular do Certificado</th>
        <th>Nº do Certificado</th>
        <th>Data de Entrega</th>
        <th>Vencido?</th>
    </tr>

   </thead>
    <tbody>
        @php
            $index = 0;
        @endphp
        @foreach ($certificados as $item)
            @php  $index++;@endphp
            <tr>
                <td class="text-center">{{ $index }}</td>
                {{-- <td>{{ $item->data_inicial }}</td> --}}
                <td>{{ $item->responsavel->name }}</td>
                <td>{{ $item->responsavel->departamento->nome ?? 'Não informado'}}</td>
                <td>{{ $item->titular->nome }}</td>
                <td>{{ $item->numero_serie_hardware }}</td>
                <td>{{$item->data_entrega ? date('d/m/Y', strtotime($item->data_entrega)) : '' }}</td>
                <td>
                    @if (Carbon\Carbon::parse($item->validade_final)->format('Y-m-d') < Carbon\Carbon::now()->format('Y-m-d'))
                        <span class="badge badge-danger">Sim</span>
                    @else
                        <span class="badge badge-success">Não</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

@endsection