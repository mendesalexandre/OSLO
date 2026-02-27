<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $pix->txid }}</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/bootstrap.css') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: #fff;
            padding: 20px;
        }

        .container {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
        }

        /* Header Principal */
        .header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 8px 8px 0 0;
            margin-bottom: 2px;
        }

        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
            text-align: center;
            letter-spacing: 1px;
        }

        .header .subtitle {
            text-align: center;
            font-size: 11px;
            margin-top: 5px;
            opacity: 0.9;
        }

        /* Seções do documento */
        .section {
            border: 2px solid #1e3c72;
            margin-bottom: 15px;
            border-radius: 0 0 8px 8px;
            overflow: hidden;
        }

        .section-header {
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 8px 15px;
            font-weight: bold;
            font-size: 11px;
            color: #1e3c72;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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
            padding: 10px 15px;
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
            background: #d4edda !important;
        }

        /* Destaque para nome do cliente */
        .cliente-destaque {
            font-weight: bold !important;
            color: #1e3c72 !important;
        }

        /* Seção do QR Code */
        .qr-section {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border: 2px dashed #1e3c72;
            border-radius: 8px;
            margin: 20px 0;
        }

        .qr-title {
            font-size: 14px;
            font-weight: bold;
            color: #1e3c72;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .qr-code {
            margin: 15px 0;
            padding: 15px;
            background: white;
            border-radius: 8px;
            display: inline-block;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .qr-instructions {
            font-size: 11px;
            color: #6c757d;
            margin-top: 10px;
            font-style: italic;
        }

        /* Seção Copia e Cola */
        .copia-cola-section {
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }

        .copia-cola-title {
            font-size: 12px;
            font-weight: bold;
            color: #856404;
            margin-bottom: 10px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .copia-cola-content {
            background: white;
            border: 1px solid #ffc107;
            border-radius: 4px;
            padding: 12px;
            word-break: break-all;
            font-family: 'Courier New', monospace;
            font-size: 10px;
            line-height: 1.3;
            color: #495057;
            max-height: 100px;
            overflow-y: auto;
        }

        /* Rodapé com instruções */
        .footer {
            margin-top: 30px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
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
            font-family: 'Courier New', monospace;
            background: #e9ecef;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 11px;
        }

        /* Responsivo para impressão */
        @media print {
            body {
                padding: 10px;
                font-size: 11px;
            }

            .container {
                max-width: 100%;
            }

            .section {
                break-inside: avoid;
            }
        }

        /* Linha pontilhada de destaque */
        .divider {
            border: none;
            border-top: 2px dashed #dee2e6;
            margin: 20px 0;
        }
    </style>
    <script>
        function subst() {
            var vars = {};

            var query_strings_from_url = document.location.search.substring(1).split('&');
            for (var query_string in query_strings_from_url) {
                if (query_strings_from_url.hasOwnProperty(query_string)) {
                    var temp_var = query_strings_from_url[query_string].split('=', 2);
                    vars[temp_var[0]] = decodeURI(temp_var[1]);
                }
            }
            var css_selector_classes = ['page', 'frompage', 'topage', 'webpage', 'section', 'subsection', 'date', 'isodate',
                'time', 'title', 'doctitle', 'sitepage', 'sitepages'
            ];
            for (var css_class in css_selector_classes) {
                if (css_selector_classes.hasOwnProperty(css_class)) {
                    var element = document.getElementsByClassName(css_selector_classes[css_class]);
                    for (var j = 0; j < element.length; ++j) {
                        element[j].textContent = vars[css_selector_classes[css_class]];
                    }
                }
            }
        }
    </script>
</head>

<body onload="subst()">
    <div class="container">
        <!-- Header Principal -->
        <div class="header">
            <h1>📱 SOLICITAÇÃO DE PAGAMENTO PIX</h1>
            <div class="subtitle">Documento de Cobrança Eletrônica</div>
        </div>

        <!-- Dados da Ordem de Serviço -->
        <div class="section">
            <div class="section-header">
                📋 Dados da Ordem de Serviço
            </div>
            <div class="section-content">
                <table class="data-table">
                    <tr>
                        <th>Ordem de Serviço:</th>
                        <td><strong>{{ $pix->idos }}</strong></td>
                    </tr>
                    <tr>
                        <th>Identificação da Transação:</th>
                        <td><span class="transaction-id">{{ $pix->transacao_id }}</span></td>
                    </tr>
                    <tr>
                        <th>Valor da Cobrança:</th>
                        <td class="valor-destaque">{{ formatarDinheiro($pix->valor) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Dados do Favorecido -->
        <div class="section">
            <div class="section-header">
                🏢 Dados do Favorecido (Recebedor)
            </div>
            <div class="section-content">
                <table class="data-table">
                    <tr>
                        <th>Nome/Razão Social:</th>
                        <td><strong>{{ strtoupper($cartorio->nome) }}</strong></td>
                    </tr>
                    <tr>
                        <th>CNPJ:</th>
                        <td>{{ formatarCnpjCpf($cartorio->cnpj) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Dados do Pagador -->
        <div class="section">
            <div class="section-header">
                👤 Dados do Pagador (Cliente)
            </div>
            <div class="section-content">
                <table class="data-table">
                    <tr>
                        <th>Nome:</th>
                        <td class="cliente-destaque">{{ $pix['devedor']['nome'] }}</td>
                    </tr>
                    <tr>
                        <th>CPF/CNPJ:</th>
                        <td>
                            @if (isset($pix['devedor']['cpf']))
                            {{ formatarCnpjCpf($pix['devedor']['cpf']) }}
                            @else
                            {{ formatarCnpjCpf($pix['devedor']['cnpj']) }}
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- QR Code para Pagamento -->
        <div class="qr-section">
            <div class="qr-title">🔲 Pagamento via QR Code</div>
            <div class="qr-code">
                <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(200)->generate($pix->copia_e_cola)) !!}" alt="QR Code PIX" style="max-width: 200px;">
            </div>
            <div class="qr-instructions">
                Aponte a câmera do seu celular ou abra o app do seu banco<br>
                e escaneie o código para efetuar o pagamento
            </div>
        </div>

        <!-- Copia e Cola -->
        <div class="copia-cola-section">
            <div class="copia-cola-title">📋 Código PIX - Copia e Cola</div>
            <div class="copia-cola-content">
                {{ $pix->copia_e_cola }}
            </div>
        </div>

        <!-- Instruções de Pagamento -->
        <div class="footer">
            <div class="footer-title">📝 Instruções para Pagamento</div>
            <div class="footer-text">
                <strong>Via QR Code:</strong> Abra o aplicativo do seu banco, acesse a opção PIX e escaneie o código
                acima.<br>
                <strong>Via Copia e Cola:</strong> Copie o código PIX acima e cole no campo correspondente no app do seu
                banco.<br>
                <strong>Atenção:</strong> Após o pagamento, o comprovante será enviado automaticamente. Guarde-o para
                seus registros.<br>
                <strong>Dúvidas:</strong> Entre em contato conosco através dos canais oficiais de atendimento.
            </div>
        </div>

        <!-- Informações do sistema para impressão -->
        <div style="margin-top: 30px; text-align: center; font-size: 9px; color: #6c757d;">
            Documento gerado automaticamente em {{ date('d/m/Y H:i:s') }} | ID: {{ $pix->txid }}
        </div>
    </div>
</body>

</html>