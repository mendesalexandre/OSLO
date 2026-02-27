<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/bootstrap.css') }}">
</head>

<body class="py-3 text-justify">
    {!! $notaDevolutiva !!}
    {{-- <div id="watermark">
        <p>Confidencial</p>
    </div> --}}
    {{-- <script>
        var watermark = document.getElementById("watermark");
        watermark.style.opacity = 0.1;

        // inserir a marca dágua em todas as páginas
        var pages = document.querySelectorAll("body > div");
        pages.forEach(function(page) {
            var clone = watermark.cloneNode(true);
            page.appendChild(clone);
        });

        // inserir a marca dágua em diagonal
        var pages = document.querySelectorAll("body > div");
        pages.forEach(function(page) {
            var clone = watermark.cloneNode(true);
            page.appendChild(clone);
            clone.style.position = "absolute";
            clone.style.top = "50%";
            clone.style.left = "50%";
            clone.style.transform = "translate(-50%, -50%) rotate(-45deg)";
        });
    </script> --}}
</body>

</html>
