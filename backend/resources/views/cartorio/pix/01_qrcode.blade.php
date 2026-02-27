<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $pix->txid }}</title>
    <style>
        /* CSS otimizado para wkhtmltopdf */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: #fff;
            padding: 15px 0;
        }

        .container {
            margin: 0 auto;
            background: white;
        }

        /* Header Principal */
        .header {
            background: #1e3c72;
            color: white;
            padding: 15px 20px;
            text-align: center;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 5px 0;
            letter-spacing: 1px;
        }

        .header .subtitle {
            font-size: 11px;
            margin-top: 5px;
        }

        /* Seções do documento */
        .section {
            border: 2px solid #1e3c72;
            margin-bottom: 10px;
            overflow: hidden;
        }

        .section-header {
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 8px 10px;
            font-weight: bold;
            font-size: 11px;
            color: #1e3c72;
            text-transform: uppercase;
        }

        .section-content {
            padding: 0;
        }

        /* Tabela de dados */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .data-table th {
            background: #e9ecef;
            font-weight: bold;
            padding: 10px 10px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
            font-size: 11px;
            color: #495057;
            width: 35%;
        }

        .data-table td {
            padding: 10px 15px;
            border-bottom: 1px solid #dee2e6;
            font-size: 12px;
            color: #212529;
        }

        .data-table tr:last-child th,
        .data-table tr:last-child td {
            border-bottom: none;
        }

        /* Destaque para valor */
        .valor-destaque {
            font-size: 16px !important;
            font-weight: bold !important;
            color: #28a745 !important;
        }

        /* Destaque para nome do cliente */
        .cliente-destaque {
            font-weight: bold !important;
            color: #1e3c72 !important;
        }

        /* Seção do QR Code */
        .qr-section {
            text-align: center;
            padding: 10px;
            background: #f8f9fa;
            border: 2px dashed #1e3c72;
            margin: 10px 0;
        }

        .qr-title {
            font-size: 14px;
            font-weight: bold;
            color: #1e3c72;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .qr-code {
            margin: 10px 0;
            padding: 10px;
            background: white;
            border-radius: 2px;
            display: inline-block;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .qr-instructions {
            font-size: 11px;
            color: #6c757d;
            margin-top: 5px;
        }

        /* Seção Copia e Cola */
        .copia-cola-section {
            background: #fff3cd;
            border: 2px solid #ffc107;
            padding: 15px;
            margin: 10px 0;
        }

        .copia-cola-title {
            font-size: 12px;
            font-weight: bold;
            color: #856404;
            margin-bottom: 10px;
            text-align: center;
            text-transform: uppercase;
        }

        .copia-cola-content {
            background: white;
            border: 1px solid #ffc107;
            padding: 12px;
            word-break: break-all;
            font-family: Courier, monospace;
            font-size: 10px;
            line-height: 1.3;
            color: #495057;
        }

        /* Rodapé com instruções */
        .footer {
            margin-top: 30px;
            padding: 15px;
            background: #f8f9fa;
            border-left: 4px solid #1e3c72;
        }

        .footer-title {
            font-size: 12px;
            font-weight: bold;
            color: #1e3c72;
            margin-bottom: 8px;
        }

        .footer-text {
            font-size: 10px;
            color: #6c757d;
            line-height: 1.4;
        }

        /* IDs de transação */
        .transaction-id {
            font-family: Courier, monospace;
            background: #e9ecef;
            padding: 3px 6px;
            font-size: 11px;
        }

        /* Linha divisória */
        .divider {
            border: none;
            border-top: 2px dashed #dee2e6;
            margin: 20px 0;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }
    </style>

</head>

<body onload="subst()">
    <div class="container">
        <!-- Header Principal -->
        <div class="header">
            <h1>SOLICITAÇÃO DE PAGAMENTO PIX</h1>
            <div class="subtitle">Documento de Cobrança Eletrônica</div>
        </div>

        <!-- Dados da Ordem de Serviço -->
        <div class="section">
            <div class="section-header">
                Dados da Ordem de Serviço
            </div>
            <div class="section-content">
                <table class="data-table">
                    <tr>
                        <th>Ordem de Serviço:</th>
                        <td><strong>{{ $pix->idos ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <th>Identificação da Transação:</th>
                        <td><span class="transaction-id">{{ $pix->transacao_id ?? ($pix->txid ?? 'N/A') }}</span></td>
                    </tr>
                    <tr>
                        <th>Valor da Cobrança:</th>
                        <td class="valor-destaque">
                            @if (isset($pix->valor))
                                {{ formatarDinheiro($pix->valor) }}
                            @else
                                R$ 0,00
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Dados do Favorecido -->
        <div class="section">
            <div class="section-header">
                Dados do Favorecido (Recebedor)
            </div>
            <div class="section-content">
                <table class="data-table">
                    <tr>
                        <th>Nome/Razão Social:</th>
                        <td><strong>{{ strtoupper($cartorio->nome) }}</strong></td>
                    </tr>
                    <tr>
                        <th>CNPJ:</th>
                        <td>
                            @if (isset($cartorio->cnpj))
                                {{ formatarCnpjCpf($cartorio->cnpj) }}
                            @else
                                CNPJ não informado
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Dados do Pagador -->
        <div class="section">
            <div class="section-header">
                Dados do Pagador (Cliente)
            </div>
            <div class="section-content">
                <table class="data-table">
                    <tr>
                        <th>Nome:</th>
                        <td class="cliente-destaque">
                            @if (isset($pix['devedor']['nome']))
                                {{ $pix['devedor']['nome'] }}
                            @elseif(isset($pix->devedor_nome))
                                {{ $pix->devedor_nome }}
                            @else
                                Nome não informado
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>CPF/CNPJ:</th>
                        <td>
                            @if (isset($pix['devedor']['cpf']) && !empty($pix['devedor']['cpf']))
                                {{ formatarCnpjCpf($pix['devedor']['cpf']) }}
                            @elseif(isset($pix['devedor']['cnpj']) && !empty($pix['devedor']['cnpj']))
                                {{ formatarCnpjCpf($pix['devedor']['cnpj']) }}
                            @elseif(isset($pix->devedor_cpf))
                                {{ formatarCnpjCpf($pix->devedor_cpf) }}
                            @elseif(isset($pix->devedor_cnpj))
                                {{ formatarCnpjCpf($pix->devedor_cnpj) }}
                            @else
                                CPF/CNPJ não informado
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>


        <!-- QR Code para Pagamento -->
        <div class="qr-section">
            <div class="qr-title">Pagamento via QR Code</div>
            <div class="qr-code">
                @if (isset($pix->copia_e_cola) && !empty($pix->copia_e_cola))
                    <img src="data:image/png;base64,{!! base64_encode(QrCode::format('png')->size(200)->generate($pix->copia_e_cola)) !!}" alt="QR Code PIX"
                        style="max-width: 200px;">
                @else
                    <div
                        style="width: 200px; height: 200px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border: 1px solid #ddd;">
                        QR Code não disponível
                    </div>
                @endif
            </div>
            <div class="qr-instructions">
                Aponte a câmera do seu celular ou abra o app do seu banco<br>
                e escaneie o código para efetuar o pagamento
            </div>
        </div>

        <!-- Copia e Cola -->
        <div class="copia-cola-section">
            <div class="copia-cola-title">Código PIX - Copia e Cola</div>
            <div class="copia-cola-content">
                {{ $pix->copia_e_cola ?? 'Código PIX não disponível' }}
            </div>
        </div>

        <!-- Instruções de Pagamento -->
        <div class="footer">
            <div class="footer-title">Instruções para Pagamento</div>
            <div class="footer-text">
                <strong>Via QR Code:</strong> Abra o aplicativo do seu banco, acesse a opção PIX e escaneie o código
                acima.<br>
                <strong>Via Copia e Cola:</strong> Copie o código PIX acima e cole no campo correspondente no app do seu
                banco.<br>
                <strong>Dúvidas:</strong> Entre em contato conosco através dos canais oficiais de atendimento.
            </div>
        </div>

    </div>
</body>

</html>
