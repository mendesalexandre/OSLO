<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>titulo</title>
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
    <table class="table table-bordered mb-0 ">
        <tr>
            <td style="width:15%">
                <div>
                    <img src="{{ asset('/assets/images/logo.png') }}" class="w-100">
                </div>
            </td>
            <td style="width:85%" class="text-center">
                <span class="font-weight-bold text-uppercase d-block h6">
                    {{ $cartorio->nome }}
                </span>
                {{-- <span class="font-weight-bold text-uppercase d-block">
                    {{ $cartorio-> }}
                </span> --}}
                <span class="font-weight-bold text-uppercase d-block h6">
                    {{ $oficial->nome }}
                </span>

                <span class="font-weight-bold text-uppercase d-block h6">
                    {{ $oficial->cargo->nome }}
                </span>
            </td>
        </tr>
    </table>
    <table class="table table-bordered">
        <tr>
            <td class="text-center font-weight-bold h3 text-uppercase"> Adiantamento Salarial</td>
        </tr>
        <tr>
            <td class="h5">
                Nome do Colaborador: <b>Alexandre Teixeira Mendes</b>
            </td>
        </tr>

        <tr>
            <td class="h5">
                Nome do Empregador: <b>Aparecida Maria Hartmann</b>
            </td>
        </tr>

        <tr>
            <td class="h5">
                Cartorio 1º Ofício - CNPJ: {{ formatarCnpjCpf($cartorio->cnpj) }}
            </td>
        </tr>

        <tr>
            <td class="h5 text-justify">
                <p>Declaro, para todos os efeitos, ter solicitado a título de "Adiantamento Salarial", a importância de
                    R$ <b>3.000,00</b> (Três Mil Reais), e em consonância com o disposto no art. 462, caput, da CLT,
                    tenho a plena ciência de que o respectivo valor será descontado juntamente com os impostos
                    necessários, pelo empregador, quando do pagamento da minha remuneração mensal relativa à folha de
                    pagamento do mês de Dezembro/2023.
                </p>
                <p>Sinop, 11 de dezembro de 2023.</p>
            </td>
        </tr>


    </table>


</body>

</html>
