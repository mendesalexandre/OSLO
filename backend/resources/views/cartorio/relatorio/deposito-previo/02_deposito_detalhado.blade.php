@extends('layouts.documento')

@section('title', 'Livro de Conferência de Emolumentos')

@section('content')

    @foreach ($dados as $titulo => $osList)
        @php
            $totalCredito = collect($osList)->sum(fn($os) => $os['totais']['credito']);
            $totalEmolumentos = collect($osList)->sum(fn($os) => $os['totais']['emolumentos']);
            $totalIssqn = collect($osList)->sum(fn($os) => $os['totais']['valor_issqn']);
        @endphp

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
                /* background-color: #ccc; */
                font-weight: bold;
                border-top: 1px solid #000 !important;
                border-bottom: none !important;
                border-left: none !important;
                border-right: none !important;
                font-size: 10px;
            }
        </style>

        <div class="header">
            <div style="overflow: hidden; width: 100%;">
                <div style="float: left; width: 50%; text-align: left;">
                    <strong>
                        {{ trim($titulo) === 'Sim' ? 'Atos de Depósito Prévio' : 'Atos Complementares ao Depósito Prévio' }}
                    </strong>
                </div>
                <div style="float: right; width: 50%; text-align: right;">
                    <strong>
                        Período: {{ date('d/m/Y', strtotime($dataInicial)) }} a {{ date('d/m/Y', strtotime($dataFinal)) }}
                    </strong>
                </div>
            </div>
        </div>

        <div class="tabela-bloco">
            <table class="table table-bordered">
                <colgroup>
                    <col style="width: 30px;" /> <!-- # -->
                    <col style="width: 50px;" /> <!-- Nr. OS -->
                    <col style="width: 50px;" /> <!-- Data Caixa -->
                    <col style="width: 50px;" /> <!-- Data Selo -->
                    <col style="width: 350px;" /> <!-- Descrição -->
                    <col style="width: 90px;" /> <!-- Emolumentos -->
                    <col style="width: 90px;" /> <!-- ISSQN -->
                    <col style="width: 90px;" /> <!-- Crédito -->
                </colgroup>

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nr. OS</th>
                        <th>Data Caixa</th>
                        <th>Data Selo</th>
                        <th>Descrição</th>
                        <th>Emolumentos</th>
                        <th>ISSQN</th>
                        <th>Crédito</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($osList as $os)
                        @foreach ($os['itens'] as $index => $registro)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $os['idos'] }}</td>
                                <td>{{ $registro['datacaixa_br'] ?? '-' }}</td>
                                <td>{{ $registro['dataselagem'] ? date('d/m/Y', strtotime($registro['dataselagem'])) : '-' }}
                                </td>
                                <td>{{ $registro['descricao'] }}</td>
                                <td>R$ {{ number_format($registro['emolumentos'], 2, ',', '.') }}</td>
                                <td>R$ {{ number_format($registro['valor_issqn'], 2, ',', '.') }}</td>
                                <td>R$ {{ number_format($registro['credito'], 2, ',', '.') }}</td>
                            </tr>
                        @endforeach

                        {{-- Totais da O.S --}}
                        <tr class="total">
                            <td colspan="5" class="text-right">Totais da O.S {{ $os['idos'] }}:</td>
                            <td><strong>R$ {{ number_format($os['totais']['emolumentos'], 2, ',', '.') }}</strong></td>
                            <td><strong>R$ {{ number_format($os['totais']['valor_issqn'], 2, ',', '.') }}</strong></td>
                            <td><strong>R$ {{ number_format($os['totais']['credito'], 2, ',', '.') }}</strong></td>
                        </tr>
                    @endforeach

                    {{-- Totais gerais do grupo --}}
                    <tr class="total">
                        <td colspan="5" class="text-right">Totais do Grupo:</td>
                        <td><strong>R$ {{ number_format($totalEmolumentos, 2, ',', '.') }}</strong></td>
                        <td><strong>R$ {{ number_format($totalIssqn, 2, ',', '.') }}</strong></td>
                        <td><strong>R$ {{ number_format($totalCredito, 2, ',', '.') }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endforeach

@endsection
