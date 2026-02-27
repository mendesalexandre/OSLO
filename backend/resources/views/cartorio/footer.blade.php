<!DOCTYPE html>
<html lang="pt-BR" style="margin:0; padding:0;">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
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
    <table class="w-100">
        {{-- <tr>
            <td class="text-center">
                <small>Celular/WhatsApp: {{ $cartorio->celular_1 }} </small>
                <small> - E-mail: {{ $cartorio->email }}</small>
                <small> - Site: https://www.1oficiosinop.com.br</small>
            </td>
        </tr> --}}

        <tr>
            <td class="text-left">
                @if (auth()->user())
                    <small>
                        Documento gerado por: {{ auth()->user()->name }} em
                        {{ Carbon\Carbon::now('America/Cuiaba')->format('d/m/Y H:i:s') }}
                    </small>
                @endif
            </td>
            <td class="text-right">
                <small>
                    Página <span class="page"></span> de <span class="topage"></span>
                </small>
            </td>
        </tr>
    </table>
    <div style="height: 5px;background:#000;width: 100%;"></div>
</body>

</html>
