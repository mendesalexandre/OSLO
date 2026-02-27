@extends('layouts.documento')

@section('title', 'Relatório de Registro do Livro F')

@section('content')

    <style>
        div.header {
            display: block;
            width: 100%;
            border-bottom: 1px solid #000;
            padding: 5px 0;
            font-size: 14px;
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
            font-size: 14px;
            padding: 6px;
            text-align: left;
            vertical-align: middle;
        }

        .table tbody tr:not(.total) td {
            border-bottom: 1px dashed #000 !important;
            border-left: none !important;
            border-right: none !important;
            border-top: none !important;
            font-size: 14px;
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
            font-size: 14px;
        }
    </style>

    <div class="header">
        <div style="overflow: hidden; width: 100%;">
            <div style="float: left; width: 33%; text-align: left;">
                @php
                    $todosItensHeader = collect($response)->flatten(1);
                    $numeroLivro = $todosItensHeader->first()['nr_livro'] ?? '02';
                @endphp
                <strong>Livro: {{ $numeroLivro }}</strong>
            </div>
            <div style="float: left; width: 34%; text-align: center;">
                <strong>Registro no Livro F</strong>
            </div>
            <div style="float: right; width: 33%; text-align: right;">
                <strong>Ano: {{ $anoLivro }}</strong>
            </div>
        </div>
    </div>

    @foreach ($response as $dataRegistro => $items)
        <div class="tabela-bloco">
            <table class="table table-bordered">
                <colgroup>
                    <col style="width: 50px;" /> <!-- Registro -->
                    <col style="width: 60px;" /> <!-- Data -->
                    <col style="width: 250px;" /> <!-- Apresentante -->
                    <col style="width: 200px;" /> <!-- Natureza/Forma de Título -->
                    <col style="width: 250px;" /> <!-- Anotações -->
                </colgroup>

                <thead>
                    <tr>
                        <th class="text-left">Registro</th>
                        <th class="text-left">Data</th>
                        <th class="text-left">Apresentante</th>
                        <th class="text-left">Natureza/Forma de Título</th>
                        <th class="text-left">Anotações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr>
                            <td class="text-left">{{ $item['nr_registro'] }}</td>
                            <td class="text-left">{{ \Carbon\Carbon::parse($item['dataregistro'])->format('d/m') }}</td>
                            <td class="text-left">{{ $item['apresentante'] }}</td>
                            <td class="text-left">{{ $item['descricao_objeto'] }}</td>
                            <td class="text-left">{{ $item['observacoes'] ?? '' }}</td>
                        </tr>
                    @endforeach

                    {{-- Totais do grupo por data --}}
                    <tr class="total">
                        <td colspan="5" class="text-center">
                            Certifico e dou fé haver encerrado o protocolo no horário regularmente no final do
                            expediente, havendo registrado <strong><u>{{ count($items) }}</u></strong>
                            {{ count($items) == 1 ? 'título' : 'títulos' }}
                            @if (count($items) > 0)
                                @php
                                    $primeiroRegistro = collect($items)->min('nr_registro');
                                    $ultimoRegistro = collect($items)->max('nr_registro');
                                @endphp
                                <strong><u>{{ $primeiroRegistro }}</u></strong> a
                                <strong><u>{{ $ultimoRegistro }}</u></strong>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endforeach

    {{-- Total de Folhas e Títulos - Calculado dinamicamente --}}
    @php
        $todosItens = collect($response)->flatten(1);
        $totalTitulos = $todosItens->count();
        $primeiroRegistroGeral = $todosItens->min('nr_registro');
        $ultimoRegistroGeral = $todosItens->max('nr_registro');

        // Calcular total de folhas baseado nos dados
        $totalFolhasCalculado = $todosItens->sum('folhas') ?? $todosItens->count();
    @endphp

    <div style="margin: 20px auto; max-width: 100%; page-break-inside: avoid;">
        <table style="border: 4px solid #000; width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 15px; text-align: center; border: none; margin: 0; line-height: 1.4;">

                    <div
                        style="font-size: 14px; font-weight: bold; text-transform: uppercase; margin: 0 0 15px 0; text-decoration: underline;">
                        TERMO DE ENCERRAMENTO
                    </div>

                    <div
                        style="font-size: 11px; font-weight: bold; text-transform: uppercase; margin: 0 0 12px 0; line-height: 1.5;">
                        CONTÉM ESTE LIVRO {{ $totalFolhas ?? $totalFolhasCalculado }}
                        {{ ($totalFolhas ?? $totalFolhasCalculado) == 1 ? 'FOLHA' : 'FOLHAS' }},
                        NUMERADAS DE 01 A {{ str_pad($totalFolhas ?? $totalFolhasCalculado, 3, '0', STR_PAD_LEFT) }},
                        TODAS POR MIM ASSINADA COM A RUBRICA
                        <span
                            style="border-bottom: 2px solid #000; display: inline-block; width: 150px; margin: 0 8px; vertical-align: baseline;">&nbsp;</span>
                    </div>

                    <div
                        style="font-size: 11px; font-weight: bold; text-transform: uppercase; margin: 0 0 15px 0; line-height: 1.5;">
                        HAVENDO REGISTRADO {{ $totalTitulos }} {{ $totalTitulos == 1 ? 'TÍTULO' : 'TÍTULOS' }},
                        @if ($totalTitulos > 0)
                            DE Nº {{ $primeiroRegistroGeral }} A {{ $ultimoRegistroGeral }}.
                        @endif
                    </div>

                    <div style="font-size: 12px; font-weight: bold; text-transform: uppercase; margin: 0;">
                        O REFERIDO É VERDADE E DOU FÉ.
                    </div>

                </td>
            </tr>
        </table>
    </div>

@endsection
