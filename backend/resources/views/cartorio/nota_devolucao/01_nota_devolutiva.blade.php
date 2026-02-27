<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota de Devolução {{ $notaDevolutiva->id }}</title>
    <style>
        html {
            box-sizing: border-box;
            border: 3px solid #fff !important;
            /* border: 3px solid #000 !important; */
            /* background: #ccc; */
            color: #000;
            height: 100%;
            width: 100%;
            /* position: relative; */
        }

        #watermark {
            /* box-sizing: border-box; */
            /* position: absolute; */
            width: 100%;
            height: 100%;
            /* height: calc(100% - 10px); */
            /* top: 50%;
            left: 50%; */
            /* opacity: 0.8; */
            z-index: 99;
            color: #ccc;
            /* transform-origin: 0 0; */
            /* transform: rotate(300deg); */
            font-size: 90px;
            background: red;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0 !important;
            padding: 10px !important;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }


        .border {
            border: 1px solid #000;
        }

        td {
            border: 1px solid #000;
            padding: 5px;
        }
    </style>
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
    <table class="border">
        <tr>
            <td>
                <b>Nota de Devolução: {{ str_pad($notaDevolutiva->id, 4, '0', STR_PAD_LEFT) }}</b>
            </td>
            <td>
                {{-- <b>{{ date('d/m/Y', strtotime($notaDevolutiva->data_cadastro)) }}</b> --}}
                <b>
                    {{ formatarDataPorExtenso(date('Y-m-d', strtotime($notaDevolutiva->data_cadastro))) }}
                </b>
            </td>
        </tr>
    </table>

    <table class="border">
        <tr>
            <td>
                <b>Cartório: {{ $cartorio->nome }}</b>
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <td>
                <b>Protocolo: {{ $notaDevolutiva->protocolo }}</b>
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <td>
                <b>Requerente: {{ $notaDevolutiva->requerente }}</b>
            </td>
        </tr>
    </table>


    <table>
        <tr>
            <td>
                {!! $notaDevolutiva->texto !!}
            </td>
        </tr>
    </table>

</body>

</html>
