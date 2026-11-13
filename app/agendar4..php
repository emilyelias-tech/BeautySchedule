<?php
session_start();
require_once __DIR__ . "/init.php";


$concluido = $_SESSION['agendamento_concluido'] ?? [
    'data'    => '2026-06-20',
    'horario' => '09:00'
];


$meses = [
    '01' => 'janeiro', '02' => 'fevereiro', '03' => 'março',
    '04' => 'abril',   '05' => 'maio',      '06' => 'junho',
    '07' => 'julho',   '08' => 'agosto',    '09' => 'setembro',
    '10' => 'outubro', '11' => 'novembro',  '12' => 'dezembro'
];

$partesData = explode('-', $concluido['data']);
$dia = isset($partesData[2]) ? (int)$partesData[2] : '20';
$mesNome = isset($partesData[1], $meses[$partesData[1]]) ? $meses[$partesData[1]] : 'junho';
$horario = $concluido['horario'] ?? '09:00';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamento Concluído - BeautySchedule</title>
    <link rel="stylesheet" href="css/agendamentos.css">
</head>
<body>

    <?php require_once __DIR__ . '/nav.php'; ?>

    <main>
        <div class="container container-confirmacao">
           
            <div class="card-header-fechar">
                <a href="index.php" class="btn-fechar" title="Fechar">x</a>
            </div>

         
            <div class="confirmacao-conteudo">
                
            
                <div class="icone-sucesso">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>

                <span class="subtitulo-sucesso">Tudo certo</span>

                <h2 class="titulo-sucesso">Seu cuidado está<br>reservado</h2>

                <p class="texto-sucesso">
                    Enviamos os detalhes para o seu E-mail. Esperamos por você<br>
                    no dia <?= htmlspecialchars($dia) ?> de <?= htmlspecialchars($mesNome) ?>, ás <?= htmlspecialchars($horario) ?>
                </p>

                <a href="index.php" class="btn-voltar-inicio">
                    Voltar ao inicio
                </a>

            </div>

        </div>
    </main>

</body>
</html>