@extends('layouts.documento')

@section('title', 'Relatório de Protocolos - ' . $cartorio->nome)

@section('content')
    <style>
        .protocolo-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
            table-layout: fixed;
        }

        .matriculas-table {
            width: 95%;
            border-collapse: collapse;
            margin-left: 30px;
            margin-bottom: 20px;
            table-layout: fixed;
        }

        .protocolo-table th,
        .protocolo-table td,
        .matriculas-table th,
        .matriculas-table td {
            border: 1px solid #dee2e6;
            padding: 6px;
            vertical-align: middle;
            word-wrap: break-word;
        }

        .protocolo-table th {
            background-color: #e9ecef;
            font-weight: bold;
            text-align: center;
            font-size: 10px;
        }

        .matriculas-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
            font-size: 9px;
        }

        .protocolo-table td {
            font-size: 10px;
        }

        .matriculas-table td {
            font-size: 9px;
        }

        .text-center {
            text-align: center;
        }

        .protocolo-section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .status-badge {
            background-color: #007bff;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
        }

        .contraditorio-sim {
            background-color: #dc3545;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
        }

        .contraditorio-nao {
            background-color: #28a745;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
        }
    </style>

    <div style="margin-bottom: 20px;">
        <h3>Relatório de Protocolos</h3>
        <p><strong>Total:</strong> {{ count($protocolos) }} protocolos</p>
    </div>

    @foreach ($protocolos as $index => $protocolo)
        <div class="protocolo-section">
            {{-- Cabeçalho do Protocolo --}}
            <table class="protocolo-table">
                <thead>
                    <tr>
                        <th style="width: 3%;">#</th>
                        <th style="width: 7%;">Protocolo</th>
                        <th style="width: 7%;">Data</th>
                        <th style="width: 10%;">Atendente</th>
                        <th style="width: 20%;">Apresentante</th>
                        <th style="width: 6%;">Cliente</th>
                        <th style="width: 7%;">Status</th>
                        <th style="width: 8%;">Ato</th>
                        <th style="width: 8%;">Natureza</th>
                        <th style="width: 6%;">Qtd Mat.</th>
                        <th style="width: 18%;">Matrículas</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center" style="font-weight: bold;">{{ $index + 1 }}</td>
                        <td class="text-center" style="font-weight: bold;">{{ $protocolo['protocolo'] }}</td>
                        <td class="text-center">{{ date('d/m/Y', strtotime($protocolo['data_protocolo'])) }}</td>
                        <td>{{ $protocolo['atendente'] }}</td>
                        <td style="font-size: 8px;">{{ $protocolo['apresentante'] }}</td>
                        <td class="text-center">{{ $protocolo['codcliente'] }}</td>
                        <td class="text-center">
                            <span class="status-badge">{{ $protocolo['status_descricao'] ?? '-' }}</span>
                        </td>
                        <td style="font-size: 8px;">{{ $protocolo['ato_descricao'] ?? '-' }}</td>
                        <td style="font-size: 8px;">{{ $protocolo['natureza_descricao'] ?? '-' }}</td>
                        <td class="text-center" style="font-weight: bold;">{{ count($protocolo['matriculas']) }}</td>
                        <td style="font-size: 8px;">
                            @foreach ($protocolo['matriculas'] as $matricula)
                                {{ $matricula['matricula'] }} -
                                {{ $matricula['circunscricao'] ?? 'Circ. ' . $matricula['idcircunscricao'] }}
                                @if (!$loop->last)
                                    <br>
                                @endif
                            @endforeach
                        </td>
                    </tr>
                    <tr style="background-color: #f9f9f9;">
                        <td></td>
                        <td colspan="2" style="font-size: 8px;"><strong>Título:</strong></td>
                        <td colspan="8" style="font-size: 8px;">{{ $protocolo['titulo_descricao'] ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endforeach

    {{-- Resumo Final --}}
    <div style="margin-top: 30px; border-top: 2px solid #333; padding-top: 15px;">
        <h4>Resumo Geral</h4>
        <table style="font-size: 12px; border-collapse: collapse;">
            <tr>
                <td style="padding: 5px; border-bottom: 1px solid #ddd;"><strong>Total de Protocolos:</strong></td>
                <td style="padding: 5px 5px 5px 15px; border-bottom: 1px solid #ddd;">{{ count($protocolos) }}</td>
            </tr>
            <tr>
                <td style="padding: 5px; border-bottom: 1px solid #ddd;"><strong>Total de Matrículas:</strong></td>
                <td style="padding: 5px 5px 5px 15px; border-bottom: 1px solid #ddd;">
                    @php
                        $totalMatriculas = 0;
                        foreach ($protocolos as $protocolo) {
                            $totalMatriculas += count($protocolo['matriculas']);
                        }
                        echo $totalMatriculas;
                    @endphp
                </td>
            </tr>
            <tr>
                <td style="padding: 5px; border-bottom: 1px solid #ddd;"><strong>Matrículas com Contraditório:</strong></td>
                <td style="padding: 5px 5px 5px 15px; border-bottom: 1px solid #ddd;">
                    @php
                        $totalContraditorio = 0;
                        foreach ($protocolos as $protocolo) {
                            foreach ($protocolo['matriculas'] as $matricula) {
                                if (($matricula['checkcontraditorio'] ?? '') === 'S') {
                                    $totalContraditorio++;
                                }
                            }
                        }
                        echo $totalContraditorio;
                    @endphp
                </td>
            </tr>
            <tr>
                <td style="padding: 5px;"><strong>Data de Geração:</strong></td>
                <td style="padding: 5px 5px 5px 15px;">{{ date('d/m/Y H:i:s') }}</td>
            </tr>
        </table>
    </div>
@endsection
