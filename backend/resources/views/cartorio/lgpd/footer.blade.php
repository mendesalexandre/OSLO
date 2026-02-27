<!DOCTYPE html>
<html lang="pt-BR" style="margin:0; padding:0;">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}"> --}}
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

<body style="margin:0; padding:0;" onload="subst()">
    <table class="w-100 font-20" style="border-top: 1px solid black">
        <tr>
            <td colspan="2" class="text-center font-size-12 font-times">
                <span class="block">Contato: {{ $cartorio->celular_1 }} e {{ $cartorio->celular_2 }} – E-mail:
                    {{ $cartorio->email }}</span>
                {{-- <span class="block"> {{ $empresa->endereco ?? 'Endereço não informado' }},
                    {{ $empresa->numero ?? 'Número não informado' }},
                    {{ $empresa->complemento ? ' - ' . $empresa->complemento : '' }}
                    {{ $empresa->cidade->nome }}
                    / {{ $empresa->estado->nome ? $empresa->estado->nome : 'Estado não informado' }} - CEP:
                    {{ $empresa->cep }}
                </span> --}}
            </td>
        </tr>

        <tr>
            <td class="text-left font-size-10">
                @if (auth()->user())
                    Documento gerado por: {{ auth()->user()->name }} em
                    {{ Carbon\Carbon::now('America/Cuiaba')->format('d/m/Y H:i:s') }}
                @endif
            </td>
            <td style="text-align:right" class="font-size-10"">
                Página <span class="page"></span> de <span class="topage"></span>
            </td>
        </tr>
    </table>
</body>

</html>
