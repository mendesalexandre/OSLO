<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificação de E-mail - OSLO</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', Arial, sans-serif;
            background: #F5F6F8;
            margin: 0;
            padding: 40px 20px;
            line-height: 1.6;
        }
        .container {
            max-width: 520px;
            margin: 0 auto;
            background: #FFFFFF;
            border-radius: 8px;
            border: 1px solid #E8EAED;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        .header {
            background: #1A1A1A;
            padding: 32px;
            text-align: center;
        }
        .header h1 {
            color: #FF7A00;
            font-size: 28px;
            font-weight: 700;
            margin: 0;
            letter-spacing: 1px;
        }
        .body {
            padding: 32px;
        }
        .body h2 {
            color: #202124;
            font-size: 20px;
            font-weight: 600;
            margin: 0 0 8px;
        }
        .body p {
            color: #5F6368;
            font-size: 14px;
            line-height: 1.6;
            margin: 12px 0;
        }
        .body strong {
            color: #202124;
            font-weight: 600;
        }
        .btn {
            display: inline-block;
            background: #FF7A00;
            color: #FFFFFF !important;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            margin: 24px 0;
            transition: background 0.2s;
        }
        .btn:hover {
            background: #E66D00;
        }
        .trial-box {
            background: #FFF7F0;
            border: 1px solid #FFE4CC;
            border-radius: 6px;
            padding: 16px;
            margin: 24px 0;
        }
        .trial-box p {
            color: #92400E;
            font-size: 13px;
            margin: 4px 0;
        }
        .trial-box strong {
            color: #7C2D12;
        }
        .footer {
            padding: 24px 32px;
            border-top: 1px solid #F1F3F4;
            background: #FAFAFA;
        }
        .footer p {
            color: #9AA0A6;
            font-size: 12px;
            margin: 4px 0;
            text-align: center;
        }
        .footer a {
            color: #FF7A00;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏛 OSLO</h1>
        </div>
        <div class="body">
            <h2>Olá, {{ $nome }}!</h2>
            <p>Sua conta na empresa <strong>{{ $empresa }}</strong> foi criada com sucesso.</p>

            <div class="trial-box">
                <p><strong>✨ Período de teste gratuito:</strong></p>
                <p>Você tem {{ $diasTrial }} dias para testar todas as funcionalidades do sistema, sem compromisso.</p>
            </div>

            <p>Clique no botão abaixo para <strong>confirmar seu e-mail</strong> e começar a usar o OSLO:</p>

            <center>
                <a href="{{ $url }}" class="btn">Confirmar E-mail</a>
            </center>

            <p style="color: #9AA0A6; font-size: 12px; margin-top: 24px;">
                Se você não criou esta conta, ignore este e-mail.<br>
                Este link expira em 24 horas.
            </p>
        </div>
        <div class="footer">
            <p><strong>OSLO</strong> — Sistema de Gestão Cartorária</p>
            <p>Precisa de ajuda? Entre em contato pelo <a href="mailto:oi@sistemaoslo.com.br">oi@sistemaoslo.com.br</a></p>
        </div>
    </div>
</body>
</html>
