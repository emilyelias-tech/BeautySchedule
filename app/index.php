<?php

declare(strict_types=1);
require_once __DIR__ . '/nav.php';
$databaseStatus = 'Conectado';

try {
    require_once __DIR__ . '/init.php';
    $pdo->query('SELECT 1');
} catch (Throwable $exception) {
    $databaseStatus = 'Indisponível';
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Projeto Integrador</title>
    <style>
        
    </style>
</head>
<body>
    <img class = "logo2" src="imagens/beauty.png" alt="Imagem da logo">
    <p>.</p>
    <div class="status">
        Banco de dados: <strong><?= htmlspecialchars($databaseStatus, ENT_QUOTES, 'UTF-8') ?></strong>
    </div>
</body>
</html>
