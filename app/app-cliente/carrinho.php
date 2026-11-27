<?php
declare(strict_types=1);
require_once __DIR__ . '/../init.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_GET['remover'])) {
    $idRemover = (int)$_GET['remover'];
    if (isset($_SESSION['carrinho'][$idRemover])) {
        unset($_SESSION['carrinho'][$idRemover]);
    }
    header("Location: carrinho.php");
    exit;
}

$itensCarrinho =$_SESSION['carrinho'] ?? [];

$totalGeral = 0;
$totalItens = 0;
foreach ($itensCarrinho as $item) {$totalGeral += (float)$item['preco'] * (int)$item['quantidade'];
    $totalItens += (int)$item['quantidade'];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho | BeautySchedule</title>
    <link rel="stylesheet" href="../css/carrinho.css">
</head>
<body>

    <?php require_once __DIR__ . "/../nav-cli.php"; ?>

    <main class="fundo">
        <div class="container-carrinho">
            <div class="carrinho-cabecalho">
                <h1>Seu Carrinho</h1>
                <span class="contador-itens"><?php echo $totalItens; ?> <?php echo$totalItens === 1 ? 'item selecionado' : 'itens selecionados'; ?></span>
            </div>

            <?php if (!empty($itensCarrinho)): ?>
                <div class="lista-itens">
                    <?php foreach ($itensCarrinho as $id =>$item): ?>
                        <?php 
                            $imgItem = !empty($item['imagem']) ? $item['imagem'] : 'https://placehold.co/100x100/d5ceb8/485136?text=BS';$subtotal = (float)$item['preco'] * (int)$item['quantidade'];
                        ?>
                        <div class="item-carrinho">
                            <img src="<?php echo htmlspecialchars($imgItem, ENT_QUOTES, 'UTF-8'); ?>" alt="Produto" class="item-img">
                            
                            <div class="item-detalhes">
                                <span class="nome-produto"><?php echo htmlspecialchars($item['nome'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <span class="preco-unitario">Valor unitário: R$<?php echo number_format((float)$item['preco'], 2, ',', '.'); ?></span>
                            </div>

                            <div class="item-qtd-badge">
                                Qtd: <strong><?php echo (int)$item['quantidade']; ?></strong>
                            </div>

                            <div class="preco-item">
                                R$<?php echo number_format($subtotal, 2, ',', '.'); ?>
                            </div>

                            <a href="carrinho.php?remover=<?php echo (int)$id; ?>" class="btn-remover" title="Remover produto">✕</a>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="resumo-carrinho">
                    <div class="linha-resumo">
                        <span>Subtotal</span>
                        <span>R$<?php echo number_format($totalGeral, 2, ',', '.'); ?></span>
                    </div>
                    <div class="linha-resumo total">
                        <span>Total a Pagar</span>
                        <strong>R$<?php echo number_format($totalGeral, 2, ',', '.'); ?></strong>
                    </div>

                    <div class="acoes-carrinho">
                        <a href="produtos.php" class="btn-continuar">← Continuar comprando</a>
                        <a href="finalizar-compra.php" class="btn-finalizar">Finalizar Compra →</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="carrinho-vazio-box">
                    <p class="carrinho-vazio">O seu carrinho está vazio no momento.</p>
                    <br>
                    <a href="produtos.php" class="btn-voltar">Olhar Produtos</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>