<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
</head>
<body style="margin:0 ;padding;0;">
        <table class="table table-bordered">
            <tr>
                <td style="width:20%">
                    <div>
                        <img src="{{ asset('/assets/images/logo.png') }}" class="w-100">
                    </div>
                </td>
                <td style="width:85%" class="text-center">
                    <span class="font-weight-bold text-uppercase d-block" style="font-size: 28px">
                        {{ $cartorio->nome }}
                    </span>

                    <span class="font-weight-bold text-uppercase d-block" style="font-size: 28px">
                        {{ $cartorio->comarca }}
                    </span>
                    {{-- <span class="font-weight-bold text-uppercase d-block">
                        {{ $cartorio-> }}
                    </span> --}}
                    <span class="font-weight-bold text-uppercase d-block" style="font-size: 22px">
                        {{ $oficial->nome }}
                    </span>

                    <span class="font-weight-bold text-uppercase d-block" style="font-size: 22px">
                        {{ $oficial->cargo->nome }}
                    </span>
                </td>
            </tr>
        </table>
</body>
</html>
