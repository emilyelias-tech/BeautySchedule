<?php
declare(strict_types=1);
require_once __DIR__ . "/../init.php";

$pdo = database();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = :id");
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$produto = $stmt->fetch();

if (!$produto) {
    header("Location: produtos.php");
    exit;
}

$imagemProduto = !empty($produto['imagem']) ? $produto['imagem'] : 'https://placehold.co/300x300/e8dfd5/495437?text=Sem+Foto';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($produto['nome']); ?> - Beauty Schedule</title>
    <link rel="stylesheet" href="/app/css/produtos.css">
    <link rel="stylesheet" href="/app/css/detalhes-produtos.css">
</head>
<body>

    <?php require_once __DIR__ . "/../nav.php"; ?>

    <section class="secao-produtos">
        <div class="detalhes-container">
            
            <div class="detalhes-imagem-box">
                <img src="<?php echo htmlspecialchars($imagemProduto); ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>" class="detalhes-img">
            </div>

            <div class="detalhes-info-box">
                <h1 class="detalhes-titulo"><?php echo htmlspecialchars($produto['nome']); ?></h1>
                
                <p class="detalhes-preco">R$ <?php echo number_format((float)$produto['preco'], 2, ',', '.'); ?></p>
                
                <div class="detalhes-descricao-area">
                    <h3>Descrição do Produto</h3>
                    <p><?php echo nl2br(htmlspecialchars($produto['descricao'] ?? 'Nenhuma descrição informada para este produto.')); ?></p>
                </div>

                <!-- Formulário corrigido com botão submit -->
                <form action="adicionar-carrinho.php" method="POST" class="form-carrinho">
                    <input type="hidden" name="produto_id" value="<?php echo $produto['id']; ?>">
                    
                    <div class="quantidade-box">
                        <label for="quantidade">Quantidade:</label>
                        <input type="number" id="quantidade" name="quantidade" value="1" min="1" max="10" class="input-qtd">
                    </div>

                    <button type="submit" class="btn-finalizar">
                        Adicionar ao Carrinho ➔
                    </button>
                </form>

                <div class="voltar-box">
                    <a href="produtos.php" class="link-voltar">← Voltar para os produtos</a>
                </div>
            </div>

        </div>
    </section>

</body>
</html>