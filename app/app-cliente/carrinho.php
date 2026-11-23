<?php
declare(strict_types=1);
require_once __DIR__ . '/../init.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$itensCarrinho = $_SESSION['carrinho'] ?? [];

$totalGeral = 0;
foreach ($itensCarrinho as $item) {
    $totalGeral += (float)$item['preco'] * (int)$item['quantidade'];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho | BeautySchedule</title>
    <link rel="stylesheet" href="/app/css/carrinho.css">
</head>
<body>

    <?php require_once __DIR__ . "/../nav.php"; ?>

    <div class="fundo">
        <div class="container-carrinho">
            <h1>Seu Carrinho</h1>

            <?php if (!empty($itensCarrinho)): ?>
                <div class="lista-itens">
                    <?php foreach ($itensCarrinho as $item): ?>
                        <div class="item-carrinho">
                            <div class="info-item">
                                <span class="nome-produto"><?php echo htmlspecialchars($item['nome'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <span class="qtd-produto">Qtd: <?php echo (int)$item['quantidade']; ?></span>
                            </div>
                            <div class="preco-item">
                                R$ <?php echo number_format((float)$item['preco'] * (int)$item['quantidade'], 2, ',', '.'); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="resumo-carrinho">
                    <div class="total">
                        <span>Total Geral:</span>
                        <strong>R$ <?php echo number_format($totalGeral, 2, ',', '.'); ?></strong>
                    </div>
                    <a href="finalizar-compra.php" class="btn-finalizar">Finalizar Compra →</a>
                </div>
            <?php else: ?>
                <p class="carrinho-vazio">O seu carrinho está vazio.</p>
                <a href="produtos.php" class="btn-voltar">Ver Produtos</a>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>