<?php
declare(strict_types=1);
require_once __DIR__ . '/../init.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o ID foi passado na URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Erro: Nenhum ID de produto foi fornecido. Verifique o link do botão (ex: adicionar-carrinho.php?id=1).");
}

$produtoId = (int)$_GET['id'];

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
        $_SESSION['carrinho'][$produtoId]['quantidade']++;
    } else {
        $_SESSION['carrinho'][$produtoId] = [
            'id' => (int)$produto['id'],
            'nome' => $produto['nome'],
            'preco' => (float)$produto['preco'],
            'quantidade' => 1,
            'imagem' => $produto['imagem'] ?? ''
        ];
    }

} catch (Throwable $e) {
    die("Erro na base de dados: " . $e->getMessage());
}

// Redireciona para o carrinho se tudo correr bem
header("Location: carrinho.php");
exit;