<?php

declare(strict_types=1);
require_once __DIR__ . '/css/nav.php';

$databaseStatus = 'Conectado';

try {
    require_once __DIR__ . '/init.php';
    $pdo->query('SELECT 1');
} catch (Throwable $exception) {
    $databaseStatus = 'Indisponível';
}
$produtos = [];
if ($databaseStatus === 'Conectado') {
    try {
        $stmt = $pdo->query('SELECT id, nome, preco, materiais, quantidade, imagem FROM produtos');
        $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nossos Produtos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    
</body>
</html>