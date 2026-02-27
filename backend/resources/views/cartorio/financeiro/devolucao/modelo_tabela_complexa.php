<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('/assets/css/bootstrap.css') }}">

    <title>Código</title>
</head>

<body>
    <header>Código</header>

    <main>
        <section>
            <h2>Html puro</h2>
            <h4>Anatomia do HTML e tabela complexa</h4>
            <p>
                Nesse arquivo html temos elementos principais do html e também tags semânticas de uma tabela complexa abordando inclusive elementos de acessibilidade.
            </p>
        </section>

        <section>
            <table class="table">
                <caption>Produzidos x Vendidos por Loja</caption>

                <colgroup>
                    <col>
                    <col span="2" style="background-color: red;">
                    <col span="2" style="background-color: blue;">
                </colgroup>

                <thead>
                    <tr>
                        <th rowspan="2"></th>
                        <th colspan="2" scope="colgroup">Afonso Pena</th>
                        <th colspan="2" scope="colgroup">Antônia Pereira</th>
                    </tr>

                    <tr>
                        <th scope="col">Produzidos</th>
                        <th scope="col">Vendidos</th>
                        <th scope="col">Produzidos</th>
                        <th scope="col">Vendidos</th>
                    </tr>

                </thead>

                <tbody>
                    <tr>
                        <th scope="row">Vassouras</th>
                        <td>30</td>
                        <td>35</td>
                        <td>25</td>
                        <td>35</td>
                    </tr>
                    <tr>
                        <th scope="row">Baldes</th>
                        <td>25</td>
                        <td>35</td>
                        <td>30</td>
                        <td>35</td>
                    </tr>
                </tbody>

                <tfoot>
                    <tr>
                        <td>Total:</td>
                        <td>2 Pessoas</td>
                    </tr>
                </tfoot>
            </table>
        </section>

    </main>

    <footer>@douglasabnovato</footer>
</body>

</html>