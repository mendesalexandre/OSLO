<!DOCTYPE html>

<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}"> --}}

    <script>
        function subst() {
            function subst() {
                var vars = {};
                var query_strings_from_url = document.location.search.substring(1).split('&');
                for (var query_string in query_strings_from_url) {
                    if (query_strings_from_url.hasOwnProperty(query_string)) {
                        var temp_var = query_strings_from_url[query_string].split('=', 2);
                        vars[temp_var[0]] = decodeURI(temp_var[1]);
                    }
                }
                var css_selector_classes = ['page', 'frompage', 'topage', 'webpage', 'section', 'subsection', 'date',
                    'isodate', 'time', 'title', 'doctitle', 'sitepage', 'sitepages'
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
        }
    </script>
</head>

<body onload="subst()" class="border">
    <table class="w-100">
        <tr>
            <td style="width:15%">
                <div class="logo">
                    {{-- <img src="{{ asset('/assets/images/logo_aros.jpg') }}" class="w-100"> --}}
                </div>
            </td>
            <td style="width:85%" class="border-left text-center">
                <span class="block text-bold font-times font-size-20">{{ $cartorio->nome }}</span>
                <span class="block text-bold font-times font-size-20">{{ $cartorio->cpf_cnpj }}</span>
            </td>
        </tr>
    </table>
</body>

</html>
