@extends('layouts.documento')

@section('title', $natureza->nome)
{{-- @section('header-title', 'Relatório de Exemplo - Laravel')
@section('footer-text', 'Relatório gerado automaticamente') --}}
@section('content')
    <div class="py-4">
        <div class="py-2 text-bold text-center text-uppercase d-block w-100 font-weight-bold h5">
            Checklist de Documentos</div>
        <span class="py-3 d-block w-100">
            A documentação necessária para a realização do registro de <b>{{ $natureza->nome }}</b> pode variar, no
            entanto, os documentos necessários para uma grande parte dos registros são:
        </span>
    </div>
    <div class="row">
        <div class="col-12">
            <table class="table table-bordered">
                <thead style="background-color: #f5f5f5">
                    <tr>
                        <th class="text-center">#</th>
                        <th scope="col">Documento</th>
                        {{-- <th scope="col">Descrição</th> --}}
                        {{-- <th scope="col">Obrigatório</th> --}}
                        <th scope="col" class="text-center">Apresentou?</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- @if ($natureza->exigencias->isEmpty())
                        <tr>
                            <td colspan="4" class="text-center">Nenhuma exigência cadastrada</td>
                        </tr>

                    @endif --}}
                        @foreach ($natureza->exigencias as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item->nome }}</td>
                            {{-- <td>{{ $item->descricao }}</td> --}}
                            {{-- <td></td> --}}
                            <td class="text-center">Sim [&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;] Não [&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;]</td>
                            {{-- <td>{{ $item->obrigatorio ? 'Sim' : 'Não' }}</td> --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
