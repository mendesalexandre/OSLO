<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 90px 70px;
            padding: 20px 0 0 0;
        }

        .qr-code-fixed {
            width: 150px !important;
            height: 150px !important;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: -moz-crisp-edges;
            image-rendering: crisp-edges;
            image-rendering: pixelated;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        *,
        *::before,
        *::after {
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        /* SNAPPY PDF */
        table {
            /* page-break-inside: auto; */
            /* border-collapse: collapse !important; */
            border-collapse: collapse;
            table-layout: fixed;
            /* width: 310px; */
        }

        table td {
            word-wrap: break-word;
        }

        tr {
            /* page-break-inside: avoid;
    page-break-after: auto; */
        }

        thead {
            /* display: table-header-group; */
        }

        tfoot {
            /* display: table-footer-group; */
        }

        .page {
            overflow: hidden;
            page-break-after: always;
        }

        th,
        td {
            /* padding: 5px; */
        }

        .cabecalho {
            border: 1px solid #000;
            padding: 10px;
        }

        .table {
            border-collapse: collapse;
        }

        .tfoot {
            border: 1px solid #000;
            background-color: #dcdcdc;
        }

        .td,
        .th {
            padding: 2px 5px;
        }

        .font-arial {
            font-family: Arial, Helvetica, sans-serif !important;
        }

        /* WIDTH */
        .w-100 {
            width: 100%;
        }

        .w-50 {
            width: 50%;
        }

        .w-20 {
            width: 20%;
        }

        .w-30 {
            width: 30%;
        }

        .w-40 {
            width: 40%;
        }

        .w-60 {
            width: 60%;
        }

        .w-70 {
            width: 70%;
        }

        .w-80 {
            width: 80%;
        }

        .w-90 {
            width: 90%;
        }

        .h-100 {
            height: 100%;
        }

        .h-screen {
            height: 100vh;
        }

        .w-screen {
            width: 100vw;
        }

        /* TEXT */

        .text-wrap {
            word-break: break-all !important;
            white-space: normal !important;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-justify {
            text-align: justify;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .text-lowercase {
            text-transform: lowercase;
        }

        .text-bold {
            font-weight: bold;
        }

        .font-size-6 {
            font-size: 6px;
        }

        .font-size-8 {
            font-size: 8px !important;
        }

        .font-size-10 {
            font-size: 10px !important;
        }

        .font-size-12 {
            font-size: 12px;
        }

        .font-size-14 {
            font-size: 14px;
        }

        .font-size-16 {
            font-size: 16px;
        }

        .font-size-18 {
            font-size: 18px;
        }

        .font-size-20 {
            font-size: 20px;
        }

        .font-size-22 {
            font-size: 22px;
        }

        .font-size-24 {
            font-size: 24px;
        }

        /* PADDING AND MARGIN */
        .no-padding {
            padding: 0 !important;
        }

        .no-margin {
            margin: 0 !important;
        }

        .padding-5 {
            padding: 5px;
        }

        .padding-left-10 {
            padding-left: 10px !important;
        }

        /* BORDER */

        .border {
            border: 1px solid #000;
        }

        .border-top {
            border-top: 1px solid #000;
        }

        .border-bottom {
            border-bottom: 1px solid #000;
        }

        .border-left {
            border-left: 1px solid #000;
        }

        .border-right {
            border-right: 1px solid #000;
        }

        .border-radius {
            border-radius: 5px;
        }

        .no-border-radius {
            border-radius: 0 !important;
        }

        /* IMG /LOGO */
        img {
            width: 100%;
        }

        .logo {
            background-color: red;
            width: 100%;
        }

        /* PAGES */
        .page-break {
            page-break-after: always;
        }

        .qrcode {
            width: 100px;
            height: 100px;
        }

        .red {
            background-color: red;
        }

        .green {
            background-color: green;
        }

        .blue {
            background-color: blue;
        }

        .float-left {
            float: left;
        }

        .height-100 {
            height: 100%;
        }

        .block {
            display: block;
        }

        .none {
            display: none;
        }

        table {
            /* border-collapse: collapse; */
            /* border-spacing: 0; */
            width: 100%;
        }

        .border-none {
            border: none !important;
        }

        .box {
            border: 1pt solid black;
            position: absolute;
            width: 28.2cm;
            height: 19.3cm;
        }

        section {
            position: relative;
        }

        .textarea {
            width: 100%;
            height: 100px;
            border: none;
            outline: none;
            resize: none;
            overflow: hidden;
            color: #000;
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            text-align: justify !important;
            /* background-color: red; */
        }

        .textarea::-webkit-scrollbar {
            width: 0px;
            background: transparent;
        }

        .textarea::-webkit-scrollbar-thumb {
            background: transparent;
        }

        .separator {
            border-bottom: 1px solid #000;
        }
    </style>

</head>

<body>
    <div class="texto-informacao">
        <p style="font-size: 10.0pt; font-family: 'Times New Roman'; text-align: justify;">
            <strong>CERTIFICO E DOU FÉ</strong> que a presente certidão é a exata reprodução da matrícula nº
            {{ $data['certidao_matricula_numero'] }}, e tem valor de Certidão de Inteiro Teor, emitida nos termos do
            artigo 19, § 11, da Lei nº 6.015/73, conforme a redação dada pela Lei 14.382/2022. "§11. No âmbito do
            registro de imóveis, a certidão de inteiro teor da matrícula contém a reprodução de todo seu conteúdo e é
            suficiente para fins de comprovação de propriedade, direitos, ônus reais e restrições sobre o imóvel,
            independentemente de certificação específica pelo oficial". Sinop-MT,
            {{ formatarDataPorExtenso(\Carbon\Carbon::parse($data['integrador_selo_data'])->format('Y-m-d')) }}, às
            {{ $data['integrador_selo_hora'] }}.
        </p>

        <p class="MsoNormal"
            style="mso-margin-top-alt: auto; mso-margin-bottom-alt: auto; text-align: justify; line-height: normal;">
            <strong>
                <span
                    style="font-size: 10.0pt; font-family: 'Times New Roman','serif'; mso-fareast-font-family: 'Times New Roman'; mso-fareast-language: PT-BR;">CERTIFICO</span>
            </strong>
            <span
                style="font-size: 10.0pt; font-family: 'Times New Roman','serif'; mso-fareast-font-family: 'Times New Roman'; mso-fareast-language: PT-BR;">
                ainda, que quando extraída sob a forma de documento eletrônico nos termos do art. 19, §§ 1º e 5º, da Lei
                nº 6.015/73 </span>
        </p>



        <p class="MsoNormal"
            style="mso-margin-top-alt: auto; mso-margin-bottom-alt: auto; text-align: justify; line-height: normal;">
            <span
                style="font-size: 10.0pt; font-family: 'Times New Roman','serif'; mso-fareast-font-family: 'Times New Roman'; mso-fareast-language: PT-BR;">(*)
                mediante processo e assinatura por certificação digital no âmbito da ICP-Brasil, nos termos da
                legislação vigente, deverá ser conservada em meio eletrônico e confirmada sua autenticidade
                em&nbsp;<span
                    style="text-decoration: underline;">https://registradores.onr.org.br/validacao.aspx</span>, ou
                clicando diretamente no link lateral ou via leitura do QR-Code menor ao final de cada página.</span>
        </p>

        <p class="MsoNormal"
            style="mso-margin-top-alt: auto; mso-margin-bottom-alt: auto; text-align: justify; line-height: normal;">
            <span
                style="font-size: 10.0pt; font-family: 'Times New Roman','serif'; mso-fareast-font-family: 'Times New Roman'; mso-fareast-language: PT-BR;">A
                certidão, tanto em formato digital como na forma impressa, tem validade e fé pública em todo o
                território nacional, nos termos do art. 19, §§ 5º e 7º, da Lei nº 6.015/73. (*)</span>
        </p>


        <p style="text-align: justify; font-size: 10.0pt; font-family: 'Times New Roman';">
            &nbsp;
        </p>

        <p style="text-align: justify; font-size: 10.0pt; font-family: 'Times New Roman';">
            &nbsp;
        </p>
    </div>

    <div class="w-100">
        {{-- LADO ESQUERDO: ASSINATURA --}}
        <table border="0" cellspacing="0" cellpadding="0" style="float:left" class="w-40">
            <tr>
                <td>
                    <table>
                        <tr>
                            <td class="text-center" style="font-size: 10pt">O referido é verdade e dou fé.</td>
                        </tr>
                        <tr>
                            <td class="text-center" style="font-size: 10pt">SINOP - MT,
                                {{ formatarDataPorExtenso(\Carbon\Carbon::parse($data['integrador_selo_data'])->format('Y-m-d')) }}.
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <br /><br />
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center" style="font-size: 10pt"><i>Assinado Digitalmente</i></td>
                        </tr>
                        <tr>
                            <td class="text-center" style="font-size: 10pt">________________________________</td>
                        </tr>
                        <tr>
                            <td class="text-center" style="font-size: 10pt">André Luís Giocondo</td>
                        </tr>
                        <tr>
                            <td class="text-center" style="font-size: 10pt">Registrador</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        {{-- LADO DIREITO: SELO DIGITAL --}}
        <table border="0" cellspacing="0" cellpadding="0" style="float:right" class="w-60 border">
            <tr>
                <td>
                    <table>
                        {{-- CABEÇALHO --}}
                        <tr>
                            <td colspan="2" class="no-padding no-margin font-arial text-center font-size-12">
                                Poder Judiciário do Estado de Mato Grosso
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="no-padding no-margin font-arial text-center font-size-12">
                                Ato de Notas e de Registro
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="no-padding no-margin font-arial text-center font-size-12">
                                Código do Cartório: 169
                            </td>
                        </tr>
                        {{-- FIM CABEÇALHO --}}

                        {{-- SEPARADOR --}}
                        <tr>
                            <td colspan="2">
                                <div class="separator"></div>
                            </td>
                        </tr>
                        {{-- FIM SEPARADOR --}}

                        {{-- INFORMAÇÕES DO SELO --}}
                        <tr>
                            <td colspan="2" class="padding-10">
                                <table class="w-100" style="height: 120px;">
                                    <tr style="height: 100%;">
                                        {{-- LADO ESQUERDO: INFORMAÇÕES --}}
                                        <td style="width: 65%; vertical-align: top; padding: 4px;">
                                            <table class="w-100">
                                                <tr>
                                                    <td colspan="2"
                                                        class="font-arial text-left font-size-14 text-bold"
                                                        style="padding-bottom: 4px;">
                                                        SELO DE CONTROLE DIGITAL
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="font-arial text-left font-size-12"
                                                        style="width: 50px; padding: 2px 0;">
                                                        Cod. Ato(s):
                                                    </th>
                                                    <td class="font-arial text-left font-size-12"
                                                        style="padding: 2px 0;">
                                                        {{ $data['integrador_selo_codigo_atos'] }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="font-arial text-left font-size-12"
                                                        style="padding: 2px 0;">
                                                        SELO DIGITAL:
                                                    </th>
                                                    <td class="font-arial text-left font-size-12"
                                                        style="padding: 2px 0;">
                                                        {{ $data['integrador_selo_prefixo'] }}-{{ $data['integrador_selo_numero'] }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="font-arial text-left font-size-12"
                                                        style="padding: 2px 0;">
                                                        VALOR:
                                                    </th>
                                                    <td class="font-arial text-left font-size-12"
                                                        style="padding: 2px 0;">
                                                        {{ formatarDinheiro($data['integrador_selo_valor']) }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="font-arial text-left font-size-10"
                                                        style="padding: 2px 0;">
                                                        SINOP-MT,
                                                        {{ formatarDataPorExtenso(\Carbon\Carbon::parse($data['integrador_selo_data'])->format('Y-m-d')) }}.
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="font-arial text-left font-size-12"
                                                        style="padding: 2px 0;">
                                                        Consulta:<a
                                                            href="https://gif.tjmt.jus.br/selo/Consulta/ConSeloDigitalExterno.aspx"
                                                            target="_blank">www.tj.mt.gov.br/selos</a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>

                                        {{-- LADO DIREITO: QR CODE --}}
                                        <td
                                            style="width: 35%; text-align: center; vertical-align: middle; padding: 4px; height: 100%;">
                                            <img src="{!! $qrCode !!}" alt="QR Code"
                                                style="width: 100%; height: 100%; max-width: 100px; max-height: 100px; object-fit: contain; image-rendering: pixelated;">
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        {{-- CLEARFIX para evitar problemas de float --}}
        <div style="clear: both;"></div>
    </div>

    {{-- <p class="MsoNormal text-uppercase w-100 text-center">
        <b>Esta Certidão tem validade de 30 dias após a sua emissão.</b>
    </p> --}}
</body>

</html>
