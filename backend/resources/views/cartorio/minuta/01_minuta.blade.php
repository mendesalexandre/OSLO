<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Termo de Responsabilidade pela Guarda e uso de Equipamento de Trabalho</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/bootstrap.css') }}">

</head>

<body class="py-2" style="font-size: 13pt ;">

    <div>
        {!! $minuta->texto !!}
    </div>
    <div>
        <td class="text-center" style="line-break: anywhere;">
            <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(150)->generate($minuta->id)) !!} ">
        </td>
    </div>

</body>

</html>
