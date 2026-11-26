<?php
declare(strict_types=1);
require_once __DIR__ . '/../init.php';

$produtoId  = (int)($_POST['produto_id'] ?? $_GET['id'] ?? 0);
$quantidade = max(1, (int)($_POST['quantidade'] ?? 1));

if ($produtoId <= 0) {
    header("Location: produtos.php");
    exit;
}

$pdo = database();
$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $produtoId]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    header("Location: produtos.php");
    exit;
}

if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

if (isset($_SESSION['carrinho'][$produtoId])) {
    $_SESSION['carrinho'][$produtoId]['quantidade'] += $quantidade;
} else {
    $_SESSION['carrinho'][$produtoId] = [
        'id'         => (int)$produto['id'],
        'nome'       => $produto['nome'],
        'preco'      => (float)$produto['preco'],
        'quantidade' => $quantidade,
        'imagem'     => $produto['imagem'] ?? ''
    ];
}

header("Location: carrinho.php");
exit;