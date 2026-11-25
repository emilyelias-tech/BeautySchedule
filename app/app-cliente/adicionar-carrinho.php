<?php
declare(strict_types=1);
require_once __DIR__ . '/../init.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o ID foi passado via POST (detalhes-produto.php) ou GET (produtos.php)
$produtoId = (int)($_POST['produto_id'] ?? $_GET['id'] ?? 0);
$quantidadeAdd = max(1, (int)($_POST['quantidade'] ?? 1));

if ($produtoId <= 0) {
    header("Location: produtos.php");
    exit;
}

try {
    $pdo = database();
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $produtoId]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$produto) {
        die("Erro: Produto com o ID {$produtoId} não foi encontrado na base de dados.");
    }

    // Inicializa o carrinho se não existir
    if (!isset($_SESSION['carrinho'])) {
        $_SESSION['carrinho'] = [];
    }

    // Adiciona ou incrementa o produto
    if (isset($_SESSION['carrinho'][$produtoId])) {
        $_SESSION['carrinho'][$produtoId]['quantidade'] += $quantidadeAdd;
    } else {
        $_SESSION['carrinho'][$produtoId] = [
            'id' => (int)$produto['id'],
            'nome' => $produto['nome'],
            'preco' => (float)$produto['preco'],
            'quantidade' => $quantidadeAdd,
            'imagem' => $produto['imagem'] ?? ''
        ];
    }

} catch (Throwable $e) {
    die("Erro na base de dados: " . $e->getMessage());
}

// Redireciona para o carrinho se tudo correr bem
header("Location: carrinho.php");
exit;