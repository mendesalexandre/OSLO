<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Relatório de PIX - Pagos</title>
</head>

<body>
    <table>
        <tr>
            <th>Período: {{ $dataInicial }} a {{ $dataFinal }}</th>
        </tr>
    </table>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>TXID</th>
                <th>O.S</th>
                <th>Histórico</th>
                <th>Dt. Pagamento</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $pix->id }}</td>
                <td>{{ $pix->txid }}</td>
                <td>{{ $pix->idos }}</td>
                <td>{{ $pix->data_pagamento }}</td>
                <td>{{ $pix->devedor }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>
