<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Solicitação de Devolução</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/bootstrap.css') }}">
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
    <table class="table table-bordered m-0">
        <tr>
            <td class="text-center font-weight-bold h5 text-uppercase"
            > Solicitação de Devolução de Valores</td>
        </tr>
    </table>
    <table class="table table-bordered m-0">
        <tr>
            <th style="width: 20%">Ordem de Serviço</th>
            <td>461064</td>

             <th style="width: 20%">Atendente</th>
            <td>Alexandre</td>
        </tr>
    </table>
    <table class="table table-bordered m-0">

        <tr>
             <th style="width: 20%">Solicitante</th>
            <td colspan="3">Alexandre</td>
        </tr>
    </table>
    <table class="table table-bordered m-0">
        <tr>
             <th style="width: 20%">Data de Entrega no Financeiro</th>
            <td >11/12/2024</td>

             <th style="width: 20%">Data de Realização da Devolução</th>
            <td >11/12/2024</td>
        </tr>
    </table>

    <table class="table table-bordered m-0">
        <tr>
             <th style="width: 20%">Item a ser devolvido</th>
            <td >11/12/2024</td>
        </tr>
    </table>

    <table class="table table-bordered table-striped m-0">
        <tr>
             <th style="width: 20%">Item a ser creditado</th>
            <td >11/12/2024</td>
        </tr>
    </table>
    <table class="table table-bordered table-striped m-0">
        <tr>
             <th style="width: 20%">Motivo da Devolução</th>
            <td >11/12/2024</td>
        </tr>
    </table>


    {{-- DADOS BANCÁRIOS --}}

    <table class="table table-bordered m-0">
        <tr>
            <td class="text-center font-weight-bold h5 text-uppercase">Dados Bancários para Devolução</td>
        </tr>
    </table>

    <table>
        <tr>
            <th style="width: 20%">Banco</th>
            <td>Itaú</td>
            <th style="width: 20%">Agência</th>
            <td>1234</td>
            <th style="width: 20%">Conta</th>
            <td>12345-6</td>
        </tr>
    </table>


</body>

</html>
