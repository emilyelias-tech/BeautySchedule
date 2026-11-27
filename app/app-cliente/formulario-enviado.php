<?php
session_start();
require_once __DIR__ . "/../init.php";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamento Concluído - BeautySchedule</title>
    <link rel="stylesheet" href="../css/formulario-enviado.css">
</head>
<body>

    <?php require_once __DIR__ . '/../nav-cli.php'; ?>

    <main>
        <div class="container container-confirmacao">
           
            <div class="card-header-fechar">
                <a href="app-cliente/index.php" class="btn-fechar" title="Fechar">x</a>
            </div>

            <div class="confirmacao-conteudo">
                
                <div class="icone-sucesso">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>

                <span class="subtitulo-sucesso">Tudo certo</span>

                <h2 class="titulo-sucesso">Seu formulario foi<br>Enviado</h2>

                <p class="texto-sucesso">
                    Recebemos suas informações com sucesso.
                </p>

                <p class="texto-sucesso">
                    Nossa equipe entrará em contato em até 3 dias úteis pelo contato informado.
                </p>
            </div>
        </div>
    </main>

</body>
</html>