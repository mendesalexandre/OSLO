<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo {{ $recibo->numero }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #333;
            padding: 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
        }

        .header h1 {
            font-size: 24pt;
            color: #2563eb;
            margin-bottom: 5px;
        }

        .header .subtitle {
            font-size: 10pt;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .recibo-info {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .info-row:last-child {
            margin-bottom: 0;
        }

        .info-label {
            display: table-cell;
            font-weight: bold;
            color: #475569;
            width: 150px;
            padding-right: 10px;
        }

        .info-value {
            display: table-cell;
            color: #1e293b;
        }

        .section-title {
            font-size: 14pt;
            font-weight: bold;
            color: #1e293b;
            margin: 25px 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #e2e8f0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table thead {
            background-color: #f1f5f9;
        }

        table th {
            font-weight: bold;
            text-align: left;
            padding: 12px 10px;
            border-bottom: 2px solid #cbd5e1;
            color: #475569;
            font-size: 10pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        table td {
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
        }

        table tbody tr:hover {
            background-color: #f8fafc;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .totals-section {
            margin-top: 30px;
            padding: 20px;
            background-color: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
        }

        .total-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
            font-size: 11pt;
        }

        .total-row.grand-total {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 2px solid #cbd5e1;
            font-size: 14pt;
            font-weight: bold;
            color: #1e293b;
        }

        .total-label {
            display: table-cell;
            text-align: right;
            padding-right: 20px;
            font-weight: 600;
            color: #475569;
        }

        .total-value {
            display: table-cell;
            text-align: right;
            width: 150px;
            font-weight: bold;
            color: #1e293b;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
        }

        .signature-section {
            margin-top: 60px;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 300px;
            margin: 0 auto;
            padding-top: 8px;
            font-size: 10pt;
        }

        .observacao {
            margin-top: 20px;
            padding: 15px;
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            border-radius: 4px;
        }

        .observacao strong {
            color: #92400e;
        }

        .valor-destaque {
            color: #16a34a;
            font-weight: bold;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-pago {
            background-color: #dcfce7;
            color: #166534;
        }

        .badge-isento {
            background-color: #dbeafe;
            color: #1e40af;
        }

        @media print {
            body {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>RECIBO DE PAGAMENTO</h1>
        <div class="subtitle">Cartório de Registro</div>
    </div>

    <!-- Informações do Recibo -->
    <div class="recibo-info">
        <div class="info-row">
            <div class="info-label">Número do Recibo:</div>
            <div class="info-value"><strong>{{ $recibo->numero }}</strong></div>
        </div>
        <div class="info-row">
            <div class="info-label">Data de Emissão:</div>
            <div class="info-value">{{ $recibo->data_emissao->format('d/m/Y H:i') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Protocolo:</div>
            <div class="info-value">{{ $protocolo->numero }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Solicitante:</div>
            <div class="info-value">{{ $recibo->solicitante_nome }}</div>
        </div>
        @if($recibo->solicitante_cpf_cnpj)
        <div class="info-row">
            <div class="info-label">CPF/CNPJ:</div>
            <div class="info-value">{{ $recibo->solicitante_cpf_cnpj }}</div>
        </div>
        @endif
        <div class="info-row">
            <div class="info-label">Emitido por:</div>
            <div class="info-value">{{ $recibo->usuario->nome }}</div>
        </div>
    </div>

    <!-- Atos/Serviços -->
    @if($protocolo->itens->count() > 0)
    <div class="section-title">Atos e Serviços Prestados</div>
    <table>
        <thead>
            <tr>
                <th style="width: 10%">Código</th>
                <th style="width: 50%">Descrição</th>
                <th style="width: 10%" class="text-center">Qtd</th>
                <th style="width: 15%" class="text-right">Valor Unit.</th>
                <th style="width: 15%" class="text-right">Valor Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($protocolo->itens as $item)
            <tr>
                <td>{{ $item->ato->codigo ?? '-' }}</td>
                <td>{{ $item->ato->nome ?? $item->descricao }}</td>
                <td class="text-center">{{ $item->quantidade }}</td>
                <td class="text-right">R$ {{ number_format($item->valor_unitario, 2, ',', '.') }}</td>
                <td class="text-right">R$ {{ number_format($item->valor_total, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Pagamentos -->
    @if($protocolo->pagamentos->count() > 0)
    <div class="section-title">Formas de Pagamento Recebidas</div>
    <table>
        <thead>
            <tr>
                <th style="width: 20%">Data</th>
                <th style="width: 25%">Forma</th>
                <th style="width: 25%">Meio</th>
                <th style="width: 30%" class="text-right">Valor</th>
            </tr>
        </thead>
        <tbody>
            @foreach($protocolo->pagamentos as $pagamento)
            <tr>
                <td>{{ \Carbon\Carbon::parse($pagamento->data_pagamento)->format('d/m/Y H:i') }}</td>
                <td>{{ $pagamento->formaPagamento->nome ?? '-' }}</td>
                <td>{{ $pagamento->meioPagamento->nome ?? '-' }}</td>
                <td class="text-right valor-destaque">R$ {{ number_format($pagamento->valor, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Isenções -->
    @if($protocolo->isencoes && $protocolo->isencoes->count() > 0)
    <div class="section-title">Isenções Concedidas</div>
    <table>
        <thead>
            <tr>
                <th style="width: 70%">Motivo</th>
                <th style="width: 30%" class="text-right">Valor Isento</th>
            </tr>
        </thead>
        <tbody>
            @foreach($protocolo->isencoes as $isencao)
            <tr>
                <td>{{ $isencao->motivo }}</td>
                <td class="text-right">R$ {{ number_format($isencao->valor_isento, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Totais -->
    <div class="totals-section">
        <div class="total-row">
            <div class="total-label">Valor Total dos Serviços:</div>
            <div class="total-value">R$ {{ number_format($recibo->valor_total, 2, ',', '.') }}</div>
        </div>
        @if($recibo->valor_isento > 0)
        <div class="total-row">
            <div class="total-label">Valor Isento:</div>
            <div class="total-value" style="color: #2563eb;">- R$ {{ number_format($recibo->valor_isento, 2, ',', '.') }}</div>
        </div>
        @endif
        <div class="total-row grand-total">
            <div class="total-label">VALOR PAGO:</div>
            <div class="total-value" style="color: #16a34a;">R$ {{ number_format($recibo->valor_pago, 2, ',', '.') }}</div>
        </div>
    </div>

    @if($recibo->observacao)
    <div class="observacao">
        <strong>Observação:</strong> {{ $recibo->observacao }}
    </div>
    @endif

    <!-- Rodapé -->
    <div class="footer">
        <p style="font-size: 9pt; color: #64748b; margin-bottom: 5px;">
            Este recibo comprova o pagamento dos serviços acima discriminados.
        </p>
        <p style="font-size: 9pt; color: #64748b;">
            Documento emitido eletronicamente em {{ now()->format('d/m/Y \à\s H:i') }}
        </p>
    </div>

    <div class="signature-section">
        <div class="signature-line">
            Assinatura do Responsável
        </div>
    </div>
</body>
</html>
