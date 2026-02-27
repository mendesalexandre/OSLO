<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certidão de Inteiro Teor</title>
    <style>
        body {
            margin: 90px 70px;
            padding: 20px 0 0 0;
            font-family: Arial, sans-serif !important;
            font-size: 14px;
            line-height: 1.2;
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
            border-collapse: collapse;
            table-layout: fixed;
        }

        table td {
            word-wrap: break-word;
        }

        .page {
            overflow: hidden;
            page-break-after: always;
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
        .texto-justificado {
            text-align: justify;
        }

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

        .padding-10 {
            padding: 10px;
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

        .border-none {
            border: none !important;
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
            width: 100%;
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

        /* Estilos específicos para o conteúdo principal */
        .conteudo-principal {
            margin-top: 120px;
        }

        .conteudo-principal p {
            margin-bottom: 15px;
            font-size: 10pt;
            font-family: Arial, serif;
            text-align: justify;
        }

        .conteudo-principal hr {
            margin: 20px 0;
            border: none;
            border-top: 1px solid #000;
        }

        .titulo-certidao {
            font-size: 16pt !important;
            font-weight: bold !important;
            text-align: center !important;
            margin-bottom: 20px;
            text-decoration: none !important;
            /* ✅ Remove underline padrão */
            border-bottom: 1px solid black !important;
            /* ✅ Linha personalizada */
            padding-bottom: 3px !important;
            /* ✅ Espaço entre texto e linha */
            display: inline-block !important;
            /* ✅ Para funcionar com center */
            width: 100% !important;
        }
    </style>
</head>

<body>
    <!-- CONTEÚDO PRINCIPAL -->
    <div class="conteudo-principal">
        <p class="titulo-certidao"><b>CERTIDÃO DE INTEIRO TEOR</b></p>
        <p class="texto-justificado">
            <b>CERTIFICO</b> e dou fé, que a presente certidão é reprodução autêntica da matrícula nº
            <b>{{ number_format($data['certidao_matricula_numero'], 0, ',', '.') ?? '______' }}</b>. O imóvel dela
            objeto tem sua situação com referência a registros e averbações de <b>ALIENAÇÕES E CONSTITUIÇÕES DE ÔNUS OU
                DIREITOS, INCLUSIVE DECORRENTES DE CITAÇÕES EM AÇÕES REAIS OU PESSOAIS REIPERSECUTÓRIAS</b>, que estejam
            integralmente nela noticiados, e retrata a situação jurídica registral imobiliária até a presente data,
            conforme dispõe os §§ 9º e 11º da Lei 6.015/73. Sinop-MT,
            {{ formatarDataPorExtenso(\Carbon\Carbon::parse($data['integrado_selo_data'])->format('Y-m-d')) }}, às
            {{ $data['integrado_selo_hora'] }}. (*)
        </p>

        <p class="texto-justificado">
            <b>CERTIFICO</b> ainda, que quando extraída sob a forma de documento eletrônico nos termos do art. 19,
            §§ 1º e 5º, da Lei nº 6.015/73 (*), mediante processo e assinatura por certificação digital no âmbito
            da ICP-Brasil, nos termos da legislação vigente, deverá ser conservada em meio eletrônico e confirmada
            sua autenticidade em <a href="https://assinador-web.onr.org.br/pt/docs" target="_blank"
                style="color: inherit; text-decoration: underline;">https://assinador-web.onr.org.br/pt/docs</a>,
            ou clicando diretamente no link lateral ou via leitura do QR-Code menor ao final de cada página.
        </p>

        <p class="texto-justificado">
            A certidão, tanto em formato digital como na forma impressa, tem validade e fé pública em todo o
            território nacional, nos termos do art. 19, §§ 5º e 7º, da Lei nº 6.015/73. (*)
        </p>

        <hr>

        <p class="texto-justificado">
            <b>(*) Lei 6.015/73, Artigo 19,</b>
        </p>

        <p class="texto-justificado">
            § 1º A certidão de inteiro teor será extraída por meio reprográfico ou eletrônico. (Redação dada pela
            Lei nº 14.382, de 2022)
        </p>

        <p class="texto-justificado">
            § 5º As certidões extraídas dos registros públicos deverão, observado o disposto no § 1º deste artigo,
            ser fornecidas eletronicamente, com uso de tecnologia que permita a sua impressão pelo usuário e a
            identificação segura de sua autenticidade, conforme critérios estabelecidos pela Corregedoria Nacional
            de Justiça do Conselho Nacional de Justiça, dispensada a materialização das certidões pelo oficial de
            registro. (Redação dada pela Lei nº 14.382, de 2022)
        </p>

        <p class="texto-justificado">
            § 7º A certidão impressa nos termos do § 5º e a certidão eletrônica lavrada nos termos do § 6º deste
            artigo terão validade e fé pública. (Incluído pela Lei nº 14.382, de 2022)
        </p>

        <p class="texto-justificado">
            § 9º A certidão da situação jurídica atualizada do imóvel compreende as informações vigentes de sua
            descrição, número de contribuinte, proprietário, direitos, ônus e restrições, judiciais e administrativas,
            incidentes sobre o imóvel e o respectivo titular, além das demais informações necessárias à comprovação
            da propriedade e à transmissão e à constituição de outros direitos reais. (Incluído pela Lei nº 14.382, de
            2022)
        </p>

        <p class="texto-justificado">
            <b><i>§ 11. No âmbito do registro de imóveis, a certidão de inteiro teor da matrícula conterá a reprodução
                    de todo seu conteúdo e será suficiente para fins de comprovação de propriedade, direitos, ônus reais
                    e restrições sobre o imóvel, independentemente de certificação específica pelo oficial. (Incluído
                    pela Lei nº 14.382, de 2022)</i></b>
        </p>
    </div>

    <!-- SEÇÃO DE ASSINATURA E SELO DIGITAL -->
    <div class="w-100" style="margin-top: 30px;">
        {{-- LADO ESQUERDO: ASSINATURA --}}
        <table border="0" cellspacing="0" cellpadding="0" style="float:left" class="w-40">
            <tr>
                <td>
                    <table>
                        <tr>
                            <td class="text-center" style="font-size: 10pt">O referido é verdade e dou fé.</td>
                        </tr>
                        <tr>
                            <td class="text-center" style="font-size: 10pt">
                                SINOP - MT,
                                {{ formatarDataPorExtenso(\Carbon\Carbon::parse($data['integrado_selo_data'] ?? now())->format('Y-m-d')) }}.
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
                            <td class="text-center" style="font-size: 10pt">______________________________</td>
                        </tr>
                        <tr>
                            <td class="text-center" style="font-size: 10pt">
                                {{ $data['nome_registrador'] ?? 'André Luís Giocondo' }}
                            </td>
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
                                Código do Cartório: {{ $data['codigo_cartorio'] ?? '169' }}
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
                                                    <th class="font-arial text-left font-size-14"
                                                        style="width: 50px; padding: 2px 0;">
                                                        Cod. Ato(s):
                                                    </th>
                                                    <td class="font-arial text-left font-size-14"
                                                        style="padding: 2px 0;">
                                                        {{ $data['integrado_selo_codigo_atos'] ?? '____' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="font-arial text-left font-size-14"
                                                        style="padding: 2px 0;">
                                                        SELO DIGITAL:
                                                    </th>
                                                    <td class="font-arial text-left font-size-14"
                                                        style="padding: 2px 0;">
                                                        {{ $data['integrado_selo_prefixo'] ?? '____' }}-{{ $data['integrado_selo_numero'] ?? '____' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="font-arial text-left font-size-14"
                                                        style="padding: 2px 0;">
                                                        VALOR:
                                                    </th>
                                                    <td class="font-arial text-left font-size-14"
                                                        style="padding: 2px 0;">
                                                        {{ isset($data['integrado_selo_valor']) ? formatarDinheiro($data['integrado_selo_valor']) : 'R$ ____,__' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="font-arial text-left font-size-12"
                                                        style="padding: 2px 0;">
                                                        SINOP-MT,
                                                        {{ formatarDataPorExtenso(\Carbon\Carbon::parse($data['integrado_selo_data'] ?? now())->format('Y-m-d')) }}.
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="font-arial text-left font-size-12"
                                                        style="padding: 2px 0;">
                                                        Consulta: <a
                                                            href="https://gif.tjmt.jus.br/selo/Consulta/ConSeloDigitalExterno.aspx"
                                                            target="_blank">www.tj.mt.gov.br/selos</a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>

                                        {{-- LADO DIREITO: QR CODE --}}
                                        <td
                                            style="width: 35%; text-align: center; vertical-align: middle; padding: 4px; height: 100%;">
                                            <img src="{!! $qrCode ?? '' !!}" alt="QR Code"
                                                style="width: 100%; height: 100%; max-width: 120px; max-height: 120px; object-fit: contain; image-rendering: pixelated;">
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
</body>

</html>