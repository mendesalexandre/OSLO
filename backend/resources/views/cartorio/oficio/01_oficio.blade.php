<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ofício</title>
    <style>
        html {
            box-sizing: border-box;
            /* border: 3px solid #000 !important; */
            /* background: #ccc; */
            color: #000;
            height: 100%;
            width: 100%;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0 !important;
            padding: 0 !important;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }
    </style>
</head>

<body>
    <table>
        <tr>
            <td>
                <table style="width: 50%;color:#000;">
                    <tr>
                        <td>
                            <div
                                style="border-radius:10%;border:2px solid #000;width: 130px; height: 100px;text-align: center;">
                                <p style="text-transform: uppercase;padding: 0;margin: 0; margin-top:10px">Matrícula</p>
                                <p style="font-size: 20px"><strong>102333</strong></p>
                            </div>
                        </td>
                        <td>
                            <div
                                style="border-radius:10%;border:2px solid #000;width: 100px; height: 100px;text-align: center;">
                                <p style="text-transform: uppercase;padding: 0;margin: 0; margin-top:10px">Ficha</p>
                                <p style="font-size: 20px"><strong>1</strong></p>
                            </div>
                        </td>
                        <td>
                            <div
                                style="border-radius:10%;border:2px solid #000;width: 130px; height: 100px;text-align: center;">
                                <p style="text-transform: uppercase;padding: 0;margin: 0; margin-top:10px">Rubrica</p>
                                <p style="font-size: 20px"><strong>1</strong></p>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 100%;color:#000;">
                <table style="width: 50%;color:#000;">
                    <tr>
                        <td style="width: 100%">
                            <div
                                style="
                                border-top-left-radius: 10px;
                                border-bottom-left-radius: 10px;

                                border-top-right-radius: 10px;
                                border-bottom-right-radius: 10px;
                                border:5px solid #000; height: 100px; width: 700px;text-align: center;">


                                <div style="margin-top: 5px">
                                    <p
                                        style="font-size: 20px;font-weight:bold; text-transform: uppercase;padding: 0;margin: 0">
                                        Cartório de Registro de Imóveis</p>
                                    <p
                                        style="font-size: 35px;text-transform: uppercase;padding: 0;margin: 0;font-weight: bold;">
                                        1 Ofício de Sinop - Mato Grosso</strong></p>
                                    <p
                                        style="font-size: 30px;text-transform: uppercase;padding: 0;margin: 0;
                                    font-family:'Times New Roman'">

                                        <strong>Livro
                                            Nº 02 - Registro Geral</strong>
                                    </p>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    {{-- LINHA DE SEPARAÇÃO --}}
    <div style="height: 5px;background:#000;width: 100%;"></div>
    <div style="margin-top: 10px;">
        <div style="font-size: 20pt;line-height: 1.5; color: #000;">
            {!! $oficio->texto !!}
        </div>
    </div>


</body>

</html>
