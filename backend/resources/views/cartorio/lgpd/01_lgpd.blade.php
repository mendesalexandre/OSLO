<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>LGPD</title>

    <style>
        .h1 {
            text-align: center;
        }

        .w-100 {
            width: 100%;
        }

        .table {
            border-collapse: collapse;
            width: 100%;
        }

        .td,
        .th {
            border: 1px solid #000;
            padding: 8px;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }
    </style>
</head>

<body>
    <h4 class="text-center">Direitos do Titular - LGPD - Lei 13.709/2018</h4>

    <p>Os direitos do titular dos dados pessoais são garantidos pela LGPD, Lei 13.709/2018, e podem ser exercidos
        mediante requisição do titular dos dados pessoais, nos termos da legislação vigente.</p>

    <table class="w-100 table">
        <tr>
            <th class="th text-left">ID</th>
            <th class="th text-left">Nome</th>
            <th class="th text-left">CPF</th>
            <th class="th text-left">RG</th>
        </tr>
        <tr>
            <td class="td text-left">{{ $parte['idindicadorpessoal'] }}</td>
            <td class="td text-left">{{ $parte['nome'] }}</td>
            <td class="td text-left">{{ $parte['cpf_cgc'] }}</td>
            <td class="td text-left">{{ $parte['rg_ins'] }}</td>
        </tr>
    </table>

    <div class="w-100">
        <h4>Dados constante no Livro 01</h4>
    </div>
    <table class="table w-100">
        <tr>
            <th class="th text-left">Protocolo</th>
            <th class="th text-left">Apresentante</th>
            <th class="th text-left">Data Protocolo</th>
            <th class="th text-left">Horário</th>
        </tr>
        @foreach ($parte['protocolo_parte'] as $item)
            <tr>
                <td class="td text-left">{{ $item['ordem'] }}</td>
                <td class="td text-left">{{ $item['apresentante'] }}</td>
                <td class="td text-left">{{ date('d/m/Y', strtotime($item['dataprotocolo'])) }}</td>
                <td class="td text-left">{{ $item['horario'] }}</td>
            </tr>
        @endforeach
    </table>

    <div class="w-100">
        <h4>Dados constante no Livro 02</h4>
        <table class="table w-100">
            <tr>
                {{-- <th class="th text-left">Nome</th> --}}
                <th class="th text-left">Participação</th>
                <th class="th text-left">Data Ato</th>
                <th class="th text-left">Matrícula</th>
                <th class="th text-left">Nº Ato</th>
                <th class="th text-left">CNM</th>
                <th class="th text-left">Protocolo</th>
                <th class="th text-left">Forma do Título</th>
            </tr>
            @foreach ($parte['matricula_ato_parte'] as $item)
                <tr>
                    {{-- <td class="td">{{ $item['nome'] }}</td> --}}
                    <td class="td">{{ $item['tipoqualificacao'] }}</td>
                    <td class="td">{{ date('d/m/Y', strtotime($item['dataato'])) }}</td>
                    <td class="td">{{ $item['matricula'] }}</td>
                    <td class="td">{{ $item['nrato'] }}</td>
                    <td class="td">{{ $item['cin'] }}</td>
                    <td class="td">{{ $item['protocolo'] }}</td>
                    <td class="td">{{ $item['descricao'] }}</td>
                </tr>
            @endforeach
        </table>

    </div>


    {{-- RODAPE - ARTIGO 29 --}}
    {{--
        Art. 29 Na informação, que poderá ser prestada por meio eletrônico, seguro e
        idôneo ou sob a forma impressa, constará a advertência de que foi entregue ao titular dos
        dados pessoais, na forma da Lei nº 13.709/18, e que não produz os efeitos de certidão, não
        sendo dotada de fé pública para prevalência de direito perante terceiros.  --}}

    {{-- DADOS CONSTANTE NO LIVRO 01 --}}
    {{-- DADOS CONSTANTE NO LIVRO 02 --}}
    {{-- DADOS CONSTANTE NO LIVRO 03 --}}
    {{-- DADOS CONSTANTE NO LIVRO 04 --}}
    {{-- DADOS CONSTANTE NO LIVRO 05 --}}

</body>

</html>
