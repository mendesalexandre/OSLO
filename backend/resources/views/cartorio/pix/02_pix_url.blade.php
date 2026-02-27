<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/css/sweetalert2.min.css') }}">
    <meta name="theme-color" content="#319197">
    <title>Solicitação de Pagamento por PIX</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f5f5f5;
            font-family: Arial, Helvetica, sans-serif;
            padding: 10px;

        }

        .text-center {
            text-align: center;
        }

        .card {
            background-color: #fff;
            width: 100%;
            height: 100%;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
        }

        a {
            padding: 20px;
            background-color: #3F7DEA;
            border-radius: 4px;
            color: #fff;
            text-decoration: none;
            cursor: pointer;
            width: 100%;
            display: block;
        }

        button {
            padding: 20px;
            background-color: #3F7DEA;
            border-radius: 4px;
            color: #fff;
            text-decoration: none;
            cursor: pointer;
            width: 100%;
            display: block;
            border: none;
            box-shadow: 0 4px 0 rgba(0, 0, 0, 0.2)
        }
    </style>
</head>

<body>

    <div class="card" style="margin-bottom: 5px;font-weight: bold">
        <p class="text-center">1º Ofício de Registro de Imóveis, Títulos e Documentos de Sinop/MT</p>
    </div>
    <div class="card" style="margin-bottom: 5px;font-weight: bold">
        <p class="text-center">Solicitação de Pagamento por PIX</p>
    </div>
    <div class="card">
        <div style="text-align: center">
            {!! QrCode::size(200)->generate($pix->copia_e_cola) !!}
        </div>
    </div>

    <div class="card" style="margin-top: 5px;">
        <p style="text-align: left">
            <strong>Ordem de Serviço:</strong> {{ $pix->idos }}
        </p>
        {{-- <p style="text-align: left">
            <strong>Identificador da Transação: </strong> {{ $url->txid }}
        </p> --}}
    </div>

    <div class="card" style="margin-top: 5px">
        <p class="text-center" style="font-weight: bold;text-transform: uppercase">Dados do Cliente</p>
        <p>Cliente: {{ $pix['devedor']['nome'] }}</p>
        <td>
            @if (isset($pix['devedor']['cpf']))
                {{ formatarCnpjCpf($pix['devedor']['cpf']) }}
            @else
                {{ formatarCnpjCpf($pix['devedor']['cnpj']) }}
            @endif
        </td>
    </div>

    <div class="card" style="margin-top: 5px">
        <div style="text-align: left">
            Valor: <strong>R$ {{ formatarDinheiro($pix->valor) }}</strong>

        </div>
    </div>

    <div class="card" style="margin-top: 5px">
        <div style="text-align: left">
            Código PIX Válido até:
            {{ \Carbon\Carbon::parse($pix->transacao_data_criacao, 'UTC')->timezone('America/Sao_Paulo')->addSeconds($pix['calendario']['expiracao'])->format('d/m/Y H:i:s') }}
            - Horário de Brasília
        </div>
    </div>
    <div class="card" style="margin-top: 5px">
        <div class="text-center">
            <p>Para usar o "Copia e Cola" copie o código e cole no aplicativo do seu banco para completar o pagamento.
            </p>
            <button class="btn" data-clipboard-text="{{ $pix->copia_e_cola }}" style="margin-top: 20px">CLIQUE PARA
                COPIAR</button>
        </div>
    </div>

    <script src="{{ asset('assets/js/clipboard.min.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>
    <script>
        var clipboard = new ClipboardJS('.btn');
        clipboard.on('success', function(e) {
            Swal.fire({
                // title: 'QrCode',
                text: 'Código copiado com sucesso!',
                icon: 'success',
                confirmButtonText: 'Fechar',
                showCloseButton: true
            });

            // console.info('Action:', e.action);
            // console.info('Text:', e.text);
            // console.info('Trigger:', e.trigger);
            e.clearSelection();
        });
    </script>
</body>

</html>
