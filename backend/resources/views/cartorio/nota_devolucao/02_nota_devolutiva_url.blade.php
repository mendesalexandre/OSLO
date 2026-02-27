<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <title>Nota Devolutiva</title>

    <style>
        .h-screen {
            height: 100vh !important;
        }

        .h-full {
            height: 100% !important;
        }

        .w-full {
            width: 100% !important;
        }
    </style>
</head>

<body>
    {{-- HEADER --}}
    <div class="h-screen">
        <header class="navbar navbar-expand-lg navbar-light bg-primary"></header>
        <main class="container h-full">
            <h5>Prezado cliente, você está recebendo sua Nota de Devolução, clique link abaixo para fazer o Download.
                </h1>
                <a href="{{ asset('/storage/nota-devolutiva/' . $arquivo->arquivo) }}"
                    class="w-100 btn btn-success btn-lg">
                    {{ $arquivo->nome }}</a>
        </main>
        {{-- FOOTER --}}
        <footer class="navbar navbar-expand-lg navbar-light bg-primary text-white text-center w-100">
            <div class="d-flex justify-content-center">
                {{ $cartorio->nome }}
            </div>
        </footer>
    </div>
</body>

</html>
