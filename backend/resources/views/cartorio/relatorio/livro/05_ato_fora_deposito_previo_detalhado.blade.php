<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <title>Livro de Simples Conferência</title>

 <style>
    body {
      font-size: 11px;
      font-family: Arial, Helvetica, sans-serif;
      margin: 0;
      padding: 0;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    thead {
      background-color: #000;
    }

    thead th {
      padding-top: 12px;
      padding-bottom: 8px;
      color: #000;
    }

    th,
    td {
      border: 1px solid #000;
      padding: 6px;
    }

    .text-center {
      text-align: center;
    }

    .text-right {
      text-align: right;
    }

    .text-left {
      text-align: left;
    }

    .text-uppercase {
      text-transform: uppercase;
    }

    .text-bold {
      font-weight: bold;
    }

    .font-size-20 {
      font-size: 20px;
    }

    .font-size-16 {
      font-size: 16px;
    }

    .text-negative {
      color: red;
    }

    tr:nth-child(odd) {
      background: #fff;
    }

    tr:nth-child(even) {
      background: #f3f3f3;
    }

    tr {
      page-break-inside: avoid;
    }

    @media print {
      table {
        page-break-inside: auto;
      }

      tr {
        page-break-inside: avoid;
        page-break-after: auto;
      }

      thead {
        display: table-header-group;
      }

      tfoot {
        display: table-footer-group;
      }
    }
  </style>
</head>
<body>
@php
  $index = 1;
  $totalCredito = 0;
  $totalCreditoGeral = 0;
  $totalEmolumentos = 0;
  $totalFundoJudiciario = 0;
  $totalRegistroCivil = 0;
  $totalIssqn = 0;
  $totalSelado = 0;
  $totalDevolvido = 0;
  $saldo = 0;
  $totalAtosSeladosNoMesmoDia = 0;
  $totalAtosNaoDepositoPrevio = 0;
@endphp

<table style="margin-top: 10px;">
  <tr>
    <td class="text-center font-size-16">Período: {{ date('d/m/Y', strtotime($dataInicial)) }} a {{ date('d/m/Y', strtotime($dataFinal)) }}</td>
  </tr>
</table>

@php $isFirst = true; @endphp
@foreach ($protocolos as $tipoDepositoPrevio => $atos)
  @foreach (array_chunk($atos, 22) as $i => $grupo)
    <div style="{{ !$loop->first || !$loop->parent->first ? 'page-break-before: always;' : '' }}">
      <table style="margin-top: 20px;" class="table table-striped table-bordered">
        <thead>
          <tr>
            <th style="width: 3%">#</th>
            <th style="width: 5%">Nr. O.S</th>
            <th style="width: 5%">Data Caixa</th>
            <th style="width: 30%" class="text-left">Descrição</th>
            <th style="width: 5%" class="text-center">
              {{ trim($tipoDepositoPrevio) == 'Sim' ? 'Selado' : 'Finalizado' }}
            </th>
            <th style="width: 5%" class="text-center">Devolução</th>
            <th style="width: 2%" class="text-right">QTD.</th>
            <th style="width: 5%" class="text-right">Crédito</th>
            <th style="width: 5%" class="text-right">Emolumentos</th>
            <th style="width: 8%" class="text-right">Fundo Judiciário</th>
            <th style="width: 8%" class="text-right">Registro Civil</th>
            <th style="width: 6%" class="text-right">ISSQN (R$)</th>
            <th style="width: 8%" class="text-center">Depósito Prévio?</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($grupo as $item)
            @php
              $isDevolvido = !empty($item['datadevolucao']);
              $isSelado = !empty($item['dataselagem']);
              $classeLinha = $isDevolvido ? 'text-negative' : ($isSelado ? '' : '');

              $totalCredito += round($item['credito'], 2);
              $totalCreditoGeral += round($item['credito'], 2);
              $totalEmolumentos += round($item['emolumentos'], 2);
              $totalFundoJudiciario += round($item['fundojudiciario'], 2);
              $totalRegistroCivil += round($item['registrocivil'], 2);
              $totalIssqn += round($item['valor_issqn'], 2);
              if ($isSelado) $totalSelado += round($item['credito'], 2);
              if ($isDevolvido) $totalDevolvido += round($item['credito'], 2);
              if ($isSelado && $item['dataselagem'] === $item['datacaixa'] && trim($item['atodepositoprevio']) === 'Sim') {
                $totalAtosSeladosNoMesmoDia += round($item['credito'], 2);
              }
              if (trim($item['atodepositoprevio']) === 'Não') {
                $totalAtosNaoDepositoPrevio += round($item['credito'], 2);
              }

              $saldo = $totalCreditoGeral - ($totalSelado + $totalDevolvido);
            @endphp
            <tr class="{{ $classeLinha }}">
              <td class="text-center">{{ $index++ }}</td>
              <td class="text-center">{{ $item['idos'] ?? '' }}</td>
              <td class="text-center">{{ $item['datacaixa_br'] ?? '' }}</td>
              <td class="text-left">{{ $item['descricao'] ?? '' }}</td>
              <td class="text-center">
                {{ $item['dataselagem'] ? date('d/m/Y', strtotime($item['dataselagem'])) : ($item['datafinalizacao'] ? date('d/m/Y', strtotime($item['datafinalizacao'])) : '') }}
              </td>
              <td class="text-center">
                {{ $item['datadevolucao'] ? date('d/m/Y', strtotime($item['datadevolucao'])) : '' }}
              </td>
              <td class="text-right">{{ number_format($item['qtd'] ?? 0, 0) }}</td>
              <td class="text-right">{{ number_format($item['credito'] ?? 0, 2, ',', '.') }}</td>
              <td class="text-right">{{ number_format(round($item['emolumentos'] ?? 0, 2), 2, ',', '.') }}</td>
              <td class="text-right">{{ number_format($item['fundojudiciario'] ?? 0, 2, ',', '.') }}</td>
              <td class="text-right">{{ number_format($item['registrocivil'] ?? 0, 2, ',', '.') }}</td>
              <td class="text-right">{{ number_format($item['valor_issqn'] ?? 0, 2, ',', '.') }}</td>
              <td class="text-center">{{ $item['atodepositoprevio'] ?? '' }}</td>
            </tr>
          @endforeach

          @if ($loop->last)
          <tr class="text-bold">
            <td colspan="7"></td>
            <td class="text-right">{{ number_format($totalCredito, 2, ',', '.') }}</td>
            <td class="text-right">{{ number_format($totalEmolumentos, 2, ',', '.') }}</td>
            <td class="text-right">{{ number_format($totalFundoJudiciario, 2, ',', '.') }}</td>
            <td class="text-right">{{ number_format($totalRegistroCivil, 2, ',', '.') }}</td>
            <td class="text-right">{{ number_format($totalIssqn, 2, ',', '.') }}</td>
            <td></td>
          </tr>
          @endif
        </tbody>
      </table>
    </div>
  @endforeach

  @php
    $index = 1;
    $totalCredito = 0;
    $totalEmolumentos = 0;
    $totalFundoJudiciario = 0;
    $totalRegistroCivil = 0;
    $totalIssqn = 0;
  @endphp
@endforeach
<div class="w-full">
    <div style="width: 50%">
        <table style="margin-top: 20px;">
            <tbody>
              <tr>
                <td colspan="2" class="text-center text-bold text-uppercase font-size-16" style="background-color: #ccc;">
                  Resumo / Fechamento
                </td>
              </tr>
              <tr>
                <th class="text-left text-uppercase">Total Crédito</th>
                <td class="text-right">R$ {{ number_format($totalCreditoGeral, 2, ',', '.') }}</td>
              </tr>
              <tr>
                <th class="text-left text-uppercase">Total Selado</th>
                <td class="text-right">R$ {{ number_format($totalSelado, 2, ',', '.') }}</td>
              </tr>
              <tr>
                <th class="text-left text-uppercase">Total Devolvido</th>
                <td class="text-right">R$ {{ number_format($totalDevolvido, 2, ',', '.') }}</td>
              </tr>
              <tr>
                <th class="text-left text-uppercase">Saldo</th>
                <td class="text-right">R$ {{ number_format($saldo, 2, ',', '.') }}</td>
              </tr>
              {{-- <tr>
                <th class="text-left text-uppercase" style="padding:10px">Total Selado Mesmo Dia</th>
                <td class="text-right" style="padding:10px">R$ {{ number_format($totalAtosSeladosNoMesmoDia, 2, ',', '.') }}</td>
              </tr> --}}
              {{-- <tr>
                <th class="text-left text-uppercase" style="padding:10px">Saldo</th>
                <td class="text-right text-bold" style="padding:10px">R$ {{ number_format($totalCreditoGeral - ($totalSelado + $totalDevolvido), 2, ',', '.') }}</td>
              </tr> --}}
            </tbody>
          </table>
    </div>
    <div
    style="width: 50%;"
    ></div>
</div>

</body>
</html>
