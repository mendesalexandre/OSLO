@extends('layouts.documento')

@section('title', 'Relatório de Caixa por OS')

@section('content')

    <style>
        body {
            font-family: Arial, sans-serif !important;
            font-size: 12px !important;
        }

        .resumo-geral {
            background-color: #f8f9fa !important;
            border: 1px solid #dee2e6 !important;
            padding: 15px !important;
            margin-bottom: 30px !important;
        }

        .resumo-geral h4 {
            margin-top: 0 !important;
            color: #495057 !important;
            font-size: 16px !important;
        }

        .os-section {
            border: 1px solid #dee2e6 !important;
            margin-bottom: 30px !important;
            padding: 15px !important;
            page-break-inside: avoid !important;
        }

        .os-header {
            background-color: #e9ecef !important;
            padding: 10px !important;
            margin: -15px -15px 15px -15px !important;
            border-bottom: 1px solid #dee2e6 !important;
        }

        .os-header h4 {
            margin: 0 !important;
            font-size: 14px !important;
            color: #495057 !important;
        }

        table {
            width: 100% !important;
            border-collapse: collapse !important;
            margin-top: 15px !important;
            font-size: 10px !important;
        }

        table th,
        table td {
            border: 1px solid #000 !important;
            padding: 6px !important;
            text-align: left !important;
        }

        table th {
            background-color: #f8f9fa !important;
            font-weight: bold !important;
        }

        .info-line {
            margin: 5px 0 !important;
            font-size: 11px !important;
        }

        .alerta-diferenca {
            color: #dc3545 !important;
            font-weight: bold !important;
        }

        .ok-diferenca {
            color: #28a745 !important;
            font-weight: bold !important;
        }

        .text-center {
            text-align: center !important;
        }

        .valores-resumo {
            display: grid !important;
            grid-template-columns: 1fr 1fr 1fr 1fr !important;
            gap: 10px !important;
            margin: 15px 0 !important;
        }

        .valor-item {
            font-size: 11px !important;
        }

        tfoot tr {
            background-color: #f8f9fa !important;
            font-weight: bold !important;
        }
    </style>

    <div class="resumo-geral">
        <h4>Resumo Geral do Relatório</h4>
        <div class="info-line"><strong>Total de OSs:</strong> {{ $resumo['total_os'] ?? 0 }}</div>
        <div class="info-line"><strong>Total de Registros:</strong> {{ $resumo['total_registros_geral'] ?? 0 }}</div>
        <div class="info-line"><strong>Valor Total Geral:</strong> R$
            {{ number_format($resumo['valor_total_geral'] ?? 0, 2, ',', '.') }}</div>
        <div class="info-line"><strong>Total Recebido Geral:</strong> R$
            {{ number_format($resumo['total_recebido_geral'] ?? 0, 2, ',', '.') }}</div>
    </div>

    @if (isset($data) && count($data) > 0)
        @foreach ($data as $osAgrupada)
            @php
                $totalValor = (float) ($osAgrupada['valor_total'] ?? 0);
                $totalRecebido = (float) ($osAgrupada['total_recebido'] ?? 0);
                $diferenca = $totalRecebido - $totalValor;
            @endphp

            <div class="os-section">
                <div class="os-header">
                    <h4>O.S: {{ $osAgrupada['idos'] ?? 'N/A' }} - {{ $osAgrupada['total_registros'] ?? 0 }} registro(s)</h4>
                </div>

                <div class="info-line">
                    <strong>Período:</strong>
                    @if (isset($osAgrupada['data_primeira_movimentacao']))
                        {{ \Carbon\Carbon::parse($osAgrupada['data_primeira_movimentacao'])->format('d/m/Y') }}
                        @if ($osAgrupada['data_primeira_movimentacao'] != $osAgrupada['data_ultima_movimentacao'])
                            até {{ \Carbon\Carbon::parse($osAgrupada['data_ultima_movimentacao'])->format('d/m/Y') }}
                        @endif
                    @else
                        N/A
                    @endif
                </div>

                <div class="info-line">
                    <strong>Funcionários:</strong>
                    @if (isset($osAgrupada['funcionarios']))
                        @if (is_object($osAgrupada['funcionarios']))
                            {{ $osAgrupada['funcionarios']->implode(', ') }}
                        @elseif(is_array($osAgrupada['funcionarios']))
                            {{ implode(', ', $osAgrupada['funcionarios']) }}
                        @else
                            N/A
                        @endif
                    @else
                        N/A
                    @endif
                </div>

                <div class="info-line">
                    <strong>Situações:</strong>
                    @if (isset($osAgrupada['situacoes']))
                        @if (is_object($osAgrupada['situacoes']))
                            {{ $osAgrupada['situacoes']->implode(', ') }}
                        @elseif(is_array($osAgrupada['situacoes']))
                            {{ implode(', ', $osAgrupada['situacoes']) }}
                        @else
                            N/A
                        @endif
                    @else
                        N/A
                    @endif
                </div>

                <div class="valores-resumo">
                    <div class="valor-item"><strong>Total Valor:</strong> R$ {{ number_format($totalValor, 2, ',', '.') }}
                    </div>
                    <div class="valor-item"><strong>Total Recebido:</strong> R$
                        {{ number_format($totalRecebido, 2, ',', '.') }}</div>
                    <div class="valor-item"><strong>Dinheiro:</strong> R$
                        {{ number_format($osAgrupada['valor_dinheiro'] ?? 0, 2, ',', '.') }}</div>
                    <div class="valor-item"><strong>Cheque:</strong> R$
                        {{ number_format($osAgrupada['valor_cheque'] ?? 0, 2, ',', '.') }}</div>
                </div>

                <div class="valores-resumo">
                    <div class="valor-item"><strong>Depósito:</strong> R$
                        {{ number_format($osAgrupada['valor_deposito'] ?? 0, 2, ',', '.') }}</div>
                    <div class="valor-item"><strong>Cartão:</strong> R$
                        {{ number_format($osAgrupada['valor_cartao'] ?? 0, 2, ',', '.') }}</div>
                    <div class="valor-item"></div>
                    <div class="valor-item"></div>
                </div>

                @if (abs($diferenca) > 0.01)
                    <div class="alerta-diferenca">⚠ Diferença entre valor total e recebido: R$
                        {{ number_format($diferenca, 2, ',', '.') }}</div>
                @else
                    <div class="ok-diferenca">✓ Valores conferem</div>
                @endif

                <table>
                    <thead>
                        <tr>
                            <th>ID Caixa</th>
                            <th>Data/Hora</th>
                            <th>Funcionário</th>
                            <th>Histórico</th>
                            <th>Valor Total</th>
                            <th>Total Recebido</th>
                            <th>Dinheiro</th>
                            <th>Cheque</th>
                            <th>Situação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (isset($osAgrupada['detalhes']) && is_array($osAgrupada['detalhes']))
                            @foreach ($osAgrupada['detalhes'] as $detalhe)
                                <tr>
                                    <td>{{ $detalhe['idcaixa'] ?? '' }}</td>
                                    <td>
                                        @if (isset($detalhe['datacaixa']))
                                            {{ \Carbon\Carbon::parse($detalhe['datacaixa'])->format('d/m/Y') }}<br>
                                            <small>{{ $detalhe['horario'] ?? '' }}</small>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $detalhe['funcionario'] ?? '' }}</td>
                                    <td>{{ $detalhe['historico'] ?? '' }}</td>
                                    <td>R$ {{ number_format($detalhe['valortotal'] ?? 0, 2, ',', '.') }}</td>
                                    <td>R$ {{ number_format($detalhe['totalrecebido'] ?? 0, 2, ',', '.') }}</td>
                                    <td>R$ {{ number_format($detalhe['valordinheiro'] ?? 0, 2, ',', '.') }}</td>
                                    <td>R$ {{ number_format($detalhe['valorcheque'] ?? 0, 2, ',', '.') }}</td>
                                    <td>{{ $detalhe['situacao'] ?? '' }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-center">Totais da OS:</td>
                            <td>R$ {{ number_format($osAgrupada['valor_total'] ?? 0, 2, ',', '.') }}</td>
                            <td>R$ {{ number_format($osAgrupada['total_recebido'] ?? 0, 2, ',', '.') }}</td>
                            <td>R$ {{ number_format($osAgrupada['valor_dinheiro'] ?? 0, 2, ',', '.') }}</td>
                            <td>R$ {{ number_format($osAgrupada['valor_cheque'] ?? 0, 2, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endforeach
    @else
        <div class="text-center">
            <p>Nenhum dado encontrado para o período selecionado.</p>
        </div>
    @endif

@endsection
