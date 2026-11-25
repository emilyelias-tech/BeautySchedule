<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$usuarioLogado = isset($_SESSION['usuario_id']);

$databaseStatus = 'Conectado';

try {
    require_once __DIR__ . '/../init.php';
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

    <title>Inicio | BeautySchedule</title>
    <link rel="stylesheet" href="/app/css/index.css">
    <?php require_once __DIR__ . "/../nav.php" ?>

</head>

<body>

    <div class="nomes">

        <h1 class="nome">Beleza que</h1>
        <h1 class="nome2">floresce em você</h1>

        <h4>Tratamentos para realçar sua beleza natural.</h4>

        <a href="/app/app-cliente/agendar-procedimento.php" class="btn-agendar-home">
            <button type="button">Agende seu horario →</button>
        </a>

    </div>

    <img src="/app/imagens/Logotipo Floral BS Beauty.png" alt="">

    <div class="conteudo">

        <?php if ($usuarioLogado): ?>

            <div class="club">

                <div class="indica">

                    <h5>BeautyClub</h5>

                    <h1>Seu cuidado vale pontos</h1>

                    <h4>
                        Voce tem 680 pontos e ja pode resgatar
                        R$ 40 de desconto no proximo serviço
                    </h4>

                </div>

                <div class="pontos">

                    <progress value="680" max="1000"></progress>

                    <h4>
                        680 de 1000 pontos para o proximo benefício
                    </h4>

                    <a href="configuracao-pontos.php">
                        <button type="button">Ver meus benefícios</button>
                    </a>

                </div>

            </div>

        <?php endif; ?>

    </div>

    <footer class="rodape">

        <div class="container">

            <div class="coluna">

                <div class="titulo">

                    <div>
                        <h2>Fale com a gente</h2>
                    </div>

                </div>

                <div class="info">

                    <a href="tel:+551144362020" class="item">

                        <span class="icone">
                            <i class="fa-solid fa-phone"></i>
                        </span>

                        <div>
                            <h3>Telefone</h3>
                            <p>(11) 4436-2020</p>
                        </div>

                    </a>

                    <a href="https://mail.google.com/" class="item">

                        <span class="icone">
                            <i class="fa-solid fa-envelope"></i>
                        </span>

                        <div>
                            <h3>E-mail</h3>
                            <p>BeautyScheduli@gmail.com</p>
                        </div>

                    </a>

                    <a href="suporte.php" class="item">

                        <span class="icone">
                            <i class="fa-solid fa-headset"></i>
                        </span>

                        <div>
                            <h3>Suporte ao cliente</h3>
                            <p>Ajuda com a conta e os agendamentos</p>
                        </div>

                    </a>

                    <a href="formulario.php" class="item">

                        <span class="icone">
                            <i class="fa-solid fa-comments"></i>
                        </span>

                        <div>
                            <h3>Formulário de contato</h3>
                            <p>Envie uma mensagem diretamente pelo site</p>
                        </div>

                    </a>

                </div>

            </div>

            <div class="local">

                <div class="titulo">

                    <span class="icone-local">
                        <i class="fa-solid fa-location-dot"></i>
                    </span>

                    <div>
                        <h2>Nossa localização</h2>
                    </div>

                </div>

                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3654.7197528318775!2d-46.54168392555614!3d-23.650205378738885!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94ce42ea3e3e88a1%3A0xec6172dcc69e184e!2sR.%20das%20Figueiras%2C%20800%20-%20Jardim%2C%20Santo%20Andr%C3%A9%20-%20SP%2C%2009080-301!5e0!3m2!1spt-BR!2sbr!4v1794578052059!5m2!1spt-BR!2sbr"
                    loading="lazy"
                    allowfullscreen>
                </iframe>

                <div class="endereco">

                    <p>
                        <i class="fa-solid fa-location-dot"></i>

                        <a
                            href="https://www.google.com/maps/search/?api=1&query=Rua+das+Figueiras+800+Santo+Andre+SP"
                            target="_blank">

                            Rua das Figueiras, 800 – Bairro Jardim,
                            Santo André – SP.

                        </a>
                    </p>

                    <p>
                        <i class="fa-solid fa-map"></i>
                        CEP: 09080-300
                    </p>

                </div>

            </div>

        </div>

        <div class="final">

            <p>
                BeautySchedule © 2026
            </p>

        </div>

    </footer>


</body>

</html>