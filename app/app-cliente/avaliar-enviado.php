<?php
session_start();
require_once __DIR__ . "/init.php";

$pontosGanhos = 20;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avaliação Enviada - BeautySchedule</title>
    <link rel="stylesheet" href="css/consulta.css">
</head>
<body>

    <?php require_once __DIR__ . '/nav.php'; ?>

    <main>
        <div class="container container-confirmacao-feedback">

            <div class="icone-check-circulo">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>

            <span class="subtitulo-obrigada">Obrigada por compartilhar</span>

            <h2 class="titulo-florecer">
                Sua opinião faz a gente<br>florecer.
            </h2>

            <p class="texto-pontos">
                Voce ganhou <span class="destaque-pontos"><?= $pontosGanhos ?> pontos extras</span> por avaliar seu atendimento
            </p>

            <a href="index.php" class="btn-concluir-feedback">
                Concluir
            </a>

        </div>
    </main>

</body>
</html>
