<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Recibo - Duas Vias</title>
  <style>
    body {
      font-family: 'Arial', sans-serif;
      font-size: 12px;
      margin: 20px 40px;
    }

    .recibo {
      border: 2px solid #000;
      padding: 20px;
      margin-bottom: 20px;
      height: 360px;
    }

    .titulo {
      text-align: center;
      font-size: 18px;
      font-weight: bold;
      margin-bottom: 20px;
      text-transform: uppercase;
    }

    .conteudo {
      line-height: 1.8;
    }

    .assinatura {
      margin-top: 60px;
      text-align: center;
    }

    .assinatura div {
      margin-top: 50px;
      border-top: 1px solid #000;
      width: 300px;
      margin-left: auto;
      margin-right: auto;
      font-size: 12px;
    }

    .data-local {
      margin-top: 40px;
      text-align: right;
    }

    .linha-corte {
      border-top: 1px dashed #000;
      margin: 30px 0;
      text-align: center;
      position: relative;
    }

    .linha-corte::after {
      content: "Recorte aqui";
      position: absolute;
      top: -10px;
      left: 50%;
      transform: translateX(-50%);
      background: #fff;
      padding: 0 10px;
      font-size: 11px;
      color: #555;
    }
  </style>
</head>
<body>
  @for ($i = 0; $i < 2; $i++)
    <div class="recibo">
      <div class="titulo">Recibo</div>

      <div class="conteudo">
        Recebi(emos) de <strong>{{ $recibo['pagador'] }}</strong>,
        a quantia de <strong>R$ {{ number_format($recibo['valor'], 2, ',', '.') }}</strong>
        ({{ $recibo['valor_extenso'] }}),
        referente a <strong>{{ $recibo['referente'] }}</strong>.
      </div>

      <div class="data-local">
        {{ $recibo['cidade'] ?? 'Cidade' }}, {{ \Carbon\Carbon::parse($recibo['data'])->format('d/m/Y') }}
      </div>

      <div class="assinatura">
        <div>{{ $recibo['assinante'] ?? 'Assinante' }}</div>
      </div>
    </div>

    @if ($i === 0)
      <div class="linha-corte"></div>
    @endif
  @endfor
</body>
</html>
