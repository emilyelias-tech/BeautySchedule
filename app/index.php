<?php

declare(strict_types=1);

$databaseStatus = 'Conectado';

try {
    require_once __DIR__ . '/init.php';
    $pdo->query('SELECT 1');
} catch (Throwable $exception) {
    $databaseStatus = 'Indisponível';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeautySchedule</title>
    <link rel="stylesheet" href="/app/css/index.css">
</head>

<body>

    <div class="nomes">
        <h2>Seu momento de cuidado</h2>
        <h1>Beleza que</h1>
        <h1>floresce em você</h1>
        <h4>Tratamentos para realçar sua beleza natural.</h4>

        <a href="agendamento.php">
            <button type="button">Agende seu horario →</button>
        </a>
    </div>

    <img src="/app/imagens/Logotipo Floral BS Beauty.png" alt="">

    <div class="conteudo">

        <div class="club">

            <div class="indica">
                <h5>BeautyClub</h5>
                <h1>Seu cuidado vale pontos</h1>
                <h4>Voce tem 680 pontos e ja pode resgatar R$ 40 de desconto no proximo serviço</h4>
            </div>

            <div class="pontos">
                <progress value="680" max="1000"></progress>

                <h4>680 de 1000 pontos para o proximo benefício</h4>

                <a href="configuracao-pontos.php">
                    <button type="button">Ver meus benefícios</button>
                </a>
            </div>

        </div>

    </div>

    <footer>

        <div class="contato">
            <h2>Fale com a gente</h2>

            <h4>Telefone</h4>
            <h5>(11) 4436-2020</h5>

            <h4>E-mail</h4>
            <h5>BeautyScheduli@gmail.com</h5>

            <h4>Suporte ao cliente</h4>
            <h5>Ajuda com a conta e os agendamentos</h5>

            <h4>Formulario de contato</h4>
            <h5>Envie uma mensagem diretamente pelo site</h5>
        </div>

        <div class="localidade">
            <h4>Nossa localização</h4>

            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3654.7197528318775!2d-46.54168392555614!3d-23.650205378738885!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94ce42ea3e3e88a1%3A0xec6172dcc69e184e!2sR.%20das%20Figueiras%2C%20800%20-%20Jardim%2C%20Santo%20Andr%C3%A9%20-%20SP%2C%2009080-301!5e0!3m2!1spt-BR!2sbr!4v1794578052059!5m2!1spt-BR!2sbr" width="400" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>

            <h4>Onde nos encontrar</h4>
            <h5>Rua das Figueiras, 800 – Bairro Jardim, Santo André – SP.</h5>

            <h4>CEP</h4>
            <h5>09080-300</h5>
        </div>

    </footer>

</body>
</html>